<?php

require_once __DIR__ . '/vendor/autoload.php';

if (!defined('_PS_VERSION_')) {
    return false;
}

/**
 * Class Core
 */
class Core extends Module
{
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
        $this->version = '1.0';
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
            $this->context->controller->addCSS($this->_path . 'views/dist/css/admin.css');
            $this->context->controller->addCSS($this->_path . 'views/dist/css/sync_page.css');
            $this->context->controller->addJS($this->_path . 'views/dist/js/back.js');
        }
    }

    /**
     * @return bool
     */
    private function registerHooksMethod()
    {
        return $this->registerHook('displayBackOfficeHeader');
    }
}


