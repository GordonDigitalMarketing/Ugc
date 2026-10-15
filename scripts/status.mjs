import { execFileSync } from 'node:child_process';
const commit=execFileSync('git',['rev-parse','HEAD'],{encoding:'utf8'}).trim();
const headers={'User-Agent':'ugc-ba-publisher','Cache-Control':'no-cache'};
const repo='GordonDigitalMarketing/Ugc';
console.log(`Local commit: ${commit}`);
const actions=await fetch(`https://api.github.com/repos/${repo}/actions/workflows/publish.yml/runs?per_page=1`,{headers});
if(actions.ok){const data=await actions.json();const run=data.workflow_runs?.[0];if(run)console.log(`GitHub build: ${run.status} / ${run.conclusion||'pending'}\n${run.html_url}`)}else console.log(`GitHub status unavailable: HTTP ${actions.status}`);
try{const response=await fetch(`https://ugc.ba/version.json?t=${Date.now()}`,{headers,signal:AbortSignal.timeout(15000)});if(!response.ok)throw new Error(`HTTP ${response.status}`);const version=await response.json();console.log(`Live commit: ${version.commit}\nMatches local commit: ${version.commit===commit}`);if(version.commit!==commit)process.exitCode=1}catch(error){console.log(`Domain verification unavailable: ${error.message}. No DNS settings were changed.`);process.exitCode=1}
