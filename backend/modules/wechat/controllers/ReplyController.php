<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 17-12-6
 * Time: 下午5:30
 */
namespace backend\modules\wechat\controllers;

use backend\controllers\BaseController;
use common\models\adminuser\Role;
use common\models\bank\BankCardUpdate;
use common\models\user\QpayBinding;
use common\models\wechat\Reply;
use Yii;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class ReplyController extends BaseController
{
    public function actionIndex()
    {
        $data = Reply::find();
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '10']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->orderBy('id asc')->asArray()->all();
        $types = [
            'text' => '文本',
            'image' => '图片',
        ];
        $status = [
            '0' => '启用',
            '1' => '禁用',
        ];
        return $this->render('index', [
            'model' => $model,
            'pages' => $pages,
            'status' => $status,
            'types' => $types,
            ]);
    }

    public function actionEdit($id = false)
    {
        $model = $id ? Reply::findOne($id) : new Reply();
        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {
            if ($model->type == 'image' && isset($_FILES['Reply'])) {
                $path = Yii::getAlias('@backend') . '/web/upload/wechat/';
                if (!file_exists($path)) {
                    mkdir($path);
                }
                $filename = time() . $_FILES['Reply']['name']['media'];
                if (move_uploaded_file($_FILES['Reply']['tmp_name']['media'], $path . $filename)) {
                    $app = Yii::$container->get('weixin_wdjf');
                    // 永久素材->图片
                    $material = $app->material;
                    $result = $material->uploadImage($path . $filename);
                    if (isset($result['media_id'])) {
                        $model->content = $result['media_id'];
                    } else {
                        $model->addErrors(['content' => '上传文件失败或文件已上传，请重试']);
                    }
                }
            }
            if ($model->validate()) {
                $model->save();
                return $this->redirect('index');
            }
        }
        $types = [
            'text' => '文本',
            'image' => '图片',
        ];
        return $this->render('edit', ['model' => $model, 'types' => $types]);
    }
}