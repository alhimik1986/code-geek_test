<?php $textarea_id = 'Site_blockedReason'; ?>
<script type="text/javascript">
(function($){$(document).ready(function(){
	window.urv.nicEdit('<?php echo $textarea_id; ?>');
	
	<?php // Форма для входа в систему ?>
	new ajaxForm({
		form: {
			selector: '#block-site-form'
		},
		initForm: {
			selector: '#block-site-form'
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
			},
			success: function(data, settings) {
				<?php Yii::import('application.modules.users.models.LoginForm'); ?>
				var url = ( ! data.SettingsModel.value.blocked && <?php echo (int)Yii::app()->settings->param['Site']['blocked']; ?>)
					? '<?php echo Users::getUrlForRedirectingUser(); ?>'
					: '<?php echo $this->createUrl('//settings/setting/siteBlocked'); ?>';
				window.location.href = url;
			}
		}
	});
});})(jQuery)
</script>