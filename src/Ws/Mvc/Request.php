<?php namespace Ws\Mvc;

/**
 * 封装一个请求
 */
class Request
{
    
    public function __construct($pathinfo='')
    {        
        $this->pathinfo = self::fmtPathinfo($pathinfo);
    }

    public function pathinfo()
    {
        return $this->pathinfo;
    }

    public static function fmtPathinfo($pathinfo)
    {
        $pathinfo = trim($pathinfo);
        if ( empty($pathinfo) )
        {
            $pathinfo = '/';
        }
        return $pathinfo;
    }

    /**
     * 取得请求的 URI 信息（不含协议、主机名）
     *
     * 例如：
     *
     * http://m.x/admin/index.php?controller=test
     *
     * 返回：
     *
     * /admin/index.php?controller=test
     *
     * @return string
     */
    public static function get_request_uri()
    {
        static $request_uri = null;
        if (!is_null($request_uri)) return $request_uri;

        if (isset($_SERVER['HTTP_X_REWRITE_URL']))
        {
            $request_uri = $_SERVER['HTTP_X_REWRITE_URL'];
        }
        elseif (isset($_SERVER['REQUEST_URI']))
        {
            $request_uri = $_SERVER['REQUEST_URI'];
        }
        elseif (isset($_SERVER['ORIG_PATH_INFO']))
        {
            $request_uri = $_SERVER['ORIG_PATH_INFO'];
            if (!empty($_SERVER['QUERY_STRING']))
            {
                $request_uri .= '?' . $_SERVER['QUERY_STRING'];
            }
        }
        else
        {
            $request_uri = '';
        }

        return $request_uri;
    }

    /**
     * 取得请求的 URI 信息（不含协议、主机名、查询参数、PATHINFO）
     *
     * 例如：
     *
     * http://m.x/admin/index.php?controller=test
     * http://m.x/admin/index.php/path/to
     *
     * 都返回：
     *
     * /admin/index.php
     *
     * @return string
     */
    public static function get_request_baseuri()
    {
        static $request_base_uri = null;
        if (!is_null($request_base_uri)) return $request_base_uri;

        $filename = basename($_SERVER['SCRIPT_FILENAME']);

        if (basename($_SERVER['SCRIPT_NAME']) === $filename)
        {
            $url = $_SERVER['SCRIPT_NAME'];
        }
        elseif (basename($_SERVER['PHP_SELF']) === $filename)
        {
            $url = $_SERVER['PHP_SELF'];
        }
        elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename)
        {
            $url = $_SERVER['ORIG_SCRIPT_NAME']; // 1and1 shared hosting compatibility
        }
        else
        {
            // Backtrack up the script_filename to find the portion matching
            // php_self
            $path = $_SERVER['PHP_SELF'];
            $segs = explode('/', trim($_SERVER['SCRIPT_FILENAME'], '/'));
            $segs = array_reverse($segs);
            $index = 0;
            $last = count($segs);
            $url = '';
            do
            {
                $seg = $segs[$index];
                $url = '/' . $seg . $url;
                ++ $index;
            } while (($last > $index) && (false !== ($pos = strpos($path, $url))) && (0 != $pos));
        }

        // Does the baseUrl have anything in common with the request_uri?
        $request_uri = self::get_request_uri();

        if (0 === strpos($request_uri, $url))
        {
            // full $url matches
            $request_base_uri = $url;
            return $request_base_uri;
        }

        if (0 === strpos($request_uri, dirname($url)))
        {
            // directory portion of $url matches
            $request_base_uri = rtrim(dirname($url), '/') . '/';
            return $request_base_uri;
        }

        if (! strpos($request_uri, basename($url)))
        {
            // no match whatsoever; set it blank
            $request_base_uri = '';
            return '';
        }

        // If using mod_rewrite or ISAPI_Rewrite strip the script filename
        // out of baseUrl. $pos !== 0 makes sure it is not matching a value
        // from PATH_INFO or QUERY_STRING
        if ((strlen($request_uri) >= strlen($url))
            && ((false !== ($pos = strpos($request_uri, $url)))
            && ($pos !== 0)))
        {
            $url = substr($request_uri, 0, $pos + strlen($url));
        }

        $request_base_uri = rtrim($url, '/') . '/';
        return $request_base_uri;
    }

    /**
     * 取得响应请求的 .php 文件在 URL 中的目录部分
     *
     * 例如：
     *
     * http://m.x/admin/index.php?controller=test
     *
     * 返回：
     *
     * /admin/
     *
     * @return string
     */
    public static function get_request_dir()
    {
        static $dir = null;
        
        $base_uri = self::get_request_baseuri();
        if (substr($base_uri, - 1, 1) == '/')
        {
            $dir = $base_uri;
        }
        else
        {
            $dir = dirname($base_uri);
        }

        $dir = rtrim($dir, '/\\') . '/';
        return $dir;    
    }

    /**
     * 返回 PATHINFO 信息
     *
     * 例如：
     *
     * http://m.x/admin/index.php/path/to
     *
     * 返回：
     *
     * /path/to
     *
     * @return string
     */
    public static function get_request_pathinfo()
    {
        static $pathinfo = null;
        if (!is_null($pathinfo)) return $pathinfo;
        
        if (!empty($_SERVER['PATH_INFO'])) 
        {
            $pathinfo = $_SERVER['PATH_INFO'];
            return $pathinfo;
        }

        $base_url = self::get_request_baseuri();

        if (null === ($request_uri = self::get_request_uri())) return '';

        // Remove the query string from REQUEST_URI
        if (($pos = strpos($request_uri, '?')))
        {
            $request_uri = substr($request_uri, 0, $pos);
        }

        if ((null !== $base_url) && (false === ($pathinfo = substr($request_uri, strlen($base_url)))))
        {
            // If substr() returns false then PATH_INFO is set to an empty string
            $pathinfo = '';
        }
        elseif (null === $base_url)
        {
            $pathinfo = $request_uri;
        }
        return $pathinfo;
    }

    public static function is_post()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    public static function is_ajax()
    {
        return strtolower(self::get_http_header('X_REQUESTED_WITH')) == 'xmlhttprequest';
    }

    public static function is_flash()
    {
        return strtolower(self::get_http_header('USER_AGENT')) == 'shockwave flash';
    }

    public static function get_http_header($header)
    {
        $name = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        return self::server($name, '');
    }

    public static function referer()
    {
        return self::get_http_header('REFERER');
    }

    public static function request($name, $default = null)
    {
        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
    }

    public static function get($name, $default = null)
    {
        return isset($_GET[$name]) ? $_GET[$name] : $default;
    }

    public static function post($name, $default = null)
    {
        return isset($_POST[$name]) ? $_POST[$name] : $default;
    }

    public static function cookie($name, $default = null)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
    }

    public static function session($name, $default = null)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
    }

    public static function server($name, $default = null)
    {
        return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
    }

    public static function env($name, $default = null)
    {
        return isset($_ENV[$name]) ? $_ENV[$name] : $default;
    }
}
