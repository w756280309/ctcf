<?php
/**
 *立合同步接口
 * 注意项：
 *     1.密钥也分正式的与测试的，上线的时候想着填正式的密钥
 *     2.日志文件路径@app/console/runtime/logs/exchange/目录下，发生错误的时候才记录日志
 *
 * 使用方法：
 *    正常情况加入crontab定期执行 /usr/bin/php yii lihe/contract，此项是未传送过的发送
 *    特殊情况，单独执行一个资源包sn的传送，此接口支持参数 php yii lihe/contract LH-20171030-04-WD
 *
 * Created by PhpStorm.
 * User: ian
 * Date: 17-11-16
 * Time: 下午3:34
 */

namespace console\controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Yii;
use yii\console\Controller;

/**
 * 给立合同步数据的接口
 */
class LiheController extends Controller
{

    private $redis_succeed_max_id = 'LH_SUCCEED_MAX_ID';
    private $guzzle;
    private $redis;
    private $repair = false; //是否是单条修复
    private $conf = array();

    /**
     * 放款成功后,标的同步接口
     * @param $sn string 资源包SN号
     */
    public function actionContract($sn = '')
    {
        if (!empty($sn)) {
            $this->repair = true;
            $sql = 'SELECT *   
                    from online_fangkuan fk,online_product p 
                    where p.pkg_sn="' . $sn . '" 
                    and fk.`status`>=3 
                    and fk.online_product_id=p.id';
        } else {
            $maxId = $this->redis->get($this->redis_succeed_max_id);
            $maxId = empty($maxId) ? 0 : $maxId;
            $sql = 'SELECT *   
                    from online_fangkuan fk,online_product p 
                    where  fk.id >"' . $maxId . '" 
                    and fk.`status`>=3 
                    and fk.online_product_id=p.id 
                    AND LENGTH(p.pkg_sn)>0';
        }
        $this->deal($sql);
    }

    /**
     * 数据后续处理
     * @param $sql
     */
    private function deal($sql)
    {
        $fDatas = $this->getFangKuanData($sql);
        foreach ($fDatas as $data) {
            $pack = $this->packLiHeFormat($data);
            if ($this->postData($pack)) {
                $this->succeed($data);
            }
        }
    }

    /**
     * 同步成功处理
     * @param $data
     */
    private function succeed($data)
    {
        $maxId = $this->redis->get($this->redis_succeed_max_id);
        if ($data['id'] > $maxId) {
            $this->redis->set($this->redis_succeed_max_id, $data['id']);
        }
    }


    /**
     * 获取放款数据
     * @param $sql
     * @return mixed
     */
    private function getFangKuanData($sql)
    {
        $dbRead = Yii::$app->db_read;
        $data = $dbRead->createCommand($sql)->queryAll();
        return $data;
    }

