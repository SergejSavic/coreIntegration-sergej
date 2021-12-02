<?php

namespace CleverReachIntegration\BusinessLogic\Services;

use CleverReach\BusinessLogic\Group\Contracts\GroupService;
use CleverReach\BusinessLogic\Language\Contracts\TranslationService;
use CleverReach\BusinessLogic\Receiver\Contracts\ReceiverService;
use CleverReachIntegration\BusinessLogic\Repositories\QueueItemRepository;
use CleverReachIntegration\BusinessLogic\Services\Receiver\CustomerService;
use CleverReachIntegration\BusinessLogic\Services\Receiver\GuestService;
use CleverReachIntegration\BusinessLogic\Services\Receiver\SubscriberService;
use CleverReachIntegration\BusinessLogic\Services\Receiver\VisitorService;
use Logeecom\Infrastructure\Configuration\ConfigEntity;
use CleverReach\BusinessLogic\Configuration\Configuration;
use Logeecom\Infrastructure\Logger\Logger;
use Logeecom\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Logeecom\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use Logeecom\Infrastructure\ORM\QueryFilter\Operators;
use Logeecom\Infrastructure\ORM\QueryFilter\QueryFilter;
use Logeecom\Infrastructure\ORM\RepositoryRegistry;
use Logeecom\Infrastructure\ServiceRegister;
use Logeecom\Infrastructure\Logger\Interfaces\ShopLoggerAdapter;
use Logeecom\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use Logeecom\Infrastructure\TaskExecution\Interfaces\AsyncProcessService;
use Logeecom\Infrastructure\TaskExecution\Interfaces\Priority;
use Logeecom\Infrastructure\TaskExecution\QueueItem;
use Logeecom\Infrastructure\TaskExecution\QueueService as BaseQueueService;
use Logeecom\Infrastructure\AutoTest\AutoTestTask;
use CleverReachIntegration\BusinessLogic\BasicTask;

class DemoService implements DemoServiceInterface
{
    /**
     * @return string
     * @throws QueryFilterInvalidParamException
     * @throws RepositoryNotRegisteredException
     * @throws QueueStorageUnavailableException
     */
    public function getMessage()
    {
        $repository = RepositoryRegistry::getRepository(ConfigEntity::CLASS_NAME);
        /** @var QueueItemRepository $queueItemRepo */
        $queueItemRepo = RepositoryRegistry::getRepository(QueueItem::CLASS_NAME);
//
//        $configEntity = new ConfigEntity();
//        $configEntity->setName('name2.1');
//        $configEntity->setValue('test2');
//        $configEntity->setContext('');
//        $configEntity->setId(20);
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
        //$repository->save($configEntity);
//
//        //$queueItemRepo->saveWithCondition($queueItem, array('index_4' => 'context'));
//
//        $id = $repository->save($configEntity);
//

        $filter = new QueryFilter();
        $filter->where('status', Operators::EQUALS, 'queued');
        $filter->where('queueName', Operators::EQUALS, 'queue2');
        $filter->orderBy('priority', QueryFilter::ORDER_DESC);
        //$queueItemEnt = $queueItemRepo->select($filter);
        //$runningTasks = $queueItemRepo->getRunningQueueNames();
        //$ids = $queueItemRepo->getQueueIdsForExecution(1, $runningTasks, 2);

//        $filter = new QueryFilter();
//        $filter->where('id', Operators::EQUALS, '15');
//        $filter->orderBy('name', QueryFilter::ORDER_DESC);
//        $filter->setLimit(2);
        //$filter->setOffset(1);
        /** @var ConfigEntity $configEntity */
        //$configEntity = $repository->selectOne($filter);

        $asyncProcessService = ServiceRegister::getService(AsyncProcessService::CLASS_NAME);

        /** @var Configuration $configService */
        $configService = ServiceRegister::getService(Configuration::CLASS_NAME);
        $configService->setMinLogLevelGlobal(Logger::DEBUG);

        //Logger::logInfo('Info', 'Integration');
        //Logger::logWarning('Warning', 'Integration');
        //Logger::logError('Error', 'Integration');
        //Logger::logDebug('Debug', 'Integration');

        /** @var BaseQueueService $queueService */
        $queueService = ServiceRegister::getService(BaseQueueService::CLASS_NAME);
        $autoTest = new AutoTestTask("data");
        $basicTask = new BasicTask();
        //$autoTest->execute();

        //$queueService->enqueue('newQueue', $autoTest,'',Priority::LOW);
        //$queueService->enqueue('queue', $autoTest);

//        $groupService = ServiceRegister::getService(GroupService::CLASS_NAME);
//        $name = $groupService->getBlacklistedEmailsSuffix();

//        $visitorService = ServiceRegister::getService(VisitorService::THIS_CLASS_NAME);
//        $visitors = $visitorService->getReceiver('lakik@mail.com');
//        $emails = $visitorService->getReceiverEmails();
//        $subscriberService = ServiceRegister::getService(SubscriberService::THIS_CLASS_NAME);
//        $subscriberService->getReceiverBatch(array('pub@prestashop.com', 'milica@mail.com', 'ivanmarko@mail.com', 'nikola@mail.com', 'lakik@mail.com'));
//        $subscriberService->getReceiverEmails();

        $translationService = ServiceRegister::getService(TranslationService::CLASS_NAME);
        $translationService->getSystemLanguage();
        return "This is new message";
    }
}