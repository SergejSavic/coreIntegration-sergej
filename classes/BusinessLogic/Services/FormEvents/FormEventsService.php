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
        return 'http://b57b-82-117-217-138.ngrok.io/en/module/core/webhook';
    }

}