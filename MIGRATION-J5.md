# Migrating cccc2026 to Joomla 5

This template currently targets **Joomla 3.10**. When the site moves to **Joomla 5**,
the template needs a handful of updates. The good news: it uses **no Bootstrap and no
jQuery** — it's self-contained CSS/JS — so this is a much smaller job than migrating a
typical Joomla template.

**Do all of this on a staging copy, never the live site.** Keep the J3.10 package
(`cccc2026-template-1.1.0.zip`) shippable on a separate branch until the J5 build is verified.

---

## 1. Namespaced API calls

Joomla 4/5 removed the old `J*` class aliases. Replace them (all are in `index.php`,
with a couple in the overrides):

| Joomla 3 (now) | Joomla 5 |
|---|---|
| `JFactory::getApplication()` / `getDocument()` | `Joomla\CMS\Factory::getApplication()` / `getDocument()` |
| `JRoute::_()` | `Joomla\CMS\Router\Route::_()` |
| `JHtml::_()` | `Joomla\CMS\HTML\HTMLHelper::_()` |
| `JUri::root()` | `Joomla\CMS\Uri\Uri::root()` |
| `FieldsHelper` (slider override) | `Joomla\Component\Fields\Administrator\Helper\FieldsHelper` |

`error.php` already avoids the removed `JError` API, so it needs only the `JRoute`→`Route`
swap.

## 2. Module chrome → chrome layouts

`html/modules.php` defines `modChrome_ccpromo`, `modChrome_ccwell`, and `modChrome_ccfootcol`.
Function-based chrome is **removed in Joomla 4/5**. Re-implement each as a chrome **layout**:

- Create `html/layouts/chrome/ccpromo.php`, `ccwell.php`, `ccfootcol.php`.
- Move the markup from each `modChrome_*` function into the matching layout file (the layout
  receives `$module`, `$params`, `$attribs`).
- The `style="ccpromo"` / `style="ccwell"` / `style="ccfootcol"` attributes in `index.php`
  stay exactly as they are — only the mechanism behind them changes.
- Then delete `html/modules.php`.

## 3. Assets → media folder + asset registration (recommended)

Joomla 4/5 prefers template assets under `media/templates/site/cccc2026/` declared in a
`joomla.asset.json`, loaded with the `WebAssetManager` instead of
`$doc->addStyleSheet()` / `$doc->addScript()`. This is optional (the direct calls still
work) but is the modern, cache-friendly approach. If you move `css/`, `js/`, `fonts/`, and
`images/`, update the paths in `index.php`, `component.php`, `error.php`, and the
`@font-face` / `url(../fonts/…)` references in `css/template.css`.

## 4. Options panel — already compatible

The `<config>` fields added in 1.1.0 (Branding / Colours / Display) carry over unchanged.
One detail is already handled: the Joomla 4/5 `media` field returns a value like
`images/logo.png#joomlaImage://local-images/logo.png?width=...`. The `ccMediaUrl()` helper
in `index.php` strips the `#…` wrapper and any query string, so the logo works on both
Joomla 3.10 and 5 with no change.

## 5. Manifest & positions

- Confirm `templateDetails.xml` validates against the J4/5 schema; bump `<version>`.
- The `<positions>` and `<config>` structures are unchanged between J3 and J5.

## 6. Overrides — verify variable names

- `html/mod_articles_news/slider.php` and `html/mod_articles_category/news.php` rely on
  `$list`, `$item->images`, `$item->jcfields`, `$item->introtext`, `$item->link`. These are
  stable in J5's `mod_articles_news` / `mod_articles_category`, but re-test after upgrade —
  override variables are the most common thing to drift.

## 7. PHP 8.x

Joomla 5 requires PHP 8.1+. The current code already looks 8-safe (guarded `json_decode`,
no `each()`, no `create_function`). Re-test with errors visible on staging.

---

## Suggested order

1. Stand up a Joomla 5 staging site; install the template package as-is and note what breaks.
2. Fix the namespaced API calls (item 1) — clears the fatal errors.
3. Convert the chrome (item 2) — restores promo/sidebar/footer styling.
4. Re-test overrides + Options panel (items 4, 6) and a forced 404.
5. (Optional) Move to the media/asset structure (item 3).
6. Walk every page type on a phone and desktop before going live.
