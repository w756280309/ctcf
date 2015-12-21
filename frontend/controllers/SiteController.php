<?php
namespace frontend\controllers;

use Yii;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\news\News;
use common\models\user\LoginForm;
use common\models\adv\AdvPos;
use common\models\adv\Adv;
use common\models\user\User;

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
                    'logout' => ['post'],
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
                'minLength'=>4,'maxLength'=>4
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


    public function actionIndex()
    {
        $news_data_1 = News::find()->andWhere(['status'=> News::STATUS_PUBLISH,'category_id'=>1,'home_status'=>  News::HOME_STATUS_SHOW])->limit(5)->orderBy('news_time desc')->all();
        $news_data_2 = News::find()->andWhere(['status'=> News::STATUS_PUBLISH,'category_id'=>2,'home_status'=>  News::HOME_STATUS_SHOW])->limit(5)->orderBy('news_time desc')->all();
        $news_data_3 = News::find()->andWhere(['status'=> News::STATUS_PUBLISH,'category_id'=>3,'home_status'=>  News::HOME_STATUS_SHOW])->limit(5)->orderBy('news_time desc')->all();
        $news_data_4 = News::find()->andWhere(['status'=> News::STATUS_PUBLISH,'category_id'=>4,'home_status'=>  News::HOME_STATUS_SHOW])->limit(5)->orderBy('news_time desc')->all();
        $news = array(1=>$news_data_1,2=>$news_data_2,3=>$news_data_3,4=>$news_data_4);
        $user_model = new LoginForm();
        if ($user_model->load(\Yii::$app->request->post()) && $user_model->login2()) {
            return $this->redirect('/');
        }
        
        $adv = new Adv();
        $index_header_arr = $adv->getPosAdv(AdvPos::POS_HOME_HEAD);
        $index_left_arr = $adv->getPosAdv(AdvPos::POS_HOME_NEWS_LEFT);
        $index_middle_arr = $adv->getPosAdv(AdvPos::POS_HOME_MIDDLE);
        $index_partner_arr = $adv->getPosAdv(AdvPos::POS_HOME_FOOT_PARTNER);//
        $adv_res = ["index_header"=>$index_header_arr,"index_left"=>$index_left_arr,'index_middle'=>$index_middle_arr,"index_partner"=>$index_partner_arr];
        //var_dump($index_header_arr);
        //首页预置广告位数据读取
//        $index_header_model = AdvPos::findOne(['code'=> AdvPos::POS_HOME_HEAD,"del_status"=>  AdvPos::DEL_STATUS_SHOW]);
//        $index_left_model = AdvPos::findOne(['code'=>  AdvPos::POS_HOME_NEWS_LEFT,"del_status"=>  AdvPos::DEL_STATUS_SHOW]);
//        $index_middel_model = AdvPos::findOne(['code'=>  AdvPos::POS_HOME_MIDDLE,"del_status"=>  AdvPos::DEL_STATUS_SHOW]);
        $cookies = Yii::$app->request->cookies;
        $abool = $cookies->has('channelalert');
        if ($abool){
        }else{
            $cookies = Yii::$app->response->cookies;
            $cookies->add(new \yii\web\Cookie([
                'name' => 'channelalert',
                'value' => '1',
            ]));
        }
       // $cookies->add
        //var_dump($cookies);
        return $this->render('index',['news'=>$news,'user_model'=>$user_model,'index_adv'=>$adv_res,"calert"=>$abool]);
    }

    public function actionLogin()
    {
      if (!\Yii::$app->user->isGuest) {
          return $this->goHome();
      }

      $model = new LoginForm();
      if ($model->load(Yii::$app->request->post()) && $model->login()) {
          return $this->goBack();
      } else {
          return $this->render('login', [
              'model' => $model,
          ]);
      }
      return $this->render('login');
      
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
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
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
    
    public function actionClogin(){
        return $this->redirect('/user/login/channel-login');
    }
}
