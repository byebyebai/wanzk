$(function(){

	$("#yx_xz .menu1 a").each(function(e){
    
    $(this).click(function(){
        add_class($(this));
        if($_GET['keywords1']!=undefined){
            toUrl("keywords="+$(this).html(),'keywords1='+decodeURIComponent($_GET['keywords1']));
        }else if($_GET['address']!=undefined){
            toUrl("keywords="+$(this).html(),'address='+decodeURIComponent($_GET['address']));
        }else{
            toUrl("keywords="+$(this).html(),'');
        } 
    })
})
/**
 * [description] 专业
 * @param  {[type]} e){                 $(this).click(function(){        add_class($(this));        if($_GET['keywords']! [description]
 * @return {[type]}      [description]
 */
$("#yx_xz .menu2 a").each(function(e){
    $(this).click(function(){
        add_class($(this));
        if($_GET['keywords']!=undefined){
            toUrl("keywords="+decodeURIComponent($_GET['keywords']),'keywords1='+$(this).html())
        }else{
            toUrl('',"keywords1="+$(this).html());
        }
    })
})
$("#yx_xz .menu3 a").each(function(e){
    $(this).click(function(){
        add_class($(this));
        if($_GET['keywords']!=undefined){
            toUrl("keywords="+decodeURIComponent($_GET['keywords']),'address='+$(this).attr('data-id'))
        }else{
            toUrl('',"address="+$(this).attr('data-id'));
        }
    })
})
//判断get地址参数
if($_GET['keywords']!= undefined){
    $("#yx_xz .menu1 a").each(function(e){
        if($(this).html() == decodeURIComponent($_GET['keywords'])){
            add_class($(this));
        }
    })
}

if($_GET['keywords1']!= undefined){
    $("#yx_xz .menu2 a").each(function(e){
        if($(this).html() == decodeURIComponent($_GET['keywords1'])){
            add_class($(this));
        }
    })
}
if($_GET['address']!= undefined){
    $("#yx_xz .menu3 a").each(function(e){
        if($(this).attr('data-id') == $_GET['address']){
            add_class($(this));
        }
    })
}

function add_class(obj){
    obj.addClass('blue').siblings().removeClass("blue");
}




});