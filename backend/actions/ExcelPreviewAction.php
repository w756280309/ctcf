<?php

namespace backend\actions;


use common\utils\ExcelUtils;
use yii\base\Action;
use yii\data\ArrayDataProvider;

/**
 * 文件预览类
 *
 * 需要对上传excel预览时候， 使用该action
 *
 * Class FilePreviewAction
 * @package backend\actions
 */
class ExcelPreviewAction extends Action
{

    public $modelClass;//导入数据所属Model
    public $backUrl;//回退url

    public $maxCol = 'Z';//Excel最大列号, 默认Z
    public $maxRow = 1000;//Excel最大行号, 默认1000
    public $minCol = 'A';//Excel最小列号, 默认A
    public $minRow = '1';//Excel最小行号, 默认1
    public $attributes = [];//Model的属性，和excel数据必须严格对应

    private $path;

    public function init()
    {

        $this->path = __DIR__ . '/../runtime/tmp';
    }

    public function run($batchSn)
    {
        $path = rtrim($this->path, '/');
        $file = $path . '/' . $batchSn;
        if (!file_exists($file)) {
            if (!empty($this->backUrl)) {
                return $this->controller->redirect($this->backUrl);
            } else {
                throw new \Exception('文件未找到');
            }
        }
        if (empty($this->modelClass)) {
            throw new \Exception('缺少核心参数');
        }
        $modelClass = $this->modelClass;
        $data = ExcelUtils::readExcelToArray($file, $this->maxCol, $this->maxRow, $this->minCol, $this->minRow);

        $totalCount = $successCount = $failCount = 0;
        $successRecords = $failRecords = [];
        foreach ($data as $excelRow) {
            $data = array_combine($this->attributes, $excelRow);
            if (empty($data)) {
                throw new \Exception('获取数据失败');
            }
            $model = new $modelClass($data);
            $totalCount++;
            if ($model->validate($this->attributes)) {
                $successCount++;
                $successRecords[] = $model;
            } else {
                $failRecords[] = $model;
                $failCount++;
            }
        }

        $records = array_merge($failRecords, $successRecords);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $records,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);

        return $this->controller->render('preview', [
            'dataProvider' => $dataProvider,
            'attributes' => $this->attributes,
            'totalCount' => $totalCount,
            'successCount' => $successCount,
            'failCount' => $failCount,
            'batchSn' => $batchSn,
        ]);
    }
}