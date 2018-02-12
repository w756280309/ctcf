<?php
$this->title = '我的转让';
$this->backUrl = '/user/user';

$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/assets/transfer_list.css?v=2018021011', ['depends' => 'wap\assets\WapAsset']);
?>
<script type="text/javascript">
    var url = '/credit/trade/assets?type=<?= $type ?>';
    var tp = '<?= $pages->pageCount ?>';
</script>
<script src="<?= ASSETS_BASE_URI ?>js/page.js"></script>

<div class="row list-title">
    <div class="col-xs-4"><a href="/credit/trade/assets?type=1" class="<?= 1 === $type ? 'active-trans-title' : '' ?> trans-title">可转让</a></div>
    <div class="col-xs-4"><a href="/credit/trade/assets?type=2" class="<?= 2 === $type ? 'active-trans-title' : '' ?> trans-title">转让中</a></div>
    <div class="col-xs-4"><a href="/credit/trade/assets?type=3" class="<?= 3 === $type ? 'active-trans-title' : '' ?> trans-title">已转让</a></div>
</div>

<?php if (!empty($data)) { ?>
    <div id="transferitem-list">
        <div class="col-xs-12 space_div"></div>
        <?= $this->renderFile('@wap/modules/credit/views/trade/_more_assets.php', ['data' => $data, 'type' => $type, 'actualIncome' => $actualIncome]) ?>
        <div class="load"></div>
    </div>
<?php } else { ?>
    <div class="col-xs-12 transfer-no-data">暂无数据</div>
<?php } ?>

<script type="text/javascript">
    $(function() {
        var listTitle=$('.list-title').position().top;

        $(document).scroll(function(e) {
            if($(document).scrollTop()> listTitle ){
                $('.list-title').css('position','fixed').addClass('list-title-top');
                $('.space_div').show();
            }else{
                $('.list-title').css('position','static');
                $('.space_div').hide();
            }
        });
    });
</script>
