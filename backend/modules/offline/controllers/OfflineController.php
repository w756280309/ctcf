<?php

namespace backend\modules\offline\controllers;

use backend\controllers\BaseController;
use common\models\adminuser\AdminLog;
use common\models\offline\OfflineOrder;
use common\models\affiliation\Affiliator;
use common\models\offline\OfflineLoan;
use common\models\offline\ImportForm;
use common\models\offline\OfflineUser;
use Yii;
use yii\data\Pagination;
use PHPExcel_Reader_Excel2007;
use PHPExcel_Reader_Excel5;
use common\filters\MyReadFilter;

class OfflineController extends BaseController
{
    /**
     * 录入线下数据页面
     *
     * @return string
     */
    public function actionAdd()
    {
        $model = new ImportForm();
        $flag = Yii::$app->request->post('flag');
        if (!empty($flag)) {
            $filename = $_FILES['ImportForm']['name']['excel'];
            if (!isset($filename) || empty($filename)) {
                $model->addError('excel', '未选择文件');
            }
            if (substr($filename, -4, 4) !== '.xls' && substr($filename, -5, 5) !== '.xlsx') {
                $model->addError('excel', '上传的文件为非.xlsx或.xls文件');
            }
            $filepath = $_FILES['ImportForm']['tmp_name']['excel'];
            try {
                $arr = $this->readExcelToArray($filepath);
            } catch (\Exception $ex) {
                $model->addError('excel', $ex->getMessage());
            }

            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            if (!$model->hasErrors()) {
                try {
                    foreach ($arr as $key => $order) {
                        //省略第一行
                        if ($key < 1) {
                            continue;
                        }
                        //判断某一行皆为空时,跳过该行
                        if (empty(array_filter($order))) {
                            continue;
                        }

                        //初始化model，寻找行号，使用batchInsert插入
                        $neworder = $this->initModel($order);
                        if ($neworder->validate()) {
                            $rows[] = $neworder->attributes;
                        } else {
                            $error_index = $key + 1;
                            if ($neworder->hasErrors('affiliator_id')) {
                                throw new \Exception('文件内容有错,行号' . $error_index . ',请在后台添加分销商' . $order[0]);
                            }
                            throw new \Exception('文件内容有错,行号' . $error_index);
                        }
                    }
                    $db->createCommand()->batchInsert(OfflineOrder::tableName(), $neworder->attributes(), $rows)->execute();
                    $transaction->commit();
                    return $this->redirect('list');
                } catch (\Exception $ex) {
                    $model->addError('excel', $ex->getMessage());
                    $transaction->rollBack();
                }
                //删除临时文件
                @unlink($filepath);
            }
        }
        return $this->render('add', ['model' => $model]);
    }

    /**
     * 线下数据页面
     */
    public function actionList()
    {
        $request = Yii::$app->request->get();
        $ol = OfflineLoan::tableName();
        $o = OfflineOrder::tableName();
        $order = OfflineOrder::find()->innerJoinWith('loan')->where(["$o.isDeleted" => false]);
        if (isset($request['bid']) && $request['bid'] > 0) {
            $order->andWhere(["$o.affiliator_id" => $request['bid']]);
        }
        if (isset($request['title']) && !empty($request['title'])) {
            $order->andFilterWhere(['like', "$ol.title", $request['title']]);
        }
        if (isset($request['realName']) && !empty($request['realName'])) {
            $order->andFilterWhere(['like', "$o.realName", $request['realName']]);
        }
        if (isset($request['mobile']) && !empty($request['mobile'])) {
            $order->andFilterWhere(['like', "$o.mobile", $request['mobile']]);
        }

        $branches = Affiliator::find()->all();
        $pages = new Pagination(['totalCount' => $order->count(), 'pageSize' => 10]);
        $orders = $order->offset($pages->offset)->limit($pages->limit)->orderBy(["$o.id" => SORT_DESC])->all();
        $totalmoney = $order->sum('money');

        return $this->render('list', ['branches' => $branches, 'orders' => $orders, 'totalmoney' => $totalmoney, 'pages' => $pages]);
    }

