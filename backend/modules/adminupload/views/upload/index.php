<?php

use yii\widgets\LinkPager;

$this->title = '文件列表';
$this->registerJsFile('/js/clipboard.min.js', ['depends' => 'yii\web\YiiAsset']);

?>
<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
资讯管理
                <small>运营模块</small>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/adminupload/upload/index">上传管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">文件上传</a>
                </li>
            </ul>
        </div>
        <!--search start-->
        <div class="portlet-body">
            <form action="/adminupload/upload/index" method="get" target="_self">
                <table class="table">
                    <tbody>
                    <tr style="text-align: right">
                        <td colspan="4"></td>
                        <td><a href="/adminupload/upload/edit" class="btn blue btn-block" style="width: 100px;">上传文件</a></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="title">文件名</span>
                        </td>
                        <td><input type="text" class="m-wrap" style="margin-bottom: 0px" id="title" name='title'
                                   value="<?= $title ?>" placeholder="请输入标题"/></td>
                        <td>
                            <span class="title">文件类型</span>
                        </td>
                        <td>
                            <select name="extension">
                                <option value="">全部</option>
                                <?php foreach($extension as $val) : ?>
                                    <option value="<?= $val ?>" <?php if(Yii::$app->request->get('extension') === $val){ ?> selected="selected" <?php } ?>><?= $val ?></option>
                                <?php endforeach;?>
                            </select>
                        </td>
                        <td>
                            <button class="btn blue btn-block" style="width: 100px;">
                                查询 <i class="m-icon-swapright m-icon-white"></i>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>

        <!--search end -->
        <div class="portlet-body">
            <table class="table table-striped table-bordered table-advance table-hover">
                <thead>
                <tr>
                    <th style="text-align: center">文件名</th>
                    <th style="text-align: center">文件类型</th>
                    <th style="text-align: center">文件地址</th>
                    <th style="text-align: center">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($model as $key => $val) : ?>
                    <tr>
                        <td style="text-align: center">
                            <?= $val->title ?>
                        </td>
                        <td style="text-align: center">
                            <?php
                                if (strrpos($val->link, ".")) {
                                    echo substr($val->link, strrpos($val->link, ".")-strlen($val->link)+1);
                                }
                            ?>
                        </td>
                        <td style="text-align: center">
                            <?php
                                if ($val->allowHtml) {
                                    echo "upload/showpic?id=$val->id";
                                } else {
                                    echo $val->link;
                                }
                            ?>
                        </td>
                        <td style="text-align: center">
                            <a href="Javascript:void(0)" data-clipboard-text="<?php if ($val->allowHtml) { echo "upload/showpic?id=$val->id"; } else { echo $val->link; } ?>" class="btn mini purple copy-buttons"><i
                                    class="icon-edit"></i> 复制链接</a>
                            <a href="/adminupload/upload/edit?id=<?= $val['id'] ?>" class="btn mini purple"><i
                                    class="icon-edit"></i> 编辑</a>
                            <a href="/adminupload/upload/delete?id=<?= $val['id'] ?>"
                               onclick="javascript:return confirm('文件删除后，链接将失效，确认删除？');" class="btn mini black"><i
                                    class="icon-trash"></i> 删除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="pagination" style="text-align:center;"><?=  LinkPager::widget(['pagination' => $pages]); ?></div>
   </div>
</div>
<script>
    $(function(){
        //复制
        if(navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion .split(";")[1].replace(/[ ]/g,"")=="MSIE8.0" && navigator.appVersion .split(";")[1].replace(/[ ]/g,"")=="MSIE7.0" && navigator.appVersion .split(";")[1].replace(/[ ]/g,"")=="MSIE6.0" ){
            $('.copy-buttons').on('click',function(){
                alert('请手动复制链接');
                return false;
            })
        } else {
            try {
                var btn = $('.copy-buttons');
                btn.each(function(){
                    var clipboard = new Clipboard(this);
                    clipboard.on('success', function(e) {
                        alert('内容已复制到剪贴板');
                    });

                    clipboard.on('error', function(e) {
                        alert('请重新复制');
                    });
                })
            } catch(error) {

            }
        }
    });
</script>
<?php $this->endBlock(); ?>
