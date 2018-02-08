<?php

$this->title = "帮助中心";
$this->registerJsFile(ASSETS_BASE_URI.'js/help/operation.js', ['depends' => 'frontend\assets\FrontAsset']);

use common\utils\StringUtils;

?>

<div class="wdjf-body">
    <div class="wdjf-ucenter clearfix">
        <div class="leftmenu">
            <?= $this->render('@frontend/views/helpcenter/left_nav.php', ['type'=>$type]) ?>
        </div>
        <div class="rightcontent">
            <!-- 主体 -->
            <div class="help-box">
                <div class="about-header">
                    <span class="about-header-font">操作篇</span>
                </div>
                <p class="help-header"><span>——————</span>&nbsp;&nbsp;注册登录&nbsp;&nbsp;<span>——————</span></p>
                <div class="kong-width">
                    <p class="single-tuchu">1、设置登录密码有什么要求？
                    </p>
                    <p>a. 6-20个数字+英文字符；
                    </p>
                    <p>b. 只能包含字母、数字以及标点符号（除空格）；
                    </p>
                    <p>c. 字母、数字以及标点符号至少包含2种。

                    </p>
                    <p class="single-tuchu">2、对注册时填写的手机号码有什么要求？
                    </p>
                    <p>用户注册时使用的手机号码必须是大陆地区电信运营商支持的号段，且不支持小灵通。

                    </p>
                    <p class="single-tuchu">3、注册成功后可以更换手机号码吗？
                    </p>
                    <p>作为用户在楚天财富平台和第三方资金托管账户重要的识别信息，手机号码无法进行变更。请用户妥善保管注册时使用的手机号码。

                    </p>
                    <p class="single-tuchu">4、注册个人用户时需要进行实名认证吗？
                    </p>
                    <p>注册楚天财富账户时不需要对用户进行实名认证。但是当用户开通第三方资金托管平台（联动优势）账户时，需对用户进行实名身份认证，用户真实身份在该平台一经核实，不能修改。

                    </p>
                    <p class="single-tuchu">5、如何更改登录密码？
                    </p>
                    <p>登录楚天财富账户，用户进入【账户】-&gt;【设置】-&gt;【安全中心】-&gt;点击修改登录密码，按相关提示设置新密码即可。

                    </p>
                    <p class="single-tuchu">6、忘记登录密码怎么办？
                    </p>
                    <p>网站提供助自助找回密码，请用户在登录页面点击“忘记密码”按钮，按照页面提示操作重新设置新的登录密码。

                    </p>
                    <p class="single-tuchu">7、账户被锁定怎么办？
                    </p>
                    <p>用户连续3次输入错误支付密码会造成联动优势的账户被锁定，锁定期24小时，涉及支付密码的操作将受影响，24小时之后即可继续操作。 如遇连续2次密码输入错误，建议您采用密码找回，修改您的密码以完成您的交易。

                    </p>
                    <p class="single-tuchu">8、同一用户能否在平台上绑定多个账号？
                    </p>
                    <p>每位用户只能在平台上绑定一个账号，平台实名认证，每个身份证号码对应一个用户。
                    </p>
                </div>
                <p class="help-header"><span>——————</span>&nbsp;&nbsp;开通资金托管&nbsp;&nbsp;<span>——————</span></p>
                <div class="kong-width">
                    <p class="single-tuchu">1、什么是资金托管？
                    </p>
                    <p>资金托管是将用户资金委托给第三方机构代为存管的一种资金管理方式。

                    </p>
                    <p class="single-tuchu">2、为什么要开通资金托管账户？
                    </p>
                    <p>楚天财富接入了联动优势电子商务有限公司的资金托管系统，实现了平台对个人及企业用户的账户进行独立管理，交易仅限用户本人操作，资金安全有保障。

                    </p>
                    <p class="single-tuchu">3、如何开通资金托管账户？
                    </p>
                    <p>用户完成注册后，点击相应页面进行充值，系统将自动引导开通资金托管账户，根据提示完成相应操作即可。

                    </p>
                    <p class="single-tuchu">4、如何管理资金托管支付密码？
                    </p>
                    <p>在首次开通托管账户时，联动优势会以短信的形式将初始密码发送到开户人绑定的手机。

                    </p>
                    <p class="single-tuchu">5、如何更改资金托管支付密码？
                    </p>
                    <p>用户可以编辑短信“GGMM#原密码#新密码”发送给联动优势来更改密码。例如编辑发送短信“GGMM#123456#234567”，交易密码只能是6位数字。联动优势短信号码：移动、联通、电信用户编辑短信发送至10690569687

                    </p>
                    <p class="single-tuchu">6、忘记资金托管支付密码如何重置？
                    </p>
                    <p>用户可以通过网站重置支付密码。用户进入【账户】-&gt;【设置】-&gt;【安全中心】-&gt;【修改交易密码】，点击“重置支付密码”，系统会自动为您重置联动优势托管账户密码，并通过短信发送到您手机。
                    </p>
                    <p>用户也可以编辑短信“CSMM#身份证后四位”发送给联动优势来重置密码。例如身份证号是110*******11055425，编辑发送短信“CSMM#5425”。（
                        请注意：CSMM不区分大小写，但是大小写要求一致；用户身份证最后一位为字母X的情况，用户编辑短信最后一位字母大小写必须与注册时相符，否则无法重置成功。）
                    </p>
                    <p>联动优势短信号码：移动、联通、电信用户编辑短信发送至10690569687

                    </p>
                    <p class="single-tuchu">7、资金托管收费吗？
                    </p>
                    <p>开通资金托管账户不收取费用。
                    </p>
                </div>
                <p class="help-header"><span>——————</span>&nbsp;&nbsp;绑卡充值&nbsp;&nbsp;<span>——————</span></p>
                <div class="kong-width">
                    <p class="single-tuchu">1、可以绑定哪些银行卡？
                    </p>
                    <p>可以绑定工商银行、农业银行、建设银行、中国银行、浦发银行、交通银行、民生银行、广发银行、中信银行、光大银行、兴业银行、平安银行等银行卡。
                    </p>
                    <p class="single-tuchu">2、如何绑定银行卡？
                    </p>
                    <p>用户登录后，进入【账户】-&gt;【提现】或【充值】，根据操作提示，选择绑定的银行卡类型完成银行卡设置。

                    </p>
                    <p class="single-tuchu">3、可以使用哪些银行卡充值？充值的限额是多少？
                    </p>
                    <p class="single-title">（1）网银充值：
                    </p>
                    <p>平台支持通过国内各大银行借记卡网银向自己账户充值，以下银行可供选择：
                    </p>
                    <?php if ($ebank) { ?>
                    <table>
                        <tbody>
                        <?php for ($key = 0; $key < count($ebank); $key+=3) { ?>
                        <tr>
                            <td><?= empty($ebank[$key]['bankName']) ? "" : $ebank[$key]['bankName'] ?></td>
                            <td><?= empty($ebank[$key+1]['bankName']) ? "" : $ebank[$key+1]['bankName'] ?></td>
                            <td><?= empty($ebank[$key+2]['bankName']) ? "" : $ebank[$key+2]['bankName'] ?></td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php } ?>
                    <p>充值限额由用户银行卡网银额度限制而定，请注意您在开通网银时设置的限额；本平台提供的各银行网银充值限额仅供参考，以银行官方公告为准，如疑详询银行客服热线。

                    </p>
                    <?php if ($qpay) { ?>
                    <p class="single-title">（2）快捷充值：
                    </p>
                    <p>快捷充值支持的银行卡名称及对应限额如下：
                    </p>
                    <table>
                        <tbody>
                        <tr>
                            <td><b>银行名称</b></td>
                            <td><b>风控限额（单笔）</b></td>
                            <td><b>风控限额（单日）</b></td>
                        </tr>
                        <?php foreach ($qpay as $val) { ?>
                            <tr>
                                <td><?= $val['bankName'] ?></td>
                                <td><?= StringUtils::amountFormat1('{amount}{unit}', $val['singleLimit']) ?></td>
                                <td><?= StringUtils::amountFormat1('{amount}{unit}', $val['dailyLimit']) ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php } ?>

                    <p class="single-tuchu">4、是否可以用信用卡充值？
                    </p>
                    <p>楚天财富平台不支持使用信用卡充值，只能使用储蓄卡。
                    </p>
                    <p class="single-tuchu">5、如何更换绑定的银行卡？
                    </p>
                    <p>用户登录后，进入“我的账户”－&gt;“安全中心”－&gt;“我的银行卡”－&gt;点击“更换银行卡”，在换卡申请页面，根据“换卡提醒”进行相关操作，完成更换银行卡的申请。在办理换卡过程中，如有疑问请拨打客服热线<?= \Yii::$app->params['platform_info.contact_tel'] ?>。
                    </p>
                    <p class="single-tuchu">6、换卡申请提交后，多久可以换卡成功？
                    </p>
                    <p>若您的账户余额为0且没有在途资金，系统将在一小时以内自动审核换卡；否则需拨打客服热线<?= \Yii::$app->params['platform_info.contact_tel'] ?>提交相关资料，提交资料后，进行2-5天的人工审核换卡；在途资金指有投资回款或提现冻结金额。换卡申请期间不影响充值和提现。
                    </p>
                    <p class="single-tuchu">7、换卡申请都需要提交哪些资料？
                    </p>
                    <p>（1）用户手持身份证照片正反面，显示本人脸和手臂，图片应能看清身份证号、人像；
                    </p>
                    <p>（2）用户手持原卡、新卡正反面照片，显示本人脸和手臂，图片应能看清银行卡卡号；
                    </p>
                    <p>（3）若原卡已丢失的，出示原卡挂失证明；若原卡已丢失的，且无法出具原卡挂失证明的，提供原卡的开户证明或银行开具的一个月内交易流水并加盖银行公章；
                    </p>
                    <p>（4）无新卡图片的，提供新卡开户单子或新卡与身份证关联关系证明（银行出具）。
                    </p>
                </div>
                <p class="help-header"><span>——————</span>&nbsp;&nbsp;投资/提现&nbsp;&nbsp;<span>——————</span></p>
                <div class="kong-width">
                    <p class="single-tuchu">1、理财产品的计息日和还款日如何计算？
                    </p>
                    <p>用户所投资的产品在募集完成后，产品状态变化为“收益中”当天即为计息日。具体以产品发布页面、相关合同为准。
                    </p>
                    <p class="single-tuchu">2、产品出现逾期，本息由谁来保障？
                    </p>
                    <p>产品出现逾期时，将由推荐产品的保障方按照保障措施相应条款规定，向投资者支付本金与约定收益。

                    <p class="single-tuchu">3、投资金额有什么限制吗？
                    </p>
                    <p>用户的投资金额需要满足项目的起投金额和递增金额，并且不能使剩余可投金额小于一倍起投金额。
                    </p>

                    <p class="single-tuchu">4、如何申请转让？
                    </p>
                    <p>进入楚天财富“账户中心－我的转让－可转让的项目”，进行转让操作；转让方在转让时，可以进行折价处理，折价比例在0%～3%之间，但折让后价格不可低于转让金额。
                    </p>

                    <p class="single-tuchu">5、什么时候可以进行转让？
                    </p>
                    <p>已购项目持有30天后即可进行转让；持有天数从计息日开始计算；对于发行人不能提前还款类的产品，付息日和还款日前3天不可转让。对于发行人可提前还款类的产品，付息日前3天不可转让，还款日前13天不可转让。
                    </p>

                    <p class="single-tuchu">6、转让的转让周期？
                    </p>
                    <p>从发布转让成功开始计算，周期时间为3天，即72小时，如果到72小时未转让完成，未转让部分会自动撤销，仍在“可转让列表”中，可继续申请转让；已转让部分可在“已转让列表”中查看。
                    </p>

                    <p class="single-tuchu">7、发起转让收取多少手续费？
                    </p>
                    <p>转让方需要支付转让金额的3‰手续费，在成交后直接从成交金额中扣除，不成交平台不向用户收取手续费。
                    </p>

                    <p class="single-tuchu">8、发布转让后可否撤销？
                    </p>
                    <p>已发布的转让如需撤销，可在“账户中心－我的转让－转让中的项目”进行撤销操作，已转让成功的部分不可撤回；撤销成功后，剩余部分仍在“可转让列表”中，可继续申请转让；已转让部分可在“已转让列表”中查看。
                    </p>

                    <p class="single-tuchu">9、如何提现？
                    </p>
                    <p>用户登录后，进入【我的账户】-&gt;【提现】-&gt;点击“提现”按钮，按照提示完成提现操作。

                    </p>
                    <p class="single-tuchu">10、提现是否有限额？
                    </p>
                    <p>提现每日最高100万，超过100万需要临时提升限额或分多日提现，如需提升限额请联系客服，电话<?= Yii::$app->params['platform_info.contact_tel'] ?>。

                    </p>
                    <p class="single-tuchu">11、提现多久可以到账？
                    </p>
                    <p>工作日内17:00之前申请提现，当日到账；
                    </p>
                    <p>17:00之后申请提现，会在下一个工作日到账。
                    </p>
                    <p>如遇双休日或法定节假日顺延。

                    </p>
                    <p class="single-tuchu">12、提现手续费怎么收取？
                    </p>
                    <p>每人每月有5次免费发起提现申请的机会，超过5次按2元每笔收取，此为第三方资金托管平台联动优势收取。
                    </p>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
