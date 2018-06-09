<?php
include('twitterkey.php');
$url="https://api.kucoin.com/v1/open/tick";
$json=file_get_contents($url);
$json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$hoge=array();
$arr = json_decode($json,true);
$i=1;
foreach($arr as $keynum => $value){
  foreach($value as $keynum2 => $value2){    
    $valueX=mb_convert_encoding($value2['coinTypePair'],'SJIS-win','utf-8');
    if ($valueX == 'BTC'){
      $valueS = (preg_replace('/-[A-Z].*/', '', $value2['symbol']));
      $value3=mb_convert_encoding($value2['lastDealPrice'],'SJIS-win','utf-8');
      $value3=sprintf("%.8f", $value3);
      $value3 = (preg_match('/\./', $value3)) ? preg_replace('/\.?0+$/', '', $value3) : $value3;
      $value4=mb_convert_encoding($value2['changeRate'],'SJIS-win','utf-8');
      $value4=sprintf("%.8f", $value4*100);
      $value4 = (preg_match('/\./', $value4)) ? preg_replace('/\.?0+$/', '', $value4) : $value4;
      $hoge[$i]['symbol']=$valueS;
      $hoge[$i]['lastDealPrice']=$value3;
      $hoge[$i]['changeRate']=$value4;      
      $i++;
    }
  }
}
foreach ($hoge as $key => $value) {
  $id[$key] = $value['changeRate'];
}
array_multisort($id, SORT_DESC, $hoge);
if($hoge[1]['changeRate']>0){
  $chgRate="+".$hoge[0]['changeRate'];
}else{
  $chgRate=$hoge[0]['changeRate'];  
}

$msg="【KuCoin騰落率速報】\r現在のKuCoin価格上昇率トップの通貨は $";
$msg=$msg.$hoge[0]['symbol'];
$msg=$msg." です。\r上昇率は ";
$msg=$msg.$chgRate;
$msg=$msg."% です。\r\r";
$msg=$msg."#仮想通貨 #アルトコイン #KuCoin  #草コイン #話題 #騰落率 #24h騰落率 \r\r";
//OAuthScriptRead
require_once('twitteroauth-master/autoload.php');
use Abraham\TwitterOAuth\TwitterOAuth;
$connection = new TwitterOAuth($consumer_key,$consumer_secret,$access_token,$access_token_secret); 
$res = $connection->post("statuses/update", array("status" => $msg));
echo $msg;
var_dump($res);
?>
