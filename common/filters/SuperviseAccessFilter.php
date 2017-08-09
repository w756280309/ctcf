<?php
namespace common\filters;

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
            'site/index',
            'licai/index',
            'deal/deal/detail'
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
        }
        return true;
    }

    private function wapAccess($action, $actionId)
    {
        if (in_array($actionId, [
            'site/index',
            'deal/deal/index',
            'deal/deal/detail'
        ])) {
            /**
             * @var Response $response
             * @var User $user
             */
            $response = Yii::$app->response;
            $user = Yii::$app->getUser()->getIdentity();
            if (is_null($user)) {
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