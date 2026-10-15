import { spawnSync } from 'node:child_process';
const run = (command, args) => {
  const result = spawnSync(command, args, { stdio: 'inherit' });
  if (result.status !== 0) process.exit(result.status || 1);
};
const message = process.argv.slice(2).join(' ') || 'Update ugc.ba website';
if (!process.env.npm_execpath) { console.error('Run this command with npm run publish.'); process.exit(1); }
run(process.execPath, [process.env.npm_execpath, 'run', 'build']);
run('git', ['add', '--', 'app', 'lib', 'public', 'scripts', '.github', '.gitignore', '.gitattributes', 'next.config.ts', 'next-env.d.ts', 'package.json', 'package-lock.json', 'tsconfig.json', 'README.md', 'AGENTS.md']);
const changed = spawnSync('git', ['diff', '--cached', '--quiet']);
if (changed.status === 1) run('git', ['commit', '-m', message]);
else if (changed.status !== 0) process.exit(1);
run('git', ['push', '-u', 'origin', 'main']);
console.log('Source pushed. GitHub Actions builds the live branch; hosting checks it every minute.');
console.log('Build status: https://github.com/GordonDigitalMarketing/Ugc/actions');
console.log('Live website: https://ugc.ba');
