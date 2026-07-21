/* Circle City Curling — hero scroller
 * Drives any element rendered by the "slider" layout (data-cc-slider).
 * Fade transition, auto-advance, arrows, dots. Pure vanilla — no jQuery needed.
 */
(function () {
	'use strict';

	function initSlider(root) {
		var slides = root.querySelectorAll('[data-cc-slide]');
		if (slides.length < 2) { return; }

		var dots    = root.querySelectorAll('[data-cc-dot]');
		var prevBtn = root.querySelector('[data-cc-prev]');
		var nextBtn = root.querySelector('[data-cc-next]');
		var current = 0;
		var timer   = null;
		var DELAY   = 6500;

		// Respect the visitor's "reduce motion" OS setting: no auto-advance.
		var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

		function show(n) {
			current = (n + slides.length) % slides.length;
			for (var i = 0; i < slides.length; i++) {
				slides[i].classList.toggle('is-active', i === current);
			}
			for (var j = 0; j < dots.length; j++) {
				dots[j].classList.toggle('is-active', j === current);
			}
		}
		function next() { show(current + 1); }
		function prev() { show(current - 1); }
		function restart() { stop(); if (!reduceMotion) { timer = setInterval(next, DELAY); } }
		function stop() { if (timer) { clearInterval(timer); timer = null; } }

		if (nextBtn) { nextBtn.addEventListener('click', function () { next(); restart(); }); }
		if (prevBtn) { prevBtn.addEventListener('click', function () { prev(); restart(); }); }
		for (var k = 0; k < dots.length; k++) {
			(function (idx) {
				dots[idx].addEventListener('click', function () { show(idx); restart(); });
			})(k);
		}

		root.addEventListener('mouseenter', stop);
		root.addEventListener('mouseleave', restart);
		// Pause while a keyboard user is focused inside the carousel.
		root.addEventListener('focusin', stop);
		root.addEventListener('focusout', restart);

		show(0);
		restart();
	}

	function boot() {
		var nodes = document.querySelectorAll('[data-cc-slider]');
		for (var i = 0; i < nodes.length; i++) { initSlider(nodes[i]); }
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
