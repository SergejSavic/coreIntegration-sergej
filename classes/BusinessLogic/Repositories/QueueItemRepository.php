<?php

namespace CleverReachIntegration\BusinessLogic\Repositories;

class QueueItemRepository extends BaseRepository
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
        return _DB_PREFIX_ . 'queue_item_entities';
    }
}