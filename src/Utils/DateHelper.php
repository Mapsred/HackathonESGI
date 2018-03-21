<?php


namespace App\Utils;

/**
 * Class DateHelper
 *
 * @author François MATHIEU <francois.mathieu@livexp.fr>
 */
class DateHelper
{
    /** @var array $dateDayFormats */
    private static $dateDayFormats = [
        'd',
    ];

    /** @var array $dateMonthFormats */
    private static $dateMonthFormats = [
        'd/m',
        'j F H:i',
        'd F \à H\h',
        'd F \à H\hi',
        'd F H\h',
        'd F H\hi',
        'd F \à H\h',
        'd F \à H\hi',
        'd F H\h',
        'd F H\hi',
    ];

    /** @var array $dateFormats */
    private static $dateFormats = [
        'Y-m-d',
        'Y-m-d H',
        'Y-m-d H:i',
        'Y-m-d H:i:s',
        'Y/m/d',
        'Y/m/d H',
        'Y/m/d H:i',
        'Y/m/d H:i:s',
        'd/m/Y',
        'd/m/Y H',
        'd/m/Y H:i',
        'd/m/Y H:i:s',
        'd-m-Y',
        'd-m-Y H',
        'd-m-Y H:i',
        'd-m-Y H:i:s',
    ];

    /**
     * @param string $time
     * @return bool|\DateTime|null
     */
    public static function getDate($time)
    {
        $time = Helper::strReplaceFirst('le ', '', $time);
        $dateFormats = array_merge(self::$dateDayFormats, self::$dateFormats, self::$dateMonthFormats);
        $date = null;
        foreach ($dateFormats as $format) {
            $date = \DateTime::createFromFormat($format, $time);
            if ($date instanceof \DateTime) {
                return self::manageFormat($date, $format);
            }
        }

        return null;
    }

    /**
     * @param \DateTime $date
     * @param $format
     * @return \DateTime
     */
    private static function manageFormat(\DateTime $date, $format)
    {
        $currentDate = new \DateTime();
        if (in_array($format, self::$dateDayFormats)) {
            $date->setDate($currentDate->format('Y'), $currentDate->format('m'), $date->format('d'));
        } elseif (in_array($format, self::$dateMonthFormats)) {
            $date->setDate($currentDate->format('Y'), $date->format('m'), $date->format('d'));
        }

        return $date;
    }
}