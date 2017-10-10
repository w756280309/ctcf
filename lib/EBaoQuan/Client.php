<?php
/*
新建保全思路
（1）点击“确认计息”。先执行确认计息流程，之后进行保全流程
（2）根据标的，搜索所有成功订单。
（3）根据成功订单，生成指定格式的pdf合同（ContractTemplate::replaceTemplate()）,可以替换认购协议、风险提示书；项目说明直接保存
（4）将合同保存为指定文件名，文件保存到临时目录中
（5）新建保全，保存本地数据库【数据结构见表：ebao_quan】
（6）删除临时文件
 */
namespace EBaoQuan;

use common\models\contract\ContractTemplate;
use common\models\order\EbaoQuan;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\user\User;
use Knp\Snappy\Pdf;
use org_mapu_themis_rop_tool\RopUtils as RopUtils;
use org_mapu_themis_rop_model\PingRequest as PingRequest;
use org_mapu_themis_rop_model\ContractFilePreservationCreateRequest as ContractFilePreservationCreateRequest;
use org_mapu_themis_rop_model\UploadFile as UploadFile;
use org_mapu_themis_rop_model\PreservationType as PreservationType;
use org_mapu_themis_rop_model\UserIdentiferType as UserIdentiferType;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use org_mapu_themis_rop_model\PreservationGetRequest as PreservationGetRequest;
use org_mapu_themis_rop_model\CertificateLinkGetRequest as CertificateLinkGetRequest;
use org_mapu_themis_rop_model\ContractFileDownloadUrlRequest as ContractFileDownloadUrlRequest;
use org_mapu_themis_rop_model\ContractStatusGetRequest as ContractStatusGetRequest;

class Client
{
    //标的订单保全
    public static function baoQuanLoanOrder(OnlineOrder $order)
    {
        $loan = $order->loan;
        $user = $order->user;
        $agreements = ContractTemplate::find()->select(['pid', 'name', 'content'])->where(['pid' => $order->online_pid])->asArray()->all();
        if (count($agreements) > 0) {
            try {
                $content = '';
                foreach ($agreements as $k => $v) {
                    //用订单填充合同模板
                    $c = $v['content'];
                    if ($c) {
                        //获取合同模板
                        $c = self::handleContent($k, $c, $order);
                        $content = $content . $c . ' <br/><br/><hr/><br/><br/>';
                    }
                }
                //多份合同合并成一份
                $content = rtrim($content, '<br/><br/><hr/><br/><br/>');
                // $url = file_get_contents(dirname(__DIR__).'/../lib/EBaoQuan/jinjiao_data_url');
                //$content .= '<img style="position: fixed;top:50px;right:170px;" src="'.$url.'"/>'; 保全签章
                //生成PDF
                $file = self::createPdf($content, $order->sn);
                if (file_exists($file)) {
                    //生成保全
                    $responseJson = self::contractFileCreate($file, $user, $order->order_money, $loan->title);
                    self::addBaoQuan($responseJson, EbaoQuan::TYPE_E_LOAN, $order->id, EbaoQuan::ITEM_TYPE_LOAN_ORDER, $loan->title, $user->id);
                    unlink($file);
                }
            } catch (Exception $e) {
                \Yii::trace('标的订单保全失败，订单ID:'.$order->id.';保全失败,失败信息'.$e->getMessage(), 'bao_quan');
                throw $e;
            }
        }
    }

