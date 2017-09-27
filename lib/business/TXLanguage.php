<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 16-7-19
 * Time: 下午5:54
 */
class TXLanguage
{
    private static $language = null;
    private static $contents = null;

    /**
     * 获取语言
     * @param $lang
     * @return null|string
     */
    public static function getLanguage($lang=null)
    {
        if (self::$language === null){
            self::$language = $lang ?: (isset($_COOKIE['biny_language']) ? $_COOKIE['biny_language'] : '');
        }
        return $lang ?: self::$language;
    }

    /**
     * 获取tpl内容
     * @param $lang
     */
    private static function getContents($lang)
    {
        if (self::$contents === null){
            $path = TXApp::$base_root . DS . 'language' . DS . $lang .'.php';
            self::$contents = is_readable($path) ? require($path) : [];
        }
    }

    /**
     * 获取翻译
     * @param $content
     * @return mixed
     */
    public static function getContent($content)
    {
        $lang = self::getLanguage();
        if ($lang){
            self::getContents($lang);
            $content = isset(self::$contents[$content]) ? self::$contents[$content] : $content;
        }
        return $content;
    }
}