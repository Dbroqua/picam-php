<!DOCTYPE html>                                                                                                                                                                                                                                                                
<html lang="en">                                                                                                                                                                                                                                                               
  <head>                                                                                                                                                                                                                                                                       
    <meta charset="utf-8">                                                                                                                                                                                                                                                     
    <meta http-equiv="X-UA-Compatible" content="IE=edge">                                                                                                                                                                                                                      
    <meta name="viewport" content="width=device-width, initial-scale=1">                                                                                                                                                                                                       
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->                                                                                                                                                          
    <title>PiCam</title>                                                                                                                                                                                                                                                 
                                                                                                                                                                                                                                                                               
    <!-- Latest compiled and minified CSS -->                                                                                                                                                                                                                                  
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">                                   
                                                                                                                                                                                                                                                                               
    <!-- Optional theme -->                                                                                                                                                                                                                                                    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" integrity="sha384-aUGj/X2zp5rLCbBxumKTCw2Z50WgIr1vs/PFN4praOTvYXWlVyh2UtNUU0KAUhAX" crossorigin="anonymous">                                                     
                                                                                                                                                                                                                                                                               
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
                        <div class="col-xs-9">
                                <h2>Living room</h2>
                        </div>
                        <div class="col-xs-3 text-right">
                                <button type="button" data-state="start" class="btn btn-primary">Run Motion</button>
                                <button type="button" data-state="stop" class="btn btn-danger">Stop Motion</button>
                        </div>
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
                                <img src="http://192.168.1.6/cam/" alt="Cam" title="Live" class="img-responsive  img-rounded" id="Living room" />
                        </div>
                </div>
        </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>

    <script type="text/javascript">
        var firstLoad = true;
        function state(){
                $.ajax({
                        url: "/action.php?action=",
                        dataType: 'json',
                        success: function( data ){
                                console.log( data.run );
                                if( data.run === true ){
                                        $('.state').html('running <span class="glyphicon glyphicon-ok alert-success" aria-hidden="true"></span>');
                                }else{
                                        $('.state').html('not running <span class="glyphicon glyphicon-remove alert-danger" aria-hidden="true"></span>');
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

                $('.btn').click(function(){
                        var action = $(this).data('state');

                        $.ajax({
                                url: "/action.php?action="+action,
                                dataType: 'json',
                                success: function( data ){
                                }
                        });
                });
        });
    </script>
  </body>
</html>
