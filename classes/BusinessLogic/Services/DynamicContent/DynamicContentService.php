<?php

namespace CleverReachIntegration\BusinessLogic\Services\DynamicContent;

use CleverReach\BusinessLogic\DynamicContent\DynamicContentService as BaseDynamicContent;

/**
 * Class DynamicContentService
 * @package CleverReachIntegration\BusinessLogic\Services\DynamicContent
 */
class DynamicContentService extends BaseDynamicContent
{
    /**
     * @return array
     */
    public function getSupportedDynamicContent()
    {
        return array();
    }

}