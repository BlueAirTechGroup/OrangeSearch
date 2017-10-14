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
		  require_once '../BoostPHP/BoostPHP.main.php';
		  require_once '../config.php';
		  $myMySQLCls = new BoostPHP_MySQLClass();
		  $searchDBConn = $myMySQLCls -> connectDB($CONFIG_MYSQLUSER, $CONFIG_MYSQLPASS, $CONFIG_MYSQLDB);
		  if(!$searchDBConn){
		      echo '<div class="cover"><div class="inner"><h1>Orange Search</h1><p>Opps..An error has occured inside the Database</p><p>Please check out later</p></div></div>';
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
                                						<span class="display-normal-tablet" style="font-size:42px;line-height:50px;margin-right:10px;">Orange Search</span>
                                						<span class="float-right" style="display:inline-block;position:static;">
                                							<input class="bg-orange" type="text" name="searchKeyword" id="searchKeyword"></input>
                                							<input type="submit" class="backgroundimg-cover" name="SearchBtn" id="SearchBtn"></input>
                                						</span>
                                					</p>
                                				</form>
                                				<div class="after-float"></div>
                                				<p class="lead text-right">Your search has never been so secure, and safe</p>
                                				<p class="text-right">Until now, <?php echo $myMySQLCls->checkExist($searchDBConn, 'SearchRstList', array());  ?> search results are available</p>
                                				<p class="text-right">Want to see our <a class="text-white" href="whyorangesearch.html">Major Concepts</a>?</p>
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
		  $myMySQLCls->closeConn($myMySQLCls);
		?>
	</body>
</html>