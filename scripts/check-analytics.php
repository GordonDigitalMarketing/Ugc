<?php
declare(strict_types=1);
require __DIR__.'/../public/analytics-lib.php';
require __DIR__.'/../public/analytics-report.php';
function verify(bool $ok,string $message): void {if(!$ok)throw new RuntimeException($message);}
$key='test-only-key';$names=['creator-202'=>'Mia','creator-203'=>'Sonja','creator-1'=>'Olivia'];
$media=['creator-202'=>['gallery'=>['/images/creator-202/01.webp']],'creator-203'=>['gallery'=>['/images/creator-203/02.webp']]];
$base=['ip'=>'203.0.113.1','country'=>'US','device'=>'Računar','os'=>'Windows','browser'=>'Chrome','host'=>'ugc.gordon.ba','source'=>'Direct','entry'=>'/','utm'=>[],'page'=>'/','pageId'=>'test-page','creator'=>'','visitor'=>'legacy-monthly-id'];
$fixtures=[];foreach([
 ['t'=>1000,'type'=>'page_view'],
 ['t'=>1001,'type'=>'click','creator'=>'creator-202','action'=>'Otvori profil'],
 ['t'=>1002,'type'=>'creator_view','creator'=>'creator-202'],
 ['t'=>1003,'type'=>'photo_view','creator'=>'creator-202','photo'=>'/images/creator-202/01.webp'],
 ['t'=>1018,'type'=>'heartbeat','creator'=>'creator-202','photo'=>'/images/creator-202/01.webp','seconds'=>15],
 ['t'=>1048,'type'=>'heartbeat','creator'=>'creator-202','seconds'=>30],
 ['t'=>4000,'type'=>'page_view','creator'=>'creator-203','visitor'=>'new-month-id','page'=>'/creators/sonja/'],
 ['t'=>4010,'type'=>'click','creator'=>'creator-203','action'=>'Otvori fotografiju','target'=>'/images/creator-203/02.webp'],
 ['t'=>4025,'type'=>'heartbeat','creator'=>'creator-203','photo'=>'/images/creator-203/02.webp','seconds'=>15],
 ['t'=>4030,'type'=>'page_view','creator'=>'creator-1','ip'=>'198.51.100.22','country'=>'IE','device'=>'Mobitel','os'=>'iOS','browser'=>'Safari'],
 ['t'=>4050,'type'=>'page_view','browser'=>'Firefox'],
] as $event)$fixtures[]=array_replace($base,$event);
$id=ugc_visitor_id($base['ip'],$base['device'],$base['os'],$base['browser'],$key);
$r=ugc_report($fixtures,$names,$key,$id,$media);
verify(count($r['people'])===3,'Different IPs and browser categories must have separate traces');
verify(count($r['sessions'])===4&&$r['returning']===1,'A return after 30 minutes must start another session');
verify(count($r['people'][$id]['sessions'])===2,'Legacy and new IDs must join using the same IP/device/browser');
verify($r['people'][$id]['models']['creator-202']['views']===1,'Creator opens must be attributed to the visitor');
verify($r['people'][$id]['models']['creator-202']['clicks']===1,'Creator clicks must be attributed to the visitor');
verify($r['people'][$id]['models']['creator-202']['seconds']===45,'Creator active time must include photo time once');
verify($r['people'][$id]['photos']['/images/creator-202/01.webp']['seconds']===15,'Individual photo time must stay separate');
verify($r['people'][$id]['photos']['/images/creator-202/01.webp']['views']===1,'Photo opens must be counted');
verify($r['people'][$id]['photos']['/images/creator-203/02.webp']['clicks']===1,'Legacy direct image targets must be recoverable');
verify($r['seconds']===60,'Photo time must not double-count total time');
verify(count($r['timeline'])===6,'Timeline must omit heartbeat noise');
verify($r['countryCounts']['US']===3&&$r['countryCounts']['IE']===1,'Country grouping must preserve separate visit traces');
$unsafe=ugc_report([array_replace($base,['t'=>5000,'type'=>'photo_view','creator'=>'creator-202','photo'=>'https://example.com/tracker'])],$names,$key,$id,$media);
verify(count($unsafe['people'][$id]['photos'])===0,'Only supplied gallery photo paths may be displayed');
verify(ugc_visitor_id('2001:db8::1','Tablet','iOS','Safari',$key)===ugc_visitor_id('2001:0db8:0:0:0:0:0:1','Tablet','iOS','Safari',$key),'Equivalent IPv6 forms must have the same trace');
echo "Analytics report checks passed.\n";