    /**
     * excel的数据读取到二维数组中
     *
     * @param $filePath
     * @return array
     * @throws \Exception
     */
    private function readExcelToArray($filePath)
    {
        $filterSubset = new MyReadFilter();
        $PHPReader = new \PHPExcel_Reader_Excel2007(); // Reader很关键，用来读excel文件
        if (!$PHPReader->canRead($filePath)) { // 这里是用Reader尝试去读文件，07不行用05，05不行就报错。
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filePath)) {
                throw new \Exception('读取文件错误');
            }
        }

        $PHPReader->setReadFilter($filterSubset);
        $PHPExcel = $PHPReader->load($filePath); // Reader读出来后，加载给Excel实例
        $currentSheet = $PHPExcel->getSheet(0);
        $row = $currentSheet->getHighestRow();
        $max_read_line = MyReadFilter::MAX_READ_LINE;
        if ($row > $max_read_line) {
            throw new \Exception('该excel文件行数超出' . $max_read_line . '行');
        }
        //将J,K行日期转为php的'Y-m-d'
        $j = 'J' . $row;
        $k = 'K' . $row;
        //excel在‘2016/7/12’识别该列时，日期格式的保持不变，非日期格式的识别为'07-12-06'，或者识别成float(42258),使用下面的是都可以转换成'2016-07-12'
        $currentSheet->getStyle("J1:$j")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        $currentSheet->getStyle("K1:$k")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        $content = $currentSheet->toArray('', true, true);
        return $content;
    }

    /**
     * 根据order数组，初始化一个新的OfflineOrder model
     *
     * @param $order
     * @return OfflineOrder
     */
    private function initModel($order)
    {
        //过滤掉所有的空格
        $order = array_map(function ($val) {
            return Yii::$app->functions->removeWhitespace($val);
        }, $order);

        $model = new OfflineOrder();
        $affiliator = Affiliator::find()->where(['name' => $order[0]])->one();
        $loan = OfflineLoan::find()->where(['title' => $order[1]])->one();
        $user = OfflineUser::find()->where(['idCard' => $order[4]])->one();
        $affiliator_id = null;
        if (null !== $affiliator) {
            $affiliator_id = $affiliator->id;
        }
        if (null !== $loan) {
            $loan_id = $loan->id;
        } else {
            $newLoan = new OfflineLoan();
            $newLoan->title = $order[1];
            $newLoan->expires = (int) $order[2];
            $newLoan->unit = str_replace($newLoan->expires, '', $order[2]);
            $newLoan->save();
            $loan_id = $newLoan->id;
        }

        //todo -更新积分
        if (null !== $user) {
            $user_id = $user->id;
            //手机号应是始终为最后导入的那个
            if ($user->mobile !== $order[5]) {
                $user->mobile = $order[5];
                $user->save();
            }
        } else {
            $newUser = new OfflineUser();
            $newUser->realName = $order[3];
            $newUser->idCard = $order[4];
            $newUser->mobile = $order[5];
            $newUser->save();
            $user_id = $newUser->id;
        }

        $model->affiliator_id = $affiliator_id;
        $model->loan_id = $loan_id;
        $model->user_id = $user_id;
        $model->realName = $order[3];
        $model->idCard = $order[4];
        $model->mobile = $order[5];
        $model->accBankName = $order[6];
        $model->bankCardNo = $order[7];
        $model->money = $order[8];
        $model->orderDate = $order[9];
        $model->valueDate = $order[10];
        $model->created_at = time();
        $model->isDeleted = false;
        return $model;
    }

    /**
     * 根据id删除对应的offline_order的一条记录（修改状态）
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->post('id');

        if ($id) {
            $model = OfflineOrder::findOne($id);
            $model->isDeleted = true;
            //修改标的修改记录
            try {
                $log = AdminLog::initNew($model);
                $log->save();
            } catch (\Exception $e) {
                return [
                    'result' => 0,
                    'message' => '线下数据删除操作日志记录失败',
                ];
            }
            if ($model->save()) {
                //todo -扣除积分和财富值(应考虑积分流水扣除)
                return ['code' => 1, 'message' => '删除成功'];
            }
        }

        return ['code' => 0, 'message' => '删除失败'];
    }
}
