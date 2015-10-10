try{Typekit.load();}catch(e){} //Typekit font requirement, do not remove
(function( $ ) {
  $(window).load(function() {

  	if(device.desktop()) var wW = window.outerWidth;
	else var wW = $(window).width();

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

	$('#onepage-guest-register-button').click(function() {
		$('body').removeClass('login');
	});

	var shipping, newShipping;
	if ($('body.checkout-onepage-index #checkout-review-table tr').length == 4) {
		shipping = $('body.checkout-onepage-index #checkout-review-table tr:nth-child(2) .price').text();
		//shipping = shipping.substr(1);
	} 
	//console.log(shipping);
	$('body.checkout-onepage-index #co-shipping-method-form .button').click(function() {
		$.ajax({
            type: "POST",
            dataType: "HTML",
            //data: { productId : productId, qty : qty },
            url: "/scripts/ajax.php",
            success: function (data) {
                //console.log(data);
                finishAjax('quantityJS', escape(data));
            },
            error: function(data){
            }
        });
        lookupShipping();
	});
	function lookupShipping() {
		var checkShipping = setInterval(function(){
			newShipping = $('body.checkout-onepage-index #shipping_method-progress-opcheckout .price').text();
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
	function setShipping(shipping) {
		if ($('#checkout-review-table tr').length == 4) {
			$('#checkout-review-table tr:nth-child(2) .price').text(shipping);
		} else if ($('body.checkout-onepage-index #checkout-review-table tr').length == 2) {
			$('<tr><td class="a-right">Shipping</td><td class="a-right"><span class="price">'+shipping+'</span></td></tr>').insertAfter('body.checkout-onepage-index #checkout-review-table tr:nth-child(1)');
		}
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
 //            	finishAjax('quantityJS', escape(data));
 //            },
 //            error: function(data){
 //            }
 //        });
 //    }
 //    function finishAjax(id, data){
 // 	 	jQuery('#'+id).html(unescape(data));
	// 	updateTotals();
	// }
	[].slice.call( document.querySelectorAll( 'select.cs-select' ) ).forEach( function(el) {	
			new SelectFx(el);
		} );
	var cartActions = function() {
		//console.log('cartActions');

		$('.product-view .quantity .icon-minus, .cart-list .quantity .icon-minus').click(function() {
			var curValue = parseInt($(this).next('input.qty').val());
			if (curValue != 1) var newValue = curValue-1;
			else newValue = 1;
			$(this).next('input.qty').val(newValue);
		});
		$('.product-view .quantity .icon-plus, .cart-list .quantity .icon-plus').click(function() {
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
			$(this).find('.quantity').appendTo($(this).find('.product-options dd.last'));
		}); 
		
		$('.color-swatch-wrapper li img').wrap('<span></span>');

		$('div.super-attribute-select .cs-options li[data-value=""]').remove();

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
	        		$('.product-view div.super-attribute-select .cs-options li:nth-child(1)').addClass('cs-selected');
	        		$('.product-view div.super-attribute-select .cs-placeholder').html($('div.super-attribute-select .cs-options li:nth-child(1) span').text());
	        	}
	  			$('.product-view .product-options .input-box select option:nth-child(2)').prop('selected', true);
	        }
	    }
	    if ($('#quickView select.super-attribute-select option:first-child').val() == '') {
        	$('#quickView .color-swatch-wrapper input[type=hidden]').val($('.product-options .input-box select option:nth-child(2)').val() );
        	if ($('#quickView .color-swatch-wrapper li:first-child').hasClass('is-disabled-option')) {
        		$('#quickView .color-swatch-wrapper li:nth-child(2)').addClass('active');
        	} else {
        		$('#quickView .color-swatch-wrapper li:first-child').addClass('active');
        		$('#quickView div.super-attribute-select .cs-options li:nth-child(1)').addClass('cs-selected');
        		$('#quickView div.super-attribute-select .cs-placeholder').html($('div.super-attribute-select .cs-options li:nth-child(1) span').text());
        	}
  			$('#quickView .product-options .input-box select option:nth-child(2)').prop('selected', true);
        }
	    $('.cs-select').click(function() {
	    	if ($(this).hasClass('cs-active')) {
	    		$(this).find('.cs-placeholder').html('Select an option');
	    	} else {
	    		$(this).find('.cs-placeholder').html($('.cs-options .cs-selected span').text());
	    	}
	    	$('.cs-select').mouseleave(function() {
	    		$(this).removeClass('cs-active');
	    		$(this).find('.cs-placeholder').html($('.cs-options .cs-selected span').text());
	    	});
	    });
	    
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
		});

		var color, colorArray, groupArray, colorCount, groupCount, currentType, currentNumber, nextNumber, prevNumber;
		
		if (typeof group !== 'undefined') groupCount = group.length;
		//console.log('groupCount '+groupCount);
		$('.color-swatch-wrapper li').click(function() {
			var productID = $(this).attr('data-product');
			// var colorID = $('.product-'+productID+' #hidden-attribute-132').val();
			// var colorName = $('.product-'+productID+' .cs-options li[data-value='+colorID+'] span').text();
			// console.log(productID);
			// console.log(colorID);
			// console.log(colorName);
			//$('.product-'+productID+' .cs-options li').removeClass('cs-selected');
			//$('.product-'+productID+' .cs-options li[data-value='+colorID+']').addClass('cs-selected');
			//$('.product-'+productID+' .cs-placeholder').text(colorName);
			if ($(this).hasClass('is-disabled-option')) {
				$('.availability.out-of-stock').fadeIn();
				$('.color-swatch-wrapper li').removeClass('active');
				$(this).addClass('active');
			} else {
				$('.availability.out-of-stock').fadeOut();
			}
			var classes = $(this).attr('class');
			if (classes.indexOf('active')) classes = classes.replace(' active','');
			if (classes.indexOf('is-disabled-option')) classes = classes.replace(' is-disabled-option','');
			color = classes.replace(/-/g,"_");
			colorCount = eval(color).length;
			color = eval(color + '[0].src');
			console.log(color);
            $('.product-'+productID+' .main-image').attr('src',color);
            $('.product-'+productID+' .product-image').data('type','color');
            $('.product-'+productID+' .product-image').data('number',0);
            getColor(productID);
        });
        $('.product-essential .product-image .icon-arrow_left').click(function() {
        	var productID = $(this).attr('data-product');
        	var classes = $('.product-'+productID+' .color-swatch-wrapper li.active').attr('class');
        	classes = classes.replace(' active','');
        	if (classes.indexOf('is-disabled-option')) classes = classes.replace(' is-disabled-option','');
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
        $('.product-essential .product-image .icon-arrow_right').click(function() {
        	var productID = $(this).attr('data-product');
        	var classes = $('.product-'+productID+' .color-swatch-wrapper li.active').attr('class');
        	classes = classes.replace(' active','');
        	if (classes.indexOf('is-disabled-option')) classes = classes.replace(' is-disabled-option','');
        	color = classes.replace(/-/g,"_");
        	currentType = $('.product-'+productID+' .product-image').attr('data-type');
        	currentNumber = $('.product-'+productID+' .product-image').attr('data-number');
        	currentNumber2 = parseInt(currentNumber) + 1;
        	colorCount = eval(color).length;
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
        });
		
	  	/*$('.super-attribute-select').addClass('cs-skin-slide').addClass('cs-select');
	  	var attributeCount = $('.super-attribute-select li').length;
	  	$('.cs-skin-slide .cs-options').css('height',attributeCount+'00%');
	  	$('.cs-skin-slide.cs-active::before').css('transform','scale3d(1.1,'+attributeCount+',1)').css('-webkit-transform','scale3d(1.1,'+attributeCount+',1)');*/
	}
	cartActions();

	$('#faqs a[href=#shipping]').click(function(e) {
		console.log('clicked');
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
	function openSection(hash) {
		if (!hash) {
			var hash = window.location.hash;
		}
	    if (hash != '') {
	    	var $target = $('.accordion-navigation > ' + hash);
	     	if (hash == '#contactus') {
	        	$('#contactus .button').hide();
				$('#sendMessage').slideDown();
				setTimeout(function(){
					$('.left-off-canvas-toggle').attr('aria-expanded', 'false');
        	$('body').removeClass('fixed');
		        	$('html,body').animate({
						scrollTop: $('#contactus').offset().top
					}, 500);
				}, 1000);
	        } else if (!$target.hasClass('active')) {
	        	$target.prev('h2').addClass('active');
	            $target.addClass('active');
	            setTimeout(function(){
	            	$('.left-off-canvas-toggle').attr('aria-expanded', 'false');
        	$('body').removeClass('fixed');
			    	$('html,body').animate({
						scrollTop: $(hash).offset().top - 80
					}, 1000);
				}, 1000);
	        }
	    }
	}
	openSection();
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
		
		/*if ($('#quickView').length) {
			if (wW > 640) { 
				$('#quickView').insertAfter('#group'+group);
			} else {
				$('#quickView').insertAfter($(this).parents('li'));
			}		
		} else { */
			if (wW > 640) { 
				$('<div id="quickView"><div class="loader"><img src="/skin/frontend/polar/default/images/opc-ajax-loader.gif"></div></div>').insertAfter('#group'+group);
			} else {
				$('<li id="quickView"><div class="loader"><img src="/skin/frontend/polar/default/images/opc-ajax-loader.gif"></div></li>').insertAfter($(this).parent().parent().parent());
			}		
		//}

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

	$('<div id="morePlus"><span class="icon icon-plus"></span></div>').insertAfter('.cms-home #group0, .block-related #group0');
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

	$('.pksr-toolbar .filter li a').click(function() {
		$('.pksr-toolbar .filter li a').removeClass('selected');
		if ($(this).hasClass('link-all')) $(this).addClass('selected');
	});
	$('.pksr-toolbar .filter li a[rel=instagram]').click();

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

	$('#group0.kurrent .product1').insertAfter('.top_image');
	$('#group0.kurrent .product2').insertBefore('.bottom_image');
	$('.cms-the-kurrent-kourtney .main-container, .cms-the-kurrent-kim .main-container, .cms-the-kurrent-khloe .main-container').append('<div class="bottom_bg"></div>');

	$('.wishlist-list li').each(function() {
		$this = $(this).find('.product-info');
		$(this).find('.item-options').appendTo($this);
	});

	$('#contactus .button').click(function() {
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

	function responsiveStuff() {
		if(device.desktop()) wW = window.outerWidth;
		else wW = $(window).width();
		var tallestImage;
		//console.log(wW);

		$('#slider li').width(wW);
		$('#slider .slides').width($('#slider li').length * wW);

	    if (wW > 640) {
	    	//console.log(wW);
	    	$('.cms-the-kurrent-kourtney .bio, .cms-the-kurrent-kim .bio, .cms-the-kurrent-khloe .bio').insertBefore('.top_image');
	    	$('#cartLogin').appendTo($('.top-line .rs'));
			$('.cms-home #slider .container').each(function() {
				$(this).find('.text').prependTo( $(this) );
			});
			var imageHeights = [], imageHeight, tallestImage;
			$('.cms-home #slider li').each(function() {
				var textHeight = $(this).find('.text').outerHeight();

				$(this).find('.text').height(textHeight).css('position','absolute');

				$(this).find('.image img').css('height','auto');
				imageHeight = $(this).find('.image img').height();
				$(this).find('.image').css('position','absolute');
				imageHeights.push(imageHeight);
				tallestImage = Math.max.apply( null, imageHeights );
			});
			if (!tallestImage) {
				/*var bg = $('.catalog-category-view #slider li:first-child').css('background-image');
				bg = bg.replace('url(','').replace(')','');
				var tmpImg = new Image();
				tmpImg.src=bg;
				tallestImage = tmpImg.height;*/
				tallestImage = wW * 0.39;
        	}
   //      	$('.catalog-category-view #slider li').each(function() {
			// 	$(this).css('background-image','url('+$(this).attr('data-desktop')+')').css('height',tallestImage);
			// });
			if (wW <= 1284) {
				$('.cms-home #slider li .container, #slider li .image, #slider li .image img').css('height',tallestImage);
				$('.cms-home #slider ul, #slider li').css('height',tallestImage+40);
                $('#slider li.nextSlide').css('margin-top','-'+(tallestImage+40)+'px');
			} else {
				$('.cms-home #slider li .container, #slider li .image, #slider li .image img').css('height','500px');
				$('.cms-home #slider ul, #slider li').css('height','540px');
				$('#slider li.nextSlide').css('margin-top','-540px');
			}
			$('#slider').animate({
				opacity: 1
			}, 1000);

			var categoryTextHeight = $('.catalog-category-view #slider .text').outerHeight();
			$('.catalog-category-view #slider .text').height(categoryTextHeight).css('position','absolute');

		} else {

			$('.cms-the-kurrent-kourtney .bio, .cms-the-kurrent-kim .bio, .cms-the-kurrent-khloe .bio').insertAfter('.top_image');
			$('#cartLogin').appendTo($('.top-line section.logo.hide-for-medium-up'));
			$('.cms-home #slider li').each(function() {
				$(this).find('.container').each(function() {
					$(this).find('.image').prependTo( $(this) );
				});
				$(this).find('.text').css('height','auto').css('position','relative');
				$(this).find('.container, .image, .image img').css('height','auto').css('position','relative');

                $(this).parent().css('height','auto');

				$("#slider li").css('height',($(this).innerHeight())+'px');

//console.log(wW);
//console.log($(this).innerHeight());

                if($(this).hasClass('nextSlide')) {
                    $(this).css('margin-top','-'+($(this).innerHeight())+'px');
                } else {
                    $(this).css('margin-top',0);
                }

			});
			$('.catalog-category-view #slider li').each(function() {
				tallestImage = wW;
				$(this).css('height',tallestImage);

                if($(this).hasClass('nextSlide')) {
                    $(this).css('margin-top','-'+(tallestImage)+'px');
                } else {
                    $(this).css('margin-top',0);
                }

				//$(this).css('background-image','url('+$(this).attr('data-mobile')+')').css('height',tallestImage);
			});
			
			$('.catalog-category-view #slider .text').css('height','auto').css('position','relative');
			$('#slider').animate({
				opacity: 1
			}, 1000);
		}
	}
	responsiveStuff();

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
