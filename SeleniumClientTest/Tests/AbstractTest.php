<?php

use SeleniumClient\DesiredCapabilities;
use SeleniumClient\Http\SeleniumUnknownErrorException;
use SeleniumClient\WebDriver;

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
        self::$_config['url']      = getenv( 'SELENIUM_CLIENT_URL' ) ?: 'http://nearsoft-php-seleniumclient.herokuapp.com';
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
                /** @var $driver WebDriver */
                $driver = end( self::$_driverInstances );
            }
        } else {
            $capabilities = new DesiredCapabilities( $browser );
            $driver = new WebDriver( $capabilities );
            self::$_driverInstances[] = $driver;
        }

        $this->_driver = $driver;
        $this->_driver->get($this->_url);
        $this->_position = $this->_driver->getCurrentWindowPosition();
        $this->_size = $this->_driver->getCurrentWindowSize();
        self::$_handle = $this->_driver->getCurrentWindowHandle();
    }

    public function tearDown()
    {
        // return if the driver wasn't initialized
        if ( !$this->_driver ) {
            return;
        }

        if ( self::$_config['persist'] ) {
            try {
                $this->_driver->dismissAlert();
            } catch ( Exception $e ) {
            }
            try {
                foreach ( $this->_driver->getCurrentWindowHandles() as $handle ) {
                    // skip the original window
                    if ( $handle == self::$_handle ) {
                        continue;
                    }
                    // try to close any other windows that were opened
                    try {
                        $this->_driver->getWindow( $handle );
                        $this->_driver->closeCurrentWindow();
                    } catch ( Exception $e ) {
                    }
                }
                $this->_driver->getWindow( self::$_handle );
                $this->_driver->getActiveElement();
            } catch ( SeleniumUnknownErrorException $e ) {
                // test case may have closed the parent window
                self::$_handle = null;
                return;
            }
            $this->_driver->clearCurrentCookies();
            $this->_driver->setImplicitWait(0);
            $this->_driver->setCurrentWindowPosition($this->_position['x'], $this->_position['y'] );
            $this->_driver->setCurrentWindowSize($this->_size['width'], $this->_size['height'] );
            $this->_driver->setPageLoadTimeout(10000);
        } else {
            try{
                $this->_driver->quit();
            } catch ( Exception $e ) {
            }
        }
    }
}