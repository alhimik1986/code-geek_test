<?php
// Загружаю стандарные jquery-плагины
JSPlugins::$includePlugins = array(
	'jquery' => '',                          // - Подключить jquery-библиотеку
	'chosen' => array(                       // - Подключить модуль: выпадающий список с поиском choosen.jquery.js
		'selector' => '.chosen',                // - css-селектор элементов, к которым будет применен данный плагин (по умолчанию стоит '.choosen')
	),
	'nicEdit' => array(                     // - Подключить простой html-редактор
		'id'         => '',                 // - id элемента, к которому применить редактор
		'buttonList' => array(               // - список кнопок в редакторе
			'bold','italic','underline','left','center','right','justify','ol','ul','subscript','superscript','strikethrough','removeformat','indent','outdent'
		),
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
	)),
	'searchEmployee' => array(),             // Поиск сотрудника
	//'compressor' => true,                    // - Включить компрессор js- и css-файлов (по умолчанию включен)
	//'compressCode'=> true,                   // - Сжимать js- и css-файлы (по умолчанию выключен)
);
?>
<?php $className = $model::className(); $textarea_id = $model::className().'_comment'; ?>
<script type="text/javascript">
$(document).ready(function(){
	<?php // Форма редактирования ?>
	new ajaxForm({
		csrf: {<?php echo Yii::app()->request->csrfTokenName . ":'" . Yii::app()->request->csrfToken."'"; ?>},
 		form: {
			selector: '#<?php echo lcfirst($model::className()); ?>-ajax-form'
		},
		create: {
			delegator: '#urv-table',
			selector: 'tr[data_id]',
			on: 'click',
			ajax: function(settings) {
				return {
					url: '<?php echo $this->createUrl('confirmationForm'); ?>',
					data: {id: settings.create.dom.attr('data_id')}
				};
			},
			success: function(data, settings) {
				$(settings.form.selector).remove();
				<?php // В этой функции обязательно нужно вернуть jQuery-объект полученной формы для ее последующей обработки ?>
				return $(data).appendTo('body');
			},
			afterSuccess: function(settings) {
				var form = settings.form.dom;
				window.urv.chosen(form.find('.chosen'));
				window.urv.nicEdit('<?php echo $textarea_id; ?>');
				form.find('#<?php echo $model::className(); ?>_username').focus();
			}
		},
		submit: {
			selector: '.ajax-form-button-submit, .ajax-form-button-delete',
			ajax: function(settings) {
				var form = settings.submit.dom.parents('form');
				if ( settings.submit.dom.hasClass('ajax-form-button-delete')) {
					if ( ! confirm('<?php echo Yii::t('app', 'Remove permanently the record?'); ?>'))
						return false;
				} else if (form.find('.ajax-form-button-delete').length == 0 && form.find(':checkbox[name="<?php echo $model::className(); ?>[removed]"]').is(':checked')) {
					if ( ! confirm('<?php echo Yii::t('app', 'Mark the record as removed?'); ?>'))
						return false;
				}
				
				window.urv.nicEditSaveContent('<?php echo $textarea_id; ?>');
				
				var url = form.attr('action');
				var data = form.serializeArray();
				if ( settings.submit.dom.hasClass('ajax-form-button-delete')) {
					url = '<?php echo $this->createUrl('delete'); ?>';
					data = {
						<?php echo $model::className(); ?>: {
							<?php echo $model::getPkColumnName(); ?>: settings.create.dom.attr('data_id')
						}
					};
				}
				return {
					url: url,
					data: data
				};
			}
		},
		afterSubmit: {
			ajax: function(settings) {
				$(settings.form.selector).remove(); // Закрываю форму только после удачной записи и обновлении таблицы
				$('#urv-table').trigger('search');
				return false;
			},
			success: function(data, settings) {}
		}
	});
	
	$('[name="<?php echo $className; ?>[all_text]"]').focus();
	$(document).on('submit', '#<?php echo lcfirst($model::className()); ?>-ajax-form', function(e){return false;});
});
</script>