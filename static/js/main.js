/**
 * Created by dbroqua on 08/01/16.
 */

var config = [];
var lastState = [];

/**
 * Load template
 * @param arr
 * @param html
 * @returns {*}
 */
function parseSimple( arr, html ){
    var formated = html;

    $.each(arr, function (index, value) {
        formated = formated.split("[" + index + "]").join(value);
    });

    return formated;
}

/**
 * Function to get state of each camera
 */
function getState(){
    $.ajax({
        url: "actions.php?action=",
        type: 'GET',
        dataType: 'json',
        success: function( data ){
            $.each( data , function( index , value ){
                var detectionStateElt = $('div[data-id="'+index+'"] .detectionstate');
                if( value.run != lastState[index] ){
                    lastState[index] = value.run;
                    if( value.run === true ){
                        var d = new Date();
                        if( config[index].type == 'local' )                                {
                            $('div[data-id="'+index+'"] .cam').attr('src','/cam?'+d.getTime());
                        }else{
                            $('div[data-id="'+index+'"] .cam').attr('src','./getCam.php?camId='+index+'&dateTime='+d.getTime());
                        }
                    }else{
                        $('div[data-id="'+index+'"] .cam').attr('src','static/img/no_webcam.png');
                    }
                }

                if( value.run === true ){
                    $('div[data-id="'+index+'"] .state').html('running <span class="glyphicon glyphicon-ok alert-success" aria-hidden="true"></span>');
                }else{
                    $('div[data-id="'+index+'"] .state').html('not running <span class="glyphicon glyphicon-remove alert-danger" aria-hidden="true"></span>');
                }

                switch ( value.detectionstate ){
                    case 'active':
                        detectionStateElt.html('Active <span class="glyphicon glyphicon-ok alert-success" aria-hidden="true"></span>');
                        break;
                    case 'pause':
                        detectionStateElt.html('Pause <span class="glyphicon glyphicon-pause alert-warning" aria-hidden="true"></span>');
                        break;
                    default:
                        detectionStateElt.html('Pause <span class="glyphicon glyphicon-remove alert-danger" aria-hidden="true"></span>');
                }

                $('div[data-id="'+index+'"] .lastedetection').html(value.lastedetection);
                $('div[data-id="'+index+'"] .lastrun').html(value.lastrun);
                $('div[data-id="'+index+'"] .laststart').html(value.laststart);
            });

            setTimeout( getState , 3000 );
        }
    });
}

/**
 * Function to load UI for each camera
 */
function loadPage(){
    $.ajax({
        dataType: "json",
        url: "./conf.json",
        success: function( data ){
            config = data;
            $.each( data , function( index , value ){
                value.id = index;
                $('.main').append( parseSimple( value , $('#template').html()));
            });
            getState();

            setEventActions();
            setEventFiles();
        }
    });
}

/**
 * Function to set a Event on each actions
 */
function setEventActions(){
    $('.action').on('click',function(e){
        e.preventDefault();
        var camId = $(this).parents('div.PiCam').data('id');
        $.ajax({
            url: "actions.php?action="+$(this).data('state')+"&camId="+camId,
            dataType: 'json'
        });
    });
}

/**
 * Function to show saved files for one Camera
 */
function setEventFiles(){
    $('.viewCaptured').on('click',function(e){
        e.preventDefault();
        var camId = $(this).parents('div.PiCam').data('id');
        $.ajax({
            url: "actions.php?action=getFiles&camId="+camId,
            dataType: 'json',
            success: function( data ){
                var list = $('div[data-id="'+camId+'"] .captured .list');
                var fileslist = '';
                list.html("");
                if( data.length > 0 ){
                    $.each( data , function( index , value ){
                        fileslist+= '<tr>' +
                            '   <td>'+index+'</td>' +
                            '   <td><a href="getFile.php?filename='+value.filename+'&camId='+camId+'"><img src="static/img/'+( value.filetype == 'jpg' ? 'image' : 'movie' )+'.png"></a></td>' +
                            '   <td><a href="getFile.php?filename='+value.filename+'&camId='+camId+'" data-file='+value.filename+' data-type='+value.filetype+' class="preview">'+value.filename+'</a></td>' +
                            '   <td>'+value.date+' à '+value.time+'</td>' +
                            '</tr>';
                    });

                    list.html( '' +
                        '<div class="btn-group pull-right">' +
                        '   <input type="search" class="search form-control" placeholder="Search" />' +
                        '   <span class="glyphicon glyphicon-remove-circle searchclear"></span>' +
                        '</div>' +
                        '<span class="counter pull-right"></span>' +
                        '<div class="savedFiles">' +
                        '   <table class="table table-striped table-hover table-condensed results">' +
                        '       <thead>' +
                        '           <tr>' +
                        '               <th>#</th>' +
                        '               <th>Type</th>' +
                        '               <th>Name</th>' +
                        '               <th>Date</th>' +
                        '           </tr>' +
                        '           <tr class="warning no-result">' +
                        '               <td colspan="4"><i class="fa fa-warning"></i> No result</td>' +
                        '           </tr>' +
                        '       </thead>' +
                        '       <tbody>' +
                        fileslist +
                        '       </tbody>' +
                        '   </table>' +
                        '</div>'
                    );
                }
                $('div[data-id="'+camId+'"] .captured').show("slow");

                $('div[data-id="'+camId+'"] .closeCaptured').off('click').on('click',function(e){
                    e.preventDefault();
                    $('div[data-id="'+camId+'"] .captured').hide("slow");
                    $('div[data-id="'+camId+'"] .search').off('keyup');
                    $('div[data-id="'+camId+'"] .preview').off('click');
                    $('div[data-id="'+camId+'"] .searchclear').off('click');
                });

                $('div[data-id="'+camId+'"] .search').off('keyup').on('keyup',function () {
                    searchFile(camId);
                });

                $('div[data-id="'+camId+'"] .searchclear').off('click').on('click',function(){
                    $(".search").val('');
                    searchFile(camId);
                });

                $('div[data-id="'+camId+'"] .preview').off('click').on("click", function(e) {
                    if( $(this).data('type') == "jpg" ){
                        e.preventDefault();
                        $('#myModalLabel').html($(this).data('file') );
                        $('#imagemodal .modal-body').html('<img src="'+$(this).attr('href')+'&view=true&camId='+camId+'" id="imagepreview" class="img-responsive img-rounded" >');
                        $('#imagemodal').modal('show');
                    }
                });
            }
        });
    });
}

/**
 * Function to search saved files
 * @param camId
 */
function searchFile( camId ){
    var searchTerm = $('div[data-id="'+camId+'"] .search').val();
    var searchSplit = searchTerm.replace(/ /g, "'):containsi('");

    $.extend($.expr[':'], {'containsi': function(elem, i, match){
        return (elem.textContent || elem.innerText || '').toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
    }
    });

    $('div[data-id="'+camId+'"] .results tbody tr').not(":containsi('" + searchSplit + "')").each(function(){
        $(this).attr('visible','false');
    });

    $('div[data-id="'+camId+'"] .results tbody tr:containsi("' + searchSplit + '")').each(function(){
        $(this).attr('visible','true');
    });

    var jobCount = $('div[data-id="'+camId+'"] .results tbody tr[visible="true"]').length;
    $('div[data-id="'+camId+'"] .counter').text(jobCount + ' item'+(Number(jobCount) > 1 ? 's' : ''));

    if( jobCount === 0 ) {$('div[data-id="'+camId+'"] .no-result').show();}
    else {$('div[data-id="'+camId+'"] .no-result').hide();}
}

$( document ).ready(function() {
    loadPage();
});