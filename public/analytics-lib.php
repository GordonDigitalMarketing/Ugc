<?php
declare(strict_types=1);
if (realpath($_SERVER['SCRIPT_FILENAME']??'') === __FILE__) { http_response_code(404); exit; }
function ugc_store(): string {
 $dir=dirname(__DIR__).'/.ugc-analytics';
 if(!is_dir($dir)&&!@mkdir($dir,0700,true)&&!is_dir($dir))throw new RuntimeException('Storage unavailable');
 return $dir;
}
function ugc_key(): string {
 $file=ugc_store().'/secret';$h=@fopen($file,'c+');if(!$h||!flock($h,LOCK_EX))throw new RuntimeException('Storage unavailable');
 $key=stream_get_contents($h);if(strlen($key)<64){$key=bin2hex(random_bytes(32));rewind($h);ftruncate($h,0);fwrite($h,$key);fflush($h);@chmod($file,0600);}flock($h,LOCK_UN);fclose($h);return $key;
}
function ugc_country(string $ip): string {
 $packed=@inet_pton($ip);if($packed===false)return 'ZZ';
 if(strlen($packed)===16&&substr($packed,0,12)===str_repeat("\0",10)."\xff\xff")$packed=substr($packed,12);
 $n=strlen($packed);$size=2*$n+2;$h=@fopen(__DIR__.'/analytics-geo/country-v'.($n===4?'4':'6').'.bin','rb');if(!$h)return 'ZZ';
 $low=0;$high=intdiv((int)fstat($h)['size'],$size)-1;$answer='ZZ';
 while($low<=$high){$mid=intdiv($low+$high,2);fseek($h,$mid*$size);$row=fread($h,$size);if(strlen($row)!==$size)break;
  if(strcmp($packed,substr($row,0,$n))<0)$high=$mid-1;elseif(strcmp($packed,substr($row,$n,$n))>0)$low=$mid+1;else{$answer=substr($row,2*$n,2);break;}}
 fclose($h);return $answer;
}
function ugc_device(string $ua): array {
 $device=preg_match('/ipad|tablet/i',$ua)?'Tablet':(preg_match('/mobi|android|iphone/i',$ua)?'Mobitel':'Računar');
 $os=preg_match('/iphone|ipad/i',$ua)?'iOS':(stripos($ua,'Android')!==false?'Android':(stripos($ua,'Windows')!==false?'Windows':(stripos($ua,'Macintosh')!==false?'macOS':(stripos($ua,'Linux')!==false?'Linux':'Drugo'))));
 $browser=preg_match('/Edg\//i',$ua)?'Edge':(preg_match('/OPR\//i',$ua)?'Opera':(preg_match('/Firefox\//i',$ua)?'Firefox':(preg_match('/Chrome\/|CriOS\//i',$ua)?'Chrome':(preg_match('/Safari\//i',$ua)?'Safari':'Drugi preglednik'))));
 return [$device,$os,$browser];
}
function ugc_safe($value,int $limit=160): string {return is_string($value)?substr(preg_replace('/[\x00-\x1f\x7f]/','',trim($value)),0,$limit):'';}
function ugc_path($value): string {$path=ugc_safe($value,250);return preg_match('~^/[a-zA-Z0-9/_\.\-]*$~',$path)?$path:'/';}
function ugc_internal(string $host): bool {return in_array(strtolower($host),['ugc.ba','www.ugc.ba','ugc.gordon.ba','localhost','127.0.0.1'],true);}
function ugc_names(): array {$file=__DIR__.'/analytics-geo/creators.json';$a=json_decode((string)@file_get_contents($file),true);return is_array($a)?$a:[];}
function ugc_json(int $code,array $data=[]): void {http_response_code($code);if($code!==204)echo json_encode($data,JSON_UNESCAPED_UNICODE);exit;}
