<?php

/**
 * Класс для подключения уже настроенных javascript-плагинов. Он удобен тем, что с минимумом кода можно подключить только нужные плагины.
 *
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
 * @package application.components
 * @depends URV/Models/EmployeesModel.php
 */

Yii::import('application.modules.js_plugins.components.JsCssFiles'); // Для yii-doc (документатора)
class JSPlugins extends JsCssFiles
{
	protected static $plugins = array();
	public static function setIfNotIsset(&$plugin, $key, $defaultValue)
	{
		$plugin[$key] = isset($plugin[$key]) ? $plugin[$key] : $defaultValue;
	}
	public static $includePlugins = array();
	
	/**
	 * Подключить заданные javascript плагины.
	 * 
	 * @param array $plugins - ассоциативный массив, у которого в ключе задано название плагина, в значении - параметры плагина
	 *
	 * Таким образом можно подключить следующие плагины с задаными параметрами (образец, просто копируйте эти строки и удаляйте ненужное):
	 * 
	JSPlugins::includePlugins( array(
		'jquery' => '',                          // - Подключить jquery-библиотеку
		'slickNav' => array(                     // - Подключить меню для мобильных приложений
			'selector' => '#mobile-menu',
			'options' => array(),
		),
		'chosen' => array(                       // - Подключить модуль: выпадающий список с поиском choosen.jquery.js
			'selector' => '.choosen',                // - css-селектор элементов, к которым будет применен данный плагин (по умолчанию стоит '.choosen')
		),
		'ajaxChosen'=>true,                      // - Подключить ajaxChosen - плагин для chosen, чтобы вести ajax-поиск на сервере
		'tablefilter' => array(                  // - Подключить модуль для поиска в таблице picnet.table.filter
			'selector'    => '#table-filter',        // - css-селектор элементов (таблиц), к которым применить плагин, в селекторе обязательно должен быть указан id, т.е. начинаться с '#' (по умолчанию '#table-filter')
			'showFilters' => false,                  // - Показывать фильтры в шапке таблицы (на каждой колонке)
			'search'      => '#quickfind',           // - css-селектор текстовой строки, в которой вести поиск, в селекторе обязательно должен быть указан id, т.е. начинаться с '#' (по умолчанию '#quickfind')
			'clear'       => '#cleanfilters',        // - css-селектор кнопки, которая сбрасывает поиск (очищает текстовую строку поиска), в селекторе обязательно должен быть указан id, т.е. начинаться с '#' (по умолчанию '#cleanfilters')
		),
		'tablesorter' => array(                  // - Подключить модуль для сортировки таблицы jquery.tablesorter
			'selector'    => '#tablesorter',         // - css-селектор таблицы, к которой применить плагин
			'html'        => false,                  // - сортировать, учитывая html-теги
			'tablefilter' => array(                  // - Подключить модуль для поиска в таблице
				'columnFilters' => 'false',              // - Показывать фильтры в шапке таблицы (на каждой колонке)
				'search'        => '.search',            // - css-селектор текстовой строки, в которой вести поиск, в селекторе обязательно должен быть указан id, т.е. начинаться с '#' (по умолчанию '#quickfind')
				'reset'         => '.reset',             // - css-селектор кнопки, которая сбрасывает поиск (очищает текстовую строку поиска), в селекторе обязательно должен быть указан id, т.е. начинаться с '#' (по умолчанию '#cleanfilters')
				'delay'         => '100',                // - Задержка перед поиском после отпускания клавиши (мс)
			),
		),
		'dataTables' => array(                   // Подключить модуль DataTables (поиск и сортировка в таблице)
			'selector' => '#datatables',             // - css-селектор таблицы, к которой применить плагин
			'options'  => array(
				'aLengthMenu' => array(              // Варианты числа строк на странице
					array(7, 10, 25, 50, 100, -1), 
					array(7, 10, 25, 50, 100, 'Все')
				),
				'iDisplayLength' => -1,                      // - Число строк в странице по умолчанию (Все)
				'tooltip' => array(                          // - Всплывающие подсказки в таблице
					'selector' => 'tr',                          // - css-селектор внутри таблицы, к которому применить плагин
					'options'  => array(                         // - параметры по умолчанию, вместо которых можно задать свои
						'delay' => 0,                            // - задержка исчезновения подсказки после того, как убрать курсор с элемента
						'track' => 'true',                       // - подсказка возле курсора
						'fade'  => 250,                          // - время исчезновения подсказки
					),
				),
			),
			'fixedTableHeader' => true,                      // Прилипание шапки таблицы к верху экрана (по умолчанию: включен)
			'onload' => true,                                // - Запустить при загрузке страницы
		),
		'fixedTableHeader' => array(                  // Прилипание шапки таблицы
			array(
				'selector' => '#fixed-table-header', 
			),
		),
		'tooltip' => array(                         // - Всплывающие подсказки
			array(
				'selector' => '.tooltip',          // - css-селектор, к которому применить плагин
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
		'datepick' => array(                     // - Подключить календарь jquery.Datepick от сайта http://keith-wood.name/datepick.html
			array(
				'selector' => '.from-date',              // - css-селектор текстового поля, к которому применить плагин
				'options'=> array(                       // - Опции, которые нужно применить для этого селектора
					'dateFormat'  => 'dd-mm-yyyy',           // - формат даты
					'defaultDate' => '',                     // - дата по умолчанию
					'minDate'     => '',                     // - минимальная разрешенная дата
					'maxDate'     => '',                     // - максимальная разрешенная дата
					'onSelect'    => '',                     // - javascript-функция при выборе даты
				),
			),
			array(
				'selector' => '.to-date',              // - css-селектор текстового поля, к которому применить плагин
				'options'=> array(                       // - Опции, которые нужно применить для этого селектора
					'dateFormat'  => 'dd-mm-yyyy',           // - формат даты
					'defaultDate' => '',                     // - дата по умолчанию
					'minDate'     => '',                     // - минимальная разрешенная дата
					'maxDate'     => '',                     // - максимальная разрешенная дата
					'onSelect'    => '',                     // - javascript-функция при выборе даты
				),
			),
		),
		'timeentry' => array(                    // - Подключить плагин, который позволяет вводить время в текстовое поле
			array(
				'selector' => '.from-time',               // - css-селектор текстового поля, к которому применить плагин
				'options'=> array(                       // - Опции, которые нужно применить для этого селектора
					'defaultTime'  => '09:00',               // - время по умолчанию
					'timeSteps'    => array(-1, -1, 0),      // - шаг прокрутки, соответственно, часов, минут, секунд
					'show24Hours'  => true,                  // - 24-часовой формат времени
					'spinnerImage' => '',                    // - рисунок прокрутки (скрыт по умолчанию)
					'scrollOnFocus'=> true,                  // - прокручивать время колесом мыши только на текстовом поле, на котором установлен курсор
				),
			),
			array(
				'selector' => '.to-time',               // - css-селектор текстового поля, к которому применить плагин
				'options'=> array(                       // - Опции, которые нужно применить для этого селектора
					'defaultTime'  => '18:00',               // - время по умолчанию
					'timeSteps'    => array(-1, -1, 0),      // - шаг прокрутки, соответственно, часов, минут, секунд
					'show24Hours'  => true,                  // - 24-часовой формат времени
					'spinnerImage' => '',                    // - рисунок прокрутки (скрыт по умолчанию)
					'scrollOnFocus'=> true,                  // - прокручивать время колесом мыши только на текстовом поле, на котором установлен курсор
				),
			),
		),
		'placeholder' => array(                  // - Подключить placeholder (надпись внутри текстового поля) для старого Internet Explorer
			'selector'=>'input, textarea',           // - css-селектор, к которому применить плагин
			'color'=>'#aaa',                         // - цвет надписи
		),
		'nicEdit' => array(                      // - Подключить простой html-редактор
			'id'         => 'nice-edit',         // - id элемента, к которому применить редактор
			'buttonList' => array(               // - список кнопок в редакторе
				'bold','italic','underline','left','center','right','justify','ol','ul','subscript','superscript','strikethrough','removeformat','indent','outdent'
			),
			'onload' => false,                   // - Запустить при загрузке страницы
		),
		'tagit' => '',                           // - Подключить инструмент для выбора адресатов почты
		'ajaxForm' => '',                        // - Подключить стандартную УРВ-форму
		'mailForm' => array(                     // - Подключить форму для отправки email-сообщений
			'selector' => '.mail-form',          // - css-селектор, при клике на который вызывать появление формы
			'on'       => 'click',               // - событие, при котором вызывать появление формы
			'urlAttr'  => 'href',                // - аттрибут, в котором искать url-куда отправить данные с формы.
			'email_list' => array(),             // - список адресатов для выбора: кому отправить
			'email_list_name' => 'tagit',        // - Имя поля, где будут храниться выбранные адресаты
			'nicEditSelector' => 'nice-edit',    // - id текстовой области (textarea), к которой подключить nicEdit в форме отправки сообщения
		),
		'ajaxTable' => array(array(                                      // Подключить вспомогательные скрипты для поиска в ajax-таблице
			'tbl_selector' => '#urv-table',                              // Селектор ajax-таблицы для поиска
			'search_url' => Yii::app()->controller->createUrl('search'), // url-адрес, куда отправлять искомые данные
			'search_request_type' => 'post',                                // Тип запроса при поиске в таблице
			'search_on_change_selector' => '.search-on-change',          // Селектор элементов, при изменении или нажатии клавиш которых осуществлять поиск
			'search_on_change_dateSelector' => '.from-date, .to-date',   // Селектор элементов, при изменении которых осуществлять поиск
			'tooltip_selector' => '',                                    // Селектор для всплывающей подсказки при обновлении ajax-таблицы
			'ajaxDataCallBack' => 'js:function(data){return data;}',     // Пост-обработка данных для поиска перед отправкой ajax-запроса (например, чтобы втиснуть в поиск диапазон дат from_date и to_date)
			'afterSuccessCallBack' => 'js:function(data){}',             // Дополнительные операции при успешном запросе (в поиске)
		)),
		'searchEmployee' => array(),             // Поиск сотрудника
		'mergeFiles'   => false,                 // - Объединить все js- и css-файлов в один (по умолчанию выключен)
		'compressFiles'=> false,                 // - Сжимать js- и css-файлы перед слиянием (по умолчанию выключен)
	));
	 * 
	 */
	public static function includePlugins($plugins)
	{
		$baseUrl = Yii::app()->baseUrl;

		// Включить-выключить слияние и сжатие js- и css-файлов
		self::$_mergeFiles    = isset($plugins['mergeFiles'])    ? $plugins['mergeFiles']    : Yii::app()->settings->param['JsCssFiles']['merge'];
		self::$_compressFiles = isset($plugins['compressFiles']) ? $plugins['compressFiles'] : Yii::app()->settings->param['JsCssFiles']['compress'];
		
		
		// Рендерю настраиваемые js-плагины + применяю кэширование
		if ($plugins) {
			ob_start();
			include Yii::getPathOfAlias('application.modules.js_plugins.views.jsPlugins').DIRECTORY_SEPARATOR.'jsPlugins.php';
			self::js(ob_get_clean());
		}
	}
}