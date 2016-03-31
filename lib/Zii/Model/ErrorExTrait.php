<?php

namespace Zii\Model;

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
