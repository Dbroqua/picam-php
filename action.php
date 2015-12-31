<?php
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
if( isset($_GET['action']) ){
    switch( $_GET['action'] ){
        case 'start':
            exec('sudo /etc/init.d/motion start');
        break;
        case 'stop':
            exec('sudo /etc/init.d/motion stop');
        break;
        case "start_detection":
        case "stop_detection":
            $uri = 'http://127.0.0.1:8081/0/detection/'.( $_GET['action'] == 'start_detection' ? 'start' : 'pause' );
            motion_web_admin( $uri );
        break;
        default:
            $lastedetection = 'Never';
            $laststart = 'Unknown';
            $pid_file = '/var/run/motion/motion.pid';
            $intrusion_file = '/media/freebox/intrusion.date';
            $detectionstate = 'off';

            $res = false;
            if( is_file( $pid_file ) ){
                $res = true;
                $laststart = date("Y-m-d H:i:s",exec('stat -c %Y '.$pid_file));
                $detectionstate = motion_web_admin( 'http://127.0.0.1:8081/0/detection/status' );
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
