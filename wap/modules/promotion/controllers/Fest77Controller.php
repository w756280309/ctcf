<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\adv\Share;
use common\view\ContainerCalcPositionHelper;
use common\view\IntervensionHelper;
use Intervention\Image\ImageManagerStatic as Image;
use Yii;
use yii\web\Controller;

class Fest77Controller extends Controller
{
    use HelpersTrait;
    public $layout = '@app/views/layouts/fe';

    public function actionIndex()
    {
        $isWx = $this->fromWx();

        return $this->render('index', [
            'isWx' => $isWx,
        ]);
    }

    public function actionResult($xcode)
    {
        if (!$this->fromWx()) {
            return $this->redirect('index');
        }

        return $this->render('result', [
            'xcode' => $xcode,
            'name' => Yii::$app->session->get('resourceOwnerNickName'),
        ]);
    }

    public function actionNoCode($xcode, $name)
    {
        $imagePath = 'images/fest77/no_code.jpg';
        $this->showImage(Image::make($imagePath), $this->getWordsConfig($xcode), $name);
    }

    public function actionWithCode($xcode, $name)
    {
        $imagePath = 'images/fest77/with_code.jpg';
        $this->showImage(Image::make($imagePath), $this->getWordsConfig($xcode), $name);
    }

    private function showImage($image, $wordsConfig, $name)
    {
        $imageHelper =  new IntervensionHelper($image);
        $imageHelper->column($wordsConfig['name'], 8, 430, 7, $this->getFontConfig('left'));
        $imageHelper->column($name.'的月老签', 522, 44, 5, $this->getFontConfig('right'));
        $calcHelper = new ContainerCalcPositionHelper($image->width(), $image->height());
        $words = $wordsConfig['content'];
        $positions = $calcHelper->calcFirstWordXY(count($words), $this->getWordMaxCount($words), 50, 64, 18);
        if (!empty($positions)) {
            foreach ($words as $k => $word) {
                $imageHelper->column($word, $positions[$k]['x'], $positions[$k]['y'], 18, $this->getFontConfig('middle'));
            }
        }
        header("Content-Type: ".$image->mime());
        echo $image->encode('jpg');
        exit;
    }

    private function getWordMaxCount(array $words)
    {
        $wordCount = 0;
        foreach ($words as $word) {
            $wCount = mb_strlen($word, 'UTF-8');
            if ($wCount > $wordCount) {
                $wordCount = $wCount;
            }
        }

        return $wordCount;
    }

    private function getFontConfig($key)
    {
        $config = [];
        if ('left' === $key) {
            $config['size'] = '74';
            $config['color'] = '#ebcac6';
            $config['file'] = 'font/HYJinChangTiJ.ttf';
        } elseif ('middle' === $key) {
            $config['size'] = '50';
            $config['color'] = '#000033';
            $config['file'] = 'font/middle.ttf';
        } elseif ('right' === $key) {
            $config['size'] = '22';
            $config['color'] = '#000000';
            $config['file'] = 'font/YeGenYouInStyle.ttf';
        }

        return $config;
    }

