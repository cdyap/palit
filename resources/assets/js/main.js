$('body.admin').on('ready turbolinks:load', function () { $.onmount() });
$('body.admin').on('turbolinks:before-cache', function () { $.onmount.teardown() });

$(document).on('turbolinks:load',function(){
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

	$('body.admin').ready(function(){
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

	    $(window).scrollTop($(window).scrollTop()+1);
	    $(window).scrollTop($(window).scrollTop()-1);
	});

	$('body.admin.index.AdminController').ready(function(){
		// $('.auto-confirm input[type="checkbox"]').on('change', function(){
		// 	toggleAutoConfirm(this.checked);
		// });

		// function toggleAutoConfirm(auto_confirm_orders){

		// 	token = $('meta[name="csrf-token"]').attr('content');

		// 	var data_for_sending = {};
		// 	data_for_sending.auto_confirm_orders = auto_confirm_orders;
		// 	data_for_sending._token = token;
		// 	// data_for_sending._method = "PATCH";
			
		// 	url = "/orders/toggleAutoConfirm";

		// 	$.ajaxSetup({
		// 	    beforeSend: function(xhr, type) {
		// 	        if (!type.crossDomain) {
		// 	            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
		// 	        }
		// 	    },
		// 	});
		// 	$.ajax({
		// 	    dataType: 'JSON',
		// 	    method: 'PATCH',
		// 	    url: url,
		// 	    data: JSON.stringify(data_for_sending),
		// 	    contentType: "json",
		// 	    processData: false,
		// 	    success: function(a) {
		// 	    	if(a.success) {
		// 		    	$('.alerts-holder').prepend('<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>Auto-confirm orders <strong>'+a.message+'</strong>!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
		// 		    }
		// 	    }
		//   	});	

		//   	setTimeout(removeAddedAlerts, 5000);
		// }

		function removeAddedAlerts(){
			$('.alerts-holder .alert:last').alert('close');

			if ($('.alerts-holder .alert').length > 1) {
				setTimeout(repeatRemoveAddedAlerts, 3000);
			}		
		}

		function repeatRemoveAddedAlerts(){
			removeAddedAlerts();
		}
	});

	// $('body.admin.orders_unpaid.OrdersController').ready(function(){
		
	// 	$('.delete-order-button').toggle(function() {
	// 	    $('.delete-order-form').submit();
	// 	}, function() {
	// 	    $(this).html('Click again to delete this order');
	// 	});
	// });

	$('body.admin table-sticky').ready(function(){
		// Declare variables and shorthands
	    var $t     = $(this),
	        $w     = $(window),
	        $thead = $(this).find('thead').clone(),
	        $col   = $(this).find('thead, tbody').clone();
	    // Here we select for <table> elements universally,
	    // but you can definitely fine tune your selector
	    
	    $('.table-sticky').each(function() {
			if($(this).find('thead').length > 0 && $(this).find('th').length > 0) {
				// Clone <thead>
				var $w	   = $(window),
					$t	   = $(this),
					$thead = $t.find('thead').clone(),
					$col   = $t.find('thead, tbody').clone();

				// Add class, remove margins, reset width and wrap table
				$t
				.addClass('sticky-enabled')
				.css({
					margin: 0,
					width: '100%'
				}).wrap('<div class="sticky-wrap" />');

				if($t.hasClass('overflow-y')) $t.removeClass('overflow-y').parent().addClass('overflow-y');

				// Create new sticky table head (basic)
				$t.after('<table class="sticky-thead" />');

				// If <tbody> contains <th>, then we create sticky column and intersect (advanced)
				if($t.find('tbody th').length > 0) {
					$t.after('<table class="sticky-col" /><table class="sticky-intersect" />');
				}

				// Create shorthand for things
				var $stickyHead  = $(this).siblings('.sticky-thead'),
					$stickyCol   = $(this).siblings('.sticky-col'),
					$stickyInsct = $(this).siblings('.sticky-intersect'),
					$stickyWrap  = $(this).parent('.sticky-wrap');

				$stickyHead.append($thead);

				$stickyCol
				.append($col)
					.find('thead th:gt(0)').remove()
					.end()
					.find('tbody td').remove();

				$stickyInsct.html('<thead><tr><th>'+$t.find('thead th:first-child').html()+'</th></tr></thead>');
				
				// Set widths
				var setWidths = function () {
						$t
						.find('thead th').each(function (i) {
							$stickyHead.find('th').eq(i).width($(this).width());
						})
						.end()
						.find('tr').each(function (i) {
							$stickyCol.find('tr').eq(i).height($(this).height());
						});

						// Set width of sticky table head
						$stickyHead.width($t.width());

						// Set width of sticky table col
						$stickyCol.find('th').add($stickyInsct.find('th')).width($t.find('thead th').width())
					},
					repositionStickyHead = function () {
						// Return value of calculated allowance
						var allowance = calcAllowance();
					
						// Check if wrapper parent is overflowing along the y-axis
						if($t.height() > $stickyWrap.height()) {
							// If it is overflowing (advanced layout)
							// Position sticky header based on wrapper scrollTop()
							if($stickyWrap.scrollTop() > 0) {
								// When top of wrapping parent is out of view
								$stickyHead.add($stickyInsct).css({
									opacity: 1,
									top: $stickyWrap.scrollTop(),
									display: "table"
								});
							} else {
								// When top of wrapping parent is in view
								$stickyHead.add($stickyInsct).css({
									opacity: 0,
									top: 0,
									display: "none"
								});
							}
						} else {
							// If it is not overflowing (basic layout)
							// Position sticky header based on viewport scrollTop
							if($w.scrollTop() > $t.offset().top && $w.scrollTop() < $t.offset().top + $t.outerHeight() - allowance) {
								// When top of viewport is in the table itself
								$stickyHead.add($stickyInsct).css({
									opacity: 1,
									top: $w.scrollTop() - $t.offset().top,
									display: "table"
								});
							} else {
								// When top of viewport is above or below table
								$stickyHead.add($stickyInsct).css({
									opacity: 0,
									top: 0,
									display: "none"
								});
							}
						}
					},
					repositionStickyCol = function () {
						if($stickyWrap.scrollLeft() > 0) {
							// When left of wrapping parent is out of view
							$stickyCol.add($stickyInsct).css({
								opacity: 1,
								left: $stickyWrap.scrollLeft()
							});
						} else {
							// When left of wrapping parent is in view
							$stickyCol
							.css({ opacity: 0 })
							.add($stickyInsct).css({ left: 0 });
						}
					},
					calcAllowance = function () {
						var a = 0;
						// Calculate allowance
						$t.find('tbody tr:lt(3)').each(function () {
							a += $(this).height();
						});
						
						// Set fail safe limit (last three row might be too tall)
						// Set arbitrary limit at 0.25 of viewport height, or you can use an arbitrary pixel value
						if(a > $w.height()*0.25) {
							a = $w.height()*0.25;
						}
						
						// Add the height of sticky header
						a += $stickyHead.height();
						return a;
					};

				setWidths();

				$t.parent('.sticky-wrap').scroll($.throttle(250, function() {
					repositionStickyHead();
					repositionStickyCol();
				}));

				$w.on('load', function(){
					setWidths
				})
				.resize($.debounce(250, function () {
					setWidths();
					repositionStickyHead();
					repositionStickyCol();
				}))
				.scroll($.throttle(200, repositionStickyHead));
			}
		});
	});
	$('body.admin.shipping.SettingsController').ready(function(){
		$('table.shippings_table').on('click', '.toggle_availability_shipping', function(){
			toggle_available_shipping($(this).data('variant'));
		});

		function toggle_available_shipping(shipping_id){
			token = $('meta[name="csrf-token"]').attr('content');

			var data_for_sending = {};
			data_for_sending.shipping_id = shipping_id;
			data_for_sending._token = token;
			// data_for_sending._method = 'PATCH';
			
			url = "/toggleAvailabilityShipping/" + shipping_id;

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
				    	$('.alerts-holder').prepend('<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+a.name+' <strong>available</strong>!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				    	$row = $('.' + a.class);
				    	$row.find('.availability').removeClass('text-red');
				    	$row.find('.availability').addClass('text-green');
				    	$row.find('.availability').attr('data-original-title', 'Available');
				    	$row.find('.toggle_availability_shipping').text('Make unavailable');

				    } else {
				    	$('.alerts-holder').prepend('<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+a.name+' <strong>unavailable</strong>!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				    	$row = $('.' + a.class);
				    	$row.find('.availability').addClass('text-red');
				    	$row.find('.availability').removeClass('text-green');
				    	$row.find('.availability').attr('data-original-title', 'Unavailable');
				    	$row.find('.toggle_availability_shipping').text('Make available');
				    }
			    },
			    error: function(xhr, status, error) {
			    	$('.alerts-holder').prepend('<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+JSON.parse(xhr.responseText)+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			    }
		  	});	

		  	setTimeout(removeAddedAlerts, 5000);
		}

		function removeAddedAlerts(){
			$('.alerts-holder .alert:last').alert('close');

			if ($('.alerts-holder .alert').length > 1) {
				setTimeout(repeatRemoveAddedAlerts, 3000);
			}		
		}

		function repeatRemoveAddedAlerts(){
			removeAddedAlerts();
		}
	});
	$('body.admin.show.ProductsController, body.admin.new.ProductsController').ready(function(){
		$('[data-toggle="tooltip"]').tooltip();
		
		// prevent 'enter' submitting form.
		// $('form:not(".with-cascading-disabling, #add_variant_form")').on("keypress", ":input:not(textarea)", function(event) {
		//     return event.keyCode != 13;
		// });

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

		$('#toggle_availability_collection').click(function(){
			toggle_available_collection($(this).attr('collection'));
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
				    	$('#toggle_availability').text('Make unavailable');

				    } else {
				    	$('.alerts-holder').prepend('<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+a.name+' <strong>unavailable</strong> for sale!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				    	$('#product_availability').addClass('text-red');
				    	$('#product_availability').text('UNAVAILABLE');
				    	$('#toggle_availability').text('Make available');
				    }
			    },
			    error: function(xhr, status, error) {
			    	$('.alerts-holder').prepend('<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+JSON.parse(xhr.responseText)+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			    }
		  	});	

		  	setTimeout(removeAddedAlerts, 5000);
		}

		function toggle_available_collection(collection_id){
			token = $('meta[name="csrf-token"]').attr('content');

			var data_for_sending = {};
			data_for_sending.collection_id = collection_id;
			data_for_sending._token = token;
			
			url = "/toggleAvailabilityCollection/" + collection_id;

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
				    	$('#toggle_availability_collection').text('Make unavailable');

				    } else {
				    	$('.alerts-holder').prepend('<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+a.name+' <strong>unavailable</strong> for sale!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				    	$('#product_availability').addClass('text-red');
				    	$('#product_availability').text('UNAVAILABLE');
				    	$('#toggle_availability_collection').text('Make available');
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

						new_variant = '<tr class="variant_'+a.variant.id+'" data-id="'+a.variant.id+'" data-inventory="'+a.variant.inventory+'" data-price="'+a.variant.price+'" data-itemID="'+a.variant.id+'"><td class="sortable-handle"><i class="fas fa-arrows-alt-v"></i></td><td class="attribute_1">'+a.variant.attribute_1+'</td>';
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
						$('#available_inventory').text(a.available_inventory);
						$('table.variants_table').append(new_variant);
						$('#no_variants_yet').remove();
						console.log(a);
				    } else {
				    	$('.alerts-holder').prepend('<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+a.message+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>').fadeTo(2000, 500);
				    	$('#available_inventory').text(a.available_inventory);
				    }
			    },
			    error: function(a) {
			    	console.log(a);
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
			$('.alerts-holder .alert:last').alert('close');

			if ($('.alerts-holder .alert').length > 1) {
				setTimeout(repeatRemoveAddedAlerts, 3000);
			}		
		}

		function repeatRemoveAddedAlerts(){
			removeAddedAlerts();
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

		//https://github.com/boxfrommars/rutorika-sortable
		/**
	     *
	     * @param type string 'insertAfter' or 'insertBefore'
	     * @param entityName
	     * @param id
	     * @param positionId
	     */
		var changePosition = function(requestData){
	        $.ajax({
	            'url': '/sort',
	            'type': 'POST',
	            'data': requestData,
	            'success': function(data) {
	                if (data.success) {
	                    $('.alerts-holder').prepend('<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>Variant reordered!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
	                    setTimeout(removeAddedAlerts, 5000);
	                } else {
	                	$.each( data.errors, function( key, value ) {
	                		$('.alerts-holder').prepend('<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+value+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
						});
						setTimeout(removeAddedAlerts, 5000);
	                    
	                }
	            },
	            'error': function(e){
	                $('.alerts-holder').prepend('<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+e.statusText+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
	                console.log(e);
	                setTimeout(removeAddedAlerts, 5000);
	            }
	        });
	    };

	    var $sortableTable = $('.variants_table tbody');
	    if ($sortableTable.length > 0) {
	        $sortableTable.sortable({
	            handle: '.sortable-handle',
	            axis: 'y',
	            update: function(a, b){

	                var entityName = $(this).data('entityname');
	                var $sorted = b.item;

	                var $previous = $sorted.prev();
	                var $next = $sorted.next();

	                if ($previous.length > 0) {
	                    changePosition({
	                        parentId: $sorted.data('parentid'),
	                        type: 'moveAfter',
	                        entityName: entityName,
	                        id: $sorted.data('itemid'),
	                        positionEntityId: $previous.data('itemid'),
	                        _token: $("meta[name='csrf-token']").attr('content')
	                    });
	                } else if ($next.length > 0) {
	                    changePosition({
	                        parentId: $sorted.data('parentid'),
	                        type: 'moveBefore',
	                        entityName: entityName,
	                        id: $sorted.data('itemid'),
	                        positionEntityId: $next.data('itemid'),
	                        _token: $("meta[name='csrf-token']").attr('content')
	                    });
	                } else {
	                    $('.alerts-holder').prepend('<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>Something went wrong!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
	                }
	            },
	            cursor: "move"
	        });
	        setTimeout(removeAddedAlerts, 5000);
	    }
	});

	$('body.admin.show.CollectionsController, body.admin.new.CollectionsController').ready(function(){
		
		$('table.products_table').on('click', '.toggle_availability_product', function(){
			toggle_available_product($(this).data('product'));
		});

		$('table.products_table').on('click', '.remove', function(){
			remove_from_collection($(this).data('product'),$(this).data('collection'));
		});

		$('.add_product_block').click(function(){
			show_add_product_block($(this).data('collectionslug'));
		});

		function show_add_product_block(collection_slug){
			$.get('/getProductsForCollection/'+collection_slug, function(a){ 
		        $table = $('#add_product_form .table tbody');
		        $table.empty();
		        $.each( a.products, function( key, value ) {
		        	product = '<tr><td><div class="form-check"><input class="form-check-input" type="checkbox" name="products[]" value="'+value+'"></div></td><td>'+key+'</td></tr>';
		        	$table.append(product);
		        });
		        
		    });

			$('#add_product_block').removeClass('hide');
			setTimeout(removeAddedAlerts, 5000);

		}

		function toggle_available_product(product_id){
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
				    	$row = $('.' + a.slug);
				    	$row.find('.availability').removeClass('text-red');
				    	$row.find('.availability').addClass('text-green');
				    	$row.find('.availability').attr('data-original-title', 'Available');
				    	$row.find('.toggle_availability_product').text('Make unavailable');

				    } else {
				    	$('.alerts-holder').prepend('<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+a.name+' <strong>unavailable</strong> for sale!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				    	$row = $('.' + a.slug);
				    	$row.find('.availability').addClass('text-red');
				    	$row.find('.availability').removeClass('text-green');
				    	$row.find('.availability').attr('data-original-title', 'Unavailable');
				    	$row.find('.toggle_availability_product').text('Make available');
				    }
			    },
			    error: function(xhr, status, error) {
			    	console.log(xhr);
			    	$('.alerts-holder').prepend('<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+xhr.statusText+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			    }
		  	});	

		  	setTimeout(removeAddedAlerts, 5000);
		}

		function remove_from_collection(product_slug, collection_slug) {
			token = $('meta[name="csrf-token"]').attr('content');

			var data_for_sending = {};
			data_for_sending.product_slug = product_slug;
			data_for_sending.collection_slug = collection_slug;
			data_for_sending._token = token;
			
			url = "/removeFromCollection";

			$.ajaxSetup({
			    beforeSend: function(xhr, type) {
			        if (!type.crossDomain) {
			            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
			        }
			    },
			});
			$.ajax({
			    method: 'POST',
			    url: url,
			    contentType: "json",
			    processData: false,
			    data: JSON.stringify(data_for_sending),
			    success: function(a) {
			    	if(a.success) {
				    	$('.alerts-holder').prepend('<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+a.product_name+' removed from '+a.collection_name+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				    	$('.' + product_slug).remove();
				    } else {
				    	$('.alerts-holder').prepend('<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>Something went wrong!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				    }
			    },
			    error: function(xhr, status, error) {
			    	$('.alerts-holder').prepend('<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+xhr.statusText+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			    }
		  	});	

		  	setTimeout(removeAddedAlerts, 5000);
		}

		function removeAddedAlerts(){
			$('.alerts-holder .alert:last').alert('close');

			if ($('.alerts-holder .alert').length > 1) {
				setTimeout(repeatRemoveAddedAlerts, 3000);
			}		
		}

		function repeatRemoveAddedAlerts(){
			removeAddedAlerts();
		}
	});

	$('body.admin.show.ProductsController, body.admin.show.CollectionsController').ready(function(){ 

		if($('#upload-image-dropzone').length) {
			var myDropzone = new Dropzone('#upload-image-dropzone',{
		        paramName: 'file',
		        addRemoveLinks: true,
		        maxFilesize: 2, // MB
		        maxFiles: 1,
		        acceptedFiles: ".jpeg,.jpg,.png,.gif",
		        init: function() {
				    this.on('addedfile', function(file) {
						if (this.files.length > 1) {
					   		this.removeFile(this.files[0]);
					  	}
					});
				},
				success: function(xhr, url, status) {
			        $('.alerts-holder').prepend('<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>Header image uploaded!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			        $('.dz-progress').css('display', 'none');
			        $('.dz-remove').html('Remove file and upload new image');
			        $(".header-image-block img").attr("src",url);
			        setTimeout(removeAddedAlerts, 5000);
			        $('#uploadImage').modal('hide');
			        $('.header-image-block.hide').removeClass('hide');
			        $('.delete-header-image-option.hide').removeClass('hide');
			    },
				error: function(xhr, error, status) {
					$('.dz-details, .dz-progress').css('display', 'none');
					$('.dz-remove').html('Remove upload');

					if (error !== null && typeof error === 'object') {
						$.each( error.errors.file, function( key, value ) {
							$('.alerts-holder').prepend('<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+value+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
						});
					} else {
						$('.alerts-holder').prepend('<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+error+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
						
					}
		    		setTimeout(removeAddedAlerts, 5000);
				}

			});
		}

		function removeAddedAlerts(){
			$('.alerts-holder .alert:last').alert('close');

			if ($('.alerts-holder .alert').length > 1) {
				setTimeout(repeatRemoveAddedAlerts, 3000);
			}		
		}

		function repeatRemoveAddedAlerts(){
			removeAddedAlerts();
		}
	});

	$('body.admin.index.InventoryController').ready(function(){
		// $('.inventory-table tbody tr').click(function(){
		// 	product_id = $(this).data('product');
		// 	$('.product-block').addClass('hide');
		// 	$('.block-'+product_id).removeClass('hide');
		// })
		//highlight whole input of inputs on form
		$('.delivery-modal').on('click', 'input[type=number]', function(){
			$(this).select();
		});
	});

	$('body.admin.create.InventoryController').ready(function(){

		//change datepicker if javascript is available
		$('.datepicker').attr('type','text');
		$('.datepicker').prop('readonly', true);
		$('.datepicker').datepicker({
		  format: 'yyyy-mm-dd',
		  autoHide: true,
		  trigger: false,
		  startDate: new Date
		});

		//highlight whole input of inputs on form
		$('.product-block').on('click', 'input[type=number]', function(){
			$(this).select();
		});

		$('#confirmProductsModal').on('show.bs.modal', function (e) {
			supplier = $('#supplier').val();
			expected_arrival = $('#expected_arrival').val();

			switch(supplier.length) {
				case 0:
					if(!$("#confirm-supplier").hasClass("text-red")){
					    $("#confirm-supplier").addClass("text-red");
					}
					$('#confirm-supplier').html('None entered');
					break;
				default:
					$('#confirm-supplier').removeClass('text-red');
					$('#confirm-supplier').html(supplier);
					break;
			}
			switch(expected_arrival.length) {
				case 0:
					if(!$("#confirm-date-arrival").hasClass("text-red")){
					    $("#confirm-date-arrival").addClass("text-red");
					}
					$('#confirm-date-arrival').html('None entered');
					break;
				default:
					$('#confirm-date-arrival').removeClass('text-red');
					$('#confirm-date-arrival').html(expected_arrival);
					break;
			}
			stocked_up_products = $('.input-stockup').filter(function() {
			    return $(this).val() > 0;
			  });

			table = $('.confirm-products tbody');
			table.empty();
			$('.input-stockup').each(function() {
				if($(this).val() > 0) {
					var tr = document.createElement("tr");
					var td = document.createElement("td");
					var txt = document.createTextNode($(this).data('description'));
					row = "<tr class='confirmed-products'><td>"+$(this).data('description')+"</td><td class='text-right'>"+$(this).val()+"</td></tr>";
					table.append(row);
				}
			});

			if($('.confirmed-products').length < 1) {
				row = "<tr class='confirmed-products'><td class='text-red text-center' colspan='2'>No products selected</td></tr>";
					table.append(row);
			}

			if($('#confirmProductsModal .text-red').length > 0) {
				$('.confirm-products-submit').prop('disabled', true);
				$('.confirm-products-submit').html('Resolve errors in red');
			} else {
				$('.confirm-products-submit').prop('disabled', false);
				$('.confirm-products-submit').html('Stock up products');
			}
		});

		function removeAddedAlerts(){
			$('.alerts-holder .alert:last').alert('close');

			if ($('.alerts-holder .alert').length > 1) {
				setTimeout(repeatRemoveAddedAlerts, 3000);
			}		
		}

		function repeatRemoveAddedAlerts(){
			removeAddedAlerts();
		}
	});

});

$('body.orders_index').ready(function(){
	$('.dropright').on('shown.bs.dropdown', function(){
		$(this).addClass('active');
	});
	$('.dropright').on('hidden.bs.dropdown', function(){
		$(this).removeClass('active');
	});

	$('body.order_page .cart-modal table.table').on('click', 'a.remove', function(){
		removeFromCart($(this).data('company'), $(this).data('rowid'));
	});

	$('body.order_page .product-modal table.table').on('click', 'tbody tr', function(event){

		if (event.target.type !== 'checkbox') {
			$(':checkbox', this).trigger('click');
		}
	});

	var debounceChangeQuantity = function(object) {debounce(changeQuantity(object.data('company'), object.data('rowid'), object.val()), 1000, false)};

	$('body.order_page .cart-modal table.table').on('change', '.item-quantity', function(){
		debounceChangeQuantity($(this));
	});

	$('body.order_page .cart-modal table.table').on('click', '.quantity-adjuster.plus', function(){
		input = $(this).parent().next();
		max = input.attr('max');

		// For some browsers, `attr` is undefined; for others,
		// `attr` is false.  Check for both.
		if (typeof max == typeof undefined || max == false) {
			new_val = +input.val() + 1;
			input.val(new_val);
		} else {
			val = +input.val();
			if(val + 1 <= max) {
				new_val = +input.val() + 1;
				input.val(new_val);
			}
		}
		debounceChangeQuantity(input);
	});

	$('body.order_page .cart-modal table.table').on('click', '.quantity-adjuster.minus', function(){
		input = $(this).parent().next();
		val = +input.val();
		if(val - 1 >= 1) {
			new_val = +input.val() - 1;
			input.val(new_val);
		}
		debounceChangeQuantity(input);
	});

	// Returns a function, that, as long as it continues to be invoked, will not
	// be triggered. The function will be called after it stops being called for
	// N milliseconds. If `immediate` is passed, trigger the function on the
	// leading edge, instead of the trailing.
	window.debounce = function (func, wait, immediate) {
		var timeout;
		return function() {
			var context = this, args = arguments;
			var later = function() {
				timeout = null;
				if (!immediate) func.apply(context, args);
			};
			var callNow = immediate && !timeout;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
			if (callNow) func.apply(context, args);
		};
	}

	$('div.catalogue table tbody tr').addClass('hover-pointer');

	$('.item-quantity').click(function(){
		$(this).select();
	});

	$('body.order_page .shipping').on('click', '.shipping-method-radio', function(){
		$('.shipping-mode').removeClass('selected');
		$('.shipping'+$(this).val()).addClass('selected');
	});

	if ($('#checkout_form').length === 1) {
		$('#checkout_form').validate({
			rules: {
				email: {
					required: true,
					email:true,
				},
				first_name: {
					required: true,
				},
				last_name: {
					required: true,
				},
				country_code: {
					required: true,
				},
				contact_number: {
					required: true,
					digits: true,
				},
				shipping_address_1: {
					required: true,
				},
				city: {
					required: true,
				},
				zip_code: {
					required: true,
					digits: true
				},
				country: {
					required: true,
				}
			},
			submitHandler: function(form) {
			    form.submit();
			}
		});
	}

	window.changeQuantity = function(companyId, rowId, quantity){

		token = $('meta[name="csrf-token"]').attr('content');

		var data_for_sending = {};
		data_for_sending.rowId = rowId;
		data_for_sending.slug = companyId;
		data_for_sending.quantity = quantity;
		data_for_sending._token = token;
		// data_for_sending._method = "PATCH";
		
		url = "/changeQuantity/" + companyId + "/" + rowId;

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
		    contentType: "json",
		    processData: false,
		    success: function(a) {
		    	if(a.success) {
			    	$('.cart-item.'+a.rowId+' .item_price').text(a.item_price);
			    	$('#cart_itemcount').text(a.cart_itemcount);
			    	$('#cart_total').text(a.currency + " " + a.cart_total);
			    } else {
			    	$('.alerts-holder').prepend('<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>Error in updating cart item<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
					setTimeout(removeAddedAlerts, 5000);
			    }
		    }
	  	});	
	}

	function removeFromCart(companySlug, rowId){

		token = $('meta[name="csrf-token"]').attr('content');

		var data_for_sending = {};
		data_for_sending.rowId = rowId;
		data_for_sending.slug = companySlug;
		data_for_sending._token = token;
		// data_for_sending._method = 'PATCH';
		
		url = "/removeFromCart/" + companySlug + "/" + rowId;

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
		    	if(a.success) {
			    	$('.alerts-holder').prepend('<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss><strong>'+a.name+'</strong> removed!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			    	$('#cart_itemcount').text(a.cart_itemcount);
			    	$('#cart_total').text(a.cart_total);
			    	$row = $('.' + a.rowId);
			    	$row.remove();
			    	if ($('.cart-modal table.table tr.cart-item').length === 0) {
			    		$('#cart').modal('hide');
			    		location.reload();
			    	}
			    } else {
			    	$('.alerts-holder').prepend('<div class="alert alert-success alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+a.name+'!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
			    }
		    },
		    error: function(xhr, status, error) {
		    	$('.alerts-holder').prepend('<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>'+JSON.parse(xhr.responseText)+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
		    }
	  	});	

	  	setTimeout(removeAddedAlerts, 5000);
	}

    $("body.order_page form .form-check-input").change(function() {
		if ($("body.order_page form input:checkbox:checked").length > 0)
		{
			$('.add-to-cart').prop("disabled", false);
			// $('.tooltip-wrapper').tooltip('hide');
		}
		else
		{
			$('.add-to-cart').prop("disabled", true);
			// $('.tooltip-wrapper').tooltip('show');
		}
	});

    //disable submit button if no radio button is selected
	if ($("body.order_page .shipping form input:radio:checked").length === 0)
	{
		$('.checkout').addClass("disabled");
	} else {
		$('.shipping'+$('body.order_page .shipping input:radio:checked').val()).addClass('selected');
	}
	
	$('body.order_page .shipping').on('change', 'input:radio', function(){
		if ($("body.order_page .shipping form input:radio:checked").length > 0)
		{
			$('.checkout').removeClass("disabled");
		}
	});

	$('body.order_page .shipping').on('click', '.checkout.disabled', function(e){
		e.preventDefault();

		$('.alerts-holder').prepend('<div class="alert alert-error alert-dismissible fade show z-depth-1-half" role="alert" data-auto-dismiss>Select a shipping mode<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
		setTimeout(removeAddedAlerts, 3000);
	});

	var header = $(".header");
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();
        if (scroll >= 80) {
            header.addClass("scrolled-down");
        } else {
            header.removeClass("scrolled-down");
        }
    });

    $(window).scrollTop($(window).scrollTop()+1);
    $(window).scrollTop($(window).scrollTop()-1);

    // $('.tooltip-wrapper').tooltip({position: "bottom"});
    function removeAddedAlerts(){
		$('.alerts-holder .alert:last').alert('close');

		if ($('.alerts-holder .alert').length > 1) {
			setTimeout(repeatRemoveAddedAlerts, 3000);
		}		
	}

	function repeatRemoveAddedAlerts(){
		removeAddedAlerts();
	}
});