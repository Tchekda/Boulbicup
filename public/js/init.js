(function($){
    $(function(){

        $('.modal').modal();
        $('.dropdown-trigger').dropdown();
        $('.dropdown-trigger-mobile').dropdown();
        $('.sidenav').sidenav();
        $('.parallax').parallax();
        $('.carousel').carousel({
            fullWidth: true,
            indicators: true
        });
        $('.materialboxed').materialbox();

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

        $('select').formSelect();

        M.updateTextFields();
    }); // end of document ready
})(jQuery); // end of jQuery name space
