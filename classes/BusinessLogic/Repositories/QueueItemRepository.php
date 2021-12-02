<?php

namespace CleverReachIntegration\BusinessLogic\Repositories;

use CleverReachIntegration\BusinessLogic\Services\TransformerService;
use Logeecom\Infrastructure\ORM\Entity;
use Logeecom\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Logeecom\Infrastructure\ORM\QueryFilter\Operators;
use Logeecom\Infrastructure\ORM\QueryFilter\QueryFilter;
use Logeecom\Infrastructure\TaskExecution\Exceptions\QueueItemSaveException;
use Logeecom\Infrastructure\TaskExecution\QueueItem;
use Logeecom\Infrastructure\ORM\Interfaces\QueueItemRepository as QueueItemRepositoryInterface;

/**
 * Class QueueItemRepository
 * @package CleverReachIntegration\BusinessLogic\Repositories
 */
class QueueItemRepository extends BaseRepository implements QueueItemRepositoryInterface
{
    /**
     * Fully qualified name of this interface.
     */
    const THIS_CLASS_NAME = __CLASS__;
    const STATUS_INDEX = 'index_1';
    const QUEUE_NAME_INDEX = 'index_3';
    const PRIORITY_INDEX = 'index_8';

    /**
     * @return string
     */
    public static function getTableName()
    {
        return _DB_PREFIX_ . 'queue_item_entities';
    }

    /**
     * @param int $priority
     * @param int $limit
     * @return array|QueueItem[]
     */
    public function findOldestQueuedItems($priority, $limit = 10)
    {
        $queuedItems = array();

        try {
            $runningQueueNames = $this->getRunningQueueNames();
            $queuedItems = $this->getQueuedItems($priority, $runningQueueNames, $limit);
        } catch (\Exception $e) {
            // In case of database exception return empty result set.
        }

        return $queuedItems;
    }

    /**
     * @param QueueItem $queueItem
     * @param array $additionalWhere
     * @return int
     * @throws QueueItemSaveException
     */
    public function saveWithCondition(QueueItem $queueItem, array $additionalWhere = array())
    {
        $savedItemId = null;

        try {
            $itemId = $queueItem->getId();
            if ($itemId === null || $itemId <= 0) {
                $savedItemId = $this->save($queueItem);
            } else {
                $this->updateQueueItem($queueItem, $additionalWhere);
            }
        } catch (\Exception $exception) {
            throw new QueueItemSaveException(
                'Failed to save queue item. Error: ' . $exception->getMessage(),
                0,
                $exception
            );
        }

        return $savedItemId ?: $itemId;
    }

    /**
     * @return array
     * @throws QueryFilterInvalidParamException
     * @throws \PrestaShopDatabaseException
     */
    public function getRunningQueueNames()
    {
        $filter = new QueryFilter();
        $filter->where(self::STATUS_INDEX, Operators::EQUALS, QueueItem::IN_PROGRESS);
        $queues = $this->selectSpecificColumns($filter, array(self::QUEUE_NAME_INDEX));
        $queueNames = array();

        foreach ($queues as $queue) {
            $queueNames[] = $queue[self::QUEUE_NAME_INDEX];
        }

        return $queueNames;
    }

    /**
     * @param $priority
     * @param $runningQueueNames
     * @param $limit
     * @return array|Entity[]
     * @throws QueryFilterInvalidParamException
     * @throws \PrestaShopDatabaseException
     */
    public function getQueuedItems($priority, $runningQueueNames, $limit)
    {
        $filter = new QueryFilter();
        $ids = $this->getQueueIdsForExecution($priority, $runningQueueNames, $limit);
        $records = array();

        if (count($ids) > 0) {
            foreach ($ids as $id) {
                $filter->where('id', Operators::EQUALS, $id);
            }

            $filter->orderBy('id');
            $records = $this->select($filter);
        }

        return $records;
    }

