<?php

namespace api\modules\v1\controllers\app;

use api\modules\v1\controllers\Controller;
use common\models\adv\Splash;
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
        $now = date('Y-m-d H:i:s', time());
        $splashModel = new Splash();
        $baseDir = Yii::$app->params['upload_base_uri'];
        $splash = Splash::find()
            ->where(['isPublished' => Splash::PUBLISHED])
            ->andWhere(['auto_publish' => Splash::AUTO_PUBLISH_ON])
            ->andWhere(['<=', 'publishTime', $now])
            ->orderBy(['publishTime' => SORT_DESC, 'id' => SORT_DESC])
            ->one();
        $data = [
            'result' => 'success',
            'msg' => '成功',
            'content' => [
                'adv_id' => (int)$splash['sn'],
                'img_640x960' => $baseDir . $splashModel->getMediaUri($splash['img640x960']),
                'img_640x1136' => $baseDir . $splashModel->getMediaUri($splash['img640x1136']),
                'img_750x1334' => $baseDir . $splashModel->getMediaUri($splash['img750x1334']),
                'img_1242x2208' => $baseDir . $splashModel->getMediaUri($splash['img1242x2208']),
                'img_1080x1920' => $baseDir . $splashModel->getMediaUri($splash['img1080x1920']), //安卓端
                'title' => $splash['title'],
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
