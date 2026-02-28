/**
 * Created by billge on 15-3-17.
 */
//ajax.js
jQuery(document).ajaxSend(function(event, xhr, settings) {
    function getCookie(name) {
        var reg = new RegExp("(^| )" + name + "(?:=([^;]*))?(;|$)"), val = document.cookie.match(reg);
        return val ? (val[2] ? unescape(val[2]) : "") : null;
    }
    if (typeof xhr.setRequestHeader == "function"){
        if (getCookie('csrf-token')){
            xhr.setRequestHeader('X-CSRF-TOKEN', getCookie('csrf-token'));
        }
    }
});

jQuery(document).ajaxSuccess(function(event,xhr,options){
    try {
        var ret = $.parseJSON(xhr.responseText);
        if (ret.__logs){
            var logs = ret.__logs;
            for (var i in logs){
                var log = logs[i];
                if (log instanceof Function){
                    continue;
                }
                switch (log['type']){
                    case 'log':
                        console.log("{0} => ".format(log['key']), log['value']);
                        break;

                    case 'info':
                        console.info("{0} => ".format(log['key']), log['value']);
                        break;

                    case 'error':
                        console.error("{0} => ".format(log['key']), log['value']);
                        break;

                    case 'warn':
                        console.warn("{0} => ".format(log['key']), log['value']);
                        break;

                    default :
                        console.log("{0} => ".format(log['key']), log['value']);
                        break;

                }
            }
        }
    } catch (e){}
});

if (!String.prototype.format) {
    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] != 'undefined'
                ? args[number] : match;
        });
    };
}

if (!String.prototype.gblen) {
    String.prototype.gblen = function () {
        var len = 0;
        for (var i = 0; i < this.length; i++) {
            if (this.charCodeAt(i) > 127 || this.charCodeAt(i) == 94) {
                len += 2;
            } else {
                len++;
            }
        }
        return len;
    }
}

if (!String.prototype.cutString) {
    String.prototype.cutString = function (max, start) {
        start = start || 0;
        var string = '';
        var len = 0;
        var x = 1;
        for (var i = 0; i < this.length; i++) {
            if (len >= max){
                break;
            }
            if (this.charCodeAt(i) > 127 || this.charCodeAt(i) == 94) {
                x = 2;
            } else {
                x = 1;
            }
            if(start > 0){
                start -= x;
            } else {
                len += x;
            }
            (len > 0 && len <= max) && (string += this[i]);
        }
        return string;
    }
}

if (!Date.prototype.getString){
    Date.prototype.getString = function (day, month, year) {
        day = parseInt(day) || 0;
        month = parseInt(month) || 0;
        year = parseInt(year) || 0;
        //生成新的日期
        var tmp = new Date(this.getFullYear()+year, this.getMonth()+month, this.getDate()+day);
        var y = tmp.getFullYear();
        var m = ((tmp.getMonth()+1).toString().length == 2) ? tmp.getMonth()+1 : ("0"+(tmp.getMonth()+1).toString());//获取当前月份的日期
        var d = (tmp.getDate().toString().length == 2) ? tmp.getDate() : ("0"+tmp.getDate().toString());
        return y+"-"+m+"-"+d;
    }
}

//写入COOKIES
function setCookie(name, value, expires, path, domain, secure) {

    var exp = new Date();
    expires = arguments[2] || null;
    path = arguments[3] || "/";
    domain = arguments[4] || null;
    secure = arguments[5] || false;
    expires ? exp.setMinutes(exp.getMinutes() + parseInt(expires)) : "";
    document.cookie = name + '=' + escape(value) + ( expires ? ';expires=' + exp.toGMTString() : '') + ';path=' + path + ( domain ? ';domain=' + domain : '') + ( secure ? ';secure' : '');
}

function in_array(id, array){
    return $.inArray(id, array) >= 0 ? true : false;
}

function array_key_exists(key, array){
    for (var i in array){
        if (key == i){
            return true;
        }
    }
    return false;
}

function array_delete(value, array){
    array.splice($.inArray(value,array),1);
    return array;
}

function checkTaggle(checked, name) {
    $('input[type="checkbox"][name="' + name + '"]').prop("checked", checked)
}

function reload(){
    window.location.reload();
}

function back(){
    window.history.go(-1);
}

function getFormArray(dom){
    var result = {};
    var params = $(dom).serializeArray();
    for (var i in params){
        var param = params[i];
        result[param['name']] = param['value'];
    }
    return result;
}

function str_repeat(str, n){
    var list = [];
    while(list.length < n){
        list.push(str);
    }
    return list.join('');
}

function getCheckBoxByName(name, isInt){
    if (isInt == undefined){
        isInt = true;
    }
    var ids = [];
    $('input[type="checkbox"][name="'+name+'"]:checked').each(function () {
        if (isInt){
            var value = parseInt($(this).val());
        } else {
            var value = $(this).val();
        }
        ids.push(value);
    });
    return ids;
}

function htmlEncode(str){
    if (typeof str == "string"){
        return str.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, "&#039;").replace(/</g, '&lt;').replace(/>/g, '&gt;');
    } else {
        return str;
    }
}

function htmlDecode(str){
    if (typeof str == "string"){
        return str.replace(/&quot;/g, '"').replace(/&#039;/g, "'").replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
    } else {
        return str;
    }
}

//Jquery Coffee
(function($){
    $.fn.Coffee = function (options) {
        for (var obj in options){
            var events = options[obj];
            for (evt in events){
                var func = events[evt];
                $(this).on(evt, obj, func);
            }
        }
    };
})(jQuery);

$(function(){
    $(document).Coffee({
        "input[type=text][maxLen],textarea[type=text][maxLen]": {
            input: function(){
                var max = parseInt($(this).attr('maxLen'));
                if (this.value.gblen() > max){
                    this.value = this.value.cutString(max);
                }
            }
        },
        "[tips]": {
            mousemove: function(e){
                if (!$(this).attr("tips")){
                    return;
                }
                if (this.nodeName == 'SELECT' && $(this).is(':focus')){
                    return;
                }
                if ($('#tooltipdiv').length == 0) {
                    var content = htmlEncode($(this).attr("tips")).replace(/[\n\r]/g, '<br />');
                    var tooltipdi = '<div id="tooltipdiv" class="txDivTips">'+content+'</div>';
                    $("body").append(tooltipdi);
                    $("#tooltipdiv").show();
                }
                $("#tooltipdiv").css({
                    "top": e.pageY + "px",
                    "left": e.pageX + "px"
                });
            },
            mouseleave: function(){
                $("#tooltipdiv").remove();
            },
            focus: function(){
                if (this.nodeName == 'SELECT' && $('#tooltipdiv').length > 0){
                    $("#tooltipdiv").remove();
                }
            }
        },
        "input[preg],textarea[preg],select[preg]": {
            "blur change": function(){
                var preg = new RegExp($(this).attr('preg'));
                if (!preg.test($(this).val())){
                    $(this).removeClass('correct');
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                    $(this).addClass('correct');
                }
            }
        },
        "input[onEnter]": {
            keypress: function(e){
                if(e.which == 13) {
                    eval($(this).attr('onEnter'));
                }
            }
        }
    });
});
