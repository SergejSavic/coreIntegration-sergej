<?php

namespace CleverReachIntegration\BusinessLogic\Services;

use CleverReach\BusinessLogic\Authorization\AuthorizationService as BaseService;

class AuthorizationService extends BaseService
{
    /**
     * @param false $isRefresh
     * @return string
     */
    public function getRedirectURL($isRefresh = false)
    {
        return '';
    }
}
