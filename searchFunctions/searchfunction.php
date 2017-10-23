<?php

function calculateSearchWeight($RowInfo,$usrSearchWord){
    $TempRST = array("URL"=>"","Title"=>"","Description"=>"","Keywords"=>"","searchRank"=>0,"Rank"=>0);
    $TempRST['searchRank'] += 50; //首先给加50 Search rank
    foreach($RowInfo as $DBVariableName => $DBVariableVal){
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
    return $TempRST;
}

?>