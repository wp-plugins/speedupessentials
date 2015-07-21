<?php

namespace SpeedUpEssentials\Helper;

class Url {

    private static $staticDomain;
    private static $baseUri;

    public static function getStaticDomain() {
        return self::$staticDomain;
    }

    public static function getBaseUri() {
        return self::$baseUri;
    }

    public static function setStaticDomain($staticDomain) {
        self::$staticDomain = $staticDomain;
    }

    public static function setBaseUri($baseUri) {
        self::$baseUri = $baseUri;
    }

    public static function getUri($url, $domain) {
        return str_replace($domain, '', $url)? : '/';
    }

    public static function normalizeUrl($url, $remove_host = false) {
        //if data, return
        if (substr($url, 0, 5) == 'data:') {
            return $url;
        }
        //if php file, return        
        $ext = pathinfo(strtok($url, '?'), PATHINFO_EXTENSION);
        if ($ext == 'php') {
            return $url;
        }
        //if external URL, return
        if ((substr($url, 0, 2) == '//' && !preg_match('#^//' . $_SERVER['HTTP_HOST'] . '#', $url)) || (preg_match('#^https?://#', $url) && !preg_match('#^https?://' . $_SERVER['HTTP_HOST'] . '#', $url))) {
            return $url;
        }
        //if same domain, return static domain
        if (preg_match('#^https?://' . $_SERVER['HTTP_HOST'] . '#', $url)) {
            return preg_replace('#^https?://' . $_SERVER['HTTP_HOST'] . '#', '//' . self::$staticDomain, $url);
        }
        //if same domain, return static domain
        if (preg_match('#^//' . $_SERVER['HTTP_HOST'] . '#', $url)) {
            return preg_replace('#^//' . $_SERVER['HTTP_HOST'] . '#', '//' . self::$staticDomain, $url);
        }
        //if relative url
        if ($url['0'] == '/') {
            return '//' . self::$staticDomain . $url;
        } else {
            return '//' . self::$staticDomain . self::$baseUri . $url;
        }
    }

}
