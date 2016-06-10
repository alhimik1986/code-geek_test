<script type="text/javascript">
$(document).ready(function(){
	<?php // Вставляю кнопку "Добавить" внутрь обертки таблицы ?>
	/*afterLaunch.push(function(){
		$('#urv-table_wrapper .ColVis').prepend('<button class="ajax-form-create ajax-form-button-create" style="position:absolute;left:0;top:0;"><i class="icon-button-create"></i>Добавить</button>');
	});*/
	
	<?php // Форма редактирования ?>
	new ajaxForm({
		form: {
			selector: '#profile-form'
		},
		initForm: {
			selector: '#profile-form'
		},
		submit: {
			selector: 'input[type="submit"]',
			ajax: function(settings) {
				var form = settings.submit.dom.parents('form');
				return {
					url: form.attr('action'),
					data: form.serializeArray()
				};
			},
			success: function(data, settings) {
				window.location.reload();
			}
		}
	});
});
</script>