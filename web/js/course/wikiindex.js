/**
 * Created by Administrator on 2016/3/15.
 */
$(function(){
    $('[data-toggle="tooltip"]').tooltip();
    $('.foldbtn').click(function(){
        if($(this).attr('folded')=="false"){
            $(this).attr('folded','true');
            $(this).removeClass('glyphicon-resize-full');
            $(this).addClass('glyphicon-resize-large');
            $(this).parent().siblings("div").fadeOut();
        }
        else{
            $(this).attr('folded','false');
            $(this).removeClass('glyphicon-resize-full');
            $(this).addClass('glyphicon-resize-small');
            $(this).parent().siblings("div").fadeIn();
        }
    });
    $('#focuswikisubmit').click(function(){
        $('#wiki-form').submit();
    });
    $('.newwiki').click(function(){
        $('#operateID').val('create');
        $('#wiki-id').val('');
        $('#wiki-title').val('');
        $('#wiki-detail').val('');
        $('#wiki-tag').val('');
        $('#WikiModal').modal('show');
    });
    $('.editbtn').click(function(){
        $('#operateID').val('edit');
        var wikiid = $(this).parent().parent().attr('wikiid');
        $('#wiki-id').val(wikiid);
        $('#wiki-title').val($(this).siblings('.panel-title').text());
        $('#wiki-detail').val($(this).parent().siblings('.panel-body').text());
        $('#wiki-tag').val($(this).parent().siblings('.panel-tag').text());
        $('#WikiModal').modal('show');
    });
    $('.deletebtn').click(function(){
        $('#operateID').val('delete');
        var wikiid = $(this).parent().parent().attr('wikiid');
        $('#wiki-id').val(wikiid);
        $('#wiki-title').val($(this).siblings('.panel-title').text());
        $('#wiki-detail').val($(this).parent().siblings('.panel-body').text());
        $('#wiki-tag').val($(this).parent().siblings('.panel-tag').text());
        $('#wiki-form').submit();
    });
    $('.favorbtn').click(function(){
        $.ajax({
            url:"/course/wiki/favor",
            data:{
                'wikiid':$(this).parent().parent().attr('wikiid')
            },
            dataType:'json',
            success:function(data){
                var wikiid = data.wikiid;
                var favor = data.favor;
                $("[wikiid="+wikiid+"]").find(".glyphicon-heart").text(favor);
            }
        });
    });
    $('span.tag').click(function(){
        window.location = '/course/wiki/index?query='+$(this).text();
    });
});