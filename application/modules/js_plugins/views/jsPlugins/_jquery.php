<?php // @param array $plugin_params Список плагинов, которые необходимо отобразить. ?><?php if (false): ?><script type="text/javascript"><?php endif; ?><?php
// **************************************************************************************************************
//модуль: jquery - jquery-библиотека
// **************************************************************************************************************
	Yii::app()->clientScript->scriptMap=array('jquery.js'=>false,); // Отключаю фирменный jquery, который появляется при использовании ajax-кнопки
	
	if ( ! is_array($plugin_params) OR ! $plugin_params){
		JsCssFiles::jsFile($baseUrl.'/js/jquery-1.11.2.min.js');
	} else {
		foreach($plugin_params as $lib)
			JsCssFiles::jsFile($baseUrl.$lib);
	}
	JsCssFiles::jsFile($baseUrl.'/js/jquery.cookie.js'); // Подгружаю плагин для работы с куками
?>

<?php if (false): ?></script><?php endif; ?>