export const packages=[{name:'The first take',videos:10},{name:'The sweet spot',videos:20},{name:'The full picture',videos:30}] as const;
export const usageOptions=[
 {id:'organic',name:'Organic',description:'Fully edited, ready-to-post files delivered to your brand for organic publishing. No creator-account posting or paid advertising licence.'},
 {id:'paid',name:'Paid ads',description:'Fully edited files delivered to your brand with agreed permission to run paid ads from your own advertising accounts. Platforms and licence duration are confirmed in your quote.'},
 {id:'whitelisting',name:'Whitelisting',description:'Creator films and publishes the video on their own channel. Your brand funds and runs ads through the agreed creator-post / partnership-ad permissions. Publishing, advertising period and fees are agreed together.'}
] as const;
export type Campaign={packageName:string;videos:number;usage:string;allocations:{id:string;name:string;videos:number}[]};
