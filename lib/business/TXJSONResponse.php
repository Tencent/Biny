<?php
/**
 * JSON response class
 */
class TXJSONResponse {
    private $data;

    /**
     * 构造函数
     * @param $data
     * @param bool $encode
     */
    public function __construct($data, $encode=true)
    {
        $data = TXString::recursionEncode($data, $encode);
        if (SYS_CONSOLE && TXLogger::$ConsoleOut){
            TXLogger::format();
            $data['__logs'] = TXLogger::$ConsoleOut;
            TXLogger::$ConsoleOut = array();
        }
        $this->data = $data;
    }

    function __toString()
    {
//        ob_clean();
        return json_encode($this->data, JSON_UNESCAPED_UNICODE) ?: json_last_error_msg();
    }
}