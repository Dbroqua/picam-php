<?php
require_once "./libs/libs.php";


if( isset($_GET['action']) ){
    $str = file_get_contents('./conf.json');
    $json = json_decode($str, true);        
    switch( $_GET['action'] ){
        case 'start':
        case 'stop':
            if( $json[$_GET['camId']]['type'] == 'local' ) // Cam is on the same computer
            {
                exec($json[$_GET['camId']]['service'].' '.$_GET['action']);
            }else{ // Cam is on an other computer, call /actions.php of the distant server
                _ajax( $json[$_GET['camId']]['scheme'].$json[$_GET['camId']]['host'].':'.$json[$_GET['camId']]['port'].'/actions.php?action='.$_GET['action'] , $json[$_GET['camId']]['login'] , $json[$_GET['camId']]['password'] );
            }
        break;
        case "start_detection":
        case "stop_detection":
            if( $json[$_GET['camId']]['type'] == 'local' )
            {
                $uri = $base_uri.( $_GET['action'] == 'start_detection' ? 'start' : 'pause' );
                $loginPassword = explode(':',exec('grep "webcontrol_authentication" /etc/motion.conf|cut -d" " -f 2'));
                $login = $loginPassword[0];
                $password = $loginPassword[1];

            }else{
                $uri = $json[$_GET['camId']]['scheme'].$json[$_GET['camId']]['host'].':'.$json[$_GET['camId']]['port'].'/actions.php?action='.$_GET['action'];
                $login = $json[$_GET['camId']]['login'];
                $password = $json[$_GET['camId']]['password'];
            }
            _ajax( $uri , $login , $password );
        break;
        case 'getFiles':
            $res = array();

            if( $json[$_GET['camId']]['type'] == 'local' )
            {
                $dir = glob( $json[$_GET['camId']]['intrusion_directory'].'/*.{jpg,avi}',GLOB_BRACE); // put all files in an array
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

                $res = json_encode($res);

            }else{
                $uri = $json[$_GET['camId']]['scheme'].$json[$_GET['camId']]['host'].':'.$json[$_GET['camId']]['port'].'/actions.php?action='.$_GET['action'];
                $login = $json[$_GET['camId']]['login'];
                $password = $json[$_GET['camId']]['password'];
                $res = _ajax( $uri , $login , $password );
            }
            
            echo $res;
        break;
        default:
            $res = [];

            foreach ($json as $key => $row) {
                if( $row['type'] == 'net' )
                {
                    $res[] = json_decode( _ajax( $row['scheme'].$row['host'].':'.$row['port'].'/actions.php?action=' , $row['login'] , $row['password'] ) , true);
                }else{
                    $lastedetection = 'Never';
                    $laststart = 'Unknown';
                    $detectionstate = 'off';

                    $run = false;
                    if( is_file( $row['pid_file'] ) ){
                        $res = true;
                        $laststart = date("Y-m-d H:i:s",exec('stat -c %Y '.$row['pid_file']));
                        $loginPassword = explode( ':' , exec('grep "webcontrol_authentication" /etc/motion.conf|cut -d" " -f 2') );

                        $detectionstate = _ajax( $row['scheme'].$row['motionAdminUri'].':'.$row['port'].'/actions.php?action=' , $loginPassword[0] , $loginPassword[1] );
                        if( strpos( $detectionstate , 'ACTIVE' ) > 0 ){
                            $detectionstate = 'active';
                        }else{
                            $detectionstate = 'pause';
                        }
                    }
                    if( is_file( $row['intrusion_file'] ) ){
                        $date = file_get_contents($row['intrusion_file']);
                        $lastedetection = date('Y-m-d H:i:s',$date);
                    }
                    $localData = array(
                        'run' => $run,
                        'lastedetection' => $lastedetection,
                        'laststart' => $laststart,
                        'lastrun' => date('Y-m-d H:i:s'),
                        'detectionstate' => $detectionstate
                    );

                    $res[] = $localData;
                }
            }
            echo json_encode($res);
    }
}
