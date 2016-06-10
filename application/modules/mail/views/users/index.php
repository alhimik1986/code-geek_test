<?php
/*
	Данные пользователей для отправки почты.
	
	@var $this   UsersController
	@var $model  Users (CActiveRecord)  - Модель пользователя (для отображения подписей аттрибутов)
	$var $uses   array                  - Список пользователй
*/
?>

<?php
$pageTitle = Yii::t('mail.app', 'Additional user data to send mail');
$this->setPageTitle($pageTitle);         // Заголовок страницы
$this->breadcrumbs = array($pageTitle);  // Хлебные крошки (навигация сайта)

$this->renderPartial('application.modules.users.views.user._index_css');    // Подключаю java-скрипты
$this->renderPartial('application.modules.users.views.user._js_plugins');   // Загружаю стандарные jquery-плагины
$this->renderPartial('application.modules.users.views.user._index_js_table', array('model'=>$model));  // Подключаю java-скрипты

$l = $model->attributeLabels();
?>

<div class="format hint">
	<?php echo Yii::t('mail.app', 'To send mail through Exchange-server (Outlook), fill the following fields:'); ?><br>
	"<?php echo $l['email_class']; ?>" - <?php echo Yii::t('mail.app', 'select the value'); ?> "<?php $list = UsersMailHelper::email_class_label(); echo $list[UsersMailHelper::ExchangeMailHelper]; ?>"<br>
	"<?php echo $l['email_host']; ?>" - <?php echo Yii::t('mail.app', 'type the mail server, for example'); ?> "mail.org.local"<br>
	"<?php echo $l['email_address']; ?>" - <?php echo Yii::t('mail.app', 'type the mail username'); ?><br>
	"<?php echo $l['email_from_name']; ?>" - <?php echo Yii::t('mail.app', 'type the sender name, for example "Marty Seamus McFly"'); ?><br>
	<?php echo Yii::t('mail.app', 'Specify'); ?> "<?php echo $l['email_username']; ?>" <?php echo Yii::t('mail.app', 'and'); ?> "<?php echo $l['email_password']; ?>"<br>
	<?php echo Yii::t('mail.app', 'Messages sent through the Exchange are stored in the log of sent messages on the Outlook Web App client.'); ?>
	<br><br>
	<?php echo Yii::t('mail.app', 'To connect to other mail server, for example to Gmail, fill the following fields:'); ?><br>
	"<?php echo $l['email_class']; ?>" - <?php echo Yii::t('mail.app', 'select the value'); ?> "<?php $list = UsersMailHelper::email_class_label(); echo $list[UsersMailHelper::PHPMailerHelper]; ?>"<br>
	"<?php echo $l['email_smtp_secure']; ?>" - <?php echo Yii::t('mail.app', 'type "tls"'); ?><br>
	"<?php echo $l['email_host']; ?>" - <?php echo Yii::t('mail.app', 'type "smtp.gmail.com"'); ?><br>
	"<?php echo $l['email_port']; ?>" - <?php echo Yii::t('mail.app', 'type "587"'); ?><br>
	"<?php echo $l['email_address']; ?>" - <?php echo Yii::t('mail.app', 'type the E-mail of the sender.'); ?><br>
	"<?php echo $l['email_from_name']; ?>" - <?php echo Yii::t('mail.app', 'type the sender name, for example "Marty Seamus McFly"'); ?><br>
	<?php echo Yii::t('mail.app', 'Specify'); ?> "<?php echo $l['email_username']; ?>" <?php echo Yii::t('mail.app', 'and'); ?> "<?php echo $l['email_password']; ?>"<br>
	<br>
</div>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>$model::className().'-search-form',
	'enableClientValidation' => false,
)); ?>

