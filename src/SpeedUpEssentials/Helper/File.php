<?php

namespace SpeedUpEssentials\Helper;

class File {

    function encode_url($url) {
        $u = explode('?', $url, 2);
        if (isset($u[1])) {
            parse_str($u[1], $data);
            $url = $u[0] . '?' . http_build_query($data);
        }
        return $url;
    }

    public static function url_decode($url) {
        return htmlspecialchars_decode(urldecode($url));
    }

    public static function get_content($URL, $showcomments = true) {

        $protocol = PROTOCOL? : 'http:';
        if (substr($URL, 0, 2) == '//') {
            $URL = $protocol . $URL;
        }
        $url_exec = self::encode_url($URL);
        if (preg_match('#^https?://#', $url_exec)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_USERAGENT, filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'));
            curl_setopt($ch, CURLOPT_URL, $url_exec);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_REFERER, $protocol . '//' . $_SERVER['HTTP_HOST'] . '/');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $data = @curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($code != 200) {
                $data = '';
            }
            curl_close($ch);
        } else {
            $data = @file_get_contents($url_exec);
        }
        if (!$data) {
            $data = $showcomments ? '/*Content of ' . $url_exec . ': <Empty>*/' . PHP_EOL : '';
        } else {
            $data = $showcomments ? '/*File: (' . $url_exec . ')*/' . PHP_EOL . $data : $data;
        }
        return $data;
    }

    public static function put_content($filename, $data) {
        //return file_put_contents($filename, stripslashes($data));
        try {
            $fp = fopen($filename, 'w');
            $return = fwrite($fp, $data);
            fclose($fp);
            return $return;
        } catch (Exception $ex) {
            
        }
    }

}
