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
                "url" => "https://m.wenjf.com/#t=1"
            ],
            [
                "name" => "新人注册",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "注册领豪礼",
                        "url" => "http://m.wenjf.com/luodiye/v2"
                    ],
                    [
                        "type" => "view",
                        "name" => "绑定微信",
                        "url" => "https://m.wenjf.com/user/wechat/bind"
                    ],
                ],
            ],
            [
                "name" => "关于我们",
                "sub_button" => [
                    [
                        "type" => "click",
                        "name" => "公司介绍",
                        "key" => "COMPANYINTRODUC"
                    ],
                    [
                        "type" => "click",
                        "name" => "意见反馈",
                        "key" => "OPINIONFEEDBACK"
                    ],
                    [
                        "type" => "click",
                        "name" => "联系我们",
                        "key" => "CONTACTUS"
                    ],
                    [
                        "type" => "view",
                        "name" => "帮助中心",
                        "url" => "https://m.wenjf.com/site/help"
                    ],
                    [
                        "type" => "view",
                        "name" => "APP下载",
                        "url" => "http://a.app.qq.com/o/simple.jsp?pkgname=com.wz.wenjf"
                    ]
                ],
            ],
        ];
    }
}