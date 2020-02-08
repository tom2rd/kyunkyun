<?php   

// XML保存するデータ数
//$FrameCount = (60 * 60 * 3);  // 60秒　＊　60　分 * 3
$FrameCount = (60);  // 60秒　＊　60　分 * 3

//　現在のサーバー時間
//$nowTime = intdiv(time(), 5) * 5;           // 全て5の倍数で丸める
$nowTime = intdiv(time(), 1);           // 全て5の倍数で丸める


//　
$TimeIndex = $nowTime % $FrameCount;  // 60秒　＊　60　分 * 3

//$CurrentTime = $nowTime - (60*3);

$CurrentTime = $nowTime - $FrameCount;

if( isset($_GET["LastTime"]) )
{
    if($_GET["LastTime"] > $CurrentTime)
    {
        $CurrentTime = (int)$_GET["LastTime"];
    }
}

//　CounterXMLの読み込み
$xml = simplexml_load_file('./Counter.xml');

// 空の配列を用意します。
$ret = array();

while ($CurrentTime < $nowTime)
{
    $Cnt=0;
    $TimeCount = $xml->xpath('/Counter/Sec/TimeCount[@Index="'.($CurrentTime % $FrameCount).'"]');
    if(count($TimeCount) > 0 )
    {
        // Indexが合っていてもTimeが違う場合は、過去のデータなので0扱いにする。
        if((int)$TimeCount[0]["Time"] == $CurrentTime)
        {
            $Cnt = (int)$TimeCount[0]["Value"];
        }
    }else {
        $Cnt = 0;
    }
    $ret[] = array('time'=>$CurrentTime, 'y'=>$Cnt);
/*
    $ret[] = array('time'=>$CurrentTime+1, 'y'=>$Cnt);
    $ret[] = array('time'=>$CurrentTime+2, 'y'=>$Cnt);
    $ret[] = array('time'=>$CurrentTime+3, 'y'=>$Cnt);
    $ret[] = array('time'=>$CurrentTime+4, 'y'=>$Cnt);
    $CurrentTime = $CurrentTime + 5;
    */
    $CurrentTime = $CurrentTime + 1;
}

$NowCnt=0;
$NowTimeCount = $xml->xpath('/Counter/Sec/TimeCount[@Time="'.$CurrentTime.'"]');
if(count($NowTimeCount) > 0 )
{
    $NowCnt = (int)$NowTimeCount[0]["Value"];
}

$DayIndex = date('Ymd') ;           // 日付

// デイリーのカウンター
$DayCount = $xml->xpath('/Counter/Day/DayCount[@Index="'.($DayIndex).'"]');
$DayCnt = 0;
if(count($DayCount) > 0 )
{
    $DayCnt = (int)$DayCount[0]["Value"];
}

// 配列をjson_encode関数でJSON形式に変換します。
$retstrct = array('ServerTime'=>$nowTime, 'LastTime'=>$CurrentTime, 'NowCount'=>$NowCnt, 'DayCount'=>$DayCnt, 'values'=>$ret);
$json = json_encode($retstrct);
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
echo json_encode($retstrct);

// 変換したJSON形式である$jsonを表示します。
//print($json);

?>
