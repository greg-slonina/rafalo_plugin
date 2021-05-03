jQuery(document).ready(function($) {
	$('.wcfm-acf-pro-multi-select').each(function() { $(this).select2(); } );
	
	function processAcfProBasedFieldGroupShow() {
		$('.wcfm_cat_based_acf_pro_article_manager_fields').addClass('wcfm_acf_hide wcfm_head_hide wcfm_block_hide wcfm_custom_hide');
		$('#article_cats_checklist').find('input[type="checkbox"]').each(function() {
			if( $(this).is(':checked') ) {
				$cat_val = $(this).val();
				$.each( wcfm_cat_based_acf_pro_fields, function( cat_id, allowed_groups ) {
				  if( $cat_val == cat_id ) {
				  	$.each( allowed_groups, function( i, allowed_group ) {
				  	  $('.wcfm_acf_articles_manage_'+allowed_group+'_collapsible').removeClass('wcfm_acf_hide wcfm_head_hide wcfm_block_hide wcfm_custom_hide');
				  	  $('.wcfm_acf_articles_manage_'+allowed_group+'_container').removeClass('wcfm_acf_hide wcfm_head_hide wcfm_block_hide wcfm_custom_hide');
				  	});
				  }
				});
			}
		});
		resetCollapsHeight($('.collapse-open').next('.wcfm-container').find('.wcfm_ele:not(.wcfm_title):first'));
	}
	
	if( $('#article_cats').hasClass('wcfm-select') ) {
		$('.wcfm_cat_based_acf_pro_article_manager_fields').addClass('wcfm_acf_hide wcfm_head_hide wcfm_block_hide wcfm_custom_hide');
		$('#article_cats').change(function() {
		  $article_cats = $(this).val();
		  $.each($article_cats, function(i, $article_cat) {
				$.each( wcfm_cat_based_acf_pro_fields, function( cat_id, allowed_groups ) {
					if( $article_cat == cat_id ) {
						$.each( allowed_groups, function( i, allowed_group ) {
							$('.wcfm_acf_articles_manage_'+allowed_group+'_collapsible').removeClass('wcfm_acf_hide wcfm_head_hide wcfm_block_hide wcfm_custom_hide');
							$('.wcfm_acf_articles_manage_'+allowed_group+'_container').removeClass('wcfm_acf_hide wcfm_head_hide wcfm_block_hide wcfm_custom_hide');
						});
					}
				});
			});
			resetCollapsHeight($('.collapse-open').next('.wcfm-container').find('.wcfm_ele:not(.wcfm_title):first'));
		}).change();
	} else {
		$('#article_cats_checklist').find('input[type="checkbox"]').each(function() {
			$(this).click(function() {
				processAcfProBasedFieldGroupShow();
			});
		});
		processAcfProBasedFieldGroupShow();
	}
	
		$store_lat = jQuery(".wcfm_acf_map_lat").val();
	$store_lng = jQuery(".wcfm_acf_map_lng").val();
  function initialize() {
		var latlng = new google.maps.LatLng( $store_lat, $store_lng );
		var wcfm_acf_map = $(".wcfm_acf_map").attr('id');
		var map = new google.maps.Map(document.getElementById(wcfm_acf_map), {
				center: latlng,
				blur : true,
				zoom: 15
		});
		var marker = new google.maps.Marker({
				map: map,
				position: latlng,
				draggable: true,
				anchorPoint: new google.maps.Point(0, -29)
		});
	
		$map_location = $(".wcfm_acf_map_location").attr('id');
		var find_address_input = document.getElementById($map_location);
		//map.controls[google.maps.ControlPosition.TOP_LEFT].push(find_address_input);
		var geocoder = new google.maps.Geocoder();
		var autocomplete = new google.maps.places.Autocomplete(find_address_input);
		autocomplete.bindTo("bounds", map);
		var infowindow = new google.maps.InfoWindow();   
	
		autocomplete.addListener("place_changed", function() {
			infowindow.close();
			marker.setVisible(false);
			var place = autocomplete.getPlace();
			if (!place.geometry) {
				window.alert("Autocomplete returned place contains no geometry");
				return;
			}

			// If the place has a geometry, then present it on a map.
			if (place.geometry.viewport) {
				map.fitBounds(place.geometry.viewport);
			} else {
				map.setCenter(place.geometry.location);
				map.setZoom(17);
			}

			marker.setPosition(place.geometry.location);
			marker.setVisible(true);

			bindDataToForm(place.formatted_address,place.geometry.location.lat(),place.geometry.location.lng());
			infowindow.setContent(place.formatted_address);
			infowindow.open(map, marker);
			showTooltip(infowindow,marker,place.formatted_address);
	
		});
		google.maps.event.addListener(marker, "dragend", function() {
			geocoder.geocode({"latLng": marker.getPosition()}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[0]) {        
						bindDataToForm(results[0].formatted_address,marker.getPosition().lat(),marker.getPosition().lng());
						infowindow.setContent(results[0].formatted_address);
						infowindow.open(map, marker);
						showTooltip(infowindow,marker,results[0].formatted_address);
						//document.getElementById("searchStoreAddress");
					}
				}
			});
		});
	}
	
	function bindDataToForm(address,lat,lng){
		$(".wcfm_acf_map_location").val(address);
		$(".wcfm_acf_map_lat").val(lat);
		$(".wcfm_acf_map_lng").val(lng);
	}
	function showTooltip(infowindow,marker,address){
	   google.maps.event.addListener(marker, "click", function() { 
			infowindow.setContent(address);
			infowindow.open(map, marker);
		});
	}
	
	$is_initialize = false;
	$( document.body ).on( 'wcfm_product_tab_changed', function( event, tab ) {
		container = $(tab).next();
		if( !$is_initialize && container.find(".wcfm_acf_map_lat").length > 0 ) {
			setTimeout( function() {
				initialize();
				//google.maps.event.addDomListener(window, "load", initialize);
				$is_initialize = true;
			}, 1000 );
		}
	});
});