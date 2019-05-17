<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * String helper class
 */

namespace biny\lib;

class BinyString
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
     * @return array
     */
    public static function recursionEncode(&$array)
    {
        $newArray = [];
        foreach ($array as $key => $arr){
            if (is_array($arr)){
                $arr = self::recursionEncode($arr);
            } elseif (is_string($arr)){
                $arr = self::encode($arr);
            } elseif ($arr instanceof Response){
                $arr = $arr->getContent();
            }
            $key = is_string($key) ? self::encode($key) : $key;
            $newArray[$key] = $arr;
        }
        unset($array);
        return $newArray;
    }
}