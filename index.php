<?php
namespace pixeless;
require_once('defines.php');

//$data['user'] = $this->user;
        //$data['pass'] = $this->pass;
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_URL, API_ENDPOINT);
//curl_setopt($ch, CURLOPT_POST, true);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5); 
curl_setopt($ch, CURLOPT_TIMEOUT, 5); //timeout in seconds
$retorno = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
print_r($retorno);
echo $httpCode;
