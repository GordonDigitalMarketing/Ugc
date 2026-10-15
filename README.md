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
