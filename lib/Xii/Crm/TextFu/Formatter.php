<?php

namespace Xii\Crm\TextFu;

use yii\i18n\Formatter as BaseFormatter;

class Formatter extends BaseFormatter
{
    const XII_FORMAT_MONEY = 'xii:money';
    const XII_FORMAT_EMPTY_NICE = 'xii:empty-nice';

    public function format($value, $format)
    {
        if (!$this->supportsCustomFormat($format)) {
            return parent::format($value, $format);
        }

        if (self::XII_FORMAT_MONEY === $format) {
            if (empty($value)) {
                $value = '<div style="text-align: right;">0.00</div>';
            }
        } elseif (self::XII_FORMAT_EMPTY_NICE === $format) {
            if (empty($value)) {
                $value = '--';
            }
        }

        return $value;
    }

    private function supportsCustomFormat($format)
    {
        return in_array($format, [
            self::XII_FORMAT_MONEY,
            self::XII_FORMAT_EMPTY_NICE,
        ]);
    }
}
