jQuery(function ($) {

  // Start dependencies

  $('input#ywgc_export_option_order_id').parent().parent().parent().parent().addClass( 'ywgc_export_checkbox_group');

  function show_hide_export_options() {
    if ( $('input#ywgc_export_or_import_option-import').prop('checked') ) {
      $('tr.ywbc-export-button-tr').hide();
      $('tr.ywgc_export_checkbox_group').hide();
      $('tr.ywgc-import-button-tr').show();
      $('tr.ywgc-start-import-button-tr').show();
      $('tr.ywbc-date-from-to-tr').hide();
    }
    else{
      $('tr.ywbc-export-button-tr').show();
      $('tr.ywgc_export_checkbox_group').show();
      $('tr.ywgc-import-button-tr').hide();
      $('tr.ywgc-start-import-button-tr').hide();
      $('tr.ywbc-date-from-to-tr').show();
    }
  }

  function show_hide_date_options() {
    if ( $('input#ywgc_export_option_date-all').prop('checked') ) {
      $('div.ywbc-date-from-to-date-selectors').hide();
    }
    else{
      $('div.ywbc-date-from-to-date-selectors').show();
    }

  }

  show_hide_export_options();
  show_hide_date_options();

  $('input#ywgc_export_or_import_option-import').change(function() {
    show_hide_export_options()
  });
  $('input#ywgc_export_or_import_option-export').change(function() {
    show_hide_export_options()
  });


  $('input#ywgc_export_option_date-all').change(function() {
    show_hide_date_options()

    $('input#ywgc_export_option_date_from').datepicker('setDate', null);
    $('input#ywgc_export_option_date_to').datepicker('setDate', null);

    $('.ywbc-export-button').attr('href', function() {
      return this.href.split('&from')[0];

    });

  });
  $('input#ywgc_export_option_date-by_date').change(function() {
    show_hide_date_options()
  });

  // End dependencies




  //Start import button

  if( $('#ywgc_import_gift_cards').length ){
    $('#ywgc_import_gift_cards').closest('form').attr('enctype',"multipart/form-data");
  }

  $('#ywgc_file_import_csv_btn').on( 'click', function(e){
    e.preventDefault();
    $( '#ywgc_file_import_csv').click();
  });

  $( '#ywgc_file_import_csv' ).on('change', function(){
    var fname =  document.getElementById("ywgc_file_import_csv").files[0].name;

    if ( fname !== '' ) {
      $('span.ywbc_file_name').html( fname );
    }
  });

  $('button#ywgc_import_gift_cards').on('click', function(e){
    e.preventDefault();
    $('.ywgc_safe_submit_field').val( 'importing_gift_cards');
    $(this).closest('form').submit();
  });


  $('#ywgc_export_option_date_from, #ywgc_export_option_date_to').each(function () {
    $(this).prop('placeholder', ywgc_data.date_format )
  }).datepicker({
    dateFormat: ywgc_data.date_format,
  });


  $( 'input#ywgc_export_option_date_from').change(function() {
    var from = $(this).val();



    $('.ywbc-export-button').attr('href', function() {
      return this.href + '&from=' + from;
    });
  });

  $( 'input#ywgc_export_option_date_to').change(function() {
    var to = $(this).val();
    $('.ywbc-export-button').attr('href', function() {
      return this.href + '&to=' + to;
    });
  });


  if ( $('.ywgc_import_result').length ) {
    $('.ywgc_import_result').show();
    setTimeout(function() {
      $('.ywgc_import_result').remove();
    }, 3000);
  }



  });

