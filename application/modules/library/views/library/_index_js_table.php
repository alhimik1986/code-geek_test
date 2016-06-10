<?php $className = $model::className(); $textarea_id = $model::className().'_description'; ?>
<script type="text/javascript">
$(document).ready(function(){
	// Просмотр книги
	new ajaxForm({
		create: {
			delegator: '#urv-table',
			selector: 'tr[data_id]',
			on: 'click',
			ajax: function(settings) {
				return {
					url: '<?php echo $this->createUrl('view'); ?>',
					data: {id: settings.create.dom.attr('data_id')}
				};
			},
			success : function(data, settings) {
				var height = $(window).height();
				$('.library-wrapper').hide();
				$('.view-content-wrapper').html(data);
				$('.view-content-wrapper')
					.show()
					.css({'position': 'absolute', 'height': '0'})
					.stop(true, true)
					.animate({height: height}, 300, function(){
						$(this).css({'min-height': height, 'height': 'auto', 'position': 'static'});
						$('.library-wrapper').hide();
					});
			},
			_afterSuccess: function(settings) {}
		}
	});
	
	$('[name="<?php echo $className; ?>[all_text]"]').focus();
	
	// Закрыть окно с книгой
	$(document).on('click', '.go-back', function(){
		$('.library-wrapper').show();
		$('.view-content-wrapper')
			.css({position: 'absolute', bottom: 0, 'min-height': '0', height: $(window).height()})
			.stop(true, true)
			.animate({height: 0}, 400, function(){
				$(this).show().css({'min-height': 'auto', 'height': 'auto', 'position': 'static'});
				$('.view-content-wrapper').hide();
				$('.view-content').html('');
			});
		return false;
	});
});
</script>