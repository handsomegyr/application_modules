<?php
/**
 * @author    AlloVince
 * @copyright Copyright (c) 2015 EvaEngine Team (https://github.com/EvaEngine)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Eva\EvaOAuth\OAuth2;

use Eva\EvaOAuth\AdapterTrait;
use Eva\EvaOAuth\Events\BeforeAuthorize;
use Eva\EvaOAuth\Events\BeforeGetAccessToken;
use Eva\EvaOAuth\Exception\InvalidArgumentException;
use Eva\EvaOAuth\OAuth2\Token\AccessToken;
use Eva\EvaOAuth\OAuth2\GrantStrategy\GrantStrategyInterface;

/**
 * OAuth2 Client
 * @package Eva\EvaOAuth\OAuth2
 */
class Client
{

    /**
     * Authorization Code Grant
     * http://tools.ietf.org/html/rfc6749#section-4.1
     */
    const GRANT_AUTHORIZATION_CODE = 'authorization_code';

    /**
     * Implicit Grant
     * http://tools.ietf.org/html/rfc6749#section-4.2
     * NOTICE: implicit type will skip authorize step.
     */
    const GRANT_IMPLICIT = 'implicit';

    /**
     * Resource Owner Password Credentials Grant
     * http://tools.ietf.org/html/rfc6749#section-4.3
     */
    const GRANT_PASSWORD = 'password';

    /**
     * Client Credentials Grant
     * http://tools.ietf.org/html/rfc6749#section-4.4
     */
    const GRANT_CLIENT_CREDENTIALS = 'client_credentials';

    /**
     * @var string
     */
    protected $grantStrategyName = self::GRANT_AUTHORIZATION_CODE;

    /**
     * @var GrantStrategyInterface
     */
    protected $grantStrategy;

    /**
     * @var array
     */
    protected static $grantStrategyMapping = [];

    use AdapterTrait;

    /**
     * @param AccessToken $token
     * @param ResourceServerInterface $resourceServer
     * @return \Eva\EvaOAuth\User\UserInterface
     */
    public static function getUser(AccessToken $token, ResourceServerInterface $resourceServer)
    {
        return $resourceServer->getUser($token);
    }

    /**
     * @return string
     */
    public function getGrantStrategyName()
    {
        return $this->grantStrategyName;
    }

    /**
     * @param $grantStrategyName
     * @return $this
     */
    public function changeGrantStrategy($grantStrategyName)
    {
        if (false === array_key_exists($grantStrategyName, self::$grantStrategyMapping)) {
            throw new InvalidArgumentException(sprintf("Input grant strategy %s not exist", $grantStrategyName));
        }

        $this->grantStrategyName = $grantStrategyName;
        return $this;
    }

    /**
     * @param $strategyName
     * @param $strategyClass
     */
    public static function registerGrantStrategy($strategyName, $strategyClass)
    {
        if (!class_exists($strategyClass) ||
            !in_array(
                'Eva\EvaOAuth\OAuth2\GrantStrategy\GrantStrategyInterface',
                class_implements($strategyClass)
            )
        ) {
            throw new InvalidArgumentException('Register grant strategy failed by unrecognized interface');
        }

        self::$grantStrategyMapping[(string)$strategyName] = $strategyClass;
    }

    /**
     * @return array
     */
    public static function getGrantStrategyMapping()
    {
        if (self::$grantStrategyMapping) {
            return self::$grantStrategyMapping;
        }

        return self::$grantStrategyMapping = [
            self::GRANT_AUTHORIZATION_CODE => 'Eva\EvaOAuth\OAuth2\GrantStrategy\AuthorizationCode',
            self::GRANT_IMPLICIT => 'Eva\EvaOAuth\OAuth2\GrantStrategy\Implicit',
            self::GRANT_PASSWORD => 'Eva\EvaOAuth\OAuth2\GrantStrategy\Password',
            self::GRANT_CLIENT_CREDENTIALS => 'Eva\EvaOAuth\OAuth2\GrantStrategy\ClientCredentials',
        ];
    }

    /**
     * @return GrantStrategyInterface
     */
    public function getGrantStrategy()
    {
        if ($this->grantStrategy) {
            return $this->grantStrategy;
        }

        $grantStrategyClass = self::getGrantStrategyMapping()[$this->grantStrategyName];

        /** @var GrantStrategyInterface $grantStrategy */
        $grantStrategy = new $grantStrategyClass(self::getHttpClient(), $this->options);

        //Events Propagation
        $grantStrategy->getEmitter()->on('beforeAuthorize', function (BeforeAuthorize $event) {
            $this->getEmitter()->emit('beforeAuthorize', new BeforeAuthorize($event->getUri(), $this));
        });
        $grantStrategy->getEmitter()->on('beforeGetAccessToken', function (BeforeGetAccessToken $event) {
            $this->getEmitter()->emit(
                'beforeGetAccessToken',
                new BeforeGetAccessToken($event->getRequest(), $event->getProvider(), $this)
            );
        });

        return $this->grantStrategy = $grantStrategy;
    }

    /**
     * @param AuthorizationServerInterface $authServer
     * @return string
     */
    public function getAuthorizeUri(AuthorizationServerInterface $authServer)
    {
        return $this->getGrantStrategy()->getAuthorizeUrl($authServer);
    }

    /**
     * @param AuthorizationServerInterface $authServer
     */
    public function requestAuthorize(AuthorizationServerInterface $authServer)
    {
        $uri = $this->getAuthorizeUri($authServer);
        $this->getGrantStrategy()->requestAuthorize($authServer, $uri);
    }

    /**
     * @param ResourceServerInterface $resourceServer
     * @return mixed
     */
    public function getAccessToken(ResourceServerInterface $resourceServer)
    {
        return $this->getGrantStrategy()->getAccessToken($resourceServer);
    }

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $options = array_merge([
            'client_id' => '',
            'client_secret' => '',
            'redirect_uri' => '',
            'scope' => '',
        ], $options);

        if (!$options['client_id'] || !$options['client_secret'] || !$options['redirect_uri']) {
            throw new InvalidArgumentException(sprintf("Empty client id or secret or redirect uri"));
        }
        $this->options = $options;
    }
}
