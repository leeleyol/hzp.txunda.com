/**
 *ajax 请求封装
 *@name requestUrl
 **/

function requestUrl(URL,DATA,CALLBACK,TYPE,DATATYPE){
    if (!URL) return;
    if (!TYPE) TYPE ="post";
    if (!DATATYPE) DATATYPE ="json";

    console.log(URL,DATA)
    $.ajax({
        "url":URL,
        "data" : DATA,
        "dataType" : DATATYPE,
        "type" : TYPE,
        "success" : function(res){
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