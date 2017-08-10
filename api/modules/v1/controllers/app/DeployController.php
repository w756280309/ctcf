<?php

namespace api\modules\v1\controllers\app;

use api\modules\v1\controllers\Controller;

/**
 * App相关api接口.
 */
class DeployController extends Controller
{
    /**
     * 版本更新信息.
     */
    public function actionAppver($version, $clienttype)
    {
        $versionCode = (int) $version;
        $clienttype = strtolower($clienttype);

        if (empty($version) || !in_array($clienttype, ['ios', 'android'])) {
            return [
                'status' => 'fail', //程序级别成功失败
                'message' => '参数错误',
                'data' => null,
            ];
        }

        $content = [
            'old_updatetype' => 1,
            'updatedesc' => null,  //新版本更新说明
            'versioncode' => null,   //新版本版本号
            'versionname' => null, //新版本版本名称
            'downloadurl' => null,  //新版本下载地址
        ];

        if ('ios' === $clienttype) {
            if ($versionCode < 8) {
                $content = [
                    'old_updatetype' => 2,  //旧版本更新类型 0:无更新;1:有更新,无提示;2:有更新,有提示;3:有更新,强制更新
                    'updatedesc' => '增加代金券多选功能，每次投资可选多张优惠券；修复bug',  //新版本更新说明
                    'versioncode' => '8',   //新版本版本号
                    'versionname' => '1.6', //新版本版本名称
                    'downloadurl' => 'https://itunes.apple.com/us/app/wen-dou-jin-fu/id1107540109?mt=8',   //新版本下载地址
                ];
            }
        } else {
            if ($versionCode < 10) {
                $content = [
                    'old_updatetype' => 3,  //旧版本更新类型 0:无更新;1:有更新,无提示;2:有更新,有提示;3:有更新,强制更新
                    'updatedesc' => '账户功能增加安全性，优化产品部分交互体验',  //新版本更新说明
                    'versioncode' => '10',   //新版本版本号
                    'versionname' => '1.6.2', //新版本版本名称
                    'downloadurl' => 'https://dapp.wenjf.com/wjf_v1.6.2.apk',   //新版本下载地址, android 不支持 http 向 https 的跳转，协议必须严格匹配
                ];
            }
        }

        return [
            'status' => 'success', //程序级别成功失败
            'message' => '成功',
            'data' => [
                'result' => 'success', //业务级别成功失败
                'msg' => '成功',
                'content' => $content,
            ],
        ];
    }
}
