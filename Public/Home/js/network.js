/**
 *ajax 请求封装
 *@name requestUrl
 **/

function requestUrl(URL,DATA,CALLBACK,TYPE,DATATYPE){
    if (!URL) return;
    if (!TYPE) TYPE ="post";
    if (!DATATYPE) DATATYPE ="json";
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
