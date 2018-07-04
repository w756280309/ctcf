<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-3-1
 * Time: 上午10:21
 */
namespace common\lib\pdf;

use common\models\order\OnlineOrder;
use common\models\user\User;
use common\utils\SecurityUtils;
use Knp\Snappy\Pdf;
use Yii;

//用于生成pdf文件
class CreatePDF
{
    /**
     * 根据指定模板生成合同
     * @param integer $type 合同类型
     * @param string $content 合同模板
     * @param OnlineOrder $onlineOrder
     * @return string
     */
    public function handleContent($type, $content, OnlineOrder $onlineOrder, User $user)
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
        $mobile = '';
        $chapter = '<img style="width: 169px;margin-top:-100px;" src="data:image/jpeg;base64,'
            . base64_encode(@file_get_contents(Yii::getAlias('@backend') . '/web'
                . Yii::$app->params['platform_info.company_seal_640'])).'">';
        $fourTotalAsset = null;
        $borrowerName = null;   //借款人
        $borrowerCardNumber = null; //借款人身份证
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
                $borrower = $loan->borrower;
                if (!is_null($borrower)) {
                    $borrowerName = $borrower->real_name;
                    $borrowerCardNumber = SecurityUtils::decrypt($borrower->safeIdCard);
                }
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
                    case '平台章':
                        $content = str_replace($array[0][$key], $chapter, $content);
                        break;
                    case '4期总资产':
                        $content = str_replace($array[0][$key], $fourTotalAsset, $content);
                        break;
                    case '借款人':
                        $content = str_replace($array[0][$key], $borrowerName, $content);
                        break;
                    case '借款人身份证':
                        $content = str_replace($array[0][$key], $borrowerCardNumber, $content);
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
    public function createPdf($content, $fileName)
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
        return null;
    }
}