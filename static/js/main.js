/**
 * Created by dbroqua on 01/01/16.
 */

var lastState = null;

function state(){
    $.ajax({
        url: "/actions.php?action=",
        dataType: 'json',
        success: function( data ){
            var detectionStateElt = $('.detectionstate');
            if( data.run != lastState ){
                lastState = data.run;
                if( data.run === true ){
                    var d = new Date();
                    $('#cam1').attr('src','/cam?'+d.getTime());
                }else{
                    $('#cam1').attr('src','/static/img/no_webcam.png');
                }
            }

            if( data.run === true ){
                $('.state').html('running <span class="glyphicon glyphicon-ok alert-success" aria-hidden="true"></span>');
            }else{
                $('.state').html('not running <span class="glyphicon glyphicon-remove alert-danger" aria-hidden="true"></span>');
            }
            switch ( data.detectionstate ){
                case 'active':
                    detectionStateElt.html('Active <span class="glyphicon glyphicon-ok alert-success" aria-hidden="true"></span>');
                    break;
                case 'pause':
                    detectionStateElt.html('Pause <span class="glyphicon glyphicon-pause alert-warning" aria-hidden="true"></span>');
                    break;
                default:
                    detectionStateElt.html('Pause <span class="glyphicon glyphicon-remove alert-danger" aria-hidden="true"></span>');
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
            url: "/actions.php?action="+$(this).data('state'),
            dataType: 'json'
        });
    });
});