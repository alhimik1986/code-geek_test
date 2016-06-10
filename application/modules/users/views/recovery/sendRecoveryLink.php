<?php echo Yii::t('users.app', 'Dear {full_name}.', array('{full_name}'=>$user['last_name'].' '.$user['first_name'].' '.$user['middle_name'])); ?>
<br>
<?php echo Yii::t('users.app', 'You sent a letter to the password recovery. If you did not, you should contact the site administrator, because maybe someone is trying to hack into your account.'); ?>
<br>
<?php echo Yii::t('users.app', 'To reset your password, click on this link:'); ?>
<br>
<?php echo CHtml::link(Yii::t('users.app', 'Link to reset your password.'), $url); ?>
<br><br>
<?php echo Yii::t('users.app', 'If the link is not displayed correctly, go to this address:'); ?><br>
<?php echo $url; ?>