    private function getWordsConfig($code)
    {
        $poetry = [
            [
                'name' => '上上签',
                'content' => [
                    '半缘修道半缘君',
                    '取次花丛懒回顾',
                ],
            ],
            [
                'name' => '上上签',
                'content' => [
                    '神仙美眷也',
                    '佳偶天成',
                ],
            ],
            [
                'name' => '上上签',
                'content' => [
                    '与子偕老',
                    '执子之手',
                ],
            ],
            [
                'name' => '上上签',
                'content' => [
                    '见此良人',
                    '今夕何夕',
                ],
            ],
            [
                'name' => '上上签',
                'content' => [
                    '只凭纤手，暗抛红豆',
                    '忆昔花间相见后',
                ],
            ],
            [
                'name' => '上上签',
                'content' => [
                    '约子成双话缠绵',
                    '花前月下人相映',
                ],
            ],
            [
                'name' => '上上签',
                'content' => [
                    '此情延万年',
                    '今日乐相乐',
                ],
            ],
            [
                'name' => '上上签',
                'content' => [
                    '夜夜安枕席',
                    '世事如所料',
                ],
            ],
            [
                'name' => '上上签',
                'content' => [
                    '玉枕花烛常相伴',
                    '与子同赴',
                ],
            ],
            [
                'name' => '上上签',
                'content' => [
                    '晨起画峨眉',
                    '铜镜映双影',
                ],
            ],
            [
                'name' => '上上签',
                'content' => [
                    '不及眼前半分颜',
                    '明月之珠如相比',
                ],
            ],
            [
                'name' => '上上签',
                'content' => [
                    '日日相守似新人',
                    '年年不变好风姿',
                ],
            ],
            [
                'name' => '大吉签',
                'content' => [
                    '和鸣锵锵',
                    '是谓凤凰于飞',
                ],
            ],
            [
                'name' => '大吉签',
                'content' => [
                    '不辞冰雪为卿热',
                    '若似月轮终皎洁',
                ],
            ],
            [
                'name' => '大吉签',
                'content' => [
                    '情似雨余黏地絮',
                    '人如风后入江云',
                ],
            ],
            [
                'name' => '大吉签',
                'content' => [
                    '双双对对落芸香',
                    '相思蝶，斜日一双双',
                ],
            ],
            [
                'name' => '大吉签',
                'content' => [
                    '软玉妖娆裹素衣',
                    '更无人处又添香',
                ],
            ],
            [
                'name' => '大吉签',
                'content' => [
                    '闲时共话，来把情深诉',
                    '长夜如相待',
                ],
            ],
            [
                'name' => '大吉签',
                'content' => [
                    '思君得见，一杯岂开怀',
                    '一年明月今宵圆',
                ],
            ],
            [
                'name' => '上吉签',
                'content' => [
                    '入骨相思知不知',
                    '玲珑骰子安红豆',
                ],
            ],
            [
                'name' => '上吉签',
                'content' => [
                    '短相思兮无穷极',
                    '长相思兮长相忆',
                ],
            ],
            [
                'name' => '上吉签',
                'content' => [
                    '入骨相思知不知',
                    '玲珑骰子安红豆',
                ],
            ],
            [
                'name' => '上吉签',
                'content' => [
                    '翻就相思结',
                    '谁料同心结不成',
                ],
            ],
            [
                'name' => '上吉签',
                'content' => [
                    '定不负相思意',
                    '只愿君心似我心',
                ],
            ],
            [
                'name' => '上吉签',
                'content' => [
                    '一寸相思一寸灰',
                    '春心莫共花争发',
                ],
            ],
            [
                'name' => '上吉签',
                'content' => [
                    '才会相思，便害相思',
                    '平生不会相思',
                ],
            ],
            [
                'name' => '上吉签',
                'content' => [
                    '只为相思老',
                    '两鬓可怜青',
                ],
            ],
            [
                'name' => '上吉签',
                'content' => [
                    '飞雨落花中',
                    '相寻梦里路',
                ],
            ],
            [
                'name' => '上吉签',
                'content' => [
                    '泪痕点点寄相思',
                    '斑竹枝，斑竹枝',
                ],
            ],
            [
                'name' => '上吉签',
                'content' => [
                    '相思始觉海非深',
                    '相恨不如潮有信',
                ],
            ],
            [
                'name' => '上吉签',
                'content' => [
                    '扫取峨眉许君郎',
                    '拟将一段情丝寄',
                ],
            ],
            [
                'name' => '上吉签',
                'content' => [
                    '也卧温柔乡',
                    '满园芳菲满殆尽',
                ],
            ],
            [
                'name' => '上吉签',
                'content' => [
                    '人自相逢，万般情浓',
                    '花有好时重开艳',
                ],
            ],
            [
                'name' => '上吉签',
                'content' => [
                    '眉眼盈盈笑颜开',
                    '轻衣摇曳拟云裳',
                ],
            ],
            [
                'name' => '上吉签',
                'content' => [
                    '归来不言迟',
                    '绿草蔓如丝',
                ],
            ],
            [
                'name' => '中吉签',
                'content' => [
                    '中有千千结',
                    '心似双丝网',
                ],
            ],
            [
                'name' => '中吉签',
                'content' => [
                    '又岂在朝朝暮暮',
                    '两情若是久长时',
                ],
            ],
            [
                'name' => '中吉签',
                'content' => [
                    '卿须怜我我怜卿',
                    '瘦影自怜秋水照',
                ],
            ],
            [
                'name' => '中吉签',
                'content' => [
                    '不辞遍唱阳春',
                    '若有知音见采',
                ],
            ],
            [
                'name' => '中吉签',
                'content' => [
                    '浅情人不知',
                    '欲把相思说似谁',
                ],
            ],
            [
                'name' => '中吉签',
                'content' => [
                    '为谁风露立中宵',
                    '似此星辰非昨夜',
                ],
            ],
            [
                'name' => '中吉签',
                'content' => [
                    '未妨惆怅是清狂',
                    '直道相思了无益',
                ],
            ],
            [
                'name' => '中吉签',
                'content' => [
                    '一寸还成千万缕',
                    '无情不似多情苦',
                ],
            ],
            [
                'name' => '中吉签',
                'content' => [
                    '脉脉此情谁诉',
                    '千金纵买相如赋',
                ],
            ],
            [
                'name' => '中吉签',
                'content' => [
                    '莫向花笺费泪行',
                    '相思本是无凭语',
                ],
            ],
            [
                'name' => '中吉签',
                'content' => [
                    '行云终与谁同',
                    '流水便随春远',
                ],
            ],
            [
                'name' => '中吉签',
                'content' => [
                    '夜夜相思，来日与君同',
                    '几经日月未相逢',
                ],
            ],
            [
                'name' => '中吉签',
                'content' => [
                    '但愿船开迟',
                    '临水送别行复止',
                ],
            ],
            [
                'name' => '中吉签',
                'content' => [
                    '明日彩云归',
                    '一夜烟雨泪江南',
                ],
            ],
            [
                'name' => '中吉签',
                'content' => [
                    '天涯何处能相随？',
                    '旅燕飞，江山远',
                ],
            ],
        ];
        $num = count($poetry) - 1;
        $code = is_numeric($code) && $code >= 0 && $code <= $num ? $code : mt_rand(0, $num);
        $finalPoetry = $poetry[$code];
        $finalPoetry['nickName'] = Yii::$app->session->get('resourceOwnerNickName');

        return $finalPoetry;
    }
}
