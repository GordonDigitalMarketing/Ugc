'use client';
import {useEffect,useRef,useState} from 'react';
import {ArrowLeft,ArrowRight,X} from 'lucide-react';
export default function PhotoGallery({creatorId,name,photos}:{creatorId:string;name:string;photos:string[]}){
 const [index,setIndex]=useState<number|null>(null);const dialog=useRef<HTMLDialogElement>(null);const opener=useRef<HTMLButtonElement|null>(null);
 useEffect(()=>{if(index===null){if(dialog.current?.open)dialog.current.close();opener.current?.focus();return;}if(!dialog.current?.open)dialog.current?.showModal();},[index]);
 useEffect(()=>{if(index===null)return;const old=document.body.style.overflow;document.body.style.overflow='hidden';return()=>{document.body.style.overflow=old};},[index]);
 const close=()=>setIndex(null);
 return <><div className="creator-photo-grid">{photos.map((src,i)=><button type="button" key={src} className="gallery-photo-button" data-photo-path={src} onClick={e=>{opener.current=e.currentTarget;setIndex(i)}} aria-label={`Open ${name} photo ${i+1}`}><img src={src} alt={`${name} portfolio photo ${i+1}`} loading="lazy"/><span>View photo ↗</span></button>)}</div><dialog ref={dialog} className="photo-dialog" data-creator-id={creatorId} aria-label={`${name} photo viewer`} onCancel={close} onClose={close} onClick={e=>{if(e.currentTarget===e.target)close()}}><button type="button" className="close" onClick={close} aria-label="Close photo viewer"><X size={22}/></button>{index!==null&&<div className="lightbox"><img src={photos[index]} alt={`${name} photo ${index+1}`}/><div className="lightbox-controls"><button type="button" className="button outline" onClick={()=>setIndex((index+photos.length-1)%photos.length)} aria-label="Previous photo"><ArrowLeft size={17}/>Previous</button><span>{index+1} / {photos.length}</span><button type="button" className="button dark" onClick={()=>setIndex((index+1)%photos.length)} aria-label="Next photo">Next<ArrowRight size={17}/></button></div></div>}</dialog></>;
}
