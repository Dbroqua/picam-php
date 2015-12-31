<!DOCTYPE html>                                                                                                                                                                                                                                                                
<html lang="en">                                                                                                                                                                                                                                                               
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>PiCam</title>
        <link rel="icon" type="image/png" href="/favicon.png" />

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" integrity="sha384-aUGj/X2zp5rLCbBxumKTCw2Z50WgIr1vs/PFN4praOTvYXWlVyh2UtNUU0KAUhAX" crossorigin="anonymous">

        <style type="text/css">
            .clear{clear: both;}
        </style>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <div class="container">
            <div class="main">
                <h1>PiCam</h1>
                <div class="col-xs-11">
                    <h2>Living room</h2>
                </div>
                <div class="col-xs-1">
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            Actions
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                            <li><a href="#" data-state="start" class="action"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Run Motion</a></li>
                            <li><a href="#" data-state="start_detection" class="action"><span class="glyphicon glyphicon-play " aria-hidden="true"></span> Start detection</a></li>
                            <li><a href="#" data-state="stop_detection" class="action"><span class="glyphicon glyphicon-stop " aria-hidden="true"></span> Stop detection</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#" data-state="stop" class="action"><span class="glyphicon glyphicon-off " aria-hidden="true"></span> Stop Motion</a></li>
                        </ul>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="col-xs-12 col-sm-4">
                    <div class="col-xs-12">
                        <table class="table table-bordered table-striped table-hover table-condensed">
                            <thead>
                            <tr>
                                <th class="col-sm-5">Information</th>
                                <th>Value</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>State</td>
                                <td class="state"></td>
                            </tr>
                            <tr>
                                <td>Detection state</td>
                                <td class="detectionstate"></td>
                            </tr>
                            <tr>
                                <td>Last detection</td>
                                <td class="lastedetection"></td>
                            </tr>
                            <tr>
                                <td>Started at</td>
                                <td class="laststart"></td>
                            </tr>
                            <tr>
                                <td>Last run</td>
                                <td class="lastrun"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-8">
                    <img src="/cam" alt="Cam" title="Live" class="img-responsive  img-rounded" id="cam1" />
                </div>
            </div>
        </div>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>

        <script type="text/javascript">
            var lastState = null;

            function state(){
                $.ajax({
                    url: "/action.php?action=",
                    dataType: 'json',
                    success: function( data ){
                        if( data.run != lastState ){
                            lastState = data.run;
                            if( data.run === true ){
                                $('#cam1').attr('src','/cam');
                            }else{
                                $('#cam1').attr('src','no_webcam.png');
                            }
                        }

                        if( data.run === true ){
                            $('.state').html('running <span class="glyphicon glyphicon-ok alert-success" aria-hidden="true"></span>');
                        }else{
                            $('.state').html('not running <span class="glyphicon glyphicon-remove alert-danger" aria-hidden="true"></span>');
                        }
                        switch ( data.detectionstate ){
                            case 'active':
                                $('.detectionstate').html('Active <span class="glyphicon glyphicon-ok alert-success" aria-hidden="true"></span>');
                                break;
                            case 'pause':
                                $('.detectionstate').html('Pause <span class="glyphicon glyphicon-pause alert-warning" aria-hidden="true"></span>');
                                break;
                            default:
                                $('.detectionstate').html('Pause <span class="glyphicon glyphicon-remove alert-danger" aria-hidden="true"></span>');
                        }
                        $('.lastedetection').html(data.lastedetection);
                        $('.lastrun').html(data.lastrun);
                        $('.laststart').html(data.laststart);
                    }
                });
            }

            $( document ).ready(function() {
                state();
                setInterval(function(){
                    state();
                }, 3000 );

                $('.action').click(function(e){
                    e.preventDefault();
                    $.ajax({
                        url: "/action.php?action="+$(this).data('state'),
                        dataType: 'json'
                    });
                });
            });
        </script>
    </body>
</html>
