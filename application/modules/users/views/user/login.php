<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle = Yii::t('users.app', 'Login');
$this->renderPartial('_index_css');
$this->renderPartial('_login_css');
$this->renderPartial('_login_js');
?>

<table style="width:100%; height:100%;">
	<tr>
		<td style="vertical-align:middle;">
			<?php $this->renderPartial('_login_form',array('model'=>$model, 'recovery'=>$recovery)); ?>
			
			<div style="position:relative;">
				<div class="login-form-reflection">
					<!--<div class="your-ip"><?php echo Yii::t('users.app', 'Your IP-address'); ?>: <?php echo $this->getIp(); ?></div>-->
					
					<div class="forgot-password-form" style="height:100px;">
						<div style="display:none;">
							<div class="recovery-form-wrapper">
								<?php $this->renderPartial('_recovery_form',array('recovery'=>$recovery)); ?>
							</div>
						</div>
					</div>
					
				</div>
			</div>
		</td>
	</tr>
</table>


<div class="login-footer">
	<div class="login-footer-line"></div>
</div>