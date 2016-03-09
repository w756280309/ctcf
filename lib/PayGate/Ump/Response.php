<?php

namespace PayGate\Ump;

class Response
{
    private $data;
    private $location;
    public function __construct(array $data, $location = null)
    {
        $this->data = $data;
        $this->location  = $location;
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

    public function isRedirection()
    {
        return null !== $this->location;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function toArray()
    {
        return $this->data;
    }
}
