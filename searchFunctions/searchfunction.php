<?php

function calculateSearchWeight($RowInfo,$usrSearchWord){
    $TempRST = array("URL"=>"","Title"=>"","Description"=>"","Keywords"=>"","searchRank"=>0,"Rank"=>0);
    $TempRST['searchRank'] += 50; //首先给加50 Search rank
    $usrLowerSearchWord = strtolower($usrSearchWord);
    foreach($RowInfo as $DBVariableName => $DBVariableVal){
        if($DBVariableName == "Title"){
            $TempRST['Title'] = base64_decode($DBVariableVal);
            $TempPos = strpos(strtolower($TempRST['Title']),$usrLowerSearchWord);
            if($TempPos===false){
                //Title未找到关键词, SearchRank - 50
                $TempRST['searchRank'] -= 50;
            }else if($TempPos<=30){
                $TempRST['searchRank'] += 30;
            }
        }else if($DBVariableName=="URL"){
            $TempRST['URL'] = $DBVariableVal;
            if(strpos(strtolower($TempRST['URL']),$usrLowerSearchWord)!==false){
                $TempRST['searchRank'] += 20;
            }
        }else if($DBVariableName=="Description"){
            $TempRST['Description'] = base64_decode($DBVariableVal);
            //Description判断, 如果在20字后, 则Rank降低
            $DescriptionPos = strpos(strtolower($TempRST['Description']), $usrLowerSearchWord);
            if($DescriptionPos === false){
                //Description未找到关键词! SearchRank - 30
                $TempRST['searchRank'] -= 30;
            }else if($DescriptionPos <= 50){
                $TempRST['searchRank'] += 30;
            }
        }else if($DBVariableName == "Keywords"){
            $TempRST['Keywords'] = base64_decode($DBVariableVal);
            $TempKeywords = strtolower($TempRST['Keywords']);
            /* 
             * Old Version(In efficient)
            $KeywordArray = explode(',',$TempRST['Keywords']);
            $KeywordFinded = false;
            if(!empty($TempRST['Keywords']) && !empty($KeywordArray)){
                for($KeywordI=0;$KeywordI<count($KeywordArray);$KeywordI++){
                    //循环遍历Keyword关键词
                    $CurrentKeyword = strtolower($KeywordArray[$KeywordI]);
                    if($usrLowerSearchWord == $CurrentKeyword || $usrLowerSearchWord == trim($CurrentKeyword)){
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
                    }else if(strpos($CurrentKeyword, $usrLowerSearchWord)){
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
            } */
            $KeywordFinded = strpos($TempKeywords, $usrLowerSearchWord);
            if($KeywordFinded === false || $KeywordFinded>100){
                $TempRST['searchRank'] -= 20;
            }elseif($KeywordFinded >=50){
                $TempRST['searchRank'] -= 15;
            }elseif($KeywordFinded >= 25){
                $TempRST['searchRank'] -= 5;
            }
        }else if($DBVariableName == "Rank"){
            $TempRST['Rank'] = $DBVariableVal;
            $TempRST['searchRank'] += ($DBVariableVal * 5);
        }
    }
    return $TempRST;
}

?>