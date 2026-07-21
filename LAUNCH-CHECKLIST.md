# cccc2026 — Production Launch Checklist

A one-page runbook for putting this template live and keeping it healthy.
(Full docs: `README.md`. Future Joomla 5 move: `MIGRATION-J5.md`.)

---

## 1. Before you touch production

- [ ] **Full backup** (Akeeba or host tool) — site + database.
- [ ] Know your rollback: *Extensions → Templates → Styles* → star your old template. That's it — switching back is instant and loses nothing.

## 2. Install / update the template

**As of v1.3.6, updates should be one click:** *Extensions → Update → Find Updates* — if
"Circle City Curling Template" shows an update available, select it and click **Update**.
This uses Joomla's own update-server mechanism (github.com/Fhajad/circlecity-curling-template)
and does **not** wipe your Options. Try this first on every future version.

**If that doesn't show an update, or a fresh v1.3.6 install still throws "Another template
is already using the named folder"** — this site's installer has a separate, confirmed
record-matching bug that even a clean reinstall didn't fix (see project notes). Fall back to
uninstall → reinstall, lossless-ish:

1. [ ] **Jot down your Options** (*Templates → Styles → cccc2026 → Options*): logo path(s),
   any colour changes, tagline/copyright text, login URL, Display toggles. A screenshot of
   the Options tabs is fastest. *(These are the ONLY things the dance loses. Articles,
   menus, modules and their position assignments all survive untouched.)*
2. [ ] *Templates → Styles* → star a different template as default (site shows it briefly).
3. [ ] *Extensions → Manage* → search cccc2026 → tick → **Uninstall**.
4. [ ] *Extensions → Manage → Install → Upload Package File* → the new
   `cccc2026-template-x.y.z.zip`.
5. [ ] *Templates → Styles* → star **cccc2026** as default again.
6. [ ] Re-enter the Options from step 1 → Save → reload the front end.
7. [ ] Confirm *Extensions → Manage* shows the expected version.

> Tip: the package can ship your logo/colour/text choices as **built-in defaults**, which
> makes step 6 disappear — a fresh install comes up already configured. Worth doing once
> your Options settle.

## 3. Configure the Options panel

*Extensions → Templates → Styles → cccc2026 → Options*

- [ ] **Branding:** upload the club logo (transparent PNG/SVG — a white-box logo will glow in dark mode), set club name / tagline / copyright / login URL.
- [ ] **Colours:** leave at defaults unless rebranding.
- [ ] **Display:**
  - **Interior page-title banner** — Hide it if your articles show their own titles (stops "Contact Us" appearing twice). OR keep it and hide article titles per menu item (*menu item → Options → Show Title: Hide*).
  - **Demo hero/promo/news** — set all three to **Hide** unless you've built the real home modules. ⚠️ The demo hero's buttons link to pages that don't exist (`/learn-to-curl` etc.) — don't leave it showing on production.

## 4. Content settings worth setting once

- [ ] *Content → Articles → Options → Show Icons: **No*** — belt-and-braces for the edit/print/email links (the template styles them anyway).
- [ ] *Extensions → Templates → Options → Preview Module Positions: **Enabled*** — then `yoursite.com/?tp=1` overlays every position name on the page. Your best friend for "which position was that?"

## 5. Smoke test (10 minutes, on the live site)

Desktop **and** a phone; if your device supports it, flip dark mode once too.

- [ ] **Home** — hero rotates (or is hidden), promo cards, news list, footer columns.
- [ ] **An article page** — title appears once, sidebar wells styled, no red edit-box (log in to check the Edit/Print/Email text links).
- [ ] **A category/blog page** — article rows, read-more links, pagination pills.
- [ ] **Contact page** — form fields styled, submit button red.
- [ ] **Login/registration** (com_users) — fields + validation messages styled.
- [ ] **Members / Community Builder pages** — walk registration, profile, members-only pages. CB ships its own CSS, so this is the most likely place to find rough edges. Screenshot anything odd.
- [ ] **Menu on a phone** — hamburger opens, submenus tap open, every child page reachable.
- [ ] **A missing URL** — styled 404 page appears.
- [ ] **Print preview** (Ctrl+P) on the contact page — chrome stripped, black on white.

## 6. Module positions quick reference

| Want to add… | Put a module in… |
|---|---|
| Site-wide announcement strip (top) | `notice` |
| Band under the home hero | `feature` |
| Sponsor logo strip (all pages) | `sponsors` |
| Call-to-action band above footer | `above-footer` |
| Home hero / cards / news | `scroller` / `promo` / `news` |
| Interior sidebar | `right` |
| Footer columns / bar | `bottom1–4` / `footer` / `footer-menu` |

All optional positions render **nothing** until a module is assigned — empty ones are invisible.

## 7. Known behaviours (not bugs)

- **Dark mode is automatic** — follows each visitor's device setting. There is no toggle button by design.
- **The page-title banner shows the *menu item's* title**, not the article title. Pages reached without a menu item (e.g. search results) show the banner only if Joomla resolves an active menu item.
- **Editor-only controls** (Edit/Print/Email links, Admin Menu module) are invisible to regular visitors — check logged out if unsure what the public sees.
- **`?tp=1` only outlines positions that render on that page** — empty positions don't draw boxes (the template deliberately skips them).
