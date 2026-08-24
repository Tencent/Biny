<?php
/**
 * Global helper functions (auto-loaded by Composer via "files" entry).
 * Keep this file tiny — one function per concern.
 */

use biny\lib\Language;

if (!function_exists('_L')) {
    /**
     * 获取多语言文本；若未启用语言则原样返回
     * @param string $content
     * @return mixed
     */
    function _L($content)
    {
        return Language::getLanguage() ? Language::getContent($content) : $content;
    }
}
