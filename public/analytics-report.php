<?php
declare(strict_types=1);
if(realpath($_SERVER['SCRIPT_FILENAME']??'')===__FILE__){http_response_code(404);exit;}
function ugc_events(string $dir,int $cutoff,string $site='all'): Generator {
 foreach(glob($dir.'/events-*.jsonl')?:[] as $file){$day=substr(basename($file),7,10);if(strtotime($day.' UTC')<$cutoff-86400)continue;
  $h=@fopen($file,'rb');if(!$h)continue;
  try{while(($line=fgets($h,10000))!==false){$e=json_decode($line,true);if(is_array($e)&&($e['t']??0)>=$cutoff&&($site==='all'||($e['host']??'')===$site))yield $e;}}finally{fclose($h);}
 }
}
function ugc_report(iterable $events,array $names,string $key,string $selected='',array $media=[]): array {
 $r=['visitors'=>[],'people'=>[],'sessions'=>[],'models'=>[],'sources'=>[],'countryCounts'=>[],'devices'=>[],'pages'=>[],'actions'=>[],'daily'=>[],'views'=>0,'clicks'=>0,'seconds'=>0,'first'=>null,'timeline'=>[],'returning'=>0,'average'=>0];
 $last=[];
 foreach($events as $e){
  $ip=$e['ip']??'';if(!is_string($ip)||!filter_var($ip,FILTER_VALIDATE_IP))continue;
  $v=ugc_visitor_id($ip,(string)($e['device']??''),(string)($e['os']??''),(string)($e['browser']??''),$key);
  $t=(int)($e['t']??0);$type=$e['type']??'';$creator=isset($names[$e['creator']??''])?$e['creator']:'';$n=$type==='heartbeat'?max(0,min(30,(int)($e['seconds']??0))):0;
  $page=ugc_path($e['page']??'/');$source=ugc_safe($e['source']??'Direct');$photo=$e['photo']??($e['target']??'');
  if(!is_string($photo)||!in_array($photo,$media[$creator]['gallery']??[],true))$photo='';
  $r['first']=$r['first']===null?$t:min($r['first'],$t);$r['visitors'][$v]=true;
  if(!isset($r['people'][$v]))$r['people'][$v]=['id'=>$v,'ip'=>$ip,'country'=>$e['country']??'ZZ','device'=>$e['device']??'','os'=>$e['os']??'','browser'=>$e['browser']??'','first'=>$t,'last'=>$t,'sessions'=>[],'pages'=>0,'clicks'=>0,'seconds'=>0,'models'=>[],'photos'=>[],'sources'=>[],'hosts'=>[]];
  $person=&$r['people'][$v];$person['first']=min($person['first'],$t);$person['last']=max($person['last'],$t);$person['sources'][$source]=true;$person['hosts'][$e['host']??'']=true;
  if(!isset($last[$v])||$t-$last[$v]['time']>1800){
   $sid=substr(hash('sha256',$v.'|'.$t.'|'.($e['pageId']??'')),0,24);$last[$v]=['time'=>$t,'sid'=>$sid];
   $r['sessions'][$sid]=['first'=>$t,'last'=>$t,'visitor'=>$v,'ip'=>$ip,'country'=>$e['country']??'ZZ','device'=>$e['device']??'','os'=>$e['os']??'','browser'=>$e['browser']??'','host'=>$e['host']??'','source'=>$source,'entry'=>ugc_path($e['entry']??$page),'utm'=>$e['utm']??[],'returning'=>count($person['sessions'])>0,'seconds'=>0,'pages'=>[],'creators'=>[],'clicks'=>[]];
   $person['sessions'][$sid]=true;$r['sources'][$source]=($r['sources'][$source]??0)+1;$country=$e['country']??'ZZ';$r['countryCounts'][$country]=($r['countryCounts'][$country]??0)+1;$device=$e['device']??'';$r['devices'][$device]=($r['devices'][$device]??0)+1;
  }
  $sid=$last[$v]['sid'];$last[$v]['time']=max($last[$v]['time'],$t);$session=&$r['sessions'][$sid];$session['last']=max($session['last'],$t);
  if($type==='page_view'){$r['views']++;$person['pages']++;$day=gmdate('Y-m-d',$t);$r['daily'][$day]=($r['daily'][$day]??0)+1;$r['pages'][$page]=($r['pages'][$page]??0)+1;$session['pages'][]=$page;}
  if($creator!==''){
   if(!isset($r['models'][$creator]))$r['models'][$creator]=['views'=>0,'clicks'=>0,'seconds'=>0,'visitors'=>[]];
   if(!isset($person['models'][$creator]))$person['models'][$creator]=['views'=>0,'clicks'=>0,'seconds'=>0,'photos'=>[]];
   $model=&$r['models'][$creator];$ownModel=&$person['models'][$creator];$model['visitors'][$v]=true;$session['creators'][$creator]=true;
   if($type==='creator_view'||$type==='page_view'){$model['views']++;$ownModel['views']++;}
   if($type==='click'){$model['clicks']++;$ownModel['clicks']++;}
   $model['seconds']+=$n;$ownModel['seconds']+=$n;
  }
  if($photo!==''){
   if(!isset($person['photos'][$photo]))$person['photos'][$photo]=['creator'=>$creator,'views'=>0,'clicks'=>0,'seconds'=>0];
   $ownPhoto=&$person['photos'][$photo];if($type==='photo_view')$ownPhoto['views']++;if($type==='click')$ownPhoto['clicks']++;$ownPhoto['seconds']+=$n;$person['models'][$creator]['photos'][$photo]=true;
  }
  $r['seconds']+=$n;$person['seconds']+=$n;$session['seconds']+=$n;
  if($type==='click'){$r['clicks']++;$person['clicks']++;$label=ugc_safe($e['action']??'Click',80);$r['actions'][$label]=($r['actions'][$label]??0)+1;$session['clicks'][]=['time'=>$t,'action'=>$label,'creator'=>$names[$creator]??'','page'=>$page,'photo'=>$photo];}
  if($selected===$v&&$type!=='heartbeat')$r['timeline'][]=['time'=>$t,'type'=>$type,'action'=>ugc_safe($e['action']??'',80),'creator'=>$creator,'photo'=>$photo,'page'=>$page,'session'=>$sid,'host'=>$e['host']??'','source'=>$source];
  unset($person,$session,$model,$ownModel,$ownPhoto);
 }
 foreach(['sources','countryCounts','devices','pages','actions'] as $field)arsort($r[$field]);ksort($r['daily']);
 uasort($r['models'],fn($a,$b)=>$b['views']<=>$a['views']);uasort($r['sessions'],fn($a,$b)=>$b['last']<=>$a['last']);uasort($r['people'],fn($a,$b)=>$b['last']<=>$a['last']);
 $r['returning']=count(array_filter($r['sessions'],fn($s)=>$s['returning']));$r['average']=count($r['sessions'])?(int)round($r['seconds']/count($r['sessions'])):0;
 usort($r['timeline'],fn($a,$b)=>$b['time']<=>$a['time']);return $r;
}
