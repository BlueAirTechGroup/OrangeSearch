<!DOCTYPE HTML>
<html>
	<head>
		<!-- This page is modified from BATG official website, the author is also Windy -->
		<meta charset="utf-8" />
		<script src="https://www.xsyds.cn/js/core.js"></script>
		<script src="https://www.xsyds.cn/js/jquery.min.js"></script>
        <script src="https://www.xsyds.cn/js/BOOST.main.js"></script>
        <link type="text/css" href="https://www.xsyds.cn/css/BOOST.main.css" rel="stylesheet" />
        <link type="text/css" href="https://www.xsyds.cn/css/BOOST.animate.css" rel="stylesheet" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<?php
		//Also Language Detection and Language Flexible SEO Optimization
		require_once 'BoostPHP/BoostPHP.main.php';
		require_once 'BoostPHP/BoostPHP.International.php';
        $MyInternationalCls = new BoostPHP_InternationalClass();
		$MySupportedLangs = $MyInternationalCls->getSupportedLanguage(true);
		if(in_array("zh-cn",$MySupportedLangs) || in_array("zh-chs",$MySupportedLangs) || in_array("zh-hk",$MySupportedLangs) || in_array("zh-mo",$MySupportedLangs) || in_array("zh-sg",$MySupportedLangs) || in_array("zh-tw",$MySupportedLangs) || in_array("zh-cht",$MySupportedLangs) || in_array("zh",$MySupportedLangs)){ //Simplified Chinese
		?>
			<title>橙子搜索 - 你的搜索, 比以前更加安全</title>
            <meta name="keywords" content="搜索, 在线搜索, 匿名, 互联网思维, 创新, 开源, 搜索引擎， 搜索" />
            <meta name="description" content="橙子搜索是由形随意动推出的新时代安全搜索服务. 橙子搜索不会主动跟踪您的搜索记录， 尽管您要求他们被记录下来." />
        <?php
		}elseif(in_array("en",$MySupportedLangs) || in_array("en-au", $MySupportedLangs) || in_array("en-bz", $MySupportedLangs) || in_array("en-ca", $MySupportedLangs) || in_array("en-cb", $MySupportedLangs) || in_array("en-ie", $MySupportedLangs) || in_array("en-jm", $MySupportedLangs) || in_array("en-nz", $MySupportedLangs) || in_array("en-ph", $MySupportedLangs) || in_array("en-za", $MySupportedLangs) || in_array("en-tt", $MySupportedLangs) || in_array("en-gb", $MySupportedLangs) || in_array("en-us", $MySupportedLangs) || in_array("en-zw", $MySupportedLangs)){
		?>
        		<title>Orange Search - Your search, more secure then ever before</title>
            <meta name="keywords" content="Search, Online Search, Anonymous, Internet Thinking, Innovative, OpenSource" />
            <meta name="description" content="Orange Search is the search engine powered by BATG. It will not track your search history, even if you asked to do so." />
		<?php
        }
        ?>
	</head>
	<body>
		<?php 
		//Also Language Detection and URL Jump
		$CHSURL = "http://search.xsyds.cn/cn/";
		$ENGURL = "http://search.xsyds.cn/en/";
		if($_GET['selectLang']!='true'){
		    $MyURLLists=array(
		        "zh-cn"=>$CHSURL,
		        "zh-chs"=>$CHSURL,
		        "zh-hk"=>$CHSURL,
		        "zh-mo"=>$CHSURL,
		        "zh-sg"=>$CHSURL,
		        "zh-tw"=>$CHSURL,
		        "zh-cht"=>$CHSURL,
		        "zh"=>$CHSURL,
		        "en"=>$ENGURL,
		        "en_au"=>$ENGURL,
		        "en_bz"=>$ENGURL,
		        "en_ca"=>$ENGURL,
		        "en_cb"=>$ENGURL,
		        "en_ie"=>$ENGURL,
		        "en_jm"=>$ENGURL,
		        "en_nz"=>$ENGURL,
		        "en_ph"=>$ENGURL,
		        "en_za"=>$ENGURL,
		        "en_tt"=>$ENGURL,
		        "en_gb"=>$ENGURL,
		        "en_us"=>$ENGURL,
		        "en_zw"=>$ENGURL,
		    );
		    $MyMatchResultURL = $MyInternationalCls->matchSupportedLanguage($MySupportedLangs,$MyURLLists,$ENGURL);
		    $MyResultCls = new BoostPHP_ResultClass();
		    $MyResultCls->jumpToPage($MyMatchResultURL);
		    ?>
			<div class="cover">
				<div class="inner">
					<h1>Automatic Redirect</h1>
					<p>The system has detected your langage and will automatically rewrite your URL to your language setting</p>
				</div>
			</div>
		<?php 
		}else{ //If options to select Language
		?>
    			<div class="col col-as-base invisible-comp invisible-bigcomp">
				<div class="row row-phone-6 bg-black">
					<p class="text-center" style="font-size:10vh;line-height:50vh;"><a class="text-white" href="<?php echo $CHSURL; ?>">简体中文</a></p>
				</div>
				<div class="row row-phone-6 bg-white">
					<p class="text-center" style="font-size:10vh;line-height:50vh;"><a class="text-black" href="<?php echo $ENGURL; ?>">ENGLISH</a></p>
				</div>
			</div>
            <div class="row row-as-base invisible-phone invisible-tablet">
            		<div class="col col-comp-6 bg-black">
                		<p class="text-center" style="font-size:5vw;line-height:100vh;"><a class="text-white" href="<?php echo $CHSURL; ?>">简体中文</a></p>
				</div>
				<div class="col col-comp-6 bg-white">
					<p class="text-center" style="font-size:5vw;line-height:100vh;"><a class="text-black" href="<?php echo $ENGURL; ?>">ENGLISH</a></p>
				</div>
			</div>
		<?php
		}
		?>
	</body>
</html>