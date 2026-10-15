# FRAME — UGC talent directory

Next.js / React / TypeScript starter for an agency-managed creator and editor directory.

## Run locally

```sh
npm install
npm run dev
```

Open http://127.0.0.1:3000. Production verification: `npm run build`. Type checking: `npm run typecheck`.

## Publish GitHub + live

```sh
npm run publish -- "Describe the change"
npm run status
```

The publish command verifies the build, commits project files and pushes `main` to https://github.com/GordonDigitalMarketing/Ugc. GitHub Actions builds the static Next.js export and publishes it to the `live` branch. The existing ugc.ba cPanel account clones that branch at `/home/ugc/repositories/ugc-live`. A configured cron checks it every minute and copies built files into `/home/ugc/public_html` using rsync with delayed updates. No hosting passwords or tokens are stored in GitHub or source files. The CLI uses the existing local Git Credential Manager login.

The cPanel **Git Version Control → ugc.ba live → Pull or Deploy** screen also supports manual update and deployment through `.cpanel.yml`. Never edit the hosting clone manually; edit source on `main` instead. No DNS or nameserver changes are part of this workflow. DNS activation and a valid TLS certificate are required before the domain can be verified publicly. `/version.json` identifies the deployed source commit. GitHub build status: https://github.com/GordonDigitalMarketing/Ugc/actions.

The first deployment is published on the existing hosting. Publication does not enable email delivery or administration; the inquiry form remains clearly marked as a demo.

## Current functionality

- 30 illustrative adult creator profiles and 6 editor profiles.
- Search, role-specific category/service filters, language, age, accent, availability and whitelisting filters.
- Accessible native profile dialogs, 5–6 illustrative gallery images and image navigation.
- Browser-persisted shortlist and a project brief form that downloads JSON. No email is sent.
- Responsive layout, package selection and FAQ.

## Replacing portraits and portfolio media

Profile records are in `lib/profiles.ts`. Put approved images in `public/images/` and set a profile's `image` to `/images/filename.jpg`. Its `gallery` is an array of image URLs. For a specific override, add an assignment after profile creation, e.g. `profiles[0].image = '/images/olivia.jpg'`. You can ask Codex to perform this replacement by supplying the image and identifying the profile. No generation API or upload service is required for this file-based workflow.

Current external images are illustrative stock portraits from Unsplash, repeated across some demo profiles and galleries. They do not identify the fictional names and are not real creator work. Remote images and Google Fonts require network access. Use approved local assets for launch. Actual video portfolios should be added as a dedicated typed media model with poster images and lazy-loaded video players; this starter does not present stock photographs as videos.

## Next phase

This is a deployed demonstration directory, not a marketplace. There is no database, administration interface, real inquiry delivery, sign-in or payment integration yet. Add a protected content-management interface and durable image storage when self-service editing is needed. The public app currently needs no secrets. Static export supports the present browser-based functionality; future server endpoints will require a different hosting configuration.

English is the initial interface language. Creator spoken languages are separate structured fields; Bosnian/German interface translations and SEO landing pages remain future work. All rights, package terms and availability currently displayed are demo proposals, not live contractual offers.