    /**
     * 债权保全（买方标的合同和买方转让合同）
     * @param array $contracts  填充后的合同，包括标的相关合同和债权相关合同
     */
    public static function createCreditBq(array $contracts, User $user, array $asset)
    {
        if (!isset($contracts['loanContract']) || !isset($contracts['creditContract']) || !isset($contracts['loanAmount']) || !isset($contracts['loanId'])) {
            throw new \Exception('合同内容不全');
        }
        $loan = OnlineProduct::findOne($contracts['loanId']);
        if (count($contracts['loanContract']) <= 0 || count($contracts['creditContract']) <= 0 || empty($loan)) {
            throw new \Exception('合同内容不全');
        }
        $loanContract = $contracts['loanContract'];
        //合并标的合同
        $finalLoanContent = implode(array_column($loanContract, 'content'), ' <br/><br/><hr/><br/><br/>');
        $loanAmount = $contracts['loanAmount'];
        //保全标的合同
        try {
            $file = self::createPdf($finalLoanContent, time().rand(10000, 99999));
            if (file_exists($file)) {
                //生成保全
                $responseJson = self::contractFileCreate($file, $user, $loanAmount, $loan->title);
                self::addBaoQuan($responseJson, EbaoQuan::TYPE_E_LOAN, $asset['credit_order_id'], EbaoQuan::ITEM_TYPE_CREDIT_ORDER, $loan->title, $user->id);
                unlink($file);
            }
        } catch (\Exception $ex) {
            \Yii::trace('购买债权订单成功之后保全标的合同，标的ID:'.$contracts['loanId'].';保全失败,资产ID:'.$asset['id'].';失败信息'.$ex->getMessage(), 'bao_quan');
            throw $ex;
        }
        //保全债权合同
        $creditContract = $contracts['creditContract'];
        foreach ($creditContract as $contract) {
            if (!isset($contract['type']) || !isset($contract['amount'])) {
                throw new \Exception('合同信息不完善');
            }
            if ($contract['type'] === 'credit_order') {
                //只保全其购买他人转让的合同
                //保全标的合同
                try {
                    $file = self::createPdf($contract['content'], time().rand(10000, 99999));
                    if (file_exists($file)) {
                        //生成保全
                        $responseJson = self::contractFileCreate($file, $user, $contract['amount'], $loan->title);
                        self::addBaoQuan($responseJson, EbaoQuan::TYPE_E_CREDIT, $asset['credit_order_id'], EbaoQuan::ITEM_TYPE_CREDIT_ORDER, $loan->title, $user->id);
                        unlink($file);
                    }
                } catch (\Exception $ex) {
                    \Yii::trace('购买债权订单成功之后保全转让合同，标的ID:'.$contracts['loanId'].';保全失败,资产ID:'.$asset['id'].';失败信息'.$ex->getMessage(), 'bao_quan');
                    throw $ex;
                }
            }
        }
    }

    //债权转让保全（卖方合同）
    public static function createCreditSellerBq($content, User $user, $amount, OnlineProduct $loan, $noteId, User $seller) {
        try {
            $file = self::createPdf($content, time().rand(10000, 99999));
            if (file_exists($file)) {
                //生成保全
                $responseJson = self::contractFileCreate($file, $seller, $amount, $loan->title);
                self::addBaoQuan($responseJson, EbaoQuan::TYPE_E_CREDIT, $noteId, EbaoQuan::ITEM_TYPE_CREDIT_NOTE, $loan->title, $seller->id);
                unlink($file);
            }
        } catch (\Exception $ex) {
            \Yii::trace('转让结束之后生成保全合同，标的ID:'.$loan->id.';保全失败,债权ID:'.$noteId.';失败信息'.$ex->getMessage(), 'bao_quan');
            throw $ex;
        }
    }

    //保全成功之后添加保全记录
    private static function addBaoQuan($responseJson, $type, $itemId, $itemType, $title, $userID)
    {
        $model = EbaoQuan::find()->where([
            'type' => $type,
            'title' => $title,
            'itemId' => $itemId,
            'itemType' => $itemType,
            'uid' => $userID])->one();
        if (null === $model) {
            $model = new EbaoQuan([
                'type' => $type,
                'title' => $title,
                'itemId' => $itemId,
                'itemType' => $itemType,
                'uid' => $userID]);
        }
        if ($responseJson->success) {
            $model->setAttributes([
                'baoId' => $responseJson->preservationId,
                'docHash' => $responseJson->docHash,
                'preservationTime' => $responseJson->preservationTime,
                'success' => $responseJson->success
            ], false);
        } else {
            $model->setAttributes([
                'docHash' => $responseJson->docHash,
                'preservationTime' => $responseJson->preservationTime,
                'success' => $responseJson->success,
                'errMessage' => $responseJson->message . '解决方案：' . $responseJson->solution
            ], false);
        }

        return $model->save(false);
    }

    //测试连通性
    public static function ping()
    {
        //组建请求参数
        $requestObj = new PingRequest();
        //请求
        $response = RopUtils::doPostByObj($requestObj);

        if ($response) {
            //以下为返回的一些处理
            $responseJson = json_decode($response);
            if ($responseJson->success) {
                echo $requestObj->getMethod() . "->处理成功";
            } else {
                echo $requestObj->getMethod() . "->处理失败";
            }
        } else {
            echo '链接异常';
        }
    }

