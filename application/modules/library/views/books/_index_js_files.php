<script type="text/javascript">
$(document).ready(function(){
	/**
	 * Добавляет файловое поле при добавлении файла.
	 */
	var add_file_field_on_change = function($this, parent_tag, close_button_class) {
		var hasEmptyFileField=false;
		var $parent = $this.parents(parent_tag);
		$parent.find('input[type="file"]').each(function(){
			if ($(this).val().length == 0) {
				hasEmptyFileField = true;
				return false;
			} else {
				if ($(this).parent().find('.'+close_button_class).length == 0) {
					$(this).parent().append(
						'<a href=\"#\" onclick=\"jQuery(this).parent().remove(); return false;\" class=\"'+close_button_class+'\" style=\"color:red;font-size:15px;padding-left:10px;text-decoration:none;\">x</a>'
					);
				}
			}
		});
		if ( ! hasEmptyFileField) {
			$parent.append('<div>'+$this.removeAttr('id')[0].outerHTML+'</div>');
		}
	};
	
	var parent_tag = 'td', close_button_class = 'file-close-button';
	$(document).on('change', '.add-file-field-on-change', function(){
		add_file_field_on_change($(this), parent_tag, close_button_class);
	});
	
	
	// Удаление файлов из формы
	new ajaxForm({
		csrf: {<?php echo Yii::app()->request->csrfTokenName . ":'" . Yii::app()->request->csrfToken."'"; ?>},
		create: {
			selector: '.<?php echo $model::className(); ?>-file-delete',
			on: 'click',
			ajax: function(settings) {
				if ( ! window.confirm('<?php echo Yii::t('app', 'Remove file?'); ?>'))
					return false;
				
				return {
					url: settings.create.dom.attr('href'),
					data: {
						<?php echo $model::className(); ?>: {
							id: settings.create.dom.attr('data_id'),
							book_id: settings.create.dom.attr('book_id')
						}
					},
					type: 'post'
				};
			},
			success: function(data, settings) {
				settings.create.dom.parents('form').find('#<?php echo $model::className(); ?>-files').html(data);
			},
			_afterSuccess: function(settings) {},
			afterSuccess: function(settings) {}
		},
		submit: {
			ajax: function(settings) {return false;}
		},
		afterSubmit: {}
	});
});
</script>