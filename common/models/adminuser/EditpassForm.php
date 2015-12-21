<?php
namespace common\models\adminuser;

use common\models\adminuser\Admin;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class EditpassForm extends Model
{
    public $password;
    public $new_pass;
    public $r_pass;
    
    private $_id = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required', 'message' => '原密码不能为空!'],
            ['new_pass', 'required', 'message' => '新密码不能为空!'],
            ['r_pass', 'required', 'message' => '确认密码不能为空!'],
            ['password', 'string','length' => [6, 12]],
            ['new_pass', 'string','length' => [6, 12]],
            ['r_pass', 'string','length' => [6, 12]],
            ['r_pass', 'compare', 'compareAttribute'=>'new_pass','message'=>'两处输入的密码不一致'],
            ['password', 'validatePassword']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => '用户密码',
            'new_pass' => '新密码',
            'r_pass' => '确认密码'
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $id = $this->getId();
            if (!$id || !$id->validatePassword($this->password)) {
                $this->addError($attribute, '原始密码不正确!');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function editpass()
    {
        if ($this->validate()) {
            $model = $this->getId();
            $model->scenario = 'editpass';
            $model->setPassword($this->new_pass);
            $model->save();
            return $model;
        } else {
            return false;
        }
    }
    
    public function getId()
    {
        if ($this->_id === false) {
            $id = Yii::$app->user->getIdentity()->id;
            $this->_id = Admin::findIdentity($id);
        }
        
        return $this->_id;
    }
}
