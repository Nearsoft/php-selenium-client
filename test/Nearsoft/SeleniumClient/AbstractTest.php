<?php

use Nearsoft\SeleniumClient\Alert;
use Nearsoft\SeleniumClient\DesiredCapabilities;
use Nearsoft\SeleniumClient\Exceptions;
use Nearsoft\SeleniumClient\WebDriver;

class AbstractTest extends PHPUnit_Framework_TestCase {
    /** @var array */
    private static $_config = array();

    /** @var WebDriver[]*/
    private static $_driverInstances = array();

    /** @var string */
    private static $_handle = '';

    /** @var WebDriver */
    protected $_driver = null;

    /** @var string */
    protected $_url = '';

    /** @var array */
    private $_position = array();

    /** @var array */
    private $_size = array();

    public static function setUpBeforeClass()
    {
        static $initialized = false;

        if ( $initialized ) {
            return;
        }

        self::$_config['browser']  = getenv( 'SELENIUM_CLIENT_BROWSER' ) ?: 'firefox';
        self::$_config['url']      = getenv( 'SELENIUM_CLIENT_URL' ) ?: 'http://nearsoft-php-seleniumclient.herokuapp.com/sandbox/';
        self::$_config['persist']  = (bool)getenv( 'SELENIUM_CLIENT_SINGLE_INSTANCE' ) ?: false;

        $drivers = &self::$_driverInstances;

        // make sure the browser closes at the end of running all tests
        register_shutdown_function( function() use ( &$drivers ) {
            foreach ( $drivers as $driver ) {
                try {
                    $driver->quit();
                } catch ( Exception $e ) {
                }
            }
        } );

        $initialized = true;
    }
	
    public function setUp()
    {
        $this->_url = self::$_config['url'];
        $browser = self::$_config['browser'];

        if ( self::$_config['persist'] ) {
            if ( !self::$_handle ) {
                $capabilities = new DesiredCapabilities( $browser );
                $driver = new WebDriver( $capabilities );
                self::$_driverInstances[] = $driver;
            } else {
                $driver = end( self::$_driverInstances );
            }
        } else {
            $capabilities = new DesiredCapabilities( $browser );
            $driver = new WebDriver( $capabilities );
            self::$_driverInstances[] = $driver;
        }

        $this->_driver = $driver;
        $this->_driver->get($this->_url);
        $this->_position = $this->_driver->manage()->window()->getPosition();
        $this->_size = $this->_driver->manage()->window()->getSize();
        self::$_handle = $this->_driver->getWindowHandle();
    }

    public function tearDown()
    {
        // return if the driver wasn't initialized
        if ( !$this->_driver ) {
            return;
        }

        if ( self::$_config['persist'] ) {
            try {
                $alert = new Alert($this->_driver);
                $alert->dismiss();
            } catch ( Exception $e ) {
            }
            try {
                foreach ( $this->_driver->getWindowHandles() as $handle ) {
                    // skip the original window
                    if ( $handle == self::$_handle ) {
                        continue;
                    }
                    // try to close any other windows that were opened
                    try {
                        $this->_driver->switchTo()->window($handle);
                        $this->_driver->close();
                    } catch ( Exception $e ) {
                    }
                }
                $this->_driver->switchTo()->window( self::$_handle );
                $this->_driver->switchTo()->activeElement();
            } catch ( SeleniumUnknownErrorException $e ) {
                // test case may have closed the parent window
                self::$_handle = null;
                return;
            }
            $this->_driver->manage()->deleteAllCookies();
            $this->_driver->manage()->timeouts()->implicitWait(0);
            $this->_driver->manage()->window()->setPosition($this->_position['x'], $this->_position['y'] );
            $this->_driver->manage()->window()->setSize($this->_size['width'], $this->_size['height'] );
            $this->_driver->manage()->timeouts()->pageLoadTimeout(10000);
        } else {
            try{
                $this->_driver->quit();
            } catch ( Exception $e ) {
            }
        }
    }
}