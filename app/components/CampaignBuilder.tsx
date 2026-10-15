'use client';
import {useEffect,useState} from 'react';
import {profiles,profileLabel} from '@/lib/profiles';
import {packages,usageOptions} from '@/lib/campaign';
import ContactForm from './ContactForm';
export default function CampaignBuilder({selectedIds,packageName='',initialCreator}:{selectedIds?:string[];onSelectionChange?:(ids:string[])=>void;packageName?:string;initialCreator?:string}){
 const [plan,setPlan]=useState<string>(packageName||packages[0].name);
 const [creatorId,setCreatorId]=useState(initialCreator||'');
 useEffect(()=>{if(packageName)setPlan(packageName)},[packageName]);
 const selectedId=selectedIds?.at(-1);
 useEffect(()=>{if(selectedId)setCreatorId(selectedId)},[selectedId]);
 const creator=profiles.find(p=>p.id===creatorId);
 return <div className="campaign-builder simple-package"><div className="builder-packages">{packages.map(p=><button type="button" key={p.name} className={plan===p.name?'is-picked':''} aria-pressed={plan===p.name} onClick={()=>setPlan(p.name)}><strong>{p.videos}</strong><span>edited videos</span><small>{p.name}</small></button>)}</div><p className="builder-note">Fully edited videos, ready to publish. Choose your package, tell us your idea and we’ll confirm the details with you.</p><label className="package-creator-label">Preferred creator <select data-track-creator value={creatorId} onChange={e=>setCreatorId(e.target.value)}><option value="">Help me choose a creator</option>{profiles.map(p=><option key={p.id} value={p.id}>{profileLabel(p)}</option>)}</select></label><div className="package-usage-guide">{usageOptions.map(o=><div key={o.id}><strong>{o.name}</strong><p>{o.description}</p></div>)}</div><ContactForm key={`${plan}-${creatorId}`} packageName={plan} talent={creator?[{id:creator.id,name:profileLabel(creator)}]:[]}/></div>;
}
