/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
require([
    'jquery',
    'mage/smart-keyboard-handler',
    'mage/mage',
    'mage/ie-class-fixer',
    'domReady!'
], function ($, keyboardHandler) {
    'use strict';

    if ($('body').hasClass('checkout-cart-index')) {
        if ($('#co-shipping-method-form .fieldset.rates').length > 0 && $('#co-shipping-method-form .fieldset.rates :checked').length === 0) {
            $('#block-shipping').on('collapsiblecreate', function () {
                $('#block-shipping').collapsible('forceActivate');
            });
        }
    }
    $('.cart-summary').mage('sticky', {
        container: '#maincontent'
    });
    $('.page-header .header.links').clone().appendTo('#store\\.links');
    keyboardHandler.apply();
});

/******************** Js Custom ***********************/
require([
    'jquery',
    'parallax'
], function ($) {
	
	//ScrollTop
	$('#backtotop').hide();
    $(window).scroll(function () {
        if ($(this).scrollTop() > 350) {
            $('#backtotop').show();
        }
        else{
            $('#backtotop').hide();
        }
        return false;
    });
	$(document).ready(function($) {
		$('#backtotop').click(function(e) {
			$('html,body').animate({scrollTop:0}, 500);
			return false;
			e.preventDefault();
		});
	});
	
	$(window).scroll(function(){
		var side_header_height = $(".page-header.header-type7 .header-content").innerHeight();
		var window_height = $(window).height();
		if(side_header_height-window_height<$(window).scrollTop()){
			if(!$(".page-header.header-type7 .header-content").hasClass("fixed-bottom"))
				$(".page-header.header-type7 .header-content").addClass("fixed-bottom");
		}
		if(side_header_height-window_height>=$(window).scrollTop()){
			if($(".page-header.header-type7 .header-content").hasClass("fixed-bottom"))
				$(".page-header.header-type7 .header-content").removeClass("fixed-bottom");
		}
	});
	
	// ==== Display navigation header-type5 ====
    $("#btn-bar").on('click', function (e) {
        e.preventDefault();
        $('.siderbarmenu').addClass('siderbarmenu-active');
		return false;
    });
	 $(".siderbarmenu .btn-close").on('click', function (e) {
        e.preventDefault();
        $('.siderbarmenu').removeClass('siderbarmenu-active');
		return false;
    });
	
	// ==== Organic Look ====
	$(document).ready(function($) {
		$('.organic-look .organic-img a').on('click', function (e) {
			e.preventDefault();
			$(this).parent().next('.organic-main').addClass('active');
			$('.organic-close a').addClass('active');
			return false;
		});
		$('.organic-close a').on('click', function (e) {
			e.preventDefault();
			$('.organic-main').removeClass('active');
			$(this).removeClass('active');
			
			return false;
		});
	});
	
	//parallax
    $(document).ready(function($) {
		if($('.bg-parallax').length >0){
			$('.bg-parallax').each(function(){
				$(this).parallax("50%",0.1);
			})  
		}	
	});
});

