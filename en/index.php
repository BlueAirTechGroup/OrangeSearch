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
		<title>Orange Search - Your search engine, much safer than ever before</title>
		<meta name="keywords" content="Search, Online Search, Anonymous, Internet Thinking, Innovative, OpenSource" />
        <meta name="description" content="Orange Search is the search engine powered by BATG. It will not track your search history, even if you asked to do so." />
	</head>
	<body>
		<?php 
		  require_once '../BoostPHP/BoostPHP.main.php';
		  require_once '../config.php';
		  $myMySQLCls = new BoostPHP_MySQLClass();
		  $searchDBConn = $myMySQLCls -> connectDB($CONFIG_MYSQLUSER, $CONFIG_MYSQLPASS, $CONFIG_MYSQLDB);
		  if(!$searchDBConn){
		      echo '<div class="cover"><div class="inner"><h1>Orange Search</h1><p>Opps..An error has occured inside the Database</p><p>Please check out later</p></div></div>';
		      exit();
		  }
		?>
		<div class="cover" style="background:#FF9900;">
			<div class="inner">
				<h1>Orange Search</h1>
				<p class="lead">Your search has never been so secure and transparent</p>
				<p>Currently, <?php echo $myMySQLCls->checkExist($searchDBConn, 'SearchRstList', array());  ?> results are availble for you</p>
				<p class="small">Test Version[0000.0002 Alpha]</p>
				
				<form method="post" action="search.php">
					<p>
						<input type="text" name="searchKeyword" id="searchKeyword" placeholder="Enter search content here..."></input>
						<input type="submit" class="btn" name="SearchBtn" value="Search" title="Search"></input>
						<a class="btn" href="../?selectLang=true">Language</a>
					</p>
				</form>
				<p>You want to see our <a class="text-white" href="whyorangesearch.html">Major Concepts</a>?</p>
			</div>
		</div>
		<?php 
		  $myMySQLCls->closeConn($myMySQLCls);
		?>
	</body>
</html>