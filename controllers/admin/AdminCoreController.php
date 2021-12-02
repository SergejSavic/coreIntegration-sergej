<?php

use CleverReach\BusinessLogic\Authorization\Contracts\AuthorizationService;
use CleverReach\BusinessLogic\InitialSynchronization\Tasks\Composite\InitialSyncTask;
use CleverReach\BusinessLogic\Receiver\DTO\Config\SyncService;
use CleverReachIntegration\BusinessLogic\Repositories\ConfigRepository;
use CleverReachIntegration\BusinessLogic\Repositories\QueueItemRepository;
use CleverReachIntegration\BusinessLogic\Services\Receiver\CustomerService;
use CleverReachIntegration\BusinessLogic\Services\Receiver\GuestService;
use CleverReachIntegration\BusinessLogic\Services\Receiver\SubscriberService;
use CleverReachIntegration\BusinessLogic\Services\Receiver\VisitorService;
use Logeecom\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Logeecom\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException;
use Logeecom\Infrastructure\ORM\RepositoryRegistry;
use CleverReachIntegration\Infrastructure\BootstrapComponent;
use Logeecom\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use Logeecom\Infrastructure\TaskExecution\QueueService as BaseQueueService;
use Logeecom\Infrastructure\ServiceRegister;
use Logeecom\Infrastructure\TaskExecution\QueueItem;
use CleverReach\BusinessLogic\Receiver\SyncConfigService;
use CleverReach\BusinessLogic\Configuration\Configuration;

/**
 * Class AdminCoreController
 */
class AdminCoreController extends ModuleAdminController
{
    /** @var string base image url */
    const BASE_IMG_URL = 'modules/cleverreach/views/img/';
    /** @var QueueItemRepository $queueItemRepository */
    private $queueItemRepository;
    /** @var ConfigRepository $configRepository */
    private $configRepository;

    /**
     * Initializes bootstrap and queue item repository
     * @throws PrestaShopException
     * @throws RepositoryNotRegisteredException
     */
    public function __construct()
    {
        $this->bootstrap = true;
        BootstrapComponent::init();
        $this->queueItemRepository = RepositoryRegistry::getRepository(QueueItem::CLASS_NAME);
        parent::__construct();
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws QueryFilterInvalidParamException
     * @throws SmartyException
     * @throws QueueStorageUnavailableException
     */
    public function initContent()
    {
        $url = Tools::getHttpHost(true) . __PS_BASE_URI__ . self::BASE_IMG_URL;
        /** @var BaseQueueService $queueService */
        $queueService = ServiceRegister::getService(BaseQueueService::CLASS_NAME);
        /** @var Configuration $configService */
        $configService = ServiceRegister::getService(Configuration::CLASS_NAME);

        //(ServiceRegister::getService(\CleverReachIntegration\BusinessLogic\Services\DemoServiceInterface::CLASS_NAME))->getMessage();
        //(new \CleverReach\BusinessLogic\SecondarySynchronization\Tasks\Composite\SecondarySyncTask())->execute();

        if ($this->queueItemRepository->isConnectTaskCompleted()) {
            /** @var SyncConfigService $syncConfigService */
            $syncConfigService = ServiceRegister::getService(SyncConfigService::CLASS_NAME);
            $enabledServices = $this->prepareServices();
            $syncConfigService->setEnabledServices($enabledServices);

            $userInfo = (ServiceRegister::getService(AuthorizationService::CLASS_NAME))->getUserInfo();
            $this->setTemplateFile('syncPage.tpl', array('clientID' => $userInfo->getId(), 'headerImage' => $url . 'logo_cleverreach.svg'));

            if (!$this->queueItemRepository->isInitialSyncCompleted()) {
                $queueService->enqueue($configService->getDefaultQueueName(), new InitialSyncTask());
            }

        } else {
            $this->setTemplateFile('origin.tpl', array('headerImage' => $url . 'logo_cleverreach.svg', 'contentImage' => $url . 'icon_hello.png'));
        }

    }

    /**
     * @throws QueryFilterInvalidParamException
     * @throws PrestaShopDatabaseException
     */
    public function ajaxProcessCheckIfConnectTaskIsCompleted()
    {
        $response = $this->queueItemRepository->isConnectTaskCompleted();
        echo json_encode($response);
        exit;
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws QueryFilterInvalidParamException
     */
    public function ajaxProcessCheckIfConnectTaskIsQueued()
    {
        $response = $this->queueItemRepository->isConnectTaskQueued();
        echo json_encode($response);
        exit;
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws QueryFilterInvalidParamException
     */
    public function ajaxProcessCheckInitialSyncStatus()
    {
        $syncStatus = $this->queueItemRepository->checkInitialSyncStatus();
        echo json_encode($syncStatus);
        exit;
    }

    /**
     * @return array
     */
    private function prepareServices()
    {
        $services = array();
        $services[] = new SyncService('service-' . VisitorService::THIS_CLASS_NAME, 2, VisitorService::THIS_CLASS_NAME);
        $services[] = new SyncService('service-' . GuestService::THIS_CLASS_NAME, 2, GuestService::THIS_CLASS_NAME);
        $services[] = new SyncService('service-' . CustomerService::THIS_CLASS_NAME, 2, CustomerService::THIS_CLASS_NAME);
        $services[] = new SyncService('service-' . SubscriberService::THIS_CLASS_NAME, 2, SubscriberService::THIS_CLASS_NAME);

        return $services;
    }

    /**
     * @param $templateName
     * @param $variables
     * @throws SmartyException
     */
    private function setTemplateFile($templateName, $variables)
    {
        $template = $this->context->smarty->createTemplate($this->getTemplatePath() . $templateName, $this->context->smarty);
        $template->assign($variables);
        $this->content .= $template->fetch();
        parent::initContent();
    }

}