# csv2geo

### 目次
[このプログラムについて](#mokuji001)  
[動作環境](#mokuji101)  
[想定しているシステム構成](#mokuji201)  

<a id="mokuji001"></a>
## このプログラムについて
CSVで与えられた住所一覧に、緯度経度を付与したjsonファイルを生成します。

## ライセンスについて
このプログラムはMITライセンスにて公開しています。

<a id="mokuji101"></a>
## 動作環境
php (5系,7系)

<a id="mokuji201"></a>
## 想定しているシステム構成
![dialog](systemdialog.png)

## 事前準備
このプログラムは住所からの緯度経度変換にYahoo! Japan提供の [YOLP API](https://developer.yahoo.co.jp/webapi/map/openlocalplatform/v1/geocoder.html) を利用しています。  
プログラムからこのAPIを使用するためには、事前に [YJDN](https://developer.yahoo.co.jp/) に登録してAPPID(CLIENTID)を取得しておく必要があります。  

## セットアップ方法
1.PHP動作環境の用意  
2.バッチプログラムの用意  
3.定数の編集  
3-1 APPIDの編集  
3-2 logfilenameの編集
4.動作確認

## ログファイルの仕様について
このプログラムの動作を監視するにはログファイルで確認を行います。  

ログ形式
日付 ログレベル ログ内容
例:
ログレベル
Error  
Warning  
Info  
Debug  



