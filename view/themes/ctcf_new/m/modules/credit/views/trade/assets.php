<?php
$this->title = '我的转让';
$this->backUrl = '/user/user';

$this->registerCssFile(ASSETS_BASE_URI.'ctcf/css/assets/transfer_list.css?v=20180210111', ['depends' => 'wap\assets\WapAsset']);
$this->registerJsFile(ASSETS_BASE_URI.'js/ua-parser.min.js?v=1', ['depends' => 'wap\assets\WapAsset','position' => 1]);
$this->registerJsFile(ASSETS_BASE_URI.'js/AppJSBridge.min.js?v=1', ['depends' => 'wap\assets\WapAsset','position' => 1]);

?>
<script type="text/javascript">
    var url = '/credit/trade/assets?type=<?= $type ?>';
    var tp = '<?= $pages->pageCount ?>';
</script>
<script src="<?= ASSETS_BASE_URI ?>js/page.js?v=1"></script>

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
      var uaString = navigator.userAgent.toLowerCase();
      var ownBrowser = [[/(wjfa.*?)\/([\w\.]+)/i], [UAParser.BROWSER.NAME, UAParser.BROWSER.VERSION]];
      var parser = new UAParser(uaString, {browser: ownBrowser});
      var versionName= parser.getBrowser().version;
      if(versionName >= '2.4'){
        window.NativePageController('openPullRefresh', {  'enable': "true" });
      }

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
