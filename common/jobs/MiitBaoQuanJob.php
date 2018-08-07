<?php
/*
新建保全思路

（1）根据成功订单，生成指定格式的pdf合同（ContractTemplate::replaceTemplate()）,可以替换认购协议、风险提示书；项目说明直接保存
（2）将合同保存为指定文件名，文件保存到临时目录中
（3）新建保全，保存本地数据库【数据结构见表：ebao_quan】
（4）删除临时文件
 */
namespace common\jobs;

use common\lib\MiitBaoQuan\Miit;
use common\lib\pdf\CreatePDF;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\tx\UserAsset;
use common\models\user\User;
use common\utils\SecurityUtils;
use yii\queue\Job;
use yii\base\Object;
use Yii;
use common\models\contract\ContractTemplate;
use yii\base\Exception;
use Knp\Snappy\Pdf;
use yii\web\NotFoundHttpException;
use org_mapu_themis_rop_tool\RopUtils;
use org_mapu_themis_rop_model\UploadFile as UploadFile;
use org_mapu_themis_rop_model\ContractFilePreservationCreateRequest as ContractFilePreservationCreateRequest;
use org_mapu_themis_rop_model\UserIdentiferType as UserIdentiferType;
use org_mapu_themis_rop_model\PreservationType as PreservationType;
use common\models\order\EbaoQuan;
use common\controllers\ContractTrait;

class MiitBaoQuanJob extends Object implements Job  //需要继承Object类和Job接口
{
    use ContractTrait;
    public $item_type;  //订单来源(3种,对应EbaoQuan的三种)
    public $order;
    public $creditNote;

    public function execute($queue)
    {
        if ($this->item_type == EbaoQuan::ITEM_TYPE_LOAN_ORDER) {
            $this->baoquan_loan_order();
        } elseif ($this->item_type == EbaoQuan::ITEM_TYPE_CREDIT_ORDER) {
            $this->baoquan_credit_order();
        } elseif ($this->item_type == EbaoQuan::ITEM_TYPE_CREDIT_NOTE) {
            $this->createCreditSellerBq();
        }
    }

    /**
     * 'loan_order';//从标的订单新建的保全
     */
    public function baoquan_loan_order()
    {
        $order = $this->order;
        $loan = OnlineProduct::findOne(['id' => $order->online_pid]);
        $user = User::findOne(['id' => $order->uid]);
        $agreements = ContractTemplate::find()->select(['pid', 'name', 'content'])->where(['pid' => $order->online_pid])->asArray()->all();
        if (count($agreements) > 0) {
            try {
                $content = '';
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
                if (file_exists($file)) {
                    //生成保全
                    $miit = new Miit();
                    $res = $miit->hetongUpload($file, $user, $order->sn, $loan->title, $order->order_time, EbaoQuan::TYPE_M_LOAN, EbaoQuan::ITEM_TYPE_LOAN_ORDER, $order->id);
                    unlink($file);
                }
            } catch (Exception $e) {
                \Yii::trace('标的订单保全失败，订单ID:'.$order->id.';保全失败,失败信息'.$e->getMessage(), 'bao_quan');
                throw $e;
            }
        }
    }

