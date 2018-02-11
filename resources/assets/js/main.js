;(function ($) {

    'use strict';

    $('.alert[data-auto-dismiss]').each(function (index, element) {
        var $element = $(element),
            timeout  = $element.data('auto-dismiss') || 5000;

        setTimeout(function () {
            $element.alert('close');
        }, timeout);
    });

})(jQuery);

$(document).ready(function(){
	$('.dropright').on('shown.bs.dropdown', function(){
		$(this).addClass('active');
	});
	$('.dropright').on('hidden.bs.dropdown', function(){
		$(this).removeClass('active');
	});
	var header = $(".navbar");
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();

        if (scroll >= 80) {
            header.addClass('z-depth-1-half');
        } else {
            header.removeClass("z-depth-1-half");
        }
    });
});

$('body.admin.show, body.admin.new').ready(function(){
	$('[data-toggle="tooltip"]').tooltip();
	
	// prevent 'enter' submitting form.
	$('form:not(".with-cascading-disabling")').on("keypress", ":input:not(textarea)", function(event) {
	    return event.keyCode != 13;
	});

	$("form.with-cascading-disabling input[name=attribute_1]").on("change paste keyup", function() {
		switch($(this).val().length) {
			case 0: 
				for (var i = 2; i <= 5; i++) {
					$("form input[name=attribute_"+i+"]").val(null);
					$("form input[name=attribute_"+i+"]").attr("disabled", "disabled");
				}				
				break;
			default:
				$("form input[name=attribute_2]").removeAttr("disabled");
				break;

		} 
	});
	$("form.with-cascading-disabling input[name=attribute_2]").on("change paste keyup", function() {
		switch($(this).val().length) {
			case 0: 
				for (var i = 3; i <= 5; i++) {
					$("form input[name=attribute_"+i+"]").val(null);
					$("form input[name=attribute_"+i+"]").attr("disabled", "disabled");
				}
				break;
			default:
				$("form input[name=attribute_3]").removeAttr("disabled");
				break;

		} 
	});
	$("form.with-cascading-disabling input[name=attribute_3]").on("change paste keyup", function() {
		switch($(this).val().length) {
			case 0: 
				for (var i = 4; i <= 5; i++) {
					$("form input[name=attribute_"+i+"]").val(null);
					$("form input[name=attribute_"+i+"]").attr("disabled", "disabled");
				}
				break;
			default:
				$("form input[name=attribute_4]").removeAttr("disabled");
				break;

		} 
	});
	$("form.with-cascading-disabling input[name=attribute_4]").on("change paste keyup", function() {
		switch($(this).val().length) {
			case 0: 
				$("form input[name=attribute_5]").val(null);
				$("form input[name=attribute_5]").attr("disabled", "disabled");
				break;
			default:
				$("form input[name=attribute_5]").removeAttr("disabled");
				break;

		} 
	});

	$('a.close.delete-variant').popover({
		trigger: 'focus'
	});

	$('a.close.delete-variant').on('inserted.bs.popover', function () {
		$(this).addClass('highlight');
	});

	$('a.close.delete-variant').on('hide.bs.popover', function () {
		$(this).removeClass('highlight');
	});

	$('table.table').on('click', 'a.close.delete-variant.highlight', function() {
		variant_id = $(this).attr('variant');
		delete_variant(variant_id);
	});

	function delete_variant(variant_id){
		token = $('meta[name="csrf-token"]').attr('content');

		var data_for_sending = {};
		data_for_sending.variant_id = variant_id;
		data_for_sending._token = token;
		
		url = "/deleteVariant/" + variant_id;

		$.ajaxSetup({
		    beforeSend: function(xhr, type) {
		        if (!type.crossDomain) {
		            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
		        }
		    },
		});

		$.ajax({
		    dataType: 'JSON',
		    method: 'DELETE',
		    url: url,
		    data: JSON.stringify(data_for_sending),
		    success: function(a) {
		    	console.log(a);

		    },
		    error: function(a) {
		    	
		    }
	  	});		
	}

	$('#toggle_availability').click(function(){
		toggle_available($(this).attr('variant'));
	});

	$('table.variants_table').on('click', '.toggle_availability_variant', function(){
		toggle_available_variant($(this).data('variant'));
	});

	function toggle_available(product_id){
		token = $('meta[name="csrf-token"]').attr('content');

		var data_for_sending = {};
		data_for_sending.product_id = product_id;
		data_for_sending._token = token;
		// data_for_sending._method = 'PATCH';
		
		url = "/toggleAvailability/" + product_id;

		$.ajaxSetup({
		    beforeSend: function(xhr, type) {
		        if (!type.crossDomain) {
		            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
		        }
		    },
		});
		$.ajax({
		    dataType: 'JSON',
		    method: 'PATCH',
		    url: url,
		    data: JSON.stringify(data_for_sending),
		    success: function(a) {
		    	if(a.is_available) {
			    	$('.alerts-holder').prepend('<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+a.name+' <strong>available</strong> for sale!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			    	$('#product_availability').removeClass('text-red');
			    	$('#product_availability').text('AVAILABLE');

			    } else {
			    	$('.alerts-holder').prepend('<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+a.name+' <strong>unavailable</strong> for sale!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			    	$('#product_availability').addClass('text-red');
			    	$('#product_availability').text('UNAVAILABLE');
			    }
		    },
		    error: function(xhr, status, error) {
		    	$('.alerts-holder').prepend('<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+JSON.parse(xhr.responseText)+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
		    }
	  	});	

	  	setTimeout(removeAddedAlerts, 5000);
	}

	function toggle_available_variant(variant_id){
		token = $('meta[name="csrf-token"]').attr('content');

		var data_for_sending = {};
		data_for_sending.variant_id = variant_id;
		data_for_sending._token = token;
		// data_for_sending._method = 'PATCH';
		
		url = "/toggleAvailabilityVariant/" + variant_id;

		$.ajaxSetup({
		    beforeSend: function(xhr, type) {
		        if (!type.crossDomain) {
		            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
		        }
		    },
		});
		$.ajax({
		    dataType: 'JSON',
		    method: 'PATCH',
		    url: url,
		    data: JSON.stringify(data_for_sending),
		    success: function(a) {
		    	if(a.is_available) {
			    	$('.alerts-holder').prepend('<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>Variant <strong>available</strong> for sale!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			    	$row = $('.' + a.class);
			    	$row.find('.availability').removeClass('text-red');
			    	$row.find('.availability').addClass('text-green');
			    	$row.find('.availability').attr('data-original-title', 'Available');
			    	$row.find('.toggle_availability_variant').text('Make unavailable');

			    } else {
			    	$('.alerts-holder').prepend('<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>Variant <strong>unavailable</strong> for sale!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			    	$row = $('.' + a.class);
			    	$row.find('.availability').addClass('text-red');
			    	$row.find('.availability').removeClass('text-green');
			    	$row.find('.availability').attr('data-original-title', 'Unavailable');
			    	$row.find('.toggle_availability_variant').text('Make available');
			    }
		    },
		    error: function(xhr, status, error) {
		    	$('.alerts-holder').prepend('<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+JSON.parse(xhr.responseText)+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
		    }
	  	});	

	  	setTimeout(removeAddedAlerts, 5000);
	}

	$('#add_variant_form').submit(function(e){
		e.preventDefault();

		$.ajaxSetup({
		  headers: {
		    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		  }
		});

		$this_form = $(this);

		$.ajax({
		    dataType: 'JSON',
		    method: $('#add_variant_form').attr('method'),
		    url: $('#add_variant_form').attr('action'),
		    data: $('#add_variant_form').serialize(),
		    success: function(a) {
		    	if(a.success) {
			    	$('.alerts-holder').prepend('<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+a.message+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');

					new_variant = '<tr class="variant_'+a.variant.id+'" data-id="'+a.variant.id+'" data-inventory="'+a.variant.inventory+'" data-price="'+a.variant.price+'"><td class="attribute_1">'+a.variant.attribute_1+'</td>';
					if (typeof a.variant.attribute_2 !== 'undefined'){
						new_variant = new_variant + '<td class="attribute_2">'+a.variant.attribute_2+'</td>';
					}

					if (typeof a.variant.attribute_3 !== 'undefined'){
						new_variant = new_variant + '<td class="attribute_3">'+a.variant.attribute_3+'</td>';
					}
					if (typeof a.variant.attribute_4 !== 'undefined'){
						new_variant = new_variant + '<td class="attribute_4">'+a.variant.attribute_4+'</td>';
					}
					if (typeof a.variant.attribute_5 !== 'undefined'){
						new_variant = new_variant + '<td class="attribute_5">'+a.variant.attribute_5+'</td>';
					}

					new_variant = new_variant + '<td class="text-right">'+a.variant.inventory+'</td><td class="text-right">'+a.variant.view_price+'</td><td class="text-center availability text-green" data-toggle="tooltip" data-placement="top" title="Available"><i class="fas fa-circle"></i></td><td><div class="btn-group dropright float-right"><div class="option-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></div><div class="dropdown-menu"><a class="dropdown-item edit_variant" href="javascript:void(0);">Edit variant</a><a class="dropdown-item toggle_availability_variant" href="javascript:void(0);" data-variant="'+a.variant.id+'">Make unavailable</a><div class="dropdown-divider"></div><a class="dropdown-item delete" onClick="window.location.reload()">Click to refresh and delete this variant.</a></div></div></td></tr>';
					$('#total_inventory').text(a.total_inventory);
					$('table.variants_table').append(new_variant);
					$('#no_variants_yet').remove();
			    } else {
			    	$('.alerts-holder').prepend('<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+a.message+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>').fadeTo(2000, 500);
			    	$('#total_inventory').text(a.total_inventory);
			    }
		    },
		    error: function(a) {
		    	console.log(a.message);
		    	$('.alerts-holder').prepend('<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+a.statusText+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
		    }
	  	});	
	  	$this_form.find('input[name=attribute_1],input[name=attribute_2],input[name=attribute_3],input[name=attribute_4],input[name=attribute_5]').val("");
	  	$this_form.find('input[name=inventory]').val(0);
		$this_form.find('input[name=attribute_1]').select();
		setTimeout(removeAddedAlerts, 5000);
		$("body").tooltip({
		    selector: '[data-toggle="tooltip"]'
		});
	});

	function removeAddedAlerts(){
		$('.alerts-holder').find('.alert:last').alert('close');
	}
	
	$('table.table').on('show.bs.dropdown', 'td .dropright', function(){
		$(this).parents('tr').addClass('active');
		$(this).parents('table').removeClass('table-hover');
	});

	$('table.table').on('hidden.bs.dropdown', 'td .dropright', function(){
		$(this).parents('tr').removeClass('active');
		$(this).parents('table').addClass('table-hover');
	});

	$('table.table').on('click', '.edit_variant', function() {
		$('#edit_variant_block').removeClass('hide');

		$row = $(this).closest('tr');
		$('#edit_variant_block').find('input[name=attribute_1]').val($row.find('.attribute_1').text());
		$('#edit_variant_block').find('input[name=attribute_2]').val($row.find('.attribute_2').text());
		$('#edit_variant_block').find('input[name=attribute_3]').val($row.find('.attribute_3').text());
		$('#edit_variant_block').find('input[name=attribute_4]').val($row.find('.attribute_4').text());
		$('#edit_variant_block').find('input[name=attribute_5]').val($row.find('.attribute_5').text());
		$('#edit_variant_block').find('input[name=inventory]').val($row.data('inventory'));
		$('#edit_variant_block').find('input[name=variant_id]').val($row.data('id'));
		$('#edit_variant_block').find('input[name=price]').val($row.data('price'));
	});

	$('#edit_variant_block').on('click', 'button.close', function() {
		$('#edit_variant_block').addClass('hide');
		$('#edit_variant_block form').reset();
	});
});