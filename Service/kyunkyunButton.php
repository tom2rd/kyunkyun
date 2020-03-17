<?php
$CounterID = "Default";
if(isset($_GET['id']))
{
    $CounterID = $_GET['id'];
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
        <!--スマホでダブルクリックした時に拡大しないようにするためのメタ情報-->
        <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    
        
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

            /*キュンキュンボタン*/
            #QnnQnnbutton {
                width: 320px;       /*幅*/
                Height: 270px;      /*高さ*/
                margin: 0 auto;     /*中央寄せ*/

                position: relative;
                color: #fe3276;

                text-decoration: none;
                text-align: center;
                vertical-align: middle;
                -webkit-transition: .3s ease-in-out;
                transition: .3s ease-in-out;
            }

            /*キュンキュン動きを定義*/
            #QnnQnnbutton:active {
                -webkit-animation: bounce 2s ease-in-out;
                animation: bounce 2s ease-in-out;
            }
            @-webkit-keyframes bounce {
                5%  { -webkit-transform: scale(1.1, .8); }
                10% { -webkit-transform: scale(.8, 1.1) translateY(-5px); }
                15% { -webkit-transform: scale(1, 1); }
            }
            @keyframes bounce {
                5%  { transform: scale(1.1, .8); }
                10% { transform: scale(.8, 1.1) translateY(-5px); }
                15% { transform: scale(1, 1); }
            }

            /*現在のキュンキュンゲージ*/
            #Gauge_Now {
                position: absolute;
                top: 50%;
                left: 50%;
                -webkit-transform : translate(-50%,-50%);
                transform : translate(-50%,-50%);
                /*display: inline-block;
                vertical-align: middle;*/
            }

            /*今のキュンキュンメーターの見た目変更*/
            #Gauge_Now .epoch .gauge .arc.outer {
                stroke-width: 8px;
                stroke:#fe3276;
            }

            /*今のキュンキュンメーターの見た目変更*/
            #Gauge_Now .epoch .gauge .arc.inner {
                stroke-width: 2px;
                stroke: #fe3276;
            }

            /*今のキュンキュンメーターの見た目変更*/
            #Gauge_Now .epoch .gauge .tick {
                stroke-width: 3px;
                stroke: #fe3276;
            }
/*
            #Gauge_Now .epoch .gauge .needle {
                fill: #fe3276;
            }

            #Gauge_Now .epoch .gauge .needle-base {
                fill:#fe3276;
            }
*/

            /*キュンキュンボタンの画像*/
            #kyunimg {
                position: absolute;
                zoom: 1;
                /*中央に来るように*/
                top: 50%;
                left: 50%;
                -webkit-transform : translateY(-50%);
                transform : translate(-50%,-50%);
            }
            
            </style>
    </head>
    <body>

        <!-- キュンキュンボタンの音声 -->
        <div id="sound-file"　class="audio">
            <audio src="audio/hee.mp3" id="heeAudio"><p>audio 要素に対応したブラウザでは「へぇ」の音声ファイルの再生が可能です。</p></audio>
        </div>
 
        <!-- キュンキュンボタン -->
        <div id="QnnQnnbutton" onClick="kyunkyunCountUP()" >
            <img id="kyunimg" src="img/kyunBtn.PNG"/>
            <div id="Gauge_Now" class="epoch gauge-small" ></div>
        </div>
        <!-- キュン君-->
 
        
        <script>

            var CounterID = "<?php echo $CounterID; ?>";

            //　初期設定
            $(function() 
            {
                var LastTime = 0;   //　クライアントサイドで現在保持している最終時間保存
                var gauge_now;          //　今のキュンキュンゲージのエレメント

                //　カウンタ情報を引数なし（全時間で取得）
                $.get("load.php",{id:CounterID},
                function(data)
                {
                    //　load.phpの応答が帰って来た時のコールバック関数

                    // 応答に含まれる最終時間を保存（次回の問い合わせに利用する）
                    LastTime = data.ServerTime;

                    //　現在のキュンキュンゲージを初期化
                    gauge_now = $('#Gauge_Now').epoch({
                        type: 'time.gauge',
                        domain: [0, <?php echo $ChartMax; ?>],        //　ゲージmaxを指定
                        format: function(v) { return parseInt(v) + ' キュン'; },    //　メータテキストのフォーマット
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
                        //　現在のカウンタ値を設定
                        gauge_now.update(data.NowCount);
                    });
                };

            
            });

            //　キュンきゅんを再生してCountUPのWEBサービスをコール
            function kyunkyunCountUP()
            {
                // [ID:heeAudio]の音声ファイルを再生[play()]する
                var audioElm = document.getElementById( 'heeAudio' );

                // 初回以外だったら音声ファイルを巻き戻す
                if( typeof( audioElm.currentTime ) != 'undefined' )
                {
                    audioElm.currentTime = 0;
                }
                
                audioElm.play();
                // カウンタ更新
                $.get('countup.php',{id:CounterID},{});
            }

        </script>

    </body>
</html>
