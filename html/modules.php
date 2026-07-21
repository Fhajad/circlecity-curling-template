<?php
/** Custom module chrome styles for the Circle City template. */
defined('_JEXEC') or die;

/**
 * "ccpromo" — wraps each module in a promo card shell so a Custom HTML
 * module dropped into the "promo" position matches the card design.
 * Module title (if shown) becomes the card heading.
 */
function modChrome_ccpromo($module, &$params, &$attribs)
{
	if (empty($module->content)) {
		return;
	}
	echo '<article class="cc-card cc-card--module">';
	if ($module->showtitle) {
		echo '<div class="cc-card-body"><h3>' . $module->title . '</h3>' . $module->content . '</div>';
	} else {
		echo '<div class="cc-card-body">' . $module->content . '</div>';
	}
	echo '</article>';
}

/**
 * "ccwell" — sidebar module styling: a titled white card.
 */
function modChrome_ccwell($module, &$params, &$attribs)
{
	if (empty($module->content)) {
		return;
	}
	echo '<div class="cc-well">';
	if ($module->showtitle) {
		echo '<h3 class="cc-well-title">' . $module->title . '</h3>';
	}
	echo '<div class="cc-well-body">' . $module->content . '</div>';
	echo '</div>';
}

/**
 * "ccfootcol" — footer column: the module title becomes a small heading,
 * its content sits below. Used by the bottom1–bottom4 footer positions.
 */
function modChrome_ccfootcol($module, &$params, &$attribs)
{
	if (empty($module->content)) {
		return;
	}
	echo '<div class="cc-footer-col">';
	if ($module->showtitle) {
		echo '<div class="cc-footer-h">' . $module->title . '</div>';
	}
	echo '<div class="cc-footer-col-body">' . $module->content . '</div>';
	echo '</div>';
}
