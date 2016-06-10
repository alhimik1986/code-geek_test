<?php

class {ClassName} extends CDbMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		
	}

	public function safeDown()
	{
		echo "{ClassName} does not support migration down.\\n";
		return false;
	}
}