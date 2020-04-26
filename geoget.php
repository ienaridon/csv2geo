<?php
// -------------------------------------------
//
// CSV から geojson に変換
// __FILE__ inputfile loglevel
// -------------------------------------------
date_default_timezone_set('Asia/Tokyo');

// -------------------------------------------
// 定数 使用ファイル
define("CACHEFILE"      ,"geoget-cache.txt" ) ; // APIの結果をキャッシュするファイル

// 定数 ログ出力系
define("LOGFILE"        ,"geoget-log.txt") ; // ログファイル
define("NOLOG"          , 0) ;
define("LOGLEVELERROR"  , 1) ;
define("LOGLEVELWARNING", 2) ;
define("LOGLEVELINFO"   , 3) ;
define("LOGLEVELDEBUG"  , 4) ;
global $displayLogLevel ; 
$displayLogLevel = LOGLEVELINFO ;

// -------------------------------------------
// YOLP API を使う準備
// Manual: https://developer.yahoo.co.jp/webapi/map/openlocalplatform/v1/geocoder.html
define("API"  , "https://map.yahooapis.jp/geocode/V1/geoCoder") ;
define("APPID", "hoge") ;
define("RESULTUNKNOWN","(Unknown)") ; // 位置情報が取れなかった時に使用

// -------------------------------------------
// 処理開始
logPrint("Batch Start",LOGLEVELINFO) ;

// コマンドライン引数の処理
if (count($argv) < 3) {
  logPrint("引数エラー Usage: $argv[0] 入力ファイル名 出力ファイル名",LOGLEVELERROR) ;
  exit(-1) ;
}
if ($argv[1] == $argv[2]) {
  logPrint("入力ファイル名と出力ファイル名は別でなければなりません。",LOGLEVELERROR) ;
  exit(-1) ;
}

$inpFilename = $argv[1] ; // 入力ファイル名
$outFilename = $argv[2] ; // 出力ファイル名

// APIをCacheする
global $apiCache ;
$apiCache = array() ;
getAPICache() ;

// CSVファイルの読み込み
$inpfile = fopen($inpFilename, "r");
if ($inpfile == null) {
  logPrint("CSVファイルの読み込みに失敗しました。 [". $inpFilename ."]",LOGLEVELERROR) ;
  exit(-1) ;
}

$outArray = Array() ; // 出力用データ
// -------------------------------------------
// 1行単位の処理
$filepos = 1 ; // 処理中の行位置
while($line = fgetcsv($inpfile)){
  // var_dump($line) ;
  if (count($line) < 2) {
    logPrint("CSVファイル中に欠損レコードです。 行番号: $filepos [$line[0]]",LOGLEVELWARNING) ;
  }
  else {
    $Coordinates = getCoordinates($line[1]) ;
    logPrint("sorce : ".$line[1]     ,LOGLEVELDEBUG) ;
    logPrint("result: ".$Coordinates ,LOGLEVELDEBUG) ;
    if ($Coordinates != RESULTUNKNOWN) {
      array_push($outArray, [
        'Address'=>$line[1], 
        'Coordinates'=> $Coordinates]
      );
    }
  }
  $filepos++ ;
}
fclose($inpfile);

// -------------------------------------------
// 結果出力

// jsonの生成
$jsonOutarray = json_encode($outArray, JSON_UNESCAPED_UNICODE) ;
// 結果をファイルに書き込み
$outfile = fopen($outFilename, "w");
if ($outfile == null) {
  logPrint("結果ファイルの作成に失敗しました。 [".$outfile."]",LOGLEVELERROR) ;
  exit(-1) ;
}
$fwrite = fwrite($outfile, $jsonOutarray) ;
if ($fwrite === false) {
  logPrint("結果の書きこみに失敗しました。 [".$outfile."]",LOGLEVELERROR) ;
  exit(-1) ;
}
fclose($outfile);

// 他終了処理
logPrint($jsonOutarray ,LOGLEVELDEBUG) ;
logPrint("Batch End",LOGLEVELINFO) ;
logPrint("--------------" ,LOGLEVELDEBUG) ;
exit(0) ;

//
//
// main  ここまで
//
//

