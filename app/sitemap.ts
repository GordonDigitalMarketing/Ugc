import type {MetadataRoute} from 'next';
import {profiles} from '@/lib/profiles';
export const dynamic='force-static';
export default function sitemap():MetadataRoute.Sitemap{return [{url:'https://ugc.ba/',changeFrequency:'weekly',priority:1},{url:'https://ugc.ba/privacy/',changeFrequency:'yearly',priority:.2},...profiles.map(p=>({url:`https://ugc.ba/creators/${p.slug}/`,changeFrequency:'monthly' as const,priority:.7}))]}
