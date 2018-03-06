<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-3-5
 * Time: 下午1:42
 */
namespace backend\modules\wechat\controllers;

use backend\controllers\BaseController;
use Yii;
/**
 * Class WechatMenuController
 * @package backend\modules\wechat\controllers
 * 用于编辑公众号菜单
 * 公众号菜单只能删除后重新添加
 */
class WechatMenuController extends BaseController
{
    public function actionIndex()
    {
        $data = Yii::$app->request->post();
        $errMessage = null;
        if (!empty($data)) {
            $list = json_decode(trim($data['content']));
            if (!empty($list)) {
                $res = self::delMenu();
                if ($res['code'] == 0) {
                    $errMessage = self::addMenu($list);
                } else {
                    $errMessage = $res['message'];
                }
            }
        }
        return $this->render('index', ['errMessage' => $errMessage]);
    }

    //删除公众号菜单，编辑菜单需要删除原有菜单
    public static function delMenu()
    {
        $app = Yii::$container->get('weixin_wdjf');
        $menu = $app->menu;
        $res = $menu->destroy();
        if ($res->errcode == 0) {
            return [
                'code' => 0,
                'message' => '删除成功',
                ];
        } else {
            Yii::info('删除公众号菜单失败，原因：code:' . $res->errcode . '，message:' .$res->errmsg);
            return [
                'code' => 1,
                'message' => '删除公众号菜单失败，原因：code:' . $res->errcode . '，message:' .$res->errmsg,
                ];
        }
    }

    //编辑公众号菜单
    public static function addMenu($list)
    {
        $app = Yii::$container->get('weixin_wdjf');
        $menu = $app->menu;
        $res = $menu->add($list);
        if ($res->errcode == 0) {
            return '公众号菜单编辑成功';
        } else {
            return '公众号菜单编辑失败，错误原因：code:' . $res->errcode . '，message:' .$res->errmsg;
        }
    }
}