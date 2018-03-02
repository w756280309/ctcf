<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

/**
 * 微信公众号菜单
 * ＠author ZouJianShuang
 */
class WechatMenuController extends Controller
{
    /**
     * 删除公众号菜单
     * 注：每次编辑菜单的时候需要删除旧菜单，重新创建
     * 如果失败信息code = '40001'，则需要传$reflash = true
     */
    public function actionMenuDel($reflash = false)
    {
        $app = Yii::$container->get('weixin_wdjf');
        $menu = $app->menu;

        $res = $menu->destroy();
        if ($res->errcode == 0) {
            echo '删除菜单成功';
        } else {
            echo '创建菜单失败，原因：code:' . $res->errcode . '，message:' .$res->errmsg;
        }
    }

    /**
     * 新建菜单
     * 如果失败信息code = '40001'，则需要传$reflash = true
     */
    public function actionMenuAdd($reflash = false)
    {
        $app = Yii::$container->get('weixin_wdjf');
        $menu = $app->menu;

        $res = $menu->add(self::menuList());

        if ($res->errcode == 0) {
            echo '创建菜单成功';
        } else {
            echo '创建菜单失败，原因：code:' . $res->errcode . '，message:' .$res->errmsg;
        }

    }

    /**
     * 菜单内容
     * 注：新建菜单只需改本代码即可
     */
    private function menuList()
    {
        return [
            [
                "type" => "view",
                "name" => "我要理财",
                "url" => "https://m.hbctcf.com#t=wx"
            ],
            [
                "name" => "福利活动",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "注册领红包",
                        "url" => "https://m.hbctcf.com/ctcf/landing/xs-invest#t=wx"
                    ],
                    [
                        "type" => "view",
                        "name" => "160元超市卡",
                        "url" => "https://m.hbctcf.com/ctcf/landing/xs-invest#t=wx"
                    ],
                    [
                        "type" => "view",
                        "name" => "每日签到",
                        "url" => "https://m.hbctcf.com/user/checkin#t=wx"
                    ],
                    [
                        "type" => "view",
                        "name" => "账户绑定",
                        "url" => "https://m.hbctcf.com/user/wechat/bind#t=wx"
                    ],
                ],
            ],
            [
                "name" => "关于我们",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "公司介绍",
                        "url" => "https://m.hbctcf.com/site/h5?wx_share_key=h5#t=wx"
                    ],
                    [
                        "type" => "view",
                        "name" => "在线客服",
                        "url" => "https://hbctcf.udesk.cn/im_client/?web_plugin_id=42843#t=wx"
                    ],
                    [
                        "type" => "click",
                        "name" => "联系我们",
                        "key" => "CONTACTUS"
                    ],
                    [
                        "type" => "view",
                        "name" => "帮助中心",
                        "url" => "https://m.hbctcf.com/site/help#t=wx"
                    ],
                    [
                        "type" => "view",
                        "name" => "APP下载",
                        "url" => "http://a.app.qq.com/o/simple.jsp?pkgname=com.hb.ctcf#t=wx"
                    ]
                ],
            ],
        ];
    }
}
