

var m_id = localStorage.getItem('m_id');
m_id = m_id?m_id:0;

function getDate(param, type) {
    var date = new window.Date(param * 1000);
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var Date = date.getDate();
    var hour = date.getHours();
    var minute = date.getMinutes();
    var second = date.getSeconds();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (Date >= 0 && Date <= 9) {
        Date = "0" + Date;
    }
    if (hour >= 0 && hour <= 9) {
        hour = '0' + hour;
    }
    if (minute >= 0 && minute <= 9) {
        minute = '0' + minute;
    }
    if (second >= 0 && second <= 9) {
        second = '0' + second;
    }

    if(type == "YY/MM/DD HH:MM"){
        return year +"/" + month + "/" + Date + " " + hour +":" +minute
    }
}


function handlerScrollBottom(callBack) {
    //页面滚动到底部,继续加载
    $(window).scroll(function () {
        //滚动条滚动距离
        var scrollTop = $(this).scrollTop();
        //内容高度
        var scrollHeight = $(document).height();
        //屏幕高度
        var windowHeight = $(this).height();
        //滚动到底步了
        if (scrollTop + windowHeight === (scrollHeight)) {
            if (typeof callBack == 'function') {
                callBack()
            }
        }
    });
}