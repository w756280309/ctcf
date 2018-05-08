<?php

namespace app\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Adv;
use common\models\media\Media;
use common\models\news\News;
use common\models\promo\PromoLotteryTicket;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Controller;

/**
 * 资讯信息类.
 */
class NewsController extends Controller
{
    use HelpersTrait;

    /**
     * 资讯列表页.
     */
    public function actionIndex($page = 1, $size = 10)
    {
        $user = Yii::$app->user->getIdentity();
        if (!is_null($user)) {
            $totalAssets = $user->jGMoney;
        } else {
            $totalAssets = 0;
        }
        $data = News::find()
            ->where(['status' => News::STATUS_PUBLISH, 'allowShowInList' => true])
            ->andWhere(['<=', "investLeast", $totalAssets])
            ->orderBy(['news_time' => SORT_DESC]);

        $pg = Yii::$container->get('paginator')->paginate($data, $page, $size);
        $model = $pg->getItems();

        $tp = $pg->getPageCount();
        $code = ($page > $tp) ? 1 : 0;

        if (Yii::$app->request->isAjax) {
            $message = ($page > $tp) ? '数据错误' : '消息返回';

            return ['header' => $pg, 'data' => $model, 'code' => $code, 'message' => $message];
        }

        return $this->render('index', ['model' => $model, 'header' => $pg->jsonSerialize()]);
    }

    /**
     * 资讯详情页.
     */
    public function actionDetail($id)
    {
        if (empty($id) || is_int($id)) {
            throw $this->ex404();     //参数无效,抛出404异常
        }

        $new = News::findOne($id);
        if (is_null($new) || $new->status != News::STATUS_PUBLISH) {
            throw $this->ex404();     //不存在的文章或文章隐藏了,抛出404异常
        }
        $user = Yii::$app->user->getIdentity();
        if (!is_null($user)) {
            $totalAssets = $user->jGMoney;
        } else {
            $totalAssets = 0;
        }
        if ($new->investLeast > $totalAssets) {
            throw $this->ex404();
        }
        return $this->render('detail', ['new' => $new]);
    }

    /**
     * 活动入口
     * 活动记录首先按照序号，然后按照活动结束时间降序排列，分页加载，每页加载5条数据
     * @param int $page  当前页码
     * @param int $size   每页加载条数
     * @return array      活动记录及分页信息
     */
    public function actionPromo($page = 1, $size = 5)
    {
        $r = RankingPromo::tableName();
        $a = Adv::tableName();
        $m = Media::tableName();
        $now = date('Y-m-d', time());

        $data = RankingPromo::find()
            ->select("date(min($r.startTime)) as startTime, date(max($r.endTime)) as endTime, $a.link, $m.uri")
            ->leftJoin($a, "$r.advSn = $a.sn")
            ->leftJoin($m, "$a.media_id = $m.id")
            ->where(["$r.isHidden" => 0])
            ->andWhere(["$a.del_status" => 0])
            ->andWhere("$a.link is not null")
            ->andWhere(['!=', "$a.link" , '/'])
            ->groupBy("$r.advSn")
            ->having(['<=', 'startTime', $now])
            ->orderBy([
                "$r.sortValue" => SORT_DESC,
                "endTime" => SORT_DESC,
            ]);
        $pg = Yii::$container->get('paginator')->paginate($data, $page, $size);
        $model = $pg->getItems();

        $tp = $pg->getPageCount();
        $code = ($page > $tp) ? 1 : 0;

        foreach ($model as $key => $value) {
            if ($now > $value['endTime'] && !is_null($value['endTime'])) {
                $model[$key]['status'] = false;                                     //过期活动
            } elseif ($now >= $value['startTime'] && ($now <= $value['endTime'] || is_null($value['endTime']))) {
                $model[$key]['status'] = true;                                      //进行中活动
            }
            $model[$key]['uri'] = UPLOAD_BASE_URI . $value['uri'];
        }
        $message = ($page > $tp) ? '数据错误' : '消息返回';
        if (Yii::$app->request->isAjax) {
            $html = $this->renderFile('@wap/views/news/_promo_list.php', ['model' => $model]);

            return ['header' => $pg->jsonSerialize(), 'html' => $html, 'code' => $code, 'message' => $message];
        }

        return $this->render('promo', [
            'model' => $model,
            'header' => $pg->jsonSerialize(),
        ]);
    }
}
