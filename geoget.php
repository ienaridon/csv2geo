<?php
//
//
// CSV to geojson
//
// Manual: https://developer.yahoo.co.jp/webapi/map/openlocalplatform/v1/geocoder.html
date_default_timezone_set('Asia/Tokyo');

// -------------------------------------------
// 定数
define("INPFILE"      , "sample.csv") ;

// ログ出力系の定数
define("LOGFILE"      , "log.txt") ;
define("LOGLEVELDEBUG", "Debug") ;
define("LOGLEVELERROR", "Error") ;

// -------------------------------------------
// Yahoo! Japan のAPIを使う準備
define("API"  , "https://map.yahooapis.jp/geocode/V1/geoCoder") ;
define("APPID", "hoge") ;

// -------------------------------------------
// 処理開始
logPrint("Batch Start",LOGLEVELDEBUG) ;

$inpfile = fopen(INPFILE, "r");
while($line = fgetcsv($inpfile)){
  // var_dump($line) ;
  echo "--------------\n" ;
  echo $line[1] . "\n" ;
  echo getCoordinates($line[1])."\n" ;
}
fclose($inpfile);

/*
$answerArray = Array() ;
$answerArray["Stasut"] = $resultArray["ResultInfo"]["Status"] ;
$answerArray["Count"]  = $resultArray["ResultInfo"]["Count"] ;
$jsonAnswer = json_encode($answerArray) ;
echo $jsonAnswer ;
*/

logPrint("Batch End",LOGLEVELDEBUG) ;
exit(0) ;

//
//
// main  ここまで
//
//

// 住所から緯度経度を得る
function getCoordinates($addressString)
{
//  logPrint("getCoordinates",LOGLEVELDEBUG) ;
//  logPrint($addressString  ,LOGLEVELDEBUG) ;

  $params = array(
    'query'  => $addressString ,
    'output' => 'json'
  );
  
 $ch = curl_init(API.'?'.http_build_query($params));
  curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERAGENT      => "Yahoo AppID: ".APPID
  ));

  $result = curl_exec($ch);
  curl_close($ch);
  // echo $result ;
  $resultArray = json_decode( $result , true ) ;
  // var_dump($jsonresult) ;
  echo "Status  : ".$resultArray["ResultInfo"]["Status"]."\n" ;
  echo "Count   : ".$resultArray["ResultInfo"]["Count"]."\n" ;
  echo "Position: ".$resultArray["Feature"][0]["Geometry"]["Coordinates"]."\n";

  return $resultArray["Feature"][0]["Geometry"]["Coordinates"] ;
}


// ログの書き出し
function logPrint($logMessage, $logLevel)
{
  $logMessage.="\n" ;
	echo $logMessage ;
  $logHeader = date("Y-m-d H:i:s")."\t".$logLevel."\t".basename(__FILE__) ;
  error_log($logHeader."\t".$logMessage, 3, LOGFILE) ;
}

?>
