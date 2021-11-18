<?php

namespace CleverReachIntegration\BusinessLogic\Services;

interface DemoServiceInterface {
    /**
     * Fully qualified name of this interface.
     */
    const CLASS_NAME = __CLASS__;

    /**
     * @return string
     */
    public function getMessage();
}