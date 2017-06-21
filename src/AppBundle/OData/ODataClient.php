<?php
/**
 * Created by PhpStorm.
 * User: stone
 * Date: 12/06/17
 * Time: 18:18
 */

namespace AppBundle\OData;


class ODataClient
{
    private $host,
            $user,
            $password;

    public function __construct($host = 'http://1c.gallery.kg', $username = 'Domosfera', $pass = 'spe#5wrA')
    {
        $this->host = $host . '/galuntu/ru_RU/odata/standard.odata/';
        $this->user = $username;
        $this->password = $pass;
    }

    public function makeRequest($route, $params)
    {
        $process = curl_init($this->host.$route. '?' . $params.'&$format=json');
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_USERPWD, $this->user . ":" . $this->password);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_POST, 0);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        $return = json_encode(json_decode(curl_exec($process))->value, JSON_UNESCAPED_UNICODE);
        curl_close($process);

        return $return;
    }

}