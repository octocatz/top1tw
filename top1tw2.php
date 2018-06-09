<?php
include('twitterkey.php');
$url="https://api.binance.com/api/v1/ticker/allPrices";
$json=file_get_contents($url);
$json=mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$hoge=array();
$arr=json_decode($json,true);
$j=1;
foreach($arr as $keynum => $value){
  $valueA=mb_convert_encoding($value['symbol'],'SJIS-win','utf-8');
  $valueB=mb_convert_encoding($value['price'],'SJIS-win','utf-8');
  $valueB=sprintf("%.8f", $valueB);
  $valueB=(preg_match('/\./', $valueB)) ? preg_replace('/\.?0+$/', '', $valueB) : $valueB;
  $pairs[$j]['symbol']=$valueA;
  $j++;
}
$i=1;
$hoge=array();
foreach($pairs as $keynum => $value){
  $pairList=$value['symbol'];
  $base=substr($pairList, -3);
  if($base=='BTC'){
    $url="https://api.binance.com/api/v1/ticker/24hr?symbol=".$pairList;
    $json=file_get_contents($url);
    $json=mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
    $arr = json_decode($json,true);
    $value3=mb_convert_encoding($arr['lastPrice'],'SJIS-win','utf-8');
    $value3=sprintf("%.8f", $value3);
    $value3=(preg_match('/\./', $value3)) ? preg_replace('/\.?0+$/', '', $value3) : $value3;
    $value4=mb_convert_encoding($arr['priceChangePercent'],'SJIS-win','utf-8');
    $value4=sprintf("%.8f", $value4);
    $value4=(preg_match('/\./', $value4)) ? preg_replace('/\.?0+$/', '', $value4) : $value4;
    $hoge[$i]['lastDealPrice']=$value3;
    $hoge[$i]['priceChangePercent']=$value4;
    $hoge[$i]['symbol']=substr($pairList,0,strlen($pairList)-3);
    $i++;
  }
}
foreach ($hoge as $key => $value) {
  $id[$key] = $value['priceChangePercent'];
}
array_multisort($id, SORT_DESC, $hoge);
if($hoge[1]['priceChangePercent']>0){
  $chgRate="+".$hoge[0]['priceChangePercent'];
}else{
  $chgRate=$hoge[0]['priceChangePercent'];  
}
$msg="【binance騰落率速報】\r現在のbinance価格上昇率トップの通貨は $";
$msg=$msg.$hoge[0]['symbol'];
$msg=$msg." です。\r上昇率は ";
$msg=$msg.$chgRate;
$msg=$msg."% です。\r\r";
$msg=$msg."#仮想通貨 #アルトコイン #binance  #草コイン #話題 #騰落率 #24h騰落率 \r\r";
require_once('twitteroauth-master/autoload.php');
use Abraham\TwitterOAuth\TwitterOAuth;
$connection = new TwitterOAuth($consumer_key,$consumer_secret,$access_token,$access_token_secret); $res = $connection->post("statuses/update", array("status" => $msg));
echo $msg;
var_dump($res);
?>
