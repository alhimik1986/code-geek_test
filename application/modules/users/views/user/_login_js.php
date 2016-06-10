<?php
// Загружаю стандарные jquery-плагины
JSPlugins::$includePlugins = array(
	'jquery'   => '',                          // - Подключить jquery-библиотеку
	'ajaxForm' => '',                          // - Подключить стандартную форму для УРВ
	'placeholder' => array(                    // - Подключить placeholder (надпись внутри текстового поля) для старого Internet Explorer
		'selector'=>'input, textarea',             // - css-селектор, к которому применить плагин
		'color'=>'#aaa',                           // - цвет надписи
	),
);
?>

<script type="text/javascript">
(function($){$(document).ready(function(){
	setTimeout(function(){
		$('#LoginForm_username').focus();
	}, 3);
	<?php // Форма для входа в систему ?>
	new ajaxForm({
		form: {
			selector: '#login-form'
		},
		initForm: {
			selector: '#login-form'
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
				window.location.href = data.LoginForm.password;
			}
		}
	});
	
	<?php // Форма для восстановления аккаунта ?>
	new ajaxForm({
		form: {
			selector: '#recovery-form'
		},
		initForm: {
			selector: '#recovery-form'
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
				window.location.href = data.RecoveryForm.username;
			},
			notValid: function(data, settings) {
				settings.form.dom.find('#recovery-captcha_button').click();
				$('#RecoveryForm_verifyCode').val('');
			}
		}
	});
	
	<?php // Форма регистрации пользователей ?>
	new ajaxForm({
		csrf: {<?php echo Yii::app()->request->csrfTokenName . ":'" . Yii::app()->request->csrfToken."'"; ?>},
		form: {
			selector: '#registration'
		},
		create: {
			selector: '#registration-form-link',
			ajax: function(settings) {
				return {
					url: '<?php echo $this->createUrl('registration/form'); ?>'
				};
			},
			success : function(data, settings) {
				$(settings.form.selector).remove();
				// В этой функции обязательно нужно вернуть jQuery-объект полученной формы для ее последующей обработки
				return $($(data)).appendTo('body');
			},
			afterSuccess: function(settings) {
				settings.form.dom.find('input[type="text"]:first').focus();
			}
		},
		submit: {
			selector: '.ajax-form-button-registration',
			ajax: function(settings) {
				var form = settings.submit.dom.parents('form');
				return {
					url: form.attr('action'),
					data: form.serializeArray()
				};
			},
			success: function(data, settings) {
				window.location.href = '<?php echo $this->createUrl('registration/success'); ?>';
			},
			notValid: function(data, settings) {
				settings.form.dom.find('#registration-captcha_button').click();
				$('#Users_verifyCode').val('');
			}
		}
	});
	
	// Кнопка обновления каптчи для формы регистрации
	jQuery(document).on('click', '#registration-captcha_button', function(){
		jQuery.ajax({
			url: '<?php echo $this->createUrl('captcha1', array('refresh'=>1)); ?>',
			dataType: 'json',
			cache: false,
			success: function(data) {
				jQuery('#registration-captcha').attr('src', data['url']);
				jQuery('body').data('//user/captcha.hash', [data['hash1'], data['hash2']]);
			}
		});
		return false;
	});
});})(jQuery)
</script>