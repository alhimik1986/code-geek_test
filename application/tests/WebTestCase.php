<?php

/**
 * Change the following URL based on your server configuration
 * Make sure the URL ends with a slash so that we can use relative URLs in test cases
 */
define('TEST_BASE_URL','http://localhost/index-test.php/');

/**
 * The base class for functional test cases.
 * In this class, we set the base URL for the test application.
 * We also provide some common methods to be used by concrete test classes.
 */
class WebTestCase extends CWebTestCase
{
	protected $coverageScriptUrl = 'http://localhost/phpunit_coverage.php'; // Эта штука нужна для отчета о покрытии кода
	
    
	protected $captureScreenshotOnFailure;
    protected $screenshotPath;
    protected $screenshotUrl;
	
	
	/**
	 * Sets up before each test method runs.
	 * This mainly sets the base URL for the test application.
	 */
	protected function setUp()
	{
		// Настройка скриншотов
		$this->screenshotPath = __DIR__ .'/report/screenshoots';
		$this->screenshotUrl = TEST_BASE_URL .'images';
		$this->captureScreenshotOnFailure = ($this->getBrowser() == '*firefox');
		
		parent::setUp();
		$this->setBrowserUrl(TEST_BASE_URL);
	}
}
