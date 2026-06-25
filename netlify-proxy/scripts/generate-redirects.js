const fs = require('fs');
const path = require('path');

const backend = process.env.RAILWAY_BACKEND_URL;
if (!backend) {
    console.error('RAILWAY_BACKEND_URL is required. Set it in Netlify → Site settings → Environment variables.');
    process.exit(1);
}

const base = backend.replace(/\/$/, '');
const redirects = `/*  ${base}/:splat  200\n`;

const out = path.join(__dirname, '..', 'public', '_redirects');
fs.writeFileSync(out, redirects, 'utf8');
console.log(`Proxy configured: /* → ${base}/:splat`);
