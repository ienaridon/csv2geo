# csv2geo

### 目次
[このプログラムについて](#mokuji001)  
[ライセンスについて](#mokuji002)  
[動作環境](#mokuji101)  
[想定しているシステム構成](#mokuji201)  
[事前準備](#mokuji301)  
[セットアップ方法](#mokuji401)  
[ログファイルの仕様について](#mokuji501)  

<a id="mokuji001"></a>
## このプログラムについて
CSVで与えられた住所リストを元に緯度経度を付与したjsonファイルを生成します。

<a id="mokuji002"></a>
## ライセンスについて
このプログラムはMITライセンスにて公開しています。

<a id="mokuji101"></a>
## 動作環境
php (5系,7系)

<a id="mokuji201"></a>
## 想定しているシステム構成
![dialog](systemdialog.png)

<a id="mokuji301"></a>
## 事前準備
このプログラムは住所からの緯度経度変換にYahoo! Japan提供の [YOLP API](https://developer.yahoo.co.jp/webapi/map/openlocalplatform/v1/geocoder.html) を利用しています。  
プログラムからこのAPIを使用するためには、事前に [YJDN](https://developer.yahoo.co.jp/) に登録してAPPID(CLIENTID)を取得しておく必要があります。  

<a id="mokuji401"></a>
## セットアップ方法
セットアップから動作確認方法までを説明します。

### 1.PHP動作環境の用意  
何かしらの方法でphpをインストールします。　ディストリビュータ向けのパッケージでもビルドしても構いません。
### 2.バッチプログラムの用意  
このレポジトリに含まれる geoget.php を適当なディレクトリにコピーします。
### 3.定数の編集  
ここれは、プログラム中の定数の変更をします。
#### 3-1 APPIDの編集  
YOLPの使用に必要なAPPIDを、以下の行の hoge の部分に記入します。  
```
define("APPID", "hoge") ;
```
#### 3-2 logfilenameの編集
以下の行のログ出力に使用するパスとファイル名を編集します。  
```
define("LOGFILE"        ,"geoget-log.txt") ; // ログファイル
```
一般的には http からアクセスできない場所に置くべきです。
### 4.動作確認
1. ターミナルを2つ開いておきます。  
2. 1つのターミナルでは、　　
```
prompt% tail -f ログファイル
```
として、ログへの書き込みを確認します。　　
3.バッチプログラムの実行　　
```
prompt% php geoget.php 入力ファイル 出力ファイル
```
で、実行します。  
(1)エラー表示が出ていなくて、(2)ログファイルに書き込まれ、(3)出力ファイルが生成されている、事を確認してください。


<a id="mokuji501"></a>
## ログファイルの仕様について
このプログラムの動作を監視するにはログファイルで確認を行います。  
#### ログ形式
日付 ログレベル プログラム名 ログ内容 が記録されます。  
例:  
```
2020-04-24 23:40:54	Info	geoget.php	Batch Start
2020-04-24 23:40:54	Warning	geoget.php	API Cacheファイルの読み込みに失敗しました。 [geoget-cache.txt]
2020-04-24 23:40:58	Warning	geoget.php	該当する緯度経度が見つかりませんでした。 [仙台市のどこか]
2020-04-24 23:40:58	Warning	geoget.php	CSVファイル中に欠損レコードです。 行番号: 4 [欠損レコード]
2020-04-24 23:41:00	Info	geoget.php	Batch End
2020-04-24 23:41:09	Info	geoget.php	Batch Start
2020-04-24 23:41:09	Info	geoget.php	Batch End
2020-04-26 16:40:04     Info    geoget.php      Batch Start
2020-04-26 16:40:04     Error   geoget.php      CSVファイルの読み込みに失敗しました。 [INPFILE]
```
#### ログレベルについて

|エラーレベル|内容|
|:---|:---|
| Error   | バッチの実行ができない問題が生ています。              |
| Warning | バッチは実行できますが、何かしらの問題が生じています。 |
| Info    | プログラムの実行状況などが記録されます。              |
| Debug   | 開発者用の詳細なログが記録されます。                  |

| 左揃え | 中央揃え | 右揃え |
|:---|:---:|---:|
|1 |2 |3 |
|4 |5 |6 |

