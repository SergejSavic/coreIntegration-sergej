<?php

namespace CleverReachIntegration\BusinessLogic\Services\HTTP;

use PrestaShop\PrestaShop\Adapter\Entity\Tools;

/**
 * Class Request
 * @package CleverReachIntegration\BusinessLogic\Services\HTTP
 */
class Request
{
    /** @var string request method */
    private $method;

    /**
     * Initializes class properties
     */
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param $param
     * @return string|false
     */
    public function getParam($param)
    {
        return Tools::getValue($param);
    }

    /**
     * @return string
     */
    public function getRawBody()
    {
        return Tools::file_get_contents('php://input');
    }

}