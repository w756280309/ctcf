<?php
$this->title = '产品合同';

$this->registerCssFile(ASSETS_BASE_URI.'css/deal/productcontract.css', ['depends' => 'frontend\assets\FrontAsset']);
?>
<style>
    .my_option{display: none;position: absolute;padding: 10px;z-index: 1000;}
    .my_option span{display: block;line-height: 30px;color: #99999d;}
    .my_option span:hover{color:#f44336;cursor: pointer; }
    .my_option span.active{color:#f44336;}
    .credit_contract .caret{
        display: inline-block;
        width: 0;
        height: 0;
        margin-left: 2px;
        vertical-align: middle;
        border-top: 4px solid;
        border-right: 4px solid transparent;
        border-left: 4px solid transparent;
    }
</style>

<div class="contract-box clearfix">
    <div class="contract-container">
        <div class="contract-container-box">
            <p class="contract-title">产品合同</p>
            <div class="contract-content">
                <ul class="title-box">
                    <?php foreach ($loanContracts as $key => $contract) : ?>
                        <li class="loan_content title <?= 0 === $key ? 'active' : '' ?>" key="<?= $key?>">
                            <a>
                                <?= $contract['title'] ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <?php if(count($creditContracts) > 0) { ?>
                    <li class="credit_contract title">
                            <a>产品转让协议<span class="caret"> </span></a>
                        <div class="my_option">
                            <?php foreach($creditContracts as $key => $contract) { ?>
                                <span  value="<?= $key?>"><?= $contract['title'] . str_pad($key + 1, 2, '0', STR_PAD_LEFT);?></span>
                            <?php }?>
                        </div>
                    </li>
                    <?php }?>
                </ul>
                <div class="pagination">
                    <div class="list">
                        <?= html_entity_decode($loanContracts[0]['content']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var loanContracts = <?= json_encode($loanContracts)?>;
    var creditContracts = <?= json_encode($creditContracts)?>;
    $('.loan_content').click(function() {
        $('.credit_contract').removeClass('active');
        $('.credit_contract span').removeClass('active');
        if (!$(this).hasClass('active')) {
            k = parseInt($(this).attr('key'));
            $('.loan_content').removeClass('active');
            $(this).addClass('active');
            $('.contract-content .pagination .list').html(loanContracts[k]['content']);
        }
    });
    $('.credit_contract').hover(function(){
        $('.my_option').show();
    });
    $('.credit_contract').mouseleave(function(){
        $('.my_option').hide();
    });
    $('.credit_contract span').click(function(){
        var key = parseInt($(this).attr('value'));
        $('.credit_contract span').removeClass('active');
        $(this).addClass('active');
        $('.loan_content').removeClass('active');
        $(this).parent().parent().addClass('active');
        $('.contract-content .pagination .list').html(creditContracts[key]['content']);
    });
</script>