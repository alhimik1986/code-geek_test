<?php
// Загружаю стандарные jquery-плагины
JSPlugins::$includePlugins = array(
	'jquery' => '',                          // - Подключить jquery-библиотеку
	'chosen' => array(                       // - Подключить модуль: выпадающий список с поиском choosen.jquery.js
		'selector' => '.chosen',                // - css-селектор элементов, к которым будет применен данный плагин (по умолчанию стоит '.choosen')
	),/*
	'tablesorter' => array(                  // - Подключить модуль для сортировки таблицы jquery.tablesorter
		'selector'    => '#urv-table',           // - css-селектор таблицы, к которой применить плагин
		'html'        => false,                  // - сортировать, учитывая html-теги
		'tablefilter' => array(                  // - Подключить модуль для поиска в таблице
			'columnFilters' => 'false',              // - Показывать фильтры в шапке таблицы (на каждой колонке)
			'search'        => '#search',            // - css-селектор текстовой строки, в которой вести поиск, в селекторе обязательно должен быть указан id, т.е. начинаться с '#' (по умолчанию '#quickfind')
			'reset'         => '#reset',             // - css-селектор кнопки, которая сбрасывает поиск (очищает текстовую строку поиска), в селекторе обязательно должен быть указан id, т.е. начинаться с '#' (по умолчанию '#cleanfilters')
			'delay'         => '300',                // - Задержка перед поиском после отпускания клавиши (мс)
		),
	),*/
	/*'dataTables' => array(                   // Подключить модуль DataTables (поиск и сортировка в таблице)
		array(
			'selector' => '#urv-table',           // - css-селектор таблицы, к которой применить плагин
			'options' => array(
				'aLengthMenu' => array(              // Варианты числа строк на странице
					array(7, 10, 25, 50, 100, -1), 
					array(7, 10, 25, 50, 100, 'Все')
				),
				'iDisplayLength' => 7,                       // Число строк в странице по умолчанию (Все)
				'tooltip' => array(                          // - Всплывающие подсказки в таблице
					'selector' => 'tr',                          // - css-селектор внутри таблицы, к которому применить плагин
				),
			),
			'onload' => false,                               // Запустить при загрузке страницы
		),
	),*/
	'fixedTableHeader' => array(                  // Прилипание шапки таблицы
		array(
			'selector' => '#fixed-table-header', 
		),
	),
	'tooltip' => array(                         // - Всплывающие подсказки
		array(
			'selector' => '#urv-table tbody tr',        // - css-селектор, к которому применить плагин
			'options' => array(                              // - параметры по умолчанию, вместо которых можно задать свои
				'delay' => 0,                                // - задержка исчезновения подсказки после того, как убрать курсор с элемента
				'track' => true,                             // - подсказка возле курсора
				'fade'  => 250,                              // - время исчезновения подсказки
			),
			'onload' => false,                          // - Запустить при загрузке страницы
		),
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
	'tagit' => '',                           // - Подключить инструмент для выбора адресатов почты
	'ajaxForm' => '',                        // - Подключить стандартную форму для УРВ
	'mailForm' => array(                     // - Подключить форму для отправки email-сообщений
		'selector' => '.mail-form',          // - css-селектор, при клике на который вызывать появление формы
		'on'       => 'click',               // - событие, при котором вызывать появление формы
		'urlAttr'  => 'href',                // - аттрибут, в котором искать url-куда отправить данные с формы.
		//'email_list' => MailForm::getEmailList(),  // - список адресатов для выбора: кому отправить
		'email_list_name' => 'MailForm[emails][]', // - Имя поля, где будут храниться выбранные адресаты
		'nicEditSelector' => 'MailForm_text',      // - id текстовой области (textarea), к которой подключить nicEdit в форме отправки сообщения
	),
	'ajaxTable' => array(array(                                      // Подключить вспомогательные скрипты для поиска в ajax-таблице
		'tbl_selector' => '#urv-table',                              // Селектор ajax-таблицы для поиска
		'search_url' => Yii::app()->controller->createUrl('search'), // url-адрес, куда отправлять искомые данные
		'search_request_type' => 'post',                                // Тип запроса при поиске в таблице
		'search_on_change_selector' => '.search-on-change',          // Селектор элементов, при изменении или нажатии клавиш которых осуществлять поиск
		'search_on_change_dateSelector' => '.from-date, .to-date',   // Селектор элементов, при изменении которых осуществлять поиск
		'tooltip_selector' => '#urv-table tbody tr',                 // Селектор для всплывающей подсказки при обновлении ajax-таблицы
	)),
	'searchEmployee' => array(),             // Поиск сотрудника
	//'mergeFiles'   => false,               // - Объединить все js- и css-файлов в один (по умолчанию выключен)
	//'compressFiles'=> false,               // - Сжимать js- и css-файлы перед слиянием (по умолчанию выключен)
);