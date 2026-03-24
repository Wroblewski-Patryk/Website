import fs from 'node:fs';
import path from 'node:path';

const manifestPath = process.argv[2] ?? 'public/build/manifest.json';
const buildDir = process.argv[3] ?? 'public/build';
const markdownOut = process.argv[4] ?? 'bundle-size-report.md';
const jsonOut = process.argv[5] ?? 'bundle-size-report.json';

if (!fs.existsSync(manifestPath)) {
  console.error(`Manifest not found: ${manifestPath}`);
  process.exit(1);
}

const manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf8'));
const assets = new Set();

for (const entry of Object.values(manifest)) {
  if (entry && typeof entry === 'object') {
    if (typeof entry.file === 'string') assets.add(entry.file);
    if (Array.isArray(entry.css)) {
      for (const cssFile of entry.css) assets.add(cssFile);
    }
  }
}

const rows = [];
let totalBytes = 0;
for (const relPath of assets) {
  const fullPath = path.join(buildDir, relPath);
  if (!fs.existsSync(fullPath)) continue;
  const size = fs.statSync(fullPath).size;
  totalBytes += size;
  rows.push({
    asset: relPath,
    bytes: size,
    kb: Number((size / 1024).toFixed(2)),
  });
}

rows.sort((a, b) => b.bytes - a.bytes);

const report = {
  generatedAt: new Date().toISOString(),
  assetCount: rows.length,
  totalBytes,
  totalKb: Number((totalBytes / 1024).toFixed(2)),
  largestAssets: rows.slice(0, 20),
  assets: rows,
};

fs.writeFileSync(jsonOut, JSON.stringify(report, null, 2));

const markdownLines = [
  '# Bundle Size Report',
  '',
  `Generated at: ${report.generatedAt}`,
  '',
  `- Asset count: **${report.assetCount}**`,
  `- Total size: **${report.totalKb} KB**`,
  '',
  '## Largest Assets (Top 20)',
  '',
  '| Asset | Size (KB) |',
  '|---|---:|',
  ...report.largestAssets.map((r) => `| \`${r.asset}\` | ${r.kb} |`),
  '',
];

fs.writeFileSync(markdownOut, markdownLines.join('\n'));

console.log(`Bundle report written: ${markdownOut}, ${jsonOut}`);
