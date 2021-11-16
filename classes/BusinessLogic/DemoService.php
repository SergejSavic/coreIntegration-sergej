<?php

namespace CleverReachIntegration\BusinessLogic;

class DemoService
{
    const CLASS_NAME = __CLASS__;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return "This is new message";
    }
}