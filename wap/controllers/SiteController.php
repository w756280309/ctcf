<?php
namespace app\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use common\service\SmsService;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use common\models\adv\Adv;
use common\models\product\OnlineProduct;
use common\models\user\SignupForm;
use common\models\user\LoginForm;
use common\models\user\EditpassForm;
use common\service\LoginService;
use common\models\log\LoginLog;
use yii\data\Pagination;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                //'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                //'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                //'backColor'=>"black",
                //'foreColor' => ''
                'minLength'=>6,'maxLength'=>6
            ],
        ];
    }


    public function actionError()
    {
        $exception = \Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $exception;
            return $this->redirect('/');
            //return $this->render('error', ['exception' => $exception]);
        }else{
            return "";
        }
    }

    public function actionUsererror(){
        $this->layout = "@app/modules/order/views/layouts/buy";
        return $this->render('usererror');
    }

    public function actionIndex()
    {
        $this->layout='main';
        //var_dump();
        $ac =5;
        $dc = 5;
        $adv = Adv::find()->where(['status' => 0, 'del_status' => 0])->select('image,link,description')->limit($ac)->orderBy("id desc")->asArray()->all();

        $deals = OnlineProduct::find()->where(['del_status'=>OnlineProduct::STATUS_USE,'online_status'=>OnlineProduct::STATUS_ONLINE])->select('id k,sn as num,title,yield_rate as yr,status,expires as qixian,money,start_date start,finish_rate')->limit($dc)->orderBy("sort asc,id desc")->asArray()->all();
        foreach($deals as $key => $val)
        {
            $dates = Yii::$app->functions->getDateDesc($val['start']);
            $deals[$key]['start'] = date("H:i",$val['start']);
            $deals[$key]['start_desc'] = $dates['desc'];
            $deals[$key]['yr'] = $val['yr']?number_format($val['yr']*100,2):"0.00";
            $deals[$key]['statusval'] = Yii::$app->params['productonline'][$val['status']];
        }
        return $this->render('index',['adv'=>$adv,'deals'=>$deals]);
    }

    public function actionLogin() {
        $this->layout = false;
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        $model = new LoginForm();
        $from = Yii::$app->request->referrer;
        $from = Yii::$app->functions->dealurl($from);

        $is_flag = Yii::$app->request->post('is_flag');    //是否需要校验图形验证码标志位
        if($is_flag && !is_bool($is_flag)) {
            $is_flag = true;
        }

        if ($is_flag) {
            $model->scenario = 'verifycode';
        } else {
            $model->scenario = 'login';
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->login()) {
                $post_from = Yii::$app->request->post('from');
                if (!empty($post_from)) {
                    return ['code' => 0, 'message' => '登录成功', 'tourl' => $post_from];
                } else {
                    $url = Yii::$app->getUser()->getReturnUrl();
                    return ['code' => 0, 'message' => '登录成功', 'tourl' => $url];
                }
            }
        }

        $login = new LoginService();

        if ($model->getErrors('password')) {
            $login->logFailure(Yii::$app->request, $model->phone, LoginLog::TYPE_WAP);
        }

        $is_flag = $is_flag? $is_flag : $login->isCaptchaRequired(Yii::$app->request, $model->phone);

        if ($model->getErrors()) {
            $message = $model->firstErrors;
            if ($is_flag) {
                return ['tourl' => '/site/login', 'code' => 1, 'message' => current($message)];
            }

            return ['code' => 1, 'message' => current($message)];
        }

        return $this->render('login', [
                    'model' => $model,
                    'from' => $from,
                    'is_flag' => $is_flag
        ]);
    }

    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionEditpass() {
        $this->layout = "@app/modules/order/views/layouts/buy";
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/site/login');
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        $model = new EditpassForm();
        $model->scenario = 'edituserpass';
        if($model->load(Yii::$app->request->post())) {
            if($model->edituserpass()) {
                \Yii::$app->user->logout();
                return $this->goHome();
            }
        }
        if($model->getErrors()) {
            $message = $model->firstErrors;
            return ['code' => 1, 'message' => current($message)];
        }

        return $this->render('editpass',['model' => $model]);
    }

    public function actionResetpass() {
        $this->layout = false;
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        $model = new SignupForm();
        if($model->load(Yii::$app->request->post())&&Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->reset_flag = 1;
            if($model->validate()&&$model->resetpass()) {
                \Yii::$app->user->logout();
                return ['code'=>1,"message"=>'密码重置成功','tourl'=>'/site/login'];
            } else {
                $message = $model->firstErrors;
                return ['code' => 1, 'message' => current($message)];
            }
        }

        return $this->render('resetpass');
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSignup()
    {
        $this->layout=false;
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())&&Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    $user->scenario = 'login';
                    $user->last_login = time();
                    $user->save();
                    return ['code'=>1,"message"=>'注册成功','tourl'=>'/'];
                }
            }else{
                $error = $model->firstErrors;
                return ['code'=>1,"message"=>current($error)];
            }
        }

        return $this->render('signup');
    }

    public function actionTestdata($page = 1){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $count = \common\models\adminuser\Auth::find()->count();
        $size = 5;
        $pages = new Pagination(['totalCount' => $count, 'pageSize' => $size]);

        $models = \common\models\adminuser\Auth::find()->offset(($page-1)*$size)->limit($pages->limit)->asArray()->all();
        $tp = ceil($count/$size);
        $code = ($page>$tp)?1:0;
        $message = ($page>$tp)?'数据错误':'消息返回';
        return ['header'=>['count'=>  intval($count),'size'=>$size,'tp'=>$tp,'cp'=>  intval($page)],'code'=>$code,'message'=>$message,'deals'=>$models];
    }

    public function actionXieyi()
    {
        $this->layout = "@app/modules/order/views/layouts/buy";
        return $this->render('xieyi');
    }

    public function actionCreatesmscode() {
        $uid = Yii::$app->request->post('uid');
        $type = Yii::$app->request->post('type');
        $phone = Yii::$app->request->post('phone');

        $result = SmsService::createSmscode($type,$phone,$uid);

        return $result;
    }

    public function actionT(){
        $payment = new \common\lib\cfca\Payment();
        $InstitutionID = 1;
        $xmltx1810 = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Request version="2.1">
  <Head>
    <TxCode>1810</TxCode>
  </Head>
  <Body>
        <InstitutionID/>
	<Date/>
  </Body>
</Request>
XML;
        $simpleXML = new \SimpleXMLElement($xmltx1810);
        $simpleXML->Body->InstitutionID = $InstitutionID;
        $simpleXML->Body->Date = '2015-12-10';

        $xmlStr = $simpleXML->asXML();
        $message = base64_encode(trim($xmlStr));
        $signature = $payment->cfcasign_pkcs12(trim($xmlStr));
        $response = $payment->cfcatx_transfer($message, $signature);
        $plainText = trim(base64_decode($response[0]));
        $ok = $payment->cfcaverify($plainText, $response[1]);
        var_dump($plainText,$ok);
    }

    public function actionTrc(){
        $xmltx1375 = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Request version="2.1">
  <Head>
    <InstitutionID/>
    <TxCode>1375</TxCode>
  </Head>
  <Body>
        <OrderNo/>
	<PaymentNo/>
	<TxSNBinding/>
        <Amount/>
        <Remark/>
  </Body>
</Request>
XML;
        $InstitutionID = 1;
        $simpleXML = new \SimpleXMLElement($xmltx1375);
        $simpleXML->Head->InstitutionID = $InstitutionID;
        $simpleXML->Body->OrderNo = time();
        $simpleXML->Body->PaymentNo = time();
        $simpleXML->Body->TxSNBinding = time();
        $simpleXML->Body->Amount = 1000;

        $payment = new \common\lib\cfca\Payment();
        $xmlStr = $simpleXML->asXML();
        $message = base64_encode(trim($xmlStr));
        $signature = $payment->cfcasign_pkcs12(trim($xmlStr));
        $response = $payment->cfcatx_transfer($message, $signature);
        $plainText = (base64_decode($response[0]));//去掉了前边的trim
        $ok = $payment->cfcaverify($plainText, $response[1]);
        var_dump($plainText,$ok);
    }


}
