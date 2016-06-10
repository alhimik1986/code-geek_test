<div class="user-menu" style="margin-bottom:20px;">
	<?php $items = array(
		'items'=>array(
			array(
				'label'=>Yii::t('menu', 'Книги'),
				'url'=>array('//library/books/index'),
			),
			array(
				'label'=>Yii::t('menu', 'Библиотека'),
				'url'=>array('//library/library/index'),
			),
			array(
				'label'=>Yii::t('menu', 'Настройки'),
				'url'=>array('//settings/setting/index'),
				'visible' => (Yii::app()->user->role == Users::SUPER_ADMIN),
			),
			array(
				'label'=>Yii::t('menu', 'Log Out').' ('.(isset(Yii::app()->user->param['fio']) ? Yii::app()->user->param['fio'] : '').')',
				'url'=>array('/users/user/logout'),
				'itemOptions'=>array(
					'class'=>'last',
				),
			),
			array(
				'label'=>Yii::t('menu', 'My profile'),
				'url'=>array('/users/profile/index'),
				'itemOptions'=>array(
					'class'=>'pre-last',
				),
			),
		),
	);
	$this->widget('zii.widgets.CMenu', $items);
	?>
</div>