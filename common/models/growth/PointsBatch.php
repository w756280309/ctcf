<?php

namespace common\models\growth;

use common\models\offline\OfflineUser;
use common\models\user\User;
use common\utils\SecurityUtils;
use Yii;

/**
 * 批量发积分
 * This is the model class for table "points_batch".
 *
 * @property integer $id
 * @property string  $batchSn       导入批次,每次导入之后、更新数据之前生成，统一批次导入数据batchSn值一样
 * @property string  $createTime    导入时间,同一批次值一样
 * @property integer $isOnline      是否时线上用户，1:线上用户;0:线下用户
 * @property string  $publicMobile  用于显示的手机号
 * @property string  $safeMobile    加密后的手机号
 * @property integer $user_id       用户ID
 * @property integer $points        待发放积分
 * @property string  $desc          积分描述
 * @property integer $status        发放状态 0:未发放;1:成功发放;2:发放失败
 */
class PointsBatch extends \yii\db\ActiveRecord
{
    public $mobile;//手机号

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'points_batch';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['points', 'isOnline', 'user_id', 'publicMobile', 'safeMobile', 'mobile'], 'required'],
            [['createTime'], 'safe'],
            ['isOnline', 'boolean', 'message' => '“是线上用户”必须是bool类型(0,1)'],
            [['points', 'status'], 'integer'],
            [['batchSn'], 'string', 'max' => 32],
            [['publicMobile'], 'safe'],
            [['safeMobile', 'desc'], 'string', 'max' => 255],
            ['mobile', 'validateMobile'],
            ['idCard','string'],
        ];
    }

    public function validateMobile($attribute)
    {
        if (strlen(trim($this->mobile)) !== 11) {
            $this->addError($attribute, '手机号格式不正确');
        }
        if (!$this->hasErrors()) {
            $isOnline = $this->isOnline;
            $safeMobile = SecurityUtils::encrypt($this->mobile);
            if ($isOnline) {
                $user = User::findOne(['safeMobile' => $safeMobile]);
                if (is_null($user)) {
                    $this->addError('mobile', '线上用户不存在');
                }
            } else {
                if (is_null($this->idCard)) {
                    $this->addError('idCard', '线下用户身份证号不能为空');
                }
                $user = OfflineUser::findOne(['mobile' => $this->mobile, 'idCard' => $this->idCard]);
                if (is_null($user)) {
                    $user = OfflineUser::findOne(['mobile' => $this->mobile, 'idCard' => SecurityUtils::decrypt($this->idCard)]);
                    if (is_null($user)) {
                        $this->addError('mobile', '线下用户不存在');
                    }
                }
            }
            if ($this->points == 0) {
                $this->addError('points', '积分不能为0');
            }
            if (abs($this->points) > 100000) {
                $this->addError('points', '积分值不能超过100000');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'batchSn' => '导入批次',
            'createTime' => '导入时间',
            'isOnline' => '是线上用户',
            'mobile' => '手机号',
            'idCard' => '身份证号',
            'publicMobile' => '手机号',
            'safeMobile' => '加密后的手机号',
            'user_id' => '用户',
            'points' => '待发放积分',
            'desc' => '描述',
            'status' => '状态',
        ];
    }
}
