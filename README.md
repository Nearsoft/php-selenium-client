PHP-SeleniumClient
=========================

PHP interaction with Selenium Webdriver API 

##Description

This library allows the interaction with Selenium Server V2 in PHP. It communicates with the WebDriver API through the official JsonWireProtocol.

One of the goals is to provide a client usage as close as possible to the Selenium Official libraries (such as Java's, c#'s). Most methods are named as the same as of these libraries'. In this way, whenever a developer runs into ideas or documentation in Java Client, the same techniques can be implemented by using this library.

##Documentation

[Version 2](http://nearsoft-php-seleniumclient.herokuapp.com/docs/v2/)

[Version 1](http://nearsoft-php-seleniumclient.herokuapp.com/docs/v1/)

##Quickstart

* Start a session by creating an instance of WebDriver. By default, the test will run in firefox
	
		$driver = new WebDriver();

* As an alternative you can define desired capabilities for the session
	
		$desiredCapabilities = new DesiredCapabilities("chrome");
		$driver = new WebDriver($desiredCapabilities);

* Navigate by using the WebDriver::get method from the WebDriver Class

		$driver->get("www.nearsoft.com");
	
* Get elements from the DOM in current location

		$textbox1 = $driver->findElement(By::id("someTextBoxId"));
		
		$button1 = $driver->findElement(By::cssSelector("html body div#content input#someButtonId"));

* Manipulate located elements

		$textbox1->sendKeys("Some text to send");
		
		$textbox1->getAttribute("value");
		
		$button1->click();
	
* Find element within elements

		$modal1->findElement(By::id("someModalId"));
		$listItems = $modal1->findElements(By::tagName("li"));
		
* Switch between windows

		$driver->switchTo()->window("windowName");
	
* Manage alerts
		$alert = $driver->switchTo()->alert();
		$alert->getText();
		$alert->accept();
		$alert->dismiss();
	
* Wait for elements to be present

		$webElement = $driver->waitForElementUntilIsPresent(By::id("someElementId"));
		
		//or
		
		$wait = new WebDriverWait(8);
		$webElement = $wait->until($driver,"findElement",array(By::id("someElementId"),true));
