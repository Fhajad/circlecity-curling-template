/* Circle City Curling — navigation enhancement
 * Makes the top nav usable on touch / small screens:
 *   - a hamburger button (.cc-nav-toggle) opens/closes the menu panel
 *   - any menu item with a nested <ul> gets a tap-to-open toggle (accordion)
 * Works for BOTH nav paths: the template's auto-built menu (.cc-submenu / .cc-mega)
 * AND a real mod_menu module dropped into the "menu" position (nested <ul>).
 * Desktop hover behaviour is left untouched above the breakpoint.
 * Pure vanilla — no jQuery.
 */
(function () {
	'use strict';

	var MOBILE = '(max-width: 860px)';

	function isMobile() {
		return window.matchMedia && window.matchMedia(MOBILE).matches;
	}

	function init() {
		var nav = document.querySelector('.cc-nav');
		if (!nav) { return; }

		var toggle = nav.querySelector('.cc-nav-toggle');
		var panel  = nav.querySelector('.cc-nav-panel');

		/* ----- hamburger: open/close the whole menu panel ----- */
		if (toggle && panel) {
			toggle.addEventListener('click', function () {
				var open = nav.classList.toggle('is-nav-open');
				toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			});
		}

		/* ----- per-item submenu toggles ----- */
		// Any <li> that directly contains a nested <ul> (or our .cc-mega) is expandable.
		var lis = nav.querySelectorAll('li');
		for (var i = 0; i < lis.length; i++) {
			(function (li) {
				var sub = li.querySelector(':scope > ul, :scope > .cc-mega');
				if (!sub) { return; }
				li.classList.add('cc-has-sub');

				// Build a small caret button so the parent link stays clickable.
				var btn = document.createElement('button');
				btn.type = 'button';
				btn.className = 'cc-sub-toggle';
				btn.setAttribute('aria-label', 'Expand submenu');
				btn.setAttribute('aria-expanded', 'false');
				btn.innerHTML = '<span class="cc-caret" aria-hidden="true"></span>';

				btn.addEventListener('click', function (e) {
					e.preventDefault();
					e.stopPropagation();
					// close sibling open items at the same level
					var siblings = li.parentNode ? li.parentNode.children : [];
					for (var s = 0; s < siblings.length; s++) {
						if (siblings[s] !== li && siblings[s].classList) {
							siblings[s].classList.remove('is-open');
							var sb = siblings[s].querySelector(':scope > .cc-sub-toggle');
							if (sb) { sb.setAttribute('aria-expanded', 'false'); }
						}
					}
					var open = li.classList.toggle('is-open');
					btn.setAttribute('aria-expanded', open ? 'true' : 'false');
				});

				// Insert the caret right after the item's own link/label.
				var firstLink = li.querySelector(':scope > a, :scope > span');
				if (firstLink && firstLink.nextSibling) {
					li.insertBefore(btn, firstLink.nextSibling);
				} else {
					li.insertBefore(btn, li.firstChild);
				}
			})(lis[i]);
		}

		/* ----- close everything on outside click / Esc ----- */
		document.addEventListener('click', function (e) {
			if (!nav.contains(e.target)) { closeAll(nav, toggle); }
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' || e.keyCode === 27) { closeAll(nav, toggle); }
		});

		/* ----- reset state when crossing the breakpoint back to desktop ----- */
		if (window.matchMedia) {
			var mq = window.matchMedia(MOBILE);
			var onChange = function () { if (!isMobile()) { closeAll(nav, toggle); } };
			if (mq.addEventListener) { mq.addEventListener('change', onChange); }
			else if (mq.addListener) { mq.addListener(onChange); }
		}
	}

	function closeAll(nav, toggle) {
		nav.classList.remove('is-nav-open');
		if (toggle) { toggle.setAttribute('aria-expanded', 'false'); }
		var open = nav.querySelectorAll('li.is-open');
		for (var i = 0; i < open.length; i++) {
			open[i].classList.remove('is-open');
			var b = open[i].querySelector('.cc-sub-toggle');
			if (b) { b.setAttribute('aria-expanded', 'false'); }
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
