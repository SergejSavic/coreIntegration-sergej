<?php

namespace CleverReachIntegration\BusinessLogic\Services;

/**
 * Class TransformerService
 * @package CleverReachIntegration\BusinessLogic\Services
 */
class TransformerService
{
    /**
     * @param $number
     * @return mixed|string|void
     */
    public static function transformNumberToString($number)
    {
        $numberLength = strlen((string)$number);
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