<!-- Шапка таблицы -->
<div style="padding:10px; color:#333; border:1px solid #a0a0a0; border-radius:5px; background:#d0d0d0; background: linear-gradient(to bottom, rgba(228, 228, 228, 1) 0%, rgba(205, 205, 205, 1) 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);">
	<?php echo Yii::t('app', 'Search'); ?>: <input type="text" name="<?php echo $model::className().'[all_text]'; ?>" style="width:180px;background:white;" placeholder="<?php echo Yii::t('app', 'on all parameters'); ?>..." title="<?php echo Yii::t('app', 'Minimum 3 characters'); ?>" class="search-on-change">
	
	<span style="margin-left:10px;"><?php echo Yii::t('app', 'Show'); ?>: 
		<span>
			<?php echo CHtml::dropDownList('limit', 10, array(10=>10, 30=>30, 100=>100, 1000=>1000), array(
				'class'=>'search-on-change'
			)); ?>
		</span> <?php echo Yii::t('app', 'rows'); ?>
	</span>
	
	
	<span style="margin-left:30px;">
		<?php echo CHtml::hiddenField($model::className().'[removed]', '0', array('id'=>$model::className().'_removed1')); ?>
		<?php echo CHtml::checkbox($model::className().'[removed]', false, array('id'=>$model::className().'_removed2', 'class'=>'search-on-change')); ?>
		<?php echo CHtml::label(Yii::t('app', 'Show removed'), $model::className().'_removed2'); ?>
	</span>
</div>
<!-- Таблица с пользователями -->
<table id="urv-table" class="urv-table">
	<thead>
		<tr>
			<th style="width:20px"><?php echo Yii::t('app', '№'); ?></th>
			<th style="width:100px"><?php echo Yii::t('app', 'Full name'); ?></th>
			<th style="width:100px"><?php echo $model->getAttributeLabel('role'); ?></th>
			<th style="width:50px" title="<?php echo $model->getAttributeLabel('email'); ?>"><?php echo Yii::t('app', 'E-mail'); ?></th>
			<th style="width:50px"><?php echo $model->getAttributeLabel('email_smtp_secure')?><?php echo Yii::t('mail.app', '://Host:'); ?><?php echo $model->getAttributeLabel('email_port'); ?></th>
			<th style="width:50px"><?php echo $model->getAttributeLabel('email_from_name'); ?></th>
			<th style="width:50px"><?php echo $model->getAttributeLabel('email_username'); ?></th>
			<th style="width:50px" title="<?php echo $model->getAttributeLabel('comment'); ?>"><?php echo Yii::t('app', 'Comment'); ?></th>
			<th style="width:20px" column_name="<?php echo $model::table(); ?>.id"><?php echo $model->getAttributeLabel('id'); ?></th>
		</tr>
		<tr class="no-top-border-in-child" style="background-color:#ddd;">
			<th></th>
			<th><input type="text" name="<?php echo $model::className(); ?>[fio]" placeholder="<?php echo Yii::t('app', 'Full name'); ?>..." class="search-on-change"></th>
			<th><?php echo CHtml::dropDownList($model::className().'[role]', '', ArrayHelper::merge(array(''=>Yii::t('app', 'All roles')), $model::roleListForForm()), array(
				'class'                => 'chosen search-on-change',
				'style'                => 'width:120px;',
				'data-placeholder'     => Yii::t('app', 'Role'),
				'data-no_results_text' => Yii::t('app', 'No results match'),
				'id'                   => $model::className().'_role1',
				//'multiple'             => 'true',
			)); ?></th>
			<th><input type="text" name="<?php echo $model::className(); ?>[email]" placeholder="<?php echo $model->getAttributeLabel('email'); ?>..." class="search-on-change"></th>
			<th><input type="text" name="<?php echo $model::className(); ?>[email_host]" placeholder="<?php echo $model->getAttributeLabel('email_host'); ?>..." class="search-on-change"></th>
			<th><input type="text" name="<?php echo $model::className(); ?>[email_from_name]" placeholder="<?php echo $model->getAttributeLabel('email_from_name'); ?>..." class="search-on-change"></th>
			<th><input type="text" name="<?php echo $model::className(); ?>[email_username]" placeholder="<?php echo $model->getAttributeLabel('email_username'); ?>..." class="search-on-change"></th>
			<th><input type="text" name="<?php echo $model::className(); ?>[comment]" placeholder="<?php echo $model->getAttributeLabel('comment'); ?>..." class="search-on-change"></th>
			<th><input type="text" name="<?php echo $model::className(); ?>[id]" placeholder="<?php echo $model->getAttributeLabel('id'); ?>..." class="search-on-change" style="width:25px;"></th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
<?php $this->endWidget(); ?>