<?php

namespace common\models\queue;


abstract class Job
{
    protected $params;

    abstract public function run();

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function getParams()
    {
        return $this->params;
    }
}