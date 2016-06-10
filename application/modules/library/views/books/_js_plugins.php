<?php
// Загружаю стандарные jquery-плагины
JSPlugins::$includePlugins = array(
	'jquery' => '',                          // - Подключить jquery-библиотеку
	'chosen' => array(                       // - Подключить модуль: выпадающий список с поиском choosen.jquery.js
		'selector' => '.chosen',                // - css-селектор элементов, к которым будет применен данный плагин (по умолчанию стоит '.choosen')
	),
	'ajaxChosen'=>true,                      // - Подключить ajaxChosen - плагин для chosen, чтобы вести ajax-поиск на сервере
	'placeholder' => array(                  // - Подключить placeholder (надпись внутри текстового поля) для старого Internet Explorer
		'selector'=>'input[type="text"], textarea',  // - css-селектор, к которому применить плагин
		'color'=>'#aaa',                         // - цвет надписи
	),
	'toggleParentNext' => array(             // - Показать-скрыть при клике (для кнопок, которые при клике показывают-скрывают содержимое)
		'selector' => '.toggle-parent-next',     // - css-селектор элементов, к которым будет применено (по умолчанию стоит '.toggle-parent-next')
		'focus'    => '[type=text]:first',       // - css-селектор элемента на который перевести фокус по завершении анимации (когда элемента станет видимым)
	),
	'nicEdit' => array(                     // - Подключить простой html-редактор
		'id'         => '',                 // - id элемента, к которому применить редактор
		'buttonList' => array(               // - список кнопок в редакторе
			'bold','italic','underline','left','center','right','justify','ol','ul','subscript','superscript','strikethrough','removeformat','indent','outdent'
		),
		'onload' => false,                   // - Запустить при загрузке страницы
	),
	'ajaxForm' => '',                        // - Подключить стандартную форму для УРВ
	'ajaxTable' => array(array(                                      // Подключить вспомогательные скрипты для поиска в ajax-таблице
		'tbl_selector' => '#urv-table',                              // Селектор ajax-таблицы для поиска
		'search_url' => Yii::app()->controller->createUrl('search'), // url-адрес, куда отправлять искомые данные
		'search_request_type' => 'post',                                // Тип запроса при поиске в таблице
		'search_on_change_selector' => '.search-on-change',          // Селектор элементов, при изменении или нажатии клавиш которых осуществлять поиск
		'search_on_change_dateSelector' => '.from-date, .to-date',   // Селектор элементов, при изменении которых осуществлять поиск
	)),
);