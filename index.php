<?php
/**
 * Circle City Curling — Joomla 3.x site template
 *
 * This file renders EVERY page on the site. It branches into two modes:
 *
 *   FRONT PAGE  (the Home menu item)  -> hero scroller + promo cards + News list
 *   ALL OTHER PAGES (About, Contact, articles, categories, search results, …)
 *                                     -> page-title bar + main content column + optional sidebar
 *
 * Both modes share the header, nav, and footer so the site is consistent.
 *
 * Logo, brand colours, key text and the demo-block toggles are all set from the
 * template's Options panel (Extensions -> Templates -> Styles -> cccc2026 -> Options).
 *
 * Module positions (named to match the existing site so current modules drop in):
 *   menu        mod_menu (main navigation)
 *   scroller    mod_articles_news,    layout "slider"  (front page only)
 *   promo       3x Custom HTML,       chrome "ccpromo"  (front page only)
 *   news        mod_articles_category,layout "news"     (front page only)
 *   masthead    mod_breadcrumbs       (interior pages, page banner)
 *   right       any modules           (interior pages, right column, chrome "ccwell")
 *   bottom1-4   Custom HTML           (footer columns, chrome "ccfootcol")
 *   footer      Footer / copyright    (footer bottom bar)
 *   footer-menu mod_menu              (footer bottom bar)
 */
defined('_JEXEC') or die;

$app  = JFactory::getApplication();
$doc  = JFactory::getDocument();
$menu = $app->getMenu();
$tpl  = $this->baseurl . '/templates/' . $this->template;

$this->setGenerator(null);
$this->language  = $doc->language;
$this->direction = $doc->direction;

// ----- Are we on the front page? (Home menu item) -----
$active = $menu->getActive();
$isHome = ($active && $active === $menu->getDefault($this->language)) || ($active && $active === $menu->getDefault('*'));

// Page title for the interior banner (the active menu item's title)
$pageHeading = $active ? $active->title : '';

// ----- Template params (set in the Options panel) -----
$brandName    = $this->params->get('brandName', 'Circle City Curling');
$brandNameEsc = htmlspecialchars($brandName, ENT_QUOTES, 'UTF-8');
$tagline      = htmlspecialchars($this->params->get('tagline', 'Indianapolis&#8217;s home for curling since 2015'), ENT_QUOTES, 'UTF-8');
$loginUrl     = $this->params->get('loginUrl', 'index.php?option=com_users&view=login');
$footerCopy   = trim((string) $this->params->get('footerCopy', ''));

// Demo-block visibility (shown only when the matching module position is empty)
$showHero  = $this->params->get('showPlaceholderHero', 1);
$showPromo = $this->params->get('showPlaceholderPromo', 1);
$showNews  = $this->params->get('showPlaceholderNews', 1);

// Interior page-title banner (set "Hide" if your articles already show their own title)
$showBanner = $this->params->get('showPageBanner', 1);

// Resolve a media-field value (logo) to a usable URL. Robust across Joomla 3.10
// (plain "images/foo.png") and 4/5 ("images/foo.png#joomlaImage://...?width=..").
if (!function_exists('ccMediaUrl')) {
	function ccMediaUrl($val, $baseurl) {
		if (!$val) { return ''; }
		$p = explode('#', (string) $val, 2);   // drop J4/5 #joomlaImage wrapper
		$p = explode('?', $p[0], 2);            // drop any query string
		$p = trim($p[0]);
		if ($p === '') { return ''; }
		if (preg_match('#^(https?:)?//#i', $p)) { return $p; }     // already absolute
		return rtrim($baseurl, '/') . '/' . ltrim($p, '/');
	}
}
$logoUrl       = ccMediaUrl($this->params->get('logo'), $this->baseurl);
$footerLogoUrl = ccMediaUrl($this->params->get('logoFooter'), $this->baseurl);
if ($footerLogoUrl === '') { $footerLogoUrl = $logoUrl; }

// Assets (fonts are self-hosted via @font-face in template.css — no external CDN)
$doc->addStyleSheet($tpl . '/css/template.css');
$doc->addScript($tpl . '/js/scroller.js');
$doc->addScript($tpl . '/js/nav.js');

