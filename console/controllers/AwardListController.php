<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use Doctrine\Common\Cache\RedisCache;

class AwardListController extends Controller
{
    /**
     * 每天上午10点跑这个脚本创建当天要抽的奖品
     */
    public function actionCaward()
    {
        $date = date('Ymd', time());
        $key = 'promo_queue201811';
        $sql = 'select id from promo_queue201811 where promodate='.$date.'  and  isvalid=1 and userid is null';
        $arrIds = Yii::$app->db->createCommand($sql)->queryColumn();
        if(empty($arrIds)){
            $file = Yii::getAlias('@app/runtime/logs/20181111_caward.txt');
            file_put_contents($file, date('Y-m-d H:i:s') . "| sql语句:".$sql, FILE_APPEND);
            return false;
        }

        $redis = new \Redis();
        $params = Yii::$app->params['redis_config'];
        $redis->connect($params['hostname'], $params['port']);
        if ($params['password']) {
            $redis->auth($params['password']);
        }
        $redis->select(1);
        //先清除用户获奖列表
        $userAwardR = $redis->keys('saveAward*');
        $delU = 0;
        if(!empty($userAwardR)){
            foreach ($userAwardR as $k=> $v){
                $delU ++;
                $redis->del($v);
            }
        }
        $i = 0;
        foreach ($arrIds as $k=> $v){
            if(!empty($redis->exists($key.':'.$v))){
                continue;
            }
            $i ++;
            $redis->set($key.':'.$v, $v);
            $redis->expire($key.':'.$v, 13*60*60);
        }
        $this->stdout('共生成'.$i.'个成功'.PHP_EOL.'获奖列表清除'.$delU.'个');
    }

    public function actionAset(){
        $redis = new \Redis();
        $params = Yii::$app->params['redis_config'];
        $redis->connect($params['hostname'], $params['port']);
        if ($params['password']) {
            $redis->auth($params['password']);
        }
        $redis->select(1);

        $redis->set('yu1', 8888);
    }

    public function actionAget(){
        $redis = new \Redis();
        $params = Yii::$app->params['redis_config'];
        $redis->connect($params['hostname'], $params['port']);
        if ($params['password']) {
            $redis->auth($params['password']);
        }

        $a = $redis->get('yu1');
        print_r($a);exit;
    }

}