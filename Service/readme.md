#　WebService版　キュンキュンボタン仕様

## kyunkyunButton.php
キュンキュンボタン
- パラメータ
    - id ：　カウンターID　英数字任意を文字列を指定する。id毎にカウント管理を行う。　（省略＝Default）
    - max ：　今のキュン数のゲージ最大値を指定　（省略＝50）
- 仕様例
> <p>今のキュンキュン数</p>
> <iframe class="center_iframe" src="./kyunkyunButton.php?id=ogjin&max=10"  width="320px" Height="270px" scrolling="no"></iframe>



## timeline.php
リアルタイムチャートを表示
- パラメータ
    - id ：　カウンターID　英数字任意を文字列を指定する。id毎にカウント管理を行う。　（省略＝Default）
    - max ：　チャートの縦軸の最大値を指定　（省略＝50）
    - height ：　チャートの縦のサイズをPxで指定　（省略＝220）
- 仕様例
> <iframe class="center_iframe" src="./timeline.php?id=ogjin&height=220&max=10"  width="80%" Height="230px" scrolling="no"></iframe>


## dailycounter.php
本日のゲージ
- パラメータ
    - id ：　カウンターID　英数字任意を文字列を指定する。id毎にカウント管理を行う。　（省略＝Default）
    - max ：　本日のキュン数のゲージ最大値を指定　（省略＝200）
- 仕様例
> <p>今のキュンキュン</p>
> <iframe class="center_iframe" src="./dailycounter.php?id=ogjin&max=10"  width="80%" Height="270px" scrolling="no"></iframe>

    