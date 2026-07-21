# Circle City Curling — Joomla 3.x Template

A front-end template that reproduces the approved homepage mockup, with the **home scroller** and **News & Announcements** driven entirely by Joomla Articles + modules. **No HTML editing is required to update content** — staff just write Articles.

> **What's new in 1.3.0**
> - **Every page type is styled** — blog/category/list pages, login/registration/contact forms, search results, tags and pagination now match the brand (not just single articles).
> - **Tidied edit buttons** — the empty red box logged-in editors saw on articles (Joomla's edit/print/email controls) now renders as plain text links.
> - **Interior banner toggle** — a Display option to hide the page-title bar site-wide if your articles already show their own title (stops the title appearing twice).
> - **Four optional module positions** — `notice`, `feature`, `sponsors`, `above-footer` — add new sections with modules, no HTML editing.
> - **Self-hosted fonts + print stylesheet** — no Google Fonts CDN (faster, privacy-friendly); pages print cleanly.
>
> **From 1.2.0**
> - **Automatic dark theme** following the device day/night setting, and **fixed interior pages on mobile** (content + sidebar stack instead of squeezing).
>
> **From 1.1.0**
> - **Template Options panel** — set your **logo**, **brand colours**, **club name / tagline / copyright**, and demo-block visibility from the admin, no file editing. See *[Template Options](#template-options-no-code-customisation)* below.
> - **Mobile/touch navigation** — the menu collapses to a hamburger and dropdowns are tap-to-open, so child pages work on phones and tablets.
> - **Accessibility** — skip-to-content link, keyboard-operable menus with visible focus, the hero respects "reduce motion," and a single `<h1>` per page.
> - **Polish** — directory-listing guards, a favicon, a Template-Manager preview image, and a J5 migration checklist (`MIGRATION-J5.md`).

---

## How content flows (the important part)

| Homepage region | Joomla source | How staff update it |
|---|---|---|
| **Top scroller** | *Articles – Newsflash* module → **Featured Articles** category, limited to **3** | Write an article, set its category to *Featured Articles*, add an intro image. Newest 3 show. |
| **News & Announcements** | *Articles – Category* module → **News** category, **Count = 3** | Write an article, set its category to *News*. Newest 3 show; older ones drop off automatically. |
| Main nav | *Menu* module (`mod_menu`) in position `menu` | Menus → manage as usual |
| 3 promo cards | Static in `index.php`, **or** Custom HTML modules in position `promo` | Optional |

> You never edit the template to change articles. Add/edit/unpublish Articles in **Content → Articles** and the homepage updates itself.

---

## Install (5 minutes)

You were given a ready-to-upload package: **`cccc2026-template.zip`**. You do **not** need to re-zip anything.

1. Log into your Joomla admin (`/administrator`).
2. Go to **Extensions → Manage → Install**.
3. On the **Upload Package File** tab, drag in **`cccc2026-template.zip`** (or click *Browse* and select it).
4. Wait for the green **"Installation of the template was successful"** message.
5. Go to **Extensions → Templates → Styles**.
6. Find **cccc2026** in the list, click the **star** in the *Default* column to make it your site's default template.
7. Visit your site's front end — you'll see the new header, nav, and footer. The scroller and News list will be empty until you create their modules (next section).

> **Backup first.** Before switching the default template on a live site, take a full site + database backup (Akeeba Backup or your host's tool). You can always switch the default back to your old template from the same Styles screen.

---

## One-time setup

### 1. Create the two categories
**Content → Categories → New**, create:
- **Featured Articles**
- **News**

### 2. Build the scroller module
**Extensions → Modules → New → Articles – Newsflash**
- **Position:** `scroller`
- **Category:** Featured Articles
- **# of Articles:** `3`
- **Advanced tab → Alternative Layout:** **slider**  ← this is what makes it look like the mockup
- **Images and Links → Image Floated / Intro image** is read automatically as the slide background (set each article's **Intro Image** under the article's *Images and Links* tab).
- Menu Assignment: *Only on the pages → Home*

### 3. Build the News module
**Extensions → Modules → New → Articles – Category**
- **Position:** `news`
- **Category:** News
- **Count:** `3`
- **Ordering:** *Most recent first* (Article Order → Date, Descending)
- **Advanced tab → Alternative Layout:** **news**
- Menu Assignment: *Only on the pages → Home*

### 4. Main menu
Point your existing Main Menu module to position `menu` (style is handled by the template). If you skip this, the template shows a static fallback nav. The header, nav, and footer appear on **every** page automatically.

---

## Interior pages (every page that isn't Home)

The template renders in **two modes** from the same `index.php`:

- **Front page** (your Home menu item) → hero scroller + promo cards + News list.
- **Every other page** (articles, categories, Contact, search, login, etc.) → a **page-title banner** + a **content column** with an optional **right sidebar**. No scroller or News block — just the page's own content, styled to match the brand (headings, lists, tables, quotes, buttons are all themed).

**You don't configure anything for this** — any menu item that isn't Home uses interior mode automatically. The page banner shows the active menu item's title with a breadcrumb trail underneath.

**Two optional interior positions:**

| Position | What it's for | How |
|---|---|---|
| `masthead` | Breadcrumb trail in the page banner | Publish a **Breadcrumbs** module (`mod_breadcrumbs`) to position `masthead`. If absent, the banner just shows the title. |
| `right` | Right-hand column on interior pages | Publish any module(s) — *Menu*, *Custom HTML*, *Latest Articles*, etc. — to position `right`. Each becomes a titled white "well" card. With nothing assigned, the content column simply runs full-width. |

> Position names match the existing Circle City site, so your current `masthead` (breadcrumbs) and `right`-column modules (Member Login, League Schedules, Upcoming Events, …) appear with no reassignment.

> See **`Circle City Curling - Interior Page.dc.html`** in the project for a visual mock of a typical interior page (the Membership page, with sidebar).

---

## Optional: badge, button text, and button link on scroller slides

The mockup slides have a small **badge** ("Every Friday Night") and a custom **button label** ("Reserve Your Spot"). You can also point the button at a page other than the slide's own article. None of these are native article fields, so the slider layout reads three optional **Custom Fields**:

**Content → Fields → New** (make sure context = *Articles*):
- Name **`badge`** — type *Text* — shows as the small label above the headline.
- Name **`cta`** — type *Text* — becomes the button text.
- Name **`url`** — type *Text* — where the button goes: a full URL, a site path (`/membership`), or `index.php?Itemid=123`.

Assign them to the **Featured Articles** category (Fields → Options, or per-group). Then each article can set its own badge, button text, and button link.

**Fallbacks if you skip this:** badge → the article's category title; button text → "Read More"; button link → the article itself. The blurb under the headline is the article's **intro text**.

Want no badge at all — not even the category title? A truly blank badge field always falls back to the category name, so type the word **`none`** into it to explicitly suppress the badge on that slide.

---

## The footer (module-driven)

The footer is built entirely from modules, matching the existing site's position names — so your current footer modules appear with **no reassignment**:

| Footer region | Position | Your module |
|---|---|---|
| Column 1 | `bottom1` | Contact Info |
| Column 2 | `bottom2` | Ice Location |
| Column 3 | `bottom3` | Follow Us |
| Column 4 | `bottom4` | About our Club |
| Bottom bar (left) | `footer` | Footer copyrights |
| Bottom bar (right) | `footer-menu` | Footer menu |

The `bottom1`–`bottom4` columns use the `ccfootcol` chrome (module title becomes a small heading; content below, styled light for the dark footer). The columns auto-fit, so 2, 3, or 4 modules all lay out cleanly. With no footer modules assigned, a brand block + setup hint shows as a fallback.

---

## Template Options (no-code customisation)

Go to **Extensions → Templates → Styles → cccc2026 → Options** (the *Options* tab). Everything here is saved without touching any file; leave a field blank to use its built-in default.

**Branding**
- **Header logo** — click *Select* and upload/choose an image (transparent PNG or SVG works best; it's sized to ~54px tall). This replaces the CSS-drawn roundel in the top-left. Clear the field to go back to the roundel.
- **Footer logo** — optional separate logo for the dark footer. If empty, the header logo is reused; if that's also empty, the roundel shows.
- **Club name** — the text beside the logo (header + footer).
- **Header tagline** — the small line under the club name.
- **Footer copyright line** — the bottom-bar text. Leave blank to auto-build "© {year} {club name}. All rights reserved." (A module in the `footer` position overrides this.)
- **Member login URL** — where the *Member Login* button points.

**Colours** — four colour pickers that recolour the whole site live:
- **Primary (red)** + its **hover/dark** shade → buttons, the login button, accents.
- **Accent (blue)** + its **dark** shade → the nav bar, headings, links, and the footer background.

**Display**
- **Interior page-title banner** — Show/Hide the styled title bar (page title + breadcrumb) at the top of every non-home page. Set to **Hide** if your articles/menu items already show their own title, so it doesn't appear twice.
- **Demo blocks** — Show/Hide switches for the three built-in demo blocks (hero slider, promo cards, news row). These only ever appear when their module position is empty; turn one **off** to leave that area blank until your real module is live.

> Click **Save**, then reload the front end to see changes.

---

## Module positions — where things go (cheat-sheet)

Joomla puts modules into named **positions**; this template's positions and the region each one fills:

| Position | Where it appears | Typical module |
|---|---|---|
| `notice` | Slim strip across the very top, every page | *Custom HTML* (announcement) |
| `menu` | Main nav bar (optional — auto-builds from your menu if empty) | *Menu* |
| `scroller` | Home hero slider | *Articles – Newsflash* (slider layout) |
| `feature` | Home full-width band under the hero | *Custom HTML* |
| `promo` | Home promo cards row | *Custom HTML* ×3 |
| `news` | Home News & Announcements list | *Articles – Category* (news layout) |
| `masthead` | Breadcrumb under the interior page title | *Breadcrumbs* |
| `right` | Interior right sidebar (white "well" cards) | any module(s) |
| `sponsors` | Logo strip above the footer, every page | *Custom HTML* (logos) |
| `above-footer` | Coloured call-to-action band above the footer, every page | *Custom HTML* |
| `bottom1`–`bottom4` | Footer columns | *Custom HTML* |
| `footer` | Footer bottom-bar, left (copyright) | *Custom HTML* / *Footer* |
| `footer-menu` | Footer bottom-bar, right | *Menu* |

> The new `notice`, `feature`, `sponsors`, and `above-footer` positions are **optional** — each only appears when you assign a module to it, so they stay invisible until you want them.

**Can't remember which is which?** Joomla can label them for you, right on the page:
1. **Extensions → Templates → Options** → set **Preview Module Positions: Enabled** → *Save*.
2. Visit your front end and add **`?tp=1`** to the URL (e.g. `https://yoursite.org/?tp=1`).
3. Joomla draws an outline + the position name everywhere a position renders. Remove `?tp=1` to turn it off. (This works the same in Joomla 3.10 and Joomla 5.)

---

## Files

```
templateDetails.xml          Template manifest + positions + params
index.php                    Homepage layout (header, nav, scroller, promo, news, footer)
component.php                Bare layout for print/component views
error.php                    Styled 404 page
css/template.css             All styling (mockup + component coverage, dark theme, print)
js/scroller.js               Scroller behaviour (fade, auto-advance, arrows, dots)
js/nav.js                    Mobile hamburger + tap-to-open dropdowns
fonts/                       Self-hosted Barlow web fonts (woff2) — no Google CDN
favicon.ico                  Browser tab icon (club roundel)
html/mod_articles_news/slider.php       Scroller layout override  ← article -> slide
html/mod_articles_category/news.php     News layout override       ← article -> date-chip row
html/modules.php             Module chrome: "ccpromo" (promo cards), "ccwell" (sidebar wells), "ccfootcol" (footer columns)
images/template_thumbnail.png, template_preview.png   Template-Manager preview
language/en-GB/...           Admin strings
MIGRATION-J5.md              Checklist for the future Joomla 5 upgrade
```

To restyle a region, edit `css/template.css`. To change *what* the scroller/news shows, change the **module settings** or the **Articles** — not the template.

---

## Notes

- Built for **Joomla 3.x**. The override folders use Joomla 3 layout conventions; a future Joomla 4/5 migration needs light updates — see **`MIGRATION-J5.md`** for the full checklist. The template has no Bootstrap/jQuery dependency, which makes that move unusually small.
- The club logo defaults to a CSS-drawn roundel (`.cc-logo`) — no image needed — but you can upload your real logo any time under **Options → Branding** (see [Template Options](#template-options-no-code-customisation)).
- `Circle City Curling - Homepage.dc.html` (in the project root) is the **visual reference mockup**, not part of the installable template.
