<?php

namespace CleverReachIntegration\BusinessLogic\Services\Group;

use CleverReach\BusinessLogic\Group\GroupService as BaseService;
use CleverReach\BusinessLogic\Configuration\Configuration;
use CleverReachIntegration\BusinessLogic\Repositories\PrestaShopRepository;
use CleverReachIntegration\BusinessLogic\Services\ConfigService as ConfigurationService;
use Logeecom\Infrastructure\ServiceRegister;

/**
 * Class GroupService
 * @package CleverReachIntegration\BusinessLogic\Services\Group
 */
class GroupService extends BaseService
{
    /** @var ConfigurationService $configService */
    private $configService;
    /** @var string name of the shop */
    private $shopName;

    /**
     * initializes configuration service
     */
    public function __construct()
    {
        $this->configService = ServiceRegister::getService(Configuration::CLASS_NAME);
        $this->shopName = (new PrestaShopRepository())->getShopName();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->configService->getIntegrationName() . '-' . $this->shopName;
    }

    /**
     * @return string
     */
    public function getBlacklistedEmailsSuffix()
    {
        return '-' . $this->configService->getIntegrationName() . '-' . $this->shopName;
    }

}
