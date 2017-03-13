<?php

namespace backend\modules\fenxiao\controllers;

use backend\controllers\BaseController;
use common\models\adminuser\AdminLog;
use common\models\affiliation\Affiliator;
use common\models\affiliation\AffiliateCampaign;
use common\models\affiliation\UserAffiliation;
use common\models\fenxiao\Admin;
use common\models\fenxiao\FenxiaoForm;
use Yii;
use yii\data\Pagination;
use yii\db\Query;
use yii\web\UploadedFile;

class FenxiaoController extends BaseController
{
    /**
     * 添加分销商.
     */
    public function actionAdd()
    {
        $model = new FenxiaoForm();

        if ($model->load(Yii::$app->request->post())
            && $model->validate()
            && $this->checkUnique($model)) {
            $this->upload($model);

            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (empty($model->password)) {
                    throw new \Exception('密码不能为空。', 1);
                }

                $aff = new Affiliator([
                    'name' => $model->affName,
                    'isRecommend' => $model->isRecommend,
                ]);

                if (!empty($model->imageFile)) {
                    $aff->picPath = $model->imageFile;
                }

                if (!$aff->save(false)) {
                    throw new \Exception('数据库错误');
                }

                $affCam = new AffiliateCampaign([
                    'trackCode' => $model->affCode,
                    'affiliator_id' => $aff->id,
                ]);

                if (!$affCam->save(false)) {
                    throw new \Exception('数据库错误');
                }

                $admin = new Admin([
                    'loginName' => $model->loginName,
                    'passwordHash' => Yii::$app->security->generatePasswordHash($model->password),
                    'affiliator_id' => $aff->id,
                    'name' => $model->affName,
                ]);

                if (!$admin->save(false)) {
                    throw new \Exception('数据库错误');
                }

                $transaction->commit();

                $this->redirect('list');
            } catch (\Exception $ex) {
                $transaction->rollBack();

                if (1 === $ex->getCode()) {
                    $model->addError('password', $ex->getMessage());
                } else {
                    throw new \Exception($ex->getMessage());
                }
            }
        }

