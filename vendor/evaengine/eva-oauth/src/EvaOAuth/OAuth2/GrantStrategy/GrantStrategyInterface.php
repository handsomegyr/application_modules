<?php
/**
 * @author    AlloVince
 * @copyright Copyright (c) 2015 EvaEngine Team (https://github.com/EvaEngine)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Eva\EvaOAuth\OAuth2\GrantStrategy;

use Doctrine\Common\Cache\Cache;
use Eva\EvaOAuth\OAuth2\AuthorizationServerInterface;
use Eva\EvaOAuth\OAuth2\ResourceServerInterface;
use GuzzleHttp\Client as HttpClient;
use Eva\EvaOAuth\Token\AccessTokenInterface;
use GuzzleHttp\Event\EmitterInterface;

/**
 * Interface GrantStrategyInterface
 * @package Eva\EvaOAuth\OAuth2\GrantStrategy
 */
interface GrantStrategyInterface
{
    /**
     * @return EmitterInterface
     */
    public function getEmitter();

    /**
     * Generate OAuth authorize URL with query
     *
     * @param AuthorizationServerInterface $authServer
     * @return string
     */
    public function getAuthorizeUrl(AuthorizationServerInterface $authServer);

    /**
     * Redirect to authorize url (Maybe do nothing under some grant types)
     *
     * @param AuthorizationServerInterface $authServer
     * @param string $url
     * @return void
     */
    public function requestAuthorize(AuthorizationServerInterface $authServer, $url = '');

    /**
     * @param ResourceServerInterface $resourceServer
     * @param array $urlQuery
     * @return AccessTokenInterface
     */
    public function getAccessToken(ResourceServerInterface $resourceServer, array $urlQuery = array());

    /**
     * @param HttpClient $httpClient
     * @param array $options
     * @param Cache $storage
     */
    public function __construct(HttpClient $httpClient, array $options, Cache $storage = null);
}
