jQuery(function ($) {

    $(document).on("click", "a.remove-amount", function (e) {
        e.preventDefault();

        var data = {
            'action'    : 'remove_gift_card_amount',
            'amount'    : $(this).closest("span.variation-amount").find('input[name="gift-card-amounts[]"]').val(),
            'product_id': $("#post_ID").val()
        };

        var clicked_item = $(this).closest("span.variation-amount");
        clicked_item.block({
            message   : null,
            overlayCSS: {
                background: "#fff url(" + ywgc_data.loader + ") no-repeat center",
                opacity   : .6
            }
        });

        $.post(ywgc_data.ajax_url, data, function (response) {
            if (1 == response.code) {
                clicked_item.remove();
            }

            clicked_item.unblock();
        });

    });

    /**
     * Add a new amount to current gift card
     * @param item
     */
    function add_amount(item) {
        var data = {
            'action'    : 'add_gift_card_amount',
            'amount'    : $("#gift_card-amount").val(),
            'product_id': $("#post_ID").val()
        };

        var clicked_item = item.closest("span.add-new-amount-section");
        clicked_item.block({
            message   : null,
            overlayCSS: {
                background: "#fff url(" + ywgc_data.loader + ") no-repeat center",
                opacity   : .6
            }
        });

        $.post(ywgc_data.ajax_url, data, function (response) {
            if (1 == response.code) {
                $("span.variation-amount-list").replaceWith(response.value);
            }

            $('#gift_card-amount').val('');
            $('#gift_card-amount').selectionStart = 0;
            $('#gift_card-amount').selectionEnd = 0;
            $('#gift_card-amount').focus();

            clicked_item.unblock();
        });
    }

    /**
     * Add a new amount for the current gift card
     */
    $(document).on("click", "a.add-new-amount", function (e) {
        e.preventDefault();
        add_amount($(this));
    });

    /**
     * Add a new amount for the current gift card
     */
    $(document).on('keypress', 'input#gift_card-amount', function (e) {
        if (event.which === 13) {
            e.preventDefault();

            //Disable textbox to prevent multiple submit
            $(this).attr("disabled", "disabled");

            //Do Stuff, submit, etc..
            add_amount($(this));

            $(this).removeAttr("disabled");

        }
    });

    $(document).on('change', 'input[name="ywgc_physical_gift_card"]', function (e) {
        var status = $(this).prop("checked");
        $('input[name="_virtual"]').prop("checked", !status);
    });

    $(document).on('click', '.image-gallery-reset', function (e) {
        e.preventDefault();

        $('#ywgc-card-header-image img').remove();
        $("#ywgc_product_image_id").val(0);
    });


    $( 'body .ywgc_order_sold_as_gift_card' ).each( function () {
        $( this ).parent( 'td' ).find( '.wc-order-item-name' ).hide();
    });

    //show the manage stock in the inventory tab
    $('._manage_stock_field').addClass('show_if_gift-card').show();

    /* Manage date when gift card is created manually */
    if(typeof jQuery.fn.datepicker !== "undefined"){

        $(".ywgc-expiration-date-picker").datepicker({dateFormat: ywgc_data.date_format, minDate: +1, maxDate: "+1Y"});
    }


  var default_button_text = $('button.ywgc-actions:first').text();


  $(document).on( 'click', 'button.ywgc-actions', function (e) {
    e.preventDefault();

    console.log('click')

    var button =  $(this);

    var link = button.prev('#ywgc_direct_link').text();
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(link).select();
    document.execCommand("copy");
    $temp.remove();

    var copied_text = $('#ywgc_copied_to_clipboard').text();
    button.text(copied_text);

    setTimeout(function() {
      button.text(default_button_text);
    }, 1000);


  });

  $( document ).on( 'change', '.ywgc-toggle-enabled input', function () {

    var enabled   = $( this ).val() === 'yes' ? 'yes' : 'no',
        container = $( this ).closest( '.ywgc-toggle-enabled' ),
        gift_card_ID   = container.data( 'gift-card-id' );

    var blockParams = {
      message        : null,
      overlayCSS     : { background: '#fff', opacity: 0.7 },
      ignoreIfBlocked: true
    };
    container.block( blockParams );

    $.ajax( {
      type    : 'POST',
      data    : {
        action  : 'ywgc_toggle_enabled_action',
        id      : gift_card_ID,
        enabled : enabled,
      },
      url     : ajaxurl,
      success : function ( response ) {
        if ( typeof response.error !== 'undefined' ) {
          alert( response.error );
        }
      },
      complete: function () {
        container.unblock();
      }
    } );
  } );


  if ( $('.ywgc-override-product-settings input' ).val() === 'yes' ){
    $( '.ywgc-custom-amount-field' ).removeClass( 'ywgc-hidden' );
    $( '.minimal-amount-field' ).removeClass( 'ywgc-hidden' );
  }

  $( document ).on( 'change', '.ywgc-override-product-settings input', function () {
    var enabled   = $( this ).val() === 'yes' ? 'yes' : 'no';

    if ( enabled == 'yes' ){
      $( '.ywgc-custom-amount-field' ).toggle();

      if ($('.ywgc-custom-amount-field input' ).val() === 'yes')
        $( '.minimal-amount-field' ).toggle();
    }
    else{
      $( '.ywgc-custom-amount-field' ).toggle();
      if ($('.ywgc-custom-amount-field input' ).val() === 'yes')
        $( '.minimal-amount-field' ).toggle();
    }
  } );


  if ( $('.ywgc-custom-amount-field input' ).val() === 'yes' && $('.ywgc-override-product-settings input').val() == 'yes' ) {
    $( '.minimal-amount-field' ).removeClass( 'ywgc-hidden' );
  }else{
    $( '.minimal-amount-field' ).addClass( 'ywgc-hidden' );
  }


  $( document ).on( 'change', '.ywgc-custom-amount-field input', function () {
    var enabled   = $( this ).val() === 'yes' ? 'yes' : 'no';

    if ( enabled == 'yes' ){
      $( '.minimal-amount-field' ).toggle();
    }
    else{
      $( '.minimal-amount-field' ).toggle();
    }
  } );


  if ( $('.ywgc-add-discount-settings input' ).val() === 'yes' ){
    $( '.ywgc-add-discount-settings-container' ).removeClass( 'ywgc-hidden' );
  }

  $( document ).on( 'change', '.ywgc-add-discount-settings input', function () {
    var enabled   = $( this ).val() === 'yes' ? 'yes' : 'no';

    if ( enabled == 'yes' ){
      $( '.ywgc-add-discount-settings-container' ).toggle();
    }
    else{
      $( '.ywgc-add-discount-settings-container' ).toggle();
    }

  } );


  if ( $('.ywgc-expiration-settings input' ).val() === 'yes' ){
    $( '.ywgc-expiration-settings-container' ).removeClass( 'ywgc-hidden' );
  }

  $( document ).on( 'change', '.ywgc-expiration-settings input', function () {
    var enabled   = $( this ).val() === 'yes' ? 'yes' : 'no';

    if ( enabled == 'yes' ){
      $( '.ywgc-expiration-settings-container' ).toggle();
    }
    else{
      $( '.ywgc-expiration-settings-container' ).toggle();
    }

  } );


  // Table hover and hide
  $(document).on('mouseover', 'table tr.type-gift_card', function (e) {
    $( this ).css( 'background-color', '#f1f6f8' );
    $( this ).find( 'td.gift_card_actions .ywgc-actions' ).show();
  });

  $(document).on('mouseout', 'table tr.type-gift_card', function (e) {
    $( this ).removeAttr('style');
    $( this ).find( 'td.gift_card_actions .ywgc-actions' ).hide();
  });


});
