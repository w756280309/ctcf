<?php

namespace common\filters;

use Yii;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\di\Instance;
use yii\web\User;
use yii\web\ForbiddenHttpException;
use common\models\app\AccessToken;

/**
 * AccessControl provides simple access control based on a set of rules.
 */
class UserAccountAcesssControl extends ActionFilter
{
    /**
     * @var User|array|string the user object representing the authentication status or the ID of the user application component.
     *                        Starting from version 2.0.2, this can also be a configuration array for creating the object.
     */
    public $user = 'user';
    /**
     * @var callable a callback that will be called if the access should be denied
     *               to the current user. If not set, [[denyAccess()]] will be called.
     *
     * The signature of the callback should be as follows:
     *
     * ~~~
     * function ($rule, $action)
     * ~~~
     *
     * where `$rule` is the rule that denies the user, and `$action` is the current [[Action|action]] object.
     * `$rule` can be `null` if access is denied because none of the rules matched.
     */
    public $denyCallback;
    /**
     * @var array the default configuration of access rules. Individual rule configurations
     *            specified via [[rules]] will take precedence when the same property of the rule is configured.
     */
//    public $ruleConfig = ['class' => 'yii\filters\AccessRule'];
    /**
     * @var array a list of access rule objects or configuration arrays for creating the rule objects.
     *            If a rule is specified via a configuration array, it will be merged with [[ruleConfig]] first
     *            before it is used for creating the rule object.
     *
     * @see ruleConfig
     */

    /**
     * Initializes the [[rules]] array by instantiating rule objects from configurations.
     */
    public function init()
    {
        parent::init();

        $this->user = Instance::ensure($this->user, User::className());
    }

    /**
     * This method is invoked right before an action is to be executed (after all possible filters.)
     * You may override this method to do last-minute preparation for the action.
     *
     * @param Action $action the action to be executed.
     *
     * @return bool whether the action should continue to be executed.
     */
    public function beforeAction($action)
    {
        if (!defined('IN_APP') && null !== $this->user->identity && 0 === $this->user->identity->status) {
            return Yii::$app->getResponse()->redirect('/site/usererror');
        }

        if (defined('IN_APP') && Yii::$app->request->get('token')) {
            $accessToken = AccessToken::isEffectiveToken(Yii::$app->request->get('token'));
            if (false === $accessToken) {
                echo json_encode([
                    'status' => 'fail', //程序级别成功失败
                    'message' => '需要登录',
                    'data' => null,
                ]);

                return false;
            } else {
                Yii::$app->user->setIdentity($accessToken->user);
                if (date('Ymd', strtotime('+30 day')) == date('Ymd', $accessToken->expireTime)) {
                    //同一天不用更新
                    $accessToken->expireTime = strtotime('+30 day');//延长有效期30天
                    $accessToken->save(false);
                }
            }
        }

        return true;
    }

    /**
     * Denies the access of the user.
     * The default implementation will redirect the user to the login page if he is a guest;
     * if the user is already logged, a 403 HTTP exception will be thrown.
     *
     * @param User $user the current user
     *
     * @throws ForbiddenHttpException if the user is already logged in.
     */
    protected function denyAccess($user)
    {
        if ($user->getIsGuest()) {
            $user->loginRequired();
        } else {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }
}
