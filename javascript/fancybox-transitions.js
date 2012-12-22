(function ($, F) {

    // Closing animation - fly to the top
    F.transitions.dropOut = function() {
        var top = 130+$(window).scrollTop();
        F.wrap.removeClass('fancybox-opened').animate({
            scale: .05,
            top:'-='+top+'px',
            left:'+=455px'
        }, {
            duration: F.current.closeSpeed,
            complete: F._afterZoomOut
        });
    };

    // Closing animation - fly to the top
    F.transitions.dropOut2 = function() {
        var top = 170+$(window).scrollTop();
        F.wrap.removeClass('fancybox-opened').animate({
            scale: .05,
            top:'-='+top+'px',
            left:'+=455px'
        }, {
            duration: F.current.closeSpeed,
            complete: F._afterZoomOut
        });
    };

}(jQuery, jQuery.fancybox));