    /**
     * @param $priority
     * @param $runningQueueNames
     * @param $limit
     * @return array
     * @throws QueryFilterInvalidParamException
     * @throws \PrestaShopDatabaseException
     */
    public function getQueueIdsForExecution($priority, $runningQueueNames, $limit)
    {
        $priority = TransformerService::transformNumberToString($priority);
        $filter = new QueryFilter();
        $filter->where(self::PRIORITY_INDEX, Operators::EQUALS, $priority);
        $filter->where(self::STATUS_INDEX, Operators::EQUALS, QueueItem::QUEUED);
        $filter->setLimit($limit);

        return $this->getArray($this->findQueueItemsIds($filter, $runningQueueNames));
    }

    /**
     * @return bool
     * @throws QueryFilterInvalidParamException
     * @throws \PrestaShopDatabaseException
     */
    public function isConnectTaskCompleted()
    {
        $filter = new QueryFilter();
        $filter->where('taskType', Operators::EQUALS, 'ConnectTask');
        $filter->where('status', Operators::EQUALS, 'completed');
        $filter->orderBy('id', QueryFilter::ORDER_DESC);

        $queueItem = $this->selectOne($filter);

        return $queueItem !== null;
    }

    /**
     * @return bool
     * @throws QueryFilterInvalidParamException
     * @throws \PrestaShopDatabaseException
     */
    public function isConnectTaskQueued()
    {
        $filter = new QueryFilter();
        $filter->where('taskType', Operators::EQUALS, 'ConnectTask');
        $filter->where('status', Operators::EQUALS, 'queued');
        $filter->orderBy('id', QueryFilter::ORDER_DESC);

        $queueItem = $this->selectOne($filter);

        return $queueItem !== null;
    }

    /**
     * @return bool
     * @throws QueryFilterInvalidParamException
     * @throws \PrestaShopDatabaseException
     */
    public function isInitialSyncCompleted()
    {
        $filter = new QueryFilter();
        $filter->where('taskType', Operators::EQUALS, 'InitialSyncTask');
        $filter->where('status', Operators::EQUALS, 'completed');
        $filter->orderBy('id', QueryFilter::ORDER_DESC);

        $queueItem = $this->selectOne($filter);

        return $queueItem !== null;
    }

    /**
     * @return mixed|string
     * @throws QueryFilterInvalidParamException
     * @throws \PrestaShopDatabaseException
     */
    public function checkInitialSyncStatus()
    {
        $filter = new QueryFilter();
        $filter->where('taskType', Operators::EQUALS, 'InitialSyncTask');
        $filter->orderBy('id', QueryFilter::ORDER_DESC);

        $queueItem = $this->selectOne($filter);

        return $queueItem !== null ? $queueItem->getStatus() : '';
    }

    /**
     * @param QueryFilter $filter
     * @param $runningQueueNames
     * @return array|bool|\mysqli_result|\PDOStatement|resource|null
     * @throws \PrestaShopDatabaseException
     */
    private function findQueueItemsIds(QueryFilter $filter, $runningQueueNames)
    {
        $query = 'SELECT min(id) as id FROM ';
        $query .= static::getTableName();
        $query .= $this->where($filter);

        if (!empty($runningQueueNames)) {
            $query .= ' AND ' . $this->whereNotIn(self::QUEUE_NAME_INDEX, $runningQueueNames);
        }

        $query .= ' GROUP BY ' . self::QUEUE_NAME_INDEX;
        $query .= $this->limit($filter);

        return \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

    /**
     * @throws \PrestaShopDatabaseException
     * @throws QueryFilterInvalidParamException
     * @throws QueueItemSaveException
     */
    private function updateQueueItem($queueItem, array $additionalWhere)
    {
        $filter = new QueryFilter();
        $filter->where('id', Operators::EQUALS, $queueItem->getId());

        foreach ($additionalWhere as $name => $value) {
            if ($value === null) {
                $filter->where($name, Operators::NULL);
            } else {
                $filter->where($name, Operators::EQUALS, $value);
            }
        }

        /** @var QueueItem $item */
        $item = $this->selectOne($filter);
        if ($item === null) {
            throw new QueueItemSaveException("Can not update queue item with id {$queueItem->getId()} .");
        }

        $this->update($queueItem);
    }
}