<?php

namespace CleverReachIntegration\BusinessLogic\Services\FormEvents;

use CleverReach\BusinessLogic\Form\FormEventsService as BaseFormEventsService;

/**
 * Class FormEventsService
 * @package CleverReachIntegration\BusinessLogic\Services\FormEvents
 */
class FormEventsService extends BaseFormEventsService
{
    /**
     * @return string
     */
    public function getEventUrl()
    {
        return 'http://c945-82-117-217-138.ngrok.io/en/module/core/webhook?XDEBUG_SESSION_START=debug';
    }

}