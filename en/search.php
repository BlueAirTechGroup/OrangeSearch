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
		      $myResultCls->jumpToPage("index.html");
		  }
		?>
		<meta charset="utf-8" />
		<script src="https://www.xsyds.cn/js/core.js"></script>
		<script src="https://www.xsyds.cn/js/jquery.min.js"></script>
        <script src="https://www.xsyds.cn/js/BOOST.main.js"></script>
        <link type="text/css" href="https://www.xsyds.cn/css/BOOST.main.css" rel="stylesheet" />
        <link type="text/css" href="https://www.xsyds.cn/css/BOOST.animate.css" rel="stylesheet" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title><?php echo $usrSearchWord ?> - Orange Search | Your search engine, much safer than ever before</title>
		<meta name="keywords" content="Search, Online Search, Anonymous, Internet Thinking, Innovative, OpenSource" />
        <meta name="description" content="Orange Search is the search engine powered by BATG. It will not track your search history, even if you asked to do so." />
	</head>
	<body class="has-navbar-top">
		<nav class="navbar-fixed-top navbar-black navbar-hasbottomborder">
			<div class="container">
                <navicon></navicon>
                <div class="navbar-brand"><b><a href="index.html">Orange Search</a></b></div>
                <div class="navbar-links">
                    <div class="navbar-link navbar-link-current"><a href="javascript:void(0);">Websites</a></div>
                </div>
                <div class="navbar-links navbar-links-right">
                    <div class="navbar-link"><a href="../?selectLang=true">Language</a></div>
                </div>
			</div>
		</nav>
		<div class="container">
			<!--  SearchBox Section Begins -->
			<form method="post" action="">
				<p>
					<input type="text" name="searchKeyword" id="searchKeyword" value="<?php echo $usrSearchWord; ?>"></input>
					<input type="submit" name="reSearchBtn" id="reSearchBtn" class="btn" value="Search" title="Search"></input>
				</p>
			</form>
			<!-- SearchBox Section Ends -->
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
			    $searchDBConn = $myMySQLCls -> connectDB($CONFIG_MYSQLUSER, $CONFIG_MYSQLPASS, $CONFIG_MYSQLDB);
			    if(!searchDBConn){
			        exit('<p class="lead">Inner Error: failed to connect to database</p>');
			    }
			    $MySQLSDBRst = $myMySQLCls->selectIntoArray_FromRequirements($searchDBConn, "SearchRstList",array(),array("Rank"));
			    $SearchRST = array();
			    for($i=$MySQLSDBRst['count']-1;$i>=0;$i--){
			        
			        //从最高的Rank往下走
			        $MySQLSDBRow = $MySQLSDBRst['result'][$i];
			        $TempRST = array("URL"=>"","Title"=>"","Description"=>"","Keywords"=>"","searchRank"=>0,"Rank"=>0);
			        $TempRST['searchRank'] += 50; //首先给加50 Search rank
			        foreach($MySQLSDBRow as $DBVariableName => $DBVariableVal){
			            if($DBVariableName == "Title"){
			                $TempRST['Title'] = base64_decode($DBVariableVal);
			                if(strpos(strtolower(base64_decode($DBVariableVal)),strtolower($usrSearchWord))===false){
			                    //Title未找到关键词, SearchRank - 50
			                    $TempRST['searchRank'] -= 50;
			                }else if(strpos(strtolower(base64_decode($DBVariableVal)),strtolower($usrSearchWord))===0){
			                    $TempRST['searchRank'] += 50;
			                }else if(strpos(strtolower(base64_decode($DBVariableVal)),strtolower($usrSearchWord))<=30){
			                    $TempRST['searchRank'] += 30;
			                }
			            }else if($DBVariableName=="URL"){
			                $TempRST['URL'] = $DBVariableVal;
			                if(strpos(strtolower($TempRST['URL']),strtolower($usrSearchWord))!==false){
			                    $TempRST['searchRank'] += 20;
			                }
			            }else if($DBVariableName=="Description"){
			                $TempRST['Description'] = base64_decode($DBVariableVal);
			                //Description判断, 如果在20字后, 则Rank降低
			                $DescriptionPos = strpos(strtolower(base64_decode($DBVariableVal)), strtolower($usrSearchWord));
			                if($DescriptionPos === false){
			                    //Description未找到关键词! SearchRank - 30
			                    $TempRST['searchRank'] -= 30;
			                }else if($DescriptionPos <= 50){
			                    $TempRST['searchRank'] += 30;
			                }
			            }else if($DBVariableName == "Keywords"){
			                $TempRST['Keywords'] = base64_decode($DBVariableVal);
			                $KeywordArray = explode(base64_decode($DBVariableVal));
			                $KeywordFinded = false;
			                if(!empty(base64_decode($DBVariableVal)) && !empty($KeywordArray)){
			                    for($KeywordI=0;$KeywordI<count($KeywordArray);$KeywordI++){
			                        //循环遍历Keyword关键词
			                        if(strtolower($usrSearchWord) == strtolower($KeywordArray[$KeywordI]) || strtolower($usrSearchWord) == trim(strtolower($KeywordArray[$KeywordI]))){
			                            //找到了, 关键字介于第1-5个之间不减rank, 5-15减10, 15到40减15, 40以上-20
			                            $KeywordFinded = true;
			                            if($KeywordI >= 5 && $KeywordI < 15){
			                                $TempRST['searchRank'] -= 10;
			                            }elseif($KeywordI >= 15 && $KeywordI < 40){
			                                $TempRST['searchRank'] -= 15;
			                            }elseif($KeywordI >= 40){
			                                $TempRST['searchRank'] -= 20;
			                            }
			                            break;
			                        }else if(strpos(strtolower($KeywordArray[$KeywordI]), strtolower($usrSearchWord))){
			                            //搜索到部分内容, 介于1-3个不减rank,3-10减10, 10-25减15， 25以上-20
			                            $KeywordFinded = true;
			                            if($KeywordI >= 3 && $KeywordI < 10){
			                                $TempRST['searchRank'] -= 10;
			                            }elseif($KeywordI >= 10 && $KeywordI < 25){
			                                $TempRST['searchRank'] -= 15;
			                            }elseif($KeywordI >= 25){
			                                $TempRST['searchRank'] -= 20;
			                            }
			                        }
			                    }
			                    //遍历完毕, 检索是否找到
			                    if(!$KeywordFinded){
			                        //没找到， -25
			                        $TempRST['searchRank'] -= 25;
			                    }
			                    
			                }else{
			                    //没有关键字, 蛇皮
			                    $TempRST['searchRank'] -= 20;
			                }
			            }else if($DBVariableName == "Rank"){
			                $TempRST['Rank'] = $DBVariableVal;
			                $TempRST['searchRank'] += ($DBVariableVal * 5);
			            }
			        }
			        //单个结果统计完毕, 准备投放数据到总Array
			        if($TempRST['searchRank'] > 0){
			            $SearchRST[] = $TempRST;
			        }
			    }
			    //所有统计完毕, 检查是否empty
			    if(!empty($SearchRST)){
			        require_once '../BoostPHP/BoostPHP.Alg.php';
			        $myStrCls = new BoostPHP_StringClass();
			        $myAlgCls = new BoostPHP_AlgorithmClass();
			        $newSearchRST = $myAlgCls -> quickSortArrays_ByField($SearchRST, "searchRank");
			        for($i=count($newSearchRST)-1;$i>=0;$i--){
			            $TempOutputArr = $newSearchRST[$i];
			            $TempOutputArr['Title'] = $myStrCls->wordLimit($TempOutputArr['Title'],100,true);
			            $TempOutputArr['Description'] = $myStrCls->wordLimit($TempOutputArr['Description'],250,true);
			            if(!empty($TempOutputArr['URL'])){
			                ?>
        		                <div class="row row-as-base">
        		                    <div class="col col-phone-12 col-comp-8">
        		                        <h4><a href="<?php echo $TempOutputArr['URL']; ?>" target="_blank"><?php echo empty($TempOutputArr['Title']) ? "No Title" : $TempOutputArr['Title']; ?></a></h4>
        		                        <p><?php echo $TempOutputArr['Description']; ?></p>
        		                        <p class="small">URL: <?php echo $TempOutputArr['URL'];  ?></p>
        		                        <p class="small">Reference: searchWeight[<?php echo $TempOutputArr['searchRank']; ?>/100], siteRank[<?php echo $TempOutputArr['Rank']; ?>/10]</p>
        		                    </div>
        		               	</div>
			        	    		<?php
			            }
			        }
			    }
			    $myMySQLCls->closeConn($searchDBConn);
			?>
		</div>
	</body>
</html>