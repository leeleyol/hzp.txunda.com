/**
 *ajax 请求封装
 *@name requestUrl
 **/

function requestUrl(URL,DATA,CALLBACK,TYPE,DATATYPE){
    if (!URL) return;
    if (!TYPE) TYPE ="post";
    if (!DATATYPE) DATATYPE ="json";

    DATA["m_id"] = m_id
    $.ajax({
        "url":URL,
        "data" : DATA,
        "dataType" : DATATYPE,
        "type" : TYPE,
        "success" : function(res){
            if(res.data.constructor  ===Array){
                if(res.data.length == 0){
                    layer.open({
                        content: res.message
                        ,skin: 'msg'
                        ,time: 2 //2秒后自动关闭
                    });
                }
            }

            if (typeof CALLBACK == 'function') {
                CALLBACK(res);
            }
        }
    });
}

function uploadfile(URL,DATA,CALLBACK,TYPE,DATATYPE) {
    if (!URL) return;
    if (!TYPE) TYPE ="post";
    if (!DATATYPE) DATATYPE ="json";

    console.log(URL,DATA)
    $.ajax({
        "url":URL,
        "data" : DATA,
        "dataType" : DATATYPE,
        "type" : TYPE,
        "processData" : false,
        // 告诉jQuery不要去设置Content-Type请求头
        "contentType" : false,
        "success" : function(res){
            if (typeof CALLBACK == 'function') {
                CALLBACK(res);
            }
        }
    });
}

$(function () {
    var isPageHide = false;
    window.addEventListener('pageshow', function () {
        if (isPageHide) {
            window.location.reload();
        }
    });
    window.addEventListener('pagehide', function () {
        isPageHide = true;
    });
})