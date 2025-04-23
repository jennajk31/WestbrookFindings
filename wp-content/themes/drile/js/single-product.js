jQuery(function($){
	"use strict";
	var on_touch = !$('body').hasClass('ts_desktop');
	
	/*** Set Cloud Zoom ***/
	ts_set_cloud_zoom();
	$(window).on('resize orientationchange', $.throttle(250, function(){
		ts_set_cloud_zoom();
	}));
	
	if( $('.cloud-zoom, .cloud-zoom-gallery').length > 0 ){
		$(document).on('found_variation reset_image', 'form.variations_form', function(){
			$('.cloud-zoom, .cloud-zoom-gallery').CloudZoom({});
		});
	}
	
	/*** Product Image Lightbox ***/
	if( typeof PhotoSwipe !== 'undefined' ){
		function ts_get_single_product_gallery_items(){
			var items = [];
			$('.images-thumbnails .woocommerce-product-gallery__image a').each(function(index, ele){
				if( $(ele).parents('.owl-item.cloned').length == 0 ){
					var img = $(ele).find('img');
					var large_image_src = img.attr( 'data-large_image' );
					var large_image_w   = img.attr( 'data-large_image_width' );
					var large_image_h   = img.attr( 'data-large_image_height' );
					var item            = {
						src: large_image_src,
						w:   large_image_w,
						h:   large_image_h,
						title: img.attr( 'title' )
					};
					items.push( item );
				}
			});
			
			if( $('.vertical-thumbnail').length > 0 && items.length > 1 ){
				var main_thumbnail = items.pop();
				items.unshift( main_thumbnail );
				
				$('.images-thumbnails > .thumbnails img').each(function(index, ele){
					$(ele).attr('data-index', index + 1);
				});
			}
			
			return items;
		}
		
		$('.images-thumbnails').on('click', '.woocommerce-product-gallery__image a', function( e ){
			e.preventDefault();
			var items = ts_get_single_product_gallery_items();
			var index = $(this).find('img').attr('data-index');
			var pswpElement = $( '.pswp' )[0];
			var options = typeof wc_single_product_params != 'undefined' && typeof wc_single_product_params.photoswipe_options != 'undefined'?wc_single_product_params.photoswipe_options:{};
			options['index'] = parseInt(index);
			
			var photoswipe = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options );
			photoswipe.init();
		});
		
		if( $('.product.thumbnail-slider').length && typeof wc_single_product_params.photoswipe_options != 'undefined' ){
			$('.woocommerce-product-gallery__image a img').on('mouseenter', function(){
				wc_single_product_params.photoswipe_options.index = parseInt($(this).attr('data-index'));
			});
		}
	}
	
	/*** Thumbnails Slider ***/
	/* Horizontal slider */
	var wrapper = $('.single-product .product:not(.vertical-thumbnail) .images-thumbnails .thumbnails-container.loading');
	var _slider_data = {
			loop: true
			,nav: true
			,navText: [,]
			,dots: false
			,navSpeed: 1000
			,rtl: $('body').hasClass('rtl')
			,margin: 0
			,navRewind: false
			,autoplay: true
			,autoplayHoverPause: true
			,autoplaySpeed: 1000
			,responsiveBaseElement: wrapper
			,responsiveRefreshRate: 1000
			,responsive:{0:{items:2},280:{items:3},400:{items:4},520:{items:5},800:{items:6}}
			,onInitialized: function(){
				wrapper.addClass('loaded').removeClass('loading');
			}
		};
	if( $('.product.thumbnail-slider').length ){
		_slider_data.responsive = {0:{items:1},500:{items:2}};
		_slider_data.margin = 0;
	}
	$(document).trigger('single_product_horizontal_thumbnail_slider_data', _slider_data);
	wrapper.find('.product-thumbnails').owlCarousel( _slider_data );
		
	/* Vertical slider */
	var wrapper = $('.single-product .product.vertical-thumbnail .images-thumbnails .thumbnails-container.loading');
	
	if( wrapper.length > 0 && typeof $.fn.carouFredSel == 'function' ){
		var has_360_gallery = $('.ts-product-360-button').length > 0;
		var items = $(window).width() < 500?(has_360_gallery?2:3):4;
		
		var _slider_data = {
				items: items
				,direction: 'up'
				,prev: wrapper.find('.owl-prev')
				,next: wrapper.find('.owl-next')
				,auto: {
					duration: 800
				}
				,scroll: {
					items: 1
				}
				,onCreate: function(){
					wrapper.addClass('loaded').removeClass('loading');
				}
			};
			
		wrapper.find('.product-thumbnails').carouFredSel(_slider_data);
		
		$(window).on('resize orientationchange', $.debounce( 250, function(){
			_slider_data.items = $(window).width() < 500?(has_360_gallery?2:3):4;
			wrapper.find('.product-thumbnails').trigger('configuration', _slider_data);
		} ));
	}
	
	/*** Product Video ***/
	$('a.ts-product-video-button').on('click', function(e){
		e.preventDefault();
		var product_id = $(this).data('product_id');
		var container = $('#ts-product-video-modal');
		if( container.find('.product-video-content').html() ){
			container.addClass('show');
		}
		else{
			container.addClass('loading');
			$.ajax({
				type : 'POST'
				,url : drile_params.ajax_url
				,data : {action : 'drile_load_product_video', product_id: product_id}
				,success : function(response){
					container.find('.product-video-content').html( response );
					container.removeClass('loading').addClass('show');
				}
			});
		}
	});
	
	/*** Product 360 ***/
	if( typeof $.fn.ThreeSixty == 'function' ){
		if( $('.ts-product-360-button').length == 0 ){
			setTimeout(function(){
				generate_product_360();
			}, 1000);
		}
		
		$('.ts-product-360-button').on('click', function(){
			$('#ts-product-360-modal').addClass('loading');
			generate_product_360();
			return false;
		});
	}
	
	function generate_product_360(){
		if( !$('.ts-product-360').hasClass('loaded') ){
			$('.ts-product-360').ThreeSixty({
				currentFrame: 1
				,imgList: '.threesixty_images'
				,imgArray: _ts_product_360_image_array
				,totalFrames: _ts_product_360_image_array.length
				,endFrame: _ts_product_360_image_array.length
				,progress: '.spinner'
				,navigation: true
				,responsive: true
				,playSpeed: 150
				,onReady: function(){
					$('#ts-product-360-modal').removeClass('loading').addClass('show');
					$('.ts-product-360').addClass('loaded');
				}
			});
		}
		else{
			$('#ts-product-360-modal').removeClass('loading').addClass('show');
		}
	}
	
	/*** Show more/less product content ***/
	if( $('.single-product .more-less-buttons').length > 0 ){
		var product_content = $('.single-product .more-less-buttons').siblings('.product-content');
		if( product_content.height() < 250 ){
			$('.single-product .more-less-buttons').remove();
			product_content.removeClass('closed show-more-less');
		}
		else{
			var timeout = 200 + ( product_content.find('img').length * 200 );
			setTimeout(function(){
				var scrollheight = product_content.get(0).scrollHeight;
				var speed = scrollheight / 1000;
				var style = '<style>'
							+ '.product-content.show-more-less{transition:'+speed+'s ease;}'
							+ '.product-content.opened{max-height:'+scrollheight+'px;}'
							+ '</style>';
				$('head').append( style );
			}, timeout);
		}
	}
	
	$('.single-product .more-less-buttons a').on('click', function(e){
		e.preventDefault();
		$(this).hide();
		$(this).siblings('a').show();
		var action = $(this).data('action');
		$(this).parent().siblings('.product-content').removeClass('opened closed').addClass(action);
		
		if( action == 'closed' ){
			var top = $(this).parents('.woocommerce-tabs').offset().top - get_fixed_header_height() - 10;
			$('body, html').animate({
				scrollTop: top
			}, 1000);
		}
	});
	
	/*** Single product scrolling ***/
	if( $(window).width() > 767 && $('.summary-scrolling').length ){
		function ts_scrolling_fixed(scrolling_element, fixed_element){
			if( scrolling_element.length == 0 || fixed_element.length == 0 || $(window).width() < 991
				|| fixed_element.height() >= scrolling_element.height() ){
				return;
			}
			
			var fixed_left = fixed_element.offset().left;
			var fixed_width = fixed_element.outerWidth();
			var admin_bar_height = $('#wpadminbar').length?$('#wpadminbar').outerHeight():0;
			var window_height = $(window).height();
			
			$(window).on('scroll', function(){
				var window_scroll_top = $(this).scrollTop();
				var sticky_height = 0;
				if( $('.is-sticky .header-sticky').length ){
					sticky_height = $('.is-sticky .header-sticky').outerHeight();
				}
				
				var fixed_height = fixed_element.height();
				var scrolling_height = scrolling_element.height();
				var scrolling_top = scrolling_element.offset().top;
				var start_scroll = fixed_height > window_height?fixed_height - window_height:0;
				
				if( window_scroll_top > scrolling_top + start_scroll - admin_bar_height - 20 ){
					var top = sticky_height + admin_bar_height + 20;
					
					if( start_scroll ){
						top = -start_scroll;
					}
					if( window_scroll_top + top + fixed_height > scrolling_top + scrolling_height ){
						top = scrolling_height - fixed_height + scrolling_top - window_scroll_top;
					}
					fixed_element.css({'position': 'fixed', 'left': fixed_left, 'top': top, 'width': fixed_width
										, 'transition': ( (top < 60 + admin_bar_height && sticky_height) || drile_params.sticky_header == 0 ?'none':'')});
				}
				else{
					fixed_element.attr('style', '');
				}
			});
		}
		ts_scrolling_fixed($('.summary-scrolling > .images-thumbnails'), $('.product > .summary'));
	}
	
	function get_fixed_header_height(){
		var admin_bar_height = $('#wpadminbar').length > 0?$('#wpadminbar').outerHeight():0;
		var sticky_height = $('.is-sticky .header-sticky').length > 0?$('.is-sticky .header-sticky').outerHeight():0;
		return admin_bar_height + sticky_height;
	}
	
	/*** Accordion - scroll to activated tab ***/
	$('.single-product .vc_tta-accordion .vc_tta-panel-heading').on('click', function(){
		if( $(this).parents('.vc_tta-panel').hasClass('vc_active') ){
			return;
		}
		var acc_header = $(this);
		
		setTimeout(function(){
			$('body,html').animate({
				scrollTop: acc_header.offset().top - get_fixed_header_height()
			}, 500);
		}, 800);
	});
	
	if( $('.woocommerce-tabs.accordion-tabs').length > 0 ){
		$('a.woocommerce-review-link').on('click', function(){
			var acc_header = $('#reviews').parents('.vc_tta-panel-body').siblings('.vc_tta-panel-heading');
			if( !acc_header.parents('.vc_tta-panel').hasClass('vc_active') ){
				setTimeout(function(){
					acc_header.trigger('click');
					acc_header.find('.vc_tta-panel-title a').trigger('click');
				}, 100);
			}
		});
	}
	
	/*** Next/Prev ***/
	if( $('.single-navigation').length > 0 ){
		var offset = $('.product.thumbnail-slider').length?250:0;
		var image_top = $('.images-thumbnails').offset().top + offset;
		$(window).on('scroll', function(){
			if( $(this).scrollTop() > image_top && $(this).scrollTop() < image_top + $('.images-thumbnails').height() ){
				$('.single-navigation').addClass('visible');
			}
			else{
				$('.single-navigation').removeClass('visible');
			}
		});
	}
	
});