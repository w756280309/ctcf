<?php
namespace app\modules\credit\controllers;

use yii\web\Controller;

class NoteController extends Controller
{
    /**
     * 转让详情页.
     */
    public function actionDetail()
    {
        return $this->render('detail');
    }
}
