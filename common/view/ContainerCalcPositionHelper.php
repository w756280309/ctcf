<?php

namespace common\view;

class ContainerCalcPositionHelper
{
    public $maxHeight;
    public $maxWidth;
    public function __construct($width, $height)
    {
        $this->maxWidth = $width;
        $this->maxHeight = $height;
    }

    /**
     * 在指定宽高的容器下，摆放多行文字，且要求多行上下左右居中
     *
     * @param int    $lineNum       行数
     * @param int    $maxWordCount  当行最大字数
     * @param string $wordSize      单字垂直高度（像素数）
     * @param string $inlineBlock   行间距（像素数）
     * @param string $letterSpacing 垂直字间距（像素数）
     *
     * @return array|bool
     */
    public function calcFirstWordXY($lineNum, $maxWordCount, $wordSize, $inlineBlock = '0', $letterSpacing = '0')
    {
        $xyPosition = [];
        $actualMinHeight = $maxWordCount * $wordSize + ($maxWordCount - 1) * $letterSpacing;
        $actualMinWidth = $lineNum * $wordSize + ($lineNum - 1) * $inlineBlock;
        if ($actualMinHeight > $this->maxHeight) {
            return $xyPosition;
        }
        if ($actualMinWidth > $this->maxWidth) {
            return $xyPosition;
        }

        $x = 0;
        $y = ($this->maxHeight - ($maxWordCount * $wordSize + ($maxWordCount - 1) * $letterSpacing)) / 2;
        for ($i = 0; $i < $lineNum; $i++) {
            if (0 === $i) {
                $x = ($this->maxWidth - ($lineNum * $wordSize + ($lineNum - 1) * $inlineBlock)) / 2;
            } else {
                $x = $x + $inlineBlock + $wordSize;
            }
            $xyPosition[$i]['x'] = $x;
            $xyPosition[$i]['y'] = $y;
        }

        return $xyPosition;
    }
}
