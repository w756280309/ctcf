<?php

use yii\widgets\LinkPager;
$this->title = '精选项目介绍页列表';

$this->registerJsFile('/js/clipboard.min.js', ['depends' => 'yii\web\YiiAsset']);
?>

<?php $this->beginBlock('blockmain'); ?>
<style>
    .valign-middle {
        vertical-align: middle !important;
    }
</style>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <h3 class="page-title">
                 发行方管理 <small>运营模块</small>
                <a href="/product/jing-xuan/add" class="btn green float-right">
                    <i class="icon-plus"></i> 添加精选项目介绍页
                </a>
            </h3>
            <ul class="breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <a href="/adv/adv/index">运营管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="/product/issuer/list">发行方管理</a>
                    <i class="icon-angle-right"></i>
                </li>
                <li>
                    <a href="javascript:void(0);">精选项目介绍页列表</a>
                </li>
            </ul>
        </div>

        <div class="portlet-body">
            <table class="table table-striped table-bordered table-advance table-hover">
                <thead>
                <tr>
                    <th class="valign-middle">页面标题</th>
                    <th class="valign-middle">发行方名称</th>
                    <th class="valign-middle">当前页面链接</th>
                    <th style="width: 25%" class="valign-middle"><center>操作</center></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($jxPage as $page) : ?>
                    <tr>
                        <td class="valign-middle"><?= $page->title ?></td>
                        <td class="valign-middle"><?= $page->issuer->name ?></td>
                        <td class="valign-middle"><a target="_blank" href="<?= Yii::$app->params['clientOption']['host']['wap'].'issuer?id='.$page->issuerId ?>">/issuer?id=<?= $page->issuerId ?></td>
                        <td class="valign-middle">
                            <center>
                                <a href="Javascript:void(0)" data-clipboard-text="/issuer?id=<?= $page->issuerId ?>" class="btn mini purple copy-buttons"><i class="icon-edit"></i>复制链接</a>
                                <a href="/product/jing-xuan/edit?id=<?= $page->id ?>" class="btn mini green"><i class="icon-edit"></i>编辑</a>
                            </center>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!--分页-->
        <div class="pagination"><center><?= LinkPager::widget(['pagination' => $pages]) ?></center></div>
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
