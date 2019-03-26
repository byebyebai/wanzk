
//  //首页轮播
//  var swiper = new Swiper('#sy_bodytop .banner .swiper-container', {
//    spaceBetween: 30,
//    centeredSlides: true,
//    autoplay: {
//      delay: 2000,
//      disableOnInteraction: true,
//    },
//    pagination: {
//      el: '#sy_bodytop .banner .swiper-pagination',
//      clickable: true,
//    },
//    navigation: {
//      nextEl: '.swiper-button-next',
//      prevEl: '.swiper-button-prev',
//    },
//  });

    function setTab(name, cursel, n) {
        for (i = 1; i <= n; i++) {
            var menu = document.getElementById(name + i);
            var con = document.getElementById("con_" + name + "_" + i);
            menu.className = i == cursel ? "hover" : "";
            con.style.display = i == cursel ? "block" : "none";
        }
    }
    //多余显示省略号
    function wordlimit(cname, wordlength) {
        var cname = document.getElementsByClassName(cname);
        for (var i = 0; i < cname.length; i++) {　　　　　　
            var nowLength = cname[i].innerHTML.length;
            if (nowLength > wordlength) {
                cname[i].innerHTML = cname[i].innerHTML.substr(0, wordlength) + '...';
            }　　　　　　
        }　
    };
    //倒计时
    function Countdown(time){
        var now=new Date();
        var end=new Date(time);
        var result=Math.floor(end-now)/1000; 
        var interval=setInterval(function(){
            if (result>1) {
               result = result - 1; 
               var day = Math.floor(result / (3600*24));
               $("#daydw").html(day);
            } else{
                window.clearInterval(interval);
            }
        },1000);
    }

    //页面点击
    function addhits(id){

      $.post("/index.php/index/hits",{'id':id},function(e){
        //console.log(e.hits);
        $("#hits").html(e.hits);
      });
    }
    //获取url参数
    var $_GET = (function(){
        var url = window.document.location.href.toString();
        var u = url.split("?");
        if(typeof(u[1]) == "string"){
            u = u[1].split("&");
            var get = {};
            for(var i in u){
                var j = u[i].split("=");
                get[j[0]] = j[1];
            }
            return get;
        } else {
            return {};
        }
    })();



    function fantop(){
        window.scrollTo(0,0);
    }
	function showIm(){
    	$("#jesong_chat_min").hide();
    	$("#jesong_chat_layout").show();
    }
	$('#showIm').click(function(){
		showIm();
	})
    $(".showIm").click(function(){
		showIm();
	})
    