    /**
     * 生成一个合同下载地址
     * @param EbaoQuan $ebaoQuan
     * @return string|null 返回下载地址或空字符串
     * @throws \Exception
     */
    public static function contractFileDownload(EbaoQuan $ebaoQuan)
    {
        //组建请求参数
        $requestObj = new ContractFileDownloadUrlRequest();
        //init params
        $requestObj->preservationId = $ebaoQuan->baoId;;
        //请求
        $response = RopUtils::doPostByObj($requestObj);
        //以下为返回的一些处理
        $responseJson = json_decode($response);
        if ($responseJson->success) {
            return $responseJson->downUrl;
        } else {
            return null;
        }
    }

    //用户查看保全信息，暂时未使用
    public static function preservationGet(EbaoQuan $ebaoQuan)
    {
        //组建请求参数
        $requestObj = new PreservationGetRequest();
        $requestObj->preservationId = $ebaoQuan->baoId;
        //请求
        $response = RopUtils::doPostByObj($requestObj);

        //以下为返回的一些处理
        $responseJson = json_decode($response);
        if ($responseJson->success) {
            echo $requestObj->getMethod() . "->处理成功";
        } else {
            echo $requestObj->getMethod() . "->处理失败";
        }
    }

    /**
     * 查看保全证书，返回一个url，通过url访问旺财谷证书
     * @param EbaoQuan $ebaoQuan
     * @return string|null 证书查看地址或空字符村
     * @throws \Exception
     */
    public static function certificateLinkGet(EbaoQuan $ebaoQuan)
    {
        //组建请求参数
        $requestObj = new CertificateLinkGetRequest();
        $requestObj->preservationId = $ebaoQuan->baoId;

        //请求
        $response = RopUtils::doPostByObj($requestObj);

        //以下为返回的一些处理
        $responseJson = json_decode($response);
        if ($responseJson->success) {
            return $responseJson->link;
        } else {
            return null;
        }
    }

    //查看用户确认状态，暂时未使用
    public static function contractStatusGet(EbaoQuan $ebaoQuan)
    {
        //组建请求参数
        $requestObj = new ContractStatusGetRequest();
        //init params
        $requestObj->preservationId = $ebaoQuan->baoId;
        //请求
        $response = RopUtils::doPostByObj($requestObj);
        //echo stristr(strtolower("123456"),"6")==null;

        //以下为返回的一些处理
        $responseJson = json_decode($response);
        if ($responseJson->success) {
            echo $requestObj->getMethod() . "->处理成功";
        } else {
            echo $requestObj->getMethod() . "->处理失败";
        }
    }

    //新建保全
    /**
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

        /*//企业用户
        $requestObj->userIdentifer="123123123123123";
        $requestObj->userRealName="XX公司";
        $requestObj->userIdentiferType=UserIdentiferType::$BUSINESS_LICENSE;*/

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
     * 根据指定模板生成合同
     * @param integer $type 合同类型
     * @param string $content 合同模板
     * @param OnlineOrder $onlineOrder
     * @return string
     */
    private static function handleContent($type, $content, OnlineOrder $onlineOrder)
    {
        if (in_array($type, [0, 1, 2])) {
            $title = '产品合同';
            $content = '<h4 style="margin: 5px auto;text-align: center;font-size: 14px;color: #000;line-height: 18px;">' . $title . '</h4>' . $content;
        }
        $real_name = '';
        $idCard = '';
        $date = "年月日";
        $money = "";
        if (null !== $onlineOrder) {
            $date = date("Y年m月d日", $onlineOrder->order_time);
            $money = $onlineOrder->order_money;
            if (null !== $onlineOrder->user) {
                $real_name = $onlineOrder->user->real_name;
                $idCard = $onlineOrder->user->idcard;
            }
        }
        $res = preg_match_all('/(\｛|\{){2}(.+?)(\｝|\}){2}/is', $content, $array);
        if ($res && isset($array[0]) && count($array[0]) > 0 && isset($array[2]) && count($array[2]) > 0) {
            foreach ($array[2] as $key => $value) {
                switch (strip_tags($value)) {
                    case '投资人';
                        $content = str_replace($array[0][$key], $real_name, $content);
                        break;
                    case '身份证号':
                        $content = str_replace($array[0][$key], $idCard, $content);
                        break;
                    case '认购日期':
                        $content = str_replace($array[0][$key], $date, $content);
                        break;
                    case '认购金额':
                        $content = str_replace($array[0][$key], $money, $content);
                        break;
                    default:
                        break;
                }
            }
        }
        return $content;
    }
}