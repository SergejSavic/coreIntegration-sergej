<?php

namespace CleverReachIntegration\BusinessLogic\Services\FormEvents;

use CleverReach\BusinessLogic\Form\FormEventsService as BaseFormEventsService;

class FormEventsService extends BaseFormEventsService
{
    /**
     * @return string
     */
    public function getEventUrl()
    {
        return '';
    }

}