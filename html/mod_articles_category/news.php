<?php
/**
 * Alternative layout for mod_articles_category  —  "news"
 * Place this module in template position "news", point it at the
 * "News" category, set "Count" to 3, and order by "Most Recent first".
 *
 * Receives: $list (array of article objects), $params, $module, $grouped
 */
defined('_JEXEC') or die;

// mod_articles_category can return a grouped array; flatten to a simple list.
$items = array();
if (!empty($list)) {
	foreach ($list as $entry) {
		if (is_array($entry)) {
			foreach ($entry as $sub) { $items[] = $sub; }
		} else {
			$items[] = $entry;
		}
	}
}

if (empty($items)) {
	return;
}
?>
<div class="cc-news-list">
	<?php foreach ($items as $item) :
		// Prefer the article's "created" date; fall back to publish_up.
		$rawDate = !empty($item->displayDate) ? $item->displayDate : (!empty($item->created) ? $item->created : $item->publish_up);
		$day = JHtml::_('date', $rawDate, 'd');
		$mon = JHtml::_('date', $rawDate, 'M');
	?>
		<a class="cc-news-item" href="<?php echo $item->link; ?>">
			<span class="cc-date">
				<span class="cc-day"><?php echo $day; ?></span>
				<span class="cc-mon"><?php echo $mon; ?></span>
			</span>
			<span class="cc-news-text">
				<h3><?php echo $item->title; ?></h3>
				<?php if (!empty($item->introtext)) : ?>
					<p><?php echo JHtml::_('string.truncate', strip_tags($item->introtext), 180); ?></p>
				<?php endif; ?>
			</span>
		</a>
	<?php endforeach; ?>
</div>
