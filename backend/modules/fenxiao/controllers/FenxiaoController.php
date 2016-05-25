<?php

namespace backend\modules\fenxiao\controllers;

use backend\controllers\BaseController;
use common\models\affiliation\Affiliator;
use common\models\affiliation\AffiliateCampaign;
use common\models\fenxiao\Admin;
use common\models\fenxiao\FenxiaoForm;
use Yii;
use yii\data\Pagination;

class FenxiaoController extends BaseController
{
    /**
     * 添加分销商.
     */
    public function actionAdd()
    {
        $model = new FenxiaoForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $this->checkUnique($model)) {
            try {
                if (empty($model->password)) {
                    throw new \Exception('密码不能为空。', 1);
                }

                $transaction = Yii::$app->db->beginTransaction();

                $aff = new Affiliator([
                    'name' => $model->affName,
                ]);

                if (!$aff->save(false)) {
                    $transaction->rollBack();
                    throw new \Exception('数据库错误');
                }

                $affCam = new AffiliateCampaign([
                    'trackCode' => $model->affCode,
                    'affiliator_id' => $aff->id,
                ]);

                if (!$affCam->save(false)) {
                    $transaction->rollBack();
                    throw new \Exception('数据库错误');
                }

                $admin = new Admin([
                    'loginName' => $model->loginName,
                    'passwordHash' => Yii::$app->security->generatePasswordHash($model->password),
                    'affiliator_id' => $aff->id,
                    'name' => $model->affName,
                ]);

                if (!$admin->save(false)) {
                    $transaction->rollBack();
                    throw new \Exception('数据库错误');
                }

                $transaction->commit();

                $this->redirect('list');
            } catch (\Exception $ex) {
                if (1 === $ex->getCode()) {
                    $model->addError('password', $ex->getMessage());
                } else {
                    $model->addError('loginName', $ex->getMessage());
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
            $this->ex404();
        }

        $admin = $this->findOr404(Admin::class, $id);
        $aff = $this->findOr404(Affiliator::class, $admin->affiliator_id);
        $affCam = $this->findOr404(AffiliateCampaign::class, ['affiliator_id' => $aff->id]);

        $model = new FenxiaoForm([
            'loginName' => $admin->loginName,
            'affName' => $admin->name,
            'affCode' => $affCam->trackCode,
        ]);

        $old = clone $model;

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $this->checkUnique($model, $old)) {
            try {
                $transaction = Yii::$app->db->beginTransaction();

                $admin->loginName = $model->loginName;
                $admin->name = $model->affName;

                if (!empty($model->password)) {
                    $admin->passwordHash = Yii::$app->security->generatePasswordHash($model->password);
                }

                if (!$admin->save(false)) {
                    $transaction->rollBack();
                    throw new \Exception('数据库错误');
                }

                $aff->name = $model->affName;

                if (!$aff->save(false)) {
                    $transaction->rollBack();
                    throw new \Exception('数据库错误');
                }

                $affCam->trackCode = $model->affCode;

                if (!$affCam->save(false)) {
                    $transaction->rollBack();
                    throw new \Exception('数据库错误');
                }

                $transaction->commit();

                $this->redirect('list');
            } catch (\Exception $ex) {
                $model->addError('loginName', $ex->getMessage());
            }
        }

        return $this->render('edit', ['model' => $model, 'admin' => $admin]);
    }

    /**
     * 分销商列表.
     */
    public function actionList()
    {
        $query = Admin::find()->orderBy("id desc");

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

            if (0 !== $count) {
                $fx->addError('loginName', '登录名称已被占用,请重试');

                return false;
            }
        }

        if (null === $old || (null !== $old && $fx->affName !== $old->affName)) {
            $count = Affiliator::find()->where(['name' => $fx->affName])->count();

            if (0 !== $count) {
                $fx->addError('affName', '分销商名称已被占用,请重试');

                return false;
            }
        }

        if (null === $old || (null !== $old && $fx->affCode !== $old->affCode)) {
            $count = AffiliateCampaign::find()->where(['trackCode' => $fx->affCode])->count();

            if (0 !== $count) {
                $fx->addError('affCode', '分销商渠道码已被占用,请重试');

                return false;
            }
        }

        return true;
    }
}