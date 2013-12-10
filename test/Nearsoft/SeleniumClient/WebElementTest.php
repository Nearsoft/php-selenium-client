<?php

require_once __DIR__ . '/AbstractTest.php';

use Nearsoft\SeleniumClient\By;
use Nearsoft\SeleniumClient\WebElement;

class WebElementTest extends AbstractTest
{
	public function testGetElementIdShouldGetId()
	{
		$element = $this->_driver->findElement(By::id("txt1"));
		$this->assertTrue(is_numeric($element->getElementId()));
	}	

    public function testGetCoordinatesInViewShouldGetLocationOnScreenOnceScrolledIntoView()
	{
		$element = $this->_driver->findElement(By::id("txt1"));
		$coordinates = $element->getLocationOnScreenOnceScrolledIntoView();
		
		$this->assertTrue(is_numeric($coordinates["x"]) );
		$this->assertTrue(is_numeric($coordinates["y"]) );
	}	
	
	public function testGetCoordinatesShouldGetCoordinates()
	{
		$element = $this->_driver->findElement(By::id("txt1"));
		$coordinates = $element->getCoordinates();
		
		$this->assertTrue(is_numeric($coordinates["x"]) );
		$this->assertTrue(is_numeric($coordinates["y"]) );
	}

    public function testGetCSSPropertyShouldReturnValueOfCssProperty() {
        $element = $this->_driver->findElement(By::xPath('/html/body/table/tbody/tr/td[2]'));
        $property = $element->getCSSProperty('vertical-align');
        $this->assertEquals('top', $property);
    }
	
	public function testIsDisplayedShouldDetermineIfDisplayed()
	{
		$button1 = $this->_driver->findElement(By::id("btnNoAction"));
		$this->assertEquals( true, $button1->isDisplayed());	
		$this->_driver->executeScript("document.getElementById('btnNoAction').style.display = 'none';");
		$this->assertEquals( false, $button1->isDisplayed());
	}
	
	public function testGetAttributeShouldGetData()
	{
		$chk = $this->_driver->findElement(By::id("chk3"));
		$this->assertEquals( "chk3",strtolower($chk->getAttribute("name")));
	}

	public function testSetAttributeShouldSet()
	{
		$webElement = $this->_driver->findElement(By::id("txt1"));
		$webElement->setAttribute('value','123456');
		$webElement->setAttribute('type','hidden');
		$this->assertEquals("123456", $webElement->getAttribute("value"));
		$this->assertEquals("hidden", $webElement->getAttribute("type"));		
	}
	
	public function testIsEnabledShouldDetermineIfEnabled()
	{
		$button1 = $this->_driver->findElement(By::id("btnNoAction"));
		$this->assertEquals( true, $button1->isEnabled());	
	
		$this->_driver->executeScript("document.getElementById('btnNoAction').disabled = true;");
		
		$this->assertEquals( false, $button1->isEnabled());
	}	
	
	public function testIsSelectedShouldDetermineIfSelected()
	{
		$selectBox = $this->_driver->findElement(By::id("sel1"));
	
		$selectBoxOption = $selectBox->findElement(By::xPath("/html/body/table/tbody/tr/td/fieldset/form/p[3]/select/option[1]"));
		
		$this->assertEquals( false, $selectBoxOption->isSelected());
		
		$selectBoxOption->click();
		
		$this->assertEquals( true, $selectBoxOption->isSelected());
	}

    public function testElementSizeShouldGetElementSizeInPixels ()
    {
        $webElement = $this->_driver->findElement(By::id("txtArea1"));
        $dimensions = $webElement->getElementSize();

        $this->assertTrue(is_numeric($dimensions['width']));
        $this->assertTrue(is_numeric($dimensions['height']));
    }

    public function testClassMethodsAffectElementClassName()
    {
        $element = $this->_driver->findElement(By::id("sel1"));
        $this->assertEmpty($element->getClassName());
        $element->setClassName("select x-small");
        $this->assertEquals("select x-small", $element->getClassName());
        $this->assertContains('class="select x-small"', $element->getOuterHTML());
        // test method chaining
        $element->addClass("foo")->addClass("bar");
        $this->assertEquals("select x-small foo bar", $element->getClassName());
        // we shouldn't be able to add a duplicate class
        $element->addClass("foo");
        $this->assertEquals("select x-small foo bar", $element->getClassName());
        $element->removeClass("foo");
        $this->assertEquals("select x-small bar", $element->getClassName());
    }

    public function testHasClass()
    {
		$element = $this->_driver->findElement(By::id("txt1"));
		$element->addClass("foo");
		$this->assertTrue($element->hasClass("foo"));
		$this->assertFalse($element->hasClass("someotherclass"));
    }
	
	public function testClearShouldSetValueEmpty()
	{
		$webElement = $this->_driver->findElement(By::id("txt1"));
	
		$webElement->sendKeys("test text");
	
		$webElement->clear();
	
		$this->assertEquals("", trim($webElement->getAttribute("value")));
	}

