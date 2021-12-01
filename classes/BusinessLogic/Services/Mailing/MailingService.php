<?php

namespace CleverReachIntegration\BusinessLogic\Services\Mailing;

use CleverReach\BusinessLogic\Mailing\Contracts\DefaultMailingService;
use CleverReach\BusinessLogic\Mailing\DTO\MailingContent;

/**
 * Class MailingService
 * @package CleverReachIntegration\BusinessLogic\Services\Mailing
 */
class MailingService implements DefaultMailingService
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Core Integration Mailing';
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return 'Core Integration Subject';
    }

    /**
     * @return MailingContent
     */
    public function getContent()
    {
        $content = new MailingContent();
        $content->setType('html/text');
        $content->setText('Core Integration-content text');
        $content->setHtml('Core Integration-content html');

        return $content;
    }

}