        return $this->render('edit', ['model' => $model]);
    }

    /**
     * 编辑分销商.
     */
    public function actionEdit($id)
    {
        if (empty($id)) {
            throw $this->ex404();
        }

        $admin = $this->findOr404(Admin::class, $id);
        $aff = $this->findOr404(Affiliator::class, $admin->affiliator_id);
        $affCam = $this->findOr404(AffiliateCampaign::class, ['affiliator_id' => $aff->id]);

        $model = new FenxiaoForm([
            'loginName' => $admin->loginName,
            'affName' => $admin->name,
            'affCode' => $affCam->trackCode,
            'isRecommend' => $aff->isRecommend,
        ]);

        $old = clone $model;

        if ($model->load(Yii::$app->request->post())
            && $model->validate()
            && $this->checkUnique($model, $old)) {
            $this->upload($model);

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $admin->loginName = $model->loginName;
                $admin->name = $model->affName;

                if (!empty($model->password)) {
                    $admin->passwordHash = Yii::$app->security->generatePasswordHash($model->password);
                }

                if (!$admin->save(false)) {
                    throw new \Exception('数据库错误');
                }

                $aff->name = $model->affName;
                $aff->isRecommend = $model->isRecommend;
                if (!empty($model->imageFile)) {
                    $aff->picPath = $model->imageFile;
                }

                if (!$aff->save(false)) {
                    throw new \Exception('数据库错误');
                }

                $affCam->trackCode = $model->affCode;

                if (!$affCam->save(false)) {
                    throw new \Exception('数据库错误');
                }

                $transaction->commit();
            } catch (\Exception $ex) {
                $transaction->rollBack();
                throw new \Exception($ex->getMessage());
            }

            $this->redirect('list');
        }

        return $this->render('edit', ['model' => $model, 'admin' => $admin, 'aff' => $aff]);
    }

    /**
     * 分销商列表.
     */
    public function actionList()
    {
        $query = Admin::find();

        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '15']);
        $model = $query->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->all();

        return $this->render('list', ['model' => $model, 'pages' => $pages]);
    }

    /**
     * 判断关键字段的唯一性,包括分销商名称,登录名,以及渠道码
     */
    private function checkUnique(FenxiaoForm $fx, FenxiaoForm $old = null)
    {
        if (null === $old || (null !== $old && $fx->loginName !== $old->loginName)) {
            $count = Admin::find()->where(['loginName' => $fx->loginName])->count();

            if ($count > 0) {
                $fx->addError('loginName', '登录名称已被占用,请重试');

                return false;
            }
        }

        if (null === $old || (null !== $old && $fx->affName !== $old->affName)) {
            $count = Affiliator::find()->where(['name' => $fx->affName])->count();

            if ($count > 0) {
                $fx->addError('affName', '分销商名称已被占用,请重试');

                return false;
            }
        }

        if (null === $old || (null !== $old && $fx->affCode !== $old->affCode)) {
            $count = AffiliateCampaign::find()->where(['trackCode' => $fx->affCode])->count();

            if ($count > 0) {
                $fx->addError('affCode', '分销商渠道码已被占用,请重试');

                return false;
            }
        }

        return true;
    }

    /**
     * 检查上传图片
     */
    private function upload(FenxiaoForm $obj)
    {
        $obj->imageFile = UploadedFile::getInstance($obj, 'imageFile');

        $path = Yii::getAlias('@backend').'/web/upload/fenxiao';
        if (!file_exists($path)) {
            mkdir($path);
        }

        if ($obj->imageFile) {
            $picPath = 'upload/fenxiao/fx'.time().'.'.$obj->imageFile->extension;

            $obj->imageFile->saveAs($picPath);
            $obj->imageFile = $picPath;
        }
    }

    /**
     * 获取分销商信息.
     */
    public function actionGetAffInfo($uid)
    {
        $userAff = UserAffiliation::findOne(['user_id' => $uid]);
        $affCamName = AffiliateCampaign::tableName();
        $affName = Affiliator::tableName();

        $affiliators = (new Query())
            ->select(["$affName.id", "name", "trackCode"])
            ->from($affName)
            ->innerJoin($affCamName, "$affName.id = $affCamName.affiliator_id")
            ->createCommand()
            ->queryAll();

        $this->layout = false;
        return $this->render('affiliator_list', ['affiliators' => $affiliators, 'userAff' => $userAff, 'uid' => $uid]);
    }

    /**
     * 修改投资用户分销商.
     * 1. 原来没有分销商的可以添加新的分销商;
     * 2. 原来有分销商的可以修改分销商;
     * 3. 原来有分销商的也可以删除分销商;
     */
    public function actionEditForUser()
    {
        $data = Yii::$app->request->post();
        $code = false;
        $dataForLog = null;
        $isNeedLog = true;

        if (empty($data['uid'])) {
            throw $this->ex404();
        }

        $userAff = UserAffiliation::findOne(['user_id' => $data['uid']]);

        if (empty($data['aff_id'])) {
            if ($userAff) {   //删除分销商
                $code = $userAff->delete();
            } else {    //没有分销商,也没有选择分销商时,成功返回,且不记日志
                $code = true;
                $isNeedLog = false;
            }
        } else {
            $affCam = $this->findOr404(AffiliateCampaign::class, ['affiliator_id' => $data['aff_id']]);

            if (!$userAff) {    //新增分销商
                $userAff = new UserAffiliation([
                    'user_id' => $data['uid'],
                    'trackCode' => $affCam->trackCode,
                    'affiliator_id' => $data['aff_id'],
                ]);

                $code = $userAff->save();
            } elseif ($userAff->affiliator_id !== (int) $data['aff_id']) {    //修改分销商
                $userAff->trackCode = $affCam->trackCode;
                $userAff->affiliator_id = $data['aff_id'];

                $dataForLog =  clone $userAff;
                $code = $userAff->save();
            } else {     //如果没有修改,则忽略,直接返回成功
                $code = true;
                $isNeedLog = false;
            }
        }

        if ($isNeedLog && $code) {
            $dataForLog = $dataForLog ? $dataForLog : $userAff;
            AdminLog::initNew($dataForLog)->save();
        }

        return [
            'code' => $code ? 0 : 1,
            'message' => $code ? '操作成功' : '操作失败',
        ];
    }
}