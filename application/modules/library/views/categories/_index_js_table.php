<?php $className = $model::className(); $textarea_id = $model::className().'_note'; ?>
<script type="text/javascript">
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
				
				/*form.find('.ajax-chosen').ajaxChosen({
					type: 'GET',
					url: '<?php echo $this->createUrl('//employees/search/forDropDownList'); ?>',
					dataType: 'json',
					lookingForMsg: '<?php echo Yii::t('app', 'Search'); ?>...',
					minTermLength: 1,
					afterTypeDelay: 500,
					jsonTermKey: 'search'
				}, function (data) {
					//$('#<?php echo $className; ?>_employee_id option').remove();
					//$('#<?php echo $className; ?>_employee_id').trigger('liszt:updated');
					var results = [];
					
					$.each(data, function (i, val) {
						results.push({ value: val.value, text: val.text });
					});
					
					return results;
				});*/
				
				window.urv.nicEdit('<?php echo $textarea_id; ?>');
				form.find('input[type="text"]:first').focus();
			}
		},
		submit: {
			selector: '.ajax-form-button-submit',
			ajax: function(settings) {
				var form = settings.submit.dom.parents('form');
				window.urv.nicEditSaveContent('<?php echo $textarea_id; ?>');
				return {
					url: form.attr('action'),
					data: form.serializeArray()
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
				
				/*form.find('.ajax-chosen').ajaxChosen({
					type: 'GET',
					url: '<?php echo $this->createUrl('//employees/search/forDropDownList'); ?>',
					dataType: 'json',
					lookingForMsg: '<?php echo Yii::t('app', 'Search'); ?>...',
					minTermLength: 1,
					afterTypeDelay: 500,
					jsonTermKey: 'search'
				}, function (data) {
					//$('#<?php echo $className; ?>_employee_id option').remove();
					//$('#<?php echo $className; ?>_employee_id').trigger('liszt:updated');
					var results = [];
					
					$.each(data, function (i, val) {
						results.push({ value: val.value, text: val.text });
					});
					
					return results;
				});*/
				
				window.urv.nicEdit('<?php echo $textarea_id; ?>');
				form.find('input[type="text"]:first').focus();
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