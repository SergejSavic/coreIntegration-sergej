<?php

namespace CleverReachIntegration\Infrastructure;

use Logeecom\Infrastructure\BootstrapComponent as InfrastructureBootstrap;
use Logeecom\Infrastructure\ServiceRegister;
use CleverReachIntegration\BusinessLogic\DemoService;

class BootstrapComponent extends InfrastructureBootstrap
{
    public static function init()
    {
        parent::init();
    }

    /**
     * Initializes services and utilities.
     */
    public static function initServices()
    {
        parent::initServices();

        ServiceRegister::registerService(
            DemoService::CLASS_NAME,
            function () {
                return new DemoService();
            }
        );
    }

}