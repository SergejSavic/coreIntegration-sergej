<?php

namespace CleverReachIntegration\BusinessLogic\Services\Translation;

use CleverReach\BusinessLogic\Language\Contracts\TranslationService as TranslationInterface;
use PrestaShop\PrestaShop\Adapter\Entity\Language;

/**
 * Class TranslationService
 * @package CleverReachIntegration\BusinessLogic\Services\Translation
 */
class TranslationService implements TranslationInterface
{
    /**
     * @param string $string
     * @param array $arguments
     * @return string
     */
    public function translate($string, array $arguments = array())
    {
        return vsprintf($string, $arguments);
    }

    /**
     * @return string
     */
    public function getSystemLanguage()
    {
        global $cookie;
        return Language::getIsoById((int)$cookie->id_lang);
    }

}