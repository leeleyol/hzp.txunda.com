

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

//验证卡号
function cartNumberCheck(bankno) {
    console.log(bankno)
    if(bankno.length == 0){
        return false
    }

    var lastNum = bankno.substr(bankno.length - 1, 1); //取出最后一位（与luhn进行比较）
    var first15Num = bankno.substr(0, bankno.length - 1); //前15或18位
    var newArr = new Array();
    for (var i = first15Num.length - 1; i > -1; i--) { //前15或18位倒序存进数组
        newArr.push(first15Num.substr(i, 1));
    }
    var arrJiShu = new Array(); //奇数位*2的积 <9
    var arrJiShu2 = new Array(); //奇数位*2的积 >9
    var arrOuShu = new Array(); //偶数位数组
    for (var j = 0; j < newArr.length; j++) {
        if ((j + 1) % 2 == 1) { //奇数位
            if (parseInt(newArr[j]) * 2 < 9) arrJiShu.push(parseInt(newArr[j]) * 2);
            else arrJiShu2.push(parseInt(newArr[j]) * 2);
        } else //偶数位
            arrOuShu.push(newArr[j]);
    }

    var jishu_child1 = new Array(); //奇数位*2 >9 的分割之后的数组个位数
    var jishu_child2 = new Array(); //奇数位*2 >9 的分割之后的数组十位数
    for (var h = 0; h < arrJiShu2.length; h++) {
        jishu_child1.push(parseInt(arrJiShu2[h]) % 10);
        jishu_child2.push(parseInt(arrJiShu2[h]) / 10);
    }

    var sumJiShu = 0; //奇数位*2 < 9 的数组之和
    var sumOuShu = 0; //偶数位数组之和
    var sumJiShuChild1 = 0; //奇数位*2 >9 的分割之后的数组个位数之和
    var sumJiShuChild2 = 0; //奇数位*2 >9 的分割之后的数组十位数之和
    var sumTotal = 0;
    for (var m = 0; m < arrJiShu.length; m++) {
        sumJiShu = sumJiShu + parseInt(arrJiShu[m]);
    }

    for (var n = 0; n < arrOuShu.length; n++) {
        sumOuShu = sumOuShu + parseInt(arrOuShu[n]);
    }

    for (var p = 0; p < jishu_child1.length; p++) {
        sumJiShuChild1 = sumJiShuChild1 + parseInt(jishu_child1[p]);
        sumJiShuChild2 = sumJiShuChild2 + parseInt(jishu_child2[p]);
    }
    //计算总和
    sumTotal = parseInt(sumJiShu) + parseInt(sumOuShu) + parseInt(sumJiShuChild1) + parseInt(sumJiShuChild2);

    //计算luhn值
    var k = parseInt(sumTotal) % 10 == 0 ? 10 : parseInt(sumTotal) % 10;
    var luhn = 10 - k;

    if (lastNum == luhn) {
        return true;
    } else {
        return false;
    }
}
//验证身份证
function IdentityCodeValid(code) {
    var city={11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江 ",31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北 ",43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏 ",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外 "};
    var tip = "";
    var pass= true;

    if(!code || !/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[12])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/i.test(code)){
        tip = "身份证号格式错误";
        pass = false;
    }

    else if(!city[code.substr(0,2)]){
        tip = "地址编码错误";
        pass = false;
    }
    else{
        //18位身份证需要验证最后一位校验位
        if(code.length == 18){
            code = code.split('');
            //∑(ai×Wi)(mod 11)
            //加权因子
            var factor = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 ];
            //校验位
            var parity = [ 1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2 ];
            var sum = 0;
            var ai = 0;
            var wi = 0;
            for (var i = 0; i < 17; i++)
            {
                ai = code[i];
                wi = factor[i];
                sum += ai * wi;
            }
            var last = parity[sum % 11];
            if(parity[sum % 11] != code[17]){
                tip = "校验位错误";
                pass =false;
            }
        }
    }
    return pass;
}

function checkPhone(phone){
    if(!(/^1(3|4|5|7|8)\d{9}$/.test(phone))){
        return false;
    }else {
        return true
    }
}
