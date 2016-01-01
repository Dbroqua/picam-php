<?php
/**
 * User: dbroqua
 * Date: 01/01/16
 * Time: 14:47
 */
require_once "./conf.inc.php";

if( isset($_GET['filename']) && !empty($_GET['filename']) && file_exists($intrusion_directory.$_GET['filename']) ){
    $file = $intrusion_directory.$_GET['filename'];
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    $mimeType = 'application/octet-stream';
    switch( $extension ){
        case 'jpg':
            $mimeType = 'image/jpeg';
        break;
        case 'avi':
            $mimeType = 'video/x-msvideo';
        break;
    }

    header('Content-Description: File Transfer');
    header('Content-Type: '.$mimeType);
    header('Content-Disposition: attachment; filename='.basename($file) );
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);
    exit;
}