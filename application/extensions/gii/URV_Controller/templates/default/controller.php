<?php
/**
 * This is the template for generating a controller class file.
 * The following variables are available in this template:
 * - $this: the ControllerCode object
 */
?>
<?php echo "<?php\n"; ?>
/**
 * Контроллер <?php echo ucfirst($this->controller).".\n"; ?>
 * 
 * @author Сидорович Николай <sidorovich21101986@mail.ru>
 * @link https://github.com/alhimik1986
 * @copyright Copyright &copy; 2014
<?php
	// Достаю @package.
	$controller = explode('/', $this->controller);
	$package = 'application.';
	foreach($controller as $key=>$name) {
		if (($key + 1) == count($controller)) {
			$package .= 'controllers';
		} else {
			$package .= 'modules.'.$name.'.';
		}
	}
?>
 * @package <?php echo $package."\n"; ?>
 */
class <?php echo $this->controllerClass; ?> extends <?php echo $this->baseClass."\n"; ?>
{
	/**
	 * Фильтр: контроль доступа по ролям.
	 */
    public function filters()
    {
        return array(
            array('application.controllers.filters.AccessControl'),
        );
    }
	/**
	 * Передаю фильтру данные о доступе в формате: действие => разрешенные_роли.
	 */
	public function accessByRoles()
	{
		return array(
<?php
	$maxLengthName = 0;
	foreach($this->getActionIDs() as $name) {
		if ($maxLengthName < strlen($name)) $maxLengthName = strlen($name);
	}
?>
<?php foreach($this->getActionIDs() as $action): ?>
			<?php echo "'$action'".str_repeat(' ', $maxLengthName - strlen($name))." => array(Users::SUPER_ADMIN),\n"; ?>
<?php endforeach; ?>
		);
	}
	
	
	/**
	 * 
	 */
<?php foreach($this->getActionIDs() as $action): ?>
	public function action<?php echo ucfirst($action); ?>()
	{
		$this->render('<?php echo $action; ?>');
	}
	
	
<?php endforeach; ?>
}