(function($){
    $(function(){

        $('.sidenav').sidenav();
        $('.parallax').parallax();
        $('.dropdown-trigger').dropdown();
        $('.carousel').carousel({
            fullWidth: true,
            indicators: true
        });
        $('.materialboxed').materialbox();


        setInterval(function () {
            $('.carousel').carousel('next')
        }, 2000)
    }); // end of document ready
})(jQuery); // end of jQuery name space
