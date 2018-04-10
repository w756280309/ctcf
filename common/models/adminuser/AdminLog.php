<?php

namespace common\models\adminuser;

use common\models\product\OnlineProduct;
use Yii;
use yii\db\ActiveRecord;
use yii\web\User;

/**
 * This is the model class for table "admin_log".
 *
 * @property integer $id
 * @property integer $admin_id
 * @property integer $created_at
 * @property string $ip
 * @property string $tableName
 * @property string $primaryKey
 * @property string $allAttributes
 * @property string $changeSet
 */
class AdminLog extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'admin_id'], 'integer'],
            [['allAttributes', 'changeSet', 'tableName'], 'string'],
            [['ip', 'tableName'], 'string', 'max' => 30],
            [['primaryKey'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'admin_id' => '操作用户ID',
            'created_at' => '时间',
            'ip' => 'Ip',
            'tableName' => '被记录对象的表名',
            'primaryKey' => '主键',
            'allAttributes' => '全部属性',//将动对象全部属性json_encode处理
            'changeSet' => '变动属性',//将对象的变动属性进行json_encode处理
        ];
    }

    /**
     * 实例化日志对象
     * @param ActiveRecord|array $object    ActiveRecord及其子类对象或包含tableName、primaryKey的数组
     * @param User $user  当前后台登录用户
     * @param array $changedAttributes  变更属性数组
     * @return AdminLog
     * @throws \Exception
     */
    public static function initNew($object, User $user = null, $changedAttributes = [])
    {
        $array = [];
        if ($object instanceof ActiveRecord) {
            $tableName = $object->tableName();
            $primaryKey = $object->getPrimaryKey();
            $attributes = $object->getAttributes();
            foreach ($attributes as $key => $val) {
                if ($object->isAttributeChanged($key, false)) {
                    $array[$key] = $val;
                }
            }
        } elseif (is_array($object) && key_exists('tableName', $object) && key_exists('primaryKey', $object)) {
            $attributes = [];
            $tableName = $object['tableName'];
            $primaryKey = $object['primaryKey'];
        } else {
            throw new \Exception('Object参数不正确');
        }

        $changedAttributes = array_merge($array, $changedAttributes);
        if (Yii::$app->request->isConsoleRequest) {
            $ip = '';
            $admin_id = '';
        } else {
            $ip = Yii::$app->request->getUserIP();
            $user = $user ?: Yii::$app->user;
            if ($user->isGuest) {
                throw new \Exception('操作标的必须登录');
            }
            $admin_id = $user->identity->getId();
        }
        $log = new self([
            'admin_id' => intval($admin_id),
            'created_at' => time(),
            'ip' => $ip,
            'tableName' => $tableName,
            'primaryKey' => strval($primaryKey),
            'allAttributes' => json_encode($attributes),
            'changeSet' => json_encode($changedAttributes),
        ]);
        return $log;
    }

    public function getAdminName()
    {
        return Admin::find()->select('real_name')->where(['id' => $this->admin_id])->scalar();
    }
}
