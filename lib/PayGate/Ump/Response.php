<?php

namespace PayGate\Ump;

class Response
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function get($name)
    {
        if (!array_key_exists($name, $this->data)) {
            throw new \Exception(sprintf('Invalid data key: %s', $name));
        }

        return $this->data[$name];
    }

    public function isSuccessful()
    {
        return '0000' === $this->get('ret_code');
    }

    public function getError()
    {
        throw new \Exception('Not implmented!');
    }
}