// ----- Brand colour overrides (Options -> Colours) -----
// Each maps onto the CSS variable that drives that colour; only valid #hex values
// are emitted, so a blank/odd value safely falls back to the stylesheet default.
$ccColorMap = array(
	'--cc-red'       => 'colorPrimary',
	'--cc-red-dark'  => 'colorPrimaryDark',
	'--cc-blue'      => 'colorAccent',
	'--cc-blue-dark' => 'colorAccentDark',
	'--cc-nav-bg'    => 'colorAccent',
	'--cc-footer-bg' => 'colorAccentDark',
);
$ccOverrides = '';
foreach ($ccColorMap as $var => $param) {
	$val = trim((string) $this->params->get($param, ''));
	if ($val !== '' && preg_match('/^#[0-9a-fA-F]{3,8}$/', $val)) {
		$ccOverrides .= $var . ':' . $val . ';';
	}
}
if ($ccOverrides !== '') {
	$doc->addStyleDeclaration(':root{' . $ccOverrides . '}');
}

// Which positions have modules assigned to this page
$hasMenu        = $this->countModules('menu');
$hasScroller    = $this->countModules('scroller');
$hasPromo       = $this->countModules('promo');
$hasNews        = $this->countModules('news');
$hasBreadcrumbs = $this->countModules('masthead');
$hasSidebar     = $this->countModules('right');
$hasFootCols    = $this->countModules('bottom1') || $this->countModules('bottom2') || $this->countModules('bottom3') || $this->countModules('bottom4');

// ----- Build the main navigation straight from the site's primary menu -----
// (the menu that owns the Home item) so the top nav works with NO module.
$navTree = array();
$defItem  = $menu->getDefault($this->language);
if (!$defItem) { $defItem = $menu->getDefault('*'); }
$navType  = ($defItem && isset($defItem->menutype)) ? $defItem->menutype : '';
if ($navType) {
	$allNav = $menu->getItems('menutype', $navType);
	if (is_array($allNav)) {
		// Index every item, then nest each under its real parent so the menu
		// works to ANY depth (top -> section heading -> link), not just 2 levels.
		$byId = array();
		foreach ($allNav as $mi) { $mi->ccKids = array(); $byId[$mi->id] = $mi; }
		foreach ($allNav as $mi) {
			$pid = isset($mi->parent_id) ? $mi->parent_id : 1;
			if ($pid == 1) { $navTree[] = $mi; }
			elseif (isset($byId[$pid])) { $byId[$pid]->ccKids[] = $mi; }
			// else: parent is hidden (e.g. behind login) -> drop the child too,
			// never promote it to the top level.
		}
	}
}
$activeId   = $active ? $active->id : 0;
$activeTree = ($active && isset($active->tree)) ? $active->tree : array();

