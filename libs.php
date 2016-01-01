<?php
/**
 * User: dbroqua
 * Date: 01/01/16
 * Time: 14:26
 */

function getDateFr( $value , $type ){
    $arr = array(
        'day' => array( 'dimanche' , 'lundi' , 'mardi' , 'mercredi' , 'jeudi' , 'vendredi' , 'samedi' ),
        'month' => array( '' , 'janvier','février','mars','avril','mai','juin','juillet','aout','septembre','octobre','novembre','décembre')
    );
    return $arr[$type][$value];
}

function motion_web_admin( $uri ){
    $loginPassword = exec('grep "webcontrol_authentication" /etc/motion.conf|cut -d" " -f 2');
    $headers = array(
        'Authorization: Basic '. base64_encode($loginPassword)
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec ($ch);
    curl_close($ch);

    return $res;
}

function newestFirst($a, $b)
{
    return filemtime($b) - filemtime($a);
}
function oldestFirst($a, $b)
{
    return filemtime($a) - filemtime($b);
}