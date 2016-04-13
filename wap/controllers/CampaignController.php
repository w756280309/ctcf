<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2016/4/12
 * Time: 10:10
 */

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Cookie;


class CampaignController extends Controller
{
    public function actionTrack()
    {
        $hmsr = htmlspecialchars(\Yii::$app->request->post('hmsr'));
        if (strlen($hmsr) > 0) {
            Yii::$app->response->cookies->add(new Cookie([
                'name'=>'campaign_source',
                'value'=>$hmsr,
                'expire'=>time()+3600*24*3
            ]));
        }
    }
}