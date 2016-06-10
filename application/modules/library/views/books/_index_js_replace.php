<script type="text/javascript">
$(document).ready(function(){
	// Перемещение из категорий в категорию
	new ajaxForm({
		csrf: {<?php echo Yii::app()->request->csrfTokenName . ":'" . Yii::app()->request->csrfToken."'"; ?>},
 		form: {
			selector: ''
		},
		create: {
			selector: '#replace-to-category',
			on: 'click',
			type: 'post',
			ajax: function(settings) {
				var ids = [];
				$('#urv-table tr[data_id]').each(function(){
					var data_id = $(this).attr('data_id');
					ids.push(data_id);
				});
				var sub_category_id = $('[name="sub_category_id"]').val();
				
				if ( ! window.confirm('Вы уверены, что хотите переместить '+ids.length+' книг в эту категорию?'))
					return false;
				if ( ids.length == 0 || ! sub_category_id)
					return false;
				
				return {
					url: '<?php echo $this->createUrl('replaceToCategory'); ?>',
					data: {
						ids: ids,
						sub_category_id: sub_category_id
					}
				};
			},
			success : function(data, settings) {
				window.notyMessage('success', 'Перемещение успешно!');
				$('#urv-table').trigger('search');
			},
			afterSuccess: function(settings) {},
			_afterSuccess: function(settings) {}
		}
	});
});
</script>