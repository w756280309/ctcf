<?php

namespace common\models\adminuser;

use Yii;
use yii\base\Model;

class Permission extends Model
{
    /**
     * 判断权限
     * @param type $auth_code
     * @return boolean
     */
    public static function checkAuthByCode($admin_id = "", $auth_code = "")
    {
        //如果$auth_code不为空， 查找当前管理者对应的权限，如果找不到就不存在这个权限,反之即存在权限
        if (!empty($auth_code)) {
            if (AdminAuth::find()->where(['auth_sn' => $auth_code, 'admin_id' => $admin_id])->count()) {
                return true;
            }
        }
        return false;
    }

    /**
     * 没有auth_code的话，判断管理者权限中是否包含此权限的url.
     */
    public static function checkAuthByPath($admin_id = "", $path = "")
    {
        if (empty($path)) {
            return true;
        }

        //需要排除权限限制的path
        $allow_paths = array('site/index', 'site/deny', 'system/role/authlist', 'adminuser/admin/authlist', 'adminuser/admin/roles');
        if (in_array($path, $allow_paths)) {
            return true;
        }

        $auth_table = Auth::tableName();
        $admin_auth_table = AdminAuth::tableName();

        $adminAuth = AdminAuth::find()
            ->innerJoin($auth_table, $admin_auth_table.'.auth_sn='.$auth_table.'.sn')
            ->where(['admin_id' => $admin_id])
            ->andWhere(['or', ['path' => $path], ['path' => $path.'/index']])
            ->select("auth_sn,path")
            ->all();

        return !empty($adminAuth);
    }
}