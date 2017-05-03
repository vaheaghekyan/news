var nextPrev;$(document).ready(function()
{slider=$('.bxslider').bxSlider({"infiniteLoop":false,"pager":false,"infiniteLoop":false,"adaptiveHeight":true,onSlideAfter:function($slideElement,oldIndex,newIndex){$("#righthttpool").attr('src',"/story/righthttpool");$("#tophttpool").attr('src',"/story/tophttpool");if($('ul.bxslider li.active-slide div.jwplayer').length)
    activePlayerInstance.pause(true);$('.active-slide').removeClass('active-slide');$('.bxslider>li').eq(newIndex).addClass('active-slide');if($('ul.bxslider li.active-slide div.jwplayer').length)
    playPlayer();$('.carousel_title').addClass("not_show");$('.carousel_title img').addClass("not_show");for(var i=newIndex+1;i<newIndex+8;i++){$('.title'+i).removeClass("not_show");if(i==newIndex+3)$('.img'+i).removeClass("not_show");if(i==newIndex+7)$('.img'+i).removeClass("not_show");}
    window.history.pushState(null,null,domain+$("ul.bxslider li.active-slide h1.post_title a").attr('href'));},onSliderLoad:function(){$('.bxslider>li').eq(0).addClass('active-slide');if(typeof(playerInstance)!="undefined"&&playerInstance!==null){playerInstance.on('ready',function(){if($('ul.bxslider li.active-slide div.jwplayer').length)
    playPlayer();});}
    protocol=window.location.protocol;host=window.location.host;domain=protocol+'//'+host;window.history.pushState(null,null,domain+$("ul.bxslider li.active-slide h1.post_title a").attr('href'));},});var currentPlayerID,activePlayerInstance;var protocol,host,domain;function playPlayer(){currentPlayerID=$('ul.bxslider li.active-slide div.jwplayer').attr('id');activePlayerInstance=jwplayer(currentPlayerID);activePlayerInstance.play(true);}
    window.onfocus=function(){if($('ul.bxslider li.active-slide div.jwplayer').length)
        playPlayer();};window.onblur=function(){if($('div.jwplayer').length)
    activePlayerInstance.pause(true);};$(document).keydown(function(e)
{if(e.keyCode==39)
{slider.goToNextSlide();return false;}
else if(e.keyCode==37)
{slider.goToPrevSlide();return false;}});if(goToSlide)
    slider.goToSlide(goToSlide);});