<?php
namespace common\filters;

use common\controllers\HelpersTrait;
use common\models\growth\AppMeta;
use common\models\stats\Perf;
use common\models\user\User;
use Yii;
use yii\base\ActionFilter;
use yii\web\Response;

/**
 * 响应监管：仅向实名用户推荐理财, 当首页、理财页、项目详情页 没有登录、没有实名时候调整对应页面
 *
 * Class SuperviseAccessFilter
 * @package common\filters
 */
class SuperviseAccessFilter extends ActionFilter
{
    use HelpersTrait;

    public function beforeAction($action)
    {
        $actionId = $action->getUniqueId();
        $appId = Yii::$app->id;
        if ($appId === 'app-wap') {
            return $this->wapAccess($action, $actionId);
        } elseif ($appId === 'app-frontend') {
            return $this->pcAccess($action, $actionId);
        } else {
            return true;
        }
    }

    private function pcAccess($action, $actionId)
    {
        if (in_array($actionId, [
            'licai/index',
            'deal/deal/detail',
            'licai/notes',
            'licai/loan',
            'credit/note/detail',
            'upload/showpic',
        ])) {
            /**
             * @var Response $response
             * @var User $user
             */
            $response = Yii::$app->response;
            $user = Yii::$app->getUser()->getIdentity();
            if (is_null($user)) {
                $action->controller->layout = '@frontend/views/layouts/main';
                $response->content = $action->controller->render('@frontend/views/guide/login_guide.php');
                return false;
            } elseif (!$user->isIdVerified()) {
                $action->controller->layout = '@frontend/views/layouts/main';
                $response->content = $action->controller->render('@frontend/views/guide/idcard_guide.php');
                return false;
            }

            if ('on' === AppMeta::getValue('xs_require_hide_deal')) {
                if (!$user->getUserIsInvested()) {
                    $action->controller->layout = '@frontend/views/layouts/main';
                    $response->content = $action->controller->render('@frontend/views/guide/invest_guide.php');
                    return false;
                }
            }
        }
        return true;
    }

    private function wapAccess($action, $actionId)
    {
        if (in_array($actionId, [
            'deal/deal/index',
            'deal/deal/detail',
            'licai/notes',
            'credit/note/detail',
            'issuer/index',
            'upload/showpic',
        ])) {
            /**
             * @var Response $response
             * @var User $user
             */
            $response = Yii::$app->response;
            $user = Yii::$app->getUser()->getIdentity();
            if (is_null($user)) {
                //如果是m站且App版本大于1.6.2
                if (!defined('IN_APP') || (defined('IN_APP') && strcmp($this->getAppVersion(), '1.6.2') > 0)) {
                    if ('deal/deal/index' === $actionId) {
                        return true;
                    }
                }
                $action->controller->layout = '@app/views/layouts/normal';
                $response->content = $action->controller->render('@wap/views/guide/login_guide.php', [
                    'statsData' => $this->getStatsData(),
                ]);
                return false;
            } elseif (!$user->isIdVerified()) {
                $action->controller->layout = '@app/views/layouts/normal';
                $response->content = $action->controller->render('@wap/views/guide/idcard_guide.php');
                return false;
            }

            if ('on' === AppMeta::getValue('xs_require_hide_deal') && 'deal/deal/index' === $actionId) {
                if (!$user->getUserIsInvested()) {
                    $view = \Yii::$app->view;
                    $view->registerJsFile(ASSETS_BASE_URI.'layer/layer.js');
                    $view->registerCssFile(ASSETS_BASE_URI.'layer/need/layer.css', ['position' => 1]);
                    $view->registerCss(<<<CSS
.customer-layer-popuo .layui-m-layercont {
    text-align:left;
}
.customer-layer-popuo .layui-m-layerbtn span[yes] {
    color :#ff6058;
}

.layui-m-layershade {
    background-color:rgba(0,0,0,0.97) !important;
} 
CSS
);
                    $view->registerJs(<<<JS
layer.open({
        title: [
            '温馨提示',
            'background-color: #ff6058; color:#fff;'
        ]
        ,content: '您好，目前理财产品正在更新，您可以先浏览其他内容。   (详情可拨打客服电话400-101-5151)'
        ,shadeClose:false
        ,className: 'customer-layer-popuo'
        ,btn: ['先逛一逛']
        ,yes: function(index){
            location.href ="/?mark="+Math.random()*100000;
        }
    });
JS
);
                }
            }
        }
        return true;
    }

    //获取登录引导页面统计数据，以亿为单位，取整
    private function getStatsData()
    {
        $cache = Yii::$app->db_cache;
        if (!$cache->get('index_stats')) {
            $statsData = Perf::getStatsForIndex();

            $cache->set('index_stats', $statsData, 600);   //缓存十分钟
        }
        $statsData = $cache->get('index_stats');
        $statsData['totalTradeAmount'] = bcdiv($statsData['totalTradeAmount'], 100000000, 0);
        $statsData['totalRefundAmount'] = bcdiv($statsData['totalRefundAmount'], 100000000, 0);
        $statsData['totalRefundInterest'] = bcdiv($statsData['totalRefundInterest'], 100000000, 0);
        return $statsData;
    }
}
