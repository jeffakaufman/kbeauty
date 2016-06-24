try{Typekit.load();}catch(e){} //Typekit font requirement, do not remove
(function( $ ) {
  $(window).load(function() {
  	
  	if(device.desktop()) var wW = window.outerWidth;
	else var wW = $(window).width();
	
	var is_windows = navigator.appVersion.indexOf("Win") != -1;
	var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
	
	if (is_windows && is_chrome) wW = parseInt(wW) - 16;
	$(window).resize(function() {
		if (is_windows && is_chrome) wW = parseInt(wW) - 16;
	});
	//console.log(wW);
    /*$('#nav li span').html(function(i, oldHTML) {
	    return oldHTML.replace(/ /g, '<br/>');
	});*/

	var catWidth = $('#nav li:first-child').width() * 0.9;
	$('#nav li').each(function() {
		$(this).height(catWidth);
	});

	// var q = 1;
	// $('body.cms-customer-service .col-main #faqs h3').each(function() {
	// 	$(this).next('p').wrap('<div class="content"></div>');
	// 	$(this).next('.content').attr('id','faq'+q);
	// 	$(this).next('.content').andSelf().wrapAll('<div class="faq"></div>');
	// 	$(this).wrap('<a href="#faq'+q+'" class="faq-q"></a>');
	// 	q++;
	// });
	// $('body.cms-customer-service a.faq-q').click(function(e) {
	// 	e.preventDefault();
	// 	$(this).next('.content').addClass('opened');
	// });
	
	var isOpen = false;
	$('input#search').val('');
	$('#searchBtn').click(function() {
		if(isOpen == false){
			$('#search_mini_form').addClass('open').find('input[type="text"]').focus();
			isOpen = true;
		} else {
			$('#search_mini_form').removeClass('open');
			isOpen = false;
		}
	});
	$('input#search').blur(function(){
		$('#search_mini_form').removeClass('open');
		$(this).val('');
		isOpen = false;
	});

	$('body.checkout-onepage-index .breadcrumbs').prependTo('.main');

	var shipping, newShipping, detectTax;
	
	$('#checkout-review-table td').each(function() {
		var tdtext = $(this).html();
		if (tdtext.indexOf('Shipping') != -1) {
			shipping = $(this).next('td').find('.price').text();
	    }
	});
	//shipping = shipping.substr(1);
	//console.log('shipping 1: '+shipping);

	$('#onestepcheckout-place-order').click(function() {
		$('html,body').animate({
			scrollTop: $('body').find('.validation-advice').filter('.validation-advice:first').first().parent().offset().top
		}, 500);
	});
	
	//console.log(shipping);
	$('#co-shipping-method-form .button, #checkout-step-payment .button').click(function() {
		var detectTax = false;
		$('#checkout-review-table td').each(function() {
			var tdtext = $(this).html();
			if (tdtext.indexOf('Tax') != -1) detectTax = true;
		});
		// $('#checkout-review-table td').each(function() {
		// 	detectTax = $(this).filter(function( index ) {
		//     	return $('Tax', this ).length === 1;
		//     }).next('td').find('.price').text();
		// });
		//var detectTax = $('#checkout-review-table tr:nth-child(2) td:first-child').text();
		//console.log(detectTax);
		//if ($('#checkout-review-table tr').length <= 3 && detectTax.indexOf('Shipping') != -1 && detectTax.indexOf('Total') != -1) {
		//if (!detectTax) {
			//console.log('get tax');
			$.ajax({
	            type: "POST",
	            dataType: "HTML",
	            data: { tax : '1' },
            	url: "/index.php/checkout/cart/update",
	            success: function (data) {
	                //console.log(data);
	                finishAjax2('ajaxJS', escape(data));
	            },
	            error: function(data){
	            }
	        });
		//}
        lookupShipping();
	});
	// $('#billing-buttons-container .button').click(function() {
 //    });
	function lookupShipping() {
		//console.log('lookupShipping');
		shipping = $('#checkout-review-table td:contains("Shipping")').next('td').find('.price').text();
		//console.log('shipping: '+shipping);
		var checkShipping = setInterval(function(){
			newShipping = $('#shipping_method-progress-opcheckout .price').text();
			//newShipping = newShipping.substr(1);
			//console.log('newShipping: '+newShipping);
			if (newShipping && newShipping != shipping) {
				clearInterval(checkShipping);
				setShipping(newShipping);
			} else {
				clearInterval(checkShipping);
				lookupShipping();
			}
		}, 1000);
	}
	function setShipping(sh) {
		//console.log('setShipping: '+sh);
		var hasShipping = false;
		var total = 0;
		$('#checkout-review-table td').each(function() {
			var tdtext = $(this).html();
			if (tdtext.indexOf('Shipping') != -1) hasShipping = true;
		});
		//if ($('#checkout-review-table tr').length > 3) {
		if (hasShipping) {
			//console.log(hasShipping);
			var shippingMethod = $('#shipping_method-progress-opcheckout .box-content').clone().children().remove().end().text();
			$('#checkout-review-table tr:nth-child(2) td:first-child').html(shippingMethod);
			$('#checkout-review-table tr:nth-child(2) .price').text(sh);
		} else {
			$('<tr><td class="a-right">Shipping</td><td class="a-right"><span class="price">'+sh+'</span></td></tr>').insertAfter('#checkout-review-table tr:first-child');
		}
		$('.col-right #checkout-review-table tr').not(':last-child').each(function() {
			var price = $(this).find('.price').text();
			//console.log('price: '+price);
			price = price.replace('$','');
			total = Number(total) + Number(price);
			total = Math.round( total * 100 ) / 100;
			total = total.toFixed(2);
			$('#checkout-review-table tr:last-child .price').html('$'+total);
		});
	}

	$('ul.messages').prepend('<li class="closeBtn"><span class="icon icon-delete"></span></li>');
	$('.messages .closeBtn').click(function() {
    	$('.messages').fadeOut();
    });
    setTimeout(function(){
    	$('.messages .success-msg').parent().fadeOut();
	}, 5000);

	//$('#payment-tool-tip').appendTo('.col-right');

	$('<div class="ingredients"><h3>Ingredients</h3></div>').insertBefore('.product-description blockquote');
	$('.ingredients').click(function() {
		$(this).find('h3').toggleClass('open');
		$(this).next('blockquote').toggleClass('opened');
	});
	
	$('.quantity .icon-minus.onepage').click(function() {
		$(this).css('cursor','progress');
		var id = $(this).data('id');
		var curValue = parseInt($(this).next('input.qty').val());
		if (curValue != 1) var newValue = curValue-1;
		else newValue = 1;
		$(this).next('input.qty').val(newValue);
		var curQty = (typeof curQty === 'undefined') ? $('.right-off-canvas-toggle .count').text() : 0;
		if (curQty != '0') $('.right-off-canvas-toggle .count').html(parseInt(curQty) - 1);
		$('#checkoutForm'+id).submit();
	});
	$('.quantity .icon-plus.onepage').click(function() {
		$(this).css('cursor','progress');
		var id = $(this).data('id');
		var curValue = $(this).prev('input.qty').val();
		var newValue = parseInt(curValue) + 1;
		$(this).prev('input.qty').val(newValue);
		$('#cart['+id+'][qty]').val(newValue);
		var curQty = $('.right-off-canvas-toggle .count').text();
		$('.right-off-canvas-toggle .count').html(parseInt(curQty) + 1);
		$('#checkoutForm'+id).submit();
	});

	// var quantityChanger = function() {
	// 	$('.quantity .icon-minus').click(function() {
	// 		var id = $(this).data('id');
	// 		//console.log(id);
	// 		var curValue = parseInt($(this).next('input.qty').val());
	// 		if (curValue != 1) var newValue = curValue-1;
	// 		else newValue = 1;
	// 		$(this).next('input.qty').val(newValue);
	// 		updateCart(id,newValue);
	// 		if ($(this).hasClass('cartPage') || $(this).hasClass('onepage')) $('#cart['+id+'][qty]').val(newValue);
	// 		if ($(this).hasClass('sidebar')) $('#cart'+id+', #cart_onepage'+id).val(newValue);
	// 		var curQty = (typeof curQty === 'undefined') ? $('.right-off-canvas-toggle .count').text() : 0;
 //            if (curQty != '0') $('.right-off-canvas-toggle .count').html(parseInt(curQty) - 1);
	// 	});
	// 	$('.quantity .icon-plus').click(function() {
	// 		var id = $(this).data('id');
	// 		var curValue = $(this).prev('input.qty').val();
	// 		var newValue = parseInt(curValue) + 1;
	// 		$(this).prev('input.qty').val(newValue);
	// 		if ($(this).hasClass('cartPage') || $(this).hasClass('onepage')) $('#cart['+id+'][qty]').val(newValue);
	// 		if ($(this).hasClass('sidebar')) $('#cart'+id+', #cart_onepage'+id).val(newValue);
	// 		var curQty = $('.right-off-canvas-toggle .count').text();
 //            $('.right-off-canvas-toggle .count').html(parseInt(curQty) + 1);
	// 		updateCart(id,newValue);
	// 	});
	// }
	// quantityChanger();
	// function updateCart(productId, qty) {
 //        $.ajax({
 //            type: "POST",
 //            dataType: "HTML",
 //            data: { productId : productId, qty : qty },
 //            url: "/scripts/ajax.php?productId=" + productId + "&qty=" + qty,
 //            success: function (data) {
 //            	console.log(data);
 //            	finishAjax('ajaxJS', escape(data));
 //            },
 //            error: function(data){
 //            }
 //        });
 //    }
 //    function finishAjax(id, data){
 // 	 	jQuery('#'+id).html(unescape(data));
	// 	updateTotals();
	// }
	// var query = window.location.search.substring(1);
 //    var vars = query.split("&");
 //    for (var i=0;i<vars.length;i++) {
 //        var pair = vars[i].split("=");
 //        if(pair[0] == 'newsletter'){
 //        	console.log('newsletter');
 //        	$('html,body').animate({
	// 			scrollTop: $('#newsletter').offset().top
	// 		}, 500);
	// 	}
	// }

	[].slice.call( document.querySelectorAll( 'select.cs-select' ) ).forEach( function(el) {	
			new SelectFx(el);
		} );
	var cartActions = function() {
		//console.log('cartActions');

		// [].slice.call( document.querySelectorAll( '#quickView select.cs-select' ) ).forEach( function(el) {	
		// 	new SelectFx(el);
		// } );
		//console.log('loaded');
		$('img').bind('contextmenu', function(e) {
		    return false;
		}); 
		$('.product-view .quantity .icon-minus, .cart-list .quantity .icon-minus, #quickView .quantity .icon-minus').click(function() {
			var curValue = parseInt($(this).next('input.qty').val());
			if (curValue != 1) var newValue = curValue-1;
			else newValue = 1;
			$(this).next('input.qty').val(newValue);
		});
		$('.product-view .quantity .icon-plus, .cart-list .quantity .icon-plus, #quickView .quantity .icon-plus').click(function() {
			var curValue = $(this).prev('input.qty').val();
			var newValue = parseInt(curValue) + 1;
			$(this).prev('input.qty').val(newValue);
		});
		$('.share a.facebook, .share a.pinterest').click(function(e) {
			e.preventDefault();
			window.open($(this).attr('href'),"share","toolbar=no, scrollbars=no, resizable=yes, width=900, height=400");
			return false;
		});
		
		$('.product-shop').each(function() {
			$(this).find('.quantity').appendTo($(this).find('.product-options dd'));
		}); 
		
		$('.color-swatch-wrapper li img').wrap('<span></span>');

		$('div.super-attribute-select .cs-options li[data-value=""]').remove();

		$('.product-1386 .cs-options li[data-value*="100"]').prependTo($('.product-1386 .cs-options ul'));
		$('.product-1386 .color-swatch-wrapper li.color-swatch-132-100').prependTo($('.product-1386 .color-swatch-wrapper ul'));
		$('.product-1386 .product-options .input-box select option[value*="100"]').insertAfter($('.product-1386 .product-options .input-box select option:first-child'));
		$('.product-1386 .product-options .input-box select option[value*="100"]').prop('selected', true);

		var variable = 'color';
	    var query = window.location.search.substring(1);
	    var vars = query.split("&");
	    for (var i=0;i<vars.length;i++) {
	        var pair = vars[i].split("=");
	        if(pair[0] == variable){
	        	$('.product-view .color-swatch-wrapper input[type=hidden]').val(pair[1]);
	        	$('.product-view .color-swatch-wrapper li.color-swatch-132-'+pair[1]).addClass('active');
	        	$('.product-view select.super-attribute-select').val(pair[1]);
	        	$('.product-view .cs-options li[data-value='+pair[1]+']').addClass('cs-selected');
	        	$('.product-view .cs-placeholder').text( $('.cs-options li[data-value='+pair[1]+'] span').text() );
	        } else if ($('.product-view select.super-attribute-select option:first-child').val() == '') {
	        	$('.product-view .color-swatch-wrapper input[type=hidden]').val($('.product-options .input-box select option:nth-child(2)').val() );
	        	if ($('.product-view .color-swatch-wrapper li:first-child').hasClass('is-disabled-option')) {
	        		$('.product-view .color-swatch-wrapper li:nth-child(2)').addClass('active');
	        	} else {
	        		$('.product-view .color-swatch-wrapper li:first-child').addClass('active');
	        	}
	        	$('.product-view div.super-attribute-select .cs-options li:nth-child(1)').addClass('cs-selected');
	        	$('.product-view div.super-attribute-select .cs-placeholder').html($('div.super-attribute-select .cs-options li:nth-child(1) span').text());
	  			$('.product-view .product-options .input-box select option:nth-child(2)').prop('selected', true);
	        }
	    }
	    if ($('#quickView select.super-attribute-select option:first-child').val() == '') {
        	$('#quickView .color-swatch-wrapper input[type=hidden]').val($('#quickView select.super-attribute-select option:nth-child(2)').val() );
        	if ($('#quickView .color-swatch-wrapper li:first-child').hasClass('is-disabled-option')) {
        		$('#quickView .color-swatch-wrapper li:nth-child(2)').addClass('active');
        	} else {
        		$('#quickView .color-swatch-wrapper li:first-child').addClass('active');
        		$('#quickView div.super-attribute-select .cs-options li:nth-child(1)').addClass('cs-selected');
        		$('#quickView div.super-attribute-select .cs-placeholder').html($('div.super-attribute-select .cs-options li:nth-child(1) span').text());
        	}
  			$('#quickView select.super-attribute-select option:nth-child(2)').prop('selected', true);
        }
	    
		var getColor = function(id) {
			var val = $('.product-'+id+' select.super-attribute-select option:selected').val();
			var text = $('.product-'+id+' select.super-attribute-select option:selected').text();
			//console.log(text);
			$('.product-'+id+' .cs-options li').removeClass('cs-selected');
			$('.product-'+id+' select.super-attribute-select').prev('.cs-options').find('li').filter(function( index ) {
				return $(this).attr('data-value') === val;
			}).addClass('cs-selected');
			$('.product-'+id+' select.super-attribute-select').prev().prev('.cs-placeholder').text(text);
		}
		$('select.super-attribute-select').change(function() {
			getColor($(this).attr('id'));
		});
		$('div.super-attribute-select li').click(function() {
			var value = $(this).attr('data-value');
			$('.color-swatch-wrapper li').removeClass('active');
			$('.color-swatch-wrapper li.color-swatch-132-'+value).addClass('active');
			$('.product-view .product-options .input-box select option[value*='+value+']').prop('selected', true);
		});

		// $('.product-1386 .cs-options li').removeClass('cs-selected');
		// $('.product-1386 .cs-options li[data-value*="100"]').addClass('cs-selected');
		// var newtext = $('.product-1386 .cs-options li[data-value*="100"]').text();
		// $('.product-1386 .cs-placeholder').text(newtext);
		// $('.product-1386 .product-options .input-box select option[value*="100"]').prop('selected', true);
		// $('.product-1386 .color-swatch-wrapper li').removeClass('active');
		// $('.product-1386 .color-swatch-wrapper li.color-swatch-132-100').addClass('active');

		var color, colorArray, colorCode, groupArray, colorCount, groupCount, currentType, currentNumber, nextNumber, prevNumber;
		var isQuickview = false;
		if (typeof group !== 'undefined') groupCount = group.length;
		//console.log('groupCount '+groupCount);
		$('.color-swatch-wrapper li').click(function() {
//console.log('one');
			var productID = $(this).attr('data-product');
			if ($(this).parent().hasClass('colorswatch-attribute')) {
				var isQuickview = true;
			}
			//console.log(productID);
			// var colorID = $('.product-'+productID+' #hidden-attribute-132').val();
			// var colorName = $('.product-'+productID+' .cs-options li[data-value='+colorID+'] span').text();
			// console.log(productID);
			// console.log(colorID);
			// console.log(colorName);
			//$('.product-'+productID+' .cs-options li').removeClass('cs-selected');
			//$('.product-'+productID+' .cs-options li[data-value='+colorID+']').addClass('cs-selected');
			//$('.product-'+productID+' .cs-placeholder').text(colorName);
			$('.product-'+productID+' .color-swatch-wrapper li').removeClass('active');
			$(this).addClass('active');
			if ($(this).hasClass('is-disabled-option')) {
				$('.product-'+productID+' .availability.out-of-stock').fadeIn();
				$('.product-'+productID+' .color-swatch-wrapper li').removeClass('active');
				$(this).addClass('active');
			} else {
				$('.product-'+productID+' .availability.out-of-stock').fadeOut();
			}
			var classes = $(this).attr('class');
			if (classes.indexOf('active')) classes = classes.replace(' active','');
			if (classes.indexOf('is-disabled-option')) classes = classes.replace(' is-disabled-option','');
			if (classes.indexOf('colorswatch-swatch-container')) classes = classes.replace(' colorswatch-swatch-container','');
			//console.log(classes);
			colorCode = classes.replace(/color-swatch-132-/g,"");
			//console.log(colorCode);
			//$('.product-'+productID+' .product-options .input-box select option[value*='+colorCode+']').prop('selected', true);
// console.log($( '.product-'+productID+' .product-options .input-box select option:selected' ).text());
			if (isQuickview) {
				$('#attribute132-'+productID).val(colorCode);
				$('#hidden-attribute-'+productID+'-132').val(colorCode);
			}
			color = classes.replace(/-/g,"_");
			colorCount = eval(color).length;
			color = eval(color + '[0].src');
			//console.log(color);
			//console.log(color);
            $('.product-'+productID+' .main-image').attr('src',color);
            $('.product-'+productID+' .product-image').data('type','color');
            $('.product-'+productID+' .product-image').data('number',0);
            getColor(productID);
        });
		$('div.cs-select').click(function() {
//console.log('two');
	    	if ($(this).hasClass('cs-active') && $(this).prev('.availability.out-of-stock').is(':hidden')) {
//console.log('two A');
	    		$(this).find('.cs-placeholder').html('Select an option');
	    	} else {
//console.log('two B');
	    		$(this).find('.cs-placeholder').html($('.cs-options .cs-selected span').text());
	    	}
	    	$(this).mouseleave(function() {
//console.log('two C');
	    		$(this).removeClass('cs-active');
	    		$(this).find('.cs-placeholder').html($(this).find('.cs-options .cs-selected span').text());
	    	});
	    });
		$('.cs-options li').click(function() {
//console.log('three');
			var productID = $(this).parents('.input-box').attr('data-product');
			var colorID = $(this).data('value');
			colorCode = 'color_swatch_132_'+colorID;
			color = eval(colorCode + '[0].src');
            $('.product-'+productID+' .main-image').attr('src',color);
            $('.product-'+productID+' .product-image').data('type','color');
            $('.product-'+productID+' .product-image').data('number',0);

			$("#hidden-attribute-132").val(colorID);

// console.log(colorID);
		});
		$('#quickView .super-attribute-select').change(function() {
//console.log('four');
			var productID = $(this).attr('id');
			productID = productID.replace(/attribute132-/,'');
			$('#quickView .colorswatch-attribute li').removeClass('active');
			$('#quickView .colorswatch-attribute li.color-swatch-132-'+$(this).val()).addClass('active');
			color = 'color_swatch_132_'+$(this).val();
			color = eval(color + '[0].src');
			$('#hidden-attribute-'+productID+'-132').val( $(this).val() );
			$('.product-'+productID+' .main-image').attr('src',color);
            $('.product-'+productID+' .product-image').data('type','color');
            $('.product-'+productID+' .product-image').data('number',0);
            //getColor(productID);
		});
        $('.product-essential.in-stock .product-image .icon-arrow_left').click(function() {
        	var productID = $(this).attr('data-product');
        	var classes = $('.product-'+productID+' .color-swatch-wrapper li.active').attr('class');
        	classes = classes.replace(' active','');
        	if (classes.indexOf('is-disabled-option')) classes = classes.replace(' is-disabled-option','');
        	if (classes.indexOf('colorswatch-swatch-container')) classes = classes.replace(' colorswatch-swatch-container','');
        	color = classes.replace(/-/g,"_");
        	currentType = $('.product-'+productID+' .product-image').attr('data-type');
        	currentNumber = $('.product-'+productID+' .product-image').attr('data-number');
        	currentNumber2 = parseInt(currentNumber) + 1;
        	colorCount = eval(color).length;
        	colorCount2 = parseInt(colorCount) - 1;
        	if (currentType == 'group') {
        		nextNumber = colorCount - 1;
        		color = eval(color + '[' + nextNumber + '].src');
        		$('.product-'+productID+' .main-image').attr('src',color);
        		$('.product-'+productID+' .product-image').attr('data-type','color');
            	$('.product-'+productID+' .product-image').attr('data-number',nextNumber);
            } else if (currentType == 'color' && colorCount == 1) {
        		$('.product-'+productID+' .main-image').attr('src',group[0].src);
        		$('.product-'+productID+' .product-image').attr('data-type','group');
            	$('.product-'+productID+' .product-image').attr('data-number',0);
        	} else if (currentType == 'color' && colorCount > 1) {

        		if (currentNumber != 0) {
	        		nextNumber = currentNumber - 1;
	        		$('.product-'+productID+' .product-image').attr('data-number', nextNumber);
	        		color = eval(color + '[' + nextNumber + '].src');
	        		$('.product-'+productID+' .main-image').attr('src',color);
	        	} else {
	        		$('.product-'+productID+' .main-image').attr('src',group[0].src);
        			$('.product-'+productID+' .product-image').attr('data-type','group');
            		$('.product-'+productID+' .product-image').attr('data-number',0);
	        	}
        	} 
        });
        $('.product-essential.in-stock .product-image .icon-arrow_right').click(function() {
        	var productID = $(this).attr('data-product');
        	var classes = $('.product-'+productID+' .color-swatch-wrapper li.active').attr('class');
        	if (classes) {
        		classes = classes.replace(' active','');
        		if (classes.indexOf('is-disabled-option')) classes = classes.replace(' is-disabled-option','');
        		if (classes.indexOf('colorswatch-swatch-container')) classes = classes.replace(' colorswatch-swatch-container','');
        		color = classes.replace(/-/g,"_");
        		colorCount = eval(color).length;
	        	currentType = $('.product-'+productID+' .product-image').attr('data-type');
	        	currentNumber = $('.product-'+productID+' .product-image').attr('data-number');
	        	currentNumber2 = parseInt(currentNumber) + 1;
	        	
	        	if (currentType == 'group') {
	        		color = eval(color + '[0].src');
	        		$('.product-'+productID+' .main-image').attr('src',color);
	        		$('.product-'+productID+' .product-image').attr('data-type','color');
	            	$('.product-'+productID+' .product-image').attr('data-number',0);
	            	
	        	} else if (currentType == 'color' && colorCount == 1) {
	        		$('.product-'+productID+' .main-image').attr('src',group[0].src);
	        		$('.product-'+productID+' .product-image').attr('data-type','group');
	            	$('.product-'+productID+' .product-image').attr('data-number',0);
	            	
	        	} else if (currentType == 'color' && colorCount > 1) {
	        		if (colorCount != currentNumber2) {
		        		nextNumber = parseInt($('.product-'+productID+' .product-image').data('number')) + 1;
		        		$('.product-'+productID+' .product-image').attr('data-number', nextNumber);
		        		color = eval(color + '[' + nextNumber + '].src');
		        		$('.product-'+productID+' .main-image').attr('src',color);
		        		
		        	} else {
		        		$('.product-'+productID+' .main-image').attr('src',group[0].src);
	        			$('.product-'+productID+' .product-image').attr('data-type','group');
	            		$('.product-'+productID+' .product-image').attr('data-number',0);
		        	}
	        	}  
	        }
        });
		
	  	/*$('.super-attribute-select').addClass('cs-skin-slide').addClass('cs-select');
	  	var attributeCount = $('.super-attribute-select li').length;
	  	$('.cs-skin-slide .cs-options').css('height',attributeCount+'00%');
	  	$('.cs-skin-slide.cs-active::before').css('transform','scale3d(1.1,'+attributeCount+',1)').css('-webkit-transform','scale3d(1.1,'+attributeCount+',1)');*/
	}
	cartActions();

	$('#authnetcim_card_id').change(function() {
		if(this.value) $('#authnetcim_cc_type_cvv_div').show();
	});

	$('#faqs a[href=#shipping]').click(function(e) {
		e.preventDefault();
		$('#shipping').prev('h2').click();
	});
	$('#faqs a[href=#returns]').click(function(e) {
		e.preventDefault();
		$('#returns').prev('h2').click();
	});
	$('#faqs a[href=#contactus]').click(function(e) {
		e.preventDefault();
		$('html,body').animate({
			scrollTop: $('#contactus').offset().top
		}, 500);
		$('#contactus .button').click();
	});
	$('#faqs a[href=#login]').click(function(e) {
		e.preventDefault();
		$('#loginBtn').click();
		$('html,body').animate({
			scrollTop: $('body').offset().top
		}, 500);
	});
	$('.cms-customer-service .footer-container .links .contact').click(function(e) {
		e.preventDefault();
		$('#contactus .button').click();
		$('html,body').animate({
			scrollTop: $('#contactus').offset().top
		}, 500);
	});

	// $('body.cms-customer-service .col-main .std ul').addClass('accordion').attr('data-accordion','');
	// $('body.cms-customer-service .col-main .std li').each(function() {
	// 	$(this).addClass('accordion-navigation');
	// 	var text;
	// 	$(this).find('h2').each(function() {
	// 		var replace = new Array(" ", "[\?]", "\!", "\%", "\&");
	//     	var by = new Array("-", "", "", "", "and");
	// 		text = $(this).text();
	// 		for (var i=0; i<replace.length; i++) {
	// 	        text = text.replace(new RegExp(replace[i], "g"), by[i]);
	// 	        text = text.replace(/\s+/g, '-').toLowerCase();
	// 	    }
	// 		$(this).wrap('<a href="#'+text+'" data-ref="'+text+'"></a>');
	// 	});
	// 	$(this).find('.content').attr('id',text);
	// });
	
	// $('body.cms-customer-service .col-main .std li').each(function() {
	// 	$(this).addClass('expandable');
	// 	var text;
	// 	$(this).find('h2').each(function() {
	// 		var replace = new Array(" ", "[\?]", "\!", "\%", "\&");
	//     	var by = new Array("-", "", "", "", "and");
	// 		text = $(this).text();
	// 		for (var i=0; i<replace.length; i++) {
	// 	        text = text.replace(new RegExp(replace[i], "g"), by[i]);
	// 	        text = text.replace(/\s+/g, '-').toLowerCase();
	// 	    }
	// 		$(this).wrap('<a href="#'+text+'" data-ref="'+text+'"></a>');
	// 	});
	// 	$(this).find('.content').attr('id',text);
	// });
	$('body.cms-customer-service .col-main').animate({
		opacity: 1
	}, 100);

	$('.accordion-navigation > h2').click(function() {
    	var loc = $(this);
    	if ($(this).hasClass('active')) {
    		$(this).removeClass('active');
			$(this).next('.content').removeClass('active');
    	} else {
    		$('.accordion-navigation > h2, .accordion-navigation .content').removeClass('active');
			$(this).addClass('active');
			$(this).next('.content').addClass('active');
			setTimeout(function(){
		    	$('html,body').animate({
					scrollTop: loc.offset().top
				}, 1000);
			}, 1000);
    	}
    });
    //$('body.checkout-onepage-index .buttons-set').appendTo('.col-right');

    $('.pksr-toolbar .filter li a').click(function() {
		$('.pksr-toolbar .filter li a').removeClass('selected');
		if ($(this).hasClass('link-all')) $(this).addClass('selected');
	});
	$('.pksr-toolbar .filter li a[rel=instagram]').click();

	$('.block-subscribe .button.expand').click(function() {
		$('#adwords').css('display','block');
	});

	function openSection(hash) {
		if (!hash) {
			//hash = hash.substring(0, hash.indexOf('?'));
		}
    	var $target = $('.accordion-navigation > ' + hash);
     	if (hash == '#contactus') {
        	$('#contactus .button.open').hide();
			$('#sendMessage').slideDown();
			setTimeout(function(){
				$('.left-off-canvas-toggle').attr('aria-expanded', 'false');
    			$('body').removeClass('fixed');
	        	$('html,body').animate({
					scrollTop: $('#contactus').offset().top
				}, 500);
			}, 1000);
			history.pushState("", document.title, window.location.href.replace(/\#(.+)/, '').replace(/http(s?)\:\/\/([^\/]+)/, '') );
		} else if (!$target.hasClass('active') && hash != '#feed') {
        	$target.prev('h2').addClass('active');
            $target.addClass('active');
            setTimeout(function(){
            	$('.left-off-canvas-toggle').attr('aria-expanded', 'false');
    			$('body').removeClass('fixed');
		    	$('html,body').animate({
					scrollTop: $(hash).offset().top - 80
				}, 1000);
			}, 1000);
			history.pushState("", document.title, window.location.href.replace(/\#(.+)/, '').replace(/http(s?)\:\/\/([^\/]+)/, '') );
        } 
	}
	var hash = window.location.hash;
	if (hash) {
		openSection(hash);
	}

    $('body.cms-customer-service .footer-container .links a, body.cms-customer-service .off-canvas-menu-wrapper a').click(function(e) {
    	$('.exit-off-canvas').click();
    	var link = $(this).attr('href');
    	if (link.indexOf('#') != -1) e.preventDefault();
    	var hash = link.split('#')[1];
    	$('.accordion-navigation > h2, .accordion-navigation .content').removeClass('active');
    	history.pushState({}, '', link);
    	openSection('#'+hash);
    });

	$('.btn-quick').click(function() {
		if ($('#closeBtn').length) $('#closeBtn').click();
		var group = $(this).parents('.row').data('group');
//console.log(group);
		/*if ($('#quickView').length) {
			if (wW > 640) { 
				$('#quickView').insertAfter('#group'+group);
			} else {
				$('#quickView').insertAfter($(this).parents('li'));
			}		
		} else { */
			if (wW > 640) { 
				$('<div id="quickView"><div class="loader"></div></div>').insertAfter('#group'+group);
			} else {
				$('<li id="quickView"><div class="loader"></div></li>').insertAfter($(this).parent().parent().parent());
			}		
		//}
//console.log($('#group'+group));
//console.log($('#quickView'));
		$('html,body').animate({
			scrollTop: $('#quickView').offset().top - 40
		}, 500);
		$('#quickView').addClass('opened');
		//console.log('opened');
		$.get( $(this).data('url'), function(data) {
			$('#quickView').html(data);
			$('#quickView').prepend('<div id="closeBtn"><span class="icon-delete"></span></div>');
			//console.log('get');
			$('#closeBtn').click(function() {
				//console.log('clicked');
				$('#quickView').removeClass('opened');
				if (group == 1) {
					$('html,body').animate({
						scrollTop: $('#group1').offset().top - 100
					}, 1000);
				} else {
					$('html,body').animate({
						scrollTop: $('#group'+group).offset().top - 80
					}, 1000);
				}
				//setTimeout(function(){ $('#quickView').empty(); }, 1000);
				$('#quickView').remove();
			});
			cartActions();
		});
	});
	window.getLoginForm = function() {
		$('<div id="loginForm"></div>').insertAfter('.main-container');
		$('html,body').animate({
			scrollTop: $('#loginForm').offset().top - 40
		}, 500);
		$('#loginForm').addClass('opened').load( '/checkout/onepage/ .account-login', function() {
			$('.main-container').addClass('disabled');
			$(this).prepend('<div id="closeBtn"><span class="icon-delete"></span></div>');
			if (wW > 640) { 
				$('.form-list .control').each(function() {
					$(this).find('label').prepend('<span class="custom"></span>');
				});
			}
			$('#closeBtn').click(function() {
				$('#loginForm').removeClass('opened');
				$('.main-container').removeClass('disabled');
				$('html,body').animate({
					scrollTop: $('body').offset().top
				}, 500);
			});
		});
		// $('html,body').animate({
		// 	scrollTop: $('body').offset().top
		// }, 500);
		// $('#loginBtn').click();
	}
	window.getLoginFormFromSidebar = function() {
		$('<div id="loginForm"></div>').insertAfter('.main-container');
		$('.exit-off-canvas').click();
		$('html,body').animate({
			scrollTop: $('body').offset().top
		}, 500);
		$('#loginBtn').click();
	}
	$('#loginBtn').click(function() {
		$('<div id="loginForm"></div>').insertBefore('.main-container');
		$('#loginForm').load( '/customer/account/login/ .account-login', function() {
			$('.main-container').addClass('disabled');
			$(this).prepend('<div id="closeBtn"><span class="icon-delete"></span></div>');
			$(this).addClass('overlay opened');
			$('#closeBtn').click(function() {
				$('#loginForm').removeClass('opened');
				$('.main-container').removeClass('disabled');
			});
		});
	});
	$('#accountBtn').click(function() {
		$(this).toggleClass('opened');
		//$(this).find('ul').slideDown();
	});
	$('#accountBtn').mouseleave(function() {
		$(this).removeClass('opened');
	});
	$('input[type=radio],input[type=checkbox]').next('label').prepend('<span class="custom"></span>');

	$('<div id="morePlus"><span class="icon icon-plus"></span></div>').insertAfter('.cms-home #group0.multi, .block-related #group0');
	$('#morePlus .icon').click(function() {

		if ($('#quickView').hasClass('opened')) {
			//$('#closeBtn').click();
			var afterGroup = $('#quickView').prev().data('group');
		} else {
			var afterGroup = $(this).parent('#morePlus').prev().data('group');
			
		}
		var newGroup = parseInt(afterGroup) + 1;
		$('html,body').animate({
 			scrollTop: $('#group'+newGroup).offset().top - 50
 		}, 1000, 'linear');
 		$('#group'+newGroup).delay(5000).addClass('opened');
 		$('#morePlus').insertAfter($('#group'+newGroup));
 		if (!$('#morePlus').next().length) $('#morePlus').hide();
	});

	$('body').append('<div id="overlay"></div>');

	$('.product-essential .icon-arrow_right').click();
	$('.product-essential').animate({
		opacity: 1
	}, 500);
	
	// $('.pksr-instagram .inner a, .pksr-facebook .inner a, .pksr-twitter .section-text a').click(function(e) {
	// 	e.preventDefault();
	// 	var url = $(this).attr('href');
	// 	var id = $(this).parents('li').attr('rel');
	// 	getNextSocial(id,url);
	// });
	// function getNextSocial(id,url) {
	// 	var thisPos = $('.pksr-li[rel='+id+']').offset().top;
	// 	console.log(id);
	// 	if (url.indexOf('facebook') != -1) {
	// 		console.log('facebook');
	// 		$('body').addClass('overlay');
	// 		$('#overlay:hidden').show().animate({
	// 			opacity: 1
	// 		}, 1000).prepend('<div id="closeBtn"><span class="icon-delete"></span></div><div class="arrow icon-arrow_left"></div><div class="arrow icon-arrow_right"></div><div class="container"></div>');
	// 		$('#overlay .container').load( url +' #imagestage', function() {
	// 			overlayNav(id);
	// 		});
	// 	} else {
	// 		console.log('not facebook');
	// 		var img = $("<img />").attr('src',url).load( url, function(data) {
	// 			if (this.complete || typeof this.naturalWidth != "undefined" || this.naturalWidth != 0) {
	// 	        	$('body').addClass('overlay');
	// 	        	$('#overlay:hidden').show().animate({
	// 					opacity: 1
	// 				}, 1000).prepend('<div id="closeBtn"><span class="icon-delete"></span></div><div class="arrow icon-arrow_left"></div><div class="arrow icon-arrow_right"></div><div class="container"></div>');
	// 	            $('#overlay .container').html(img);
	// 	            overlayNav(id);
	// 	        }
	// 	    });
	// 	}
	// }
	// function overlayNav(id) {
	// 	$('#overlay #closeBtn').click(function() {
	// 		$('body').removeClass('overlay');
	// 		$('#overlay').animate({
	// 			opacity: 0
	// 		}, 1000).empty().hide();
	// 	});
	// 	$('#overlay .icon-arrow_right').click(function() {
	// 		var nextID = $('.pksr-li[rel='+id+']').next().attr('rel');
	// 		var nextUrl = $('.pksr-li[rel='+nextID+']').find('.inner a, .image a').attr('href');
	// 		getNextSocial(nextID,nextUrl);
	// 	});
	// }

	$('.block-subscribe').each(function() {
		$(this).find('.expand').click(function(e) {
			$(this).parent().parent().find('.more-fields').slideDown();
			$(this).parents('.block-subscribe').addClass('opened');
		});
		$(this).find('.email').keyup(function(event){
		    if(event.keyCode == 13){
		    	return false;
		    }
		    $(this).prev('.expand').click();
		});
		$(this).find('div.cs-select').insertAfter($(this).find('input.name'));
	});

	$('.mobile-searchBtn').click(function(e) {
		e.preventDefault();
		if ($('.top-line').hasClass('search')) {
			$('.top-line').removeClass('search');
		} else {
			$('#search_mini_form_mobile input[type="text"]').focus();
			$('.top-line').addClass('search');
		}
	});

	$('#open-shop').click(function() {
    	if ($(this).hasClass('open')) {
    		//console.log('close 1');
    		$('#shopMenu').removeClass('opened');
	    	$(this).removeClass('open');
	    } else {
	    	//console.log('open');
	    	$('#shopMenu').addClass('opened');
	    	$(this).addClass('open');
	    }
	    if (wW > 640) { 
	    	$('.main-container').mouseover(function() {
	    		//console.log('close 2');
		    	$('#shopMenu').removeClass('opened');
		    	$('#open-shop').removeClass('open');
		    });
		}
    });

    $('.dob-month input').attr('placeholder','MM');
    $('.dob-day input').attr('placeholder','DD');
    $('.dob-year input').attr('placeholder','YYYY');

	$('#group0.kurrent .product1').appendTo( $('.bio') );
	$('#group0.kurrent .product2').insertBefore('.bottom_image');
	$('#group0.kurrent .product3').insertAfter('.bottom_image');
	$('.cms-the-kurrent-kourtney .main-container, .cms-the-kurrent-kim .main-container, .cms-the-kurrent-khloe .main-container').append('<div class="bottom_bg"></div>');

	//$('.bio h2.faves').prependTo( $('.kurrentProduct.product1') );

	$('.wishlist-list li').each(function() {
		$this = $(this).find('.product-info');
		$(this).find('.item-options').appendTo($this);
	});

	$('#contactus .button.open').click(function() {
		$(this).hide();
		$('#sendMessage').slideDown();
	});
    $('select#country option[value=""]').text('COUNTRY*');

    $('body.checkout-cart-index .shipping').insertAfter('#shopping-cart-totals-table');

	$('#wishlist-view-form .btn-share').click(function() {
		var url = $(this).parents('form').attr('action');
		url = url.replace("update","share");
		//window.open(url, 'share', 'width=800,height=600,status=yes,resizable=yes,scrollbars=yes');
		$('body').addClass('overlay');
		$('#overlay').show().animate({
			opacity: 1
		}, 1000).prepend('<div class="window"><div id="closeBtn"><span class="icon-delete"></span></div><div class="container"></div></div>');
            
		$('#overlay .container').load( url + ' .my-account', function(data) {

			function _prepareItemsList() {
				var _description = "";

				function __prepareProductName(_input) {
					/*
					 var _limit = 79;
					 //var _nbsp = String.fromCharCode(160);
					 var _nbsp = "\xA0&160;";
					 _input = _input.replace(" ", _nbsp);
					 var _diff = _limit - _input.length;
					 console.log(_diff);
					 for(var i=0; i< _diff; i++ ) {
					 _input += _nbsp;
					 }

					 _input += " ";
					 console.log(_input);
					 */
					return _input;
				}


				$("#wishlist-list .product-name a").each(function(idx) {


					//&#10;
					//HTML Hexadecimal: &#x0A;
					//Java Hexadecimal: \u000A (Common To Javascript)
					//URL Hexadecimal: %0A
					_description += ((idx > 0 ? ", " : "")+__prepareProductName(this.innerHTML));
				});

				//_description += "Hi!!!";


				return _description;
			}


			$("#share-wishlist-share-twitter").attr("href", "https://twitter.com/intent/tweet?text=I%27ve+got+a+wishlist%21+:+"+encodeURIComponent(_prepareItemsList()))+"&url="+encodeURIComponent("https://kbeauty.com/");

			//"https://twitter.com/intent/tweet?text=I%27ve+got+a+wishlist%21&url=https%3A%2F%2Fkbeauty.com%2F";
			$("#share-wishlist-share-email").click(function(e) {
				e.preventDefault();

				$('#share-wishlist-select-type').hide();
				$('#share-wishlist-email').fadeIn('fast');
			});

			$(".share-email-back-button a").click(function(e) {
				e.preventDefault();

				$('#share-wishlist-email').hide();
				$('#share-wishlist-select-type').fadeIn('fast');
			});

/*
			twttr.widgets.createShareButton(
				"https:\/\/dev.twitter.com\/web\/tweet-button",
				document.getElementById("share-wishlist-share-twitter"),
				{
					//size: "large",
					via: "twitterdev",
					related: "twitterapi,twitter",
					text: "custom share text",
					hashtags: "example,demo"
				}
			);
*/
/*
 <a href="https://twitter.com/share" class="twitter-share-button"{count} data-via="kbeautyofficial">Tweet</a>
 <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
 */


			$("#share-wishlist-share-facebook").click(function(e) {
				e.preventDefault();


				var _description = _prepareItemsList();

				var requestObj = {
					method: 'feed',
					//app_id: 235468389807927,
					app_id: 770199153099219,
					//display: 'dialog',
					link: 'http://kardashianbeauty.hellojrdev.com/',
            // picture: 'http://kardashianbeauty.hellojrdev.com/media/wysiwyg/images/khloe2.jpg',
            		picture: 'http://kbeauty.com/media/KBeauty_FBShare.jpg',
					source: '',
					name: "YEAH!!!",
					caption: "Take A Look",
					description: _description,
//            redirect_uri: '<?php echo $canvasUrl; ?>Spanish/',
					ref: "Public"
				};

// console.log(requestObj);

				function _fbShare(error, response) {

					if(error) {
						console.log("FB Error");
						console.log(error);
						return;
					}

					console.log(requestObj);
					console.log(response);

					FB.ui(requestObj, function(response){
						$(".fb_dialog").css("z-index", "10000000!important");
						console.log(response);
					});

				}

				FB.getLoginStatus(function(response) {
					if (response.status == 'connected') {
console.log(response);
						FB.api('/me', function(response) {
							_fbShare(null, response);
						});
					} else if (response.status == 'not_authorized') {
						FB.login(function(response) {
							if (response.authResponse) {
								FB.api('/me', function(response) {
									_fbShare(null, response);
								});
							} else {
								_fbShare(response.error, null);
							}
						});
					}
				});

			});

            $('#overlay #closeBtn').click(function() {
				$('body').removeClass('overlay');
				$('#overlay').animate({
					opacity: 0
				}, 1000).empty().hide();
			});
			$(document).mouseup(function (e){
			    var container = $('#overlay .window');
			    if (!container.is(e.target) && container.has(e.target).length === 0) {
			        $('#overlay #closeBtn').click();
			    }
			});
		});
	});
	//if (!$.session.get('promo15')) {
	var name = "promo15=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) var cv = c.substring(name.length,c.length);
    }
    //console.log(cv);
    if (!cv) {
	    var _revealPromoTimer = setTimeout(function() {
	        $('#subpop').foundation('reveal', 'open');
	        document.cookie="promo15=on; expires=20*365";
	        //$('body').css('overflow','hidden');
	        //$.session.set('promo15', 'on');
	    }, 6000);

	    // $('#subpop').click(function() {
	    //     $('body').css('overflow','auto');
	    // });
	}

	$('.share .email').click(function(e) {
		e.preventDefault();
		var url = $(this).attr('href');
		//window.open(url, 'share', 'width=800,height=600,status=yes,resizable=yes,scrollbars=yes');
		$('body').addClass('overlay');
		$('#overlay').show().animate({
			opacity: 1
		}, 1000).prepend('<div class="window"><div id="closeBtn"><span class="icon-delete"></span></div><div class="container"></div></div>');
            
		$('#overlay .container').load( url + ' .col-main', function(data) {

            $('#overlay #closeBtn').click(function() {
				$('body').removeClass('overlay');
				$('#overlay').animate({
					opacity: 0
				}, 1000).empty().hide();
			});
			$(document).mouseup(function (e){
			    var container = $('#overlay .window');
			    if (!container.is(e.target) && container.has(e.target).length === 0) {
			        $('#overlay #closeBtn').click();
			    }
			});
		});
	});

	$('#hero .bg').addClass('anim');
	$(window).scroll(function (event) {
	    var topWindow = $(window).scrollTop();
	    var heroTop = $('#hero').height();
	    var topLine = $('.top-line').height();
	    if (topWindow > heroTop) {
	    	$('#hero .bg').removeClass('anim');
	    } 
	    if (topWindow < topLine) {
	    	$('#hero .bg').addClass('anim');
	    }
	});

	function responsiveStuff() {
		if(device.desktop()) wW = window.outerWidth;
		else wW = $(window).width();
		if (is_windows && is_chrome) wW = parseInt(wW) - 16;
		var tallestImage;
		//console.log(wW);

		$('#slider li, #hero').width(wW);
		$('#slider .slides').width($('#slider li').length * wW);

	    if (wW > 641) {
	    	//console.log(wW);
	    	$('.cms-the-kurrent-kourtney .bio, .cms-the-kurrent-kim .bio, .cms-the-kurrent-khloe .bio').insertBefore('.top_image');
	    	$('#cartLogin').appendTo($('.top-line .rs'));
			$('.cms-home #slider .container').each(function() {
				$(this).find('.text').prependTo( $(this) );
			});
			var imageHeights = [], imageHeight, tallestImage;
			$('.cms-home #slider li, #hero').each(function() {
				var textHeight = $(this).find('.text').outerHeight();
//console.log(textHeight);
				$(this).find('.text').height(textHeight).css('position','absolute');

				$(this).find('.big-image.desktop img').css('height','auto');
				imageHeight = $(this).find('.big-image.desktop img, .subtitle img').height();
				$(this).find('.image').css('position','absolute');
				imageHeights.push(imageHeight);
				tallestImage = Math.max.apply( null, imageHeights );
			});

//console.log(tallestImage);

			if (!tallestImage) {
				// var bg = $('.catalog-category-view #hero .text').css('background-image');
				// bg = bg.replace('url(','').replace(')','');
				// var tmpImg = new Image();
				// tmpImg.src=bg;
				// tallestImage = tmpImg.height;
				tallestImage = wW * 0.39;
        	}
   //      	$('.catalog-category-view #slider li').each(function() {
			// 	$(this).css('background-image','url('+$(this).attr('data-desktop')+')').css('height',tallestImage);
			// });
			//if (wW <= 1284) {
				$('.cms-home #slider li .container, #slider li .image #slider li .image img').css('height',tallestImage);
				$('#slider li .big-image img').css('height',tallestImage);
				$('.cms-home #slider ul, #slider li').css('height',tallestImage+0);
                $('#slider li.nextSlide').css('margin-top','-'+(tallestImage+0)+'px');
			//} else {
				//$('.cms-home #slider li .container, #slider li .image, #slider li .image img').css('height','500px');
				//$('.cms-home #slider ul, #slider li, #hero').css('height','540px');
				//$('#slider li.nextSlide').css('margin-top','-540px');
			//}
			$('#slider, #hero').animate({
				opacity: 1
			}, 1000);

			var categoryTextHeight = $('#hero .text').outerHeight();
			// var bg_url = $('#hero').css('background-image');
			// bg_url = /^url\((['"]?)(.*)\1\)$/.exec(bg_url);
   //  		bg_url = bg_url ? bg_url[2] : "";
   //  		var tmpImg = new Image();
			// tmpImg.src = bg_url;
			
			// $(tmpImg).on('load',function(){
			//   var heroWidth = tmpImg.width;
			//   var heroHeight = tmpImg.height;
			//   imgRatio = heroHeight / heroWidth;
			// });
			//heroHeight = Math.max(heroHeight,categoryTextHeight);
			
        	if ($('#hero img').length) {
        		$('#hero').height(wW * 0.384);
        		var textH = $('#hero').height() * 0.52;
        		$('#hero .text').height( textH );
        		$('#hero, body.category-the-fierce-collection .category-products').animate({
					opacity: 1
				}, 500);
			} else {
				$('#hero .text').height(categoryTextHeight).css('position','absolute');
				$('#hero').css('height',tallestImage+0);
				$('#hero').animate({
					opacity: 1
				}, 1000);
			}
		} else {

			$('.cms-the-kurrent-kourtney .bio, .cms-the-kurrent-kim .bio, .cms-the-kurrent-khloe .bio').insertAfter('.top_image');
			$('#cartLogin').appendTo($('.top-line section.logo.hide-for-medium-up'));

            var imageHeights = [], imageHeight, tallestImage;

			$('.cms-home #slider li').each(function() {
				$(this).find('.container').each(function() {
					$(this).find('.image').prependTo( $(this) );
				});
				$(this).find('.text').css('height','auto').css('position','relative');
				$(this).find('.container, .image, .image img').css('position','relative');

                $(this).parent().css('height','auto');

                $(this).find('.big-image.mobile img').css('height','auto');
                imageHeight = $(this).find('.big-image.mobile img').height();
                $(this).find('.image').css('position','absolute');
                imageHeights.push(imageHeight);
                tallestImage = Math.max.apply( null, imageHeights );

//console.log(tallestImage);
				//$("#slider li").css('height',tallestImage+'px');
				$("#slider li").height(tallestImage+'px');

//console.log(wW);
//console.log($(this).innerHeight());
//console.log(tallestImage);

                if($(this).hasClass('nextSlide')) {
                    $(this).css('margin-top','-'+tallestImage+'px');
                } else {
                    $(this).css('margin-top',0);
                }

			});
			
			$('#hero').each(function() {
				// if (wW > 641) {
				// 	tallestImage = wW;
				// 	$(this).css('height',tallestImage);
				// }

                if($(this).hasClass('nextSlide')) {
                    $(this).css('margin-top','-'+(tallestImage)+'px');
                } else {
                    $(this).css('margin-top',0);
                }

				//$(this).css('background-image','url('+$(this).attr('data-mobile')+')').css('height',tallestImage);
			});
			
			if ($('body').hasClass('category-the-fierce-collection') && $('#hero img').length) {
				$('#hero').height(wW);
        		var textH = $('#hero').height() * 0.52;
        		var textMargin = ($('#hero').height() - textH) * 0.5;
        		$('#hero .text').height( textH ).css('top',textMargin+'px');
        		$('#hero, .category-products').animate({
					opacity: 1
				}, 500);
			} else {
				$('#hero .text').css('height','auto').css('position','relative');
			}

			$('#slider').animate({
				opacity: 1
			}, 1000);
		}
	}
	responsiveStuff();

	// document.getElementsByClassName("customer-dob")[0].onkeyup = function(e) {
	// 	//console.log('keyup');
	//     var target = e.srcElement;
	//     var maxLength = parseInt(target.attributes["maxlength"].value, 10);
	//     var myLength = target.value.length;
	//     if (myLength >= maxLength) {
	//         var next = target;
	//         while (next = next.nextElementSibling) {
	//             if (next == null)
	//                 break;
	//             if (next.tagName.toLowerCase() == "input") {
	//                 next.focus();
	//                 break;
	//             }
	//         }
	//     }
	// }

	function lazyload(){
   		var sliderHeight = $('#slider ul').height();
   		var trigger = $(window).height() * 0.98;
	   	var wt = $(window).scrollTop();    //* top of the window
	   	var wb = wt + trigger;  //* bottom of the window
	   	$('.lazy, #bestsellers .item, #group0').each(function(){
	   	  if (sliderHeight) var ot = $(this).offset().top + sliderHeight;  //* top of object
	      else var ot = $(this).offset().top;  //* top of object
	      ot = $(this).offset().top;
	      var ob = ot + $(this).height(); //* bottom of object
	      if(wb >= ot){
	         $(this).addClass('loaded');
	      }
	   	});
	}
	lazyload();
	$(window).scroll(lazyload);

	$(window).resize(function() {
		catWidth = $('#nav li:first-child').width();
		$('#nav li').each(function() {
			$(this).height(catWidth);
		});
		$(document).foundation('equalizer','reflow');

		responsiveStuff();
		lazyload();
	});

  
	$(document).foundation({
	  equalizer : {
	    // Specify if Equalizer should make elements equal height once they become stacked.
	    equalize_on_stack: false
	  }
	});

  });
})(jQuery);
