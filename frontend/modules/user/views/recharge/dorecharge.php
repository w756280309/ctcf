<?php

$payUrl = Yii::$app->params['cfca']['payUrl'];
if (empty($payUrl)) {
    throw new \Exception('Pay URL not set.');
}

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <script language="JavaScript" type="text/JavaScript">
            function doSubmit()
            {
                document.recharge.submit();
            }
        </script>
    </head>
    <body onload="doSubmit()">
        <form action="<?= $payUrl ?>" name="recharge" method="post">
            <input type="hidden" name="message" value="<?= $message ?>" />
            <input type="hidden" name="signature" value="<?= $signature ?>" />
        </form>
    </body>
</html>
