<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 17-12-16
 * Time: 下午3:22
 */
use yii\bootstrap\ActiveForm;
?>
<link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
<link href="/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="/css/style-metro.css" rel="stylesheet" type="text/css"/>
<link href="/css/style.css" rel="stylesheet" type="text/css"/>
<link href="/css/style-responsive.css" rel="stylesheet" type="text/css"/>

<script src="/js/jquery.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/layer/layer.min.js"></script>
<script type="text/javascript" src="/js/layer/extend/layer.ext.js"></script>
<script type="text/javascript" src="/js/showres.js"></script>
<script type="text/javascript" src="/js/My97DatePicker/WdatePicker.js"></script>

<style>
    p {
        color: red !important;
    }
</style>

<div class="page-container row-fluid" style="margin-top:0px">
    <div class="page-content">
        <div class="form-horizontal form-view">
            <div class="row-fluid">
                <div class="span6 ">
                    <form action="" method="post">
                        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken?>">
                    <div class="control-group">
                        <label class="control-label">付息日期</label>
                        <div class="controls">
                            <span class="text">
                                <input name="fuxi_date" type="text" class ="m-wrap span12" onclick = WdatePicker({dateFmt:"yyyy-MM-dd"}) required>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn blue">确定</button>
                <button type="button" class="btn" onclick="closewin();">取消</button>
            </div>
            </form>
        </div>
        </div>
</div>

<script type="text/javascript">
    $(function() {
        var refresh = '<?= $refresh ?>';
        if (refresh) {
            window.parent.location.reload();
        }
    })
</script>
