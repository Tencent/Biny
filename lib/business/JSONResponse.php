<?php
/**
 * Tencent is pleased to support the open source community by making Biny available.
 * Copyright (C) 2017 THL A29 Limited, a Tencent company. All rights reserved.
 * Licensed under the BSD 3-Clause License (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * https://opensource.org/licenses/BSD-3-Clause
 * Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
 * JSON response class
 */

namespace biny\lib;

class JSONResponse {
    private $data;

    /**
     * 构造函数
     * @param $data
     * @param bool $encode
     */
    public function __construct($data, $encode=true)
    {
        $data = $encode ? BinyString::recursionEncode($data) : $data;
        if (SYS_CONSOLE && Logger::$ConsoleOut){
            Logger::format();
            $data['__logs'] = Logger::$ConsoleOut;
            Logger::$ConsoleOut = [];
        }
        $this->data = $data;
    }

    function __toString()
    {
//        ob_clean();
        return json_encode($this->data, JSON_UNESCAPED_UNICODE) ?: json_last_error_msg();
    }
}