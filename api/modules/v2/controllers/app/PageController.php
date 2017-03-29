<?php

namespace api\modules\v2\controllers\app;


use api\modules\v2\controllers\BaseController;
use common\models\adv\Adv;
use common\models\news\News;
use common\models\product\Issuer;
use common\models\product\LoanFinder;
use common\models\product\OnlineProduct;
use common\models\product\RateSteps;
use common\models\stats\Perf;
use common\utils\StringUtils;
use yii\helpers\Html;
use yii\web\Response;

class PageController extends BaseController
{
    /**
     * 首页接口
     *
     * 请求头
     * ```
     * Accept:application/vnd.uft.mob.legacy+json
     * ```
     * 请求参数
     * @param string $token 用户唯一标示
     * @param string $versionCode 当前版本号
     */
    public function actionHome()
    {
        $token = \Yii::$app->request->get('token');
        $appHost = rtrim(\Yii::$app->params['clientOption']['host']['app'], '/');
        $loanStatus = [OnlineProduct::STATUS_PRE, OnlineProduct::STATUS_NOW];

        //头部数据
        $headerScope = [];

        //获取banner图
        $advs = Adv::find()
            ->where([
                'status' => Adv::STATUS_SHOW,
                'del_status' => Adv::DEL_STATUS_SHOW,
                'showOnPc' => 0,
                'isDisabledInApp' => 0,
                'type' => Adv::TYPE_LUNBO
            ])
            ->orderBy([
                'show_order' => SORT_ASC,
                'id' => SORT_DESC,
            ])
            ->limit(5)
            ->all();
        $headerScope['bannerItem'] = [];
        foreach ($advs as $adv) {
            if (!is_null($adv->media)) {
                $headerScope['bannerItem'][] = [
                    'url' => UPLOAD_BASE_URI . $adv->media->uri,
                    'jumpUrl' => $appHost . $adv->getLinkUrl(),
                ];
            }
        }

        //平台统计数据
        $cache = \Yii::$app->cache;
        $key = 'index_stats';
        if (!$cache->get($key)) {
            $statsData = Perf::getStatsForIndex();
            $cache->set($key, $statsData, 600);   //缓存十分钟
        } else {
            $statsData = $cache->get($key);
        }
        $headerScope['dealTotal'] = '平台累计交易额：' . number_format(bcdiv($statsData['totalTradeAmount'], 100000000, 2), 2) . '亿元';
        $headerScope['historyExchangeRate'] = '历史兑付率100%';
        $headerScope['totalExchange'] = '累计兑付' . number_format(bcdiv($statsData['totalRefundAmount'], 10000, 2), 2) . '万元';
        $headerScope['totalProfit'] = '带来' . number_format(bcdiv($statsData['totalRefundInterest'], 10000, 2), 2) . '万元收益';

        //首页功能入口区域
        $headerScope['functionItem'] = [
            [
                'url' => FE_BASE_URI . "wap/index/images/icon_01.png",
                'desc' => '平台介绍',
                'jumpUrl' => $appHost . '/site/h5?wx_share_key=h5',
            ],
            [
                'url' => FE_BASE_URI . "wap/index/images/icon_02.png",
                'desc' => '邀请好友',
                'jumpUrl' => $appHost . '/user/invite',
            ],
            [
                'url' => FE_BASE_URI . "wap/index/images/icon_03.png",
                'desc' => '积分商城',
                'jumpUrl' => $appHost . '/mall/portal/guest',
            ],
            [
                'url' => FE_BASE_URI . "wap/index/images/icon_04.png",
                'desc' => '官网公告',
                'jumpUrl' => $appHost . '/news',
            ],
        ];


        //新手专项
        $newUserScope = [];
        $xs = LoanFinder::queryPublicLoans()
            ->andWhere([
                'is_xs' => true,
                'status' => $loanStatus,
            ])
            ->orderBy([
                'xs_status' => SORT_DESC,
                'recommendTime' => SORT_DESC,
                'sort' => SORT_ASC,
                'finish_rate' => SORT_DESC,
                'id' => SORT_DESC,
            ])
            ->one();
        if (!is_null($xs)) {
            $baseRate = StringUtils::amountFormat2(OnlineProduct::calcBaseRate($xs->yield_rate, $xs->jiaxi));
            $topRate = RateSteps::getTopRate(RateSteps::parse($xs->rateSteps));
            if (!is_null($xs->jiaxi)) {
                $topRate = bcsub($topRate, $xs->jiaxi, 2);
            }
            $topRate = StringUtils::amountFormat2($topRate);
            $raiseRate = $xs->jiaxi;
            $ex = $xs->getDuration();
            $legalTags = [];
            if (!is_null($xs->tags)) {
                $tags = explode('，', $xs->tags);
                foreach ($tags as $key => $tag) {
                    if ($key < 2 && !empty($tag)) {
                        $legalTags[] = Html::encode($tag);
                    }
                }
            }

            $newUserScope = [
                'dealTitle' => $xs->title,
                'minYearRate' => $baseRate,
                'maxYearRate' => $topRate,
                'raiseRate' => $raiseRate,
                'leftMoney' => StringUtils::amountFormat2($xs->start_money) . '元起投',
                'limitTime' => $ex['value'] . $ex['unit'] . "期限",
                'limitInvest' => '限购一万',
                'smartCornerUrl' => '',
                'labelItem' => $legalTags,
            ];
        }

        //精选项目
        $handpickScope = [];
        $issuers = Issuer::find()
            ->where(['isShow' => true])
            ->andWhere(['!=', 'big_pic', 'null'])
            ->andWhere(['!=', 'mid_pic', 'null'])
            ->andWhere(['!=', 'small_pic', 'null'])
            ->orderBy(['sort' => SORT_ASC])
            ->limit(3)
            ->all();

        $issuerCount = count($issuers);
        foreach ($issuers as $issuer) {
            $jumpUrl = $issuer->path;
            $url = '';
            $medias = $issuer->getMedias();
            if ($issuerCount === 1) {
                $url = isset($medias['big']) ? $medias['big']->uri : '';
            } elseif ($issuerCount === 2) {
                $url = isset($medias['mid']) ? $medias['mid']->uri : '';
            } elseif ($issuerCount === 3) {
                $url = isset($medias['small']) ? $medias['small']->uri : '';
            }
            $handpickScope['handpickItem'][] = [
                'url' => UPLOAD_BASE_URI . $url,
                'jumpUrl' => $jumpUrl,
            ];
        }

        //理财专区
        $financialScope = [];
        $loans = OnlineProduct::find()
            ->where([
                'isPrivate' => 0,
                'del_status' => OnlineProduct::STATUS_USE,
                'online_status' => OnlineProduct::STATUS_ONLINE,
                'is_xs' => false,
                'status' => $loanStatus,
            ])
            ->orderBy([
                'recommendTime' => SORT_DESC,
                'sort' => SORT_ASC,
                'finish_rate' => SORT_DESC,
                'id' => SORT_DESC,
            ])
            ->limit(2)
            ->all();
        foreach ($loans as $loan) {
            $baseRate = StringUtils::amountFormat2(OnlineProduct::calcBaseRate($loan->yield_rate, $loan->jiaxi));
            $topRate = RateSteps::getTopRate(RateSteps::parse($loan->rateSteps));
            if (!is_null($loan->jiaxi)) {
                $topRate = bcsub($topRate, $loan->jiaxi, 2);
            }
            $topRate = StringUtils::amountFormat2($topRate);
            $raiseRate = $loan->jiaxi;
            $ex = $loan->getDuration();
            $legalTags = [];
            if (!is_null($loan->tags)) {
                $tags = explode('，', $loan->tags);
                foreach ($tags as $key => $tag) {
                    if ($key < 2 && !empty($tag)) {
                        $legalTags[] = Html::encode($tag);
                    }
                }
            }

            $financialScope[] = [
                'smartCornerUrl' => '',
                'dealTitle' => $loan->title,
                'minYearRate' => $baseRate,
                'maxYearRate' => $topRate,
                'raiseRate' => $raiseRate,
                'leftMoney' => StringUtils::amountFormat2($loan->start_money) . '元起投',
                'limitTime' => $ex['value'] . $ex['unit'] . "期限",
                'dealProgress' => $loan->getProgressForDisplay(),
                'repaymentType' => \Yii::$app->params['refund_method'][$loan->refund_method],
                'jumpUrl' => $appHost . '/deal/deal/detail?sn=' . $loan->sn,
                'labelItem' => $legalTags,
            ];
        }

        //公告专区
        $noticeScope = [];
        $newsData = News::find()
            ->where([
                'status' => News::STATUS_PUBLISH,
                'allowShowInList' => true,
            ])
            ->orderBy(['news_time' => SORT_DESC])
            ->limit(3)
            ->all();
        $noticeScope['moreNoticeJumpUrl'] = $appHost . '/news';
        foreach ($newsData as $news) {
            $noticeScope['noticeItem'][] = [
                'noticeTitle' => $news->title,
                'jumpUrl' => $appHost . '/news/detail?id=' . $news->id . '&v=' . time(),
            ];
        }

        //优势介绍
        $introduceScope = [];
        $introduceScope['businessImgUrl'] = ASSETS_BASE_URI . '/images/icp.png';
        $introduceScope['businessJumpUrl'] = $appHost . '/news/detail?type=info&id=383';
        $introduceScope['servicePhone'] = \Yii::$app->params['contact_tel'];
        $introduceScope['address'] = '温州市鹿城区飞霞南路657号保丰大楼四层';
        $introduceScope['productPlatform'] = '温州报业传媒旗下理财平台';


        \Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'headerScope' => $headerScope,
            'newUserScope' => $newUserScope,
            'handpickScope' => $handpickScope,
            'financialScope' => $financialScope,
            'noticeScope' => $noticeScope,
            'introduceScope' => $introduceScope,
        ];

    }

}