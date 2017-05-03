var nextPrev;
//Used in story/index, views/story/index.php
$(document).ready(function()
{

    if($(window).width()<992)
    {
        //show swipe left/right for more stories image
        var swipeleftright=$.cookie('swipeleftright');
        if(swipeleftright === null || swipeleftright == undefined)
        {

            $.colorbox(
            {
                html:"<h2 style='text-align:center'>"+h2_tag+"</h2><img src='"+url+"/images/swipe.jpg' class='img-responsive' style=' max-width:150px; margin:0 auto; '>",
                height:"85%",
                width:"90%"
            });
            $.cookie('swipeleftright', 'true', {expires:365, path:'/'});
        }
    }

    //when you click on buttons to swipe through news
    $('.next, .prev').click(function()
    {
       // nextPrev=$(this).attr('class') ;

        //https://github.com/defunkt/jquery-pjax
        $(document).on('pjax:complete', function()
        {
            //after pjax it always goes "load" to load image
            colorboxStory();
            paginationButtonsCss('load');
            touchSwipe();

            //render AddThis toolbox again
            //http://support.addthis.com/customer/portal/articles/1293805-using-addthis-asynchronously#.UvvWw0JdWTM
            var addthisScript = document.createElement('script');
            addthisScript.setAttribute('src', 'http://s7.addthis.com/js/300/addthis_widget.js#domready=1');
            document.body.appendChild(addthisScript);

            //reload site so you can load video normally
            if($("#myVideo").length)
                location.reload();

            /*
            if(nextPrev=="next")
                page=page+1;
            else
                page=page-1;
            if(page==5)
            {
                var storyid, date_published;
                storyid=$("#storyid").val();
                date_published=$("#date_published").val();

                //set session
                $.ajax({
                    url : setSessionVarUrl,
                    type : "POST",
                    dataType : "json",
                    data: {storyid:storyid, date_published:date_published},
                    success: function(data)
                    {
                        if(data.result=="true")
                        {
                            window.location=goHome;
                        }
                        else
                            alert("Something was wrong.")
                    }
                });

            }*/
        });

    });

    //on document ready it always goes "ready" to load image
    colorboxStory();
    paginationButtonsCss('ready');
    touchSwipe();


});

$(window).resize(function()
{
});


