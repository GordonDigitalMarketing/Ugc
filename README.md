# UGC.ba Studio

Next.js / React / TypeScript website with 20 owner-supplied creator photo portfolios, a browser-persisted shortlist, enquiry forms and a PHP mail endpoint on the existing cPanel hosting.

## Local preview

npm install
npm run dev

Open http://127.0.0.1:3000. Contact forms in local preview submit to https://ugc.ba/contact.php; the live domain must resolve and have valid HTTPS. Next.js development does not execute PHP. No secrets are required in the frontend.

## Publishing

npm run publish -- "Describe the change"
npm run status

The CLI builds, commits project source and pushes main to https://github.com/GordonDigitalMarketing/Ugc. GitHub Actions checks PHP syntax, builds the static export and publishes built assets to the live branch. A cPanel cron pulls live every minute into /home/ugc/repositories/ugc-live and copies files to /home/ugc/public_html with checksums, delayed updates and public asset permissions. No hosting passwords are committed. DNS and nameservers are not modified. /version.json identifies the deployed source commit.

## Forms and delivery

The homepage form, project brief and individual creator pages all POST JSON to public/contact.php. Recipients are fixed server-side: info@gordondm.com and info@ugc.ba. The server validates data, uses plain-text email, requires privacy acknowledgement, rejects a honeypot and limits a hashed network address to five submissions per hour. Temporary rate-limit records are kept outside public_html and old entries are removed after 24 hours on later requests. No message content is saved in a website database. The PHP mail return value confirms mail-server acceptance; cPanel Track Delivery is used to check recipient delivery.

Enquiries do not create a booking or charge. A hidden companyFax field must stay empty. Failed submissions keep typed fields and show direct email links. Success is shown only after the server accepts the email.

## Profiles and assets

lib/profiles.ts holds stable ids, portfolio names, categories and slugs. lib/creator-media.json maps to neutral local WebP filenames in public/images. All 20 profiles use owner-supplied photos. Public names remain fictional portfolio names by request. Birth dates, original folder names, inferred ages and unsupported accents/availability are not published. Five original profiles are first, followed by 15 additional profiles. Photo sets have their actual available number of photos, without duplicate filler.

Original archives and temporary asset tools remain in ignored .local-assets and are not committed. Sharp exports strip original EXIF metadata. Fonts are self-hosted with their OFL licences in public/fonts. No stock portraits, external font requests, advertising pixels or analytics have been added.

## SEO and policy

Server-rendered content, descriptive metadata, canonical links, social preview PNG, favicon, robots.txt, sitemap and 20 static creator URLs support discovery. Homepage FAQ markup matches visible questions. The privacy page describes the implemented enquiry and browser-storage behavior; firm registration/address details are omitted at the owner's request. No search-ranking or legal-compliance guarantee is made.

## Checks

npm run build
npm run typecheck

GitHub Actions also runs php -l public/contact.php. Browser checks cover profile filtering, actual-image galleries, shortlist selection/removal and handoff to the enquiry form, responsive navigation and policy links.

## Temporary live mirror: ugc.gordon.ba

An isolated mirror is configured on the existing gordondm cPanel account:
- Document root: /home/gordondm/ugc.gordon.ba (separate from /home/gordondm/gordon.ba and public_html).
- Git clone: /home/gordondm/repositories/ugc-gordon-live, checked out to live.
- A dedicated cron pulls the live branch and copies only into /home/gordondm/ugc.gordon.ba every minute. Existing cron jobs are untouched.
- The shared .cpanel.yml selects only the fixed UGC destination for each known account. It does not write to gordon.ba or any other website.
- HTTPS is valid on the mirror. Both enquiry recipients remain info@gordondm.com and info@ugc.ba. The mirror sends using the existing info@gordondm.com domain, and local preview submits to the mirror.
- Canonicals remain on ugc.ba; the temporary host sends X-Robots-Tag: noindex, follow to avoid duplicate indexing.

Future requests to publish GitHub + live should verify both mirrors' version.json. Never edit other WHM accounts, the gordon.ba document root, existing redirects or unrelated DNS records as part of UGC publication.

## Private studio analytics

- `/studio-statistika.php` is an authenticated dashboard, omitted from site navigation and marked noindex. The primary-domain URL redirects to the protected mirror dashboard.
- Both sites send first-party events to `https://ugc.gordon.ba/analytics.php`. Visitor cookies and persistent browser identifiers are not used. Do Not Track and Global Privacy Control opt out of collection.
- Event logs and the server-generated identity key are stored only in `/home/gordondm/.ugc-analytics`, outside every public document root, with restricted file permissions. This folder must be preserved across deployments.
- Statistics include full IPs, approximate country, device/browser, entry sources, creator opens/clicks, individual photo opens and active time. Returning visits are estimated from an HMAC of the normalized IP address plus device, OS and browser categories, with a 30-minute session boundary. Existing records are grouped by the same criteria retrospectively within the retained 30-day window. A routine collection cleanup removes files older than 30 days.
- Country lookups use a local DB-IP Country Lite October 2026 binary database. The dashboard provides the required attribution; no visitor IP is sent to a geolocation provider. Update both network-order range files together when refreshing the dataset.
- One account is configured. Its password verifier uses salted PBKDF2-SHA256 with 600,000 iterations. Dashboard sessions expire after two hours, with Secure/HttpOnly/SameSite cookies, CSRF protection and login rate limiting. No plaintext password is stored in source.
- PHP syntax checks cover every public PHP endpoint before deployment. Test authentication, anonymous access denial, valid creator collection and optional contact fields after changes.

Visitor drill-down: choose a country or search an IP/trace ID in the visitor list, then open a trace to view sessions, models, individual photos, active-time totals and a paginated action timeline. No visitor accounts or persistent browser identifiers are created. Detail pages require the same owner authentication as the dashboard. Gallery lightboxes let the collector attribute foreground time to individual photo paths. Photo time is a subset of creator time, not additional total time. Historical photo details cannot be reconstructed when they were not logged.
