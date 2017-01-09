<?php $this->beginBlock('blockmain'); ?>
<div class="container-fluid">
    <div class="row-fluid">

        <div class="span12">

            <h3 class="page-title">
                兑换码下载页面
            </h3>
        </div>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="10%">
                    <p><a href="/growth/code/create?gid=<?= $id ?>&num=<?= $num ?>&expiresAt=<?= $expiresAt ?>">下载兑换码txt</a></p>
                </td>
                <td>
                    <p class="c_666 ml40"><a href="/growth/code/goods-list">点击返回商品列表页</a></p></td>
            </tr>
        </table>
    </div>
</div>
<?php $this->endBlock(); ?>
