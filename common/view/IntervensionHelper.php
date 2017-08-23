<?php

namespace common\view;

class IntervensionHelper
{
    public $image;
    public function __construct($image)
    {
        $this->image = $image;
    }

    public function column($words, $x, $y, $padding, $config)
    {
        $wordArr = array_map(function ($i) use ($words) {
            return mb_substr($words, $i, 1, 'UTF-8');
        }, range(0, mb_strlen($words) - 1));
        foreach ($wordArr as $num => $text) {
            $numY = $y + $num * $config['size'] + $num * $padding;
            $this->image->text($text, $x, $numY, function ($font) use ($config) {
                $font->file($config['file']);
                $font->size($config['size']);
                $font->color($config['color']);
            });
        }

        return true;
    }
}
