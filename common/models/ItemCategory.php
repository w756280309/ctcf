<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "item_category".
 *
 * @property string $id
 * @property string $item_id
 * @property string $category_id
 * @property integer $type
 * @property integer $updated_at
 * @property integer $created_at
 */
class ItemCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'category_id', 'type'], 'required'],
            [['item_id', 'category_id', 'updated_at', 'created_at', 'type'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_id' => 'Item ID',
            'category_id' => 'Category ID',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * 添加项目和分类对照
     * @param integer $item_id 项目ID
     * @param Category $category 分类对象
     * @return bool
     */
    public static function addItem($item_id, Category $category)
    {
        if (!self::find()->where(['item_id' => $item_id, 'category_id' => $category['id'], 'type' => $category['type']])->one()) {
            $model = new self([
                'item_id' => $item_id,
                'category_id' => $category['id'],
                'type' => $category['type']
            ]);
            $res = $model->save();
            return $res ? true : false;
        }
        return true;
    }

    /**
     * 获取项目-分类对照对象数组
     * @param array $categoryIds 分类数组
     * @param int $type 分类类型
     * @return array  对象数组或空数组
     */
    public static function getItems(array $categoryIds, $type)
    {
        $model = self::find()->where(['in', 'category_id', $categoryIds])->andWhere(['type' => $type])->all();
        if ($model) {
            return ArrayHelper::getColumn($model, 'item_id');
        } else {
            return [];
        }
    }

    /**
     * 删除指定项目的所有对照
     * @param array $item_ids 指定项目id数组
     * @param int $type 分类类型
     * @throws \Exception
     */
    public static function clearItems(array $item_ids, $type)
    {
        $items = self::find()->where(['in', 'item_id', $item_ids])->andWhere(['type' => $type])->all();
        if (count($items) > 0) {
            foreach ($items as $item) {
                $item->delete();
            }
        }
    }
}
