<?php

namespace app\controllers;


use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii;

class DevController extends Controller
{
    public function actionView($viewPath)
    {
        $dev = Yii::$app->params['enable_dev_helpers'];
        if (true !== $dev) {
            throw new NotFoundHttpException('只有在 ENV 环境下(enable_dev_helpers 配置为true)允许访问该页面。');
        }
        $basePath = Yii::getAlias('@wap') . '/';
        $viewFile = $basePath . $viewPath;
        if (file_exists($viewFile) && is_file($viewFile)) {
            $ext = pathinfo($viewFile, PATHINFO_EXTENSION);
            if ('php' !== $ext) {
                throw new NotFoundHttpException('模板文件只能是php文件（扩展名位.php）');
            }
            return $this->renderFile($viewFile);
        } else {
            throw new NotFoundHttpException('指定模板（' . $viewFile . '）没有找到。模板示例：文件位置，wap/views/ebaoquan/index.php;$viewPath = "views/ebaoquan/index.php";访问地址 /dev/view?viewPath=views/ebaoquan/index.php ');
        }
    }
}