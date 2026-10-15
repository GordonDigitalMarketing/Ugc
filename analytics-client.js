/* First-party studio analytics. No cookies or persistent browser identifiers. */
(()=>{
 'use strict';
 if(!['ugc.ba','www.ugc.ba','ugc.gordon.ba'].includes(location.hostname)||navigator.doNotTrack==='1'||navigator.globalPrivacyControl===true)return;
 const endpoint='https://ugc.gordon.ba/analytics.php';
 const uuid=()=>crypto.randomUUID();const pageId=uuid();const page=location.pathname;
 let currentCreator='',currentPhoto='',active=0,lastInteraction=Date.now(),stopped=false;
 const pathOnly=value=>{try{const u=new URL(value,location.origin);return u.origin===location.origin?u.pathname:'/';}catch{return '/';}};
 const utm={};const params=new URLSearchParams(location.search);for(const key of ['source','medium','campaign'])utm[key]=(params.get('utm_'+key)||'').slice(0,80);
 let referrer='';try{const u=new URL(document.referrer);referrer=u.origin+u.pathname;}catch{}
 function send(type,extra={}){const payload=JSON.stringify({type,page,pageId,eventId:uuid(),creator:currentCreator,photo:currentPhoto,referrer,utm,...extra});try{if(navigator.sendBeacon){navigator.sendBeacon(endpoint,new Blob([payload],{type:'text/plain'}));}else{fetch(endpoint,{method:'POST',body:payload,keepalive:true,credentials:'omit',headers:{'Content-Type':'text/plain'}}).catch(()=>{});}}catch{}}
 function flush(){if(active>0){send('heartbeat',{seconds:Math.min(30,active)});active=0;}}
 function visibleCreator(){return [...document.querySelectorAll('dialog[open][data-creator-id]')].at(-1)?.getAttribute('data-creator-id')||document.querySelector('main.creator-page[data-creator-id]')?.getAttribute('data-creator-id')||'';}
 function visiblePhoto(){const img=[...document.querySelectorAll('dialog[open] .lightbox>img')].at(-1);return img?pathOnly(img.getAttribute('src')||''):'';}
 currentCreator=visibleCreator();send('page_view');
 new MutationObserver(()=>{const next=visibleCreator(),photo=visiblePhoto();if(next!==currentCreator||photo!==currentPhoto){flush();const creatorChanged=next!==currentCreator;currentCreator=next;currentPhoto=photo;if(creatorChanged&&next)send('creator_view');if(photo)send('photo_view');}}).observe(document.body,{childList:true,subtree:true,attributes:true,attributeFilter:['open','src','data-creator-id']});
 ['pointerdown','pointermove','keydown','scroll','touchstart'].forEach(type=>addEventListener(type,()=>{lastInteraction=Date.now();},{passive:true}));
 setInterval(()=>{if(!stopped&&document.visibilityState==='visible'&&Date.now()-lastInteraction<60000)active++;},1000);
 setInterval(flush,15000);
 document.addEventListener('visibilitychange',()=>{if(document.visibilityState==='hidden')flush();else lastInteraction=Date.now();});
 addEventListener('pagehide',()=>{flush();stopped=true;});addEventListener('pageshow',event=>{if(event.persisted){stopped=false;lastInteraction=Date.now();send('page_view');}});
 document.addEventListener('click',event=>{
  const el=event.target instanceof Element?event.target.closest('a,button'):null;if(!el)return;
  const creator=el.closest('[data-creator-id]')?.getAttribute('data-creator-id')||currentCreator;
  let action=(el.getAttribute('aria-label')||el.textContent||'Click').replace(/\s+/g,' ').trim().slice(0,80);
  if(el.classList.contains('image-button')||el.classList.contains('portfolio-link')||el.classList.contains('hero-profile'))action='Otvori profil';
  else if(el.closest('.gallery-grid')||el.closest('.creator-photo-grid'))action='Otvori fotografiju';
  else if(el.type==='submit')action='Pošalji upit';
  const photo=action==='Otvori fotografiju'?pathOnly(el.getAttribute('data-photo-path')||el.querySelector('img')?.getAttribute('src')||''):currentPhoto;
  send('click',{creator,action,photo,target:pathOnly(el.getAttribute('href')||page)});
 },true);
 document.addEventListener('change',event=>{const el=event.target;if(el instanceof HTMLSelectElement&&el.matches('[data-track-creator]'))send('click',{creator:el.value,action:'Odaberi kreatorku u paketu',target:page});});
 addEventListener('ugc:enquiry-sent',()=>send('click',{action:'Upit uspješno poslan',target:page}));
})();
