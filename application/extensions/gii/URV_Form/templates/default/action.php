<?php
/**
 * This is the template for generating the action script for the form.
 * - $this: the CrudCode object
 */
?>
<?php
$viewName=basename($this->viewName);
?>
/**
 * Форма создания и редактирования.
 */
public function action<?php echo ucfirst(trim($viewName,'_')); ?>($id=false)
{
	$model = $id ? <?php echo $this->modelClass; ?>::getModel($id) : new <?php echo $this->modelClass; ?><?php echo empty($this->scenario) ? '' : "('{$this->scenario}')"; ?>;
	if (isset($_POST['<?php echo $this->modelClass; ?>'])) {
		$model->setAttrAndSave($_POST['<?php echo $this->modelClass; ?>']);
		// Проверить ошибоки валидации и вывести в формате JSON: если успех - удачно записанные данные, если ошибка - ошибки.
		$this->checkErrorsAndDisplayResult($model);
	} else {
		$this->renderJson('<?php echo $viewName; ?>', array('model' => $model));
	}
}