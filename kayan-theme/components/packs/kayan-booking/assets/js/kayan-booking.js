/**
 * kayan-booking — معالج الحجز متعدد الخطوات (Phase 1)
 * لا يعتمد على أي مكتبة خارجية — Vanilla JS فقط.
 */
(function () {
	'use strict';

	if ( typeof KayanBookingConfig === 'undefined' ) return;
	var CFG = KayanBookingConfig;
	var I18N = CFG.i18n;

	function el( tag, attrs, html ) {
		var e = document.createElement( tag );
		attrs = attrs || {};
		for ( var k in attrs ) {
			if ( k === 'class' ) e.className = attrs[ k ];
			else e.setAttribute( k, attrs[ k ] );
		}
		if ( html !== undefined ) e.innerHTML = html;
		return e;
	}

	function ajaxUrl( action ) {
		return CFG.ajaxBase + action + '/';
	}

	function todayPlusHours( hours ) {
		var d = new Date();
		d.setHours( d.getHours() + hours );
		return d;
	}

	function fmtDate( d ) {
		var mm = ( d.getMonth() + 1 ).toString().padStart( 2, '0' );
		var dd = d.getDate().toString().padStart( 2, '0' );
		return d.getFullYear() + '-' + mm + '-' + dd;
	}

	function KayanBookingWizard( root ) {
		this.root = root;
		var boot;
		try {
			boot = JSON.parse( decodeURIComponent( escape( atob( root.getAttribute( 'data-boot' ) ) ) ) );
		} catch ( e ) {
			boot = null;
		}
		if ( ! boot || ! boot.services || ! boot.services.length ) {
			root.remove();
			return;
		}

		this.boot           = boot;
		this.step            = 1;
		this.selected        = boot.services.length === 1 ? [ boot.services[0].id ] : [];
		this.serviceFieldDefs= {};
		this.answers         = {}; // { serviceId: { fieldId: value } }
		this.files           = {}; // { "serviceId_fieldId": File }
		this.customer         = {};

		this.Render();
	}

	KayanBookingWizard.prototype.Render = function () {
		this.root.innerHTML = '';
		var wrap = el( 'div', { class: 'kbw-inner' } );

		wrap.appendChild( this.RenderProgress() );

		var body = el( 'div', { class: 'kbw-body' } );
		if ( this.step === 1 ) body.appendChild( this.RenderStep1() );
		else if ( this.step === 2 ) body.appendChild( this.RenderStep2() );
		else if ( this.step === 3 ) body.appendChild( this.RenderStep3() );
		else if ( this.step === 4 ) body.appendChild( this.RenderStep4() );
		else if ( this.step === 5 ) body.appendChild( this.RenderStep5() );
		else if ( this.step === 'payment' ) body.appendChild( this.RenderPayment() );
		else if ( this.step === 'success' ) body.appendChild( this.RenderSuccess() );

		wrap.appendChild( body );
		this.root.appendChild( wrap );
	};

	KayanBookingWizard.prototype.RenderProgress = function () {
		var labels = [ I18N.chooseService, 'التفاصيل', I18N.customerData, I18N.dateTime, I18N.review, I18N.payChooseMethod ];
		var currentN = ( typeof this.step === 'number' ) ? this.step : ( this.step === 'payment' ? 6 : 7 );
		var bar = el( 'div', { class: 'kbw-progress' } );
		for ( var i = 0; i < labels.length; i++ ) {
			var n = i + 1;
			var cls = 'kbw-step-dot';
			if ( n < currentN ) cls += ' is-done';
			if ( n === currentN ) cls += ' is-active';
			var dot = el( 'div', { class: cls }, '<span>' + n + '</span><label>' + labels[ i ] + '</label>' );
			bar.appendChild( dot );
		}
		return bar;
	};

	KayanBookingWizard.prototype.ServiceById = function ( id ) {
		for ( var i = 0; i < this.boot.services.length; i++ ) {
			if ( this.boot.services[ i ].id == id ) return this.boot.services[ i ];
		}
		return null;
	};

	// ═══════════ الخطوة 1 — اختيار الخدمة ═══════════
	KayanBookingWizard.prototype.RenderStep1 = function () {
		var self = this;
		var box = el( 'div', { class: 'kbw-step kbw-step-1' } );
		box.appendChild( el( 'h3', {}, I18N.chooseService ) );

		var grid = el( 'div', { class: 'kbw-service-grid' } );
		this.boot.services.forEach( function ( s ) {
			var checked = self.selected.indexOf( s.id ) !== -1;
			var card = el( 'div', { class: 'kbw-service-card' + ( checked ? ' is-selected' : '' ), 'data-id': s.id } );
			var priceLabel = s.price ? ( ( s.price_from ? 'يبدأ من ' : '' ) + s.price + ' ' + CFG.currency ) : '';
			card.innerHTML =
				'<div class="kbw-service-icon">' + ( s.icon || '' ) + '</div>' +
				'<div class="kbw-service-title">' + s.title + '</div>' +
				( s.desc ? '<div class="kbw-service-desc">' + s.desc + '</div>' : '' ) +
				'<div class="kbw-service-meta">' +
					( s.duration ? '<span class="kbw-duration"><i class="fa-regular fa-clock"></i> ' + s.duration + '</span>' : '' ) +
					( priceLabel ? '<span class="kbw-price">' + priceLabel + '</span>' : '' ) +
				'</div>' +
				'<div class="kbw-service-check"><i class="fa-solid fa-check"></i></div>';
			if ( s.color ) card.style.setProperty( '--kbw-accent', s.color );

			card.addEventListener( 'click', function () {
				var id = parseInt( this.getAttribute( 'data-id' ), 10 );
				var idx = self.selected.indexOf( id );
				if ( idx === -1 ) self.selected.push( id );
				else self.selected.splice( idx, 1 );
				self.Render();
			} );
			grid.appendChild( card );
		} );
		box.appendChild( grid );

		box.appendChild( this.RenderNav( { back: false, nextLabel: I18N.next, onNext: function () {
			if ( ! self.selected.length ) {
				self.ShowError( I18N.selectAtLeastOne );
				return;
			}
			self.LoadServiceFields();
		} } ) );

		return box;
	};

	KayanBookingWizard.prototype.LoadServiceFields = function () {
		var self = this;
		this.SetLoading( true );
		var body = new URLSearchParams();
		body.set( 'service_ids', this.selected.join( ',' ) );

		fetch( ajaxUrl( 'kayan_booking_get_service_fields' ), { method: 'POST', body: body } )
			.then( function ( r ) { return r.json(); } )
			.then( function ( data ) {
				self.serviceFieldDefs = data.fields || {};
				self.SetLoading( false );
				self.step = Object.keys( self.serviceFieldDefs ).length ? 2 : 3;
				self.Render();
			} )
			.catch( function () {
				self.SetLoading( false );
				self.step = 3;
				self.Render();
			} );
	};

	// ═══════════ الخطوة 2 — الحقول الديناميكية لكل خدمة ═══════════
	KayanBookingWizard.prototype.RenderStep2 = function () {
		var self = this;
		var box = el( 'div', { class: 'kbw-step kbw-step-2' } );

		this.selected.forEach( function ( sid ) {
			var defs = self.serviceFieldDefs[ sid ];
			if ( ! defs || ! defs.length ) return;

			var service = self.ServiceById( sid );
			box.appendChild( el( 'h3', {}, service ? service.title : '' ) );

			var group = el( 'div', { class: 'kbw-fields-group' } );
			defs.forEach( function ( f ) {
				group.appendChild( self.RenderField( sid, f ) );
			} );
			box.appendChild( group );
		} );

		box.appendChild( this.RenderNav( { back: true, nextLabel: I18N.next, onBack: function () {
			self.step = 1; self.Render();
		}, onNext: function () {
			if ( self.ValidateStep2() ) { self.step = 3; self.Render(); }
		} } ) );

		return box;
	};

	KayanBookingWizard.prototype.RenderField = function ( sid, f ) {
		var self = this;
		var wrap = el( 'div', { class: 'kbw-field kbw-field-' + f.type, 'data-sid': sid, 'data-fid': f.id } );
		var label = el( 'label', {}, f.title + ( f.require ? ' <span class="req">*</span>' : '' ) );
		wrap.appendChild( label );
		if ( f.disc ) wrap.appendChild( el( 'small', { class: 'kbw-hint' }, f.disc ) );

		var input;
		if ( f.type === 'TextArea' ) {
			input = el( 'textarea', { rows: '3' } );
		} else if ( f.type === 'Select' ) {
			input = el( 'select' );
			input.appendChild( el( 'option', { value: '' }, '— اختر —' ) );
			( f.options || [] ).forEach( function ( o ) { input.appendChild( el( 'option', { value: o }, o ) ); } );
		} else if ( f.type === 'CheckBox' || f.type === 'Radio' ) {
			input = el( 'div', { class: 'kbw-choice-list' } );
			( f.options || [] ).forEach( function ( o, idx ) {
				var inputType = f.type === 'CheckBox' ? 'checkbox' : 'radio';
				var optId = 'kbw-opt-' + sid + '-' + f.id + '-' + idx;
				var line = el( 'label', { class: 'kbw-choice', for: optId } );
				line.innerHTML = '<input type="' + inputType + '" id="' + optId + '" name="kbw_' + sid + '_' + f.id + '" value="' + o + '"> <span>' + o + '</span>';
				input.appendChild( line );
			} );
		} else if ( f.type === 'SwitchBox' ) {
			input = el( 'label', { class: 'kbw-switch' } );
			input.innerHTML = '<input type="checkbox"><span class="kbw-switch-slider"></span>';
		} else if ( f.type === 'File' ) {
			input = el( 'input', { type: 'file', accept: 'image/*' } );
			input.addEventListener( 'change', function () {
				if ( this.files && this.files[0] ) self.files[ sid + '_' + f.id ] = this.files[0];
			} );
		} else if ( f.type === 'Number' ) {
			input = el( 'input', { type: 'number' } );
		} else {
			input = el( 'input', { type: 'text' } );
		}

		if ( input.tagName !== 'DIV' && f.type !== 'File' ) {
			input.addEventListener( 'input', function () { self.SetAnswer( sid, f.id, this.value ); } );
			if ( f.type === 'SwitchBox' ) {
				input.querySelector( 'input' ).addEventListener( 'change', function () { self.SetAnswer( sid, f.id, this.checked ? '1' : '' ); } );
			}
		} else if ( f.type === 'CheckBox' || f.type === 'Radio' ) {
			input.querySelectorAll( 'input' ).forEach( function ( inp ) {
				inp.addEventListener( 'change', function () {
					if ( f.type === 'Radio' ) {
						self.SetAnswer( sid, f.id, this.value );
					} else {
						var current = ( self.answers[ sid ] && self.answers[ sid ][ f.id ] ) || [];
						if ( this.checked ) current.push( this.value );
						else current = current.filter( function ( v ) { return v !== this.value; }.bind( this ) );
						self.SetAnswer( sid, f.id, current );
					}
				} );
			} );
		}

		wrap.appendChild( input );
		return wrap;
	};

	KayanBookingWizard.prototype.SetAnswer = function ( sid, fid, value ) {
		if ( ! this.answers[ sid ] ) this.answers[ sid ] = {};
		this.answers[ sid ][ fid ] = value;
	};

	KayanBookingWizard.prototype.ValidateStep2 = function () {
		var self = this;
		var ok = true;
		var missing = '';
		this.selected.forEach( function ( sid ) {
			var defs = self.serviceFieldDefs[ sid ] || [];
			defs.forEach( function ( f ) {
				if ( f.require ) {
					var val = self.answers[ sid ] && self.answers[ sid ][ f.id ];
					var hasFile = f.type === 'File' && self.files[ sid + '_' + f.id ];
					if ( ( ! val || ( Array.isArray( val ) && ! val.length ) ) && ! hasFile ) {
						ok = false;
						missing = f.title;
					}
				}
			} );
		} );
		if ( ! ok ) this.ShowError( I18N.required + ( missing ? ' (' + missing + ')' : '' ) );
		return ok;
	};

	// ═══════════ الخطوة 3 — بيانات العميل والموقع ═══════════
	KayanBookingWizard.prototype.RenderStep3 = function () {
		var self = this;
		var box = el( 'div', { class: 'kbw-step kbw-step-3' } );
		box.appendChild( el( 'h3', {}, I18N.customerData ) );

		var fieldsSpec = [
			[ 'customer_name', 'الاسم بالكامل', 'text', true ],
			[ 'customer_phone', 'رقم الهاتف', 'tel', true ],
			[ 'customer_whatsapp', 'رقم الواتساب (إن اختلف)', 'tel', false ],
			[ 'customer_email', 'البريد الإلكتروني', 'email', false ],
			[ 'country', 'الدولة', 'text', true ],
			[ 'emirate', 'الإمارة / المنطقة الرئيسية', 'text', true ],
			[ 'city', 'المدينة', 'text', true ],
			[ 'district', 'المنطقة', 'text', false ],
			[ 'building_name', 'اسم المبنى', 'text', false ],
			[ 'unit_number', 'رقم الفيلا / الشقة', 'text', false ],
			[ 'floor', 'الدور', 'text', false ],
		];

		var grid = el( 'div', { class: 'kbw-customer-grid' } );
		fieldsSpec.forEach( function ( spec ) {
			var wrap = el( 'div', { class: 'kbw-field' } );
			wrap.appendChild( el( 'label', {}, spec[1] + ( spec[3] ? ' <span class="req">*</span>' : '' ) ) );
			var inp = el( 'input', { type: spec[2], value: self.customer[ spec[0] ] || '' } );
			inp.addEventListener( 'input', function () { self.customer[ spec[0] ] = this.value; } );
			wrap.appendChild( inp );
			grid.appendChild( wrap );
		} );
		box.appendChild( grid );

		var addrWrap = el( 'div', { class: 'kbw-field' } );
		addrWrap.appendChild( el( 'label', {}, 'العنوان بالتفصيل <span class="req">*</span>' ) );
		var addrInput = el( 'textarea', { rows: '2' }, self.customer.address || '' );
		addrInput.addEventListener( 'input', function () { self.customer.address = this.value; } );
		addrWrap.appendChild( addrInput );
		box.appendChild( addrWrap );

		var locBtn = el( 'button', { type: 'button', class: 'kbw-locate-btn' }, '<i class="fa-solid fa-location-crosshairs"></i> ' + I18N.detectLocation );
		var locStatus = el( 'span', { class: 'kbw-loc-status' }, self.customer.lat ? '✓ تم تحديد الموقع' : '' );
		locBtn.addEventListener( 'click', function () {
			if ( ! navigator.geolocation ) return;
			locStatus.textContent = '...جارِ التحديد';
			navigator.geolocation.getCurrentPosition( function ( pos ) {
				self.customer.lat = pos.coords.latitude;
				self.customer.lng = pos.coords.longitude;
				locStatus.textContent = '✓ تم تحديد الموقع';
			}, function () {
				locStatus.textContent = 'تعذّر تحديد الموقع';
			} );
		} );
		var locWrap = el( 'div', { class: 'kbw-locate-row' } );
		locWrap.appendChild( locBtn );
		locWrap.appendChild( locStatus );
		box.appendChild( locWrap );

		box.appendChild( this.RenderNav( { back: true, nextLabel: I18N.next, onBack: function () {
			self.step = Object.keys( self.serviceFieldDefs ).length ? 2 : 1;
			self.Render();
		}, onNext: function () {
			if ( self.ValidateStep3() ) { self.step = 4; self.Render(); }
		} } ) );

		return box;
	};

	KayanBookingWizard.prototype.ValidateStep3 = function () {
		var required = [ 'customer_name', 'customer_phone', 'country', 'emirate', 'city', 'address' ];
		for ( var i = 0; i < required.length; i++ ) {
			if ( ! this.customer[ required[ i ] ] || ! ( '' + this.customer[ required[ i ] ] ).trim() ) {
				this.ShowError( I18N.required );
				return false;
			}
		}
		return true;
	};

	// ═══════════ الخطوة 4 — التاريخ والوقت ═══════════
	KayanBookingWizard.prototype.RenderStep4 = function () {
		var self = this;
		var box = el( 'div', { class: 'kbw-step kbw-step-4' } );
		box.appendChild( el( 'h3', {}, I18N.dateTime ) );

		var minDate = todayPlusHours( CFG.hours.lead || 2 );

		var dateWrap = el( 'div', { class: 'kbw-field' } );
		dateWrap.appendChild( el( 'label', {}, 'التاريخ <span class="req">*</span>' ) );
		var dateInput = el( 'input', { type: 'date', min: fmtDate( minDate ), value: self.customer.booking_date || '' } );
		dateInput.addEventListener( 'input', function () { self.customer.booking_date = this.value; } );
		dateWrap.appendChild( dateInput );
		box.appendChild( dateWrap );

		var timeWrap = el( 'div', { class: 'kbw-field' } );
		timeWrap.appendChild( el( 'label', {}, 'الوقت <span class="req">*</span>' ) );
		var timeSelect = el( 'select' );
		timeSelect.appendChild( el( 'option', { value: '' }, '— اختر الوقت —' ) );
		( this.GenerateSlots() ).forEach( function ( slot ) {
			var opt = el( 'option', { value: slot }, slot );
			if ( slot === self.customer.booking_time ) opt.setAttribute( 'selected', 'selected' );
			timeSelect.appendChild( opt );
		} );
		timeSelect.addEventListener( 'change', function () { self.customer.booking_time = this.value; } );
		timeWrap.appendChild( timeSelect );
		box.appendChild( timeWrap );

		var notesWrap = el( 'div', { class: 'kbw-field' } );
		notesWrap.appendChild( el( 'label', {}, 'ملاحظات إضافية (اختياري)' ) );
		var notesInput = el( 'textarea', { rows: '2' }, self.customer.notes || '' );
		notesInput.addEventListener( 'input', function () { self.customer.notes = this.value; } );
		notesWrap.appendChild( notesInput );
		box.appendChild( notesWrap );

		box.appendChild( this.RenderNav( { back: true, nextLabel: I18N.next, onBack: function () {
			self.step = 3; self.Render();
		}, onNext: function () {
			if ( ! self.customer.booking_date || ! self.customer.booking_time ) {
				self.ShowError( I18N.required );
				return;
			}
			self.step = 5; self.Render();
		} } ) );

		return box;
	};

	KayanBookingWizard.prototype.GenerateSlots = function () {
		var hours = CFG.hours || { from: '09:00', to: '21:00', slot: 60 };
		var slots = [];
		var slotMin = parseInt( hours.slot, 10 ) || 60;
		var fromParts = ( hours.from || '09:00' ).split( ':' ).map( Number );
		var toParts   = ( hours.to || '21:00' ).split( ':' ).map( Number );
		var cur = fromParts[0] * 60 + fromParts[1];
		var end = toParts[0] * 60 + toParts[1];
		while ( cur < end ) {
			var h = Math.floor( cur / 60 ).toString().padStart( 2, '0' );
			var m = ( cur % 60 ).toString().padStart( 2, '0' );
			slots.push( h + ':' + m );
			cur += slotMin;
		}
		return slots;
	};

	// ═══════════ الخطوة 5 — مراجعة الطلب والدفع ═══════════
	KayanBookingWizard.prototype.RenderStep5 = function () {
		var self = this;
		var box = el( 'div', { class: 'kbw-step kbw-step-5' } );
		box.appendChild( el( 'h3', {}, I18N.review ) );

		var subtotal = 0;
		var list = el( 'ul', { class: 'kbw-review-services' } );
		this.selected.forEach( function ( sid ) {
			var s = self.ServiceById( sid );
			if ( ! s ) return;
			var price = parseFloat( s.price ) || 0;
			subtotal += price;
			list.appendChild( el( 'li', {}, '<span>' + s.title + '</span><span>' + ( price ? price + ' ' + CFG.currency : '—' ) + '</span>' ) );
		} );
		box.appendChild( list );

		var tax = Math.round( subtotal * ( CFG.taxRate / 100 ) * 100 ) / 100;
		var total = Math.round( ( subtotal + tax ) * 100 ) / 100;

		var totals = el( 'div', { class: 'kbw-totals' } );
		totals.innerHTML =
			'<div><span>' + I18N.subtotal + '</span><span>' + subtotal.toFixed( 2 ) + ' ' + CFG.currency + '</span></div>' +
			'<div><span>' + I18N.tax + ' (' + CFG.taxRate + '%)</span><span>' + tax.toFixed( 2 ) + ' ' + CFG.currency + '</span></div>' +
			'<div class="kbw-total-final"><span>' + I18N.total + '</span><span>' + total.toFixed( 2 ) + ' ' + CFG.currency + '</span></div>';
		box.appendChild( totals );

		var summary = el( 'div', { class: 'kbw-review-summary' } );
		summary.innerHTML =
			'<p><strong>العميل:</strong> ' + ( self.customer.customer_name || '' ) + ' — ' + ( self.customer.customer_phone || '' ) + '</p>' +
			'<p><strong>الموقع:</strong> ' + [ self.customer.emirate, self.customer.city, self.customer.district, self.customer.address ].filter( Boolean ).join( '، ' ) + '</p>' +
			'<p><strong>الموعد:</strong> ' + ( self.customer.booking_date || '' ) + ' ' + ( self.customer.booking_time || '' ) + '</p>';
		box.appendChild( summary );

		box.appendChild( this.RenderNav( { back: true, nextLabel: I18N.submit, onBack: function () {
			self.step = 4; self.Render();
		}, onNext: function () {
			self.Submit();
		} } ) );

		return box;
	};

	KayanBookingWizard.prototype.Submit = function () {
		var self = this;
		this.SetLoading( true, I18N.sending );

		fetch( ajaxUrl( 'kayan_booking_nonce' ) ).then( function ( r ) { return r.json(); } ).then( function ( nonceRes ) {
			var fd = new FormData();
			fd.set( 'kb_nonce', nonceRes.nonce );
			fd.set( 'kb_website', '' ); // honeypot

			var servicesPayload = self.selected.map( function ( sid ) {
				return { id: sid, fields: self.answers[ sid ] || {} };
			} );
			fd.set( 'services', JSON.stringify( servicesPayload ) );

			Object.keys( self.customer ).forEach( function ( k ) { fd.set( k, self.customer[ k ] ); } );
			fd.set( 'source_post_id', self.boot.postId || 0 );

			Object.keys( self.files ).forEach( function ( key ) {
				fd.set( 'kb_file_' + key, self.files[ key ] );
			} );

			return fetch( ajaxUrl( 'kayan_booking_submit' ), { method: 'POST', body: fd } );
		} ).then( function ( r ) { return r.json(); } ).then( function ( data ) {
			self.SetLoading( false );
			if ( data.success ) {
				self.bookingResult = data;
				self.step = 'payment';
				self.paymentStage = 'method';
				self.Render();
			} else {
				self.ShowError( data.message || I18N.error );
			}
		} ).catch( function () {
			self.SetLoading( false );
			self.ShowError( I18N.error );
		} );
	};

	// ═══════════ الخطوة 6 — الدفع (Demo Gateway) ═══════════
	KayanBookingWizard.prototype.RenderPayment = function () {
		if ( this.paymentStage === 'card' )       return this.RenderPaymentCard();
		if ( this.paymentStage === 'otp' )        return this.RenderPaymentOtp();
		if ( this.paymentStage === 'processing' ) return this.RenderPaymentProcessing();
		return this.RenderPaymentMethods();
	};

	KayanBookingWizard.prototype.RenderPaymentMethods = function () {
		var self = this;
		var box = el( 'div', { class: 'kbw-step kbw-payment-methods' } );
		box.appendChild( el( 'h3', {}, I18N.payChooseMethod ) );
		box.appendChild( el( 'p', { class: 'kbw-demo-notice' }, '<i class="fa-solid fa-flask"></i> ' + I18N.payDemoNotice ) );

		var methods = ( CFG.payMethods || [ 'card', 'wallet', 'cash' ] );
		var defs = {
			card:   { label: I18N.payCard, icon: 'fa-regular fa-credit-card' },
			wallet: { label: I18N.payWallet, icon: 'fa-solid fa-wallet' },
			cash:   { label: I18N.payCash, icon: 'fa-solid fa-hand-holding-dollar' },
		};

		var list = el( 'div', { class: 'kbw-pay-method-list' } );
		methods.forEach( function ( m ) {
			if ( ! defs[ m ] ) return;
			var card = el( 'div', { class: 'kbw-pay-method-card' } );
			card.innerHTML = '<i class="' + defs[ m ].icon + '"></i><span>' + defs[ m ].label + '</span>';
			card.addEventListener( 'click', function () {
				if ( m === 'card' ) { self.paymentStage = 'card'; self.Render(); }
				else if ( m === 'cash' ) { self.ConfirmCash(); }
				else { self.ChargeWallet( m ); }
			} );
			list.appendChild( card );
		} );
		box.appendChild( list );

		box.appendChild( this.RenderNav( { back: true, nextLabel: '', onBack: function () {
			self.step = 5; self.Render();
		} } ) );
		box.querySelector( '.kbw-btn-next' ).remove();

		return box;
	};

	KayanBookingWizard.prototype.RenderPaymentCard = function () {
		var self = this;
		var box = el( 'div', { class: 'kbw-step kbw-payment-card' } );
		box.appendChild( el( 'h3', {}, I18N.payCardTitle ) );

		var card = { number: '', name: '', expiry: '', cvv: '' };

		var grid = el( 'div', { class: 'kbw-customer-grid' } );

		var numWrap = el( 'div', { class: 'kbw-field' } );
		numWrap.appendChild( el( 'label', {}, I18N.payCardNumber ) );
		var numInput = el( 'input', { type: 'text', inputmode: 'numeric', maxlength: '19', placeholder: '4111 1111 1111 1111' } );
		numInput.addEventListener( 'input', function () { card.number = this.value; } );
		numWrap.appendChild( numInput );
		grid.appendChild( numWrap );

		var nameWrap = el( 'div', { class: 'kbw-field' } );
		nameWrap.appendChild( el( 'label', {}, I18N.payCardName ) );
		var nameInput = el( 'input', { type: 'text' } );
		nameInput.addEventListener( 'input', function () { card.name = this.value; } );
		nameWrap.appendChild( nameInput );
		grid.appendChild( nameWrap );

		var expWrap = el( 'div', { class: 'kbw-field' } );
		expWrap.appendChild( el( 'label', {}, I18N.payCardExpiry ) );
		var expInput = el( 'input', { type: 'text', placeholder: '12/28', maxlength: '5' } );
		expInput.addEventListener( 'input', function () { card.expiry = this.value; } );
		expWrap.appendChild( expInput );
		grid.appendChild( expWrap );

		var cvvWrap = el( 'div', { class: 'kbw-field' } );
		cvvWrap.appendChild( el( 'label', {}, I18N.payCardCvv ) );
		var cvvInput = el( 'input', { type: 'text', inputmode: 'numeric', maxlength: '4', placeholder: '123' } );
		cvvInput.addEventListener( 'input', function () { card.cvv = this.value; } );
		cvvWrap.appendChild( cvvInput );
		grid.appendChild( cvvWrap );

		box.appendChild( grid );

		box.appendChild( this.RenderNav( { back: true, nextLabel: I18N.payConfirm, onBack: function () {
			self.paymentStage = 'method'; self.Render();
		}, onNext: function () {
			self.ChargeCard( card );
		} } ) );

		return box;
	};

	KayanBookingWizard.prototype.RenderPaymentProcessing = function () {
		var box = el( 'div', { class: 'kbw-step kbw-payment-processing' } );
		box.innerHTML = '<div class="kbw-spinner kbw-spinner-lg"></div><p>' + I18N.payProcessing + '</p>';
		return box;
	};

	KayanBookingWizard.prototype.RenderPaymentOtp = function () {
		var self = this;
		var box = el( 'div', { class: 'kbw-step kbw-payment-otp' } );
		box.appendChild( el( 'h3', {}, I18N.payOtpTitle ) );
		if ( this.demoOtpHint ) {
			box.appendChild( el( 'p', { class: 'kbw-demo-notice' }, '<i class="fa-solid fa-flask"></i> رمز تجريبي: <strong>' + this.demoOtpHint + '</strong>' ) );
		}

		var otpWrap = el( 'div', { class: 'kbw-field kbw-otp-field' } );
		var otpInput = el( 'input', { type: 'text', inputmode: 'numeric', maxlength: '6', placeholder: '123456' } );
		var otpVal = '';
		otpInput.addEventListener( 'input', function () { otpVal = this.value; } );
		otpWrap.appendChild( otpInput );
		box.appendChild( otpWrap );

		box.appendChild( this.RenderNav( { back: true, nextLabel: I18N.payConfirm, onBack: function () {
			self.paymentStage = 'card'; self.Render();
		}, onNext: function () {
			self.VerifyOtp( otpVal );
		} } ) );

		return box;
	};

	KayanBookingWizard.prototype.ChargeCard = function ( card ) {
		var self = this;
		this.paymentStage = 'processing';
		this.Render();

		var fd = new FormData();
		fd.set( 'kb_nonce', this.lastNonce || '' );
		fd.set( 'booking_id', this.bookingResult.booking_id );
		fd.set( 'card_number', card.number );
		fd.set( 'card_name', card.name );
		fd.set( 'card_expiry', card.expiry );
		fd.set( 'card_cvv', card.cvv );

		this.WithFreshNonce( fd, function () {
			fetch( ajaxUrl( 'kayan_payment_charge_card' ), { method: 'POST', body: fd } )
				.then( function ( r ) { return r.json(); } )
				.then( function ( data ) {
					if ( data.success ) {
						self.txnRef = data.txn_ref;
						self.demoOtpHint = data.demo_otp || '';
						setTimeout( function () { self.paymentStage = 'otp'; self.Render(); }, 900 );
					} else {
						self.paymentStage = 'card';
						self.Render();
						self.ShowError( data.message || I18N.error );
					}
				} )
				.catch( function () {
					self.paymentStage = 'card';
					self.Render();
					self.ShowError( I18N.error );
				} );
		} );
	};

	KayanBookingWizard.prototype.VerifyOtp = function ( otp ) {
		var self = this;
		this.paymentStage = 'processing';
		this.Render();

		var fd = new FormData();
		fd.set( 'txn_ref', this.txnRef );
		fd.set( 'otp', otp );

		fetch( ajaxUrl( 'kayan_payment_verify_otp' ), { method: 'POST', body: fd } )
			.then( function ( r ) { return r.json(); } )
			.then( function ( data ) {
				if ( data.success ) {
					self.paymentResult = data;
					self.step = 'success';
					self.Render();
				} else {
					self.paymentStage = 'otp';
					self.Render();
					self.ShowError( data.message || I18N.error );
				}
			} )
			.catch( function () {
				self.paymentStage = 'otp';
				self.Render();
				self.ShowError( I18N.error );
			} );
	};

	KayanBookingWizard.prototype.ConfirmCash = function () {
		var self = this;
		this.paymentStage = 'processing';
		this.Render();

		var fd = new FormData();
		fd.set( 'booking_id', this.bookingResult.booking_id );

		this.WithFreshNonce( fd, function () {
			fetch( ajaxUrl( 'kayan_payment_confirm_cash' ), { method: 'POST', body: fd } )
				.then( function ( r ) { return r.json(); } )
				.then( function ( data ) {
					if ( data.success ) {
						self.paymentResult = data;
						self.step = 'success';
						self.Render();
					} else {
						self.paymentStage = 'method';
						self.Render();
						self.ShowError( data.message || I18N.error );
					}
				} )
				.catch( function () {
					self.paymentStage = 'method';
					self.Render();
					self.ShowError( I18N.error );
				} );
		} );
	};

	KayanBookingWizard.prototype.ChargeWallet = function ( walletType ) {
		var self = this;
		this.paymentStage = 'processing';
		this.Render();

		var fd = new FormData();
		fd.set( 'booking_id', this.bookingResult.booking_id );
		fd.set( 'wallet_type', walletType );

		this.WithFreshNonce( fd, function () {
			fetch( ajaxUrl( 'kayan_payment_charge_wallet' ), { method: 'POST', body: fd } )
				.then( function ( r ) { return r.json(); } )
				.then( function ( data ) {
					if ( data.success ) {
						self.paymentResult = data;
						self.step = 'success';
						self.Render();
					} else {
						self.paymentStage = 'method';
						self.Render();
						self.ShowError( data.message || I18N.error );
					}
				} )
				.catch( function () {
					self.paymentStage = 'method';
					self.Render();
					self.ShowError( I18N.error );
				} );
		} );
	};

	KayanBookingWizard.prototype.WithFreshNonce = function ( fd, cb ) {
		var self = this;
		fetch( ajaxUrl( 'kayan_booking_nonce' ) )
			.then( function ( r ) { return r.json(); } )
			.then( function ( nonceRes ) {
				self.lastNonce = nonceRes.nonce;
				fd.set( 'kb_nonce', nonceRes.nonce );
				cb();
			} )
			.catch( cb );
	};

	KayanBookingWizard.prototype.RenderSuccess = function () {
		var p = this.paymentResult || {};
		var b = this.bookingResult || {};
		var box = el( 'div', { class: 'kbw-step kbw-success' } );
		box.innerHTML =
			'<div class="kbw-success-icon"><i class="fa-solid fa-circle-check"></i></div>' +
			'<h3>' + ( p.message || b.message || I18N.success ) + '</h3>' +
			( p.booking_ref || b.booking_ref ? '<p class="kbw-ref">رقم الحجز: <strong>' + ( p.booking_ref || b.booking_ref ) + '</strong></p>' : '' ) +
			( p.invoice_number ? '<p>رقم الفاتورة: <strong>' + p.invoice_number + '</strong></p>' : '' ) +
			( p.total || b.total ? '<p>الإجمالي: <strong>' + ( p.total || b.total ) + ' ' + ( p.currency || b.currency ) + '</strong></p>' : '' ) +
			( p.invoice_url ? '<a target="_blank" class="kbw-invoice-btn" href="' + p.invoice_url + '"><i class="fa-solid fa-file-invoice"></i> ' + I18N.invoiceBtn + '</a>' : '' ) +
			( b.whatsapp_url ? '<a target="_blank" class="kbw-whatsapp-confirm" href="' + b.whatsapp_url + '"><i class="fa-brands fa-whatsapp"></i> تأكيد عبر واتساب</a>' : '' );
		return box;
	};

	// ═══════════ عناصر مشتركة ═══════════
	KayanBookingWizard.prototype.RenderNav = function ( opts ) {
		var nav = el( 'div', { class: 'kbw-nav' } );
		if ( opts.back ) {
			var backBtn = el( 'button', { type: 'button', class: 'kbw-btn kbw-btn-back' }, I18N.back );
			backBtn.addEventListener( 'click', opts.onBack );
			nav.appendChild( backBtn );
		}
		var nextBtn = el( 'button', { type: 'button', class: 'kbw-btn kbw-btn-next' }, opts.nextLabel );
		nextBtn.addEventListener( 'click', opts.onNext );
		nav.appendChild( nextBtn );
		return nav;
	};

	KayanBookingWizard.prototype.ShowError = function ( msg ) {
		var existing = this.root.querySelector( '.kbw-error' );
		if ( existing ) existing.remove();
		var e = el( 'div', { class: 'kbw-error' }, msg );
		this.root.querySelector( '.kbw-inner' ).appendChild( e );
		setTimeout( function () { e.remove(); }, 4000 );
	};

	KayanBookingWizard.prototype.SetLoading = function ( state, label ) {
		var existing = this.root.querySelector( '.kbw-loading-overlay' );
		if ( existing ) existing.remove();
		if ( state ) {
			var o = el( 'div', { class: 'kbw-loading-overlay' }, '<span class="kbw-spinner"></span>' + ( label ? '<p>' + label + '</p>' : '' ) );
			this.root.appendChild( o );
		}
	};

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.kayan-booking-wizard[data-boot]' ).forEach( function ( node ) {
			new KayanBookingWizard( node );
		} );
	} );
})();
