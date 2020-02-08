<?php   

// XML保存するデータ数
//$FrameCount = (60 * 60 * 3);  // 60秒　＊　60　分 * 3
$FrameCount = (60);  // 60秒　＊　60　分 * 3


//$nowTime = intdiv(time(), 5) * 5;           // 全て5の倍数で丸める
$nowTime = intdiv(time(), 1);           // 全て5の倍数で丸める
$TimeIndex = $nowTime % $FrameCount;  // 60秒　＊　60　分 * 3

//　CounterXMLの読み込み
$xml = simplexml_load_file('./Counter.xml');
$TimeCount = $xml->xpath('/Counter/Sec/TimeCount[@Index="'.($TimeIndex).'"]');
if(count($TimeCount) > 0 )
{
    // Indexが合っていてもTimeが違う場合は、過去のデータなので0扱いにする。
    if((int)$TimeCount[0]["Time"] == (int)$nowTime)
    {
        echo "カウントアップ";
        $TimeCount[0]["Value"] = (int)$TimeCount[0]["Value"] + 1;
    }else {
        echo "カウントリセット time=".$TimeCount[0]["Time"]."nowTime=".$nowTime."index=".$TimeIndex;
        $TimeCount[0]["Time"] = $nowTime;
        $TimeCount[0]["Value"] = 1;
    }
}else 
{
    echo "ノード追加";
    $SecRootItem = $xml->xpath('/Counter/Sec');
    $result = $SecRootItem[0]->addChild("TimeCount");
    $result[0]['Index'] = $TimeIndex;
    $result[0]['Time'] = $nowTime;
    $result[0]['Value'] = 1;
}

$DayIndex = date('Ymd') ;           // 日付

// デイリーのカウンター
$DayCount = $xml->xpath('/Counter/Day/DayCount[@Index="'.($DayIndex).'"]');
if(count($DayCount) > 0 )
{
    $DayCount[0]["Value"] = (int)$DayCount[0]["Value"] + 1;
 }else 
{
    echo "ノード追加";
    $SecRootItem = $xml->xpath('/Counter/Day');
    $result = $SecRootItem[0]->addChild("DayCount");
    $result[0]['Index'] = $DayIndex;
    $result[0]['Value'] = 1;
}



$xml->saveXML('./Counter.xml');

?>
