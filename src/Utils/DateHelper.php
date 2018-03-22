<?php


namespace App\Utils;

/**
 * Class DateHelper
 *
 * @author François MATHIEU <francois.mathieu@livexp.fr>
 */
class DateHelper
{
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
        'd F',
    ];

    /**
     * @var array noTimeFormats
     */
    private static $noTimeFormats = [
        'd/m',
        'd F',
    ];

    /** @var array $dateFormats */
    private static $dateFormats = [
        'd F Y \à H\hi',
        'd F Y \à H\h',
    ];

    /**
     * @param string $time
     * @return bool|\DateTime|null
     */
    public static function getDate($time)
    {
        $time = Helper::strReplaceFirst('le ', '', $time);
        $time = Helper::strReplaceFirst('du ', '', $time);

        $dateFormats = array_merge(self::$dateFormats, self::$dateMonthFormats, self::$noTimeFormats);
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
        if (in_array($format, self::$dateMonthFormats)) {
            $date->setDate($currentDate->format('Y'), $date->format('m'), $date->format('d'));
        }

        if (in_array($format, self::$noTimeFormats)) {
            $date->setTime(0, 0, 0, 0);
        }

        return $date;
    }
}