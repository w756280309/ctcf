<?php
$this->title = '风险揭示';

use wap\assets\WapAsset;

$this->registerCssFile(ASSETS_BASE_URI.'css/informationAndHelp.css', ['depends' => WapAsset::class]);
?>

<?php if (1 === $type) { ?>
    <div class="container bootstrap-common helpcenter_login_resister">
        <div class="kong-width">
            <div class="row single">
                <p>尊敬的客户：</p>
                <p>在转让本产品之前，请您仔细阅读如下风险提示，并按照您的实际资产情况与风险承受能力，审慎作出转让决策。</p>
                <p><b>一、损失部分利息的风险</b></p>
                <p>楚天财富转让规则规定，根据转让人设定的折让率计算所得的转让价款不得低于转让金额（转让金额为转让人拟转让的出借本金，折让率是指产品在转让时折让的比率）。因此，如果您设定的折让率为零，您不会因转让行为损失出借本金和根据合同约定所计算的自收益起算日至转让成功日期间的出借收益；但如果您设定的折让率大于零，您将损失部分出借收益。</p>
                <p><b>二、转让失败风险</b></p>
                <p>楚天财富仅为转让人和受让人提供中介服务，并不承诺保证每一个转让申请都能全部/部分成功完成转让。因此，在转让申请时效内，您可能面临全部/部分产品转让失败的风险。</p>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="container bootstrap-common helpcenter_login_resister">
        <div class="kong-width">
            <div class="row single">
                <p>尊敬的客户：</p>
                <p>您即将受让<?= $loan->title ?>产品（以下简称本产品），受让成功后您将成为本产品的出借者。为了使您更好地了解本产品风险，现向您提供本风险揭示书。请您在实际受让前，充分了解本产品的特点，仔细阅读本风险揭示书和相关发行文件（包括但不限于产品说明书、出借协议、产品转让协议等相关发行、转让文件）的所有条款，并充分理解条款的含义，根据您自身的出借目标、出借经验、财务状况、风险承受能力及其他相关情况，慎重，确保您能够识别和承担本产品可能给您带来的损失。</p>
                <p>本产品面临的风险包括但不限于以下几方面：</p>
                <p><b>一、法律与政策风险</b></p>
                <p>国家监管政策、财政税收政策、产业政策、宏观政策及相关法律法规的调整与变化将会影响产品的正常运行，甚至导致您不能获得预期收益。</p>
                <p><b>二、发行人经营风险</b></p>
                <p>受宏观经济环境和发行人所在行业环境及发行人自身经营水平的影响，在产品存续期间发行人若无法继续经营或经营出现困难，则可能会对本产品的兑付产生不利影响。</p>
                <p><b>三、提前兑付风险</b></p>
                <p>您持有本产品期间，如遇国家金融政策改变或发行人提前还款，则本产品将可能被提前兑付，产品将按您持有产品的实际天数计算产品收益，您可能无法实现预期的全部收益，并将面临再出借机会风险。</p>
                <p><b>四、延期兑付风险</b></p>
                <p>因市场内部和外部的原因导致产品不能及时清算兑付，您获得产品收益的时点将会延后，从而导致本金及收益的延期支付。</p>
                <p><b>五、受让失败风险</b></p>
                <p>您的受让行为可能因为网络、操作等问题而导致本产品的受让失败、资金划拨失败等，从而导致您的本金及收益发生损失。</p>
                <p><b>六、操作风险</b></p>
                <p>1.不可预测或无法控制的系统故障、设备故障、通讯故障、停电等突发事故将有可能给您造成一定损失。因上述事故造成交易或交易数据中断，恢复交易时以事故发生前系统最终记录的交易数据为有效数据；</p>
                <p>2.由于密码失密、操作不当、决策失误、黑客攻击等原因可能会造成您的损失；</p>
                <p>3.网上交易、热键操作完毕，未及时退出，他人进行恶意操作将可能造成您的损失；</p>
                <p>4.委托他人代理交易、或长期不关注账户变化，可能致使他人恶意操作而造成您的损失；</p>
                <p>5.由于银行系统延迟、代扣代收机构系统故障等原因，造成交易双方不能及时收取或支付本协议项下款项。</p>
                <p><b>七、信息传递风险</b></p>
                <p>您可通过楚天财富网站或交易终端等，及时了解本产品的相关信息和公告，并充分理解本平台交易规则及相关配套制度。如您未及时查询，或对交易规则和配套制度的理解不够准确，导致出借决策失误，由此产生的责任和风险由您自行承担。</p>
                <p><b>八、其他风险</b></p>
                <p>战争、自然灾害、政府行为等不可抗力可能导致产品有遭受损失的风险, 以及证券市场、资产管理人、资产托管人可能因不可抗力无法正常工作, 从而有影响产品的兑付的风险。</p>
                <p><b>以上并不能揭示您出借本产品的全部风险及市场的全部情形。您在受让前，务必认真阅读转让规则、转让协议以及本产品的产品说明书、出借协议、本风险揭示书等法律文件的全部内容，理解、掌握本产品的特点和所有条款规则，并通过楚天财富网站、交易终端所公布的信息及其他相关公告了解拟出借产品的风险收益特征，确保出借金额占用您可支配收入总额的合理份额，根据自身的风险承受能力和资产状况等谨慎决策，自行承担全部风险。如影响您风险承受能力的因素发生变化，请及时联系楚天财富更新您的风险承受能力评估结果。</b></p>
            </div>
        </div>
    </div>
<?php } ?>