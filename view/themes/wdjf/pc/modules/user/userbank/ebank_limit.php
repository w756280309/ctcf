<?php
use yii\helpers\Html;

$bid = Html::encode($bid);
?>

<?php if ('100' === $bid) { ?>
<!--中国邮政储蓄银行-->
    <table>
        <tr>
            <th>卡种</th>
            <th>所需条件</th>
            <th>单笔限额（元）</th>
            <th>每日限额（元）</th>
        </tr>
        <tr>
            <td rowspan="3">借记卡</td>
            <td>手机短信服务</td>
            <td>5万</td>
            <td>5万</td>
        </tr>
        <tr>
            <td>电子令牌+手机短信服务</td>
            <td>20万</td>
            <td>20万</td>
        </tr>
        <tr>
            <td>UKEY+手机短信服务</td>
            <td>200万</td>
            <td>200万</td>
        </tr>
    </table>
    <p class="bank-note">以上限额仅供参考，以银行官方公告为准，若在银行设置的网上支付额度低于以上限额，以银行设置的为准， 邮政储蓄银行客服热线：95580。</p>
<?php } elseif ('102' === $bid) { ?>
<!--中国工商银行-->
    <table>
        <tr>
            <th>卡种</th>
            <th>所需条件</th>
            <th>单笔限额（元）</th>
            <th>每日限额（元）</th>
        </tr>
        <tr>
            <td rowspan="7">借记卡</td>
            <td>柜面注册，静态支付密码</td>
            <td>300</td>
            <td>300</td>
        </tr>
        <tr>
            <td>电子银行口令卡（未开通短信认证）</td>
            <td>500</td>
            <td>1,000</td>
        </tr>
        <tr>
            <td>电子银行口令卡（开通短信认证）</td>
            <td>2,000</td>
            <td>5,000</td>
        </tr>
        <tr>
            <td>电子密码器</td>
            <td>5万</td>
            <td>5万</td>
        </tr>
        <tr>
            <td>一代U盾（未开通短信认证）</td>
            <td>50万</td>
            <td>100万</td>
        </tr>
        <tr>
            <td>一代U盾（开通短信认证）</td>
            <td>100万</td>
            <td>100万</td>
        </tr>
        <tr>
            <td>二代U盾（含通用U盾）</td>
            <td>100万</td>
            <td>500万</td>
        </tr>
    </table>
    <p class="bank-note">以上限额仅供参考，以银行官方公告为准，若在银行设置的网上支付额度低于以上限额，以银行设置的为准， 中国工商银行客服热线：95588。</p>
<?php } elseif ('103' === $bid) { ?>
<!--中国农业银行-->
    <table>
        <tr>
            <th>卡种</th>
            <th>所需条件</th>
            <th>单笔限额（元）</th>
            <th>每日限额（元）</th>
        </tr>
        <tr>
            <td rowspan="3">借记卡</td>
            <td>网上银行IE证书+动态口令卡</td>
            <td>1,000</td>
            <td>3,000</td>
        </tr>
        <tr>
            <td>一代K宝</td>
            <td>50万</td>
            <td>100万</td>
        </tr>
        <tr>
            <td>二代K宝</td>
            <td>100万</td>
            <td>500万</td>
        </tr>
    </table>
    <p class="bank-note">以上限额仅供参考，以银行官方公告为准，若在银行设置的网上支付额度低于以上限额，以银行设置的为准， 中国农业银行客服热线：95599。</p>
<?php } elseif ('104' === $bid) { ?>
<!--中国银行-->
    <table>
        <tr>
            <th>卡种</th>
            <th>所需条件</th>
            <th>单笔限额（元）</th>
            <th>每日限额（元）</th>
        </tr>
        <tr>
            <td rowspan="3">借记卡</td>
            <td>手机交易码</td>
            <td>5,000</td>
            <td>5,000</td>
        </tr>
        <tr>
            <td>动态口令牌</td>
            <td>50万</td>
            <td>50万</td>
        </tr>
        <tr>
            <td>数字证书</td>
            <td>350万</td>
            <td>350万</td>
        </tr>
    </table>
    <p class="bank-note">以上限额仅供参考，以银行官方公告为准，若在银行设置的网上支付额度低于以上限额，以银行设置的为准， 中国银行客服热线：95566。</p>
<?php } elseif ('105' === $bid) { ?>
<!--中国建设银行-->
    <table>
        <tr>
            <th>卡种</th>
            <th>所需条件</th>
            <th>单笔限额（元）</th>
            <th>每日限额（元）</th>
        </tr>
        <tr>
            <td rowspan="7">借记卡</td>
            <td>动态口令卡</td>
            <td>5,000</td>
            <td>5,000</td>
        </tr>
        <tr>
            <td>短信动态口令</td>
            <td>5,000</td>
            <td>5,000</td>
        </tr>
        <tr>
            <td>文件证书+动态口令卡</td>
            <td>5,000</td>
            <td>5,000</td>
        </tr>
        <tr>
            <td>一代网银盾</td>
            <td>5万</td>
            <td>10万</td>
        </tr>
        <tr>
            <td>一代网银盾+动态口令卡</td>
            <td>50万</td>
            <td>50万</td>
        </tr>
        <tr>
            <td>一代网银盾+短信动态口令卡</td>
            <td>50万</td>
            <td>50万</td>
        </tr>
        <tr>
            <td>二代网银盾</td>
            <td>50万</td>
            <td>50万</td>
        </tr>
    </table>
    <p class="bank-note">以上限额仅供参考，以银行官方公告为准，若在银行设置的网上支付额度低于以上限额，以银行设置的为准， 中国建设银行客服热线：95533。</p>
<?php } elseif ('301' === $bid) { ?>
<!--交通银行-->
    <table>
        <tr>
            <th>卡种</th>
            <th>所需条件</th>
            <th>单笔限额（元）</th>
            <th>每日限额（元）</th>
            <th>银行规定最高限额</th>
        </tr>
        <tr>
            <td rowspan="2">借记卡</td>
            <td>手机动态密码</td>
            <td>5,000</td>
            <td>5,000</td>
            <td>5万</td>
        </tr>
        <tr>
            <td>U盾</td>
            <td>20万</td>
            <td>20万</td>
            <td>100万</td>
        </tr>
    </table>
    <p class="bank-note">以上限额仅供参考，以银行官方公告为准，若在银行设置的网上支付额度低于以上限额，以银行设置的为准， 交通银行客服热线：95559。</p>
<?php } elseif ('302' === $bid) { ?>
<!--中信银行-->
    <table>
        <tr>
            <th>卡种</th>
            <th>所需条件</th>
            <th>单笔限额（元）</th>
            <th>每日限额（元）</th>
        </tr>
        <tr>
            <td rowspan="4">借记卡</td>
            <td>文件证书</td>
            <td>1,000</td>
            <td>5,000</td>
        </tr>
        <tr>
            <td>无证书+动态口令</td>
            <td>1,000</td>
            <td>5,000</td>
        </tr>
        <tr>
            <td>文件证书+动态口令</td>
            <td>1万</td>
            <td>5万</td>
        </tr>
        <tr>
            <td>移动证书（USBKEY）</td>
            <td>无限额</td>
            <td>无限额</td>
        </tr>
    </table>
    <p class="bank-note">以上限额仅供参考，以银行官方公告为准，若在银行设置的网上支付额度低于以上限额，以银行设置的为准， 中信银行客服热线：95558。</p>
<?php } elseif ('303' === $bid) { ?>
<!--中国光大银行 参考支付宝-->
    <table>
        <tr>
            <th>卡种</th>
            <th>所需条件</th>
            <th>单笔限额（元）</th>
            <th>每日限额（元）</th>
        </tr>
        <tr>
            <td rowspan="3">借记卡</td>
            <td>手机动态密码</td>
            <td>1万</td>
            <td>1万</td>
        </tr>
        <tr>
            <td>阳光网盾</td>
            <td>50万</td>
            <td>50万</td>
        </tr>
        <tr>
            <td>阳光令牌</td>
            <td>100万</td>
            <td>100万</td>
        </tr>
    </table>
    <p class="bank-note">以上限额仅供参考，以银行官方公告为准，若在银行设置的网上支付额度低于以上限额，以银行设置的为准， 中国光大银行客服热线：95595。</p>
<?php } elseif ('304' === $bid) { ?>
<!--华夏银行 参考支付宝-->
    <table>
        <tr>
            <th>卡种</th>
            <th>所需条件</th>
            <th>单笔限额（元）</th>
            <th>每日限额（元）</th>
        </tr>
        <tr>
            <td rowspan="3">借记卡</td>
            <td>非签约客户</td>
            <td>300</td>
            <td>1,000</td>
        </tr>
        <tr>
            <td>证书/U-key</td>
            <td>5,000</td>
            <td>5,000</td>
        </tr>
        <tr>
            <td>电子钱包用户</td>
            <td>无限额</td>
            <td>无限额</td>
        </tr>
    </table>
    <p class="bank-note">以上限额仅供参考，以银行官方公告为准，若在银行设置的网上支付额度低于以上限额，以银行设置的为准， 华夏银行客服热线：95577。</p>
<?php } elseif ('305' === $bid) { ?>
<!--中国民生银行-->
    <table>
        <tr>
            <th>卡种</th>
            <th>所需条件</th>
            <th>单笔限额（元）</th>
            <th>每日限额（元）</th>
        </tr>
        <tr>
            <td rowspan="4">借记卡</td>
            <td>短信验证码</td>
            <td>5,000</td>
            <td>5,000</td>
        </tr>
        <tr>
            <td>浏览器证书</td>
            <td>5,000</td>
            <td>5,000</td>
        </tr>
        <tr>
            <td>动态令牌（OTP）</td>
            <td>50万</td>
            <td>50万</td>
        </tr>
        <tr>
            <td>U宝</td>
            <td>50万</td>
            <td>50万</td>
        </tr>
    </table>
    <p class="bank-note">以上限额仅供参考，以银行官方公告为准，若在银行设置的网上支付额度低于以上限额，以银行设置的为准， 中国民生银行客服热线：95568。</p>
<?php } elseif ('306' === $bid) { ?>
<!--广发银行-->
    <table>
        <tr>
            <th>卡种</th>
            <th>所需条件</th>
            <th>单笔限额（元）</th>
            <th>每日限额（元）</th>
        </tr>
        <tr>
            <td rowspan="5">借记卡</td>
            <td>卡密</td>
            <td>不限</td>
            <td>1,000</td>
        </tr>
        <tr>
            <td>短信动态验证码（通过网银设置）</td>
            <td>不限</td>
            <td>5,000</td>
        </tr>
        <tr>
            <td>短信动态验证码（通过柜面等渠道设置）</td>
            <td>不限</td>
            <td>2万</td>
        </tr>
        <tr>
            <td>Key令</td>
            <td>不限</td>
            <td>5万</td>
        </tr>
        <tr>
            <td>Key盾</td>
            <td>不限</td>
            <td>100万</td>
        </tr>
    </table>
    <p class="bank-note">以上限额仅供参考，以银行官方公告为准，若在银行设置的网上支付额度低于以上限额，以银行设置的为准， 广发银行客服热线：95508。</p>
<?php } elseif ('308' === $bid) { ?>
<!--招商银行 参考支付宝-->
    <table>
        <tr>
            <th>卡种</th>
            <th>所需条件</th>
            <th>单笔限额（元）</th>
            <th>每日限额（元）</th>
        </tr>
        <tr>
            <td rowspan="2">借记卡</td>
            <td>开通大众版网上支付功能</td>
            <td>500</td>
            <td>500</td>
        </tr>
        <tr>
            <td>开通专业版网上支付功能</td>
            <td>无限额</td>
            <td>无限额</td>
        </tr>
    </table>
    <p class="bank-note">以上限额仅供参考，以银行官方公告为准，若在银行设置的网上支付额度低于以上限额，以银行设置的为准， 招商银行客服热线：95555。</p>
<?php } elseif ('310' === $bid) { ?>
<!--浦发银行 参考支付宝-->
    <table>
        <tr>
            <th>卡种</th>
            <th>所需条件</th>
            <th>单笔限额（元）</th>
            <th>每日限额（元）</th>
        </tr>
        <tr>
            <td rowspan="2">借记卡</td>
            <td>动态密码版</td>
            <td>20万</td>
            <td>20万</td>
        </tr>
        <tr>
            <td>数字证书版</td>
            <td>自行设定</td>
            <td>自行设定</td>
        </tr>
    </table>
    <p class="bank-note">以上限额仅供参考，以银行官方公告为准，若在银行设置的网上支付额度低于以上限额，以银行设置的为准， 浦发银行客服热线：95528。</p>
<?php } ?>