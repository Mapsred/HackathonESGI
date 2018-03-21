<?php
/**
 * This file is part of the SDMReporting Project
 *
 * (c) 2018 LiveXP <dev@livexp.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Utils;

use Sonata\IntlBundle\Templating\Helper\DateTimeHelper;

class Helper
{
    private static $englishMonths = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september',
        'october', 'november', 'décember'];
    private static $frenchMonths = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre',
        'octobre', 'novembre', 'décembre'];

    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return $length === 0 || (substr($haystack, -$length) === $needle);
    }

    /**
     * @param string $from
     * @param string $to
     * @param string $subject
     *
     * @return string
     */
    public static function strReplaceFirst($from, $to, $subject)
    {
        $from = '/' . preg_quote($from, '/') . '/';

        return preg_replace($from, $to, $subject, 1);
    }

    /**
     * @param string $stripAccents
     *
     * @return string
     */
    public static function stripAccents($stripAccents)
    {
        return str_replace(["é", "û"], ["e", "u"], $stripAccents);
    }

    /**
     * @param $date
     * @return string
     */
    public static function translateDate($date)
    {
        return str_replace(self::$frenchMonths, self::$englishMonths, strtolower($date));
    }

    /**
     * @param \DateTimeInterface $date
     * @param string $format
     * @return string
     */
    public static function formatDate(\DateTimeInterface $date, $format = "MMMM Y")
    {
        return str_replace(array_map('ucfirst', self::$englishMonths), array_map('ucfirst', self::$frenchMonths), $date->format($format));
    }

}