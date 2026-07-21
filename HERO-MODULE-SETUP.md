# Hero Scroller — placing the dynamic module

The rotating hero on the Home page is driven by a **module**, so you (or any volunteer)
change the slides by editing **Articles** — no template/HTML editing. The slider *layout*
and the rotate/arrows/dots JavaScript already ship inside this template; you just have to
create the module and point it at a category.

> Until you place this module, the template shows a built-in 3-slide placeholder hero so the
> Home page is never blank. **The moment this module is published, it replaces the placeholder.**

---

## What each part of a slide comes from

| Slide element | Comes from the Article |
|---|---|
| Background photo | **Intro Image** (article → *Images and Links* tab → *Intro Image*) |
| Headline (big) | Article **Title** |
| Paragraph | Article **Intro Text** (keep it to ~1–2 sentences) |
| Button link | a custom field named **url** *(optional)* — otherwise the **article itself** (full view) |
| Small label above headline *(optional)* | a custom field named **badge** — otherwise the article's **category name** |
| Button text *(optional)* | a custom field named **cta** — otherwise "Read More" |

**How the button linking works — the important mental model:** each slide's article is
normally *both* the slide *and* the destination. The intro text (above the "Read More"
break) is the blurb on the slide; everything **below** the break is the page visitors land
on when they click the button. So "the page you want" is usually just… the rest of that
same article. Only reach for the **url** custom field when the button should go somewhere
*else* — an existing menu page, another article, or an external signup form. It accepts:

- a full URL — `https://forms.example.com/signup`
- a site path — `/membership`
- a Joomla link — `index.php?Itemid=123` (find the Itemid in **Menus** → the item's ID column)

---

## Step 1 — make a category + the slide articles
1. **Content → Categories → New** → title **`Home Hero`** → Save.
2. **Content → Articles → New** — one article per slide (make 3):
   - **Title** = the headline (e.g. *Friday Night Learn-to-Curl*).
   - **Category** = Home Hero.
   - **Images and Links** tab → **Intro Image** → select/upload a wide photo (1600×900+).
   - **Intro text** = the one- or two-line blurb.
   - Save. (Repeat for Bonspiel, Open House, etc.)

## Step 2 — create the hero module
1. **Extensions → Modules → New → "Articles – Newsflash".**
   *(Modules live under the* ***Extensions*** *menu in Joomla 3/4/5 — not "Content".)*
2. **Title:** `Home Hero` · **Show Title: No.**
3. **Module tab:**
   - **Category:** Home Hero
   - **# of Articles:** 3
   - **Show Article Text:** Show *(this is what feeds the blurb)*
   - **Show Images in article content / Image:** you can leave defaults — the layout reads the Intro Image itself.
4. **Advanced tab → Alternative Layout → `slider`.**  ← this is the key step; it switches the
   module to our hero design. If you don't see "slider," the template isn't active yet, or the
   override file didn't install.
5. **Assignment tab → Position →** type/choose **`scroller`** *(this template's top hero slot).*
6. **Menu Assignment →** Only on the pages selected → **Home.**
7. **Publish.** Reload Home — the live slideshow replaces the placeholder.

## Step 3 (optional) — badge, custom button text, custom button link
Only if you want the little label above the headline, custom button wording, or a button
that goes somewhere other than the slide's own article:
1. **Content → Fields → New** (context = *Articles*) → type **Text**, **Name** `badge`, Save.
   Repeat for one named `cta`, and one named `url`.
2. Open each hero article → fill **badge** (e.g. *Every Friday Night*), **cta**
   (e.g. *Reserve Your Spot*), and **url** (e.g. `/membership`, a full `https://…`
   address, or `index.php?Itemid=123`). Leave any of them blank to use the fallbacks:
   category name / "Read More" / the article itself.
   - **Want no badge at all** (not even the category name)? Type the word **`none`** into
     the badge field. A truly blank field always falls back to the category name — `none`
     is the explicit "show nothing" signal.

---

## Troubleshooting
- **Placeholder hero still showing** → the module isn't in position `scroller`, isn't assigned
  to **Home**, or isn't Published.
- **Nothing shows at all — not even the placeholder** → this is different from the above: the
  module IS being picked up (so the placeholder correctly steps aside), but its article query
  returned **zero articles**, so the slider renders nothing. Check the article(s) in the
  module's category: **Status = Published** (not Unpublished/Archived/Expired), **Category**
  matches exactly what the module points to (watch for a similarly-named duplicate category),
  **Access = Public**, and **Start Publishing** isn't a future date. Fastest check:
  **Content → Articles**, filter by the hero category — if nothing's listed, that's the cause.
- **Slides show but no photos** → those articles have no **Intro Image** set (Images and Links tab).
- **"slider" not in the Alternative Layout list** → make sure cccc2026 is your active template
  and the file `templates/cccc2026/html/mod_articles_news/slider.php` exists (it ships with the template).
- **Only one slide / no rotation** → you need **2+ published articles** in the Home Hero category;
  the arrows/dots/auto-advance only appear with more than one.
- **Button goes to the wrong place** → check the article's **url** custom field: blank means
  "open this article"; anything else is used as-is. Custom fields only feed the slider when
  the module's **Show Article Text** is on (that's also what feeds the blurb).
