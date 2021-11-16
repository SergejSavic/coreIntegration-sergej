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
        return parent::install();
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall();
    }
}


