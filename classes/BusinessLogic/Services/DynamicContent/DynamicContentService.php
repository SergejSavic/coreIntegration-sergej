<?php

namespace CleverReachIntegration\BusinessLogic\Services\DynamicContent;

use CleverReach\BusinessLogic\DynamicContent\DynamicContentService as BaseDynamicContent;

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