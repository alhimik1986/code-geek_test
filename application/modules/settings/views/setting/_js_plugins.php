<?php
// Загружаю стандарные jquery-плагины
JSPlugins::$includePlugins = array(
	'jquery' => '',                          // - Подключить jquery-библиотеку
	'dataTables' => array(                   // Подключить модуль DataTables (поиск и сортировка в таблице)
		array(
			'selector' => '#urv-table',           // - css-селектор таблицы, к которой применить плагин
			'options'  => array(
				'aLengthMenu' => array(              // Варианты числа строк на странице
					array(7, 10, 25, 50, 100, -1), 
					array(7, 10, 25, 50, 100, Yii::t('settings.app', 'all'))
				),
				'iDisplayLength' => -1,                      // Число строк в странице по умолчанию (Все)
				'aaSorting' => array(),              // Не сортировать по умолчанию
			),
			'onload' => true,                                // Запустить при загрузке страницы
		),
	),
	'nicEdit' => array(                     // - Подключить простой html-редактор
		'id'         => '',                 // - id элемента, к которому применить редактор
		'buttonList' => array(               // - список кнопок в редакторе
			'bold','italic','underline','left','center','right','justify','ol','ul','subscript','superscript','strikethrough','removeformat','indent','outdent','fontSize','fontFamily','fontFormat',
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
	'searchEmployee' => array(),             // Поиск сотрудника
	//'mergeFiles'   => false,               // - Объединить все js- и css-файлов в один (по умолчанию выключен)
	//'compressFiles'=> false,               // - Сжимать js- и css-файлы перед слиянием (по умолчанию выключен)
);