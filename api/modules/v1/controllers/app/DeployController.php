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
        $platCode = \Yii::$app->params['plat_code'];
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

        if ($platCode == 'WDJF') {
            if ('ios' === $clienttype) {
                if ($versionCode < 21) {
                    $content = [
                        'old_updatetype' => 2,  //旧版本更新类型 0:无更新;1:有更新,无提示;2:有更新,有提示;3:有更新,强制更新
                        'updatedesc' => '优化页面显示效果，优化了交互体验',  //新版本更新说明
                        'versioncode' => '21',   //新版本版本号
                        'versionname' => '2.1', //新版本版本名称
                        'downloadurl' => 'https://itunes.apple.com/us/app/wen-dou-jin-fu/id1107540109?mt=8',   //新版本下载地址
                    ];
                }
            } else {
                if ($versionCode < 24) {
                    $content = [
                        'old_updatetype' => 3,  //旧版本更新类型 0:无更新;1:有更新,无提示;2:有更新,有提示;3:有更新,强制更新
                        'updatedesc' => '理财界面优化，使用更流畅',  //新版本更新说明
                        'versioncode' => '24',   //新版本版本号
                        'versionname' => '2.3.2', //新版本版本名称
                        'downloadurl' => 'https://dapp.wenjf.com/wjf_v2.3.2.apk',   //新版本下载地址, android 不支持 http 向 https 的跳转，协议必须严格匹配
                    ];
                }
            }
        } else {
            if ('ios' === $clienttype) {
                if ($versionCode < 5) {
                    $content = [
                        'old_updatetype' => 2,  //旧版本更新类型 0:无更新;1:有更新,无提示;2:有更新,有提示;3:有更新,强制更新
                        'updatedesc' => '修复已知问题，优化体验',  //新版本更新说明
                        'versioncode' => '5',   //新版本版本号
                        'versionname' => '2.4', //新版本版本名称
                        'downloadurl' => 'https://itunes.apple.com/cn/app/楚天财富/id1290079669?l=zh&ls=1&mt=8',   //新版本下载地址
                    ];
                }
            } else {
                if ($versionCode < 5) {
                    $content = [
                        'old_updatetype' => 2,  //旧版本更新类型 0:无更新;1:有更新,无提示;2:有更新,有提示;3:有更新,强制更新
                        'updatedesc' => '修复已知问题，优化体验',  //新版本更新说明
                        'versioncode' => '5',   //新版本版本号
                        'versionname' => '2.4', //新版本版本名称
                        'downloadurl' => 'https://dapp.hbctcf.com/ctcf_v2.4.apk',   //新版本下载地址, android 不支持 http 向 https 的跳转，协议必须严格匹配
                    ];
                }
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
