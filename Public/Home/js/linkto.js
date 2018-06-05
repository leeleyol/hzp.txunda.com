/**
 * @name dom  linkto 属性增强
 * @author topqiang
 * @version 1.0
 * **/
function top_linkto(){
	$("[linkto]").on('click',function(){
		var self = $(this);
		var url = $.trim(self.attr("linkto"));
		if( url == ""){
			return;
		}
		//匹配全路径跳转
		if( url.indexOf("http://") == 0 || url.indexOf("https://") == 0 ){
			window.location.href = url;
		}else if( url.indexOf("/") == 0 ){
			//匹配   类似于Thinkphp类型的URL {:U('Index/index')} => /项目名/index.php/Index/index  
			var hostname = getRootPath();
			url = hostname + url;
			window.location.href = url;
		}else if( url.indexOf("tel:") == 0){
			window.location.href = url;
		}else if( url.indexOf("javascript:") == 0){
			var start = url.indexOf(":");
			var str = url.substring(start+1);
			eval(str);
		}else if( url.indexOf("./") == 0 || url.indexOf("/") == -1){
			//匹配静态跳转，已经相对路径跳转。
			var curUrl = window.location.href;
			url = curUrl.substr(0,curUrl.lastIndexOf("/")+1) + url;
			window.location.href = url;
		}
	});
}
function getRootPath(){
    var curWwwPath=window.document.location.href;
    var pathName=window.document.location.pathname;
    var pos=curWwwPath.indexOf(pathName);
    var localhostPaht=curWwwPath.substring(0,pos);
    var projectName=pathName.substring(0,pathName.substr(1).indexOf('/')+1);
    return(localhostPaht);
}

$(function(){
	//吊起linkto增强页面跳转
	top_linkto();
});
