<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author PrestaShop SA <contact@prestashop.com>
 * @copyright  2007-2019 PrestaShop SA
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace CleverReachIntegration\BusinessLogic\Services\Transformer;

/**
 * Class TransformerService
 * @package CleverReachIntegration\BusinessLogic\Services\Transformer
 */
class TransformerService
{
    /**
     * @param $number
     * @return mixed|string|void
     */
    public static function transformNumberToString($number)
    {
        $numberLength = \Tools::strlen((string)$number);
        switch ($numberLength) {
            case 1:
                return '0000000000'. $number;
            case 2:
                return '000000000'. $number;
            case 3:
                return '00000000'. $number;
            case 4:
                return '0000000'. $number;
            case 5:
                return '000000'. $number;
            case 6:
                return '00000'. $number;
            case 7:
                return '0000'. $number;
            case 8:
                return '000'. $number;
            case 9:
                return '00'. $number;
            case 10:
                return '0'. $number;
            case 11:
                return $number;
        }
    }
}
