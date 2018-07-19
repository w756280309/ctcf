<?php

namespace console\controllers;

use common\lib\user\UserStats;
use common\models\promo\InviteRecord;
use common\models\tx\CreditOrder;
use common\models\user\User;
use common\models\user\UserInfo;
use common\utils\SecurityUtils;
use GuzzleHttp\Client;
use yii\console\Controller;
use Yii;

class UserInfoController extends Controller
{
    public function actionInit()
    {
        $db = Yii::$app->db;
        $userCount = User::find()->count();
        $userInfoCount = UserInfo::find()->count();

        if ($userInfoCount !== $userCount) {
            $userInfo = UserInfo::find()->select('user_id')->column();
            $users = User::find()->select('id')->column();
            //待新增初始化的UserInfo
            $userIds = array_diff($users, $userInfo);
            $queryUserIds = [];
            foreach ($userIds as $k => $userId) {
                $queryUserIds[$k] = [$userId];
            }
            $num = $db->createCommand()->batchInsert('user_info', ['user_id'], $queryUserIds)->execute();
            if ($num <= 0) {
                $this->stdout('初始化UserInfo失败');
                return self::EXIT_CODE_ERROR;
            }
        }

        //获得所有的被邀请的用户ID集合
        $invitedUsers = InviteRecord::find()
            ->select('invitee_id')
            ->distinct()
            ->column();
        $usersIsAffiliator = UserInfo::find()->select('user_id')->where(['isAffiliator' => true])->column();

        if (!empty(array_diff($invitedUsers, $usersIsAffiliator))) {
            //更新isAffiliator字段为true
            $num = $db->createCommand()->update('user_info', ['isAffiliator' => true], ['in', 'user_id', $invitedUsers])->execute();
            if ($num <= 0) {
                $this->stdout('更新失败');
                return self::EXIT_CODE_ERROR;
            }
        }

        $this->stdout('UserInfo被邀请人初始化完毕');
        return self::EXIT_CODE_NORMAL;
    }

    /**
     * 根据注册用户的IP信息解析成所属地信息, 存入user.regLocation字段当中.
     *
     * 1. 调用淘宝的http://ip.taobao.com/instructions.php,请求频率不能超过10qps;
     */
    public function actionRegLocation()
    {
        $users = User::find()
            ->where(['regLocation' => null])
            ->andWhere('registerIp is not null')
            ->orderBy(['id' => SORT_DESC])
            ->limit(10)
            ->all();

        $client = new Client([
            'connect_timeout' => 2,
            'timeout' => 2,
        ]);

        foreach ($users as $user) {
            try {
                $request = $client->get('http://ip.taobao.com/service/getIpInfo.php?ip='.$user->registerIp);
                $resp = json_decode($request->getBody()->getContents(), true);

                if (0 === $resp['code']) {
                    $user->setRegLocation($resp['data']);
                    $user->save(false);
                }
            } catch (\Exception $e) {
                //DO NOTHING
            }

            usleep(100000);  //延迟0.1s
        }

        return self::EXIT_CODE_NORMAL;
    }


    /**
     * 更新UserInfo表转让字段信息creditInvestCount，creditInvestTotal
     * 这两个字段只增不减
     *
     * @return int
     * @throws \Exception
     */
    public function actionInitCredit()
    {
        $orders = CreditOrder::find()
            ->select("user_id, count(id) as totalNum, sum(principal) as investTotal")
            ->where(['buyerPaymentStatus' => 1])
            ->groupBy('user_id')
            ->asArray()
            ->all();

        $this->stdout('共有'.count($orders).'人需要初始化转让数据'.PHP_EOL);
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            foreach ($orders as $order) {
                $sql = "update user_info set creditInvestCount=:creditInvestCount,creditInvestTotal=:creditInvestTotal where user_id = :userId";
                $updateCredit = $db->createCommand($sql, [
                    'creditInvestCount' => $order['totalNum'],
                    'creditInvestTotal' => $order['investTotal']/100,
                    'userId' => $order['user_id'],
                ])->execute();

                if (0 === $updateCredit) {
                    throw new \Exception($order['user_id'].'更新失败');
                }
            }
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
        $this->stdout('脚本执行结束');

        return Controller::EXIT_CODE_NORMAL;
    }

    public function actionThirdParty()
    {
        $sql = "SELECT
u.real_name '姓名',
u.safeMobile '联系方式',
tpc.publicId '对吧ID',
af.name '分销商',
ui.investTotal '累计投资'
FROM third_party_connect tpc
INNER JOIN user u 
ON u.id = tpc.user_id
INNER JOIN user_info ui
ON tpc.user_id = ui.user_id
LEFT JOIN user_affiliation ua 
ON tpc.user_id = ua.user_id
LEFT JOIN affiliator af
ON af.id = ua.affiliator_id";
        $datas = Yii::$app->db->createCommand($sql)->queryAll();
        $exportData[] = ['姓名', '联系方式', '分销商', '对吧ID', '累计投资'];
        foreach ($datas as $data) {
            array_push($exportData, [
                $data['姓名'],
                SecurityUtils::decrypt($data['联系方式']),
                $data['分销商'],
                $data['对吧ID'],
                $data['累计投资'],
            ]);
        }
        //导出Excel
        $file = Yii::getAlias('@app/runtime/third_party_user_' . date("YmdHis") . '.xlsx');
        $objPHPExcel = UserStats::initPhpExcelObject($exportData);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        $this->stdout('操作成功，文件：'. $file . PHP_EOL);
    }

    public function actionX()
    {
        $sql = "select u.real_name, u.safeMobile,p.title,date(from_unixtime(p.jixi_time)) startDate,date(from_unixtime(p.finish_date)) endDate,from_unixtime(o.created_at) investTime, o.order_money from online_order o inner join user u on o.uid=u.id 
            left join online_product p on o.online_pid=p.id
            where o.status=1 and u.campaign_source ='jdgbdst'";

        $datas = Yii::$app->db->createCommand($sql)->queryAll();
        $exportData[] = ['姓名', '手机号', '产品名称', '起息日', '到期日', '认购时间', '认购金额'];
        foreach ($datas as $data) {
            array_push($exportData, [
                $data['real_name'],
                SecurityUtils::decrypt($data['safeMobile']),
                $data['title'],
                $data['startDate'],
                $data['endDate'],
                $data['investTime'],
                $data['order_money'],
            ]);
        }
        //导出Excel
        $file = Yii::getAlias('@app/runtime/aff-' . date("YmdHis") . '.xlsx');
        $objPHPExcel = UserStats::initPhpExcelObject($exportData);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        $this->stdout('操作成功，文件：'. $file . PHP_EOL);
    }
}
