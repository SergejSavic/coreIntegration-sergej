<?php

namespace CleverReachIntegration\BusinessLogic\Services\Authorization;

use CleverReach\BusinessLogic\Authorization\AuthorizationService as BaseService;
use CleverReach\BusinessLogic\Authorization\Http\AuthProxy;
use Logeecom\Infrastructure\ServiceRegister;
use Logeecom\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use PrestaShop\PrestaShop\Adapter\Entity\Tools;

/**
 * Class AuthorizationService
 * @package CleverReachIntegration\BusinessLogic\Services\Authorization
 */
class AuthorizationService extends BaseService
{
    /**
     * @param false $isRefresh
     * @return string
     */
    public function getRedirectURL($isRefresh = false)
    {
        return Tools::getHttpHost(true) . __PS_BASE_URI__ . 'en/module/core/authorization' . '?XDEBUG_SESSION_START=debug';
    }

    /**
     * @param $code
     * @throws QueryFilterInvalidParamException
     */
    public function authorize($code)
    {
        $authInfo = $this->getAuthProxy()->getAuthInfo($code, $this->getRedirectURL());
        $this->setAuthInfo($authInfo);
    }

    /**
     * @return object
     */
    private function getAuthProxy()
    {
        return ServiceRegister::getService(AuthProxy::CLASS_NAME);
    }
}
