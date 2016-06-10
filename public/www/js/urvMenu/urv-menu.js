(function($){$(document).ready(function(){
	var isTouchDevice = 'ontouchstart' in document.documentElement; // Имеется ли в системе тачскрин
	//isTouchDevice = true;
	var isContextMenu = false;
	var interval = 50; // Интервал анимации fadIn-fadeOut
	var openMenu = function(dom){
		dom.parent().children('li').children('ul').stop(false,false).fadeOut(interval);
		dom.children('ul').stop(false,false).fadeIn(interval);
		dom.parent().children('li').children('a').removeClass('urv-menu-hovered');
		dom.children('a:not(.urv-menu-link)').addClass('urv-menu-hovered');
	};
	
	if ( ! isTouchDevice) {
		// Показывает подпункты меню
		$('.urv-menu>ul li').on('mouseenter', function(){
			openMenu($(this));
		});
		// Скрывает подпункты меню
		$('.urv-menu-wrapper').on('mouseleave', function(){
			if (isContextMenu) return;
			$(this).children('.urv-menu').children('ul').children('li').children('ul').stop(false,false).fadeOut(interval);
		});
	} else {
		// Если имеется тачскрин, то при клике на иконки отменяю переходы по ссылкам.
		$('.urv-menu li:has(ul) > a:not(.urv-menu-link)').on('click', function(){
			var isVisble = $(this).next().is(':visible');
			$(this).parent().parent().children('li').children('ul').hide();
			$(this).next().toggle( ! isVisble);
			return false;
		});
	}
	
	// Закрываю меню при клике в пустое место
	$(document).on('click', function(e){
		if ($(e.target).is('.urv-menu-level-2, .urv-menu-level-2 li, .urv-menu-level-3 li')) return;
		if (e.which == 1)
			$('.urv-menu-level-2').hide();
	});
	
	// Закрепляю меню к верху экрана
	$(document).on('click', '.urv-menu-icon-unsticked', function(){
		$(this).removeClass('urv-menu-icon-unsticked').addClass('urv-menu-icon-sticked').parents('.main-menu').css({position: 'fixed'});
		$('.urv-menu').css({'box-shadow': '0 6px 15px #000000'});
		$.cookie('urv_menu_sticked', 'true', {expired: 1000, path: '/'});

	});
	// Открепляю меню от верха экрана
	$(document).on('click', '.urv-menu-icon-sticked', function(){
		$(this).removeClass('urv-menu-icon-sticked').addClass('urv-menu-icon-unsticked').parents('.main-menu').css({position: 'absolute'});
		$('.urv-menu').css({'box-shadow': 'none'});
		$.cookie('urv_menu_sticked', 'false', {expired: 1000, path: '/'});
	});
	$(document).ready(function(){
		if ($.cookie('urv_menu_sticked') == 'false') {
			$('.urv-menu-icon-sticked').removeClass('urv-menu-icon-sticked').addClass('urv-menu-icon-unsticked').parents('.main-menu').css({position: 'absolute'});
			$('.urv-menu').css({'box-shadow': 'none'});
		} else if ($.cookie('urv_menu_sticked') == 'true') {
			$('.urv-menu-icon-sticked').removeClass('urv-menu-icon-unsticked').addClass('urv-menu-icon-sticked').parents('.main-menu').css({position: 'fixed'});
			$('.urv-menu').css({'box-shadow': '0 6px 15px #000000'});
		}
	});
	
	/*
	var slowInterval = 200; // Интервал анимации slideDown-slideUp
	
	// если ie7 и ниже
	if (navigator.userAgent.toLowerCase().indexOf('msie') != -1 && parseInt(navigator.userAgent.toLowerCase().split('msie')[1]) < 8) {
		$('.urv-menu-open').on('mouseenter', function(){
			$('.urv-menu-wrapper').stop(false,false).slideDown(slowInterval);
			$('.main-menu').css('height', '350px')
				.css('z-index', 2); // для ie7
		});
	
		$('.urv-menu-wrapper, .urv-menu-wrapper .urv-menu-level-3').on('mouseleave', function(e){
			if ($(e.target).parents('.urv-menu-wrapper').length > 0 && ! $(e.target).is('.urv-menu-level-3') && ! $(e.target).is('.urv-menu-level-2')) {
				return;
			} // для ie7
			$('.urv-menu-wrapper').stop(false,false).slideUp(slowInterval).css('height', 'auto');
			$('.main-menu').css('height', 'auto')
				.css('z-index', 'auto'); // для ie7
		});
		// Закрываю меню при клике
		$(document).on('click', function(e){
			$('.urv-menu-wrapper').stop(false,false).slideUp(slowInterval).css('height', 'auto');
			$('.main-menu').css('height', 'auto')
				.css('z-index', 'auto'); // для ie7
		});
	} else { // если современный браузер
		$('.urv-menu-open').on('mouseenter', function(){
			$('.urv-menu-wrapper').stop(false,false).slideDown(slowInterval);
			$('.main-menu').css('height', '350px');
		});
	
		$('.urv-menu-wrapper').on('mouseleave', function(e){
			if (isContextMenu) return;
			$('.urv-menu-wrapper').stop(false,false).slideUp(slowInterval).css('height', 'auto');
			$('.main-menu').css('height', 'auto');
		});
		
		// Закрываю меню при клике
		$(document).on('click', function(e){
			isContextMenu = (e.which == 3) ? true : false;
			if (e.which == 1) {
				$('.urv-menu-wrapper').trigger('mouseleave');
			}
		});
	}
	
	// Закрываю меню при клике на поиск сотрудника.
	$('.urv-menu-link, .search-employee').on('mouseenter', function(){
		$('.urv-menu-wrapper').stop(false,false).slideUp(slowInterval);
		$('.main-menu').css('height', 'auto');
	});
	*/
	
	/*
	$('.urv-menu-open').on('mouseenter', function(){
		$('.urv-menu-wrapper').stop(false,false).slideDown(slowInterval);
		$('.main-menu').css('height', '350px');
	});

	$('.urv-menu-wrapper').on('mouseleave', function(e){
		if (isContextMenu) return;
		$('.urv-menu-wrapper').stop(false,false).slideUp(slowInterval).css('height', 'auto');
		$('.main-menu').css('height', 'auto');
	});
	
	// Закрываю меню при клике
	$(document).on('click', function(e){
		isContextMenu = (e.which == 3) ? true : false;
		if (e.which == 1) {
			$('.urv-menu-wrapper').trigger('mouseleave');
		}
	});*/
});})(jQuery);