<?php
if( isset($_GET['action']) ){
        switch( $_GET['action'] ){
                case 'start':
                        exec('sudo /etc/init.d/motion start');
                        break;
                case 'stop':
                        exec('sudo /etc/init.d/motion stop');
                        break;
                default:
                        $lastedetection = 'Never';
                        $laststart = 'Unknown';
                        $pid_file = '/var/run/motion/motion.pid';
                        $intrusion_file = '/media/freebox/intrusion.date';
                  
                        $res = false;
                        if( is_file( $pid_file ) ){
                                $res = true;
                                $laststart = date("Y-m-d H:i:s",exec('stat -c %Y '.$pid_file));
                        }
                        if( is_file($intrusion_file) ){
                                $date = file_get_contents($intrusion_file);
                                $lastedetection = date('Y-m-d H:i:s',$date);
                        }
                        $test = array('run'=>$res,'lastedetection'=>$lastedetection,'laststart'=>$laststart,'lastrun'=>date('Y-m-d H:i:s'));
                        echo json_encode($test);
        }
}
