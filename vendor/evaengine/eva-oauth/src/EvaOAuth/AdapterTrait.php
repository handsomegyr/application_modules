<?php
/**
 * @author    AlloVince
 * @copyright Copyright (c) 2015 EvaEngine Team (https://github.com/EvaEngine)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Eva\EvaOAuth;

use Doctrine\Common\Cache\Cache;
use Eva\EvaOAuth\Events\EventsManager;
use GuzzleHttp\Client;
use GuzzleHttp\Event\Emitter;

trait AdapterTrait
{
    /**
     * @var HttpClient
     */
    protected static $httpClient;

    /**
     * @var array
     */
    protected static $httpClientDefaultOptions = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var Cache
     */
    protected $storage;

    /**
     * @var Emitter
     */
    protected $emitter;

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public static function setHttpClientDefaultOptions(array $options)
    {
        self::$httpClientDefaultOptions = $options;
    }


    /**
     * @return HttpClient
     */
    public static function getHttpClient()
    {
        if (self::$httpClient) {
            return self::$httpClient;
        }

        return self::$httpClient = new HttpClient(self::$httpClientDefaultOptions);
    }

    /**
     * @param Client $httpClient
     */
    public static function setHttpClient(Client $httpClient)
    {
        self::$httpClient = $httpClient;
    }

    /**
     * @return Cache
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param Cache $storage
     */
    public function setStorage(Cache $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return Emitter
     */
    public function getEmitter()
    {
        if ($this->emitter) {
            return $this->emitter;
        }
        //All adapters share same emitter
        return $this->emitter = EventsManager::getEmitter();
    }
}
