# FRAME — UGC talent directory

Next.js / React / TypeScript starter for an agency-managed creator and editor directory.

## Run locally

```sh
npm install
npm run dev
```

Open http://127.0.0.1:3000. Production verification: `npm run build`. Type checking: `npm run typecheck`.

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

This is a local working prototype, not a deployed marketplace. There is no database, administration interface, real inquiry delivery, sign-in or payment integration yet. Those require a hosting and data-storage choice. Add a protected content-management interface and durable image storage when self-service editing is needed. The public app currently needs no secrets.

English is the initial interface language. Creator spoken languages are separate structured fields; Bosnian/German interface translations and SEO landing pages remain future work. All rights, package terms and availability currently displayed are demo proposals, not live contractual offers.
