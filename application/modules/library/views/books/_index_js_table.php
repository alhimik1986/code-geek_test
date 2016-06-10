<?php $className = $model::className(); $textarea_id = $model::className().'_description'; ?>
<script type="text/javascript">
// Действия после успешного запроса
var afterSubmit = {
	ajax: function(settings) {
		// Не обновляю строку, если отправляется файл
		var hasFile = false;
		settings.submit.dom.parents('form').find('input[type="file"]').each(function(){
			if ($(this).val().length != 0) {
				hasFile = true;
				return;
			}
		});
		if (hasFile)
			return false;
		
		$(settings.form.selector).remove(); // Закрываю форму только после удачной записи и обновлении таблицы
		$('#urv-table').trigger('search');
		return false;
	},
	success: function(data, settings) {}
};
// Предварительная ajax-валидация данных перед тем, как отправлять файл
window.validateFile = function($form, $_return){
	// Отправляю форму без ajax, если в нее загружен файл
	var hasFile = false;
	$form.find('input[type="file"]').each(function(){
		if ($(this).val().length != 0) {
			hasFile = true;
			return;
		}
	});
	// Запрещаю сохранять запись
	var validate = hasFile ? '1' : '0';
	$form.find('[name="<?php echo $model::className(); ?>[validate]"]').val(validate);
	// Функция в случае успешной отправки
	if ( hasFile ) {
		$_return['success'] = function(data, settings) {
			var $form = settings.submit.dom.parents('form');
			// Отправляю форму в отдельном потоке, чтобы выполнить одновременно и выход и отправку формы
			setTimeout(function(){
				// Разрешаю сохранять запись
				$form.find('[name="<?php echo $model::className(); ?>[validate]"]').val('0');
				$form.submit();
			}, 10);
		};
		$_return['afterSuccess'] = function(){};
		$_return['_afterSuccess'] = function(){};
	}
	return $_return;
};
// Закрыть форму и обновить таблицу
window.remove_form_and_search = function(){
	$('#<?php echo lcfirst($model::className()); ?>-ajax-form').remove();
	$('#urv-table').trigger('search');
};

$(document).ready(function(){
	// Форма создания
	new ajaxForm({
		csrf: {<?php echo Yii::app()->request->csrfTokenName . ":'" . Yii::app()->request->csrfToken."'"; ?>},
 		form: {
			selector: '#<?php echo lcfirst($model::className()); ?>-ajax-form'
		},
		create: {
			selector: '.ajax-form-button-create',
			on: 'click',
			ajax: function(settings) {
				return {
					url: '<?php echo $this->createUrl('form'); ?>'
				};
			},
			success : function(data, settings) {
				$(settings.form.selector).remove();
				// В этой функции обязательно нужно вернуть jQuery-объект полученной формы для ее последующей обработки
				return $($(data)).appendTo('body');
			},
			afterSuccess: function(settings) {
				var form = settings.form.dom;
				window.urv.chosen(form.find('.chosen'));
				
				window.urv.nicEdit('<?php echo $textarea_id; ?>');
				form.find('input[type="text"]:first').focus();
			}
		},
		submit: {
			selector: '.ajax-form-button-submit',
			ajax: function(settings) {
				var form = settings.submit.dom.parents('form');
				var $_return = window.validateFile(form, {});
				window.urv.nicEditSaveContent('<?php echo $textarea_id; ?>');
				
				$_return['url'] = form.attr('action');
				$_return['data'] = form.serializeArray();
				return $_return;
			}
		},
		afterSubmit: afterSubmit
	});
	// Форма редактирования
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
					url: '<?php echo $this->createUrl('form'); ?>',
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
				form.find('input[type="text"]:first').focus();
			}
		},
		submit: {
			selector: '.ajax-form-button-submit, .ajax-form-button-delete',
			ajax: function(settings) {
				var form = settings.submit.dom.parents('form');
				var $_return = window.validateFile(form, {});
				
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
				
				$_return['url'] = url;
				$_return['data'] = data;
				return $_return;
			}
		},
		afterSubmit: afterSubmit
	});
	
	$('[name="<?php echo $className; ?>[all_text]"]').focus();
	//$(document).on('submit', '#<?php echo lcfirst($model::className()); ?>-ajax-form', function(e){return false;});
	/*$(document).on('submit', '#<?php echo lcfirst($model::className()); ?>-ajax-form', function(){
		$('#<?php echo lcfirst($model::className()); ?>-ajax-form').remove();
		$('#urv-table').trigger('search');
		return true;
	});*/
});
</script>