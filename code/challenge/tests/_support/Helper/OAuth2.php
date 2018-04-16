<?php
namespace Helper;

use Log;

/**
 * Helper to login with OAuth2 (laravel Passport)
 *
 * @author Andres Estepa <caestepa05@gmail.com>
 */
class OAuth2 extends \Codeception\Module
{

    /**
     *
     * @var type
     */
    private $token_value = null;

    /**
     *
     * @var type
     */
    private $token_type = null;

    /**
     *
     * @var type
     */
    private $auth2_active = false;

    public function getTokenType()
    {
        return $this->token_type;
    }
    public function getTokenValue()
    {
        return $this->token_value;
    }
    public function isOAuth2Active()
    {
        return $this->auth2_active;
    }

    /**
     * Logs in to the system using OAuth2
     * @param type $data
     * @return boolean
     */
    public function loginAuth2($data, $code_expected = 200)
    {
        $rest = $this->getModule('REST');
        // The validation endpoint does not uses the prefix
        // $previus_url = $rest->_getConfig('url');
        // $rest->_reconfigure(['url' => '']);
        $rest->haveHttpHeader('Content-Type', 'application/json');
        $rest->sendPOST('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => 3,
            'client_secret' => 'secret',
            'username' => $data['username'],
            'password' => $data['password'],
            'scope' => '*'
        ]);
        $rest->seeResponseCodeIs($code_expected);
        $rest->seeResponseIsJSON();
        // $rest->_reconfigure(['url' => $previus_url]);
        if ($code_expected !== 200) {
            return true;
        }
        $response = $rest->grabResponse();
        $response = json_decode($response, true);
        $this->token_value  = $response['access_token'];
        $this->token_type   = $response['token_type'];
        $this->auth2_active = true;
        return true;
    }

    /**
     * Sends a post thru autenticated
     *
     * @param string $url
     * @param array $params
     * @param array $files
     */
    public function sendPOST($url, $params = [], $files = [])
    {
        if ($this->isOAuth2Active()) {
            $this->getModule('REST')->haveHttpHeader('Authorization', $this->getTokenType() .' '. $this->getTokenValue());
        }
        $this->getModule('REST')->sendPOST($url, $params, $files);
    }

    /**
     * Sends a patch to the url with oAuth2
     *
     * @param string $url
     * @param array $params
     * @param array $files
     */
    public function sendPATCH($url, $params = [], $files = [])
    {
        if ($this->isOAuth2Active()) {
            $this->getModule('REST')->haveHttpHeader('Authorization', $this->getTokenType() .' '. $this->getTokenValue());
        }
        $this->getModule('REST')->sendPATCH($url, $params, $files);
    }

    /**
     * Sends a get with oAuth2
     *
     * @param string $url
     * @param array $params
     */
    public function sendGET($url, $params = [])
    {
        if ($this->isOAuth2Active()) {
            $this->getModule('REST')->haveHttpHeader('Authorization', $this->getTokenType() .' '. $this->getTokenValue());
        }
        $this->getModule('REST')->sendGET($url, $params);
    }

    /**
     * Sends a DELETE request with oAuth2
     *
     * @param string $url
     * @param array $params
     * @param array $files
     */
    public function sendDELETE($url, $params = [], $files = [])
    {
        if ($this->isOAuth2Active()) {
            $this->getModule('REST')->haveHttpHeader('Authorization', $this->getTokenType() .' '. $this->getTokenValue());
        }
        $this->getModule('REST')->sendDELETE($url, $params, $files);
    }

    /**
     *
     * @param string $url
     * @param array $params
     * @param array $files
     */
    public function sendPUT($url, $params = [], $files = [])
    {
        if ($this->isOAuth2Active()) {
            $this->getModule('REST')->haveHttpHeader('Authorization', $this->getTokenType() .' '. $this->getTokenValue());
        }
        $this->getModule('REST')->sendPUT($url, $params, $files);
    }

    /**
     * Logs out from the system with the current user
     */
    public function logout()
    {
        if ($this->isOAuth2Active()) {
            $this->auth2_active = false;
            $this->token_type   = null;
            $this->token_value  = false;
        }
        $this->getModule('REST')->haveHttpHeader('Authorization', null);
    }
}