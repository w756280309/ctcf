<?php

namespace common\models\adminuser;

use Yii;

/**
 * This is the model class for table "auth".
 *
 * 时间表sn组合规则如下
 * P代表产品管理，N代表资讯内容管理，U代表会员管理，A代表广告营销，C代表渠道管理，S代表系统管理
 * 第一位为1
 * 第二位到第四位为按照左侧二级菜单顺序递增排列从001开始的数列
 * 最后三位代表不同的功能
 * 最后三位的第一位若为1则代表”添加“，”上线“，”下线“，”批量删除“等页面按钮；
 *                           若为2则代表单个目标的操作按钮；
 * 最后三位的第二位到第三位代表如下
 * 01：添加      02：修改      03：删除     04：复制     05：上线     06：下线     07：批量删除    08：产品管理——投资记录查看
 * 09：会员列表——重新审核  10：冻结激活   11：会员投资明细    12：渠道管理——导入数据
 * 13：渠道管理——导出会员数据      14：权限——禁用   15：显示  16：线上产品管理——撤销  17：线上产品管理——放款批次
 * 18：提现管理——查看  19：提现管理——审核  20：提现管理——放款  21：会员列表——账户查阅  22 :  线上产品管理——放款
 * 23：会员列表——创建融资账户   24：产品管理——线上标的管理——查看订单
 * 
 * @property integer $id
 * @property string $sn
 * @property string $psn
 * @property string $auth_name
 * @property string $auth_description
 * @property integer $status
 * @property integer $updated_at
 * @property integer $created_at
 */
class Auth extends \yii\db\ActiveRecord {




    //0不显示，1正常
    const STATUS_SHOW = 0;
    const STATUS_HIDDEN = 1;




    /**
     * @inheritdoc
     */
    public static function tableName() {
	return 'auth';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
	return [
	    [['sn','auth_name', 'auth_description','path'], 'required'],
             ['sn','unique','message'=>'编号已占用'],
	    [['status', 'updated_at', 'created_at'], 'integer'],
	    [['sn', 'psn'], 'string', 'max' => 24],
	    [['auth_name'], 'string', 'max' => 50],
	    [['auth_description'], 'string', 'max' => 100],
	    [['sn'], 'unique']
	];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
	return [
	    'id' => 'ID',
	    'sn' => '编号',
	    'psn' => 'Psn',
	    'auth_name' => '权限名称',
	    'auth_description' => '权限说明',
             'path' => '地址',
	    'status' => '状态',
	    'updated_at' => 'Updated At',
	    'created_at' => 'Created At',
	];
    }

}
