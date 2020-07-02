jQuery(document).ready(function() {

    window.initDataTable = function() {

        var settings = {
            "destroy": true,
            "scrollCollapse": true,
            "searching": true,
            "language": {
                "processing": "Traitement en cours...",
                "search": "",
                "searchPlaceholder": "Filtre par annee",
                "lengthMenu": "Afficher _MENU_ &eacute;l&eacute;ments",
                "info": "Affichage de  _START_ &agrave; _END_ sur _TOTAL_ ",
                "infoEmpty": "Affichage de 0 &agrave; 0 sur 0 ",
                "infoFiltered": "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
                "infoPostFix": "",
                "loadingRecords": "Chargement en cours...",
                "zeroRecords": "<h3 class='uk-margin-top uk-margin-bottom'>Aucun &eacute;l&eacute;ment &agrave; afficher</h3>",
                "emptyTable": "<h3 class='uk-margin-top uk-margin-bottom'>Aucune donn&eacute;e disponible</h3>",
                "paginate": {
                    "first": "",
                    "previous": "",
                    "next": "",
                    "last": ""
                },
                "aria": {
                    "sortAscending": ": activer pour trier la colonne par ordre croissant",
                    "sortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
                }
            },
            "pageLength": 25,
            "columnDefs": [{
                "orderable": false,
                "searchable": false,
                "targets": 2
            },
                {
                    "orderable": true,
                    "searchable": false,
                    "targets": 0
                }
            ]

        }

        jQuery('.dataTable').dataTable(settings);
    }

    initDataTable();


});

jQuery(document).ready(function($) {
    if ($('.datepicker').length > 0) {
        $('.datepicker').datepicker({
            language: 'fr-FR',
            format: 'dd/mm/yyyy',
            autoHide: true
        });
    }

    if ($('.datepicker_birth').length > 0) {
        $('.datepicker_birth').datepicker({
            language: 'fr-FR',
            format: 'dd/mm/yyyy',
            startView: 2,
            autoHide: true
        });
    }

    if ($('.datepicker_start').length > 0) {
        $('.datepicker_start').datepicker({
            language: 'fr-FR',
            format: 'dd/mm/yyyy',
            startView: 2,
            autoHide: true,
            pick: function(date) {
                $date_end = $(date.currentTarget).parent().next().find('input.datepicker_end');
                $reforme_date_start_show = ('0' + date.date.getDate()).slice(-2) + '/' + ('0' + (date.date.getMonth() + 1)).slice(-2) + '/' + date.date.getFullYear();
                $reforme_date_start = '' + ('0' + (date.date.getMonth() + 1)).slice(-2) + '/' + date.date.getDate() + '/' + date.date.getFullYear();

                if ($date_end) {

                    $reforme_date_end = "";

                    if ($date_end.val() === '') {
                        $date_end.val($reforme_date_start_show);
                    } else {
                        date_end_js = $date_end.val().split('/');
                        $reforme_date_end = (date_end_js[1]) + '/' + date_end_js[0] + '/' + date_end_js[2];
                    }

                    if (new Date($reforme_date_start) >= new Date($reforme_date_end)) {
                        $date_end.val($reforme_date_start_show);
                    }
                }
            }
        });
    }

    if ($('.datepicker_end').length > 0) {
        $('.datepicker_end').datepicker({
            language: 'fr-FR',
            format: 'dd/mm/yyyy',
            startView: 2,
            autoHide: true,
            pick: function(date) {
                $date_start = $(date.currentTarget).parent().prev().find('input.datepicker_start');
                $reforme_date_end_show = ('0' + date.date.getDate()).slice(-2) + '/' + ('0' + (date.date.getMonth() + 1)).slice(-2) + '/' + date.date.getFullYear();
                $reforme_date_end = '' + ('0' + (date.date.getMonth() + 1)).slice(-2) + '/' + date.date.getDate() + '/' + date.date.getFullYear();

                if ($date_start) {

                    $reforme_date_start = "";

                    if ($date_start.val() === '') {
                        $date_start.val($reforme_date_end_show);
                    } else {
                        date_start_js = $date_start.val().split('/');
                        $reforme_date_start = (date_start_js[1]) + '/' + date_start_js[0] + '/' + date_start_js[2];
                    }


                    if (new Date($reforme_date_end) <= new Date($reforme_date_start)) {
                        $date_start.val($reforme_date_end_show);
                    }
                }
            }
        });
    }

    if ($('.datepicker_year_start').length > 0) {
        $('.datepicker_year_start').datepicker({
            language: 'fr-FR',
            format: 'yyyy',
            startView: 2,
            autoHide: true,
            pick: function(date) {

                if ($('.datepicker_year_end')) {

                    if ($('.datepicker_year_end').val() === '') {
                        $('.datepicker_year_end').val(date.date.getFullYear() + 1);
                    }

                    if (parseFloat(date.date.getFullYear()) >= parseInt($('.datepicker_year_end').val())) {

                        $('.datepicker_year_end').val(date.date.getFullYear() + 1);
                    }
                }

            }
        })
    }

    if ($('.datepicker_year_end').length > 0) {
        $('.datepicker_year_end').datepicker({
            language: 'fr-FR',
            format: 'yyyy',
            startView: 2,
            autoHide: true,
            pick: function(date) {
                if ($('.datepicker_year_start')) {

                    if ($('.datepicker_year_start').val() === '') {
                        $('.datepicker_year_start').val(date.date.getFullYear() - 1);
                    }

                    if (parseFloat(date.date.getFullYear()) <= parseInt($('.datepicker_year_start').val())) {

                        $('.datepicker_year_start').val(date.date.getFullYear() - 1);
                    }
                }
            }
        })
    }

    $(document).on("input", ".numeric", function(e) {
        this.value = this.value.replace(/[^\d]/,'');
    });

    $(".numeric").on('paste',function(e) {

        if(gBrowser=='IE') {
            var clipboardData, pastedData;
            clipboardData = e.clipboardData || window.clipboardData;
            pastedData = clipboardData.getData('Text');

        }
        else if((gBrowser=='Firefox')|| (gBrowser=='Chrome')){
            var pastedData = (e.originalEvent || e).clipboardData.getData('text/plain');
            window.document.execCommand('insertText', false, pastedData)
        }
        if(Math.floor(pastedData) == pastedData && $.isNumeric(pastedData)){
            $(this).val($(this).val().replace(/[^\d]/g,''));
        }
        else{
            return false;
        }
    });



    // Listen for input event on numInput.
    $('.number').onkeydown = function(e) {
        if (!((e.keyCode > 95 && e.keyCode < 106) ||
            (e.keyCode > 47 && e.keyCode < 58) ||
            e.keyCode == 8)) {
            return false;
        }
    }


    $('.repeater').repeater({
        show: function() {
            $(this).slideDown();
        },
        hide: function(remove) {
            if (confirm('Etes vous sure de supprimer cet élément ?')) {
                $(this).slideUp(remove);
            }
        }
    });
});