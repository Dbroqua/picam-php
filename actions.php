<?php
require_once "./conf.inc.php";
require_once "./libs.php";

if( isset($_GET['action']) ){
    switch( $_GET['action'] ){
        case 'start':
            exec($service.' start');
        break;
        case 'stop':
            exec($service.' stop');
        break;
        case "start_detection":
        case "stop_detection":
            $uri = $base_uri.( $_GET['action'] == 'start_detection' ? 'start' : 'pause' );
            motion_web_admin( $uri );
        break;
        case 'getFiles':
            $res = array();


            $dir = glob($intrusion_directory.'/*.{jpg,avi}',GLOB_BRACE); // put all files in an array
            uasort($dir, "newestFirst"); // sort the array by calling newest()

            foreach($dir as $file)
            {
                $date = filemtime( $file );
                $res[] = array(
                    'filename' => basename($file),
                    'filetype' => pathinfo($file, PATHINFO_EXTENSION),
                    'date' => getDateFr(date('w',$date),'day').' '.date('d' , filemtime( $file ) ).' '.getDateFr( date('n' , filemtime( $file ) ), 'month' ).' '.date('Y' , filemtime( $file ) ),
                    'time' => date('h:i' , filemtime( $file ) )
                );
            }
            echo json_encode($res);
        break;
        default:
            $lastedetection = 'Never';
            $laststart = 'Unknown';            
            $detectionstate = 'off';

            $res = false;
            if( is_file( $pid_file ) ){
                $res = true;
                $laststart = date("Y-m-d H:i:s",exec('stat -c %Y '.$pid_file));
                $detectionstate = motion_web_admin( $base_uri.'status' );
                if( strpos( $detectionstate , 'ACTIVE' ) > 0 ){
                    $detectionstate = 'active';
                }else{
                    $detectionstate = 'pause';
                }
            }
            if( is_file($intrusion_file) ){
                $date = file_get_contents($intrusion_file);
                $lastedetection = date('Y-m-d H:i:s',$date);
            }
            $test = array(
                'run' => $res,
                'lastedetection' => $lastedetection,
                'laststart' => $laststart,
                'lastrun' => date('Y-m-d H:i:s'),
                'detectionstate' => $detectionstate
            );
            echo json_encode($test);
    }
}
