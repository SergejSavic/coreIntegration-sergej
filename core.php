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

require_once rtrim(_PS_MODULE_DIR_, '/') . '/core/vendor/autoload.php';

use CleverReachIntegration\Infrastructure\BootstrapComponent;
use Logeecom\Infrastructure\ServiceRegister;
use Logeecom\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use Logeecom\Infrastructure\TaskExecution\QueueService as BaseQueueService;
use CleverReach\BusinessLogic\Configuration\Configuration;
use CleverReach\BusinessLogic\Receiver\Tasks\Composite\ReceiverSyncTask;
use CleverReach\BusinessLogic\Receiver\Tasks\Composite\Configuration\SyncConfiguration;
use CleverReach\BusinessLogic\Order\Tasks\OrderItemsSyncTask;

if (!defined('_PS_VERSION_')) {
    return false;
}

/**
 * Class Core
 */
class Core extends Module
{
    /** @var BaseQueueService $queueService */
    private $queueService;
    /** @var Configuration $configService */
    private $configService;

    /**
     * @var array[]
     */
    public $tabs = array(
        array(
            'name' => 'Core Integration',
            'class_name' => 'AdminCore',
            'visible' => true,
            'parent_class_name' => 'Marketing',
        ),
    );

    /**
     * Initializes plugin info
     */
    public function __construct()
    {
        $this->name = 'core';
        $this->author = 'Sergej Savic';
        $this->tab = 'advertising_marketing';
        $this->version = '1.1.2';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Core Integration', array(), 'Modules.Core.Admin');
        $this->description = $this->trans('Allow store users to manipulate CleverReach customers.', array(), 'Modules.Core.Admin');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * @return bool
     */
    public function install()
    {
        return parent::install() && $this->createDatabaseTables() && $this->registerHooksMethod();
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall() && $this->dropDatabaseTables();
    }

    /**
     * @return bool
     */
    private function createDatabaseTables()
    {
        return Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'cleverreach_entities` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `type` varchar(100) NOT NULL,
            `index_1` varchar(100) ,
            `index_2` varchar(100) ,
            `index_3` varchar(100) ,
            `index_4` varchar(100) ,
            `index_5` varchar(100) ,
            `index_6` varchar(100) ,
            `index_7` varchar(100) ,
            `index_8` varchar(100) ,
            `data` LONGTEXT NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;') &&
            Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'config_entities` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `type` varchar(100) NOT NULL,
            `index_1` varchar(100) ,
            `index_2` varchar(100) ,
            `data` LONGTEXT NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;') &&
            Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'process_entities` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `type` varchar(100) NOT NULL,
            `index_1` varchar(100) ,
            `data` LONGTEXT NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;') &&
            Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'queue_item_entities` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `type` varchar(100) NOT NULL,
            `index_1` varchar(100) ,
            `index_2` varchar(100) ,
            `index_3` varchar(100) ,
            `index_4` varchar(100) ,
            `index_5` varchar(100) ,
            `index_6` varchar(100) ,
            `index_7` varchar(100) ,
            `index_8` varchar(100) ,
            `data` LONGTEXT NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;');
    }

    /**
     * @return bool
     */
    private function dropDatabaseTables()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS`' . _DB_PREFIX_ . 'cleverreach_entities`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS`' . _DB_PREFIX_ . 'config_entities`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS`' . _DB_PREFIX_ . 'process_entities`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS`' . _DB_PREFIX_ . 'queue_item_entities`');
    }

    /**
     * Calls method to set css and js to controller
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (false !== strpos(Tools::getValue('controller'), 'AdminCore')) {
            $this->initControllerAssets();
        }
    }

    /**
     * @param $params
     * @throws QueueStorageUnavailableException
     */
    public function hookActionObjectCustomerAddBefore($params)
    {
        $this->initServices();
        $email = $params['object']->email;

        $this->queueService->enqueue($this->configService->getDefaultQueueName(), new ReceiverSyncTask(new SyncConfiguration(array($email))));
    }

    /**
     * @param $params
     * @throws QueueStorageUnavailableException
     */
    public function hookActionObjectCustomerUpdateBefore($params)
    {
        $this->initServices();
        $email = $params['object']->email;

        $this->queueService->enqueue($this->configService->getDefaultQueueName(), new ReceiverSyncTask(new SyncConfiguration(array($email))));
    }

    /**
     * @param $params
     * @throws QueueStorageUnavailableException
     */
    public function hookActionValidateOrder($params)
    {
        $this->initServices();
        $orderId = $params['order']->id;
        $email = $params['customer']->email;

        $this->queueService->enqueue($this->configService->getDefaultQueueName(), new OrderItemsSyncTask($orderId, $email));
    }

    /**
     * @param $params
     * @throws QueueStorageUnavailableException
     */
    public function hookActionObjectCustomerDeleteBefore($params)
    {
        $this->initServices();
        $email = $params['object']->email;

        $this->queueService->enqueue($this->configService->getDefaultQueueName(), new \CleverReach\BusinessLogic\Receiver\Tasks\DeactivateReceiverTask($email));
    }

    /**
     * Sets css and js files for admin controllers
     */
    private function initControllerAssets()
    {
        if (Tools::getValue('controller') === 'AdminCore') {
            $adminAjaxLink = $this->context->link->getAdminLink('AdminCore');
            $cleverReachURL = 'http://rest.cleverreach.com/oauth/authorize.php?client_id=zhYTmczOCA&grant=basic&response_type=code&redirect_uri=' .
                Tools::getHttpHost(true) . __PS_BASE_URI__ . 'en/module/core/authorization' . '?XDEBUG_SESSION_START=debug';

            Media::addJsDef(array(
                'adminAjaxLink' => $adminAjaxLink,
                'cleverReachURL' => $cleverReachURL
            ));
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
            $this->context->controller->addCSS($this->_path . 'views/css/sync_page.css');
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
        }
    }

    /**
     * @return bool
     */
    private function initServices()
    {
        BootstrapComponent::init();
        $this->configService = ServiceRegister::getService(Configuration::CLASS_NAME);
        $this->queueService = ServiceRegister::getService(BaseQueueService::CLASS_NAME);

        return true;
    }

    /**
     * @return bool
     */
    private function registerHooksMethod()
    {
        return $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('actionObjectCustomerAddBefore')
            && $this->registerHook('actionObjectCustomerUpdateBefore')
            && $this->registerHook('actionValidateOrder')
            && $this->registerHook('actionObjectCustomerDeleteBefore');
    }
}
