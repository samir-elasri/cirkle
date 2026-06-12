<?php

namespace Mbiance\AdminUtility;

class StringUtility {

    public function __construct() {
    }

    public function sluggify($string, $isUrl = false, $separator = '-') {
        $string = $this->replaceAccent($string);

        $regex = $isUrl ? '/[^A-Za-z0-9-\/]]+/' : '/[^A-Za-z0-9-]+/';

        return strtolower(preg_replace($regex, $separator, $string));
    }

    public function replaceAccent($string) {

        $unwanted_array = array(  'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', '!'=>'', '.'=>'' );
        $string = strtr( $string, $unwanted_array );
        return $string;
    }

    public function lowerAccent($string) {

        $unwanted_array = array('À'=>'à', 'Â'=>'â', 'Ä'=>'ä', 'È'=>'è', 'É'=>'é', 'Ê'=>'ê', 'Ë'=>'ë', 'Î'=>'î', 'Ï'=>'ï', 'Ô'=>'ô', 'Ö'=>'ö', 'Ù'=>'ù', 'Û'=>'û', 'Ü'=>'ü');
        $string = strtr( $string, $unwanted_array );
        return $string;
    }

    public function zerofill($number, $zerofill = 3) {

        return str_pad($number, $zerofill, '0', STR_PAD_LEFT);
    }

    /**
     * get youtube video ID from URL
     *
     * @param string $url
     * @return string Youtube video id or empty if none found.
     */
    public function getYouTubeIdFromUrl($url) {
        $pattern =
            '%^# Match any youtube URL
                (?:https?://)?  # Optional scheme. Either http or https
                (?:www\.)?      # Optional www subdomain
                (?:             # Group host alternatives
                youtu\.be/      # Either youtu.be,
                | youtube\.com  # or youtube.com
                (?:             # Group path alternatives
                /embed/         # Either /embed/
                | /v/           # or /v/
                | /watch\?v=    # or /watch\?v=
                )               # End path alternatives.
                )               # End host alternatives.
                ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
                $%x'
            ;
        $result = preg_match($pattern, $url, $matches);

        if ($result !== 0) {
            return $matches[1];
        }
        return '';
    }

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function text2Html($string) {

       $string = '<p>' . str_replace("\r\n", '</p><p>', $string) . '</p>';
       $string = str_replace('<p></p>', '', $string);
       $string = str_replace('<p>&nbsp;</p>', '', $string);
       return $string;
    }

    public function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function convertSpecial($string)
    {
        return preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1' . chr(255) . '$2', htmlentities($string, ENT_QUOTES, 'UTF-8'));
    }

    public function setList($collection, $tags)
    {

        $arr = array();
        foreach ($tags as $tag) {
            if (isset($collection[$tag])) {
                array_push($arr, $collection[$tag]);
            }
        }
        return $arr;
    }
}