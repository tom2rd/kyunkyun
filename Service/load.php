<?php   
if(isset($_GET['id']))
{
    // カウンターファイル名
    $CounterFileName = "./data/" . $_GET['id'] ."/Counter.xml"; 

    // XML保存するデータ数
    $FrameCount = (60);  // 60秒
    //$FrameCount = (60 * 60 * 3);  // 60秒　＊　60　分 * 3

    //　現在のサーバー時間秒
    $nowTime = time();              // 現在の秒数
    //$nowTime = intdiv(time(), 5) * 5;     // 全て5の倍数で丸める

    $CurrentTime = $nowTime - $FrameCount;  //　読み込み時間秒　（初期値を現在日時-フレーム数することで全てのフレームを読み込むようにする）

    if( isset($_GET["LastTime"]) )
    {
        //　URLパラメータにLastTimeが指定される場合
        if($_GET["LastTime"] > $CurrentTime)
        {
            //　LastTimeが　フレームの最初の時間よりも新しければ、読み込み開始位置をパラメータの時間に変更する。
            $CurrentTime = (int)$_GET["LastTime"];
        }
    }

    //　CounterXMLの読み込み
    $xml = null;
    if (file_exists($CounterFileName)) 
    {
        $xml = simplexml_load_file($CounterFileName);
    }

    $retValues = array();   //秒ごとのカウンタ値を入れる配列

    //　読み出し位置が今の時間の１秒前まで実行
    while ($CurrentTime < $nowTime)
    {
        $Cnt=0;
        $TimeCount = array();   // 空の配列で初期化
        if($xml != null)
        {
            // カレント時間秒数をフレーム数で割った余りをインデックスとする
            $TimeCount = $xml->xpath('/Counter/Sec/TimeCount[@Index="'.($CurrentTime % $FrameCount).'"]');
        }
        if(count($TimeCount) > 0 )
        {
            // 対象のタグが存在した場合
            // Indexが合っていてもTimeが違う場合は、過去のデータなので0扱いにする。
            if((int)$TimeCount[0]["Time"] == $CurrentTime)
            {
                //　カウンタ値を読み込む
                $Cnt = (int)$TimeCount[0]["Value"];
            }
        }else {
            //　対象のタグが存在しない場合
            $Cnt = 0;
        }
        //　レスポンス用の構造体に追加する
        $retValues[] = array('time'=>$CurrentTime, 'y'=>$Cnt);

        //　カレント時間を１秒進める
        $CurrentTime = $CurrentTime + 1;
    }

    // 現在のカウンタは過去5秒分の合計
    $NowCnt=0;                      //　現在のカウント数
    $NowCurrentTime = $nowTime - 5;    //　カレントの初期値を５秒前に再設定

    //　読み出し位置が今の時間まで実行
    while($NowCurrentTime <= $nowTime)
    {
        $NowTimeCount = array();    // 空の配列で初期化
        if($xml != null)
        {
            $NowTimeCount = $xml->xpath('/Counter/Sec/TimeCount[@Time="'.$NowCurrentTime.'"]');
        }
        if(count($NowTimeCount) > 0 )
        { 
            // 対象のタグが存在した場合
            // Indexが合っていてもTimeが違う場合は、過去のデータなので0扱いにする。
            if((int)$NowTimeCount[0]["Time"] == $NowCurrentTime)
            {
                // 存在した場合、現在のカウント値に追加
                $NowCnt = $NowCnt + (int)$NowTimeCount[0]["Value"];
            }
        }

        //　カレント時間を１秒進める
        $NowCurrentTime = $NowCurrentTime + 1;
    }

    $DayIndex = date('Ymd') ;           // 本日付　例：20200220

    // デイリーのカウンターを取得
    $DayCount = array();
    if($xml != null)
    {
        $DayCount = $xml->xpath('/Counter/Day/DayCount[@Index="'.($DayIndex).'"]');
    }
    $DayCnt = 0;
    if(count($DayCount) > 0 )
    {
        //　本日のカウントエレメントが存在していた場合は、値を格納
        $DayCnt = (int)$DayCount[0]["Value"];
    }

    // 配列をjson_encode関数でJSON形式に変換します。
    $retstrct = array('ServerTime'=>$nowTime, 'LastTime'=>$CurrentTime, 'NowCount'=>$NowCnt, 'DayCount'=>$DayCnt, 'values'=>$retValues);
    //　構造体をJSONに変換
    $json = json_encode($retstrct);

    //　メタ情報を追加
    header('Content-Type: application/json');
    header("Access-Control-Allow-Origin: *");
    echo json_encode($retstrct);
}else
{
    print("Parameter　is null. ".$_GET['id']);
}

?>
