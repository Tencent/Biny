<?php
/**
 * String helper class
 */
class TXString
{
    /**
     * 中英文长度截取
     * @param $str @截取字符串
     * @param int $len @需要截取的长度
     * @return string
     */
    public static function cut_chinese($str, $len=10)
    {
        $str = trim($str);
        $result = '';
        $length = 0;
        $inc = 1;
        for ($j = 0; $j < strlen($str); $j += $inc) {
            if ($length < $len) {
                if (ord($str[$j]) == 194){
                    $inc = 2;
                    $length += 1;
                } else if (ord($str[$j]) > 128) {
                    $inc = 3;
                    $length += 2;
                } else {
                    $inc = 1;
                    $length += 1;
                }
                $result .= substr($str, $j, $inc);
            } else {
                $result = substr($result, 0, strlen($result) - $inc) . "...";
                break;

            }
        }
        return $result;
    }

    /**
     * 实体化转义
     * @param $content
     * @return string
     */
    public static function encode($content)
    {
        return is_string($content) ? htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE) : $content;
    }

    /**
     * 实体化转义
     * @param $content
     * @return string
     */
    public static function decode($content)
    {
        return is_string($content) ? htmlspecialchars_decode($content, ENT_QUOTES) : $content;
    }

    /**
     * 递归替换Encode
     * @param $array
     * @param bool $encode
     * @return array
     */
    public static function recursionEncode(&$array, $encode=true)
    {
        $newArray = array();
        foreach ($array as $key => $arr){
            if (is_array($arr)){
                $arr = self::recursionEncode($arr, $encode);
            } elseif (is_string($arr)){
                $arr = $encode ? self::encode($arr) : $arr;
            } elseif ($arr instanceof TXResponse){
                $arr = $arr->getContent();
            }
            $key = (is_string($key) && $encode) ? self::encode($key) : $key;
            $newArray[$key] = $arr;
        }
        unset($array);
        return $newArray;
    }
}