    public function testGetInnerHTMLShouldGetInnerHTML()
    {
        $selectBox = $this->_driver->findElement(By::id("sel1"));
        $text = $selectBox->getInnerHTML();
        $this->assertContains('<option', $text);
        $this->assertStringEndsNotWith('</select>', $text);
    }

    public function testGetOuterHTMLShouldGetOuterHTML()
    {
        $selectBox = $this->_driver->findElement(By::id("sel1"));
        $text = $selectBox->getOuterHTML();
        $this->assertContains('<option', $text);
        $this->assertStringEndsWith('</select>', $text);
    }

	public function testGetTagNameShouldGetTagName()
	{
		$webElement = $this->_driver->findElement(By::id("txt1"));
		$this->assertEquals("input",strtolower($webElement->getTagName()));
	}

    public function testCompareToShouldCompareElementWithID()
    {
       $webElement1 = $this->_driver->findElement(By::id("txt1"));
       $webElementOther = $this->_driver->findElement(By::xPath("//*[@id='txt1']"));
       $webElement2 = $this->_driver->findElement(By::id("txt2"));


       $this->assertFalse($webElement1->compareToOther($webElement2));
       $this->assertTrue ($webElement1->compareToOther($webElementOther));
    }


	
	public function testDescribeShouldGetElementId()
	{
		$webElement = $this->_driver->findElement(By::id("btnSubmit"));
    	$expectedKeys = array('id','enabled','selected','text','displayed','tagName','class','hCode');
    	$descriptionData = $webElement->describe();
		$this->assertTrue(array_intersect(array_keys($descriptionData),$expectedKeys) === $expectedKeys);    
	}

    public function testFindElementByJsSelectorShouldGetChildElement()
    {
        $selectBox = $this->_driver->findElement(By::id("sel1"));
        $option = $selectBox->findElement(By::jsSelector('option[selected="selected"]', 'document.querySelector'));
        $this->assertEquals('Orange', $option->getText());
    }
	
	public function testFindElementShouldGetFoundElementText()
	{
	
		$selectBox = $this->_driver->findElement(By::id("sel1"));
	
		$selectBoxOption = $selectBox->findElement(By::xPath("/html/body/table/tbody/tr/td/fieldset/form/p[3]/select/option[2]"));
	
		$this->assertTrue($selectBoxOption instanceof  WebElement);
		
		$this->assertEquals( "Red", $selectBoxOption->getText() );
	}
	
	public function testFindElementsShouldGetOneOfFoundElementsText()
	{
	
		$selectBox = $this->_driver->findElement(By::id("sel1"));
	
		$selectBoxOptions = $selectBox->findElements(By::tagName("option"));

		foreach($selectBoxOptions as $selectBoxOption)
		{
			
			$this->assertTrue($selectBoxOption instanceof  WebElement);
			if($selectBoxOption->getAttribute("value") == "4")
			{
				
				$selectBoxOption->click();
			}
		}

		foreach($selectBoxOptions as $selectBoxOption)
		{
			
			if($selectBoxOption->getAttribute("selected") == "true")
			{
				$this->assertEquals("Black", $selectBoxOption->getText());
			}		
		}
	}

	public function testClickShouldSubmitForm()
	{	
		$button = $this->_driver->findElement(By::id("btnSubmit"));
		
		$button->click();
		
		$this->assertTrue(strstr($this->_driver->getCurrentUrl(), "formReceptor") >= 0);
	}

	public function testClickShouldGetAlert()
    {
    	$webElement = $this->_driver->findElement(By::id("btnAlert"));
		$webElement->click();
		$this->assertEquals('Here is the alert',$this->_driver->switchTo()->alert()->getText());   	
    }   
	
	public function testSubmitShouldSubmitForm()
	{
		$form = $this->_driver->findElement(By::xPath("/html/body/table/tbody/tr/td[1]/fieldset/form"));	
		$form->submit();	
		$this->assertTrue(strstr($this->_driver->getCurrentUrl(), "formReceptor") >= 0);
	}

	public function testSubmitShouldSubmitFormFromButton()
	{
		$button = $this->_driver->findElement(By::id("btnSubmit"));
	
		$button->submit();
	
		$this->assertTrue(strstr($this->_driver->getCurrentUrl(), "formReceptor") >= 0);
	}
	
	public function testGetTextShouldGetText()
	{
		$label = $this->_driver->findElement(By::xPath("/html/body/table/tbody/tr/td[2]/fieldset/p"));
		$this->assertEquals("Simple paragraph", $label->getText());
	}
	
	public function testSendKeysShouldRetreiveText()
	{
		$textarea1 = $this->_driver->findElement(By::id("txtArea1"));
		$textarea1->sendKeys("TEST");
		$this->assertEquals("TEST", $textarea1->getAttribute("value"));
	}

	public function testSendKeysShouldRetrieveHebrewText()
	{
		$textarea1 = $this->_driver->findElement(By::id("txtArea1"));
		$textarea1->sendKeys("יאיר 34557");
		$this->assertEquals("יאיר 34557", $textarea1->getAttribute("value"));
	}
}