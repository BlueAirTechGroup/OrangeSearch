/* BOOST Framework 1.0 Main JS FILE - If you want to change, please refer to the GPL open-source lisence
 * Powered by BlueAirTechGroup(www.xsyds.cn)
 */
var loadingWrapperDeleted;
loadingWrapperDeleted=false;

document.onreadystatechange = completeLoading;


	var prompttime = 0;
	var promptx = [];
	var prompty = [];
	var promptdraging = [];
    var browser = function () {   
    var agent = navigator.userAgent.toLowerCase(),  
    opera = window.opera,  
    browser = {  
        ie: /(msie\s|trident.*rv:)([\w.]+)/.test(agent), 
 
        opera: (!!opera && opera.version),  
        webkit: (agent.indexOf(' applewebkit/') > -1), 
        mac: (agent.indexOf('macintosh') > -1), 
        quirks: (document.compatMode == 'BackCompat')  
    }; 
    browser.gecko = (navigator.product == 'Gecko' && !browser.webkit && !browser.opera && !browser.ie); 
    var version = 0; 
    // Internet Explorer 6.0+  
    if (browser.ie) {  
        var v1 = agent.match(/(?:msie\s([\w.]+))/);  
        var v2 = agent.match(/(?:trident.*rv:([\w.]+))/);  
        if (v1 && v2 && v1[1] && v2[1]) {  
            version = Math.max(v1[1] * 1, v2[1] * 1);  
        } else if (v1 && v1[1]) {  
            version = v1[1] * 1;  
        } else if (v2 && v2[1]) {  
            version = v2[1] * 1;  
        } else {  
            version = 0;  
        } 
        browser.ie11Compat = document.documentMode == 11; 
        browser.ie9Compat = document.documentMode == 9; 
        browser.ie10Compat = document.documentMode == 10;  
        browser.ie8 = !!document.documentMode; 
        browser.ie8Compat = document.documentMode == 8; 
        browser.ie7Compat = ((version == 7 && !document.documentMode) || document.documentMode == 7); 
        browser.ie6Compat = (version < 7 || browser.quirks); 
        browser.ie9above = version > 8; 
        browser.ie9below = version < 9;  
    } 
    // Gecko.  
    if (browser.gecko) {  
        var geckoRelease = agent.match(/rv:([\d\.]+)/);  
        if (geckoRelease) {  
            geckoRelease = geckoRelease[1].split('.');  
            version = geckoRelease[0] * 10000 + (geckoRelease[1] || 0) * 100 + (geckoRelease[2] || 0) * 1;  
        }  
    } 
    if (/chrome\/(\d+\.\d)/i.test(agent)) {  
        browser.chrome = +RegExp['\x241'];  
    }  
    if (/(\d+\.\d)?(?:\.\d)?\s+safari\/?(\d+\.\d+)?/i.test(agent) && !/chrome/i.test(agent)) {  
        browser.safari = +(RegExp['\x241'] || RegExp['\x242']);
    } 
    // Opera 9.50+  
    if (browser.opera){ 
        version = parseFloat(opera.version()); 
	}
    // WebKit 522+ (Safari 3+)  
    if (browser.webkit){
        version = parseFloat(agent.match(/ applewebkit\/(\d+)/)[1]); 
	} 
    browser.version = version; 
    return browser;  
}();
$(window).ready(
function(e){
	"use strict";
	var ScreenWidth = parseInt(GetWindowWidth());
	var ScreenHeight = parseInt(GetWindowHeight());
	if(loadingWrapperDeleted==false){
		$("body").append('<div id="LoadingWrapper" class="cover bg-white" style="background:#FFFFFF;color:#000000;position:fixed;top:0px;left:0px;width:100vw;height:100vh;z-index:9999;display:table;vertical-align:middle;"><div class="inner" style="display:table-cell"><p align="center"><div class="boost-animate-spinner"></div></p><p align="center">Powered by Boost Framework</p><!--[if lt IE 9]><p>对不起,本网页<b>不支持</b>您的浏览器, 请升级您的浏览器.</p><p>Sorry, this page does not support your current browser, to change this situation, you will need to update your browser</p><![endif]--></div></div>');
	}
	if(ScreenWidth<1201){
		//Phone or tablet
		$(".navbar-links").hide();
	}
	$("navicon").click(
	function(e){
		var MyContainer = $(this).parent(".container");
		var MyLinks = MyContainer.children(".navbar-links");
		if($(this).hasClass("navicon-rotate")){
			$(this).addClass("navicon-revrotate");
			$(this).removeClass("navicon-rotate");
		}else{
			$(this).removeClass("navicon-revrotate");
			$(this).addClass("navicon-rotate");
		}
		MyLinks.toggle();
	}
	);
}
);
$(window).resize(
function(e){
	"use strict";
	var ScreenWidth = parseInt(GetWindowWidth());
	var ScreenHeight = parseInt(GetWindowHeight());
	if(ScreenWidth<1201){
		//Phone or tablet
		$(".navbar-links").hide();
		$("navicon").removeClass("navicon-revrotate");
		$("navicon").removeClass("navicon-rotate");
	}else{
		$(".navbar-links").show();
	}
}
);

function GetWindowWidth(){
	"use strict";
	return $(window).width();
}
function GetWindowHeight(){
	"use strict";
	return $(window).height();
}
function completeLoading(){
	if(document.readyState == "complete"){
		loadingWrapperDeleted=true;
		//做爱做的事
		$("#LoadingWrapper").hide();
		$("#LoadingWrapper").remove();
		$("body").addClass("boost-animate-completeloading");
	}
}

if(browser.ie9below === true){
		//当前Jquery版本不支持, 部分功能无法使用
		//提示更新浏览器
		document.write(
			'<div class="cover bg-white" style="width:100%;height:100%;width:100vw;height:100vh;position:fixed;top:0px;left:0px;text-align:center;z-index:9999;"><h1>对不起, 本网页不支持您的浏览器</h1><p>Sorry, your browser does not support this page</p><p>Powered by Boost Framework</p></div>'
		);
}
