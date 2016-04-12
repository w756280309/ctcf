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


class CampaignController extends Controller
{
    public function actionTrack()
    {
        $hmsr = htmlspecialchars(\Yii::$app->request->post('hmsr'));
        if (strlen($hmsr) > 0) {
            \Yii::$app->session->set('campaign_source', $hmsr);
        }
    }
}