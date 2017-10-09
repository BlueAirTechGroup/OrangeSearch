<?php
    /*
     * Give a rank for the URL
     * Range: 0 - 100
     * 
     */
    function checkURL($URL){
        $URLComponent = parse_url($URL);
        $NonTakenList = array(
            "www.baidu.com",
            "www.sogou.com",
            "www.so.com"
        );
        foreach($NonTakenList as $MyNon){
            if(strpos($URL, $MyNon)!==false){
                return false;
            }
        }
        return true;
    }
    function MakeAURL($URL,$PastURL){
        $URLComponent = parse_url($URL);
        if(strpos($URL,"http://")!==0 && strpos($URL,"https://")!==0 && strpos($URL,"//")!==0){
            //需要BaseURL
            $URLComponent = parse_url($PastURL);
            if(empty($URLComponent['path'])){$URLComponent['path']='/';}
            $baseURL = strtolower($URLComponent['scheme']) . '://';
            if(!empty($URLComponent['user']) && empty($URLComponent['pass'])){
                $baseURL.= $URLComponent['user'] . '@';
            }
            if(!empty($URLComponent['user']) && !empty($URLComponent['pass'])){
                $baseURL.= $URLComponent['user'] . ':' . $URLComponent['pass']  . '@';
            }
            $baseURL.= strtolower($URLComponent['host']);
            $pathArr = explode("/",$URLComponent['path']);
            $pathLevel = count($pathArr)-1;
            while(strpos($URL,"../")===0){
                $URL = substr($URL,3);
                $pathLevel--;
            }
            if(!empty($URLComponent['path'])){
                for($i=0;$i<$pathLevel;$i++){
                    $baseURL .= $pathArr[$i] . '/';
                }
            }
            if(strpos($URL,"/")===0){
                $URL = substr($URL, 1);
            }
            $newURL = $baseURL . $URL;
            return $newURL;
        }else{
            return $URL;
        }
    }
    function calculate_Rank($URL,$Title,$Keywords,$Description){
        $MyRank = 100;
        //首先计算是否为顶级域名， 2级域名-10Rank， 3级-20， 以此类推
        $URLComponent = parse_url($URL);
        
        $URLHost = $URLComponent['host'];
        $domainLevelArr = explode('.', $URLHost);
        $domainLevel = $domainLevelArr[0]=='www' ? count($domainLevelArr)-1 : count($domainLevelArr);
        if($domainLevel > 2){
            echo 'DomainLevel:' . ($domainlevel - 1);
            //2,3,4...级域名
            $MyRank -= ($domainLevel-2) * 10;
        } 
        //其次查看域名的
        //接着计算Path是否为顶级path, 如果不是, 发现一个/减去15 rank
        $URLPath = $URLComponent['path'];
        $PathLevelArr = explode('/', $URLPath);
        $PathLevel = count($PathLevelArr);
        if($PathLevel > 2){
            echo 'PathLevel: ' . ($PathLevel-2);
            $MyRank -= ($PathLevel - 2) * 15;
        }
        //然后看协议, 如果是http,-5, 其他协议-20
        if($URLComponent['scheme']=='http' || empty($URLComponent['scheme'])){
            $MyRank -= 5;
        }else if($URLComponent['scheme']!='https'){
            echo 'scheme: ' . $URLComponent['scheme'];
            $MyRank -= 20;
        }
        
        //然后查看Contents， Title
        $SiteName = '';
        $SiteNameSplit = '';
        $henggangPos = strpos($Title,'-');
        $shugangPos = strpos($Title,'|');
        if($henggangPos !== false && ($shugangPos >= $henggangPos || $shugangPos === false)){
            $SiteNameSplit = '-';
        }else if($shugangPos !== false && $henggangPos !== false){
            $SiteNameSplit = '|';
        }
        if(!empty($SiteNameSplit)){
            $SiteNameArr = explode($SiteNameSplit, $Title);
            $SiteName = trim($SiteNameArr[0]);
        }else{
            $SiteName = $Title;
        }
        echo 'SiteName: ' . $SiteName;
        
        //如果SiteName在Description
        if(strpos($Description,$SiteName)===false){
            //不在, 权重 - 15
            $MyRank -= 10;
        }else if(strpos($Description,$SiteName)>=30){
            //30字开外, -20
            $MyRank -= 20;
        }
        if(empty($Description)){
            //没有description, rank-15
            $MyRank -= 15;
        }
        if(empty($Keywords)){
            //没有keywords, rank-20
            $MyRank -= 20;
        }
        if(empty($Title)){
            //没有title, rank-50
            $MyRank -= 50;
        }
        return $MyRank;
    }
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
    require_once 'config.php';
    require_once 'BoostPHP/BoostPHP.main.php';
    require_once 'simple_html_dom.php';
    
    set_time_limit(0);
    //ob_end_clean(); //清除之前的缓冲内容，这是必需的，如果之前的缓存不为空的话，里面可能有http头或者其它内容，导致后面的内容不能及时的输出
    //header("Connection: close");//告诉浏览器，连接关闭了，这样浏览器就不用等待服务器的响应
    header("HTTP/1.1 200 OK"); //可以发送200状态码，以这些请求是成功的，要不然可能浏览器会重试，特别是有代理的情况下
    ignore_user_abort(true); //无视浏览器关闭, 继续执行
    $usrPassword = $_GET['Auth'];
    $usrCrawTimeLim = !empty($_GET['TimeLimit']) ? $_GET['TimeLimit'] : 300000; //默认采集5分钟
    $programStartTime = microtime(true);
    //1 second = 1000 millisecond = 1000,000 microsecond = 1000,000,000 nanosecond
    if($usrPassword != $CONFIG_CRAWPASS){
        header("HTTP/1.0 404 Not Found");
        exit("404 Not Found");
    }
    $myMYSQLCls = new BoostPHP_MySQLClass();
    $dbConn = $myMYSQLCls->connectDB($CONFIG_MYSQLUSER, $CONFIG_MYSQLPASS, $CONFIG_MYSQLDB);
    if(!$dbConn){
        exit("Error: Cannot connect to the Databse");
    }
    $crawURLTaken = true;
    $crawURLTitle = '';
    $crawURLDescription = '';
    $crawURLKeyword = '';
    $crawContent = '';
    $crawURL = '';
    $crawURLRank = 10;
    $mySimpleDom = new simple_html_dom();
    $myNetworkCls = new BoostPHP_NetworkClass();
    $myStringCls = new BoostPHP_StringClass();
    while(round((microtime(true)-$programStartTime)*1000) < $usrCrawTimeLim){
        echo '<br />CurrentTime: ' . round((microtime(true)-$programStartTime)*1000) . '; ';
        //当前时间与启动时间减去小于CrawTimeLim时, 不断采集
        $crawContent = '';
        $crawURLTaken = true;
        $crawURLRank = 10;
        
        $waitForCrawList = $myMYSQLCls->selectIntoArray_FromRequirements($dbConn, 'PendingScanList');
        if($waitForCrawList['count']==0){
            exit("FINISHED: The Pending List is empty");
        }
        $tempcrawURL = $waitForCrawList['result'][0]['URL'];
        $tempcrawURLarr = parse_url($tempcrawURL);
        if(empty($tempcrawURLarr['path'])){$tempcrawURLarr='/';}
        $crawURL = strtolower($tempcrawURLarr['scheme']) . '://';
        if(!empty($tempcrawURLarr['user']) && empty($tempcrawURLarr['pass'])){
            $crawURL.= $tempcrawURLarr['user'] . '@';
        }
        if(!empty($tempcrawURLarr['user']) && !empty($tempcrawURLarr['pass'])){
            $crawURL.= $tempcrawURLarr['user'] . ':' . $tempcrawURLarr['pass']  . '@';
        }
        $crawURL.= strtolower($tempcrawURLarr['host']);
        if(!empty($tempcrawURLarr['path'])){
            $crawURL.= $tempcrawURLarr['path'];
        }
        if(!empty($tempcrawURLarr['query'])){
            $crawURL .= "?" . $tempcrawURLarr['query'];
        }
        if(empty($crawURL)){
            //神他妈URL为空, 直接跳过
            continue;
        }
        echo 'crawURL:' . $crawURL . '; ';
        //先把URL从MySQL表中删除
        $myMYSQLCls->deleteRows($dbConn, 'PendingScanList', array("URL"=>$tempcrawURL));
        //添加到已采集列表
        $myMYSQLCls->insertRow($dbConn, 'ScannedList', array("URL"=>$crawURL));
        $crawContent = $myNetworkCls->getFromAddr($crawURL, $crawURL);
        $EncodingUTF8MethodPos = strpos(strtolower($crawContent),"charset=utf-8") || strpos(strtolower($crawContent),'charset="utf-8"') || strpos(strtolower($crawContent),"charset=utf8") || strpos(strtolower($crawContent),'charset="utf8"');
        if(!$EncodingUTF8MethodPos){
            echo 'Page-IS-GBK';
            //不是UTF-8, 目前认为是GBK
            $crawContent=iconv("GBK", "UTF-8//IGNORE", $crawContent);
        }else{
            echo 'Page-IS-UTF';
            //$crawContent=iconv("UTF-8",'GBK//IGNORE',$crawContent);
        }
        $mySimpleDom->load($crawContent);
        $crawDomTitles = $mySimpleDom->find('title',0);
        if(empty($crawDomTitles)){
            //Title不存在
            $crawURLTitle = '';
        }else{
            $crawURLTitle = $crawDomTitles->innertext;
        }
        $crawDomDescriptions = $mySimpleDom->find('meta[name=description]',0);
        if(empty($crawDomDescriptions)){
            //Description不存在
            $crawURLDescription = $myStringCls->wordLimit($mySimpleDom->find('body',0)->plaintext,300,true);
        }else{
            $crawURLDescription = $myStringCls->wordLimit($crawDomDescriptions->content,300,true);
        }
        $crawDomKeywords = $mySimpleDom->find('meta[name=keywords]',0);
        if(empty($crawDomKeywords)){
            //页面没有keywords
            $crawURLKeyword = '';
        }else{
            $crawURLKeyword = $crawDomKeywords->content;
        }
        //计算权重开始
        $crawURLRank = round(calculate_Rank($crawURL, $crawURLTitle, $crawURLKeyword, $crawURLDescription)/10);
        echo 'Rank:' . $crawURLRank . '; ';
        //录入信息
        if($crawURLRank<0 || strpos(strtolower($crawURLTitle),"404")!==false || strpos(strtolower($crawURLTitle),"301")!==false || strpos(strtolower($crawURLTitle),"500")!==false || strpos(strtolower($crawURLTitle),"错误")!==false || strpos(strtolower($crawURLTitle),"error")!==false){
            $crawURLTaken = false;
            if($myMYSQLCls->checkExist($dbConn, 'NonTakenList', array("URL"=>$crawURL))==0){
                $myMYSQLCls->insertRow($dbConn, 'NonTakenList', array("URL"=>$crawURL,"Title"=>base64_encode($crawURLTitle),"Rank"=>$crawURLRank,"LastAccess"=>time()));
            }
        }else if(checkURL($crawURL) && !empty($crawContent) && $myMYSQLCls->checkExist($dbConn, 'SearchRstList', array("URL"=>$crawURL))==0){ //只有符合采集规则的网页才可被采集
            $crawURLTaken = true;
            $myMYSQLCls->insertRow($dbConn, 'SearchRstList', array("URL"=>$crawURL,"Title"=>base64_encode($crawURLTitle),"Description"=>base64_encode($crawURLDescription),"Keywords"=>base64_encode($crawURLKeyword),"Rank"=>$crawURLRank,"LastAccess"=>time()));
        }
        //获取该网页所关联的AHref链接
        $crawLinks = $mySimpleDom->find("a[href]");
        if(!empty($crawLinks)){
            foreach($crawLinks as $myLink){
                //首先检查是否存在
                $AnchorActualURL = MakeAURL($myLink->href, $crawURL);
                if($myMYSQLCls->checkExist($dbConn, 'ScannedList', array("URL"=>$AnchorActualURL))==0 && $myMYSQLCls->checkExist($dbConn, 'PendingScanList', array("URL"=>$AnchorActualURL))==0){
                    $myMYSQLCls->insertRow($dbConn, 'PendingScanList', array("URL"=>$AnchorActualURL));
                }
            }
        }
        $mySimpleDom->clear();
    }
    $myMYSQLCls->closeConn($dbConn);
?>