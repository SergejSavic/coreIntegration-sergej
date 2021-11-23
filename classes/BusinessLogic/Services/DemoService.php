<?php

namespace CleverReachIntegration\BusinessLogic\Services;

use Logeecom\Infrastructure\Configuration\ConfigEntity;
use CleverReach\BusinessLogic\Configuration\Configuration;
use Logeecom\Infrastructure\Logger\Logger;
use Logeecom\Infrastructure\ORM\QueryFilter\Operators;
use Logeecom\Infrastructure\ORM\QueryFilter\QueryFilter;
use Logeecom\Infrastructure\ORM\RepositoryRegistry;
use Logeecom\Infrastructure\ServiceRegister;
use Logeecom\Infrastructure\Logger\Interfaces\ShopLoggerAdapter;
use Logeecom\Infrastructure\TaskExecution\QueueItem;

class DemoService implements DemoServiceInterface
{
    /**
     * @return string
     * @throws \Logeecom\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException
     * @throws \Logeecom\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException
     */
    public function getMessage()
    {
        $repository = RepositoryRegistry::getRepository(ConfigEntity::CLASS_NAME);
//        $queueItemRepo = RepositoryRegistry::getRepository(QueueItem::CLASS_NAME);
//
//        $configEntity = new ConfigEntity();
//        $configEntity->setName('name2');
//        $configEntity->setValue('test2');
//        $configEntity->setContext('context2');
//        //$configEntity->setId(13);
//
//        $queueItem = new QueueItem();
//        $queueItem->setStatus('queued');
//        $queueItem->setQueueName('queue1');
//        $queueItem->setContext('context');
//        $queueItem->setQueueTimestamp(123124);
//        $queueItem->setLastExecutionProgressBasePoints(0);
//        $queueItem->setLastUpdateTimestamp(32141241);
//        $queueItem->setPriority(1);
//        $queueItem->setProgressBasePoints(10000);
//        $queueItem->setRetries(0);
//        $queueItem->setFailureDescription("");
//
//        //$repository->save($configEntity);
//
//        //$queueItemRepo->saveWithCondition($queueItem, array('index_4' => 'context'));
//
//        $id = $repository->save($configEntity);
//
        $filter = new QueryFilter();
        $filter->where('id', Operators::EQUALS, '15');
        $filter->orderBy('name', QueryFilter::ORDER_DESC);
        $filter->setLimit(2);
        //$filter->setOffset(1);
        /** @var ConfigEntity $configEntity */
        $configEntity = $repository->selectOne($filter);

        /** @var Configuration $configService */
        $configService = ServiceRegister::getService(Configuration::CLASS_NAME);
        $configService->setMinLogLevelGlobal(Logger::DEBUG);

        //Logger::logInfo('Info', 'Integration');
        //Logger::logWarning('Warning', 'Integration');
        //Logger::logError('Error', 'Integration');
        //Logger::logDebug('Debug', 'Integration');

        return "This is new message";
    }
}