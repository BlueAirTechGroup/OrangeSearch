<!DOCTYPE html>
<html>
	<head>
		<?php 
		  //先预处理Post值
		  require_once '../BoostPHP/BoostPHP.main.php';
		  require_once '../config.php';
		  $myMySQLCls = new BoostPHP_MySQLClass();
		  $myResultCls = new BoostPHP_ResultClass();
		  $usrSearchWord = $_POST['searchKeyword'];
		  if(empty($usrSearchWord)){
		      $myResultCls->jumpToPage("index.php");
		  }
		  $usrSearchPage = $_GET['page'];
		  if(empty($usrSearchPage)){
		      $usrSearchPage = 0;
		  }
		  $usrSearchHTMLWord = str_replace("\"", "&quot;", $usrSearchWord);
		  $usrSearchHTMLWord = str_replace("<","&lt;",$usrSearchHTMLWord);
		  $usrSearchHTMLWord = str_replace(">", "&gt;", $usrSearchHTMLWord);
		?>
		<meta charset="utf-8" />
		<script src="https://www.xsyds.cn/js/core.js"></script>
		<script src="https://www.xsyds.cn/js/jquery.min.js"></script>
        <script src="https://www.xsyds.cn/js/BOOST.main.js"></script>
        <link type="text/css" href="https://www.xsyds.cn/css/BOOST.main.css" rel="stylesheet" />
        <link type="text/css" href="https://www.xsyds.cn/css/BOOST.animate.css" rel="stylesheet" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title><?php echo $usrSearchHTMLWord ?> - Orange Search | Your search engine, much safer than ever before</title>
		<meta name="keywords" content="Search, Online Search, Anonymous, Internet Thinking, Innovative, OpenSource" />
        <meta name="description" content="Orange Search is the search engine powered by BATG. It will not track your search history, even if you asked to do so." />
        <style>
            #OSTitle{
                font-size:30px;
                line-height:35px;
                vertical-align:top;
                color:#FC9F4D;
                margin-right:8px;
            }
            #searchKeyword{
                font-size:20px;
                line-height:35px;
                height:35px;
                border:none;
                border-radius:0;
                vertical-align:top;
                color:#FFFFFF;
                background-color:#FC9F4D;
                padding:0;
                padding-left: 5px;
                padding-right: 5px;
            }
            #reSearchBtn{
                border:none;
                height:35px;
                width:35px;
                color:#FFFFFF;
                font-size:0;
                vertical-align:top;
                background-color:#FC9F4D;
                background-image:url("../image/searchbtn.png");
            }
            #changeLangBtn{
                font-size:22px;
                padding:0;
                height:35px;
                margin:0;
                width:auto;
                padding-left:5px;
                padding-right:5px;
                line-height:35px;
                vertical-align:top;
                color:#FFFFFF;
                background-color:#FC9F4D;
                display:inline-block;
                border-left:2px solid #000000;
            }
        </style>
	</head>
	<body style="padding-top:77px;">
		<div class="bg-white" style="position:fixed;top:0;left:0;width:100%;width:100vw;">
        		<div class="container" style="padding-top:20px;padding-bottom:20px;">
        			<span id="OSTitle" class="invisible-phone invisible-tablet">OrangeSearch</span><form action="" method="post" style="display:inline-block;"><input type="text" name="searchKeyword" id="searchKeyword" value="<?php echo $usrSearchHTMLWord; ?>"></input><input class="backgroundimg-cover" type="submit" name="reSearchBtn" id="reSearchBtn" value="搜索" title="搜索"></input></form><a href="../?selectLang=true" id="changeLangBtn" class="invisible-phone">Language</a></div>
			<div style="display:box;width:100%;border-bottom:2px solid #FC9F4D;"></div>
		</div>
			<?php 
                /* PHP自动输出搜索结果 */
                /* $CONFIG_MYSQLDB
                 * $CONFIG_MYSQLUSER
                 * $CONFIG_MYSQLPASS
                 * 表(PendingScanList, ScannedList, NonTakenList, SearchRstList)结构
                 * - PendingScanList:
                 *   - String URL
                 * - ScannedList:
                 *   - String URL
                 * - NonTakenList
                 *   - String URL
                 *   - String Title
                 *   - TinyInt Rank
                 *   - Int (TimeStamp) LastAccess
                 * - SearchRstList
                 *   - String URL
                 *   - String Title
                 *   - String Description
                 *   - String Keywords
                 *   - TinyInt Rank(Range: 0 - 10, 实际判断时*100)
                 *   - Int (TimeStamp) LastAcess
                 */
			    require_once '../searchFunctions/searchfunction.php';
			    $startTime = microtime(true);
			    $cacheRST = $myResultCls->cacheStart($CONFIG_CACHETIME, true, 'cachepages/');
			    if($cacheRST){
        			    $searchDBConn = $myMySQLCls -> connectDB($CONFIG_MYSQLUSER, $CONFIG_MYSQLPASS, $CONFIG_MYSQLDB);
        			    if(!searchDBConn){
        			        exit('<p class="lead">Inner Error: failed to connect to database</p>');
        			    }
        			    $SearchRST = array();
        			    
        			    $partialit = 0;
        			    $lastResultNum = $CONFIG_PARTITIONSIZE;
        			    while($lastResultNum == $CONFIG_PARTITIONSIZE){
        			        //开始分段select
        			        $MySQLSDBRst = $myMySQLCls->selectIntoArray_FromRequirements($searchDBConn, "SearchRstList",array(),array("Rank"),$CONFIG_PARTITIONSIZE,($partialit*$CONFIG_PARTITIONSIZE));
        			        for($i=$MySQLSDBRst['count']-1;$i>=0;$i--){
        			            //从最高的Rank往下走
        			            $MySQLSDBRow = $MySQLSDBRst['result'][$i];
        			            $TempRST = calculateSearchWeight($MySQLSDBRow,$usrSearchWord);
        			            //单个结果统计完毕, 准备投放数据到总Array
        			            if($TempRST['searchRank'] > 0){
        			                $SearchRST[] = $TempRST;
        			            }
        			            unset($MySQLSDBRst['result'][$i]); //单个结果访问完毕,释放内存
        			        }
        			        $lastResultNum = $MySQLSDBRst['count'];
        			        unset($MySQLSDBRst); //释放总内存
        			        $partialit++; //循环次数增加
        			    }
        			    $calcSearchWeightEndTime = microtime(true);
        			    
        			    //所有统计完毕, 检查是否empty
        			    if(!empty($SearchRST)){
        			        require_once '../BoostPHP/BoostPHP.Alg.php';
        			        $myStrCls = new BoostPHP_StringClass();
        			        $myAlgCls = new BoostPHP_AlgorithmClass();
        			        $newSearchRST = $myAlgCls -> quickSortArrays_ByField($SearchRST, "searchRank");
        			        for($i=count($newSearchRST)-1;$i>=0;$i--){ //-($usrSearchPage*$CONFIG_PAGERESULT)
        			            $TempOutputArr = $newSearchRST[$i];
        			            $TempOutputArr['Title'] = $myStrCls->wordLimit($TempOutputArr['Title'],100,true);
        			            $TempOutputArr['Description'] = $myStrCls->wordLimit($TempOutputArr['Description'],250,true);
        			            if(!empty($TempOutputArr['URL'])){
        			                ?>
                		                <!-- div class="row row-as-base">
                		                    <div class="col col-phone-12 col-comp-8" -->
                		                    <div class="container" style="margin-top:20px;">
                		                        <h4 class="without-margin"><a class="text-orange" href="<?php echo $TempOutputArr['URL']; ?>" target="_blank"><?php echo empty($TempOutputArr['Title']) ? "No Title" : $TempOutputArr['Title']; ?></a></h4>
                		                        <p class="small text-grey without-margin">URL: <?php echo $TempOutputArr['URL'];  ?></p>
                		                        <p class="without-margin"><?php echo $TempOutputArr['Description']; ?></p>
                		                        <p class="small without-margin">Reference: searchWeight[<?php echo $TempOutputArr['searchRank']; ?>/180], siteRank[<?php echo $TempOutputArr['Rank']; ?>/10]</p>
                		                    </div>
                		                    <!-- /div>
                		               	</div -->
        			        	    		<?php
        			            }
        			        }
        			    }
        			    $myMySQLCls->closeConn($searchDBConn);
			    }
			    $myResultCls->cacheEnd(true,'cachepages/',$cacheRST);
			    $nowTime = microtime(true);
			?>
			<div class="container" style="margin-top:20px;">
        			<p class="text-grey">Used Memory: <?php echo((memory_get_peak_usage()/1024/1024)); ?>M</p>
        			<?php if($cacheRST){ ?><p class="text-grey">Weight Calculation Time: <?php echo ($calcSearchWeightEndTime - $startTime); ?> second(s)</p><?php } ?>
        			<p class="text-grey">Execution Time: <?php echo ($nowTime-$startTime); ?> second(s)</p>
        			<?php if($cacheRST){ ?><p class="text-grey">Total Result Number: <?php echo count($SearchRST); ?></p><?php } ?>
        			<p class="text-grey">Powered by <a href="http://www.xsyds.cn/" target="_blank">BlueAirTechGroup</a>&copy;2015-2017</p>
				<?php if(!$cacheRST){ ?> <p class="text-grey">This page is a cache page generated by BoostPHP</p> <?php } ?>
			</div>
	</body>
</html>