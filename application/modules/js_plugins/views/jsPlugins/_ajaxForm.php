<?php // @param array $plugin_params Список плагинов, которые необходимо отобразить. ?><?php if (false): ?><script type="text/javascript"><?php endif; ?><?php
// **************************************************************************************************************
//модуль: ajax-форма для УРВ.
// **************************************************************************************************************
JsCssFiles::jsFile($baseUrl.'/js/ajaxForm/ajax-form.js');
JsCssFiles::cssFile($baseUrl.'/css/urv-form.css');
// Плагин, делающий форму перемещаемой
JsCssFiles::jsFile($baseUrl.'/js/drag/jquery.event.drag-2.2.js');
JsCssFiles::jsFile($baseUrl.'/js/drag/jquery.event.drag.live-2.2.js');
// модуль: noty - всплывающие сообщения. jquery.noty
JsCssFiles::jsFile($baseUrl.'/js/noty/jquery.noty.js');
JsCssFiles::jsFile($baseUrl.'/js/noty/layouts/top.js');
JsCssFiles::jsFile($baseUrl.'/js/noty/themes/default.js');
?>
<?php if (false): ?></script><?php endif; ?>