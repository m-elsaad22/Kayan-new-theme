(function () {
  'use strict';
  var KT = window.KayanTracker || {};

  function mkFP() {
    var s = navigator.userAgent + screen.width + screen.height + navigator.language;
    var h = 0;
    for (var i = 0; i < s.length; i++) { h = ((h << 5) - h) + s.charCodeAt(i); h |= 0; }
    return 'kfp' + (h >>> 0).toString(16) + Date.now().toString(36).slice(-4);
  }
  var FP  = localStorage.getItem('kayan_fp')  || (function(){ var f=mkFP(); localStorage.setItem('kayan_fp',f); return f; })();
  var SID = sessionStorage.getItem('kayan_sid')|| (function(){ var s='ks'+Date.now().toString(36)+Math.random().toString(36).slice(2,5); sessionStorage.setItem('kayan_sid',s); return s; })();

  var UA  = navigator.userAgent;
  var DEV = {
    type:    /Mobi|Android|iPhone/i.test(UA) ? 'mobile' : /iPad|Tablet/i.test(UA) ? 'tablet' : 'desktop',
    os:      /Windows/i.test(UA) ? 'Windows' : /iPhone/.test(UA) ? 'iOS' : /Android/.test(UA) ? 'Android' : 'Other',
    browser: /Edg\//.test(UA) ? 'Edge' : /Chrome\//.test(UA) ? 'Chrome' : /Firefox\//.test(UA) ? 'Firefox' : /Safari\//.test(UA) ? 'Safari' : 'Other',
  };

  var P    = new URLSearchParams(location.search);
  var UTM  = { src: P.get('utm_source')||'', med: P.get('utm_medium')||'', camp: P.get('utm_campaign')||'' };
  var GCLD = P.get('gclid') || sessionStorage.getItem('kayan_gclid') || '';
  if (P.get('gclid')) sessionStorage.setItem('kayan_gclid', GCLD);

  function tsrc() {
    if (GCLD) return 'google_ads';
    if (UTM.med === 'cpc' || UTM.med === 'paid') return 'paid';
    if (UTM.med || UTM.src) return 'campaign';
    if (!document.referrer) return 'direct';
    var d = ''; try { d = new URL(document.referrer).hostname; } catch(e) {}
    if (/facebook|instagram|twitter|tiktok|linkedin/i.test(d)) return 'social';
    if (/google|bing|yahoo|yandex/i.test(d)) return 'organic';
    return 'referral';
  }
  var TSRC = tsrc();

  var maxScroll = 0, startTime = Date.now();
  document.addEventListener('scroll', function () {
    var p = Math.round((scrollY / (document.documentElement.scrollHeight - innerHeight || 1)) * 100);
    if (p > maxScroll) maxScroll = Math.min(p, 100);
  }, { passive: true });

  function extPhone(href) {
    if (!href) return '';
    var m = href.match(/^tel:\+?([0-9\s\-\(\)]+)/i);
    if (m) return m[1].replace(/[\s\-\(\)]/g, '');
    var w = href.match(/wa\.me\/\+?([0-9]+)/i);
    if (w) return w[1];
    return '';
  }

  function onCD(phone) {
    var k = 'kayan_cd_' + phone, t = localStorage.getItem(k);
    return t && (Date.now() - parseInt(t, 10)) < (KT.cooldown || 30) * 60000;
  }
  function setCD(phone) { localStorage.setItem('kayan_cd_' + phone, String(Date.now())); }

  function post(event, extra) {
    var body = Object.assign({
      event:        event,
      fp:           FP,
      sid:          SID,
      device:       DEV.type,
      browser:      DEV.browser,
      os:           DEV.os,
      screen:       screen.width + 'x' + screen.height,
      lang:         navigator.language || '',
      page_url:     location.href,
      page_title:   document.title,
      page_service: KT.pageService || '',
      referrer:     document.referrer,
      utm_source:   UTM.src,
      utm_medium:   UTM.med,
      utm_campaign: UTM.camp,
      traffic_src:  TSRC,
      gclid:        GCLD,
      scroll:       maxScroll,
      time:         Math.round((Date.now() - startTime) / 1000),
    }, extra || {});
    var url = KT.restUrl || '';
    if (!url) return;
    if (navigator.sendBeacon) {
      var blob = new Blob([JSON.stringify(body)], { type: 'application/json' });
      navigator.sendBeacon(url, blob);
    } else {
      fetch(url, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body), keepalive: true });
    }
  }

  function trackConv(type, phone) {
    if (phone && onCD(phone)) return;
    if (phone) setCD(phone);
    post('conversion', { click_type: type, phone: phone || '' });
  }

  document.addEventListener('click', function (e) {
    var a = e.target.closest('a');
    if (!a) return;
    var h = a.href || '';
    if (/^tel:/i.test(h)) trackConv('call', extPhone(h));
    else if (/wa\.me|whatsapp\.com/i.test(h)) trackConv('whatsapp', extPhone(h));
  }, { passive: true });

  document.addEventListener('submit', function () { trackConv('form', ''); }, { passive: true });

  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-track]');
    if (!el) return;
    trackConv(el.getAttribute('data-track') || 'cta', el.getAttribute('data-phone') || '');
  }, { passive: true });

  setTimeout(function () { post('visit'); }, 500);

  function endSession() { post('session_end', { duration: Math.round((Date.now() - startTime) / 1000), scroll: maxScroll }); }
  window.addEventListener('pagehide', endSession);
  window.addEventListener('beforeunload', endSession);

  var hq = [], hTimer;
  document.addEventListener('click', function (e) {
    var xp = Math.round(e.clientX / innerWidth * 100);
    var yp = Math.round((e.clientY + scrollY) / Math.max(document.body.scrollHeight, 1) * 100);
    hq.push({ x: xp, y: yp, el: (e.target.tagName || '').slice(0, 40) });
    clearTimeout(hTimer);
    hTimer = setTimeout(function () {
      if (!hq.length) return;
      post('heatmap', { points: JSON.stringify(hq) });
      hq = [];
    }, 3000);
  }, { passive: true });

  if (KT.dniEnabled) {
    fetch((KT.dniUrl || '') + '?src=' + encodeURIComponent(TSRC) + '&utm_source=' + encodeURIComponent(UTM.src) + '&utm_campaign=' + encodeURIComponent(UTM.camp))
      .then(function (r) { return r.json(); })
      .then(function (d) {
        if (!d || !d.phone) return;
        document.querySelectorAll('a[href^="tel:"]').forEach(function (a) {
          a.href = 'tel:+' + d.phone;
          if (/[0-9]/.test(a.textContent)) a.textContent = '+' + d.phone;
        });
        if (d.wa_number) {
          document.querySelectorAll('a[href*="wa.me"]').forEach(function (a) {
            a.href = a.href.replace(/wa\.me\/\+?[0-9]+/, 'wa.me/' + d.wa_number);
          });
        }
      }).catch(function () {});
  }
}());
