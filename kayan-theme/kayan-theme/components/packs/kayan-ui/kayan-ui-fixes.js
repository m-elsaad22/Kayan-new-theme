(function ($) {
	function removeContentCallButtons() {
		if (window.kayanShowCallButtons) {
			return;
		}
		$('.order-services-phonenumber, .-callbutton--post-card, a[href^="tel:"].post-card-buttons').remove();
		$('.YC-wigdht-contact-minibox .phonenumber').remove();
		$('.-company-contact-minibox .phonenumber').remove();
		$('.-taxonomy--contact- a[href^="tel:"]').closest('.-taxonimes-').remove();
		$('.-header-call-').remove();
		$('a[href^="tel:"].order-services-button').remove();
	}

	function removeFloatingCallButton() {
		if (window.kayanShowFloatingCallButton !== false) {
			return;
		}
		$('.--YourColor--phone-button').remove();
	}

	function removeCallButtons() {
		removeContentCallButtons();
		removeFloatingCallButton();
	}

	$(document).ready(removeCallButtons);
	$(window).on('scroll', function () {
		setTimeout(removeCallButtons, 50);
	});
})(jQuery);
