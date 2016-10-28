<?php namespace Ws\Plugins\Hadoop\Tools;

use Ws\Debug\Tracks;

class Curl
{
    private $debug;
    private $lastRequestContentResult;
    private $lastRequestInfoResult;

    public function __construct($debug = false)
    {
        $this->debug = $debug;
    }

    public function getWithRedirect($url)
    {
        return $this->get($url, [CURLOPT_FOLLOWLOCATION => true]);
    }

    public function get($url, $options = [])
    {
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_RETURNTRANSFER] = true;
        return $this->_exec($options);
    }

    public function putLocation($url)
    {
        return $this->_findRedirectUrl($url, [CURLOPT_PUT => true]);
    }

    public function postLocation($url)
    {
        return $this->_findRedirectUrl($url, [CURLOPT_POST => true]);
    }

    private function _findRedirectUrl($url, $options)
    {
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_HEADER] = true;
        $options[CURLINFO_EFFECTIVE_URL] = true;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $header = $this->_exec($options);
        $matches = [];
        if (preg_match('/Location:(.*?)\n/', $header, $matches)) {
            $redirectUrl = trim($matches[1]);
            return $redirectUrl;
        }
        return null;
    }

    public function putFile($url, $filename)
    {
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_PUT] = true;
        $handle = fopen($filename, "r");
        $options[CURLOPT_INFILE] = $handle;
        $options[CURLOPT_INFILESIZE] = filesize($filename);

        $info = $this->_exec($options, true);

        return ('201' == $info['http_code']);
    }

    public function putData($url, $data, $contentType = 'application/json')
    {
        $options[CURLOPT_URL] = $url;
        // $options[CURLOPT_PUT] = true;
        $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
        $options[CURLOPT_POSTFIELDS] = $data;
        /*
        $options[CURLOPT_HTTPHEADER] = array(
            'Content-Length: '.strlen($data)
        );
        */
        $info = $this->_exec($options, true);

        return ('201' == $info['http_code']);
    }

    public function postString($url, $string)
    {
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = $string;

        $info = $this->_exec($options, true);

        return ('200' == $info['http_code']);
    }

    public function put($url)
    {
        $options = [];
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_PUT] = true;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_INFILESIZE] = 0;

        return $this->_exec($options);
    }

    public function post($url)
    {
        $options = [];
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_RETURNTRANSFER] = true;

        return $this->_exec($options);
    }

    public function delete($url)
    {
        $options = [];
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_CUSTOMREQUEST] = "DELETE";
        $options[CURLOPT_RETURNTRANSFER] = true;

        return $this->_exec($options);
    }

    public function getLastRequestContentResult()
    {
        return $this->lastRequestContentResult;
    }

    public function getLastRequestInfoResult()
    {
        return $this->lastRequestInfoResult;
    }

    public function validateLastRequest()
    {
        $http_code = $this->getLastRequestInfoResult()['http_code'];
        if ($http_code >= 400 && $http_code <= 500) {
            return false;
        }
        return true;
    }

    private function _exec($options, $returnInfo = false)
    {
        $ch = curl_init();
        if ($this->debug === true) {
            $options[CURLOPT_VERBOSE] = true;
        }
        if (!isset($options[CURLOPT_RETURNTRANSFER])) {
            $options[CURLOPT_RETURNTRANSFER] = true;
        }
        if (!isset($options[CURLOPT_HTTPHEADER])) {
            $options[CURLOPT_HTTPHEADER] = ['Expect: '];
        } else {
            $options[CURLOPT_HTTPHEADER] = array_merge($options[CURLOPT_HTTPHEADER], ['Expect: ']);
        }
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        $this->lastRequestContentResult = $result;
        $this->lastRequestInfoResult = curl_getinfo($ch);
        if ($returnInfo) {
            $result = $this->lastRequestInfoResult;
        }

        curl_close($ch);
        
        Tracks::instance()->track('hadoop:curl', [
                'request' => $options,
                'response' => $result,
            ]);

        return $result;
    }

}