<?php
$CounterID = "Default";
if(isset($_GET['id']))
{
    $CounterID = $_GET['id'];
}
$ChartMax = 200;
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


            /*本日のキュンキュンカウントゲージエリア*/
            #Area_DailyCount{
                text-align: center;         /*タイトルを横中央に*/
            }            

            /*本日のキュンキュンカウントゲージ*/
            #Gauge_Daily{
                margin: 0 auto;             /*左右中央*/
            }            

            /*本日のキュンキュンカウントゲージエリア*/
            #MonitorButton{
                margin: 5px auto;            /* 左右中央*/
                text-align: center;         /*タイトルを横中央に*/
                font-weight:bold;
                text-decoration:none;
                display:block;
                text-align:center;
                padding:8px 0 10px;
                color:#fff;
                background-color:#fe3276;
                border-radius:25px;
            }                
            


            </style>
    </head>
    <body>


        <!-- 本日のキュンキュンボタン -->
        <div id="Area_DailyCount">
            <div id="Gauge_Daily" class="epoch gauge-small"></div>
        </div>

        <script>
            var CounterID = "<?php echo $CounterID; ?>";

            //　初期設定
            $(function() 
            {
                var LastTime = 0;   //　クライアントサイドで現在保持している最終時間保存
                var gauge_Daily;    //　今日のキュンキュンのエレメント

                //　カウンタ情報を引数なし（全時間で取得）
                $.get("load.php",{id:CounterID},
                function(data)
                {
                    //　load.phpの応答が帰って来た時のコールバック関数

                    // 応答に含まれる最終時間を保存（次回の問い合わせに利用する）
                    LastTime = data.ServerTime;
                    
                    //　本日ののキュンキュンゲージを初期化
                    gauge_Daily = $('#Gauge_Daily').epoch({
                        type: 'time.gauge',
                        domain: [0, <?php echo $ChartMax; ?>],       //　ゲージマックスを指定
                        format: function(v) { return parseInt(v) + ' キュン'; },　　//　メータテキストのフォーマット
                        value: 0
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
                        //　本日のカウンタ値を設定
                        gauge_Daily.update(data.DayCount);
                   
                    });
                };
            
            });

        </script>

    </body>
</html>
