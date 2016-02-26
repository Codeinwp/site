(function($){

    $(document).ready(function() {
        $('#menu1').dropit();
        $('#menu2').dropit({ action: 'mouseenter' });
    });

    hljs.initHighlightingOnLoad();

})(window.jQuery);
