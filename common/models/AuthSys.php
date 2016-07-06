<?php

namespace common\models;

use common\models\adminuser\Auth;
use common\models\adminuser\AdminAuth;
use Yii;

class AuthSys
{
    const OP_TYPE_PAGE = 1;
    const OP_TYPE_LIST = 2;
    const LIST_RULE_ADD = '01';   //添加
    const LIST_RULE_EDIT = '02';   //修改
    const LIST_RULE_DEL = '03'; //删除
    const LIST_RULE_COPY = '04'; //复制
    const LIST_RULE_LINE_ON = '05';  //上线
    const LIST_RULE_LINE_OFF = '06';  //下线
    const LIST_RULE_BATCH_DEL = '07';  //批量删除
    const LIST_RULE_SEARCH = '08'; //产品管理——投资记录查看
    const LIST_RULE_EXAMIN = '09'; //会员列表——重新审核
    const LIST_RULE_FREEZE = '10';   //冻结激活
    const LIST_RULE_DETAIL = '11';   //会员投资明细
    const LIST_RULE_IMPORT = '12'; //渠道管理——导入数据
    const LIST_RULE_EXPLORT = '13';   //渠道管理——导出会员数据
    const LIST_RULE_AUTH = '14';  //权限——禁用
    const LIST_RULE_DISPLAY = '15';  //展示？隐藏
    const LIST_RULE_ONLINE_ANNUL = '16'; //线上产品管理——撤销
    const LIST_RULE_ONLINE_BATCH = '17';  //线上产品管理——放款批次
    const LIST_RULE_DRAW_LOOK = '18';  //提现管理——查看
    const LIST_RULE_DRAW_AUDIT = '19';  //提现管理——审核
    const LIST_RULE_DRAW_LOAN = '20';  //提现管理——放款
    const LIST_RULE_USER_ACCESS = '21';  //会员列表——账户查阅
    const LIST_RULE_USER_CREATE = '23';  //会员列表——创建融资账户
    const LIST_RULE_CHECKOUDER = '24'; //产品管理——线上标的管理——查看订单

    public static function checkAuth($code = "")
    {
        $admin = Yii::$app->user->getIdentity();
        $admin_id = $admin->id;

        if ($admin_id == Yii::$app->params['admin']) {
            return true;
        }

        return \common\models\adminuser\Permission::checkAuthByPath($admin_id, \Yii::$app->requestedRoute);
    }

    /**
     * @param type $psn
     * @return type根据父sn获取子菜单
     */
    public static function getMenus($psn = "0")
    {
        $admin = Yii::$app->user->getIdentity();
        $admin_id = $admin->id;
        $db = Yii::$app->db;

        $auth_table = Auth::tableName();
        $admin_auth_table = AdminAuth::tableName();
        $sql = AdminAuth::find()->innerJoin($auth_table, $admin_auth_table.".auth_sn=".$auth_table.".sn")
                ->where(['admin_id' => $admin_id, $auth_table.'.psn' => $psn])->select("$auth_table.auth_name,$auth_table.psn,auth_sn,path")->createCommand()->getRawSql();
        $menus = $db->createCommand($sql)->queryAll();

        return $menus;
    }

    public static function checkMenus($data = array(), $mtype = "P1", $msort = "001", $type = 1, $num = "00")
    {
        if (empty($data)) {
            return false;
        }

        $num = $mtype.$msort.$type.$num;
        foreach ($data as $val) {
            if ($val['auth_sn'] == $num) {
                return true;
            }
        }

        return false;
    }
}