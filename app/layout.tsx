import type { Metadata } from 'next';
import './globals.css';
export const metadata: Metadata = {title:'FRAME — UGC creators & video editors',description:'Discover multilingual UGC creators and video editors. Explore portfolios, build your shortlist and plan your next brand campaign.'};
export default function RootLayout({children}:Readonly<{children:React.ReactNode}>){return <html lang="en"><body>{children}</body></html>}
