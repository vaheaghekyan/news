//Used in story/view, views/story/view.php

$(document).ready(function()
{
    //colorboxStory();

    //SINGLE STORY ARROWS
    /*
    var single_next=$("#next");
    var single_previous=$("#previous");
    var move_to_top=$(".main-image").height()/2;
    move_to_top+=106;
    single_previous.css({"top":move_to_top});
    single_next.css({"top":move_to_top});
    */
    
    //video JW player related variables
    var currentPlayerID, activePlayerInstance;

    //function for playing video JW player
    function playPlayer() {
        currentPlayerID = $('ul.bxslider li.active-slide div.jwplayer').attr('id');
        activePlayerInstance = jwplayer(currentPlayerID);
        activePlayerInstance.play(true);
    }

    //if view opened has video, play video JW player
    if(typeof(playerInstance) != "undefined" && playerInstance !== null) {
        playerInstance.on('ready',function() {
            if($('div.jwplayer').length)
                playPlayer();
        });
    }

    //if current tab gets focus, play video if it's there
    window.onfocus = function() {
        if($('div.jwplayer').length)
            playPlayer();
    };
    //if current tab loses focus or window minimised, play video if it's there
    window.onblur = function() {
        if($('div.jwplayer').length)
            playerInstance.pause(true);
    };

});




