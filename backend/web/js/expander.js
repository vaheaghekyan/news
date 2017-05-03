/**
 * Created by alekseyyp on 29.07.15.
 */
function Expander( config )
{

    var collapsCls  = "collapsed",
        el          = config.element,
        cnt         = $("." + el.data('target') );

    function toggle()
    {
        if ( el.hasClass( collapsCls ) )
        {
            cnt.toggle();
           el.children().removeClass("fa-arrow-up").addClass("fa-arrow-down");
        }
        else
        {
            cnt.toggle();
            el.children().removeClass("fa-arrow-down").addClass("fa-arrow-up");
        }

    }
    toggle();
    el.click(function () {

        el.toggleClass( collapsCls );
        toggle();

    });

}
$.fn.expander = function() {

    $(this).each (function(){

       new Expander({
           element : $(this)
       });

    });

}