    /**
     * 'credit_order';//从债权订单新建的保全,买方
     */
    public function baoquan_credit_order()
    {
        $order = $this->order;
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
            }
        } catch (\Exception $ex) {
            \Yii::trace('购买债权订单成功之后保全标的合同，标的ID:'.$contracts['loanId'].';保全失败,资产ID:'.$userAsset['id'].';失败信息'.$ex->getMessage(), 'bao_quan');
            throw $ex;
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
                    }
                } catch (\Exception $ex) {
                    \Yii::trace('购买债权订单成功之后保全转让合同，标的ID:'.$contracts['loanId'].';保全失败,资产ID:'.$userAsset['id'].';失败信息'.$ex->getMessage(), 'bao_quan');
                    throw $ex;
                }
            }
        }
    }

    //债权转让保全（卖方合同）
    public function createCreditSellerBq()
    {
        $note = $this->creditNote;
        $userAsset = UserAsset::find()
            ->where(['note_id' => $note->id])
            ->asArray()
            ->one();
        $seller = User::findOne($note['user_id']);
        $loan = OnlineProduct::findOne($userAsset['loan_id']);
        $txClient = \Yii::$container->get('txClient');
        $creditContract = [];
        if ($userAsset['note_id'] && $userAsset['credit_order_id']) {
            //购买该转让生成的转让合同
            $creditTemplate = $this->loadCreditContractByAsset($userAsset, $txClient, $loan);
            $creditContract[] = $creditTemplate['content'];
        }
        $content = implode(' <br><hr><br> ', $creditContract);
        try {
            $file = self::createPdf($content, time().rand(10000, 99999));
            if (file_exists($file)) {
                $baoId = '001102' . $note->id;
                //生成保全
                $res = (new Miit())->hetongUpload(
                    $file,
                    $seller,
                    $baoId,
                    $loan->title,
                    $note->closeTime,
                    EbaoQuan::TYPE_M_CREDIT,
                    EbaoQuan::ITEM_TYPE_CREDIT_NOTE,
                    $note->id
                );
                unlink($file);
            }
        } catch (\Exception $ex) {
            \Yii::trace('转让结束之后生成保全合同，标的ID:'.$loan->id.';保全失败,债权ID:'.$note->id.';失败信息'.$ex->getMessage(), 'bao_quan');
            throw $ex;
        }
    }

    /**
     * 根据指定模板生成合同
     * @param integer $type 合同类型
     * @param string $content 合同模板
     * @param OnlineOrder $onlineOrder
     * @return string
     */
    private static function handleContent($type, $content, OnlineOrder $onlineOrder, User $user)
    {
        if (in_array($type, [0, 1, 2])) {
            $title = '产品合同';
            $content = '<h4 style="margin: 5px auto;text-align: center;font-size: 14px;color: #000;line-height: 18px;">' . $title . '</h4>' . $content;
        }
        $real_name = '';
        $idCard = '';
        $userName = '';
        $date = "年月日";
        $money = "";
        $mobile = null;
        $fourTotalAsset = null; //todo 四期总资产
        if (null !== $onlineOrder) {
            $date = date("Y年m月d日", $onlineOrder->order_time);
            $money = $onlineOrder->order_money;
            if (null !== $user) {
                $real_name = $user->real_name;
                $idCard = $user->getIdcard();
                $userName = $user->getMobile();
                $mobile = $userName;
            }
            $loan = $onlineOrder->loan;
            if (null !== $loan) {
                $fourTotalAsset = 4 * $loan->money;
            }
        }
        $res = preg_match_all('/(\｛|\{){2}(.+?)(\｝|\}){2}/is', $content, $array);
        if ($res && isset($array[0]) && count($array[0]) > 0 && isset($array[2]) && count($array[2]) > 0) {
            foreach ($array[2] as $key => $value) {
                switch (strip_tags($value)) {
                    case '投资人':
                    case '出借人':
                        $content = str_replace($array[0][$key], $real_name, $content);
                        break;
                    case '投资人手机号':
                    case '出借人手机号':
                        $content = str_replace($array[0][$key], $mobile, $content);
                        break;
                    case '身份证号':
                        $content = str_replace($array[0][$key], $idCard, $content);
                        break;
                    case '用户名':
                        $content = str_replace($array[0][$key], $userName, $content);
                        break;
                    case '认购日期':
                    case '出借日期':
                        $content = str_replace($array[0][$key], $date, $content);
                        break;
                    case '认购金额':
                    case '出借金额':
                        $content = str_replace($array[0][$key], $money, $content);
                        break;
                    case '4期总资产':
                        $content = str_replace($array[0][$key], $fourTotalAsset, $content);
                        break;
                    default:
                        break;
                }
            }
        }
        return $content;
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

    /**
     * 新建保全
     * @param string $filePath pdf文件绝对路径
     * @param User $user 订单用户对象
     * @param OnlineOrder $onlineOrder 订单对象
     * @param integer $type 合同类型,0:认购协议,1:风险提示书,2:产品要素表
     * @param string $title 标题
     * @return bool
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    private static function contractFileCreate($filePath, User $user, $amount, $loanTitle)
    {
        if (!file_exists($filePath)) {
            throw new NotFoundHttpException('合同文件(' . $filePath . ')不存在');
        }
        require_once dirname(__FILE__) . '/../org_mapu_themis_rop_model/enum.php';
        //组建请求参数
        //下面语句当系统为windows时，访问中文文件名的内容需转换，当前程序中的文件名从utf-8->gbk，再进行读取文件路径
        $fileName = RopUtils::getFileName($filePath);
        $filePath = iconv("utf-8", "gb2312//IGNORE", $filePath);
        //封装上传文件内容
        $file = new UploadFile();
        $file->content = file_get_contents($filePath);
        $file->fileName = $fileName;
        //初始化合同文件上传
        $requestObj = new ContractFilePreservationCreateRequest();
        //init保全请求参数
        $requestObj->file = $file;
        $name = '产品合同';
        $requestObj->preservationTitle = $name;

        //个人用户
        $requestObj->userIdentifer = $user['idcard'];
        $requestObj->userRealName = $user['real_name'];
        $requestObj->userIdentiferType = UserIdentiferType::$PRIVATE_ID;

        $requestObj->preservationType = PreservationType::$DIGITAL_CONTRACT;
        $requestObj->sourceRegistryId = $user['id'];
        $requestObj->userEmail = $user['email'];
        $requestObj->mobilePhone = $user['mobile'];
        $requestObj->contractAmount = $amount;
        $microtime = explode('.', microtime(true));
        $requestObj->contractNumber = $microtime[0].$microtime[1].rand(10000, 99999);
        //$requestObj->objectId="0000001";//关联保全时使用
        $requestObj->comments = $loanTitle;
        $requestObj->isNeedSign = "1";
        //isNeedSign 这个参数如果为1是需要签名，则上传的文件必须是pdf文件，且服务端会将文件做签名后再保全.请->
        //使用保全contractFileDownloadUrl.php例子的使用方法得到合同保全的保全后文件的下载地址进行下载。（下载地址有时效性，过期后重新按此方法取得新地址）

        //请求服务器
        $response = RopUtils::doPostByObj($requestObj);
        //以下为返回的一些处理
        $responseJson = json_decode($response);
        return $responseJson;
    }

}