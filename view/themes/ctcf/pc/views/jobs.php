<?php

$this->title = '加入我们';

$this->registerCssFile(ASSETS_BASE_URI.'css/news/informationlist.css?v=20160818', ['depends' => 'frontend\assets\FrontAsset']);

?>
<div class="wdjf-body">
    <div class="wdjf-ucenter clearfix">
        <div class="leftmenu">
            <?= $this->render('@frontend/views/news/left_nav.php') ?>
        </div>
        <div class="rightcontent">
            <div class="information-box">
                <div class="information-header">
                    <span class="information-header-font">招聘岗位</span>
                </div>
                <table class="gangwei">
                    <tr class="tr-first">
                        <td width="12%">招聘岗位</td>
                        <td width="12%">年龄</td>
                        <td width="12%">学历</td>
                        <td>任职要求</td>
                    </tr>
                    <tr>
                        <td>理财顾问</td>
                        <td>35周岁以下</td>
                        <td>大专及以上</td>
                        <td class="td-last">
                            <p class="p-first">1、金融、经济营销管理类专业毕业，专科及以上学历</p>
                            <p>2、2年以上金融行业工作经验，有高净值客户服务工作经验</p>
                            <p>3、具备良好的大客户开拓与维护能力</p>
                            <p class="p-last">4、拥有高端客户、渠道、同业资源者可酌情优先考虑</p>
                        </td>
                    </tr>
                    <tr>
                        <td>文案策划</td>
                        <td>35周岁以下</td>
                        <td>本科及以上</td>
                        <td class="td-last">
                            <p class="p-first">1、具备良好的策划能力、资源整合能力、熟悉金融及市场特性</p>
                            <p>2、本科学历且有2年以上策划工作经验</p>
                            <p class="p-last">3、有微信公众号运营经验者优先</p>
                        </td>
                    </tr>
                    <tr>
                        <td>平面设计师</td>
                        <td>35周岁以下</td>
                        <td>本科及以上</td>
                        <td class="td-last">
                            <p class="p-first">1、平面设计、广告学、美术设计或相关专业，擅长运用PS做平面图、海报及动态图，熟练运用cdr做排版应用于实物印刷</p>
                            <p>2、具备2年以上平面设计工作经验</p>
                            <p>3、能独立完成设计任务，设计富有表现力</p>
                            <p>4、工作有责任心，具备一定的文案策划能力</p>
                            <p class="p-last">5、有微信公众号编辑经验者优先</p>
                        </td>
                    </tr>
                    <tr>
                        <td>客服门店专员</td>
                        <td>28周岁以下</td>
                        <td>本科及以上</td>
                        <td class="td-last">
                            <p class="p-first">1、金融、经济营销管理类专业毕业，本科及以上学历</p>
                            <p>2、2年以上金融行业工作经验，有高净值客户服务工作经验者优先</p>
                            <p class="p-last">3、具备良好的客户开拓与维护能力</p>
                        </td>
                    </tr>
                    <tr>
                        <td>客服微电专员</td>
                        <td>28周岁以下</td>
                        <td>本科及以上</td>
                        <td class="td-last">
                            <p class="p-first">1、金融、经济营销管理类专业毕业，本科及以上学历</p>
                            <p>2、2年以上金融行业工作经验，有高净值客户服务工作经验者优先</p>
                            <p class="p-last">3、具备良好的客户开拓与维护能力</p>
                        </td>
                    </tr>
                    <tr>
                        <td>高级运营经理</td>
                        <td>30周岁以下</td>
                        <td>本科及以上</td>
                        <td class="td-last">
                            <p class="p-first">1、有3年以上的互联网运营经验，熟悉toC运营</p>
                            <p>2、具备一定的文案设计、活动策划能力</p>
                            <p>3、具备良好的沟通技巧、组织协调能力</p>
                            <p class="p-last">4、有互联网金融运营工作经验者优先</p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