// -------------------------------------------
// 住所から緯度経度を得る
// -------------------------------------------
function getCoordinates($addressString)
{
//  logPrint("getCoordinates",LOGLEVELDEBUG) ;
//  logPrint($addressString  ,LOGLEVELDEBUG) ;
  $answer = RESULTUNKNOWN ;
  global $apiCache ;

  // キャッシュされているか確認
  if (isset($apiCache[$addressString])) {
    // logPrint("cache hit!",LOGLEVELDEBUG) ;
    return $apiCache[$addressString]  ;
  }
  // logPrint("no cache hit",LOGLEVELDEBUG) ;
  
  // -------------------------------------------
  // YOLP API のパラーメータを設定
  $params = array(
    'query'  => $addressString ,
    'output' => 'json'
  );
  // -------------------------------------------
  // curlを使ってAPI実行  
  $ch = curl_init(API.'?'.http_build_query($params));
  curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERAGENT      => "Yahoo AppID: ".APPID
  ));
  $result = curl_exec($ch);
  // curlの実行に失敗
  if(curl_errno($ch)) {
    logPrint('Curl error: ' . curl_error($ch),LOGLEVELERROR) ;
    return $answer ;
  }
  curl_close($ch);
  // echo $result ;
  $resultArray = json_decode( $result , true ) ; // 取得したjsonを配列に。
  // var_dump($jsonresult) ;

  // -------------------------------------------
  // 緯度経度変換ができなかったケース  
  
  // APIは実行できたが正しい戻り値が返ってきていない
  if (!isset($resultArray["ResultInfo"]["Status"]))  {
    logPrint("API get failed. maybe Paramater.",LOGLEVELERROR) ;
    return $answer ;
  }
  // 何か未知のステータスで失敗している
  $resultStatus = $resultArray["ResultInfo"]["Status"] ;
  if ($resultStatus != "200")  {
    logPrint("API status error. Status[".$resultStatus."]",LOGLEVELWORNING) ;
    return $answer ;
  }
  // APIは実行できたが、経度緯度変換はできていない
  if (!$resultArray["ResultInfo"]["Count"]) {
    logPrint("該当する緯度経度が見つかりませんでした。 [$addressString]",LOGLEVELWARNING) ;
    putAPICache($addressString, $answer) ; // APIが正常動作で見つからなかった場合はキャッシュする。
    return $answer ;
  }
  
  // -------------------------------------------
  // 正しく取得できた
  $answer = $resultArray["Feature"][0]["Geometry"]["Coordinates"] ;
  putAPICache($addressString, $answer) ;
  // logPrint("Status  : ".$resultStatus,LOGLEVELDEBUG) ;
  // logPrint("Count   : ".$resultArray["ResultInfo"]["Count"],LOGLEVELDEBUG) ;
  return $answer ;
}

// -------------------------------------------
// APIキャッシュファイルの読み込み
// -------------------------------------------
function getAPICache()
{
  global $apiCache ;
  
  $inpfile = fopen(CACHEFILE, "r");
  if ($inpfile == null) {
    // cacheが無いなら仕方ないので次に行く、エラー終了するほどではない。
    logPrint("API Cacheファイルの読み込みに失敗しました。 [".CACHEFILE."]",LOGLEVELWARNING) ;
    return ;
  }

  $filepos = 1 ; // 処理中の行位置
  while($line = fgetcsv($inpfile)){
    if (count($line) < 2) {
      logPrint("API Cacheファイル中に欠損レコードです。 行番号: $filepos [$line[0]]",LOGLEVELWARNING) ;
    }
    else {
      $apiCache[$line[0]] = $line[1] ;
    }
    $filepos++ ;
  }
  fclose($inpfile);
  logPrint("API Cache read OK.",LOGLEVELDEBUG) ;
  
  return ;
}

// -------------------------------------------
// APIキャッシュファイルへの追加
// -------------------------------------------
function putAPICache($key, $value)
{
  $putline  = '"'. $key .'","' . $value. '"' ;
  $putline.="\n" ;
  error_log($putline, 3, CACHEFILE) ;
}

// -------------------------------------------
// ログの書き出し
// -------------------------------------------
function logPrint($logMessage, $logLevel)
{
  global $displayLogLevel ;
  $logString = Array("","Error", "Warning", "Info", "Debug") ;
  if ($logLevel > $displayLogLevel) return ;
  
  $logMessage.="\n" ;
	echo $logMessage ;
  $logHeader = date("Y-m-d H:i:s")."\t".$logString[$logLevel]."\t".basename(__FILE__) ;
  error_log($logHeader."\t".$logMessage, 3, LOGFILE) ;
}

?>
