<?php
/**
 * User: dbroqua
 * Date: 01/01/16
 * Time: 14:47
 */
require_once "./libs/libs.php";
require_once "./libs/ProxyHandler.class.php";

$str = file_get_contents('./conf.json');
$json = json_decode($str, true);        

if( isset($_GET['camId']) ){

    $proxy = new ProxyHandler(array(
        'proxyUri' => $json[$_GET['camId']]['scheme'].$json[$_GET['camId']]['login'].':'.$json[$_GET['camId']]['password'].'@'.$json[$_GET['camId']]['host'].':'.$json[$_GET['camId']]['port'].'/cam/',
        'requestUri' => ''
    ));

    // Check for a success
    if ($proxy->execute()) {
    print_r($proxy->getCurlInfo()); // Uncomment to see request info
    } else {
        echo $proxy->getCurlError();
    }

    $proxy->close();    
}