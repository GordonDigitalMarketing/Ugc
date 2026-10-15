import {execFileSync} from 'node:child_process';
const commit=execFileSync('git',['rev-parse','HEAD'],{encoding:'utf8'}).trim();
const headers={'User-Agent':'ugc-ba-publisher','Cache-Control':'no-cache'};
console.log(`Local commit: ${commit}`);
const actions=await fetch('https://api.github.com/repos/GordonDigitalMarketing/Ugc/actions/workflows/publish.yml/runs?per_page=1',{headers});
if(actions.ok){const data=await actions.json();const run=data.workflow_runs?.[0];if(run)console.log(`GitHub build: ${run.status} / ${run.conclusion||'pending'}\n${run.html_url}`)}
let matching=0;
for(const base of ['https://ugc.gordon.ba','https://ugc.ba']){
 try{const response=await fetch(`${base}/version.json?t=${Date.now()}`,{headers,signal:AbortSignal.timeout(15000)});if(!response.ok)throw new Error(`HTTP ${response.status}`);const version=await response.json();console.log(`${base}: ${version.commit} (${version.commit===commit?'current':'update pending'})`);if(version.commit===commit)matching++}catch(error){console.log(`${base}: verification unavailable (${error.message}).`)}
}
if(!matching)process.exitCode=1;
