<?php
use Phalcon\Di\Di;
use Phalcon\Test\UnitTestCase as PhalconTestCase;

abstract class UnitTestCase extends PhalconTestCase
{

    /**
     *
     * @var \Voice\Cache
     */
    protected $_cache;

    /**
     *
     * @var \Phalcon\Config\Config
     */
    protected $_config;

    /**
     *
     * @var bool
     */
    private $_loaded = false;

    public function setUp(Phalcon\DiInterface $di = NULL, Phalcon\Config\Config $config = NULL)
    {
        // Load any additional services that might be required during testing
        $di = DI::getDefault();
        
        // Get any DI components here. If you have a config, be sure to pass it to the parent
        
        parent::setUp($di);
        
        $this->_loaded = true;
    }

    /**
     * Check if the test case is setup properly
     *
     * @throws \PHPUnit_Framework_IncompleteTestError;
     */
    public function __destruct()
    {
        if (! $this->_loaded) {
            throw new \PHPUnit_Framework_IncompleteTestError('Please run parent::setUp().');
        }
    }
}