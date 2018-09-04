<?php

namespace api\modules\v1\controllers\app;

use api\modules\v1\controllers\Controller;
use common\models\adv\Splash;
use Yii;
use yii\db\Query;

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

    /**
     * 返回App首页底部Tab图片链接地址
     * 后台文件上传指定文件名图片（文件名首次确定后不可变），客户端根据文件名展示图片地址
     * 目的：解决切换tab图片，app需要发版问题
     * @return array
     */
    public function actionTabImage()
    {
        $uri = isset(Yii::$app->params['upload_base_uri']) ? Yii::$app->params['upload_base_uri']: '';
        $tabImages = (new Query())
            ->select('title,link')
            ->from('admin_upload')
            ->where("title like 'app%'")
            ->indexBy('title')
            ->all();
        if (count($tabImages) < 6) {
            $tabImages = [];
        }
        if (!empty($tabImages)) {
            $tabImages['uri'] = $uri;
        }
        $data = [
            'result' => 'success',
            'msg' => '成功',
            'content' => $tabImages,
        ];

        $message = '成功';

        return [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ];
    }
}
