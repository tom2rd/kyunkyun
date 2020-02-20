<?php   

// XML保存するデータ数
$FrameCount = (60);  // 60秒　
//$FrameCount = (60 * 60 * 3);  // 60秒　＊　60　分 * 3
$nowTime = time();                      // 現在の秒数
//$nowTime = intdiv(time(), 5) * 5;     // 全て5の倍数で丸める

$TimeIndex = $nowTime % $FrameCount;    // 現在の書き込みIndexを計算

//　排他スタート
$lockfilename = 'lock'; //　排他用のファイル名
//＊＊ロック用ファイルのオープン＊＊
$lockfp = fopen($lockfilename,'w');
//＊＊ロック用ファイルのロック＊＊
flock($lockfp, LOCK_EX);

//　CounterXMLの読み込み
$xml = simplexml_load_file('./Counter.xml');
$TimeCount = $xml->xpath('/Counter/Sec/TimeCount[@Index="'.($TimeIndex).'"]');
if(count($TimeCount) > 0 )
{
    // Indexが合っていてもTimeが違う場合は、過去のデータなので0扱いにする。
    if((int)$TimeCount[0]["Time"] == (int)$nowTime)
    {
        //　すでに同じ時間のエレメントが存在する場合は、カウントアップ
        $TimeCount[0]["Value"] = (int)$TimeCount[0]["Value"] + 1;
    }else {
        //　エレメントは存在するが、時間が異なる場合は、時間を更新してカウントを１とする。
        $TimeCount[0]["Time"] = $nowTime;
        $TimeCount[0]["Value"] = 1;
    }
}else 
{
    //　エレメントが存在しない場合は追加する、
    $SecRootItem = $xml->xpath('/Counter/Sec');
    $result = $SecRootItem[0]->addChild("TimeCount");
    $result[0]['Index'] = $TimeIndex;
    $result[0]['Time'] = $nowTime;
    $result[0]['Value'] = 1;
}

$DayIndex = date('Ymd') ;           // 本日付　例：20200220

// デイリーのカウンター
$DayCount = $xml->xpath('/Counter/Day/DayCount[@Index="'.($DayIndex).'"]');
if(count($DayCount) > 0 )
{
    //　同一日付のカウンターが存在する場合は、カウントアップ
    $DayCount[0]["Value"] = (int)$DayCount[0]["Value"] + 1;
 }else 
{
    //　同一日付のカウンターが存在しない場合は、タグを作成してカウントを１とする。
    $SecRootItem = $xml->xpath('/Counter/Day');
    $result = $SecRootItem[0]->addChild("DayCount");
    $result[0]['Index'] = $DayIndex;
    $result[0]['Value'] = 1;
}

//　XMLの更新
$xml->saveXML('./Counter.xml');


//＊＊ロック用ファイルのロックの開放＊＊
flock($lockfp, LOCK_UN);
//＊＊ロック用ファイルのクローズ＊＊
fclose($lockfp);
//　排他終了


?>
