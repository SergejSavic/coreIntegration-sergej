<?php

namespace CleverReachIntegration\BusinessLogic\Services;

use Logeecom\Infrastructure\Configuration\ConfigEntity;
use Logeecom\Infrastructure\ORM\QueryFilter\Operators;
use Logeecom\Infrastructure\ORM\QueryFilter\QueryFilter;
use Logeecom\Infrastructure\ORM\RepositoryRegistry;
use Logeecom\Infrastructure\ServiceRegister;

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

        $configEntity = new ConfigEntity();
        $configEntity->setName('name2');
        $configEntity->setValue('test2');
        $configEntity->setContext('context2');
        $configEntity->setId(13);

        $repository->delete($configEntity);


        $filter = new QueryFilter();
        $filter->where('index_1', Operators::EQUALS, 'name2');
        $filter->orderBy('id', QueryFilter::ORDER_DESC);
        $filter->setLimit(1);
        $filter->setOffset(1);
        /** @var ConfigEntity $configEntity */
        $configEntity = $repository->selectOne($filter);

        return "This is new message";
    }
}