<?php

namespace common\models\product;

use common\models\media\Media;
use common\models\order\OnlineRepaymentPlan;
use common\models\order\OnlineRepaymentRecord;
use yii\data\Pagination;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * 发行方（项目）.
 *
 * @property string $id
 * @property string $name           发行方名称
 * @property string $mediaTitle     视频名称
 * @property int    $video_id       视频对应的mediaID
 * @property int    $videoCover_id  视频示例图对应的mediaID
 * @property int    $big_pic        精选项目大图对应的mediaID
 * @property int    $mid_pic        精选项目中图对应的mediaID
 * @property int    $small_pic      精选项目小图对应的mediaID
 * @property boolean   $isShow      首页是否显示图片
 * @property int    $sort           精选项目图片显示顺序
 * @property string $path           图片跳转地址
 */
class Issuer extends ActiveRecord
{
    public $videoUrl;   //存放发行方对应的视频地址
    public $imgUrl;     //存放发行方对应的图片地址

    const SCENARIO_JICHU = 'faXingfang';
    const SCENARIO_KUOZHAN = 'jingXuan';

    public function scenarios()
    {
        return [
            self::SCENARIO_JICHU => ['name', 'mediaTitle', 'videoUrl', 'imgUrl', 'isShow', 'sort'],
            self::SCENARIO_KUOZHAN => ['name', 'big_pic', 'mid_pic', 'small_pic', 'isShow', 'sort', 'path', 'allowShowOnPc', 'pcTitle', 'pcDescription', 'pcLink'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'required', 'on' => self::SCENARIO_JICHU],
            [['name', 'mediaTitle'], 'string', 'on' => [self::SCENARIO_JICHU, self::SCENARIO_KUOZHAN]],
            [['name', 'mediaTitle'], 'trim', 'on' => [self::SCENARIO_JICHU, self::SCENARIO_KUOZHAN]],
            ['videoUrl', 'url', 'on' => self::SCENARIO_JICHU],
            ['videoUrl', 'match', 'pattern' => '/^[a-zA-Z0-9.:\/_-]+$/', 'message' => '{attribute}不应包含特殊字符,如中文等', 'on' => self::SCENARIO_JICHU],   //链接可以包含数字,字母,和一些特殊字符,如.:/_-
            ['imgUrl', 'image', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'on' => self::SCENARIO_JICHU],
            ['imgUrl', 'image', 'skipOnEmpty' => true, 'maxHeight' => 420, 'overHeight' => '{attribute}的高度应为420px', 'on' => self::SCENARIO_JICHU],
            ['imgUrl', 'image', 'skipOnEmpty' => true, 'minHeight' => 420, 'underHeight' => '{attribute}的高度应为420px', 'on' => self::SCENARIO_JICHU],
            ['imgUrl', 'image', 'skipOnEmpty' => true, 'maxWidth' => 750, 'overWidth' => '{attribute}的宽度应为750px', 'on' => self::SCENARIO_JICHU],
            ['imgUrl', 'image', 'skipOnEmpty' => true, 'minWidth' => 750, 'underWidth' => '{attribute}的宽度应为750px', 'on' => self::SCENARIO_JICHU],
            [['big_pic', 'mid_pic', 'small_pic'], 'image', 'extensions' => 'png, jpg', 'on' => self::SCENARIO_KUOZHAN],
            ['big_pic', 'image', 'maxHeight' => 310, 'overHeight' => '{attribute}的高度应为310px', 'on' => self::SCENARIO_KUOZHAN],
            ['big_pic', 'image', 'minHeight' => 310, 'underHeight' => '{attribute}的高度应为310px', 'on' => self::SCENARIO_KUOZHAN],
            ['big_pic', 'image', 'maxWidth' => 670, 'overWidth' => '{attribute}的宽度应为670px', 'on' => self::SCENARIO_KUOZHAN],
            ['big_pic', 'image', 'minWidth' => 670, 'underWidth' => '{attribute}的宽度应为670px', 'on' => self::SCENARIO_KUOZHAN],
            ['mid_pic', 'image', 'maxHeight' => 310, 'overHeight' => '{attribute}的高度应为310px', 'on' => self::SCENARIO_KUOZHAN],
            ['mid_pic', 'image', 'minHeight' => 310, 'underHeight' => '{attribute}的高度应为310px', 'on' => self::SCENARIO_KUOZHAN],
            ['mid_pic', 'image', 'maxWidth' => 370, 'overWidth' => '{attribute}的宽度应为370px', 'on' => self::SCENARIO_KUOZHAN],
            ['mid_pic', 'image', 'minWidth' => 370, 'underWidth' => '{attribute}的宽度应为370px', 'on' => self::SCENARIO_KUOZHAN],
            ['small_pic', 'image', 'maxHeight' => 150, 'overHeight' => '{attribute}的高度应为150px', 'on' => self::SCENARIO_KUOZHAN],
            ['small_pic', 'image', 'minHeight' => 150, 'underHeight' => '{attribute}的高度应为150px', 'on' => self::SCENARIO_KUOZHAN],
            ['small_pic', 'image', 'maxWidth' => 286, 'overWidth' => '{attribute}的宽度应为286px', 'on' => self::SCENARIO_KUOZHAN],
            ['small_pic', 'image', 'minWidth' => 286, 'underWidth' => '{attribute}的宽度应为286px', 'on' => self::SCENARIO_KUOZHAN],
            ['isShow', 'default',  'value' => 0, 'on' => [self::SCENARIO_JICHU, self::SCENARIO_KUOZHAN]],
            ['sort', 'integer', 'on' => [self::SCENARIO_JICHU, self::SCENARIO_KUOZHAN]],
            ['path', 'string', 'on' => self::SCENARIO_KUOZHAN],
            [['path', 'pcLink'], 'match', 'pattern' => '/^[a-zA-Z0-9.:\/?&=_-]+$/', 'message' => '{attribute}不应包含特殊字符,如中文等', 'on' => self::SCENARIO_KUOZHAN],
            [['small_pic', 'mid_pic', 'big_pic'], 'checkPic', 'skipOnEmpty' => false, 'skipOnError' => false, 'on' => self::SCENARIO_KUOZHAN],
            ['allowShowOnPc', 'default',  'value' => 0, 'on' => [self::SCENARIO_JICHU, self::SCENARIO_KUOZHAN]],
            [['pcTitle', 'pcDescription', 'pcLink'], 'required', 'when' => function ($model) {
                return 1 === $model->allowShowOnPc;}, 'whenClient' => "function (attribute, value) {
                return $('#issuer-allowshowonpc').parent().hasClass('checked');
            }", 'on' => self::SCENARIO_KUOZHAN],
            ['pcLink', 'string', 'on' => self::SCENARIO_KUOZHAN],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '发行方ID',
            'name' => '发行方名称',
            'mediaTitle' => '视频名称',
            'videoUrl' => '视频地址',
            'imgUrl' => '视频示例图',
            'big_pic' => '首页精选项目大图',
            'mid_pic' => '首页精选项目中图',
            'small_pic' => '首页精选项目小图',
            'isShow' => '首页显示',
            'sort' => '排序',
            'path' => '图片跳转地址',
            'allowShowOnPc' => 'PC端显示',
            'pcTitle' => 'PC端标题',
            'pcDescription' => 'PC端内容',
            'pcLink' => 'PC端跳转地址',
        ];
    }

