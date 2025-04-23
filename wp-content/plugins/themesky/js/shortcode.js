jQuery(function($){
	"use strict";
	
	/*** Add shortcode custom style into the html tag ***/
	var shortcode_custom_style = '';
	$('.ts-shortcode-custom-style').each(function(){
		shortcode_custom_style += $(this).html();
	});
	$('.ts-shortcode-custom-style').remove();
	if( shortcode_custom_style ){
		shortcode_custom_style = shortcode_custom_style.replace(/&gt;/g, '>');
		$('head').append('<style id="ts-shortcode-custom-style" type="text/css">' + shortcode_custom_style + '</style>');
	}
	
	/*** Products ***/
	$('.ts-product-wrapper.ts-shortcode.ts-slider').each(function(){
		var element = $(this);
		
		var show_nav = element.data('nav')?true:false;
		var show_dots = element.data('dots')?true:false;
		var auto_play = element.data('autoplay')?true:false;
		var columns = element.data('columns')?element.data('columns'):5;
		var margin = element.data('margin')?element.data('margin'):0;
		var disable_responsive = element.data('disable_responsive')?true:false;
		
		var _slider_data = {
					loop: true
					,nav: show_nav
					,navText: [,]
					,dots: show_dots
					,navSpeed: 1000
					,rtl: $('body').hasClass('rtl')
					,margin: margin
					,navRewind: false
					,autoplay: auto_play
					,autoplayHoverPause: true
					,autoplaySpeed: 1000
					,responsiveBaseElement: element
					,responsiveRefreshRate: 400
					,responsive:{0:{items:1},320:{items:2},680:{items:3},940:{items:4},1025:{items:columns}}
					,onInitialized: function(){
						element.find('.content-wrapper').addClass('loaded').removeClass('loading');
					}
				};
		
		if( disable_responsive ){
			_slider_data.responsive = {0:{items:columns}};
		}
		
		if( columns == 1 ){
			_slider_data.responsive = {0:{items:1},320:{items:2},700:{items:3}};
		}
		
		setTimeout(function(){
			element.find('.products').owlCarousel(_slider_data);
		}, 200);
	});
	
	/*** Product Deals ***/
	$('.ts-product-deals-wrapper.ts-slider').each(function(){
		var element = $(this);
		var show_nav = false;
		var auto_play = false;
		var margin = 20;
		var columns = 4;
		
		if( element.data('nav') ){
			show_nav = true;
		}
		if( element.data('autoplay') ){
			auto_play = true;
		}
		if( element.data('margin') != undefined ){
			margin = element.data('margin');
		}
		if( element.data('columns') ){
			columns = element.data('columns');
		}
		
		var _slider_data = {
				loop: true
				,nav: show_nav
				,navText: [,]
				,dots: false
				,navSpeed: 1000
				,rtl: $('body').hasClass('rtl')
				,margin: margin
				,navRewind: false
				,autoplay: auto_play
				,autoplayHoverPause: true
				,autoplaySpeed: 1000
				,responsiveBaseElement: element
				,responsiveRefreshRate: 400
				,responsive:{0:{items:1},500:{items:2},751:{items:3},800:{items:4},870:{items:columns}}
				,onInitialized: function(){
					element.find('.content-wrapper').addClass('loaded').removeClass('loading');
				}
			};
			
		if( columns == 1 ){
			_slider_data.responsive = {0:{items:1},500:{items:2},700:{items:3}};
		}
		
		element.find('.products').owlCarousel(_slider_data);
	});
	
	/*** Product Category ***/
	$('.ts-product-category-wrapper.ts-slider').each(function(){
		var element = $(this);
		var show_nav = element.data('nav')?true:false;
		var auto_play = element.data('autoplay')?true:false;
		var margin = element.data('margin')?parseInt( element.data('margin') ):0;
		var columns = element.data('columns')?parseInt( element.data('columns') ):4;
		var _slider_data = { 
			loop: true
			,nav: show_nav
			,navText: [,]
			,dots: false
			,navSpeed: 1000
			,center: element.hasClass('center-slider')
			,rtl: $('body').hasClass('rtl')
			,margin: margin
			,navRewind: false
			,autoplay: auto_play
			,autoplayHoverPause: false
			,autoplaySpeed: 1000
			,responsiveBaseElement: element
			,responsiveRefreshRate: 400
			,responsive:{0:{items:1},420:{items:2},761:{items:3},871:{items:columns}}
			,onInitialized: function(){
				element.find('.content-wrapper').addClass('loaded').removeClass('loading');
			}
		};
		
		if( element.hasClass('style-3') ){
			_slider_data.responsive = {0:{items:2},420:{items:3},620:{items:4},731:{items:5},871:{items:columns}};
		}
		if( element.hasClass('center-slider') ){
			_slider_data.responsive = {0:{items:2},420:{items:3},761:{items:4},871:{items:columns}};
			
			_slider_data.onChanged = function(){
				element.find('.owl-item').removeClass('first-item last-item');
				element.find('.owl-item.active:first').addClass('first-item');
				if( !$('body').hasClass('rtl') ){
					element.find('.owl-item.active:last').addClass('last-item');
				}
				else{
					element.find('.owl-item.active:last').prev('.owl-item.active').addClass('last-item');
				}
			}
		}
		
		setTimeout(function(){
			element.find('.products').owlCarousel( _slider_data );
		}, 200);
	});
	
	/*** Product Brand ***/
	$('.ts-product-brand-wrapper.ts-slider').each(function(){
		var element = $(this);
		var show_nav = element.data('nav')?true:false;
		var auto_play = element.data('autoplay')?true:false;
		var margin = element.data('margin')?parseInt( element.data('margin') ):0;
		var columns = element.data('columns')?parseInt( element.data('columns') ):4;
		var _slider_data = { 
			loop: true
			,nav: show_nav
			,navText: [,]
			,dots: false
			,navSpeed: 1000
			,rtl: $('body').hasClass('rtl')
			,margin: margin
			,navRewind: false
			,autoplay: auto_play
			,autoplayHoverPause: false
			,autoplaySpeed: 1000
			,responsiveBaseElement: element
			,responsiveRefreshRate: 400
			,responsive:{0:{items:1},420:{items:2},700:{items:3},871:{items:columns}}
			,onInitialized: function(){
				element.find('.content-wrapper').addClass('loaded').removeClass('loading');
			}
		};
		
		if( element.hasClass('use-logo-setting') ){
			var break_point = element.data('break_point');
			var item = element.data('item');
			if( break_point.length > 0 ){
				_slider_data.responsive = {};
				for( var i = 0; i < break_point.length; i++ ){
					_slider_data.responsive[break_point[i]] = {items: item[i]};
				}
			}
		}
		
		element.find('.content-wrapper').owlCarousel( _slider_data );
	});
	
	/*** Product Widget ***/
	$('.ts-products-widget-wrapper.ts-slider').each(function(){
		var element = $(this);
		var show_nav = element.data('show_nav') == 1;
		var auto_play = element.data('auto_play') == 1;
		
		element.owlCarousel({
					loop : true
					,nav : show_nav
					,navText: [,]
					,dots : false
					,margin : 10
					,navSpeed : 1000
					,rtl: $('body').hasClass('rtl')
					,navRewind: false
					,autoplay: auto_play
					,autoplayHoverPause: true
					,autoplaySpeed: 1000
					,responsive:{0:{items:1}}
					,onInitialized: function(){
						element.addClass('loaded').removeClass('loading');
					}
				});
	});
	
	/*** Load Products In Category Tab ***/
	var ts_product_in_category_tab_data = [];
	
	/* Change tab */
	$('.ts-product-in-category-tab-wrapper .column-tabs .tab-item, .ts-product-in-product-type-tab-wrapper .column-tabs .tab-item').on('click', function(){
		var element = $(this).parents('.ts-product-in-category-tab-wrapper');
		var is_product_type_tab = false;
		if( element.length == 0 ){
			element = $(this).parents('.ts-product-in-product-type-tab-wrapper');
			is_product_type_tab = true;
		}
		
		var element_top = element.hasClass('style-verticle')?element.find('.column-tabs').offset().top:element.offset().top;
		if( element_top > $(window).scrollTop() ){
			var admin_bar_height = $('#wpadminbar').length > 0?$('#wpadminbar').outerHeight():0;
			var sticky_height = $('.is-sticky .header-sticky').length > 0?$('.is-sticky .header-sticky').outerHeight():0;
			$('body, html').animate({
				scrollTop: element_top - sticky_height - admin_bar_height - 20
			}, 500);
		}
		
		if( $(this).hasClass('current') || element.find('.column-products').hasClass('loading') ){
			return;
		}
		
		var element_id = element.attr('id');
		var atts = element.data('atts');
		if( !is_product_type_tab ){
			var product_cat = $(this).data('product_cat');
			var shop_more_link = $(this).data('link');
			var is_general_tab = $(this).hasClass('general-tab')?1:0;
			var margin = atts.margin;
		}
		else{
			var product_cat = atts.product_cats;
			var is_general_tab = 0;
			var margin = atts.margin;
			atts.product_type = $(this).data('product_type');
			element.find('.column-products').removeClass('recent sale featured best_selling top_rated mixed_order').addClass(atts.product_type);
		}
		
		if( !is_product_type_tab && element.find('a.shop-more-button').length > 0 ){
			element.find('a.shop-more-button').attr('href', shop_more_link);
		}
		
		element.find('.column-tabs .tab-item').removeClass('current');
		$(this).addClass('current');
		
		/* Change banners */
		if( !is_product_type_tab && element.hasClass('style-verticle') ){
			var banners = element.find('.banners');
			if( banners.length ){
				var banner_urls = banners.data('banner_urls');
				banner_urls = banner_urls.split(',');
				var tab_index = element.find('.column-tabs .tab-item.current').index();
				if( banner_urls.length > tab_index ){
					banners.attr('style', 'background-image:url('+banner_urls[tab_index]+')');
				}
				banners.attr('href', shop_more_link);
			}
		}
		
		/* Check cache */
		var tab_data_index = element_id + '-' + product_cat.toString().split(',').join('-');
		if( is_product_type_tab ){
			tab_data_index += '-' + atts.product_type;
		}
		if( ts_product_in_category_tab_data[tab_data_index] != undefined ){
			/* destroy slider first */
			element.find('.column-products .products.owl-carousel').owlCarousel('destroy');
			
			element.find('.column-products .products').remove();
			element.find('.column-products').append( ts_product_in_category_tab_data[tab_data_index] ).hide().fadeIn(600);
			
			/* Shop more button handle */
			if( !is_product_type_tab ){
				ts_product_in_category_tab_shop_more_handle( element, atts );
			}
			
			/* Generate slider */
			ts_product_slider_in_category_tab( element, atts.show_nav, atts.auto_play, atts.columns, margin, false );
			
			return;
		}
		
		element.find('.column-products').addClass('loading');
		
		$.ajax({
			type : "POST",
			timeout : 30000,
			url : ts_shortcode_params.ajax_uri,
			data : {action: 'ts_get_product_content_in_category_tab', atts: atts, product_cat: product_cat, is_general_tab: is_general_tab},
			error: function(xhr,err){
				
			},
			success: function(response) {
				if( response ){
					/* destroy slider first */
					element.find('.column-products .products.owl-carousel').owlCarousel('destroy');
					
					element.find('.column-products .products').remove();
					element.find('.column-products').append( response ).hide().fadeIn(600);
					/* save cache */
					if( element.find('.counter-wrapper').length == 0 ){
						ts_product_in_category_tab_data[tab_data_index] = response;
					}
					else{
						ts_counter( element.find('.counter-wrapper') );
					}
					/* Shop more button handle */
					if( !is_product_type_tab ){
						ts_product_in_category_tab_shop_more_handle( element, atts );
					}
					/* Generate slider */
					ts_product_slider_in_category_tab( element, atts.show_nav, atts.auto_play, atts.columns, margin, false );
				}
				element.find('.column-products').removeClass('loading');
			}
		});
	});
	
	$('.ts-product-in-category-tab-wrapper, .ts-product-in-product-type-tab-wrapper').each(function(){
		var element = $(this);
		var atts = element.data('atts');
		if( $(this).hasClass('ts-product-in-category-tab-wrapper') ){
			ts_product_in_category_tab_shop_more_handle( element, atts );
			ts_product_slider_in_category_tab( element, atts.show_nav, atts.auto_play, atts.columns, atts.margin, true );
		}
		else{
			ts_product_slider_in_category_tab( element, atts.show_nav, atts.auto_play, atts.columns, atts.margin, true );
		}
	});
	
	$('.ts-product-in-category-tab-wrapper.style-verticle .banners').each(function(){
		var banners = $(this);
		var banner_urls = banners.data('banner_urls');
		banner_urls = banner_urls.split(',');
		$.each(banner_urls, function(i, src){
			banners.after('<img src="'+banner_urls[0]+'" data-src="'+src+'" class="ts-lazy-load" style="visibility:hidden;opacity:0;position:absolute;left:-5000px;top:100%" alt="Hidden Banner" />');
		});
	});
	
	$('.ts-product-in-category-tab-wrapper.style-horizontal-icons').each(function(){
		var tabs = $(this).find('.tabs');
		var _slider_data = {
				loop: true
				,nav: true
				,navText: [,]
				,dots: false
				,navSpeed: 1000
				,rtl: $('body').hasClass('rtl')
				,navRewind: false
				,responsiveBaseElement: tabs
				,responsiveRefreshRate: 400
				,responsive:{0:{items:1},320:{items:2},550:{items:3},750:{items:4},950:{items:5},1150:{items:6},1350:{items:7}}
				,onInitialized: function(){
					tabs.addClass('loaded').removeClass('loading');
				}
			};
		tabs.owlCarousel(_slider_data);
	});
	
	$(window).on('resize', function(){
		$('.ts-product-in-category-tab-wrapper .column-products, .ts-product-in-product-type-tab-wrapper .column-products').css('min-height', '250px');
	});
	
	function ts_product_in_category_tab_min_height( element ){
		var product_wrapper = element.find('.column-products');
		setTimeout(function(){
			if( !product_wrapper.hasClass('loading') ){
				product_wrapper.css('min-height', '');
				product_wrapper.css('min-height', product_wrapper.height());
			}
		}, 800);
	}
	
	function ts_product_in_category_tab_shop_more_handle(element, atts){
		var hide_shop_more = element.find('.products .hide-shop-more').length;
		element.find('.products .hide-shop-more').remove();
		
		if( element.find('.tab-item.current').hasClass('general-tab') && atts.show_shop_more_general_tab == 0 ){
			hide_shop_more = true;
		}
		
		if( element.find('.products .product').length == 0 ){
			hide_shop_more = true;
		}
		
		if( atts.show_shop_more_button == 1 ){
			if( hide_shop_more ){
				element.find('.shop-more').addClass('hidden');
				element.removeClass('has-shop-more-button');
			}
			else{
				element.find('.shop-more').removeClass('hidden');
				element.addClass('has-shop-more-button');
			}
		}
	}
	
	function ts_product_slider_in_category_tab( element, show_nav, auto_play, columns, margin, timeout ){
		if( element.hasClass('ts-slider') && element.find('.product').length > 0 ){
			show_nav = (show_nav == 1)?true:false;
			auto_play = (auto_play == 1)?true:false;
			columns = parseInt(columns);
			margin = parseInt(margin);
			var atts = element.data('atts');
			var show_dots = typeof atts.show_dots != 'undefined'?atts.show_dots == 1:false;
			var navTextPrev = (atts.prev_text)?atts.prev_text:'';
			var navTextNext = (atts.next_text)?atts.next_text:'';
			var _slider_data = { 
				loop : true
				,nav : show_nav
				,navText : [navTextPrev,navTextNext]
				,dots : show_dots
				,navSpeed: 1000
				,rtl: $('body').hasClass('rtl')
				,margin : margin
				,navRewind: false
				,autoplay: auto_play
				,autoplayHoverPause: false
				,autoplaySpeed: 1000
				,responsiveBaseElement: element.find('.products')
				,responsiveRefreshRate: 400
				,responsive:{0:{items:1},320:{items:2},600:{items:3},800:{items:4},1000:{items:columns}}
				,onInitialized: function(){
					element.find('.column-products').removeClass('loading');
					ts_product_in_category_tab_min_height( element );
				}
			};

			if( timeout ){
				setTimeout(function(){
					element.find('.products').owlCarousel( _slider_data );
				}, 200);
			}
			else{
				element.find('.products').owlCarousel( _slider_data );
			}
		}
		else{
			element.find('.column-products').removeClass('loading');
			ts_product_in_category_tab_min_height( element );
		}
	}
	
	function ts_blog_shortcode_gallery_slider( element, atts ){
		if( element.find('.thumbnail.gallery:not(.loaded)').length == 0 ){
			return;
		}
		var show_nav = parseInt(atts.show_nav) == 1;
		var slider_data = {
			loop: true
			,nav: show_nav
			,navText: [,]
			,dots: false
			,animateIn: 'fadeIn'
			,animateOut: 'fadeOut'
			,navSpeed: 1000
			,rtl: $('body').hasClass('rtl')
			,margin: 10
			,navRewind: false
			,autoplay: true
			,autoplayTimeout: 4000
			,autoplayHoverPause: true
			,autoHeight: true
			,mouseDrag: false
			,touchDrag: false
			,responsive:{0:{items:1}}
			,onInitialized: function(){
				element.find('.thumbnail.gallery').addClass('loaded').removeClass('loading');
			}
		};
		element.find('.thumbnail.gallery:not(.loaded) figure').owlCarousel(slider_data);
	}
	
	/*** Blog Shortcode ***/
	$('.ts-blogs-wrapper.ts-shortcode').each(function(){
		var element = $(this);
		var atts = element.data('atts');
		
		/* Slider */
		if( atts.is_slider ){
			var show_nav = parseInt(atts.show_nav) == 1;
			var auto_play = parseInt(atts.auto_play) == 1;
			var margin = parseInt(atts.margin);
			var columns = parseInt(atts.columns);
			var center = ($(this).hasClass('partial-view') && $(window).width() > 992)?true:false;
			var slider_data = {
				loop: true
				,center: center
				,nav: show_nav
				,navText: [,]
				,dots: false
				,navSpeed: 1000
				,rtl: $('body').hasClass('rtl')
				,margin: margin
				,navRewind: false
				,autoplay: auto_play
				,autoplayHoverPause: true
				,autoplaySpeed: 1000
				,responsiveBaseElement: element
				,responsiveRefreshRate: 400
				,responsive:{0:{items:1},570:{items:2},767:{items:3},870:{items:columns}}
				,onInitialized: function(){
					element.addClass('loaded').removeClass('loading');
				}
			};
			
			if( element.hasClass('item-list') ){
				slider_data.responsive = {0:{items:1},570:{items:2},767:{items:3},870:{items:columns}};
			}
			
			setTimeout(function(){
				element.find('.content-wrapper > .blogs').owlCarousel(slider_data);
			}, 200);
		}
		
		/* Blog Gallery - Masonry - Load more */
		var is_masonry = false;
		if( atts.is_masonry && typeof $.fn.isotope == 'function' ){
			is_masonry = true;
		}
		
		ts_blog_shortcode_gallery_slider( element, atts );
		
		if( is_masonry ){
			setTimeout(function(){
				element.removeClass('loading');
				element.find('.blogs').isotope();
			}, 500);
		}
		
		/* Show more */
		element.find('a.load-more').on('click', function(){
			var button = $(this);
			if( button.hasClass('loading') ){
				return false;
			}
			
			button.addClass('loading');
			var paged = button.attr('data-paged');
			var total_pages = button.attr('data-total_pages');
			
			$.ajax({
				type : "POST",
				timeout : 30000,
				url : ts_shortcode_params.ajax_uri,
				data : {action: 'ts_blogs_load_items', paged: paged, atts : atts},
				error: function(xhr,err){
					
				},
				success: function(response) {
					if( paged == total_pages ){
						button.parent().remove();
					}
					else{
						button.removeClass('loading');
						button.attr('data-paged', ++paged);
					}
					if( response != 0 && response != '' ){
						if( is_masonry ){										
							element.find('.blogs').isotope('insert', $(response));
							setTimeout(function(){
								element.find('.blogs').isotope('layout');
							}, 500);
						}
						else { /* Append and Update first-last classes */
							element.find('.blogs').append(response);
							
							var columns = parseInt(atts.columns);
							element.find('.blogs .item').removeClass('first last');
							element.find('.blogs .item').each(function(index, ele){
								if( index % columns == 0 ){
									$(ele).addClass('first');
								}
								if( index % columns == columns - 1 ){
									$(ele).addClass('last');
								}
							});
						}
						
						ts_blog_shortcode_gallery_slider( element, atts );
					}
					else{ /* No results */
						button.parent().remove();
					}
				}
			});
			
			return false;
		});
	});
	
	/*** Image Gallery ***/
	$('.ts-image-gallery-wrapper.ts-slider').each(function(){
		var element = $(this);
		var show_nav = parseInt(element.data('nav')) == 1;
		var show_dots = parseInt(element.data('dots')) == 1;
		var auto_play = parseInt(element.data('autoplay')) == 1;
		var margin = parseInt(element.data('margin'));
		var columns = parseInt(element.data('columns'));
		var responsive_items = parseInt(element.data('responsive'));
		var slider_data = {
			loop: true
			,nav: show_nav
			,navText: [,]
			,navSpeed: 1000
			,dots: show_dots
			,rtl: $('body').hasClass('rtl')
			,margin: margin
			,navRewind: false
			,autoplay: auto_play
			,autoplayHoverPause: true
			,autoplaySpeed: 1000
			,autoHeight: true
			,responsiveBaseElement: element
			,responsiveRefreshRate: 400
			,responsive:{0:{items:1},320:{items:2},550:{items:3},750:{items:4},950:{items:columns}}
			,onInitialized: function(){
				element.find('.images').addClass('loaded').removeClass('loading');
			}
		};
		
		if( responsive_items == 0 ){
			slider_data.responsive = {0:{items:columns}};
		}
		setTimeout(function(){
			element.find('.images').owlCarousel(slider_data);
		}, 200);
	});
	
	/*** Logo Slider ***/
	$('.ts-logo-slider-wrapper.loading').each(function(){
		var element = $(this);
		var margin = parseInt(element.data('margin'));
		var show_nav = element.data('nav')?true:false;
		var auto_play = element.data('auto_play')?true:false;
		
		var break_point = element.data('break_point');
		var item = element.data('item');
		var _slider_data = {
				loop: true
				,nav: show_nav
				,navText: [,]
				,dots: false
				,navSpeed: 1000
				,rtl: $('body').hasClass('rtl')
				,margin: margin
				,navRewind: false
				,autoplay: auto_play
				,autoplayHoverPause: true
				,autoplaySpeed: 1000
				,responsiveBaseElement: element
				,responsiveRefreshRate: 400
				,responsive:{0:{items:1},300:{items:2},400:{items:3},640:{items:4},930:{items:5}}
				,onInitialized: function(){
					element.addClass('loaded').removeClass('loading');
				}
			};
			
		if( break_point.length > 0 ){
			_slider_data.responsive = {};
			for( var i = 0; i < break_point.length; i++ ){
				_slider_data.responsive[break_point[i]] = {items: item[i]};
			}
		}
		
		setTimeout(function(){
			element.find('.items').owlCarousel(_slider_data);
		}, 200);
	});
	
	/*** Team Member ***/
	$('.ts-team-members.ts-slider').each(function(){
		var element = $(this);
		var margin = parseInt(element.data('margin'));
		var show_nav = element.data('nav')?true:false;
		var auto_play = element.data('auto_play')?true:false;
		var columns = parseInt(element.data('columns'));
		
		var _slider_data = {
				loop: true
				,nav: show_nav
				,navText: [,]
				,dots: false
				,navSpeed: 1000
				,rtl: $('body').hasClass('rtl')
				,margin: margin
				,navRewind: false
				,autoplay: auto_play
				,autoplayHoverPause: true
				,autoplaySpeed: 1000
				,responsiveBaseElement: element
				,responsiveRefreshRate: 400
				,responsive:{0:{items:1},420:{items:2},640:{items:3},768:{items:4},930:{items:columns}}
				,onInitialized: function(){
					element.find('.items').addClass('loaded').removeClass('loading');
				}
			};
			
		if( element.hasClass('style-3') ){
			_slider_data.responsive = {0:{items:1},640:{items:2},768:{items:3},930:{items:columns}};
		}
		
		element.find('.items').owlCarousel(_slider_data);
	});
	
	/*** Reload Soundcloud ***/
	$('.owl-item .ts-soundcloud iframe').each(function(){
		var iframe = $(this);
		var src = iframe.attr('src');
		iframe.attr('src', src);
	});
	
	/*** Testimonial ***/
	setTimeout(function(){
		$('.ts-testimonial-wrapper.ts-slider .items').each(function(){
			var element = $(this).parent('.ts-slider');
			var validate_slider = true;
			
			if( element.find('.item').length <= 1 ){
				validate_slider = false;
			}
			
			if( validate_slider ){
				var show_nav = element.data('nav')?true:false;
				var show_dots = element.data('dots')?true:false;
				var autoplay = element.data('autoplay')?true:false;
				var data_dot = element.find('.item:first').attr('data-dot') != undefined;
				
				var slider_data = {
					loop: true
					,nav: show_nav
					,dots: show_dots
					,dotData: data_dot
					,dotsData: data_dot
					,animateIn: 'fadeIn'
					,animateOut: 'fadeOut'
					,navText: [,]
					,navSpeed: 1000
					,rtl: $('body').hasClass('rtl')
					,navRewind: false
					,autoplay: autoplay
					,autoplayHoverPause: true
					,mouseDrag: false
					,responsive: {0:{items:1}}
					,onInitialized: function(){
						element.find('.items').addClass('loaded').removeClass('loading');
					}
				};
				element.find('.items').owlCarousel(slider_data);
			}
			else{
				element.find('.items').removeClass('loading');
			}
		});
	}, 200);
	
	/*** Instagram ***/
	$('.ts-instagram-wrapper.ts-slider').each(function(){
		var element = $(this);
		var show_nav = element.data('nav')?true:false;
		var auto_play = element.data('autoplay')?true:false;
		var margin = element.data('margin')?parseInt(element.data('margin')):0;
		var columns = element.data('columns')?parseInt(element.data('columns')):4;
		var _slider_data = { 
			loop: true
			,nav: show_nav
			,navText: [,]
			,dots: false
			,navSpeed: 1000
			,rtl: $('body').hasClass('rtl')
			,margin: margin
			,navRewind: false
			,autoplay: auto_play
			,autoplaySpeed: 1000
			,responsiveBaseElement: element
			,responsiveRefreshRate: 400
			,responsive: {0:{items:1},320:{items:2},500:{items:3},800:{items:4},1170:{items:columns}}
			,onInitialized: function(){
				element.addClass('loaded').removeClass('loading');
			}
		};
		
		setTimeout(function(){
			element.owlCarousel( _slider_data );
		}, 200);
	});
	
	/*** Video ***/
	$('.ts-video-2 > a').on('click', function(e){
		e.preventDefault();
		$(this).siblings('.ts-popup-modal').addClass('show');
	});
	
	$('.ts-home-tabs .tab-item a').on('mouseenter first_active', function(){
		$(this).parent().siblings().find('a').removeClass('active');
		$(this).addClass('active');
		var tab_id = $(this).attr('data-tab');
		if( $('#' + tab_id).length ){
			$('#' + tab_id).siblings().removeClass('active');
			$('#' + tab_id).addClass('active');
		}
	});
	
	$('.ts-home-tabs .tab-item a[href="'+window.location.href+'"]').trigger('first_active');
	
	/*** Counter ***/
	function ts_counter( elements ){
		if( elements.length > 0 ){
			var interval = setInterval(function(){
				elements.each(function(index, element){
					var wrapper = $(element);
					var second = parseInt( wrapper.find('.seconds .number').text() );
					if( second > 0 ){
						second--;
						second = ( second < 10 )? zeroise(second, 2) : second.toString();
						wrapper.find('.seconds .number').text(second);
						return;
					}
					
					var delta = 0;
					var time_day = 60 * 60 * 24;
					var time_hour = 60 * 60;
					var time_minute = 60;
					
					var day = parseInt( wrapper.find('.days .number').text() );
					var hour = parseInt( wrapper.find('.hours .number').text() );
					var minute = parseInt( wrapper.find('.minutes .number').text() );
					
					if( day != 0 || hour != 0  || minute != 0 || second != 0 ){
						delta = (day * time_day) + (hour * time_hour) + (minute * time_minute) + second;
						delta--;
						
						day = Math.floor(delta / time_day);
						delta -= day * time_day;
						
						hour = Math.floor(delta / time_hour);
						delta -= hour * time_hour;
						
						minute = Math.floor(delta / time_minute);
						delta -= minute * time_minute;
						
						second = delta > 0?delta:0;
						
						day = ( day < 10 )? zeroise(day, 2) : day.toString();
						hour = ( hour < 10 )? zeroise(hour, 2) : hour.toString();
						minute = ( minute < 10 )? zeroise(minute, 2) : minute.toString();
						second = ( second < 10 )? zeroise(second, 2) : second.toString();
						
						wrapper.find('.days .number').text(day);
						wrapper.find('.hours .number').text(hour);
						wrapper.find('.minutes .number').text(minute);
						wrapper.find('.seconds .number').text(second);
					}
					
				});
			}, 1000);
		}
	}
	
	ts_counter( $('.product .counter-wrapper, .ts-countdown .counter-wrapper') );
	
	/*** Portfolio ***/
	setTimeout(function(){
		if( typeof $.fn.isotope == 'function' ){
			$('.ts-portfolio-wrapper.ts-masonry .portfolio-inner').isotope({filter: '*'});
		}
		$('.ts-portfolio-wrapper.ts-masonry').removeClass('loading');
	}, 500);
	
	/* Load more + Slider */
	$('.ts-portfolio-wrapper').each(function(){
		var element = $(this);
		var atts = element.data('atts');
		var is_slider = parseInt(atts.is_slider);
		
		element.find('a.load-more').on('click', function(){
			var button = $(this);
			if( button.hasClass('loading') ){
				return false;
			}
			
			button.addClass('loading');
			var paged = button.attr('data-paged');
			var total_pages = button.attr('data-total_pages');
			
			$.ajax({
				type : "POST",
				timeout : 30000,
				url : ts_shortcode_params.ajax_uri,
				data : {action: 'ts_portfolio_load_items', paged: paged, atts : atts},
				error: function(xhr,err){
					
				},
				success: function(response) {
					if( paged == total_pages ){
						button.parent().remove();
					}
					else{
						button.removeClass('loading');
						button.attr('data-paged', ++paged);
					}
					if( response != 0 && response != '' ){
						if( typeof $.fn.isotope == 'function' ){										
							element.find('.portfolio-inner').isotope('insert', $(response));
							element.find('.filter-bar li.current').trigger('click');
							setTimeout(function(){
								element.find('.portfolio-inner').isotope('layout');
							}, 500);
						}
					}
					else{ /* No results */
						button.parent().remove();
					}
				}
			});
			
			return false;
		});
		
		if( is_slider ){
			var auto_play = parseInt(atts.auto_play)?true:false;
			var show_nav = parseInt(atts.show_nav)?true:false;
			var show_dots = parseInt(atts.show_dots)?true:false;
			var columns = parseInt(atts.columns);
			var margin = parseInt(atts.margin);
			var slider_data = {
				loop: true
				,nav: show_nav
				,navText: [,]
				,dots: show_dots
				,navSpeed: 1000
				,rtl: $('body').hasClass('rtl')
				,margin: margin
				,navRewind: false
				,autoplay: auto_play
				,autoplayHoverPause: true
				,autoplaySpeed: 1000
				,responsiveBaseElement: element
				,responsiveRefreshRate: 400
				,responsive: {0:{items:1},500:{items:2},900:{items:3},1170:{items:columns}}
				,onInitialized: function(){
					element.addClass('loaded').removeClass('loading');
				}
			};
			element.find('.portfolio-inner').owlCarousel(slider_data);
		}
	});
	
	$('.ts-portfolio-wrapper .filter-bar li').on('click', function(){
		$(this).siblings('li').removeClass('current');
		$(this).addClass('current');
		var container = $(this).parents('.ts-portfolio-wrapper').find('.portfolio-inner');
		var data_filter = $(this).data('filter');
		container.isotope({filter: data_filter});
	});
	
	/* Update like */
	$(document).on('click', '.ts-portfolio-wrapper .portfolio-thumbnail .like, .single-portfolio .portfolio-like .ic-like', function(e){
		var _this = $(this);
		
		if( _this.hasClass('loading') ){
			return false;
		}
		_this.addClass('loading');
		
		var already_like = _this.hasClass('already-like');
		var is_single = _this.hasClass('ic-like');
		
		var post_id = _this.data('post_id');
		$.ajax({
			type : "POST",
			timeout : 30000,
			url : ts_shortcode_params.ajax_uri,
			data : {action: 'ts_portfolio_update_like', post_id: post_id},
			error: function(xhr,err){
				_this.removeClass('loading');
			},
			success: function(response) {
				if( response != '' ){
					if( already_like ){
						_this.removeClass('already-like');
						if( !is_single ){
							_this.attr('title', _this.data('like-title'));
						}
					}
					else{
						_this.addClass('already-like');
						if( !is_single ){
							_this.attr('title', _this.data('liked-title'));
						}
					}
					if( is_single ){
						var single_plural = '1' == response ? 'single' : 'plural';
						response += ' ' + _this.siblings('.like-num').data(single_plural);
						_this.siblings('.like-num').text(response);
					}
				}
				_this.removeClass('loading');
			}
		});
		
		return false;
	});
	
	$(document).on('click', '.single-portfolio .portfolio-like', function(){
		$(this).find('.ic-like').trigger('click');
	});
	
	/*** Milestone ***/
	if( typeof $.fn.waypoint == 'function' && typeof $.fn.countTo == 'function' ){
		$('.ts-milestone').waypoint(function(){
			this.disable();
			var element = $(this.element);
			var end_num = element.data('number');
			
			element.find('.count').countTo({
							from: 0
							,to: end_num
							,speed: 1500
							,refreshInterval: 30
						});
		}, {offset: '95%', triggerOnce: true});
	}
	
	/*** Google Map ***/
	function ts_gmap_initialize( map_content_obj, address, zoom, map_type, title ){
		var geocoder, map;
		geocoder = new google.maps.Geocoder();
	
		geocoder.geocode( {'address': address}, function(results, status) {
			if( status == google.maps.GeocoderStatus.OK ){
				var _ret_array =  new Array(results[0].geometry.location.lat(),results[0].geometry.location.lng());
				map.setCenter(results[0].geometry.location);
				var marker = new google.maps.Marker({
					map: map
					,title: title
					,position: results[0].geometry.location
				});
			}
		});
		
		var mapCanvas = map_content_obj.get(0);
		var mapOptions = {
			center: new google.maps.LatLng(44.5403, -78.5463)
			,zoom: zoom
			,mapTypeId: google.maps.MapTypeId[map_type]
			,scrollwheel : false
			,zoomControl : true
			,panControl : true
			,scaleControl : true
			,streetViewControl : false
			,overviewMapControl : true
			,disableDoubleClickZoom : false
		}
		map = new google.maps.Map(mapCanvas, mapOptions)
	}
	
	$(window).on('load_gmap resize', function(){
		$('.google-map-container').each(function(){
			var element = $(this);
			var map_content = element.find('> .map-content');
			var address = element.data('address');
			var zoom = element.data('zoom');
			var map_type = element.data('map_type');
			var title = element.data('title');
			ts_gmap_initialize( map_content, address, zoom, map_type, title );
		});
	});
	
	$(window).trigger('load_gmap');
	
	/*** Widgets ***/
	/* Blog widget */
	$('.ts-blogs-widget-wrapper.ts-slider').each(function(){
		var element = $(this);
		var show_nav = element.data('show_nav') == 1;
		var auto_play = element.data('auto_play') == 1;
		
		element.owlCarousel({
				loop: true
				,nav: show_nav
				,navText: [,]
				,dots: false
				,margin: 10
				,navSpeed: 1000
				,rtl: $('body').hasClass('rtl')
				,navRewind: false
				,autoplay: auto_play
				,autoplayHoverPause: true
				,autoplaySpeed: 1000
				,responsive: {0:{items:1}}
				,onInitialized: function(){
					element.addClass('loaded').removeClass('loading');
				}
			});
	});
	
	/* Custom WP Widget Categories Dropdown */
	$('.widget_categories > ul').each(function(index, ele){
		var _this = $(ele);
		var icon_toggle_html = '<span class="icon-toggle"></span>';
		var ul_child = _this.find('ul.children');
		ul_child.hide();
		ul_child.closest('li').addClass('cat-parent');
		ul_child.before( icon_toggle_html );
	});
	
	$('.widget_categories span.icon-toggle').on('click', function(){
		var parent_li = $(this).parent('li.cat-parent');
		if( !parent_li.hasClass('active') ){
			parent_li.find('ul.children:first').slideDown();
			parent_li.addClass('active');
		}
		else{
			parent_li.find('ul.children').slideUp();
			parent_li.removeClass('active');
			parent_li.find('li.cat-parent').removeClass('active');
		}
	});
	
	$('.widget_categories li.current-cat').parents('ul.children').siblings('.icon-toggle').trigger('click');
	$('.widget_categories li.current-cat.cat-parent > .icon-toggle').trigger('click');
	
	/* Product Categories widget */
	$('.widget-container.ts-product-categories-widget .icon-toggle').on('click', function(){
		var parent_li = $(this).parent('li.cat-parent');
		if( !parent_li.hasClass('active') ){
			parent_li.addClass('active');
			parent_li.find('ul.children:first').slideDown();
		}
		else{
			parent_li.find('ul.children').slideUp();
			parent_li.removeClass('active');
			parent_li.find('li.cat-parent').removeClass('active');
		}
	});
	
	$('.widget-container.ts-product-categories-widget').each(function(){
		var element = $(this);
		
		var parent_li = element.find('ul.children').parent('li');
		parent_li.addClass('cat-parent');
		
		element.find('li.current').parents('ul.children').siblings('.icon-toggle').trigger('click');
	});
	
	$('.widget-container.ts-product-categories-widget .cat-parent.current > .icon-toggle').trigger('click');
	
	/* Product Filter By Availability */
	$('.product-filter-by-availability-wrapper > ul input[type="checkbox"]').on('change', function(){
		$(this).parent('li').siblings('li').find('input[type="checkbox"]').attr('checked', false);
		var val = '';
		if( $(this).is(':checked') ){
			val = $(this).val();
		}
		var form = $(this).closest('ul').siblings('form');
		if( val != '' ){
			form.find('input[name="stock"]').val(val);
		}
		else{
			form.find('input[name="stock"]').remove();
		}
		form.submit();
	});
	
	/* Product Filter By Price */
	$('.product-filter-by-price-wrapper li').on('click', function(){
		var form = $(this).closest('ul').siblings('form');
		if( !$(this).hasClass('chosen') ){
			var min_price = $(this).data('min');
			var max_price = $(this).data('max');
			
			if( min_price !== '' ){
				form.find('input[name="min_price"]').val(min_price);
			}
			else{
				form.find('input[name="min_price"]').remove();
			}
			if( max_price !== '' ){
				form.find('input[name="max_price"]').val(max_price);
			}
			else{
				form.find('input[name="max_price"]').remove();
			}
		}
		else{
			form.find('input[name="min_price"]').remove();
			form.find('input[name="max_price"]').remove();
		}
		form.submit();
	});
	
	/* Product Filter By Brand */
	$('.product-filter-by-brand-wrapper ul input[type="checkbox"]').on('change', function(){
		var wrapper = $(this).parents('.product-filter-by-brand-wrapper');
		var query_type = wrapper.find('> .query-type').val();
		var checked = $(this).is(':checked');
		var val = new Array();
		if( query_type == 'or' ){
			wrapper.find('ul input[type="checkbox"]').attr('checked', false);
			if( checked ){
				$(this).off('change');
				$(this).attr('checked', true);
				val.push( $(this).val() );
			}
		}
		else{
			wrapper.find('ul input[type="checkbox"]:checked').each(function(index, ele){
				val.push( $(ele).val() );
			});
		}
		val = val.join(',');
		var form = wrapper.find('form');
		if( val != '' ){
			form.find('input[name="product_brand"]').val( val );
		}
		else{
			form.find('input[name="product_brand"]').remove();
		}
		form.submit();
	});
	
	/* Recent Comment Widget */
	$('.ts-recent-comments-widget-wrapper.ts-slider').each(function(){
		var element = $(this);
		var show_nav = element.data('show_nav') == 1;
		var auto_play = element.data('auto_play') == 1;
		
		element.owlCarousel({
					loop: true
					,margin: 10
					,nav: show_nav
					,navText: [,]
					,dots: false
					,navSpeed: 1000
					,rtl: $('body').hasClass('rtl')
					,navRewind: false
					,autoplay: auto_play
					,autoplayHoverPause: true
					,autoplaySpeed: 1000
					,responsive:{0:{items:1}}
					,onInitialized: function(){
						element.addClass('loaded').removeClass('loading');
					}
				});
	});
	
});

function zeroise( str, max ){
	str = str.toString();
	return str.length < max ? zeroise('0' + str, max) : str;
}