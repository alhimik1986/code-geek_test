<?php // @param array $plugin_params Список плагинов, которые необходимо отобразить. ?><?php if (false): ?><script type="text/javascript"><?php endif; ?><?php
// **************************************************************************************************************
//модуль: ajaxChosen - плагин для chosen, чтобы вести ajax-поиск на сервере.
// **************************************************************************************************************
JsCssFiles::jsFile($baseUrl.'/js/ajax-chosen/lib/ajax-chosen.js');
JsCssFiles::css('.chzn-container .chzn-results .group-result {display:list-item;}');
?>

<?php if (false): ?></script><?php endif; ?>