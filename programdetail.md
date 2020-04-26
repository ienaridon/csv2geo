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
| 住所リストファイル   | コマンドライン引数で指定 |  | 
| 結果出力ファイル     | コマンドライン引数で指定 |  | 
| キャッシュファイル   | geoget-cache.txt |  |
| ログファイル        | geoget-log.txt |  |

### 関数概要

### シーケンス

