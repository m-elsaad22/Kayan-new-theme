/**
 * KAYAN Price Pay — Vanilla JS
 * اختيار باقة + نموذج حجز + تحويل GET لبوابة الدفع + زر عائم 3D
 */
(function () {
	'use strict';

	var CFG = window.KayanPricePay || {};
	var PAY_BASE = CFG.payBase || 'https://rukn-eltatawer-pay.tanceq.com/';
	var I18N = CFG.i18n || {};

	function qs(sel, root) {
		return (root || document).querySelector(sel);
	}
	function qsa(sel, root) {
		return Array.prototype.slice.call((root || document).querySelectorAll(sel));
	}

	function extractAmount(raw) {
		var s = String(raw || '');
		var m = s.match(/(\d+(?:[.,]\d+)?)/);
		if (!m) return '';
		return String(m[1]).replace(/,/g, '');
	}

	function buildPayUrl(data) {
		var params = new URLSearchParams();
		params.set('service', data.service || '');
		params.set('package', data.package || '');
		params.set('amount', data.amount || '');
		params.set('name', data.name || '');
		params.set('phone', data.phone || '');
		params.set('address', data.address || '');
		params.set('date', data.date || '');
		params.set('time', data.time || '');
		params.set('notes', data.notes || '');
		var base = PAY_BASE.indexOf('?') === -1 ? PAY_BASE + '?' : PAY_BASE + '&';
		return base + params.toString();
	}

	function setActivePackage(root, card) {
		qsa('.kpp-package, .pcard.kpp-selectable, .-PriceBox-v1-box.kpp-selectable', root).forEach(function (el) {
			el.classList.remove('is-active');
			el.setAttribute('aria-pressed', 'false');
		});
		card.classList.add('is-active');
		card.setAttribute('aria-pressed', 'true');

		var pkg = card.getAttribute('data-package') || '';
		var amount = card.getAttribute('data-amount') || extractAmount(card.getAttribute('data-amount-raw') || '');
		var form = qs('.kpp-form', root) || qs('.kpp-form');
		if (!form) return;

		var pkgInput = qs('.kpp-field-package', form);
		var amtInput = qs('.kpp-field-amount', form);
		if (pkgInput) pkgInput.value = pkg;
		if (amtInput) amtInput.value = amount;

		var sumPkg = qs('.kpp-summary-package', form);
		var sumAmt = qs('.kpp-summary-amount', form);
		if (sumPkg) sumPkg.textContent = pkg || '—';
		if (sumAmt) {
			var currency = card.getAttribute('data-currency') || CFG.currency || '';
			sumAmt.textContent = amount ? (amount + (currency ? ' ' + currency : '')) : '—';
		}

		var alertEl = qs('.kpp-alert', form);
		if (alertEl) alertEl.classList.remove('is-show');
	}

	function bindBookingRoot(root) {
		if (!root || root.getAttribute('data-kpp-bound') === '1') return;
		root.setAttribute('data-kpp-bound', '1');

		var cards = qsa('.kpp-package, .pcard.kpp-selectable, .-PriceBox-v1-box.kpp-selectable', root);
		cards.forEach(function (card, idx) {
			card.setAttribute('role', 'button');
			card.setAttribute('tabindex', '0');
			card.setAttribute('aria-pressed', 'false');
			if (!card.getAttribute('data-amount') && card.getAttribute('data-amount-raw')) {
				card.setAttribute('data-amount', extractAmount(card.getAttribute('data-amount-raw')));
			}
			card.addEventListener('click', function (e) {
				if (e.target.closest('a') && !e.target.closest('.kpp-package')) return;
				e.preventDefault();
				setActivePackage(root, card);
			});
			card.addEventListener('keydown', function (e) {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					setActivePackage(root, card);
				}
			});
			if (idx === 0 || card.classList.contains('is-active') || card.classList.contains('-ActivePlane')) {
				setActivePackage(root, card);
			}
		});

		var form = qs('.kpp-form', root);
		if (!form) return;

		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var alertEl = qs('.kpp-alert', form);
			if (!alertEl) {
				alertEl = document.createElement('div');
				alertEl.className = 'kpp-alert';
				form.insertBefore(alertEl, form.firstChild);
			}

			var pkg = (qs('.kpp-field-package', form) || {}).value || '';
			var amount = (qs('.kpp-field-amount', form) || {}).value || '';
			if (!pkg || !amount) {
				alertEl.textContent = I18N.selectPackage || 'اختر باقة أولاً';
				alertEl.classList.add('is-show');
				return;
			}

			var required = qsa('[required]', form);
			var ok = true;
			required.forEach(function (input) {
				var wrap = input.closest('.kpp-field');
				if (!String(input.value || '').trim()) {
					ok = false;
					if (wrap) wrap.classList.add('is-invalid');
				} else if (wrap) {
					wrap.classList.remove('is-invalid');
				}
			});
			if (!ok) {
				alertEl.textContent = I18N.required || 'يرجى تعبئة الحقول المطلوبة';
				alertEl.classList.add('is-show');
				return;
			}

			var serviceField = qs('input[name="service"]', form);
			var data = {
				service: (serviceField && serviceField.value) || CFG.service || document.title || '',
				package: pkg,
				amount: amount,
				name: (qs('[name="name"]', form) || {}).value || '',
				phone: (qs('[name="phone"]', form) || {}).value || '',
				address: (qs('[name="address"]', form) || {}).value || '',
				date: (qs('[name="date"]', form) || {}).value || '',
				time: (qs('[name="time"]', form) || {}).value || '',
				notes: (qs('[name="notes"]', form) || {}).value || ''
			};

			window.location.href = buildPayUrl(data);
		});
	}

	function enhanceLegacyPriceBoxes() {
		var boxes = qsa('.-PriceBox-v1-box');
		if (!boxes.length) return;

		var parent = boxes[0].parentElement;
		if (!parent) return;

		// غلاف مستقل حتى لا نكسر صف/شبكة كروت الأسعار
		var root = parent.classList.contains('kayan-price-booking')
			? parent
			: (parent.closest && parent.closest('.kayan-price-booking'));
		if (!root) {
			root = document.createElement('div');
			root.className = 'kayan-price-booking kayan-price-booking--boxes';
			root.id = 'kayan-price-booking';
			root.setAttribute('data-service', CFG.service || '');
			parent.parentNode.insertBefore(root, parent);
			root.appendChild(parent);
		}

		boxes.forEach(function (box) {
			box.classList.add('kpp-selectable');
			if (!box.getAttribute('data-package')) {
				var title = qs('h3', box);
				box.setAttribute('data-package', title ? title.textContent.trim() : '');
			}
			if (!box.getAttribute('data-amount')) {
				var priceEl = qs('.-price-app-value strong', box) || qs('.-Price-Selary strong', box);
				var raw = priceEl ? priceEl.textContent.trim() : '';
				box.setAttribute('data-amount-raw', raw);
				box.setAttribute('data-amount', extractAmount(raw));
			}
			var currencyEl = qs('.-price-app-value p', box);
			if (currencyEl && !box.getAttribute('data-currency')) {
				box.setAttribute('data-currency', currencyEl.textContent.trim());
			}
			var oldBtn = qs('.-Plane-Button-v1 a', box);
			if (oldBtn) {
				oldBtn.addEventListener('click', function (e) {
					e.preventDefault();
					setActivePackage(root, box);
					var form = qs('.kpp-form', root);
					if (form) form.scrollIntoView({ behavior: 'smooth', block: 'center' });
				});
			}
		});

		if (!qs('.kpp-form', root)) {
			var formWrap = document.createElement('div');
			formWrap.className = 'kpp-form-mount';
			formWrap.innerHTML =
				'<form class="kpp-form" novalidate>' +
				'<input type="hidden" name="service" value="' + escapeAttr(CFG.service || '') + '" />' +
				'<input type="hidden" name="package" value="" class="kpp-field-package" />' +
				'<input type="hidden" name="amount" value="" class="kpp-field-amount" />' +
				'<div class="kpp-form-grid">' +
				fieldHtml('name', 'الاسم', 'text', true, 'الاسم الكامل') +
				fieldHtml('phone', 'الجوال', 'tel', true, '05xxxxxxxx') +
				fieldHtml('address', 'العنوان', 'text', true, 'المدينة / المنطقة / الشارع', true) +
				fieldHtml('date', 'التاريخ', 'date', true, '') +
				fieldHtml('time', 'الوقت', 'time', true, '') +
				'<label class="kpp-field kpp-field-full"><span>ملاحظات</span><textarea name="notes" rows="3" placeholder="تفاصيل إضافية (اختياري)"></textarea></label>' +
				'</div>' +
				'<div class="kpp-summary" aria-live="polite">' +
				'<div class="kpp-summary-row"><span>الباقة المختارة</span><strong class="kpp-summary-package">—</strong></div>' +
				'<div class="kpp-summary-row"><span>المبلغ</span><strong class="kpp-summary-amount">—</strong></div>' +
				'</div>' +
				'<button type="submit" class="kpp-pay-btn"><i class="fas fa-lock" aria-hidden="true"></i><span>ادفع الآن</span></button>' +
				'<p class="kpp-form-hint">بعد الضغط سيتم تحويلك لصفحة الدفع الآمنة لإتمام الطلب.</p>' +
				'</form>';
			root.appendChild(formWrap);
		}

		bindBookingRoot(root);
	}

	function fieldHtml(name, label, type, required, placeholder, full) {
		return (
			'<label class="kpp-field' + (full ? ' kpp-field-full' : '') + '">' +
			'<span>' + label + (required ? ' <em>*</em>' : '') + '</span>' +
			'<input type="' + type + '" name="' + name + '"' + (required ? ' required' : '') +
			(placeholder ? ' placeholder="' + escapeAttr(placeholder) + '"' : '') + ' />' +
			'</label>'
		);
	}

	function escapeAttr(str) {
		return String(str)
			.replace(/&/g, '&amp;')
			.replace(/"/g, '&quot;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;');
	}

	function enhanceWidgetCards() {
		qsa('.price-grid').forEach(function (grid) {
			var section = grid.closest('.kayan-price-booking') || grid.parentElement;
			if (!section) return;
			section.classList.add('kayan-price-booking');
			if (!section.id) section.id = 'kayan-price-booking';

			qsa('.pcard', grid).forEach(function (card) {
				card.classList.add('kpp-selectable');
				if (!card.getAttribute('data-package')) {
					var title = qs('h3', card);
					card.setAttribute('data-package', title ? title.textContent.trim() : '');
				}
				if (!card.getAttribute('data-amount')) {
					var range = qs('.range b', card);
					var raw = range ? range.textContent.trim() : '';
					card.setAttribute('data-amount-raw', raw);
					card.setAttribute('data-amount', extractAmount(raw));
				}
			});
			bindBookingRoot(section);
		});
	}

	function initFloatingCta() {
		var hasBooking =
			qs('.yc-shortcode--price_list') ||
			qs('#kayan-price-booking') ||
			qs('.kayan-price-booking .kpp-form') ||
			qs('.kpp-form');

		var cta = qs('#kayanBookCta');

		// لا يظهر زر احجز الآن إلا بوجود بلوك الأسعار/الحجز
		if (!hasBooking) {
			if (cta) cta.parentNode.removeChild(cta);
			return;
		}

		if (!cta) return;

		cta.classList.add('is-visible');

		cta.addEventListener('click', function (e) {
			e.preventDefault();
			var target =
				qs('#kayan-price-booking') ||
				qs('.kayan-price-booking') ||
				qs('.yc-shortcode--price_list') ||
				qs('.kpp-form') ||
				qs('.price-grid');
			if (!target) return;
			target.scrollIntoView({ behavior: 'smooth', block: 'start' });
		});
	}

	function init() {
		qsa('.kayan-price-booking').forEach(bindBookingRoot);
		enhanceLegacyPriceBoxes();
		enhanceWidgetCards();
		initFloatingCta();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
