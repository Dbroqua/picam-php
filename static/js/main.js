/**
 * Created by dbroqua on 01/01/16.
 */

var lastState = null;

function state(){
    $.ajax({
        url: "actions.php?action=",
        dataType: 'json',
        success: function( data ){
            var detectionStateElt = $('.detectionstate');
            if( data.run != lastState ){
                lastState = data.run;
                if( data.run === true ){
                    var d = new Date();
                    $('#cam1').attr('src','/cam?'+d.getTime());
                }else{
                    $('#cam1').attr('src','static/img/no_webcam.png');
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

function searchFile(){
    var searchTerm = $(".search").val();
    var listItem = $('.results tbody').children('tr');
    var searchSplit = searchTerm.replace(/ /g, "'):containsi('")

    $.extend($.expr[':'], {'containsi': function(elem, i, match, array){
        return (elem.textContent || elem.innerText || '').toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
    }
    });

    $(".results tbody tr").not(":containsi('" + searchSplit + "')").each(function(e){
        $(this).attr('visible','false');
    });

    $(".results tbody tr:containsi('" + searchSplit + "')").each(function(e){
        $(this).attr('visible','true');
    });

    var jobCount = $('.results tbody tr[visible="true"]').length;
    $('.counter').text(jobCount + ' item'+(Number(jobCount) > 1 ? 's' : ''));

    if(jobCount == '0') {$('.no-result').show();}
    else {$('.no-result').hide();}
}

$( document ).ready(function() {
    state();
    setInterval(function(){
        state();
    }, 3000 );

    $('.action').click(function(e){
        e.preventDefault();
        $.ajax({
            url: "actions.php?action="+$(this).data('state'),
            dataType: 'json'
        });
    });

    $('.closeCaptured').on('click',function(e){
        e.preventDefault();
        $('.captured').hide("slow");
        $(".search").off('keyup');
        $(".preview").off('click');
        $("#searchclear").off('click');
    });
    $('.viewCaptured').on('click',function(e){
        e.preventDefault();
        $.ajax({
            url: "actions.php?action=getFiles",
            dataType: 'json',
            success: function( data ){
                var list = $('.captured .list');
                var fileslist = '';
                list.html("");
                if( data.length > 0 ){
                    $.each( data , function( index , value ){
                        fileslist+= '<tr>' +
                        '   <td>'+index+'</td>' +
                        '   <td><a href="getFile.php?filename='+value.filename+'"><img src="static/img/'+( value.filetype == 'jpg' ? 'image' : 'movie' )+'.png"></a></td>' +
                        '   <td><a href="getFile.php?filename='+value.filename+'" data-file='+value.filename+' data-type='+value.filetype+' class="preview">'+value.filename+'</a></td>' +
                        '   <td>'+value.date+' à '+value.time+'</td>' +
                        '</tr>';
                    });

                    list.html( '' +
                        '<div class="btn-group pull-right">' +
                        '   <input type="search" class="search form-control" placeholder="Search" />' +
                        '   <span id="searchclear" class="glyphicon glyphicon-remove-circle"></span>' +
                        '</div>' +
                        '<span class="counter pull-right"></span>' +
                        '<table class="table table-striped table-hover table-condensed results">' +
                        '<thead>' +
                        '   <tr>' +
                        '       <th>#</th>' +
                        '           <th>Type</th>' +
                        '           <th>Name</th>' +
                        '           <th>Date</th>' +
                        '       </tr>' +
                        '       <tr class="warning no-result">' +
                        '           <td colspan="4"><i class="fa fa-warning"></i> No result</td>' +
                        '       </tr>' +
                        '</thead>' +
                        '<tbody>' +
                        fileslist +
                        '</tbody>' +
                        '</table>' );
                }
                $('.captured').show("slow");


                $(".search").on('keyup',function () {
                    searchFile();
                });

                $("#searchclear").on('click',function(){
                    $(".search").val('');
                    searchFile();
                });

                $(".preview").on("click", function(e) {
                    if( $(this).data('type') == "jpg" ){
                        e.preventDefault();
                        $('#myModalLabel').html($(this).data('file') );
                        $('#imagemodal .modal-body').html('<img src="'+$(this).attr('href')+'&view=true" id="imagepreview" class="img-responsive img-rounded" >');
                        $('#imagemodal').modal('show');
                    }
                });
            }
        });


    });
});