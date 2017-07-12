<?php

namespace Zii\Http;

use Symfony\Component\HttpFoundation\ParameterBag;
use yii\web\Request as BaseRequest;

/**
 * 扩展Yii的Request类.
 */
class Request extends BaseRequest
{
    private $queryParamsBag;
    private $bodyParamsBag;

    /*
     * Query参数集合
     *
     * 例1）按int类型来获取参数：`$request->query->getInt('name')`
     *
     * 例2）按bool类型来获取参数：`$request->query->getBoolean('name')`
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    public function getQuery()
    {
        if (null === $this->queryParamsBag) {
            $this->queryParamsBag = new ParameterBag($_GET);
        }

        return $this->queryParamsBag;
    }

    /*
     * Request参数集合
     *
     * 例1）Model对象load数据：
     * ```
     * $model->load($request->body->all())
     * ```
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    public function getBody()
    {
        if (null === $this->bodyParamsBag) {
            $this->bodyParamsBag = new ParameterBag(parent::getBodyParams());
        }

        return $this->bodyParamsBag;
    }
}
