<?php // @param array $plugin_params Список плагинов, которые необходимо отобразить. ?><?php if (false): ?><script type="text/javascript"><?php endif; ?><?php
// **************************************************************************************************************
// Модуль: mailForm - Форма для отправки email-сообщений
// **************************************************************************************************************
if (Yii::app()->user->inGroup['ADMINS']) {
	Yii::import('application.modules.mail.models.MailForm');
	JsCssFiles::css('
		.mail-form-file-close {text-decoration:none; color:red; font-size:15px; padding-left:10px;}
		.mail-form-file-close:hover{font-weight:bold; color:red; font-size:17px; }
	');

	JSPlugins::setIfNotIsset($plugin_params, 'selector', '.mail-form');
	JSPlugins::setIfNotIsset($plugin_params, 'on', 'click');
	JSPlugins::setIfNotIsset($plugin_params, 'urlAttr', 'href');
	try {
		JSPlugins::setIfNotIsset($plugin_params, 'email_list', MailForm::getEmailList());
	} catch (CDbException $e) {
		JSPlugins::setIfNotIsset($plugin_params, 'email_list', array());
		Yii::app()->user->setFlash('error', Yii::t('mail.app', 'Unable to get a list of email-addresses.'));
	}
	JSPlugins::setIfNotIsset($plugin_params, 'email_list_name', 'tagit');
	JSPlugins::setIfNotIsset($plugin_params, 'nicEditSelector', 'nice-edit');
}
?>
<?php if (Yii::app()->user->inGroup['ADMINS']): ?>
window.urv.launch.push(function(){
	// Появление формы для отправки сообщения с возможностью прикрепить файлы
	new ajaxForm({
		csrf: {<?php echo Yii::app()->request->csrfTokenName . ":'" . Yii::app()->request->csrfToken."'"; ?>},
		form: {
			selector: '#mailForm-ajax-form'
		},
		create: {
			selector: '<?php echo $plugin_params['selector']; ?>',
			on: '<?php echo $plugin_params['on']; ?>',
			ajax: function(settings) {
				return {
					url: settings.create.dom.attr('<?php echo $plugin_params['urlAttr']; ?>')
				};
			},
			success: function(data, settings) {
				$(settings.form.selector).remove();
				<?php // В этой функции обязательно нужно вернуть jQuery-объект полученной формы для ее последующей обработки ?>
				return $(data).appendTo('body');
			},
			afterSuccess: function(settings) {
				var form = settings.form.dom;
				//chosen(form.find('.chosen'));
				window.urv.nicEdit('<?php echo $plugin_params['nicEditSelector']; ?>');
				window.urv.tagit('#tagit', <?php echo CJavaScript::encode($plugin_params['email_list']); ?>, <?php echo CJavaScript::encode($plugin_params['email_list_name']); ?>);
				// email-адреса по умолчанию
				if (settings.create.dom.attr('cc_emails')) {
					var cc_emails = settings.create.dom.attr('cc_emails').split("\n");
					var cc_labels = settings.create.dom.attr('cc_labels').split("\n");
					for(var key in cc_emails)
						$('#tagit').tagit('addTag', cc_labels[key]+' <'+cc_emails[key]+'>');
				}
				form.find('input[type="text"]').focus();
			}
		},
		submit: {
			selector: '.ajax-form-button-submit',
			ajax: function(settings) {
				var form = settings.submit.dom.parents('form');
				// Сохраняю контент текстового редактора nicEdit вручную, т.к. он не делает это автоматически
				window.urv.nicEditSaveContent('<?php echo $plugin_params['nicEditSelector']; ?>');
				
				// Отправляю форму без ajax, если в нее загружен файл
				var hasFile = false;
				form.find('input[type="file"]').each(function(){
					if ($(this).val().length != 0) {
						hasFile = true;
						return;
					}
				});
				
				// Запрещаю отправлять сообщение через ajax, если в прикреплен файл, данные отправляются только для валидации
				var validate = hasFile ? '1' : '0';
				form.find('[name="MailForm[validate]"]').val(validate);
				
				var success = function(){};
				if (hasFile) {
					success = function(data, settings) {
						var form = settings.submit.dom.parents('form');
						
						// Отправляю форму в отдельном потоке, чтобы выполнить одновременно и выход и отправку формы
						setTimeout(function(){
							// Разрешаю отправлять сообщение
							form.find('[name="MailForm[validate]"]').val('0');
							form.submit();
						}, 10);
					};
				} else {
					success = function(data, settings) {
						notyMessage('success', '<?php echo Yii::t('mail.app', 'The message successfully sent!'); ?>');
						$(settings.form.selector).remove(); // Закрываю форму только в случае успеха
					};
				}
				
				return {
					url: form.attr('action'),
					data: form.serializeArray(),
					success: success
				};
			}
		}
	});
	
	// Появление формы для отправки отчетов по email
	new ajaxForm({
		loadingDom: function(settings){return '.urv-table:first'},
		csrf: {<?php echo Yii::app()->request->csrfTokenName . ":'" . Yii::app()->request->csrfToken."'"; ?>},
		form: {
			selector: '#report-mailing-ajax-form'
		},
		create: {
			selector: '.send-by-mail-form',
			on: 'click',
			ajax: function(settings) {
				return {
					url: settings.create.dom.attr('href')
				};
			},
			success: function(data, settings) {
				$(settings.form.selector).remove();
				// В этой функции обязательно нужно вернуть jQuery-объект полученной формы для ее последующей обработки
				return $(data).appendTo('body');
			},
			afterSuccess: function(settings) {
				var form = settings.form.dom;
				var tagit_selector = '#tagit-report';
				//chosen(form.find('.chosen'));
				window.urv.nicEdit('MailForm_text11');
				window.urv.tagit(tagit_selector, <?php echo CJavaScript::encode($plugin_params['email_list']); ?>, 'MailingReport[emails][]');
				// email-адреса по умолчанию
				if (settings.create.dom.attr('cc_emails')) {
					var cc_emails = settings.create.dom.attr('cc_emails').split("\n");
					var cc_labels = settings.create.dom.attr('cc_labels').split("\n");
					for(var key in cc_emails)
						$(tagit_selector).tagit('addTag', cc_labels[key]+' <'+cc_emails[key]+'>');
				}
				form.find('input[type="text"]').focus();
			}
		},
		submit: {
			selector: '.ajax-form-button-submit',
			ajax: function(settings) {
				var form = settings.submit.dom.parents('form');
				// Сохраняю контент текстового редактора nicEdit вручную, т.к. он не делает это автоматически.
				window.urv.nicEditSaveContent('MailForm_text11');
				
				// Отправляю форму без ajax, если в нее загружен файл
				var hasFile = false;
				form.find('input[type="file"]').each(function(){
					if ($(this).val().length != 0) {
						hasFile = true;
						return;
					}
				});
				
				// Запрещаю отправлять сообщение через ajax, если в прикреплен файл, данные отправляются только для валидации
				var validate = hasFile ? '1' : '0';
				form.find('[name="MailForm[validate]"]').val(validate);
				
				var success = function(){};
				if (hasFile) {
					success = function(data, settings) {
						var form = settings.submit.dom.parents('form');
						
						// Отправляю форму в отдельном потоке, чтобы выполнить одновременно и выход и отправку формы
						setTimeout(function(){
							// Разрешаю отправлять сообщение
							form.find('[name="MailForm[validate]"]').val('0');
							form.submit();
						}, 10);
					};
				} else {
					success = function(data, settings) {
						notyMessage('success', '<?php echo Yii::t('mail.app', 'The message successfully sent!'); ?>');
						$(settings.form.selector).remove(); // Закрываю форму только в случае успеха
					};
				}
				
				return {
					url: form.attr('action'),
					data: form.serializeArray(),
					success: success
				};
			}
		}
	});
});
<?php endIf; ?>