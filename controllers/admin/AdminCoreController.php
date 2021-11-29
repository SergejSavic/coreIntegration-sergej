<?php

use CleverReachIntegration\BusinessLogic\Repositories\QueueItemRepository;
use Logeecom\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use Logeecom\Infrastructure\ORM\RepositoryRegistry;
use CleverReachIntegration\Infrastructure\BootstrapComponent;
use Logeecom\Infrastructure\TaskExecution\QueueItem;

/**
 * Class AdminCoreController
 */
class AdminCoreController extends ModuleAdminController
{
    /**
     * @var string
     */
    const BASE_IMG_URL = 'modules/cleverreach/views/img/';
    /** @var QueueItemRepository $queueItemRepository */
    private $queueItemRepository;

    /**
     * Initializes bootstrap and queue item repository
     * @throws PrestaShopException
     */
    public function __construct()
    {
        $this->bootstrap = true;
        BootstrapComponent::init();
        $this->queueItemRepository = RepositoryRegistry::getRepository(QueueItem::CLASS_NAME);
        parent::__construct();
    }

    /**
     * @throws SmartyException
     * @throws \Logeecom\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException
     */
    public function initContent()
    {
        $url = Tools::getHttpHost(true) . __PS_BASE_URI__ . self::BASE_IMG_URL;

        try {
            if ($this->queueItemRepository->isConnectTaskCompleted()) {
                $this->setTemplateFile('syncPage.tpl', array('clientID' => '305190', 'headerImage' => $url . 'logo_cleverreach.svg'));
            } else {
                $this->setTemplateFile('origin.tpl', array('headerImage' => $url . 'logo_cleverreach.svg', 'contentImage' => $url . 'icon_hello.png'));
            }
        } catch (QueryFilterInvalidParamException $e) {
        } catch (PrestaShopDatabaseException $e) {
        }
    }

    /**
     * @throws \Logeecom\Infrastructure\ORM\Exceptions\RepositoryNotRegisteredException
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