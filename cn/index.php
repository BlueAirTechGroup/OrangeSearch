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
		<div class="cover">
			<div class="inner">
				<h1>橙子搜索</h1>
				<p>你的搜索,从未如此安全和安心</p>
				<p>截止到现在, 有<?php echo $myMySQLCls->checkExist($searchDBConn, 'SearchRstList', array());  ?>个URL被收录</p>
				<p class="small">Test Version[0.000001A]</p>
				<form method="post" action="search.php">
					<p>
						<input type="text" name="searchKeyword" id="searchKeyword" placeholder="在此输入搜索内容..."></input>
						<input type="submit" class="btn" name="SearchBtn" value="搜索" title="搜索"></input>
						<a href="../?selectLang=true" class="btn">Language</a>
					</p>
				</form>
				<p>想看看我们的<a href="whyorangesearch.html">核心理念</a>?</p>
			</div>
		</div>
		<?php 
		  $myMySQLCls->closeConn($searchDBConn);
		?>
	</body>
</html>