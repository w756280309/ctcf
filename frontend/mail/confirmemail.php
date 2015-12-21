<?php 
use yii\helpers\Html;  
  
/* @var $this yii\web\View */  
/* @var $user common\models\User */  
  
$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['user/default/confirmemail', 'token' => $token]);  
?>  
尊敬的会员您好：
请点击此链接绑定您的Email。
<a href="<?php echo $resetLink ?>" ><?php echo $resetLink ?></a> 。如果以上链接无法点击，请将它复制到您的浏览器地址栏中进行访问，该链接1小时有效。
 
