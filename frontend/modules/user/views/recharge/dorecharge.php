<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<script language="JavaScript" type="text/JavaScript">
    function doSubmit() {    
        document.recharge.submit();
    }
</script>
</head>
<body onload="doSubmit()">
    <form action="<?= Yii::$app->params['cfca']['PAYURL']?>" name="recharge" method="post">
    <input type="hidden" name="message" value="<?= $message?>" />
    <input type="hidden" name="signature" value="<?= $signature?>" />
    </form>
</body>
</html>

