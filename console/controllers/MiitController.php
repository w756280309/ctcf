<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-3-1
 * Time: 上午10:08
 */
namespace console\controllers;

use common\lib\MiitBaoQuan\Miit;
use common\lib\pdf\CreatePDF;
use common\models\contract\ContractTemplate;
use common\models\order\EbaoQuan;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\user\User;
use yii\console\Controller;
use yii\db\Exception;

class MiitController extends Controller
{
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
}