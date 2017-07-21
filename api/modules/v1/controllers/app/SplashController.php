<?php

namespace api\modules\v1\controllers\app;

use api\modules\v1\controllers\Controller;
use Yii;

class SplashController extends Controller
{
    /**
     * 返回App闪屏图相关信息
     *
     * @return array
     */
    public function actionShow()
    {
        //获得图片的路径
        $baseDir = Yii::$app->params['m_assets_base_uri'];

        if ($baseDir === '/') {
            $baseDir = Yii::$app->params['clientOption']['host']['wap'];
        }
        $data = [
            'result' => 'success',
            'msg' => '成功',
            'content' => [
                'adv_id' => 170721,
                'img_640x960' => $baseDir . 'images/shanping/170721/img_640x960.png',
                'img_640x1136' => $baseDir . 'images/shanping/170721/img_640x1136.png',
                'img_750x1334' => $baseDir . 'images/shanping/170721/img_750x1334.png',
                'img_1242x2208' => $baseDir . 'images/shanping/170721/img_1242x2208.png',
                'img_1080x1920' => $baseDir . 'images/shanping/170721/img_1080x1920.png', //安卓端
                'title' => '温都金服启动页',
            ],
        ];

        $message = '成功';

        return [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ];
    }
}
