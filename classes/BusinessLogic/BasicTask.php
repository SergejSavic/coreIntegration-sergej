<?php

namespace CleverReachIntegration\BusinessLogic;

use Logeecom\Infrastructure\Logger\Logger;
use Logeecom\Infrastructure\TaskExecution\Task;

class BasicTask extends Task
{

    public function execute()
    {
        Logger::logWarning('This is new message', 'Integration');
        Logger::logInfo('This is new message', 'Integration');
        $this->reportProgress(100);
    }

}