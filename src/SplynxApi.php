<?php
/**
 * Splynx API v. 1.0
 * REST API Class
 * Author: Ruslan Malymon (Top Net Media s.r.o.)
 * https://splynx.com/wiki/index.php/API - documentation
 */

class SplynxApi
{
    private $api_key;
    private $api_secret;
    private $nonce_v;
    private $url;
    private $version = '1.0';

    public $debug = false;

    public $result;
    public $response;
    public $response_code;

    /** @var string Hash of admin session id. Will be send in $_GET['sash'] in add-ons requests */
    private $sash;

    /**
     * Create Splynx API object
     *
     * @param $url
     * @param $api_key
     * @param $api_secret
     */
    public function __construct($url, $api_key, $api_secret)
    {
        $this->url = $url . 'api/' . $this->version;
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
        $this->nonce();
    }

    /**
     * Create signature for API call validation
     * @return string hash
     */
    private function signature()
    {
        // Create string
        $string = $this->nonce_v . $this->api_key;

        // Create hash
        $hash = hash_hmac('sha256', $string, $this->api_secret);
        $hash = strtoupper($hash);

        return $hash;
    }

    /**
     * Set nonce as timestamp
     */
    private function nonce()
    {
        $this->nonce_v = round(microtime(true) * 100);
    }

    /**
     * Send curl request to Splynx API
     *
     * @param string $method Method: get, delete, put, post
     * @param string $url
     * @param array $param
     * @return array JSON results
     */
    private function curl_process($method, $url, $param = array())
    {
        $ch = curl_init();

        if ($this->debug == true) {
            print $method . " to " . $url . "\n";
            print_r($param);
        }

        $headers = array();
        $headers[] = 'Content-type: application/json';
        $auth_str = $this->make_auth();
        $headers[] = 'Authorization: Splynx-EA (' . $auth_str . ')';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($param));
        }

        if ($method == 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($param));
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Splynx PHP API ' . $this->version);

        if ($this->debug == true) {
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
//            curl_setopt($ch, CURLOPT_HEADER, 0);
        }

        $out = curl_exec($ch);

        if (curl_errno($ch)) {
            trigger_error("cURL failed. Error #" . curl_errno($ch) . ": " . curl_error($ch), E_USER_ERROR);
        }

        $this->response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($this->debug == true) {
            var_dump($out);
        }

        $this->result = false;

        switch ($method) {
            case 'POST':
                if ($this->response_code == 201) {
                    $this->result = true;
                }
                break;

            case 'PUT':
                if ($this->response_code == 202) {
                    $this->result = true;
                }
                break;

            case 'DELETE':
                if ($this->response_code == 204) {
                    $this->result = true;
                }
                break;

            default:
                if ($this->response_code == 200) {
                    $this->result = true;
                }
                break;
        }

        $this->response = json_decode($out, true);

        return $this->result;
    }

    /**
     * Make Splynx Extended Authorization string
     *
     * @return string of Splynx EA
     */
    private function make_auth()
    {
        $auth = array(
            'key' => $this->api_key,
            'signature' => $this->signature(),
            'nonce' => $this->nonce_v++
        );

        // Add $sash is needed
        if ($this->sash !== null) {
            $auth['sash'] = $this->sash;
        }

        return http_build_query($auth);
    }

    private function getUrl($path, $id = null)
    {
        $url = $this->url . '/' . $path;
        if ($id !== null) {
            $url .= '/' . $id;
        }
        return $url;
    }

    /**
     * Send API call GET to Splynx API
     *
     * @param $path
     * @param string $id
     * @return array
     */
    public function api_call_get($path, $id = null)
    {
        return $this->curl_process('GET', $this->getUrl($path, $id));
    }

    /**
     * Send API call DELETE to Splynx API
     *
     * @param string $path
     * @param integer $id
     * @return array JSON results
     */
    public function api_call_delete($path, $id)
    {
        if (empty($id)) return false;
        return $this->curl_process('DELETE', $this->getUrl($path, $id));
    }

    /**
     * Send API call POST (add) to Splynx API
     *
     * @param string $path
     * @param array $params
     * @return array JSON results
     */
    public function api_call_post($path, $params)
    {
        if (empty($params)) return false;
        return $this->curl_process('POST', $this->getUrl($path), $params);
    }

    /**
     * Send API call PUT (update) to Splynx API
     *
     * @param string $path
     * @param integer $id
     * @param array $params
     * @return array JSON results
     */
    public function api_call_put($path, $id, $params)
    {
        if (empty($params)) return false;
        if (empty($id)) return false;
        return $this->curl_process('PUT', $this->getUrl($path, $id), $params);
    }

    /**
     * Get $sash
     *
     * @return string
     */
    public function getSash()
    {
        return $this->sash;
    }

    /**
     * Set $sash
     *
     * @param string $sash
     */
    public function setSash($sash)
    {
        $this->sash = $sash;
    }

}
