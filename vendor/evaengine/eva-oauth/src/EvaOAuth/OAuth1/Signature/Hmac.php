<?php
/**
 * @author    AlloVince
 * @copyright Copyright (c) 2015 EvaEngine Team (https://github.com/EvaEngine)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Eva\EvaOAuth\OAuth1\Signature;

/**
 * Class Hmac
 * @package Eva\EvaOAuth\OAuth1\Signature
 */
class Hmac implements SignatureInterface
{
    /**
     * @var string
     */
    protected $secert;

    /**
     * @var string
     */
    protected $input;

    /**
     * @var string
     */
    protected $tokenSecret;


    public static function verify($str)
    {
        //TODO: verify logic
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return base64_encode(hash_hmac('sha1', $this->input, $this->secret . '&' . $this->tokenSecret, true));
    }

    /**
     * @param $input
     * @param $secret
     * @param string $tokenSecret
     */
    public function __construct($input, $secret, $tokenSecret = null)
    {
        $this->secret = (string) rawurlencode($secret);
        $this->input = (string) $input;
        $this->tokenSecret = $tokenSecret ? (string) rawurlencode($tokenSecret) : '';
    }
}
