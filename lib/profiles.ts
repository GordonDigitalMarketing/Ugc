import media from './creator-media.json';
export type Profile = {id:string;slug:string;legacySlug?:string;legacySlugs?:string[];name:string;role:'Creator';image:string;languages:string[];languageNote?:string;category:string;gallery:string[];age?:number;ageEstimated?:boolean;focus?:string[]};
const roster:[string,string,string][]=[
 ['creator-1','Olivia Bennett','Lifestyle'],['creator-3','Emma Collins','Fitness'],['creator-5','Sophie Williams','SaaS'],['creator-7','Charlotte Hayes','Sport'],['creator-9','Amelia Foster','Fashion'],
 ['creator-101','Ava Richardson','Lifestyle'],['creator-102','Chloe Spencer','Fashion'],['creator-103','Isla Kennedy','Fashion'],['creator-104','Ruby Lawson','Beauty'],['creator-105','Freya Hudson','Fitness'],['creator-106','Evie Fletcher','Fashion'],['creator-107','Alice Monroe','Beauty'],['creator-108','Maya Sterling','Lifestyle'],['creator-109','Zoe Whitmore','Fashion'],['creator-110','Nora Ellis','Beauty'],['creator-111','Clara West','Lifestyle'],['creator-112','Aria Blake','Lifestyle'],['creator-113','Hazel Quinn','Lifestyle'],['creator-114','Daisy Emerson','Fashion'],['creator-115','Mila Hart','Sport']
];
export const profiles:Profile[]=roster.map(([id,name,category])=>({id,slug:name.toLowerCase().replace(/ /g,'-'),name,category,role:'Creator',languages:['English'],...media[id as keyof typeof media]}));
const details:Record<string,{name?:string;age:number;languages:string[];ageEstimated?:boolean}>={
 'creator-1':{age:25,languages:['English','Bosnian']},
 'creator-3':{age:24,languages:['English']},
 'creator-5':{age:26,languages:['English','Bosnian']},
 'creator-7':{age:23,languages:['English']},
 'creator-9':{age:24,languages:['English']},
 'creator-101':{age:24,languages:['English']},
 'creator-102':{name:'Lejla Hadžić',age:23,languages:['English','Bosnian']},
 'creator-103':{name:'Mila Petrović',age:19,languages:['English','Serbian']},
 'creator-104':{age:21,languages:['English']},
 'creator-105':{age:22,languages:['English']},
 'creator-106':{name:'Sara Begović',age:25,languages:['English','Bosnian']},
 'creator-107':{age:24,languages:['English']},
 'creator-108':{name:'Nina Jovanović',age:23,languages:['English','Serbian']},
 'creator-109':{age:22,languages:['English']},
 'creator-110':{age:24,languages:['English']},
 'creator-111':{name:'Hana Delić',age:23,languages:['English','Bosnian']},
 'creator-112':{age:22,languages:['English']},
 'creator-113':{age:25,languages:['English']},
 'creator-114':{name:'Dunja Marković',age:22,languages:['English','Serbian']},
 'creator-115':{name:'Ema Kovač',age:18,ageEstimated:false,languages:['English','Bosnian']}
};
for(const profile of profiles){const detail=details[profile.id];const originalSlug=profile.slug;Object.assign(profile,{ageEstimated:true},detail);profile.slug=profile.name.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/ /g,'-');if(profile.slug!==originalSlug)profile.legacySlug=originalSlug;}

const revisions:Record<string,{name?:string;languages:string[]}>={
 'creator-1':{languages:['English','Croatian']},'creator-3':{name:'Emily Carter',languages:['English']},'creator-5':{name:'Laura Mitchell',languages:['English','Croatian']},'creator-7':{languages:['English']},'creator-9':{languages:['English']},
 'creator-101':{name:'Anna Wilson',languages:['English']},'creator-102':{name:'Lejla Hadžić',languages:['English','Bosnian']},'creator-103':{name:'Mila Petrović',languages:['English','Serbian']},'creator-104':{name:'Sarah Miller',languages:['English']},'creator-105':{name:'Hannah Brooks',languages:['English']},'creator-106':{name:'Sara Begović',languages:['English','Bosnian']},'creator-107':{name:'Julia Evans',languages:['English']},'creator-108':{name:'Nina Jovanović',languages:['English','Serbian']},'creator-109':{name:'Chloe Martin',languages:['English']},'creator-110':{name:'Emma Novak',languages:['English','Croatian']},'creator-111':{name:'Petra Horvat',languages:['English','Croatian']},'creator-112':{name:'Lena Fischer',languages:['English','German']},'creator-113':{name:'Lucy Parker',languages:['English']},'creator-114':{name:'Dunja Marković',languages:['English','Serbian']},'creator-115':{name:'Ana Kovač',languages:['English','Croatian']}
};
for(const p of profiles){const old=[p.slug,...(p.legacySlug?[p.legacySlug]:[])];Object.assign(p,revisions[p.id]);p.slug=p.name.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/ /g,'-');p.legacySlugs=[...new Set(old)].filter(s=>s!==p.slug);p.focus=[...new Set(['SaaS',p.category,...(p.category==='Beauty'?['Fashion']:p.category==='Sport'||p.category==='Fitness'?['Lifestyle']:['Beauty'])])];}
// Language updates follow the supplied archive-folder records, not appearance.
const confirmedLanguages:Record<string,{languages:string[];languageNote?:string}>={
 'creator-7':{languages:['English','German (very good)']},
 'creator-101':{languages:['English (C1)','Italian (C2)','Spanish (B2)']},
 'creator-102':{languages:['English','Bosnian'],languageNote:'German script rehearsal available on request; German proficiency has not been confirmed.'},
 'creator-103':{languages:['Turkish (B1)']},
 'creator-104':{languages:['English']},
 'creator-110':{languages:['English','German (basic)']},
 'creator-111':{languages:['English (fluent)']},
 'creator-112':{languages:['English']},
 'creator-115':{languages:['English']}
};
for(const profile of profiles){if(confirmedLanguages[profile.id])Object.assign(profile,confirmedLanguages[profile.id]);}
profiles.push(
 {id:'creator-201',slug:'elise',name:'Elise',role:'Creator',category:'Lifestyle',focus:['SaaS','Lifestyle','Beauty'],languages:['English'],age:23,ageEstimated:true,...media['creator-201']},
 {id:'creator-202',slug:'mia',name:'Mia',role:'Creator',category:'Fashion',focus:['SaaS','Fashion','Beauty'],languages:['English'],age:24,ageEstimated:true,...media['creator-202']}
);
// Keep existing portfolio URLs working while displaying only first names everywhere.
for(const profile of profiles){profile.name=profile.name.trim().split(/\s+/)[0];profile.languages=profile.languages.map(language=>language.replace(/\s*\([^)]*\)/g,''));}
// Only adult profiles are listed; ages follow the latest supplied corrections.
for(let index=profiles.length-1;index>=0;index--){if(typeof profiles[index].age==='number'&&profiles[index].age!<18)profiles.splice(index,1);}
const featuredIndex=profiles.findIndex(profile=>profile.id==='creator-202');
if(featuredIndex>0)profiles.unshift(...profiles.splice(featuredIndex,1));
export function profileLabel(profile:Profile){return `${profile.name}${typeof profile.age==='number'?`, ${profile.age}`:''}`;}
export const categories=['Lifestyle','Beauty','Fitness','Fashion','SaaS','Sport'];
