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
        return parent::install() && $this->createDatabaseTables();
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
            `index_1` varchar(100) NOT NULL,
            `index_2` varchar(100) NOT NULL,
            `index_3` varchar(100) NOT NULL,
            `index_4` varchar(100) NOT NULL,
            `index_5` varchar(100) NOT NULL,
            `index_6` varchar(100) NOT NULL,
            `index_7` varchar(100) NOT NULL,
            `index_8` varchar(100) NOT NULL,
            `data` LONGTEXT NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;') &&
            Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'config_entities` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `type` varchar(100) NOT NULL,
            `name` varchar(100) NOT NULL,
            `context` varchar(100) NOT NULL,
            `data` LONGTEXT NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;') &&
            Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'process_entities` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `type` varchar(100) NOT NULL,
            `guid` varchar(100) NOT NULL,
            `data` LONGTEXT NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;') &&
            Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'queue_item_entities` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `type` varchar(100) NOT NULL,
            `status` varchar(100) NOT NULL,
            `taskType` varchar(100) NOT NULL,
            `queueName` varchar(100) NOT NULL,
            `context` varchar(100) NOT NULL,
            `queueTime` varchar(100) NOT NULL,
            `lastExecutionProgress` varchar(100) NOT NULL,
            `lastUpdateTimestamp` varchar(100) NOT NULL,
            `priority` varchar(100) NOT NULL,
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
}


