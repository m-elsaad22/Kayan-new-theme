/**
 * KAYAN Admin Platform — Arabic/mobile UI behaviors.
 */
(function () {
	'use strict';

	function onReady(fn) {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', fn);
		} else {
			fn();
		}
	}

	onReady(function () {
		var root = document.querySelector('[data-kayan-admin]');
		if (!root) {
			return;
		}

		var mobileNav = root.querySelector('[data-kayan-mobile-nav]');
		if (mobileNav) {
			mobileNav.addEventListener('change', function () {
				var url = mobileNav.value;
				if (url) {
					window.location.href = url;
				}
			});
		}

		root.querySelectorAll('[data-kayan-tab]').forEach(function (tab) {
			tab.addEventListener('click', function (e) {
				e.preventDefault();
				var id = tab.getAttribute('data-kayan-tab');
				var nav = tab.closest('.kayan-admin-tabs');
				if (!nav || !id) {
					return;
				}
				nav.querySelectorAll('.nav-tab').forEach(function (el) {
					el.classList.toggle('nav-tab-active', el === tab);
				});
				nav.querySelectorAll('[data-kayan-tab-panel]').forEach(function (panel) {
					panel.classList.toggle('is-active', panel.getAttribute('data-kayan-tab-panel') === id);
				});
			});
		});

		root.querySelectorAll('[data-kayan-dialog-close]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var dialog = btn.closest('[data-kayan-dialog]');
				if (dialog) {
					dialog.hidden = true;
				}
			});
		});

		root.querySelectorAll('[data-kayan-drawer-close]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var drawer = btn.closest('[data-kayan-drawer]');
				if (drawer) {
					drawer.hidden = true;
				}
			});
		});
	});
})();
