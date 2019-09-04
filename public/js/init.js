(function($){
    $(function(){

        $('.modal').modal();
        $('.dropdown-trigger').dropdown();
        $('.sidenav').sidenav();
        $('.parallax').parallax();
        $('.carousel').carousel({
            fullWidth: true,
            indicators: true
        });
        $('.materialboxed').materialbox();

        M.updateTextFields();
        $('.datepicker').datepicker({
            firstDay: 1,
            format: 'dd/mm/yyyy'
        });
        $('.timepicker').timepicker({
            twelveHour: false
        });

        setInterval(function () {
            $('.carousel').carousel('next')
        }, 2000);

        $('.tabs').tabs();

    }); // end of document ready
})(jQuery); // end of jQuery name space
