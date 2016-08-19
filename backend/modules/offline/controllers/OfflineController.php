<?php

namespace backend\modules\offline\controllers;

use backend\controllers\BaseController;
use common\models\offline\OfflineOrder;
use common\models\salebranch\SaleBranch;
use common\models\offline\OfflineLoan;
use common\models\offline\ImportForm;
use Yii;
use yii\data\Pagination;
use PHPExcel_Reader_Excel2007;
use PHPExcel_Reader_Excel5;

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
                $model->addError('excel', '读取文件错误');
            }
            $transaction = Yii::$app->db->beginTransaction();
            if (!$model->hasErrors()) {
                try {
                    foreach ($arr as $key => $order) {
                        if ($key < 3) {
                            continue;
                        }
                        $neworder = $this->initModel($order);
                        if ($neworder->validate()) {
                            $neworder->save();
                        } else {
                            throw new \Exception($key + 1);
                        }
                    }
                    $transaction->commit();
                    @unlink($filepath);
                    return $this->redirect('list');
                } catch (\Exception $ex) {
                    $model->addError('excel', '文件内容有错,行号' . $ex->getMessage());
                    $transaction->rollBack();
                }
            }
        }
        return $this->render('add', ['model' => $model]);
    }

    /**
     * 线下数据页面
     *
     * @param null $bid
     * @return string
     */
    public function actionList($bid = null)
    {
        $order = OfflineOrder::find();
        if (null !== $bid && is_numeric($bid)) {
            $order->where(['branch_id' => $bid]);
        }

        $branches = SaleBranch::find()->all();
        $pages = new Pagination(['totalCount' => $order->count(), 'pageSize' => 10]);
        $orders = $order->offset($pages->offset)->limit($pages->limit)->orderBy(['id' => SORT_DESC])->all();
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
        $PHPReader = new \PHPExcel_Reader_Excel2007(); // Reader很关键，用来读excel文件
        if (!$PHPReader->canRead($filePath)) { // 这里是用Reader尝试去读文件，07不行用05，05不行就报错。
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filePath)) {
                throw new \Exception('不支持该版本的excel');
            }
        }
        $PHPExcel = $PHPReader->load($filePath); // Reader读出来后，加载给Excel实例
        $currentSheet = $PHPExcel->getSheet(0);
        $row = $currentSheet->getHighestRow();
        //将F行日期转为php的'Y-m-d'
        $d = 'F' . $row;
        $currentSheet->getStyle("F1:$d")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
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
        $order = array_map(function ($val) {
            return Yii::$app->functions->removeWhitespace($val);
        }, $order);

        $model = new OfflineOrder();
        $branch = SaleBranch::find()->where(['branchName' => $order[0]])->one();
        $loan = OfflineLoan::find()->where(['title' => $order[1]])->one();
        if (null !== $branch) {
            $branch_id = $branch->id;
        } else {
            $newbranch = new SaleBranch();
            $newbranch->branchName = $order[0];
            $newbranch->save();
            $branch_id = $newbranch->id;
        }
        if (null !== $loan) {
            $loan_id = $loan->id;
        } else {
            $newloan = new OfflineLoan();
            $newloan->title = $order[1];
            $newloan->save();
            $loan_id = $newloan->id;
        }
        $model->branch_id = $branch_id;
        $model->loan_id = $loan_id;
        $model->realName = $order[2];
        $model->mobile = $order[3];
        $model->money = $order[4];
        $model->orderDate = $order[5];
        $model->created_at = time();
        return $model;
    }
}
