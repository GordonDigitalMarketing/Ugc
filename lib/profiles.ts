import media from './creator-media.json';
export type Profile = {id:string;slug:string;legacySlug?:string;name:string;role:'Creator';image:string;languages:string[];category:string;gallery:string[];age?:number;ageEstimated?:boolean};
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
 'creator-115':{name:'Ema Kovač',age:16,ageEstimated:false,languages:['English','Bosnian']}
};
for(const profile of profiles){const detail=details[profile.id];const originalSlug=profile.slug;Object.assign(profile,{ageEstimated:true},detail);profile.slug=profile.name.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/ /g,'-');if(profile.slug!==originalSlug)profile.legacySlug=originalSlug;}
export const categories=['Lifestyle','Beauty','Fitness','Fashion','SaaS','Sport'];
