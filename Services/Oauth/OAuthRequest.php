<?php

namespace Intracto\LTIConsumerBundle\Services\Oauth;

class OAuthRequest
{
    const OAUTH_VERSION = '1.0';

    const OAUTH_SIGNATURE_METHOD = 'HMAC-SHA1';

    private $parameters;

    private $method;

    private $url;

    private $baseUrl;

    private $key;

    private $secret;

    /**
     * OauthProvider constructor.
     *
     * @param        $parameters
     * @param        $url
     * @param        $baseUrl
     * @param        $key
     * @param        $secret
     * @param string $method
     */
    public function __construct($parameters, $url, $baseUrl = null, $key, $secret, $method = 'POST')
    {
        $this->parameters = $parameters;
        $this->method = $method;
        $this->url = $url;
        $this->baseUrl = ($baseUrl) ? $baseUrl : $url;
        $this->key = $key;
        $this->secret = $secret;
    }

    public function signRequest($token = '')
    {
        $oauth_params = array(
            'oauth_version' => self::OAUTH_VERSION,
            'oauth_signature_method' => self::OAUTH_SIGNATURE_METHOD,
            'oauth_nonce' => $this->generateNonce(),
            'oauth_timestamp' => time(),
            'oauth_consumer_key' => $this->key,
        );

        $this->parameters = array_merge($oauth_params, $this->parameters);

        $base_string = $this->getBaseString();

        $key_parts = array($this->secret, $token);
        $key_parts = $this->urlencode_rfc3986($key_parts);
        $key = implode('&', $key_parts);

        $computed_signature = base64_encode(hash_hmac('sha1', $base_string, $key, true));
        $this->parameters = array_merge($this->parameters, array('oauth_signature' => $computed_signature));

        return $this->parameters;
    }

    private function generateNonce()
    {
        $mt = microtime();
        $rand = mt_rand();

        return md5($mt.$rand);
    }

    public function getBaseString()
    {
        $parts = array(
            $this->method,
            $this->getNormalizedUrl(),
            $this->getSignableParameters(),
        );

        $parts = $this->urlencode_rfc3986($parts);

        return implode('&', $parts);
    }

    public function getNormalizedUrl()
    {
        $parts = parse_url($this->baseUrl);
        $port = @$parts['port'];
        $scheme = $parts['scheme'];
        $host = $parts['host'];
        $path = @$parts['path'];

        $port or $port = ('https' == $scheme) ? '443' : '80';

        if (('https' == $scheme && '443' != $port)
            || ('http' == $scheme && '80' != $port)) {
            $host = "$host:$port";
        }

        return "$scheme://$host$path";
    }

    private function getSignableParameters()
    {
        $params = $this->parameters;

        // Remove oauth_signature if present
        // Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.")
        if (isset($params['oauth_signature'])) {
            unset($params['oauth_signature']);
        }

        return $this->buildHttpQuery($params);
    }

    private function buildHttpQuery($params)
    {
        if (!$params) {
            return '';
        }

        // Urlencode both keys and values
        $keys = $this->urlencode_rfc3986(array_keys($params));
        $values = $this->urlencode_rfc3986(array_values($params));
        $params = array_combine($keys, $values);

        // Parameters are sorted by name, using lexicographical byte value ordering.
        // Ref: Spec: 9.1.1 (1)
        uksort($params, 'strcmp');

        $pairs = array();
        foreach ($params as $parameter => $value) {
            if (is_array($value)) {
                // If two or more parameters share the same name, they are sorted by their value
                // Ref: Spec: 9.1.1 (1)
                natsort($value);
                foreach ($value as $duplicate_value) {
                    $pairs[] = $parameter.'='.$duplicate_value;
                }
            } else {
                $pairs[] = $parameter.'='.$value;
            }
        }
        // For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
        // Each name-value pair is separated by an '&' character (ASCII code 38)
        return implode('&', $pairs);
    }

    public function urlencode_rfc3986($input)
    {
        if (is_array($input)) {
            return array_map('self::urlencode_rfc3986', $input);
        } elseif (is_scalar($input)) {
            return str_replace(
                '+',
                ' ',
                str_replace('%7E', '~', rawurlencode($input))
            );
        } else {
            return '';
        }
    }

    /**
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param mixed $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param mixed $secret
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }
}
