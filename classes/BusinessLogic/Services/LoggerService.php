<?php

namespace CleverReachIntegration\BusinessLogic\Services;

use Logeecom\Infrastructure\Logger\Interfaces\ShopLoggerAdapter;
use CleverReach\BusinessLogic\Configuration\Configuration;
use Logeecom\Infrastructure\ServiceRegister;
use Logeecom\Infrastructure\Logger\LogData;
use PrestaShopLoggerCore as Logger;

class LoggerService implements ShopLoggerAdapter
{
    const LOG_SEVERITY_LEVEL_INFORMATIVE = 1;
    const LOG_SEVERITY_LEVEL_WARNING = 2;
    const LOG_SEVERITY_LEVEL_ERROR = 3;
    const LOG_SEVERITY_LEVEL_MAJOR = 4;

    /**
     * @param LogData $data
     */
    public function logMessage(LogData $data)
    {
        $configService = ServiceRegister::getService(Configuration::CLASS_NAME);
        $minLogLevel = $configService->getMinLogLevel();
        $logLevel = $data->getLogLevel();
        $severity = $this->getSeverityLevel($logLevel);

        if ($logLevel > (int)$minLogLevel) {
            return;
        }

        Logger::addLog($data->getMessage(), $severity);
    }

    /**
     * @param int $logLevel
     * @return int|void
     */
    private function getSeverityLevel($logLevel)
    {
        switch ($logLevel) {
            case 0:
                return self::LOG_SEVERITY_LEVEL_ERROR;
            case 1:
                return self::LOG_SEVERITY_LEVEL_WARNING;
            case 3:
            case 2:
                return self::LOG_SEVERITY_LEVEL_INFORMATIVE;
        }
    }

}