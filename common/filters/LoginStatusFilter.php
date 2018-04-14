<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-4-12
 * Time: 下午2:44
 */
namespace common\filters;

use common\models\adminuser\Admin;
use common\models\user\User;
use yii\base\ActionFilter;
use common\controllers\HelpersTrait;
use Yii;
use yii\helpers\Url;

class LoginStatusFilter extends ActionFilter
{
    use HelpersTrait;
    public function beforeAction($action)
    {
        $user = Yii::$app->user->identity;
        if (!empty($user) && !defined('IN_APP')) {
            if ($user instanceof User) {    //用户登录控制
                $equipment = CLIENT_TYPE == 'pc' ? 'pc' : 'wap';
                $loginSign = Yii::$app->session->getId();
                if (!empty($equipment) && !empty($loginSign)) {
                    $redis = Yii::$app->redis;
                    $res = json_decode($redis->hget('login_status_user', $user->id), true);
                    //当前设备已在其他地方登，强制退出
                    if (!empty($res[$equipment]) && $loginSign != $res[$equipment]) {
                        //return Yii::$app->getResponse()->redirect(Url::to('/site/logout?forcedReturn=true'), 302)->send();
                        Yii::$app->user->logout();
                    }
                }
            } elseif ($user instanceof Admin) { //管理员登录控制

                $loginSign = Yii::$app->session->getId();
                if (!empty($loginSign)) {
                    $redis = Yii::$app->redis;
                    $res = $redis->hget('login_status_admin', $user->id);
                    if (!empty($res) && $loginSign != $res) {
                        //return Yii::$app->getResponse()->redirect(Url::to('/login/logout?forcedReturn=true'), 302)->send();
                        Yii::$app->user->logout();
                    }
                }
            }
        }
        return true;
    }
}