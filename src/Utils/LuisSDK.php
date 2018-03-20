<?php

namespace App\Utils;

/**
 * Class LuisSDK
 *
 * @author François MATHIEU <francois.mathieu@livexp.fr>
 */
class LuisSDK
{

    /** @var string url */
    private $url;

    /**
     * LuisSDK constructor.
     * @param $luisUrl
     * @param $luisKey
     */
    public function __construct($luisUrl, $luisKey)
    {
        $this->url = sprintf("%s?subscription-key=%s&verbose=true&timezoneOffset=0&q=", $luisUrl, $luisKey);
    }

    /**
     * Send a GET Request and return its result
     *
     * @param string $text
     * @return array
     */
    public function query($text)
    {
        $url = $this->url . $text;
        $url = str_replace(' ', '%20', $url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $data = curl_exec($ch);
        curl_close($ch);

        return json_decode($data, true);
    }
}