<?php
declare(strict_types=1);
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed = ['https://ugc.ba', 'https://www.ugc.ba', 'http://ugc.ba', 'https://ugc.gordon.ba', 'http://ugc.gordon.ba', 'http://127.0.0.1:3000', 'http://localhost:3000'];
if ($origin !== '' && !in_array($origin, $allowed, true)) { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'This origin is not allowed.']); exit; }
if ($origin !== '') { header('Access-Control-Allow-Origin: '.$origin); header('Vary: Origin'); }
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { header('Access-Control-Allow-Methods: POST'); header('Access-Control-Allow-Headers: Content-Type'); http_response_code(204); exit; }
function respond(int $status, array $body): void { http_response_code($status); echo json_encode($body); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(405,['ok'=>false,'error'=>'Please submit the contact form.']);
if ((int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 20000) respond(413,['ok'=>false,'error'=>'Your enquiry is too long.']);
if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== 0) respond(415,['ok'=>false,'error'=>'JSON is required.']);
$raw=file_get_contents('php://input', false, null, 0, 20001);
if ($raw === false || strlen($raw)>20000) respond(413,['ok'=>false,'error'=>'Your enquiry is too long.']);
$data=json_decode($raw,true);
if (!is_array($data)) respond(400,['ok'=>false,'error'=>'Invalid form data.']);
if (!empty($data['companyFax'])) respond(400,['ok'=>false,'error'=>'Unable to submit this form.']);
function field(array $data,string $key,int $max=500): string { $value=$data[$key]??''; if(!is_string($value)) respond(422,['ok'=>false,'error'=>'Invalid form field.']); $value=trim($value); if(strlen($value)>$max) respond(422,['ok'=>false,'error'=>'A form field is too long.']); return $value; }
$name=field($data,'name',100); $email=field($data,'email',200); $brand=field($data,'brand',150); $description=field($data,'description',5000); $service=field($data,'service',100);
if (!$name || !$brand || !$service || strlen($description)<15 || !filter_var($email,FILTER_VALIDATE_EMAIL) || preg_match('/[\r\n]/',$email)) respond(422,['ok'=>false,'error'=>'Please complete your name, email, brand, service and project details.']);
if (($data['privacy']??'')!=='on') respond(422,['ok'=>false,'error'=>'Please read and acknowledge the privacy policy.']);
$website=field($data,'website',300); if($website!=='' && !filter_var($website,FILTER_VALIDATE_URL)) respond(422,['ok'=>false,'error'=>'Please enter a valid website URL.']);
// Rate-limit outside the public web directory. Message contents are not saved here.
$storage=dirname(__DIR__).'/.ugc-contact';
if (!is_dir($storage) && !@mkdir($storage,0700,true)) respond(503,['ok'=>false,'error'=>'Please email us while the form is unavailable.']);
$ipHash=hash('sha256',$_SERVER['REMOTE_ADDR']??'unknown');
$handle=@fopen($storage.'/'.$ipHash.'.json','c+');
if (!$handle || !flock($handle,LOCK_EX)) respond(503,['ok'=>false,'error'=>'Please try again shortly.']);
$now=time(); $attempts=json_decode(stream_get_contents($handle),true); if(!is_array($attempts))$attempts=[];
$attempts=array_values(array_filter($attempts,fn($t)=>is_int($t)&&$t>$now-3600));
if(count($attempts)>=5){flock($handle,LOCK_UN);fclose($handle);respond(429,['ok'=>false,'error'=>'Too many enquiries. Please try again in an hour or email us.']);}
$attempts[]=$now; rewind($handle); ftruncate($handle,0); fwrite($handle,json_encode($attempts)); fflush($handle); flock($handle,LOCK_UN); fclose($handle);
foreach(glob($storage.'/*.json')?:[] as $file){if(filemtime($file)<$now-86400)@unlink($file);}
$selection=[]; if(isset($data['creators']) && is_array($data['creators'])) foreach(array_slice($data['creators'],0,30) as $person){if(is_array($person)&&isset($person['name'])&&is_string($person['name']))$selection[]=substr(str_replace(["\r","\n"],' ',$person['name']),0,100);}
$lines=['New UGC.ba Studio enquiry','Name: '.$name,'Email: '.$email,'Brand: '.$brand,'Website: '.$website,'Service: '.$service,'Creators: '.($selection?implode(', ',$selection):'Please suggest a match')];
foreach(['budget','deadline','language'] as $key)$lines[]=ucfirst($key).': '.field($data,$key,200);
$campaign=$data['campaign']??null;
if($campaign!==null){
 if(!is_array($campaign)||!in_array($campaign['videos']??null,[10,20,30],true)||!in_array($campaign['usage']??null,['organic','paid','whitelisting'],true)||!isset($campaign['allocations'])||!is_array($campaign['allocations'])||count($campaign['allocations'])<1||count($campaign['allocations'])>20)respond(422,['ok'=>false,'error'=>'Please choose a valid video package and creator mix.']);
 $sum=0;$ids=[];$splits=[];
 foreach($campaign['allocations'] as $part){if(!is_array($part)||!isset($part['id'],$part['name'],$part['videos'])||!is_string($part['id'])||!is_string($part['name'])||strlen($part['name'])>100||!is_int($part['videos'])||$part['videos']<1||$part['videos']>30||in_array($part['id'],$ids,true))respond(422,['ok'=>false,'error'=>'Please check the video allocation for each creator.']);$sum+=$part['videos'];$ids[]=$part['id'];$splits[]=str_replace(["\r","\n"],' ',$part['name']).': '.$part['videos'].' videos';}
 if($sum!==$campaign['videos'])respond(422,['ok'=>false,'error'=>'The creator split must add up to your selected package.']);
 $lines[]='PACKAGE: '.$campaign['videos'].' fully edited, ready-to-publish videos';$lines[]='Usage: '.$campaign['usage'];$lines[]='Creator mix: '.implode('; ',$splits);$lines[]='Pricing: one combined quote for this entire package; no payment or booking created.';
}else{foreach(['videos','usage'] as $key)$lines[]=ucfirst($key).': '.field($data,$key,200);}
$lines[]="\nProject:\n".$description; $lines[]="\nPrivacy notice acknowledged. Submitted: ".gmdate('c');
$host=strtolower(explode(':',$_SERVER['HTTP_HOST']??'ugc.ba')[0]);
$sender=$host==='ugc.gordon.ba'?'info@gordondm.com':'info@ugc.ba';
$headers=['From'=>'UGC.ba Studio <'.$sender.'>','Reply-To'=>$email,'MIME-Version'=>'1.0','Content-Type'=>'text/plain; charset=UTF-8'];
$accepted=@mail('info@gordondm.com, info@ugc.ba','New project enquiry | UGC.ba Studio',implode("\r\n",$lines),$headers,'-f'.$sender);
if(!$accepted)respond(503,['ok'=>false,'error'=>'Mail could not be accepted. Please email info@ugc.ba directly.']);
respond(200,['ok'=>true]);
