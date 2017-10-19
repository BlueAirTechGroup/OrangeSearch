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
		?>
		<meta charset="utf-8" />
		<script src="https://www.xsyds.cn/js/core.js"></script>
		<script src="https://www.xsyds.cn/js/jquery.min.js"></script>
        <script src="https://www.xsyds.cn/js/BOOST.main.js"></script>
        <link type="text/css" href="https://www.xsyds.cn/css/BOOST.main.css" rel="stylesheet" />
        <link type="text/css" href="https://www.xsyds.cn/css/BOOST.animate.css" rel="stylesheet" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title><?php echo $usrSearchWord ?> - 橙子搜索 | 你的搜索, 比以前更安全</title>
		<meta name="keywords" content="搜索, 在线搜索, 匿名, 互联网思维, 创新, 开源, 搜索引擎， 搜索" />
        <meta name="description" content="橙子搜索是由形随意动推出的新时代安全搜索服务. 橙子搜索不会主动跟踪您的搜索记录， 尽管您要求他们被记录下来." />
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
        			<span id="OSTitle" class="display-normal-comp">橘子搜索</span><form action="" method="post" style="display:inline-block;"><input type="text" name="searchKeyword" id="searchKeyword" value="<?php echo $usrSearchWord; ?>"></input><input class="backgroundimg-cover" type="submit" name="reSearchBtn" id="reSearchBtn" value="搜索" title="搜索"></input></form><a href="../?selectLang=true" id="changeLangBtn">Language</a></div>
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
			    $startTime = microtime(true);
			    $cacheRST = $myResultCls->cacheStart(43200, true, 'cachepages/');
			    if($cacheRST){
        			    $searchDBConn = $myMySQLCls -> connectDB($CONFIG_MYSQLUSER, $CONFIG_MYSQLPASS, $CONFIG_MYSQLDB);
        			    if(!searchDBConn){
        			        exit('<p class="lead">内部错误: 连接数据库失败</p>');
        			    }
        			    
        			    $SearchRST = array();
        			    $MySQLDBNum = $myMySQLCls->checkExist($searchDBConn, 'SearchRstList', array(),array());
        			    //$MySQLDBLastNum = $MySQLDBNum % $CONFIG_PARTITIONSIZE;
        			    $MySQLIterationTime = ceil($MySQLDBNum / $CONFIG_PARTITIONSIZE);
        			    for($partialit = 0; $partialit < $MySQLIterationTime; $partialit++){
        			        //开始分段select
        			        $MySQLSDBRst = $myMySQLCls->selectIntoArray_FromRequirements($searchDBConn, "SearchRstList",array(),array("Rank"),$CONFIG_PARTITIONSIZE,($partialit*$CONFIG_PARTITIONSIZE));
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
                			        unset($MySQLSDBRst['result'][$i]); //单个结果访问完毕,释放内存
                			    }
                			    unset($MySQLSDBRst); //释放总内存
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
        			                <!-- >div class="row row-as-base">
        			                    <div class="col col-phone-12 col-comp-8" -->
        			                    <div class="container" style="margin-top:20px;">
        			                        <h4 class="without-margin"><a class="text-orange" href="<?php echo $TempOutputArr['URL']; ?>" target="_blank"><?php echo empty($TempOutputArr['Title']) ? "No Title" : $TempOutputArr['Title']; ?></a></h4>
        			                        <p class="small without-margin text-grey">URL: <?php echo $TempOutputArr['URL'];  ?></p>
        			                        <p class="without-margin"><?php echo $TempOutputArr['Description']; ?></p>
        			                        <p class="small without-margin">参考信息: 搜索重量[<?php echo $TempOutputArr['searchRank']; ?>/100], 权重[<?php echo $TempOutputArr['Rank']; ?>/10]</p>
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
    			?>
			<div class="container" style="margin-top:20px;">
        			<p class="text-grey">使用内存: <?php echo((memory_get_peak_usage()/1024/1024)); ?>M</p>
        			<p class="text-grey">总执行时间: <?php $nowTime = microtime(true); echo ($nowTime-$startTime); ?>秒</p>
        			<p class="text-grey">Powered by <a href="http://www.xsyds.cn/" target="_blank">形随意动</a>&copy;2015-2017</p>
				<?php if(!$cacheRST){ ?> <p class="text-grey">使用形随意动BoostPHP框架进行快速缓存</p> <?php } ?>
			</div>
	</body>
</html>