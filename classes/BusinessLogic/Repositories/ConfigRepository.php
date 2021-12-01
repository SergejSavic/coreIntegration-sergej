<?php

namespace CleverReachIntegration\BusinessLogic\Repositories;

/**
 * Class ConfigRepository
 * @package CleverReachIntegration\BusinessLogic\Repositories
 */
class ConfigRepository extends BaseRepository
{
    /**
     * Fully qualified name of this interface.
     */
    const THIS_CLASS_NAME = __CLASS__;

    /**
     * @return string
     */
    public static function getTableName()
    {
        return _DB_PREFIX_ . 'config_entities';
    }

}