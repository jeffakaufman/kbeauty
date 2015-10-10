(function( $ ) {
	window.cartActions = function() {
		//console.log('cartActions');

		// [].slice.call( document.querySelectorAll( '#quickView select.cs-select' ) ).forEach( function(el) {	
		// 	new SelectFx(el);
		// } );
		//console.log('loaded');
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
		});

		var color, colorArray, colorCode, groupArray, colorCount, groupCount, currentType, currentNumber, nextNumber, prevNumber;
		var isQuickview = false;
		if (typeof group !== 'undefined') groupCount = group.length;
		//console.log('groupCount '+groupCount);
		$('.color-swatch-wrapper li').click(function() {
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
	    	if ($(this).hasClass('cs-active') && $(this).prev('.availability.out-of-stock').is(':hidden')) {
	    		$(this).find('.cs-placeholder').html('Select an option');
	    	} else {
	    		$(this).find('.cs-placeholder').html($('.cs-options .cs-selected span').text());
	    	}
	    	$(this).mouseleave(function() {
	    		$(this).removeClass('cs-active');
	    		$(this).find('.cs-placeholder').html($(this).find('.cs-options .cs-selected span').text());
	    	});
	    });
		$('.cs-options li').click(function() {
			var productID = $(this).parents('.input-box').attr('data-product');
			var colorID = $(this).data('value');
			colorCode = 'color_swatch_132_'+colorID;
			color = eval(colorCode + '[0].src');
            $('.product-'+productID+' .main-image').attr('src',color);
            $('.product-'+productID+' .product-image').data('type','color');
            $('.product-'+productID+' .product-image').data('number',0);
		});
		$('#quickView .super-attribute-select').change(function() {
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
        $('.product-essential .product-image .icon-arrow_left').click(function() {
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
        $('.product-essential .product-image .icon-arrow_right').click(function() {
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
})(jQuery);