// Resolve a menu item's href (null = non-link separator/heading).
if (!function_exists('ccNavHref')) {
	function ccNavHref($mi) {
		$type = isset($mi->type) ? $mi->type : 'component';
		if ($type === 'separator' || $type === 'heading') { return null; }
		if ($type === 'url') {
			$link = $mi->link;
			if (preg_match('#^(https?:)?//#i', $link)) { return $link; }
			return JRoute::_($link);
		}
		return JRoute::_('index.php?Itemid=' . $mi->id);
	}
}
// Escape a menu item title for safe output.
if (!function_exists('ccNavTitle')) {
	function ccNavTitle($mi) {
		return htmlspecialchars((string) $mi->title, ENT_QUOTES, 'UTF-8');
	}
}
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="head" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="cc-site <?php echo $isHome ? 'cc-is-home' : 'cc-is-interior'; ?>">

	<a class="cc-skip" href="#content">Skip to content</a>

	<!-- ===== NOTICE (optional site-wide strip above the header) ===== -->
	<?php if ($this->countModules('notice')) : ?>
		<div class="cc-notice"><div class="cc-wrap"><jdoc:include type="modules" name="notice" style="none" /></div></div>
	<?php endif; ?>

	<!-- ===== HEADER ===== -->
	<header class="cc-header">
		<div class="cc-wrap cc-header-inner">
			<a class="cc-brand" href="<?php echo $this->baseurl; ?>/">
				<?php if ($logoUrl !== '') : ?>
					<img class="cc-logo-img" src="<?php echo htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo $brandNameEsc; ?>">
				<?php else : ?>
					<span class="cc-logo" aria-hidden="true"></span>
				<?php endif; ?>
				<span class="cc-brand-text">
					<span class="cc-brand-name"><?php echo $brandNameEsc; ?></span>
					<span class="cc-brand-tag"><?php echo $tagline; ?></span>
				</span>
			</a>
			<a class="cc-login" href="<?php echo JRoute::_($loginUrl); ?>">
				<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"></path><path d="M4 21a8 8 0 0 1 16 0"></path></svg>
				Member Login
			</a>
		</div>
	</header>

	<!-- ===== NAV (auto from the site's main menu; a "menu" module overrides; static list as last resort) ===== -->
	<nav class="cc-nav">
		<div class="cc-wrap">
			<button class="cc-nav-toggle" type="button" aria-controls="cc-primary-nav" aria-expanded="false" aria-label="Toggle navigation menu">
				<span class="cc-nav-toggle-bar"></span>
				<span class="cc-nav-toggle-bar"></span>
				<span class="cc-nav-toggle-bar"></span>
			</button>
			<div class="cc-nav-panel" id="cc-primary-nav">
			<?php if ($hasMenu) : ?>
				<jdoc:include type="modules" name="menu" style="none" />
			<?php elseif (!empty($navTree)) : ?>
				<ul class="menu">
					<?php foreach ($navTree as $item) :
						$kids    = $item->ccKids;
						$hasKids = !empty($kids);
						$isMega  = false;
						foreach ($kids as $k) { if (!empty($k->ccKids)) { $isMega = true; break; } }
						$cls = array();
						if ($item->id == $activeId)        { $cls[] = 'current'; }
						if (in_array($item->id, $activeTree)) { $cls[] = 'active'; }
						if ($hasKids) { $cls[] = 'has-children'; }
						if ($isMega)  { $cls[] = 'mega'; }
						$href = ccNavHref($item);
						$tgt  = (isset($item->browserNav) && $item->browserNav == 1) ? ' target="_blank" rel="noopener"' : '';
					?>
						<li class="<?php echo implode(' ', $cls); ?>">
							<?php if ($href === null) : ?>
								<span><?php echo ccNavTitle($item); ?></span>
							<?php else : ?>
								<a href="<?php echo $href; ?>"<?php echo $tgt; ?>><?php echo ccNavTitle($item); ?></a>
							<?php endif; ?>

							<?php if ($hasKids && $isMega) : // section headings -> columns of links ?>
								<div class="cc-mega"><div class="cc-mega-inner">
									<?php foreach ($kids as $col) :
										$colHref = ccNavHref($col);
										$grand   = $col->ccKids;
									?>
										<div class="cc-mega-col">
											<?php if ($colHref === null) : ?>
												<span class="cc-mega-h"><?php echo ccNavTitle($col); ?></span>
											<?php else : ?>
												<a class="cc-mega-h" href="<?php echo $colHref; ?>"><?php echo ccNavTitle($col); ?></a>
											<?php endif; ?>
											<?php if (!empty($grand)) : ?>
												<ul>
													<?php foreach ($grand as $g) :
														$gh = ccNavHref($g);
														$gt = (isset($g->browserNav) && $g->browserNav == 1) ? ' target="_blank" rel="noopener"' : '';
													?>
														<li><?php if ($gh === null) : ?><span><?php echo ccNavTitle($g); ?></span><?php else : ?><a href="<?php echo $gh; ?>"<?php echo $gt; ?>><?php echo ccNavTitle($g); ?></a><?php endif; ?></li>
													<?php endforeach; ?>
												</ul>
											<?php endif; ?>
										</div>
									<?php endforeach; ?>
								</div></div>
							<?php elseif ($hasKids) : // single level of children -> simple dropdown ?>
								<ul class="cc-submenu">
									<?php foreach ($kids as $k) :
										$kh = ccNavHref($k);
										$kt = (isset($k->browserNav) && $k->browserNav == 1) ? ' target="_blank" rel="noopener"' : '';
									?>
										<li><?php if ($kh === null) : ?><span><?php echo ccNavTitle($k); ?></span><?php else : ?><a href="<?php echo $kh; ?>"<?php echo $kt; ?>><?php echo ccNavTitle($k); ?></a><?php endif; ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<ul class="menu">
					<li class="current active"><a href="<?php echo $this->baseurl; ?>/">Home</a></li>
				</ul>
			<?php endif; ?>
			</div>
		</div>
	</nav>

	<?php if ($isHome) : ?><a id="content" tabindex="-1"></a><?php endif; ?>

<?php if ($isHome) : // ============ FRONT PAGE ============ ?>

	<!-- ===== HERO SCROLLER (front page only) ===== -->
	<?php if ($hasScroller) : // dynamic: a mod_articles_news in position "scroller" (slider layout) ?>
		<jdoc:include type="modules" name="scroller" style="none" />
	<?php elseif ($showHero) : // built-in: designed 3-slide hero, no module needed. Drop photos at /images/hero/. ?>
		<section class="cc-hero" data-cc-slider aria-roledescription="carousel" aria-label="Club highlights">
			<div class="cc-slide is-active" data-cc-slide>
				<div class="cc-slide-img" style="background-image:url('<?php echo $this->baseurl; ?>/images/hero/friday-ltc.jpg'), linear-gradient(135deg,#3d5a78,#2b3a4a);"></div>
				<div class="cc-slide-scrim"></div>
				<div class="cc-slide-inner cc-wrap"><div class="cc-slide-copy">
					<span class="cc-badge">Every Friday Night</span>
					<h2>Friday Night<br>Learn-to-Curl</h2>
					<div class="cc-slide-blurb">No experience, no equipment, no problem. Two hours on the ice with our coaches &mdash; you&rsquo;ll be throwing stones by the end.</div>
					<a class="cc-btn" href="<?php echo $this->baseurl; ?>/learn-to-curl">Reserve Your Spot <span aria-hidden="true">&rarr;</span></a>
				</div></div>
			</div>
			<div class="cc-slide" data-cc-slide>
				<div class="cc-slide-img" style="background-image:url('<?php echo $this->baseurl; ?>/images/hero/bonspiel.jpg'), linear-gradient(135deg,#3d5a78,#2b3a4a);"></div>
				<div class="cc-slide-scrim"></div>
				<div class="cc-slide-inner cc-wrap"><div class="cc-slide-copy">
					<span class="cc-badge">March 14&ndash;16, 2026</span>
					<h2>The Circle City<br>Bonspiel</h2>
					<div class="cc-slide-blurb">Three days, sixteen teams, one trophy. Open to all skill levels &mdash; round up a rink and join the most fun weekend on our calendar.</div>
					<a class="cc-btn" href="<?php echo $this->baseurl; ?>/bonspiel">Register Your Team <span aria-hidden="true">&rarr;</span></a>
				</div></div>
			</div>
			<div class="cc-slide" data-cc-slide>
				<div class="cc-slide-img" style="background-image:url('<?php echo $this->baseurl; ?>/images/hero/open-house.jpg'), linear-gradient(135deg,#3d5a78,#2b3a4a);"></div>
				<div class="cc-slide-scrim"></div>
				<div class="cc-slide-inner cc-wrap"><div class="cc-slide-copy">
					<span class="cc-badge">Free &amp; Open to All</span>
					<h2>Open House<br>on the Ice</h2>
					<div class="cc-slide-blurb">Curious about curling? Come watch, ask questions, and slide a stone for free. Bring the whole family &mdash; flat shoes are all you need.</div>
					<a class="cc-btn" href="<?php echo $this->baseurl; ?>/open-house">Find a Date <span aria-hidden="true">&rarr;</span></a>
				</div></div>
			</div>
			<button class="cc-arrow cc-arrow--prev" type="button" data-cc-prev aria-label="Previous">&#8249;</button>
			<button class="cc-arrow cc-arrow--next" type="button" data-cc-next aria-label="Next">&#8250;</button>
			<div class="cc-dots" data-cc-dots>
				<button class="cc-dot is-active" type="button" data-cc-dot="0" aria-label="Go to slide 1"></button>
				<button class="cc-dot" type="button" data-cc-dot="1" aria-label="Go to slide 2"></button>
				<button class="cc-dot" type="button" data-cc-dot="2" aria-label="Go to slide 3"></button>
			</div>
		</section>
	<?php endif; ?>

	<!-- System messages + any home menu-item content -->
	<div class="cc-wrap"><jdoc:include type="message" /></div>
	<jdoc:include type="component" />

	<!-- ===== FEATURE BAND (optional, home only — between hero and promo) ===== -->
	<?php if ($this->countModules('feature')) : ?>
		<section class="cc-feature"><div class="cc-wrap"><jdoc:include type="modules" name="feature" style="none" /></div></section>
	<?php endif; ?>

	<!-- ===== PROMO CARDS ===== -->
	<?php if ($hasPromo) : ?>
		<section class="cc-promo cc-wrap">
			<div class="cc-promo-grid">
				<jdoc:include type="modules" name="promo" style="ccpromo" />
			</div>
		</section>
	<?php elseif ($showPromo) : ?>
		<section class="cc-promo cc-wrap">
			<div class="cc-promo-grid">
				<article class="cc-card">
					<div class="cc-card-media"><span class="cc-card-icon">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><circle cx="12" cy="12" r="4.5"></circle></svg>
					</span></div>
					<div class="cc-card-body">
						<h3>Try Curling</h3>
						<p>New to the ice? Our Learn-to-Curl sessions cover the basics &mdash; throwing, sweeping, and a little strategy. All gear provided.</p>
						<a class="cc-card-link" href="#ltc">Sign up for Learn-to-Curl &rarr;</a>
					</div>
				</article>
				<article class="cc-card">
					<div class="cc-card-media"><span class="cc-card-icon">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
					</span></div>
					<div class="cc-card-body">
						<h3>Become a Member</h3>
						<p>Hooked already? Membership gets you league play, open ice, social events, and a whole club of friendly folks who love this game.</p>
						<a class="cc-card-link" href="#membership">View membership &rarr;</a>
					</div>
				</article>
				<article class="cc-card">
					<div class="cc-card-media"><span class="cc-card-icon">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path></svg>
					</span></div>
					<div class="cc-card-body">
						<h3>Upcoming Bonspiels</h3>
						<p>Bring a team or join one. Our bonspiels mix competitive draws with great food and better company. Spots fill fast &mdash; grab yours.</p>
						<a class="cc-card-link" href="#bonspiel">Register now &rarr;</a>
					</div>
				</article>
			</div>
		</section>
	<?php endif; ?>

	<!-- ===== NEWS ===== -->
	<?php if ($hasNews || $showNews) : ?>
	<section class="cc-news">
		<div class="cc-wrap">
			<div class="cc-news-head">
				<div>
					<div class="cc-eyebrow">From the Club</div>
					<h2>News &amp; Announcements</h2>
				</div>
				<a class="cc-news-all" href="#news">All news &rarr;</a>
			</div>
			<?php if ($hasNews) : ?>
				<jdoc:include type="modules" name="news" style="none" />
			<?php else : ?>
				<div class="cc-news-list">
					<a class="cc-news-item" href="#"><span class="cc-date"><span class="cc-day">14</span><span class="cc-mon">Jun</span></span><span class="cc-news-text"><h3>Summer leagues are open for registration</h3><p>Publish an article to your <strong>News</strong> category and it will appear here automatically.</p></span></a>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php endif; ?>

<?php else : // ============ INTERIOR PAGES ============ ?>

	<!-- ===== PAGE TITLE BAR ===== -->
	<?php if ($pageHeading && $showBanner) : ?>
		<div class="cc-banner">
			<div class="cc-wrap">
				<h1 class="cc-banner-title"><?php echo htmlspecialchars($pageHeading, ENT_QUOTES, 'UTF-8'); ?></h1>
				<?php if ($hasBreadcrumbs) : ?>
					<div class="cc-breadcrumbs"><jdoc:include type="modules" name="masthead" style="none" /></div>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<!-- ===== MAIN CONTENT (+ optional sidebar) ===== -->
	<div class="cc-page cc-wrap <?php echo $hasSidebar ? 'has-sidebar' : ''; ?>">
		<main class="cc-main" id="content" tabindex="-1">
			<jdoc:include type="message" />
			<jdoc:include type="component" />
		</main>
		<?php if ($hasSidebar) : ?>
			<aside class="cc-side">
				<jdoc:include type="modules" name="right" style="ccwell" />
			</aside>
		<?php endif; ?>
	</div>

<?php endif; // ============ /branch ============ ?>

	<!-- ===== SPONSORS (optional logo strip above the footer, all pages) ===== -->
	<?php if ($this->countModules('sponsors')) : ?>
		<section class="cc-sponsors"><div class="cc-wrap"><jdoc:include type="modules" name="sponsors" style="none" /></div></section>
	<?php endif; ?>

	<!-- ===== ABOVE-FOOTER (optional call-to-action band, all pages) ===== -->
	<?php if ($this->countModules('above-footer')) : ?>
		<section class="cc-cta"><div class="cc-wrap"><jdoc:include type="modules" name="above-footer" style="none" /></div></section>
	<?php endif; ?>

	<!-- ===== FOOTER (module-driven) ===== -->
	<footer class="cc-footer">
		<?php if ($hasFootCols) : ?>
			<div class="cc-wrap cc-footer-grid">
				<jdoc:include type="modules" name="bottom1" style="ccfootcol" />
				<jdoc:include type="modules" name="bottom2" style="ccfootcol" />
				<jdoc:include type="modules" name="bottom3" style="ccfootcol" />
				<jdoc:include type="modules" name="bottom4" style="ccfootcol" />
			</div>
		<?php else : ?>
			<div class="cc-wrap cc-footer-grid">
				<div class="cc-footer-col">
					<div class="cc-footer-brand">
						<?php if ($footerLogoUrl !== '') : ?>
							<img class="cc-logo-img cc-logo-sm" src="<?php echo htmlspecialchars($footerLogoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo $brandNameEsc; ?>">
						<?php else : ?>
							<span class="cc-logo cc-logo-sm" aria-hidden="true"></span>
						<?php endif; ?>
						<span class="cc-brand-name"><?php echo $brandNameEsc; ?></span>
					</div>
					<div class="cc-footer-col-body"><p>A member-run, not-for-profit curling club bringing the roaring game to central Indiana.</p></div>
				</div>
				<div class="cc-footer-col">
					<div class="cc-footer-h">Footer columns</div>
					<div class="cc-footer-col-body"><p>Assign your Custom HTML modules to positions <strong>bottom1</strong>&ndash;<strong>bottom4</strong> and they&rsquo;ll fill these footer columns.</p></div>
				</div>
			</div>
		<?php endif; ?>

		<div class="cc-footer-bar">
			<div class="cc-wrap cc-footer-bar-inner">
				<?php if ($this->countModules('footer')) : ?>
					<div class="cc-footer-copy"><jdoc:include type="modules" name="footer" style="none" /></div>
				<?php else : ?>
					<div class="cc-footer-copy"><?php echo $footerCopy !== '' ? $footerCopy : '&copy; ' . date('Y') . ' ' . $brandNameEsc . '. All rights reserved.'; ?></div>
				<?php endif; ?>
				<div class="cc-footer-meta">
					<jdoc:include type="modules" name="footer-menu" style="none" />
				</div>
			</div>
		</div>
	</footer>

	<jdoc:include type="modules" name="debug" style="none" />
</body>
</html>
