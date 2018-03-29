<?php

namespace common\filters;

use common\controllers\HelpersTrait;
use Yii;
use yii\base\ActionFilter;

/**
 * Class LoginAccessControl
 */
class LoginAccessControl extends ActionFilter
{
    use HelpersTrait;
    public function beforeAction($action)
    {
        $appVersionCode = $this->getAppVersion();
        $actionId = $action->getUniqueId();
        //针对deal/deal/index路径做返回url处理
        if ($actionId === 'deal/deal/index') {
            //如果在APP环境中且版本小于等于1.6.2，不走此逻辑
            if (defined('IN_APP') && strcmp($appVersionCode, '1.6.2') <= 0) {
                return true;
            }
            $user = Yii::$app->getUser()->getIdentity();
            if (null === $user) {
                if (defined('IN_APP')) {
                    $url = '/';
                } else {
                    $url = Yii::$app->request->hostInfo.'/';
                }
                Yii::$app->response->redirect('/site/login?cancelUrl='.urlencode($url))->send();
            }
        }

        return true;
    }
}
