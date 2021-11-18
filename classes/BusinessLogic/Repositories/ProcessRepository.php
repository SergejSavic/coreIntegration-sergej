<?php

namespace CleverReachIntegration\BusinessLogic\Repositories;

class ProcessRepository extends BaseRepository
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
        return _DB_PREFIX_ . 'process_entities';
    }
}
