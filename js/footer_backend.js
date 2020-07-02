jQuery(document).ready(function($) {
    if ($('.datepicker').length > 0) {
        datepicker()
    }

    $('.button .add').on('click', function () {
        console.log('clicked');
        datepicker();
    });

    function datepicker(){
        $('.datepicker').datepicker({
            language: 'fr-FR',
            format: 'dd/mm/yyyy',
            autoHide: true
        });
    }
});