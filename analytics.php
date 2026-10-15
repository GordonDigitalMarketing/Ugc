<?php
declare(strict_types=1);
ini_set('display_errors','0');
require __DIR__.'/analytics-lib.php';
header('Content-Type: application/json; charset=utf-8');header('Cache-Control: no-store');header('X-Content-Type-Options: nosniff');header('X-Robots-Tag: noindex, nofollow');
$origin=$_SERVER['HTTP_ORIGIN']??'';
$origins=['https://ugc.ba','https://www.ugc.ba','http://ugc.ba','https://ugc.gordon.ba','http://ugc.gordon.ba','http://127.0.0.1:3000','http://localhost:3000'];
if(!in_array($origin,$origins,true))ugc_json(403,['ok'=>false]);
header('Access-Control-Allow-Origin: '.$origin);header('Vary: Origin');
if($_SERVER['REQUEST_METHOD']==='OPTIONS'){header('Access-Control-Allow-Methods: POST');header('Access-Control-Allow-Headers: Content-Type');ugc_json(204);}
if($_SERVER['REQUEST_METHOD']!=='POST')ugc_json(405);
if(($_SERVER['HTTP_DNT']??'')==='1'||($_SERVER['HTTP_SEC_GPC']??'')==='1')ugc_json(204);
if((int)($_SERVER['CONTENT_LENGTH']??0)>5000)ugc_json(413);
$raw=file_get_contents('php://input',false,null,0,5001);if($raw===false||strlen($raw)>5000)ugc_json(413);
$d=json_decode($raw,true);if(!is_array($d))ugc_json(400);
$type=ugc_safe($d['type']??'',20);if(!in_array($type,['page_view','creator_view','click','heartbeat'],true))ugc_json(422);
$page=ugc_path($d['page']??'/');if(strpos($page,'.php')!==false||strpos($page,'/images/')===0)ugc_json(204);
$pageId=ugc_safe($d['pageId']??'',50);$eventId=ugc_safe($d['eventId']??'',50);if(!preg_match('/^[a-f0-9-]{16,50}$/',$pageId)||!preg_match('/^[a-f0-9-]{16,50}$/',$eventId))ugc_json(422);
$ua=substr($_SERVER['HTTP_USER_AGENT']??'',0,500);if(preg_match('/bot\b|crawl|spider|headless|lighthouse|monitoring/i',$ua))ugc_json(204);
$ip=$_SERVER['REMOTE_ADDR']??'';if(!filter_var($ip,FILTER_VALIDATE_IP))ugc_json(422);
$host=strtolower((string)parse_url($origin,PHP_URL_HOST));$host=$host==='www.ugc.ba'?'ugc.ba':$host;if($host==='localhost'||$host==='127.0.0.1')ugc_json(204);
$creator=ugc_safe($d['creator']??'',30);$names=ugc_names();if(!isset($names[$creator]))$creator='';
$source='Direct';$ref=ugc_safe($d['referrer']??'',250);$refHost=strtolower((string)parse_url($ref,PHP_URL_HOST));if($refHost!==''&&!ugc_internal($refHost))$source=$refHost;
$utm=[];foreach(['source','medium','campaign'] as $key){$v=ugc_safe($d['utm'][$key]??'',80);$utm[$key]=preg_replace('/[^\pL\pN _\.\-]/u','',$v);}
if($utm['source']!=='')$source=$utm['source'];
try{
 $dir=ugc_store();$now=time();$visitor=hash_hmac('sha256',gmdate('Y-m').'|'.$ip.'|'.$ua,ugc_key());
 $h=@fopen($dir.'/visitor-'.$visitor.'.json','c+');if(!$h||!flock($h,LOCK_EX))ugc_json(503);
 $state=json_decode(stream_get_contents($h),true);if(!is_array($state))$state=[];
 $rate=$state['rate']??[];$rate=array_values(array_filter($rate,fn($t)=>is_int($t)&&$t>$now-60));
 if(count($rate)>=60){flock($h,LOCK_UN);fclose($h);ugc_json(429);}$rate[]=$now;
 $seen=$state['seen']??[];if(in_array($eventId,$seen,true)){flock($h,LOCK_UN);fclose($h);ugc_json(204);}
 if(($state['last']??0)<$now-1800){$state['session']=bin2hex(random_bytes(12));$state['visits']=($state['visits']??0)+1;$state['entry']=$page;$state['source']=$source;$state['utm']=$utm;$state['country']=ugc_country($ip);}
 [$device,$os,$browser]=ugc_device($ua);
 $event=['t'=>$now,'type'=>$type,'visitor'=>substr($visitor,0,24),'session'=>$state['session'],'returning'=>($state['visits']??1)>1,'host'=>$host,'ip'=>$ip,'country'=>$state['country']??'ZZ','device'=>$device,'os'=>$os,'browser'=>$browser,'page'=>$page,'pageId'=>$pageId,'creator'=>$creator,'source'=>$state['source']??$source,'entry'=>$state['entry']??$page,'utm'=>$state['utm']??$utm];
 if($type==='heartbeat'){$seconds=$d['seconds']??0;if(!is_numeric($seconds)||$seconds<1||$seconds>30){flock($h,LOCK_UN);fclose($h);ugc_json(422);}$event['seconds']=(int)$seconds;}
 if($type==='click'){$event['action']=ugc_safe($d['action']??'Click',80);$event['target']=ugc_path($d['target']??'/');}
 $file=$dir.'/events-'.gmdate('Y-m-d').'.jsonl';if(is_file($file)&&filesize($file)>30000000){flock($h,LOCK_UN);fclose($h);ugc_json(503);}
 $written=file_put_contents($file,json_encode($event,JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE)."\n",FILE_APPEND|LOCK_EX);@chmod($file,0600);
 if($written===false){flock($h,LOCK_UN);fclose($h);ugc_json(503);}
 $state['last']=$now;$state['rate']=$rate;$state['seen']=array_slice([...$seen,$eventId],-100);
 rewind($h);ftruncate($h,0);fwrite($h,json_encode($state));fflush($h);flock($h,LOCK_UN);fclose($h);@chmod($dir.'/visitor-'.$visitor.'.json',0600);
 if(random_int(1,100)===1){foreach(glob($dir.'/*')?:[] as $old){if(is_file($old)&&basename($old)!=='secret'&&filemtime($old)<$now-30*86400)@unlink($old);}}
 ugc_json(204);
}catch(Throwable $error){ugc_json(503,['ok'=>false]);}
