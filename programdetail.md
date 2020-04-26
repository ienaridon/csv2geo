# geoget.php の詳細仕様

### 外部API
住所から緯度経度を取得するにあたって、以下のAPIを使用しています。
https://developer.yahoo.co.jp/webapi/map/openlocalplatform/v1/geocoder.html  

### PHPのバージョンと外部ライブラリ
```
$ php -v
PHP 7.0.33-0ubuntu0.16.04.5 (cli) ( NTS )
Copyright (c) 1997-2017 The PHP Group
Zend Engine v3.0.0, Copyright (c) 1998-2017 Zend Technologies
    with Zend OPcache v7.0.33-0ubuntu0.16.04.5, Copyright (c) 1999-2017, by Zend Technologies
```
開発はPHP7で行いましたが、最近実装された機能は使っていないのでPHP5でも動作すると思います。  
また、標準でインストールされる意外のライブラリは使用していません。  

### 関連ファイル
| 名称 | デフォルト名 | 機能　| 
|:----|:----|:----|
| 住所リストファイル   | コマンドライン引数で指定 | CSV形式の住所一覧 | 
| 結果出力ファイル     | コマンドライン引数で指定 | geojson形式の住所一覧 | 
| キャッシュファイル   | geoget-cache.txt | 以前APIで取得した緯度経度、CSV形式 |
| ログファイル        | geoget-log.txt | プログラム実行時に吐かれるログ、TXT形式 |

### 関数概要

#### メイン関数

| 引数 | 機能　| 
|:----|:----|
| argv[1] | 入力ファイル名  |
| argv[2] | 出力ファイル名  |

##### この関数の機能
プログラム全体のメイン処理となります。

#### function getCoordinates($addressString)
住所から緯度経度を得る
引数:
	$addressString
戻値:
	緯度,経度文字列


// -------------------------------------------
// APIキャッシュファイルの読み込み
// -------------------------------------------
function getAPICache()
APIキャッシュファイルの読み込み
引数:
戻値:

function putAPICache($key, $value)
APIキャッシュファイルへの追加
引数:
戻値:

// -------------------------------------------
// ログの書き出し
// -------------------------------------------
function logPrint($logMessage, $logLevel)
ログの書き出し
引数:
$logMessage, $logLevel
戻値:





### シーケンス