    public function checkPic($attribute, $params)
    {
        if (null === $this->$attribute && !isset(UploadedFile::getInstance($this, $attribute)->name)) {
            $this->addError($attribute, '请上传图片');
        }
    }

    /**
     * 获取发行商视频信息.
     */
    public function getVideo()
    {
        return $this->hasOne(Media::class, ['id' => 'video_id']);
    }

    /**
     * 获得发行方的Media信息，大中小图片
     */
    public function getMedias()
    {
        return [
            'big' => Media::findOne($this->big_pic),
            'mid' => Media::findOne($this->mid_pic),
            'small' => Media::findOne($this->small_pic),
        ];
    }

    /**
     * 获取发行商视频示例图.
     */
    public function getVideoImg()
    {
        return $this->hasOne(Media::class, ['id' => 'videoCover_id']);
    }

    public static function getIssuerRecords($issuerId = 1, $pageNum = 0)
    {
        $issuer = self::findOne($issuerId);
        if (empty($issuer)) {
            throw new NotFoundHttpException();
        }

        $query = OnlineProduct::find()
            ->where(['online_status' => OnlineProduct::STATUS_ONLINE, 'del_status' => OnlineProduct::STATUS_USE, 'issuer' => $issuer->id])
            ->orderBy(['created_at' => SORT_DESC]);
        $pages = new Pagination(['totalCount' => $query->count()]);
        if (empty($pageNum)) {
            $model = $query->innerJoinWith('borrower')->all();
        } else {
            $pages->pageSize = $pageNum;
            $model = $query->offset($pages->offset)->limit($pages->limit)->all();
        }

        $plan = [];
        $refundTime = [];
        foreach ($model as $key => $val) {
            if (in_array($val->status, [OnlineProduct::STATUS_HUAN, OnlineProduct::STATUS_OVER])) {
                $plan[$key] = OnlineRepaymentPlan::find()
                    ->where(['online_pid' => $val->id])
                    ->groupBy('online_pid, qishu')
                    ->select(['totalBenjin' => 'sum(benjin)', 'totalLixi' => 'sum(lixi)', 'refund_time', 'qishu', 'online_pid', 'count' => 'count(*)'])
                    ->asArray()
                    ->all();

                foreach ($plan[$key] as $v) {
                    $data = OnlineRepaymentRecord::find()
                        ->where(['online_pid' => $val->id, 'qishu' => $v['qishu']])
                        ->orderBy('refund_time desc')
                        ->all();

                    if ((int) $v['count'] !== count($data)) {       //每期实际放款时间以当期还清状态下的最后一笔为准,总的实际还款日期以全部还款成功的最后一笔为准
                        break;
                    } else {
                        $refundTime[$key][$v['qishu']] = $data[0]->refund_time;
                    }
                }
            }
        }

        return ['issuer' => $issuer, 'model' => $model, 'plan' => $plan, 'refundTime' => $refundTime, 'pages' => $pages];
    }
}
