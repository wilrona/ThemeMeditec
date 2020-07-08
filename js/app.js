
(function($) {

  $(document).ready(function () {

    if ($('select.selected').length > 0) {
      $('select.selected').select2();
      //                $( document.body ).on( "click", function() {
      //                    $( 'select' ).select2();
      //                });
    }

    if ($('select.selectedComp').length > 0) {

      $('select.selectedComp').select2({
        maximumSelectionLength: 10
      });
      //                $( document.body ).on( "click", function() {
      //                    $( 'select' ).select2();
      //                });
    }


    $('body').on('click', '.modal', function (e) {
      e.preventDefault();
      var url = $(this).attr('href');

      UIkit.modal($('#modal')).hide();
      UIkit.modal($('#modal')).show();

      $.ajax({
        method: "GET",
        url: url
      })
          .done(function (msg) {
            $('#modal .uk-body-custom').html(msg);
          });

    });


    $('#modal').on('hide', function () {
      $('#modal .uk-body-custom').html('<div class="uk-text-center uk-height-1-1 uk-flex-middle uk-padding"><div uk-spinner></div><h1 style="color: #000;" class="uk-margin-remove">Chargement</h1></div>');
    });

    $("body").on('click', 'a', function () {
      window.onbeforeunload = null;
    });

    $('#owl-carousel').owlCarousel({
      loop:true,
      margin:10,
      nav:false,
      dots: true,
      autoplay:true,
      autoplayTimeout:5000,
      autoplayHoverPause:false,
      items: 1
    });

    $('#owl-carousel-fabriquant').owlCarousel({
      loop:true,
      margin:30,
      nav:true,
      center: true,
      dots: false,
      autoplay:true,
      autoplayTimeout:5000,
      autoplayHoverPause:false,
      items: 1,
      responsive:{
        1200: {
          items: 4
        }
      }
    })


  });

})( jQuery );


