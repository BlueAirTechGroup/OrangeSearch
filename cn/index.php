<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<script src="https://www.xsyds.cn/js/core.js"></script>
		<script src="https://www.xsyds.cn/js/jquery.min.js"></script>
        <script src="https://www.xsyds.cn/js/BOOST.main.js"></script>
        <link type="text/css" href="https://www.xsyds.cn/css/BOOST.main.css" rel="stylesheet" />
        <link type="text/css" href="https://www.xsyds.cn/css/BOOST.animate.css" rel="stylesheet" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title>橙子搜索 - 你的搜索, 比以前更安全</title>
		<meta name="keywords" content="搜索, 在线搜索, 匿名, 互联网思维, 创新, 开源, 搜索引擎， 搜索" />
        <meta name="description" content="橙子搜索是由形随意动推出的新时代安全搜索服务. 橙子搜索不会主动跟踪您的搜索记录， 尽管您要求他们被记录下来." />
        <style>
            #SearchBtn{
                vertical-align:top;
                font-size:0;
                background-color:transparent;
                display:inline-block;
                height:50px;
                width:50px;
                border:3px solid #FFFFFF;
                border-top:none;
                border-left:none;
                border-right:none;
                border-radius:0;
                background-image:url("../image/searchbtn.png");
                margin:0;
            }
            #searchKeyword{
                border:3px solid #FFFFFF;
                border-top:none;
                border-left:none;
                border-right:none;
                border-radius:0;
                height:47px;
                line-height:50px;
                font-size:24px;
                padding:0;
                margin:0;
                vertical-align:top;
            }
        </style>
	</head>
	<body>
		<?php 
		  require '../BoostPHP/BoostPHP.main.php';
		  require '../config.php';
		  $myMySQLCls = new BoostPHP_MySQLClass();
		  $searchDBConn = $myMySQLCls -> connectDB($CONFIG_MYSQLUSER, $CONFIG_MYSQLPASS, $CONFIG_MYSQLDB);
		  if(!$searchDBConn){
		      echo '<div class="cover"><div class="inner"><h1>橙子搜索</h1><p>对不起, 一个内部数据库错误发生了</p><p>工程师已经在路上啦, 请稍后再查看</p></div></div>';
		      exit();
		  }
	    ?>
		<div class="cover bg-orange">
			<div class="inner">
				<div class="row row-as-base">
					<div class="col col-tablet-9">
        					<div class="container">
        							<div class="row row-as-base">
        								<div class="col col-tablet-1 col-comp-2 invisible-phone"></div>
        								<div class="col col-tablet-10 col-comp-8">
        									<!-- div class="row row-as-base" -->
        										<form method="post" action="search.php">
                                					<p style="font-size:0;margin-bottom:10px;display:block;">
                                						<span class="display-normal-tablet" style="font-size:42px;line-height:50px;margin-right:10px;">橙子搜索</span>
                                						<span class="float-right" style="display:inline-block;position:static;">
                                							<input class="bg-orange" type="text" name="searchKeyword" id="searchKeyword"></input>
                                							<input type="submit" class="backgroundimg-cover" name="SearchBtn" id="SearchBtn"></input>
                                						</span>
                                					</p>
                                				</form>
                                				<div class="after-float" style="margin-bottom:10px;"></div>
                                				<p class="lead text-right">你的搜索,从未如此安全和安心</p>
                                				<p class="text-right">截止到现在, 有<?php echo $myMySQLCls->checkExist($searchDBConn, 'SearchRstList', array());  ?>个URL被收录</p>
                                				<p class="text-right">想看看我们的<a class="text-white" href="whyorangesearch.html">核心理念</a>?</p>
                                				<p class="text-right small">Test Version[0000.0002 Alpha]</p>
                                				<p class="text-right"><a class="text-white" href="../?selectLang=true">Choose Language</a></p>
        									<!-- /div -->
        								</div>
        								<div class="col col-tablet-1 col-comp-2 invisible-phone"></div>
                    				</div>
        					</div>
					</div>
				</div>
			</div>
		</div>
		<?php 
		  $myMySQLCls->closeConn($searchDBConn);
		?>
	</body>
</html>