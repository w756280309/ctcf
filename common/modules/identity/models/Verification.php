<?php
namespace common\modules\identity\models;

use common\models\user\Identity;
use common\models\user\User;
use common\utils\SecurityUtils;
use Lhjx\Identity\VerificationInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class IdentityVerification
 * @property int $id
 * @property string $name    用户名
 * @property string $idCardNum  用户身份证号
 * @property integer $userId    用户id
 * @property string $status     开户状态
 * @property int $identityId    identity表id
 * @property string created_at  创建时间
 * @property string updated_at  更新时间
 */

class Verification extends ActiveRecord implements VerificationInterface
{
    const STATUS_VERIFYING = 'verifying';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    public static function tableName()
    {
        return 'verification';
    }

    public function rules()
    {
        return [
            [['idCardNum', 'name'], 'trim'],
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    public function getName()
    {
        return SecurityUtils::decrypt($this->name);
    }

    public function getIdentity()
    {
        return Identity::findOne(['id' => $this->identityId]);
    }

    public function getIdCardNumber()
    {
        return SecurityUtils::decrypt($this->idCardNum);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => '用户ID',
            'name' => '用户姓名',
            'idCardNum' => '用户身份证号',
            'status' => '开户状态',
            'identity_id' => 'identity表id',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 某段时间内重复登录的query
     * @param Identity $identity  identity对象
     * @param $minute 分钟数
     * @return $this
     */
    public static function getSameInfoQuery(Identity $identity, $minute)
    {
        return self::find()
            ->where(['name' => $identity->name])
            ->andWhere(['idCardNum' => $identity->idCardNumber])
            ->andWhere(['status' => self::STATUS_FAILED])
            ->andWhere(['>', 'created_at', time() - $minute * 60]);
    }

    /**
     * 是否显示验证码
     * @param User $user  当前登录用户
     * @return bool
     */
    public static function isShowCaptcha(User $user)
    {
        $repeatCount = (int) self::find()
            ->where(['userId' => $user->id])
            ->andWhere(['status' => Verification::STATUS_FAILED])
            ->andWhere(['>', 'created_at', time() - 10 * 60])
            ->count();

        return $repeatCount >= 3;
    }

    /**
     * 增加一个verification记录
     * @param Identity $identity  identity对象
     * @param null $userId  当前登录用户
     * @return Verification
     */
    public static function initNew(Identity $identity, $userId = null)
    {
        return new self([
            'name' => $identity->name,
            'idCardNum' => $identity->idCardNumber,
            'userId' => $userId,
            'status' => self::STATUS_VERIFYING,
        ]);
    }
}
