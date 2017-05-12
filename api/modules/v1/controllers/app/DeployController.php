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
            if ($versionCode < 7) {
                $content = [
                    'old_updatetype' => 2,  //旧版本更新类型
                    'updatedesc' => '庆祝温都金服1周年；修复兑换码复制问题',  //新版本更新说明
                    'versioncode' => '7',   //新版本版本号
                    'versionname' => '1.5.1', //新版本版本名称
                    'downloadurl' => 'https://itunes.apple.com/us/app/wen-dou-jin-fu/id1107540109?mt=8',   //新版本下载地址
                ];
            }
        } else {
            if ($versionCode < 7) {
                $content = [
                    'old_updatetype' => 2,  //旧版本更新类型
                    'updatedesc' => '庆祝温都金服1周年；修复兑换码复制问题',  //新版本更新说明
                    'versioncode' => '7',   //新版本版本号
                    'versionname' => '1.5.1', //新版本版本名称
                    'downloadurl' => 'http://dapp.wenjf.com/wjf_v1.5.1.apk',   //新版本下载地址
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
