<?php
/**
 * User: dbroqua
 * Date: 01/01/16
 * Time: 14:47
 */
require_once "./libs/libs.php";

$str = file_get_contents('./conf.json');
$json = json_decode($str, true);        

if( isset($_GET['filename']) && !empty($_GET['filename']) ){

    if( $json[$_GET['camId']]['type'] == 'local' )
    {
        if( file_exists($json[$_GET['camId']]['intrusion_directory'].$_GET['filename']) )
        {
            $file = $json[$_GET['camId']]['intrusion_directory'].$_GET['filename'];
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

            if( isset($_GET['view']) && $_GET['view'] == true ){
                $fp = fopen( $file , 'rb' );

                // send the right headers
                header("Content-Type: ".$mimeType);
                header("Content-Length: " . filesize($file));

                // dump the picture and stop the script
                fpassthru($fp);
                exit;
            }else{
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
        }
    }else{
        $filename = $_GET['filename'];
        $extension = explode('.',$filename);
        $extension = $extension[1];
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
        header('Content-Disposition: attachment; filename='.$file );
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        
        echo _ajax( $json[$_GET['camId']]['scheme'].$json[$_GET['camId']]['host'].':'.$json[$_GET['camId']]['port'].'/getFile.php?filename='.$_GET['filename'] , $json[$_GET['camId']]['login'] , $json[$_GET['camId']]['password']);

        exit;
    }
    
}