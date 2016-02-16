<?php

namespace YiiPlus\Model;

trait ErrorExTrait
{
    public function getSingleError()
    {
        $firstErrors = $this->getFirstErrors();

        return [
            'message' => reset($firstErrors),
            'attribute' => key($firstErrors),
        ];
    }
}
