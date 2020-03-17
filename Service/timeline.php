<?php
    $CounterID = "Default";
    if(isset($_GET['id']))
    {
        $CounterID = $_GET['id'];
    }
    $Chartheight = 220;
    if(isset($_GET['height']))
    {
        $Chartheight = $_GET['height'];
    }
    $ChartMax = 50;
    if(isset($_GET['max']))
    {
        $ChartMax = $_GET['max'];
    }

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                
        <script src="https://code.jquery.com/jquery-1.10.1.min.js"></script>
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
        
        <!-- svg処理ライブラリ epochを利用する為のベースライブラリ-->
        <script src="https://d3js.org/d3.v3.js" charset="utf-8"></script>
        <script src="./js/epoch.min.js"></script>
                
        <link rel="stylesheet" type="text/css" href="./css/epoch.min.css">
        <style>
            
            body {
                font-family: helvetica;
            }

            /* キュンキュンタイムライン */
            #TimeLineGraph .epoch {
                margin: 0 auto;            /* 左右中央*/
                vertical-align:  top;      /* 要素を上揃えにする */ 
                height: <?php echo $Chartheight; ?>px;
            }
          

            </style>
    </head>
    <body>

 
        <!-- 時系列Graph -->
        <div id="TimeLineGraph" >
            <div class="epoch"></div>
        </div>

        <script>
            var CounterID = "<?php echo $CounterID; ?>";

            //　初期設定
            $(function() 
            {
                var LastTime = 0;   //　クライアントサイドで現在保持している最終時間保存
                var chart;          //　タイムラインのエレメント参照
             
                //　カウンタ情報を引数なし（全時間で取得）
                $.get("load.php",{id:CounterID},
                function(data)
                {
                    //　load.phpの応答が帰って来た時のコールバック関数

                    // 応答に含まれる最終時間を保存（次回の問い合わせに利用する）
                    LastTime = data.ServerTime;
                    
                    //　タイムラインの初期化
                    var leftRange = [0, <?php echo $ChartMax; ?>];    //　縦軸の幅を設定
                    var initData = [{ label: 'A', values: data.values, range: leftRange }];　//　ラインの情報を設定　ラベル名、　時系列データ、　レンジ
                    
                    //　タイムチャートを初期化
                    chart = $('#TimeLineGraph .epoch').epoch({
                            type: 'time.line',
                            range: {
                                left: leftRange
                            },
                            data: initData,
                            axes: ['left', 'right', 'bottom']
                            });  

                    //　更新の為にタイマーのコールバックを設定
                    interval = setInterval(pushPoint, 1000);    //　一秒
                });

                //　インターバル処理から呼ばれる関数　毎秒実行
                var pushPoint = function() 
                {
                    //　カウンター状況を取得
                    $.get("load.php",{id:CounterID, LastTime : LastTime},
                    function(data)
                    {
                        //　１秒ごとのデータがあったらチャートに追加
                        LastTime = data.ServerTime;
                        for( let idx in data.values)
                        {
                            chart.push([data.values[idx]]);
                        }

                    });
                };

            
            });
        </script>

    </body>
</html>