    /**
     * 封装立合数据格式
     * @param array $fkData 放款数据
     * @return array 封装好的数组
     */
    private function packLiHeFormat($fkData)
    {

        //到期日 = 到期日 - 宽限期
        $finish_date = $fkData['finish_date'] - $fkData['kuanxianqi'] * 86400;
        //计算方式=还款方式
        $refund_method = ($fkData['refund_method'] + 1) * 10;
        $data = [
            'pid' => $fkData['id'],
            //标的主键id
            'name' => $fkData['title'] . '+' . $fkData['sn'],
            //	标的名+编号
            'amount' => $fkData['money'],
            //	标的融资金额，单位（分）
            'startDate' => $this->formatDate($fkData['jixi_time']),
            //	起息日，格式：2017-01-01
            'dueDate' => $this->formatDate($finish_date),
            //	到期日，格式：2017-01-01
            'gracePeriodDate' => $this->formatDate($fkData['finish_date']),
            //	宽限期截止日期，格式：2017-01-01
            'interestRate' => $fkData['yield_rate'],
            //	投资人利率，单位（%）<-->项目利率
            'actAmount' => $fkData['funded_money'],
            //	实际到账金额，单位（分）
            'actualAccountDate' => $this->formatDate($fkData['fk_examin_time']),
            //	实际到账日，格式：2017-01-01
            'headCollectRate' => 0.000000,
            //	前收平台服务费率，六位纯小数，例如千分之五：0.005000
            'headRateStatus' => 10,
            //	前收平台服务费率状态 10,按金额，20按年化
            'headCollectMoney' => 0,
            //	前收金额 ，单位（分）
            'laterCollectRate' => 0,
            //	后收平台服务费率，六位纯小数，例如千分之五：0.005000
            'laterCollectMoney' => 0,
            //	后收金额，单位（分）
            'lendTime' => $this->formatDate($fkData['created_at']),
            //	平台放款时间，格式：2017-01-01
            'lendMoney' => $fkData['order_money'],
            //	平台放款金额，单位（分）
            'interestType' => $refund_method,
            //	计息方式： 10到期本息（两头计息）；20 到期本息（计头不计尾）；30按月付息，到期本息；40 按季付息，到期本息；50按半年付息，到期本息；
            //60按年付息，到期本息；70按自然月付息，到期本息；80按自然季付息，到期本息；90按自然半年付息，到期本息；100按自然年付息，到期本息；110等额本息；120一次性收取
            'contractTitle' => '',
            //	合同标题
            'interestBenchmark' => 365,
            //	计息基准，单位（天），例如：360
            'addInterestRate' => empty($fkData['jiaxi']) ? 0 : $fkData['jiaxi'],
            //	加息利率（%）
        ];
        ksort($data);
        $sign = sha1(implode(',', $data) . $this->conf['key']);
        $package = [
            'pkg_id' => 0, //资产包ID
            'pkg_sn' => $fkData['pkg_sn'], //资产包sn
            'platformId' => 2, //资产系统分配的平台id 1旺财谷；2 温都金服
            'sign' => $sign, //签名参数，生成方式ksort（$post['data']），sha1(implode(',',$post['data']))	Y
            'data' => $data, //接收的标的信息参数（按照平台实际参数传参即可）
        ];
        return $package;
    }

    /**
     * 格式化日期
     * @param $date
     * @return false|string
     */
    private function formatDate($date)
    {
        return date('Y-m-d', $date);
    }

    /**
     * 发送数据
     * @param $data array 封装后的数据
     * @return boolean true则记录同步记录的ID号，false则不记录
     */
    private function postData($data)
    {
        try {
            $r = $this->guzzle->request('POST', $this->conf['url'], ['form_params' => $data]);
            if ($r->getStatusCode() == 200) { //网络正常
                $json = json_decode($r->getBody(), true);
                if ($json['status'] == 0) { //数据不对
                    Yii::info(json_encode($json) . ' ' . json_encode($data), 'lihe');
                }
            } else {//网络不正常
                Yii::info(json_encode($r->getReasonPhrase()) . ' ' . json_encode($data), 'lihe');
            }
            if ($this->repair) {
                return false;
            } else {
                return true;
            }//正确与错误都记录最大ID
        } catch (RequestException $e) {
            Yii::info(json_encode($e->getResponse()) . ' ' . $e->getMessage() . ' ' . json_encode($data), 'lihe');
        }
        return true;
    }


    /**
     * LiheController constructor.
     */
    public function init()
    {
        $this->conf = Yii::$app->params['li_he'];
        if (!$this->conf) {
            throw new \Exception('缺少核心参数1');
        }
        if (!isset($this->conf['url']) || !isset($this->conf['key'])) {
            throw new \Exception('缺少核心参数2');
        }
        $this->guzzle = new Client([
            'base_uri' => $this->conf['url'],
            'timeout' => 3.0,
        ]);
        $this->redis = Yii::$app->redis;
    }
}
