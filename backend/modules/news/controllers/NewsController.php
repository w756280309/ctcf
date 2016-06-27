<?php
namespace backend\modules\news\controllers;

use common\models\category\Category;
use common\models\category\ItemCategory;
use Yii;
use yii\data\Pagination;
use backend\controllers\BaseController;
use common\models\news\News;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class NewsController extends BaseController
{
    const NEWS_PAGE_SIZE = 10;

    public function actionIndex()
    {
        //所有文章分类
        $categories = Category::getTree(News::CATEGORY_TYPE_ARTICLE, 3);
        //状态
        $_statusList = News::getStatusList();
        $_where = [];
        $_andWhere = [];
        $_selectQueryParams = Yii::$app->request->get();
        foreach ($_selectQueryParams as $key => $val) {
            if ($key != 'title' && $key != 'status' && $key != 'home_status' && $key != 'category') {
                unset($_selectQueryParams[$key]);
                continue;
            }
            if ($val !== '') {
                if ($key == 'title') {
                    $_andWhere = ['like', $key, $val];
                } elseif ($key == 'category') {
                    if ($val) {
                        $ids = ItemCategory::getItems([$val], News::CATEGORY_TYPE_ARTICLE);
                        if ($ids) {
                            $_where['id'] = $ids;
                        }
                    }
                } else {
                    $_where[$key] = $val;
                }
            }
        }

        $query = News::find();
        if ($_where) {
            $query = $query->where($_where);
        }
        if ($_andWhere) {
            $query = $query->andWhere($_andWhere);
        }
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => static::NEWS_PAGE_SIZE]);
        $models = $query->orderBy(['sort' => SORT_DESC, 'news_time' => SORT_DESC, 'id' => SORT_DESC])->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('index', [
            'models' => $models,
            'pages' => $pages,
            'status' => $_statusList,
            'selectQueryParams' => $_selectQueryParams,
            'categories' => ArrayHelper::map($categories, 'id', 'name'),
        ]);
    }


    public function actionEdit($id = null)
    {
        //所有文章分类
        $categories = Category::getTree(News::CATEGORY_TYPE_ARTICLE, 3);
        //状态
        $_statusList = News::getStatusList();
        if ($id) {
            $model = $this->findModel($id);
            $model->news_time = date('Y-m-d H:i:s', $model->news_time);
            $item_category = $model->getItemCategories();
            $model->category = $item_category ? ArrayHelper::getColumn($item_category, 'category_id') : [];
        } else {
            $model = News::initNew();
            $model->creator_id = Yii::$app->user->getId();
        }

        if (isset($_FILES['News']['tmp_name']['pc_thumb']) && '' !== $_FILES['News']['tmp_name']['pc_thumb']) {
            $imageSize = getimagesize($_FILES['News']['tmp_name']['pc_thumb']);
            if ($imageSize[1] !== 156 && $imageSize[0] !== 271) {
                $model->addError("pc_thumb", "图片尺寸应限定为：宽271px，高156px");
            }
        }

        if (!$model->hasErrors("pc_thumb")) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $this->uploadPcThump($model);
                if (null === $model->pc_thumb) {
                    unset($model->pc_thumb);
                }
                if ($model->save(false)) {
                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('edit', ['model' => $model,
            'status' => $_statusList,
            'categories' => $categories,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = News::STATUS_DELETE;
        $model->save(false);

        return $this->redirect(['index']);
    }


    protected function findModel($id)
    {
        if (!empty($id) && ($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpload()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $php_path = Yii::$app->basePath . '/web/upload/news';
        $php_url = UPLOAD_BASE_URI . 'upload/news/';
        $image_ext = ['gif', 'jpg', 'jpeg', 'png', 'bmp'];
        $max_size = 1024 * 1024 * 10;
        $save_path = realpath($php_path) . '/';
        //检查目录
        if (false === is_dir($save_path)) {
            return ['error' => 1, 'message' => '上传目录不存在'];
        }
        if (false === file_exists($save_path)) {
            if (false === mkdir($save_path)) {
                return ['error' => 1, 'message' => '上传目录不存在'];
            }
        }
        //检查目录写权限
        if (false === is_writable($save_path)) {
            return ['error' => 1, 'message' => '上传目录没有写权限'];
        }
        //有上传文件时
        if (count($_FILES['imgFile']) > 0) {
            if (0 !== $_FILES['imgFile']['error']) {
                return ['error' => 1, 'message' => '上传失败'];
            }
            //原文件名
            $file_name = $_FILES['imgFile']['name'];
            //服务器上临时文件名
            $tmp_name = $_FILES['imgFile']['tmp_name'];
            //文件大小
            $file_size = $_FILES['imgFile']['size'];
            //检查文件名
            if (empty($file_name)) {
                return ['error' => 1, 'message' => '请选择文件'];
            }
            //检查是否已上传
            if (false === is_uploaded_file($tmp_name)) {
                return ['error' => 1, 'message' => '上传失败'];
            }
            //检查文件大小
            if ($file_size > $max_size) {
                return ['error' => 1, 'message' => '上传文件超过限制'];
            }
            //获得文件扩展名
            $temp_arr = explode(".", $file_name);
            $file_ext = strtolower(trim(array_pop($temp_arr)));
            //检查扩展名
            if (false === in_array($file_ext, $image_ext)) {
                return ['error' => 1, 'message' => "上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $image_ext) . "格式。"];
            }
            $new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
            $file_path = $save_path . $new_file_name;
            if (false === move_uploaded_file($tmp_name, $file_path)) {
                return ['error' => 1, 'message' => '上传文件失败'];
            }
            @chmod($file_path, 0644);
            $file_url = $php_url . $new_file_name;
            return ['error' => 0, 'url' => $file_url];
        } else {
            return ['error' => 1, 'message' => '请选择文件'];
        }
    }

    /**
     * 检查上传图片
     */
    private function uploadPcThump(News $obj)
    {
        $obj->pc_thumb = UploadedFile::getInstance($obj, 'pc_thumb');

        $path = Yii::getAlias('@backend').'/web/upload/news';
        if (!file_exists($path)) {
            mkdir($path);
        }

        if ($obj->pc_thumb) {
            $picPath = 'upload/news/pcthumb'.time().rand(100000, 999999).'.'.$obj->pc_thumb->extension;

            $obj->pc_thumb->saveAs($picPath);
            $obj->pc_thumb = $picPath;
        }
    }
}
