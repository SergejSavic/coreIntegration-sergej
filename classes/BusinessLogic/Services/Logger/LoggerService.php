<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author PrestaShop SA <contact@prestashop.com>
 * @copyright  2007-2019 PrestaShop SA
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace CleverReachIntegration\BusinessLogic\Services\Logger;

use Logeecom\Infrastructure\Logger\Interfaces\ShopLoggerAdapter;
use CleverReach\BusinessLogic\Configuration\Configuration;
use Logeecom\Infrastructure\ServiceRegister;
use Logeecom\Infrastructure\Logger\LogData;
use PrestaShopLoggerCore as Logger;

/**
 * Class LoggerService
 * @package CleverReachIntegration\BusinessLogic\Services\Logger
 */
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
            case 2:
                return self::LOG_SEVERITY_LEVEL_INFORMATIVE;
            case 3:
                return self::LOG_SEVERITY_LEVEL_MAJOR;
        }
    }
}
