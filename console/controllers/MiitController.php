<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-3-1
 * Time: 上午10:08
 */
namespace console\controllers;

use common\controllers\ContractTrait;
use common\lib\MiitBaoQuan\Miit;
use common\lib\pdf\CreatePDF;
use common\models\contract\ContractTemplate;
use common\models\order\EbaoQuan;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\tx\CreditOrder;
use common\models\tx\UserAsset;
use common\models\user\User;
use Knp\Snappy\Pdf;
use yii\console\Controller;
use yii\db\Exception;

class MiitController extends Controller
{
    use ContractTrait;
    //认证
    public function actionAuth()
    {
        $miit = new Miit();
        echo $miit->getTicket() .PHP_EOL;
    }
    //合同上传
    public function actionUpload($id)
    {
        $order = OnlineOrder::findOne($id);
        if (!is_null($order)) {
            try {
                $loan = OnlineProduct::findOne(['id' => $order->online_pid]);
                $user = User::findOne($order->uid);
                $agreements = ContractTemplate::find()->select(['pid', 'name', 'content'])->where(['pid' => $order->online_pid])->asArray()->all();
                if (is_null($loan)) {
                    throw new Exception('标的不存在');
                }
                if (is_null($user)) {
                    throw new Exception('用户不存在');
                }
                if (count($agreements) < 1) {
                    throw new Exception('合同模板不存在');
                }
                //生成pdf文件
                $content = null;
                foreach ($agreements as $k => $v) {
                    //用订单填充合同模板
                    $c = $v['content'];
                    if ($c) {
                        //获取合同模板
                        $c = (new CreatePDF())->handleContent($k, $c, $order, $user);
                        $content = $content . $c . ' <br/><br/><hr/><br/><br/>';
                    }
                }
                //多份合同合并成一份
                $content = rtrim($content, '<br/><br/><hr/><br/><br/>');
                //生成PDF
                $file = (new CreatePDF())->createPdf($content, $order->sn);
                if (!file_exists($file)) {
                    throw new Exception('pdf文件不存在');
                }
                //保全上传
                $miit = new Miit();
                $res = $miit->hetongUpload($file, $user, $order->sn, $loan->title, $order->order_time, EbaoQuan::TYPE_M_LOAN, EbaoQuan::ITEM_TYPE_LOAN_ORDER, $order->id);
                unlink($file);
                var_dump($res);
            } catch (\Exception $ex) {
                var_dump($ex->getMessage());
            }
        }
    }

    /**
     * 上传转让标的合同测试
     */
    public function actionUploadCredit($id)
    {
        $order = CreditOrder::findOne($id);
        $userAsset = UserAsset::find()
            ->where(['credit_order_id' => $order->id])
            ->asArray()
            ->one();
        if (null === $userAsset) {
            throw new \Exception('未发现该用户转让资产记录');
        }
        $contracts = $this->getUserContract($userAsset);
        $user = User::findOne($userAsset['user_id']);
        if (!isset($contracts['loanContract'])
            || !isset($contracts['creditContract'])
            || !isset($contracts['loanAmount'])
            || !isset($contracts['loanId'])
        ) {
            throw new \Exception('合同内容不全');
        }
        $loan = OnlineProduct::findOne($contracts['loanId']);
        if (count($contracts['loanContract']) <= 0 || count($contracts['creditContract']) <= 0 || empty($loan)) {
            throw new \Exception('合同内容不全');
        }
        $loanContract = $contracts['loanContract'];
        //合并标的合同
        $finalLoanContent = implode(array_column($loanContract, 'content'), ' <br/><br/><hr/><br/><br/>');
        //保全标的合同
        try {
            $file = self::createPdf($finalLoanContent, time().rand(10000, 99999));
            if (file_exists($file)) {
                $loanBaoId = '001100' . $order->id;
                //生成保全
                $res = (new Miit())->hetongUpload(
                    $file,
                    $user,
                    $loanBaoId,
                    $loan->title,
                    strtotime($order->updateTime),
                    EbaoQuan::TYPE_M_LOAN,
                    EbaoQuan::ITEM_TYPE_CREDIT_ORDER,
                    $order->id
                );
                unlink($file);
                var_dump($res);
            }
        } catch (\Exception $ex) {
            var_dump($ex->getMessage());
        }
        //保全债权合同
        $creditContract = $contracts['creditContract'];
        foreach ($creditContract as $contract) {
            if (!isset($contract['type'])) {
                throw new \Exception('合同信息不完善');
            }
            if ($contract['type'] === 'credit_order') {
                //只保全其购买他人转让的合同
                //保全标的合同
                try {
                    $file = self::createPdf($contract['content'], time().rand(10000, 99999));
                    if (file_exists($file)) {
                        $creditBaoId = '001101'.$order->id;
                        //生成保全
                        $res = (new Miit())->hetongUpload(
                            $file,
                            $user,
                            $creditBaoId,
                            $loan->title,
                            strtotime($order->updateTime),
                            EbaoQuan::TYPE_M_CREDIT,
                            EbaoQuan::ITEM_TYPE_CREDIT_ORDER,
                            $order->id
                        );
                        unlink($file);
                        var_dump($res);
                    }
                } catch (\Exception $ex) {
                    var_dump($ex->getMessage());
                }
            }
        }
    }

    /**
     * 新建pdf文件
     * @param string $content pdf文件内容
     * @param string $fileName pdf文件名
     * @return string
     */
    private static function createPdf($content, $fileName)
    {
        $content = '<head><meta charset="utf-8"/></head>' . $content;
        $file = \Yii::$app->getBasePath() . '/runtime/bao_quan/' . $fileName . '(' . date('YmdHis') . ')' . '.pdf';
        if (!file_exists($file)) {
            $myProjectDirectory = \Yii::$app->getBasePath();
            $snappy = new Pdf($myProjectDirectory . '/../vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
            $snappy->generateFromHtml($content, $file);
            if (file_exists($file)) {
                return realpath($file);
            }
        }
        return '';
    }
}
