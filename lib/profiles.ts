import media from './creator-media.json';
export type Profile = {id:string;slug:string;name:string;role:'Creator';image:string;languages:string[];category:string;gallery:string[]};
const roster:[string,string,string][]=[
 ['creator-1','Olivia Bennett','Lifestyle'],['creator-3','Emma Collins','Fitness'],['creator-5','Sophie Williams','SaaS'],['creator-7','Charlotte Hayes','Sport'],['creator-9','Amelia Foster','Fashion'],
 ['creator-101','Ava Richardson','Lifestyle'],['creator-102','Chloe Spencer','Fashion'],['creator-103','Isla Kennedy','Fashion'],['creator-104','Ruby Lawson','Beauty'],['creator-105','Freya Hudson','Fitness'],['creator-106','Evie Fletcher','Fashion'],['creator-107','Alice Monroe','Beauty'],['creator-108','Maya Sterling','Lifestyle'],['creator-109','Zoe Whitmore','Fashion'],['creator-110','Nora Ellis','Beauty'],['creator-111','Clara West','Lifestyle'],['creator-112','Aria Blake','Lifestyle'],['creator-113','Hazel Quinn','Lifestyle'],['creator-114','Daisy Emerson','Fashion'],['creator-115','Mila Hart','Sport']
];
export const profiles:Profile[]=roster.map(([id,name,category])=>({id,slug:name.toLowerCase().replace(/ /g,'-'),name,category,role:'Creator',languages:['English'],...media[id as keyof typeof media]}));
export const categories=['Lifestyle','Beauty','Fitness','Fashion','SaaS','Sport'];
