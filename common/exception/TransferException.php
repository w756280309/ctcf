<?php

namespace common\exception;

class TransferException extends \Exception
{
    public $transfer;

    public function __construct($transfer, $message = '', $code = 0, \Exception $previous = null)
    {
        $this->transfer = $transfer;
        $message = '【' . $transfer->sn . '】' . '【' . date('Y-m-d H:i:s') . '】' . $message;
        parent::__construct($message, $code, $previous);
    }
}