<?php
/**
 * Alternative layout for mod_articles_news  —  "slider"
 * Place this module in template position "scroller", point it at the
 * "Featured Articles" category, and set "# of Articles" to 3.
 *
 * Optional per-article custom fields (Article context):
 *   badge  -> small label shown above the headline (falls back to category title)
 *   cta    -> button text                          (falls back to "Read More")
 *   url    -> where the button goes: a full URL (https://...), a site path
 *             (/membership), or a Joomla link (index.php?Itemid=123).
 *             Falls back to the slide's own article (full view).
 *
 * Receives: $list (array of article objects), $params, $module
 */
defined('_JEXEC') or die;

if (empty($list)) {
	return;
}

// Helper: pull a custom field value by name from an article's jcfields
$ccField = function ($item, $name) {
	if (empty($item->jcfields)) {
		return '';
	}
	foreach ($item->jcfields as $f) {
		if ($f->name === $name && $f->value !== '') {
			return $f->value;
		}
	}
	return '';
};

$sliderId = 'cc-slider-' . $module->id;
?>
<section class="cc-hero" id="<?php echo $sliderId; ?>" data-cc-slider aria-roledescription="carousel" aria-label="<?php echo htmlspecialchars(($module->title !== '' ? $module->title : 'Featured highlights'), ENT_QUOTES, 'UTF-8'); ?>">
	<?php foreach ($list as $i => $item) :
		// Custom fields (badge / cta) are optional — load them defensively.
		if (empty($item->jcfields) && class_exists('FieldsHelper')) {
			try { $item->jcfields = FieldsHelper::getFields('com_content.article', $item, true); }
			catch (Exception $e) { $item->jcfields = array(); }
		}
		$images = json_decode($item->images);
		$img    = (is_object($images) && !empty($images->image_intro)) ? $images->image_intro : '';
		$alt    = (is_object($images) && !empty($images->image_intro_alt)) ? $images->image_intro_alt : $item->title;
		// Badge: the "badge" custom field, falling back to the category name when the
		// field is left untouched. Type the word "none" into the field to explicitly
		// suppress the badge for a slide (blank alone is not enough, since blank is
		// indistinguishable from "field not filled in yet").
		$badgeRaw = trim($ccField($item, 'badge'));
		if (strcasecmp($badgeRaw, 'none') === 0) {
			$badge = '';
		} elseif ($badgeRaw !== '') {
			$badge = $badgeRaw;
		} elseif (!empty($item->category_title)) {
			$badge = $item->category_title;
		} else {
			$badge = '';
		}
		$cta    = $ccField($item, 'cta');
		if ($cta === '') {
			$cta = 'Read More';
		}
		// Button destination: the optional "url" custom field wins; otherwise the
		// slide's own article. Accepts absolute URLs, site paths, or index.php links.
		$href = trim($ccField($item, 'url'));
		if ($href !== '' && stripos($href, 'index.php') === 0) {
			$href = JRoute::_($href);
		}
		if ($href === '') {
			$href = $item->link;
		}
		// Blurb: newsflash may expose the intro as ->text or ->introtext depending on settings.
		$blurb = '';
		if (!empty($item->text))          { $blurb = $item->text; }
		elseif (!empty($item->introtext)) { $blurb = $item->introtext; }
	?>
		<div class="cc-slide<?php echo $i === 0 ? ' is-active' : ''; ?>" data-cc-slide>
			<?php if ($img) : ?>
				<div class="cc-slide-img" style="background-image:url('<?php echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>'), linear-gradient(135deg,#3d5a78,#2b3a4a)" role="img" aria-label="<?php echo htmlspecialchars($alt, ENT_QUOTES, 'UTF-8'); ?>"></div>
			<?php else : ?>
				<div class="cc-slide-img cc-slide-img--empty"></div>
			<?php endif; ?>
			<div class="cc-slide-scrim"></div>
			<div class="cc-slide-inner cc-wrap">
				<div class="cc-slide-copy">
					<?php if ($badge) : ?><span class="cc-badge"><?php echo htmlspecialchars($badge, ENT_QUOTES, 'UTF-8'); ?></span><?php endif; ?>
					<h2><?php echo $item->title; ?></h2>
					<?php if ($blurb !== '') : ?><div class="cc-slide-blurb"><?php echo JHtml::_('content.prepare', $blurb); ?></div><?php endif; ?>
					<a class="cc-btn" href="<?php echo htmlspecialchars($href, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($cta, ENT_QUOTES, 'UTF-8'); ?> <span aria-hidden="true">&rarr;</span></a>
				</div>
			</div>
		</div>
	<?php endforeach; ?>

	<?php if (count($list) > 1) : ?>
		<button class="cc-arrow cc-arrow--prev" type="button" data-cc-prev aria-label="Previous">&#8249;</button>
		<button class="cc-arrow cc-arrow--next" type="button" data-cc-next aria-label="Next">&#8250;</button>
		<div class="cc-dots" data-cc-dots>
			<?php foreach ($list as $i => $item) : ?>
				<button class="cc-dot<?php echo $i === 0 ? ' is-active' : ''; ?>" type="button" data-cc-dot="<?php echo $i; ?>" aria-label="Go to slide <?php echo $i + 1; ?>"></button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
