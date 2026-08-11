(function () {
  'use strict';
  var CFG = window.KayanTrackAdmin || {};
  var state = {
    tab: 'overview',
    preset: '30d',
    dateFrom: '',
    dateTo: '',
    charts: {},
    conv: { page: 1, search: '', click_type: '', device: '' }
  };

  function $(sel, ctx) { return (ctx || document).querySelector(sel); }
  function $all(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }

  function toast(msg) {
    var el = $('#kt-toast');
    if (!el) return;
    el.textContent = msg;
    el.hidden = false;
    clearTimeout(el._t);
    el._t = setTimeout(function () { el.hidden = true; }, 2800);
  }

  function ajax(action, data) {
    var body = new FormData();
    body.append('action', action);
    body.append('nonce', CFG.nonce || '');
    Object.keys(data || {}).forEach(function (k) { body.append(k, data[k]); });
    body.append('preset', state.preset);
    if (state.preset === 'custom') {
      body.append('date_from', state.dateFrom);
      body.append('date_to', state.dateTo);
    }
    return fetch(CFG.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
      .then(function (r) { return r.json(); });
  }

  function setLoading(html) {
    var c = $('#kt-content');
    if (c) c.innerHTML = html || '<div class="kt-loading"><i class="fas fa-spinner fa-spin"></i> جاري التحميل...</div>';
  }

  function destroyCharts() {
    Object.keys(state.charts).forEach(function (k) {
      if (state.charts[k]) { state.charts[k].destroy(); delete state.charts[k]; }
    });
  }

  function bindFilters() {
    $all('.qf-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        $all('.qf-btn').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        state.preset = btn.getAttribute('data-preset') || '30d';
        if (state.tab === 'conversions') state.conv.page = 1;
        loadTab();
      });
    });
    var apply = $('#kt-apply-custom');
    if (apply) {
      apply.addEventListener('click', function () {
        state.preset = 'custom';
        state.dateFrom = ($('#kt-date-from') || {}).value || '';
        state.dateTo = ($('#kt-date-to') || {}).value || '';
        $all('.qf-btn').forEach(function (b) { b.classList.remove('active'); });
        if (state.tab === 'conversions') state.conv.page = 1;
        loadTab();
      });
    }
  }

  function bindNav() {
    $all('.kt-nav-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        $all('.kt-nav-btn').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        state.tab = btn.getAttribute('data-tab') || 'overview';
        loadTab();
      });
    });
  }

  function renderOverview(data) {
    var s = data.stats || {};
    destroyCharts();
    var html = '<div class="stat-row">' +
      statBox('الإجمالي', s.total, '') +
      statBox('مكالمات', s.calls, 'green') +
      statBox('واتساب', s.whatsapp, 'wa') +
      statBox('فريدة', s.unique_fp, '') +
      statBox('مشبوهة', s.suspicious, 'red') +
      '</div><div class="charts-row">' +
      '<div class="chart-card"><div class="section-title"><i class="fas fa-chart-line"></i> التحويلات اليومية</div><canvas id="kt-line"></canvas></div>' +
      '<div class="chart-card"><div class="section-title"><i class="fas fa-chart-pie"></i> واتساب vs اتصال</div><canvas id="kt-donut"></canvas></div>' +
      '</div><div class="charts-row">' +
      '<div class="chart-card"><div class="section-title">أفضل 5 صفحات</div>' + miniTable(data.top_pages, 'page_title', 'total') + '</div>' +
      '<div class="chart-card"><div class="section-title">أفضل 5 مدن</div>' + miniTable(data.top_cities, 'city', 'total') + '</div>' +
      '</div>';
    if (data.sus_ips && data.sus_ips.length) {
      html += '<div class="chart-card"><div class="section-title">عناوين IP مشبوهة</div><table class="kt-tbl"><thead><tr><th>IP</th><th>عدد</th><th></th></tr></thead><tbody>';
      data.sus_ips.forEach(function (row) {
        html += '<tr><td data-label="IP">' + esc(row.ip) + '</td><td data-label="عدد">' + esc(row.cnt) + '</td><td><button class="kt-btn sm danger kt-block-ip" data-ip="' + esc(row.ip) + '">حظر</button></td></tr>';
      });
      html += '</tbody></table></div>';
    }
    $('#kt-content').innerHTML = html;

    var daily = data.daily || [];
    var ctx1 = $('#kt-line');
    if (ctx1 && window.Chart) {
      state.charts.line = new Chart(ctx1, {
        type: 'line',
        data: {
          labels: daily.map(function (d) { return d.day; }),
          datasets: [
            { label: 'مكالمات', data: daily.map(function (d) { return d.calls; }), borderColor: '#0056b3', tension: .3 },
            { label: 'واتساب', data: daily.map(function (d) { return d.whatsapp; }), borderColor: '#25d366', tension: .3 }
          ]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
      });
    }
    var ctx2 = $('#kt-donut');
    if (ctx2 && window.Chart) {
      state.charts.donut = new Chart(ctx2, {
        type: 'doughnut',
        data: {
          labels: ['اتصال', 'واتساب'],
          datasets: [{ data: [s.calls || 0, s.whatsapp || 0], backgroundColor: ['#0056b3', '#25d366'] }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
      });
    }
    bindBlockIp();
  }

  function statBox(lbl, val, cls) {
    return '<div class="stat-box ' + (cls || '') + '"><div class="stat-val">' + esc(val || 0) + '</div><div class="stat-lbl">' + lbl + '</div></div>';
  }

  function miniTable(rows, labelKey, valKey) {
    if (!rows || !rows.length) return '<p class="kt-loading">لا توجد بيانات</p>';
    var h = '<table class="kt-tbl"><tbody>';
    rows.forEach(function (r) {
      h += '<tr><td>' + esc(r[labelKey] || '-') + '</td><td><strong>' + esc(r[valKey] || 0) + '</strong></td></tr>';
    });
    return h + '</tbody></table>';
  }

  function typeBadge(type) {
    var t = String(type || '').toLowerCase();
    if (t === 'whatsapp' || t === 'wa' || t === 'tsapp') {
      return '<span class="kt-badge kt-badge-wa"><i class="fab fa-whatsapp"></i> whatsapp</span>';
    }
    if (t === 'call' || t === 'phone') {
      return '<span class="kt-badge kt-badge-call"><i class="fas fa-phone"></i> call</span>';
    }
    if (t === 'form') {
      return '<span class="kt-badge kt-badge-form"><i class="fas fa-wpforms"></i> form</span>';
    }
    return '<span class="kt-badge">' + esc(type || '-') + '</span>';
  }

  function sourceBadge(src) {
    var s = String(src || 'direct').toLowerCase();
    var cls = 'kt-src-direct';
    if (s === 'organic') cls = 'kt-src-organic';
    else if (s === 'referral') cls = 'kt-src-referral';
    else if (s === 'social') cls = 'kt-src-social';
    else if (s === 'paid' || s === 'google_ads' || s === 'campaign') cls = 'kt-src-paid';
    return '<span class="kt-src ' + cls + '">' + esc(s) + '</span>';
  }

  function deviceCell(r) {
    var dev = r.device_type || '-';
    var br = r.browser || '';
    var icon = 'fa-desktop';
    if (dev === 'mobile') icon = 'fa-mobile-alt';
    else if (dev === 'tablet') icon = 'fa-tablet-alt';
    return '<span class="kt-device"><i class="fas ' + icon + '"></i> ' + esc(dev) + (br ? ' <small>' + esc(br) + '</small>' : '') + '</span>';
  }

  function pageCell(r) {
    var title = r.page_title || '-';
    var url = r.page_url || '';
    var short = url;
    try {
      if (url) {
        var u = new URL(url, (CFG.homeUrl || location.origin));
        short = (u.hostname + u.pathname).replace(/\/$/, '');
        if (short.length > 42) short = short.slice(0, 40) + '…';
      }
    } catch (e) {
      if (short.length > 42) short = short.slice(0, 40) + '…';
    }
    return '<div class="kt-page-cell"><strong>' + esc(title) + '</strong>' +
      (url ? '<a class="kt-page-url" href="' + esc(url) + '" target="_blank" rel="noopener">' + esc(short) + '</a>' : '') +
      '</div>';
  }

  function renderConversions(data) {
    var rows = data.rows || [];
    var total = data.total || 0;
    var page = data.page || 1;
    var pages = data.pages || 1;
    var html = '<div class="kt-toolbar kt-conv-toolbar">' +
      '<input type="search" class="kt-search" id="kt-conv-search" placeholder="IP / مدينة / صفحة" value="' + esc(state.conv.search || '') + '">' +
      '<select id="kt-conv-type">' +
        '<option value="">كل الأنواع</option>' +
        '<option value="call"' + (state.conv.click_type === 'call' ? ' selected' : '') + '>اتصال</option>' +
        '<option value="whatsapp"' + (state.conv.click_type === 'whatsapp' ? ' selected' : '') + '>واتساب</option>' +
        '<option value="form"' + (state.conv.click_type === 'form' ? ' selected' : '') + '>نموذج</option>' +
      '</select>' +
      '<select id="kt-conv-device">' +
        '<option value="">كل الأجهزة</option>' +
        '<option value="mobile"' + (state.conv.device === 'mobile' ? ' selected' : '') + '>موبايل</option>' +
        '<option value="desktop"' + (state.conv.device === 'desktop' ? ' selected' : '') + '>سطح المكتب</option>' +
        '<option value="tablet"' + (state.conv.device === 'tablet' ? ' selected' : '') + '>تابلت</option>' +
      '</select>' +
      '<button type="button" class="kt-btn" id="kt-conv-apply"><i class="fas fa-filter"></i> تطبيق</button>' +
      '<button type="button" class="kt-btn ghost" id="kt-export-excel"><i class="fas fa-file-excel"></i> Excel</button>' +
      '<button type="button" class="kt-btn ghost" id="kt-export-csv"><i class="fas fa-file-csv"></i> CSV</button>' +
      '<span class="kt-total">إجمالي: <strong>' + esc(total) + '</strong> تحويل</span>' +
      '</div>';

    if (!rows.length) {
      html += '<div class="kt-empty">لا توجد تحويلات في هذه الفترة</div>';
    } else {
      html += '<div class="kt-table-wrap"><table class="kt-tbl kt-conv-tbl"><thead><tr>' +
        '<th>التحويل</th><th>المصدر</th><th>الجهاز / المتصفح</th><th>المدينة</th><th>IP</th><th>الصفحة</th><th>الوقت</th><th></th>' +
        '</tr></thead><tbody>';
      rows.forEach(function (r) {
        html += '<tr class="' + (r.is_suspicious === '1' || r.is_suspicious === 1 ? 'suspicious' : '') + '">' +
          '<td data-label="التحويل">' + typeBadge(r.click_type) + '</td>' +
          '<td data-label="المصدر">' + sourceBadge(r.traffic_src) + '</td>' +
          '<td data-label="الجهاز">' + deviceCell(r) + '</td>' +
          '<td data-label="المدينة">' + esc(r.city || '-') + '</td>' +
          '<td data-label="IP" class="kt-ip-cell"><span dir="ltr">' + esc(r.ip || '-') + '</span> ' +
            '<button type="button" class="kt-icon-ban kt-block-ip" data-ip="' + esc(r.ip) + '" title="حظر IP"><i class="fas fa-ban"></i></button></td>' +
          '<td data-label="الصفحة">' + pageCell(r) + '</td>' +
          '<td data-label="الوقت" dir="ltr">' + esc(r.created_at || '') + '</td>' +
          '<td data-label=""><i class="fas fa-bolt kt-bolt" title="تحويل"></i></td>' +
          '</tr>';
      });
      html += '</tbody></table></div>';
    }

    html += '<div class="kt-pager">' + renderPager(page, pages) + '</div>';
    $('#kt-content').innerHTML = html;
    bindBlockIp();
    bindConversionControls(page);
  }

  function renderPager(page, pages) {
    if (pages <= 1) return '<span class="kt-page-info">صفحة ' + page + ' / ' + pages + '</span>';
    var h = '';
    var start = Math.max(1, page - 2);
    var end = Math.min(pages, start + 4);
    start = Math.max(1, end - 4);
    if (page > 1) h += '<button type="button" class="kt-page-btn" data-page="' + (page - 1) + '">‹</button>';
    for (var i = start; i <= end; i++) {
      h += '<button type="button" class="kt-page-btn' + (i === page ? ' active' : '') + '" data-page="' + i + '">' + i + '</button>';
    }
    if (page < pages) h += '<button type="button" class="kt-page-btn" data-page="' + (page + 1) + '">›</button>';
    return h;
  }

  function bindConversionControls() {
    var search = $('#kt-conv-search');
    var type = $('#kt-conv-type');
    var device = $('#kt-conv-device');
    var apply = $('#kt-conv-apply');

    function readFilters() {
      state.conv.search = search ? search.value.trim() : '';
      state.conv.click_type = type ? type.value : '';
      state.conv.device = device ? device.value : '';
    }

    if (apply) {
      apply.addEventListener('click', function () {
        readFilters();
        state.conv.page = 1;
        loadConversions(1);
      });
    }
    if (search) {
      search.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          readFilters();
          state.conv.page = 1;
          loadConversions(1);
        }
      });
    }
    if (type) {
      type.addEventListener('change', function () {
        readFilters();
        state.conv.page = 1;
        loadConversions(1);
      });
    }
    if (device) {
      device.addEventListener('change', function () {
        readFilters();
        state.conv.page = 1;
        loadConversions(1);
      });
    }

    $all('.kt-page-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var p = parseInt(btn.getAttribute('data-page'), 10) || 1;
        state.conv.page = p;
        loadConversions(p);
      });
    });

    function downloadExport(filename) {
      ajax('kayan_track_export_csv', {
        click_type: state.conv.click_type || '',
        device: state.conv.device || '',
        search: state.conv.search || ''
      }).then(function (res) {
        if (!res.success) return toast('فشل التصدير');
        var blob = new Blob(['\uFEFF' + res.data.csv], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = filename;
        a.click();
      });
    }

    var exp = $('#kt-export-csv');
    if (exp) exp.addEventListener('click', function () { downloadExport('kayan-conversions.csv'); });
    var excel = $('#kt-export-excel');
    if (excel) excel.addEventListener('click', function () { downloadExport('kayan-conversions.xls'); });
  }

  function loadConversions(page) {
    setLoading();
    state.conv.page = page || state.conv.page || 1;
    ajax('kayan_track_conversions', {
      page_num: state.conv.page,
      search: state.conv.search || '',
      click_type: state.conv.click_type || '',
      device: state.conv.device || ''
    }).then(function (res) {
      if (res.success) renderConversions(res.data);
      else toast('تعذّر تحميل التحويلات');
    }).catch(function () { toast('خطأ في الاتصال'); });
  }

  function renderNumbers(data) {
    var nums = data.numbers || [];
    var html = '<div class="kt-toolbar"><button class="kt-btn" id="kt-add-number"><i class="fas fa-plus"></i> إضافة رقم</button></div><div class="num-grid">';
    nums.forEach(function (n) {
      var st = n.stats || {};
      html += '<div class="num-card" style="border-right-color:' + esc(n.color || '#0056b3') + '">' +
        '<strong>' + esc(n.label) + '</strong><br>' +
        '<span>' + esc(n.phone) + ' / WA: ' + esc(n.wa_number) + '</span><br>' +
        '<small>مكالمات: ' + esc(st.calls || 0) + ' | واتساب: ' + esc(st.whatsapp || 0) + '</small><br>' +
        '<button class="kt-btn sm ghost kt-edit-num" data-id="' + n.id + '">تعديل</button> ' +
        '<button class="kt-btn sm danger kt-del-num" data-id="' + n.id + '">حذف</button></div>';
    });
    html += '</div><div id="kt-num-modal"></div>';
    $('#kt-content').innerHTML = html;
    var add = $('#kt-add-number');
    if (add) add.addEventListener('click', function () { openNumberModal({}); });
    $all('.kt-edit-num').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var id = btn.getAttribute('data-id');
        var num = nums.filter(function (n) { return String(n.id) === String(id); })[0];
        openNumberModal(num || {});
      });
    });
    $all('.kt-del-num').forEach(function (btn) {
      btn.addEventListener('click', function () {
        if (!confirm('حذف الرقم؟')) return;
        ajax('kayan_track_number_delete', { id: btn.getAttribute('data-id') }).then(function () { loadTab(); });
      });
    });
  }

  function openNumberModal(n) {
    var m = $('#kt-num-modal');
    if (!m) return;
    m.innerHTML = '<div class="kt-modal"><div class="kt-modal-box"><h3>' + (n.id ? 'تعديل رقم' : 'رقم جديد') + '</h3>' +
      '<div class="kt-form-row"><input id="nm-label" placeholder="التسمية" value="' + esc(n.label || '') + '"></div>' +
      '<div class="kt-form-row"><input id="nm-phone" placeholder="هاتف" value="' + esc(n.phone || '') + '"></div>' +
      '<div class="kt-form-row"><input id="nm-wa" placeholder="واتساب" value="' + esc(n.wa_number || '') + '"></div>' +
      '<div class="kt-form-row"><input id="nm-color" type="color" value="' + esc(n.color || '#0056b3') + '"></div>' +
      '<div class="kt-toolbar"><button class="kt-btn" id="nm-save">حفظ</button><button class="kt-btn ghost" id="nm-cancel">إلغاء</button></div></div></div>';
    $('#nm-cancel').addEventListener('click', function () { m.innerHTML = ''; });
    $('#nm-save').addEventListener('click', function () {
      ajax('kayan_track_number_save', {
        id: n.id || 0,
        label: $('#nm-label').value,
        phone: $('#nm-phone').value,
        wa_number: $('#nm-wa').value,
        color: $('#nm-color').value,
        active: 1
      }).then(function (res) {
        if (res.success) { m.innerHTML = ''; toast('تم الحفظ'); loadTab(); }
      });
    });
  }

  function renderPages(data) {
    var rows = data.rows || [];
    var html = '<div class="kt-toolbar"><button class="kt-btn" id="kt-share-pages">مشاركة التقرير</button></div>' +
      '<table class="kt-tbl"><thead><tr><th>الصفحة</th><th>اتصال</th><th>واتساب</th><th>الإجمالي</th><th></th></tr></thead><tbody>';
    rows.forEach(function (r) {
      html += '<tr><td data-label="الصفحة">' + esc(r.page_title) + '</td>' +
        '<td data-label="اتصال">' + esc(r.calls) + '</td>' +
        '<td data-label="واتساب">' + esc(r.whatsapp) + '</td>' +
        '<td data-label="الإجمالي">' + esc(r.total) + '</td>' +
        '<td><a href="' + esc(r.page_url) + '" target="_blank" rel="noopener">رابط</a></td></tr>';
    });
    html += '</tbody></table>';
    $('#kt-content').innerHTML = html;
    var btn = $('#kt-share-pages');
    if (btn) btn.addEventListener('click', function () {
      ajax('kayan_track_generate_report', { title: 'تقرير الصفحات' }).then(function (res) {
        if (res.success) prompt('رابط التقرير:', res.data.url);
      });
    });
  }

  function renderAnalytics(data) {
    destroyCharts();
    var html = '<div class="charts-row">' +
      '<div class="chart-card"><div class="section-title">أفضل الخدمات</div><canvas id="kt-svc"></canvas></div>' +
      '<div class="chart-card"><div class="section-title">أفضل المدن</div><canvas id="kt-cities"></canvas></div></div>' +
      '<div class="charts-row">' +
      '<div class="chart-card"><div class="section-title">الأجهزة</div><canvas id="kt-dev"></canvas></div>' +
      '<div class="chart-card"><div class="section-title">مصادر الزيارات</div><canvas id="kt-src"></canvas></div></div>';
    $('#kt-content').innerHTML = html;
    barChart('#kt-svc', data.services);
    barChart('#kt-cities', data.cities, 'city');
    barChart('#kt-dev', data.devices);
    barChart('#kt-src', data.sources);
  }

  function barChart(sel, rows, labelKey) {
    labelKey = labelKey || 'name';
    var el = $(sel);
    if (!el || !window.Chart || !rows) return;
    var key = sel.replace('#', '');
    state.charts[key] = new Chart(el, {
      type: 'bar',
      data: {
        labels: rows.map(function (r) { return r[labelKey] || r.name || '-'; }),
        datasets: [{ label: 'تحويلات', data: rows.map(function (r) { return r.total; }), backgroundColor: '#0056b3' }]
      },
      options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } } }
    });
  }

  function renderVisitors(data, blacklist) {
    var vrows = (data && data.rows) || [];
    var brows = (blacklist && blacklist.rows) || [];
    var html = '<div class="kt-tabs"><button class="kt-tab active" data-vtab="visitors">الزوار</button><button class="kt-tab" data-vtab="blacklist">القائمة السوداء</button><button class="kt-tab" data-vtab="dni">DNI</button></div>' +
      '<div id="kt-vtab-content"></div>';
    $('#kt-content').innerHTML = html;
    function show(tab) {
      $all('.kt-tab').forEach(function (t) { t.classList.toggle('active', t.getAttribute('data-vtab') === tab); });
      var box = $('#kt-vtab-content');
      if (tab === 'visitors') {
        var h = '<table class="kt-tbl"><thead><tr><th>البصمة</th><th>الزيارات</th><th>الجهاز</th><th>آخر زيارة</th></tr></thead><tbody>';
        vrows.forEach(function (r) {
          h += '<tr><td data-label="البصمة">' + esc(r.fingerprint).slice(0, 12) + '…</td><td>' + esc(r.visit_count) + '</td><td>' + esc(r.device_type) + '</td><td>' + esc(r.last_visit) + '</td></tr>';
        });
        box.innerHTML = h + '</tbody></table>';
      } else if (tab === 'blacklist') {
        var h2 = '<table class="kt-tbl"><thead><tr><th>IP</th><th>السبب</th><th></th></tr></thead><tbody>';
        brows.forEach(function (r) {
          h2 += '<tr><td>' + esc(r.ip) + '</td><td>' + esc(r.reason) + '</td><td><button class="kt-btn sm ghost kt-unblock" data-ip="' + esc(r.ip) + '">رفع الحظر</button></td></tr>';
        });
        box.innerHTML = h2 + '</tbody></table>';
        bindUnblock();
      } else {
        loadDni(box);
      }
    }
    $all('.kt-tab').forEach(function (t) {
      t.addEventListener('click', function () { show(t.getAttribute('data-vtab')); });
    });
    show('visitors');
  }

  function loadDni(box) {
    box.innerHTML = '<div class="kt-loading">جاري التحميل...</div>';
    ajax('kayan_track_dni_list', {}).then(function (res) {
      if (!res.success) return;
      var rules = res.data.rules || [];
      var html = '<div class="kt-toolbar"><button class="kt-btn" id="kt-add-dni">قاعدة DNI</button></div><table class="kt-tbl"><thead><tr><th>المصدر</th><th>UTM</th><th>الرقم</th><th></th></tr></thead><tbody>';
      rules.forEach(function (r) {
        html += '<tr><td>' + esc(r.source_type) + '</td><td>' + esc(r.utm_source) + '</td><td>' + esc(r.number_label) + '</td>' +
          '<td><button class="kt-btn sm danger kt-del-dni" data-id="' + r.id + '">حذف</button></td></tr>';
      });
      html += '</tbody></table>';
      box.innerHTML = html;
      $all('.kt-del-dni').forEach(function (btn) {
        btn.addEventListener('click', function () {
          ajax('kayan_track_dni_delete', { id: btn.getAttribute('data-id') }).then(function () { loadDni(box); });
        });
      });
    });
  }

  function renderSettings(data) {
    var d = data || {};
    var sizes = d.table_sizes || {};
    $('#kt-content').innerHTML =
      '<form id="kt-settings-form" class="chart-card">' +
      '<div class="kt-form-row"><label>تعطيل التتبع</label><input type="checkbox" name="kayan_track_disable" ' + (d.kayan_track_disable ? 'checked' : '') + '></div>' +
      '<div class="kt-form-row"><label>الاحتفاظ (أيام)</label><select name="kayan_tracking_retention">' +
      [30,60,90,180,365].map(function (n) {
        return '<option value="' + n + '"' + (d.kayan_tracking_retention == n ? ' selected' : '') + '>' + n + ' يوم (~' + (sizes.conversions || 0) + ' MB conversions)</option>';
      }).join('') + '</select></div>' +
      '<div class="kt-form-row"><label>مهلة التكرار (دقائق)</label><input name="kayan_cooldown_mins" type="number" value="' + esc(d.kayan_cooldown_mins || 30) + '"></div>' +
      '<div class="kt-form-row"><label>حد الاشتباه / 24س</label><input name="kayan_fraud_threshold" type="number" value="' + esc(d.kayan_fraud_threshold || 3) + '"></div>' +
      '<div class="kt-form-row"><label>حظر تلقائي عند</label><input name="kayan_auto_blacklist" type="number" value="' + esc(d.kayan_auto_blacklist || 5) + '"></div>' +
      '<div class="kt-form-row"><label>تفعيل Telegram</label><input type="checkbox" name="kayan_telegram_on" ' + (d.kayan_telegram_on ? 'checked' : '') + '></div>' +
      '<div class="kt-form-row"><input name="kayan_telegram_token" placeholder="Bot Token" value="' + esc(d.kayan_telegram_token || '') + '"></div>' +
      '<div class="kt-form-row"><input name="kayan_telegram_chat" placeholder="Chat ID" value="' + esc(d.kayan_telegram_chat || '') + '"></div>' +
      '<div class="kt-form-row"><label>تفعيل DNI</label><input type="checkbox" name="kayan_dni_enabled" ' + (d.kayan_dni_enabled ? 'checked' : '') + '></div>' +
      '<button type="submit" class="kt-btn">حفظ الإعدادات</button></form>';
    $('#kt-settings-form').addEventListener('submit', function (e) {
      e.preventDefault();
      var fd = {};
      $all('#kt-settings-form [name]').forEach(function (el) {
        if (el.type === 'checkbox') fd[el.name] = el.checked ? '1' : '';
        else fd[el.name] = el.value;
      });
      ajax('kayan_track_settings_save', fd).then(function (res) {
        if (res.success) toast('تم حفظ الإعدادات');
      });
    });
  }

  function bindBlockIp() {
    $all('.kt-block-ip').forEach(function (btn) {
      btn.addEventListener('click', function () {
        ajax('kayan_track_block_ip', { ip: btn.getAttribute('data-ip') }).then(function (res) {
          if (res.success) toast('تم الحظر');
        });
      });
    });
  }

  function bindUnblock() {
    $all('.kt-unblock').forEach(function (btn) {
      btn.addEventListener('click', function () {
        ajax('kayan_track_unblock_ip', { ip: btn.getAttribute('data-ip') }).then(function () { loadTab(); });
      });
    });
  }

  function esc(s) {
    return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/"/g,'&quot;');
  }

  function loadTab() {
    destroyCharts();
    setLoading();
    var tab = state.tab;
    if (tab === 'overview') {
      ajax('kayan_track_overview', {}).then(function (res) { if (res.success) renderOverview(res.data); });
    } else if (tab === 'conversions') {
      loadConversions(state.conv.page || 1);
    } else if (tab === 'numbers') {
      ajax('kayan_track_numbers_list', {}).then(function (res) { if (res.success) renderNumbers(res.data); });
    } else if (tab === 'pages') {
      ajax('kayan_track_pages', {}).then(function (res) { if (res.success) renderPages(res.data); });
    } else if (tab === 'analytics') {
      ajax('kayan_track_analytics', {}).then(function (res) { if (res.success) renderAnalytics(res.data); });
    } else if (tab === 'visitors') {
      Promise.all([
        ajax('kayan_track_visitors', {}),
        ajax('kayan_track_blacklist', {})
      ]).then(function (arr) {
        renderVisitors(arr[0].success ? arr[0].data : {}, arr[1].success ? arr[1].data : {});
      });
    } else if (tab === 'settings') {
      ajax('kayan_track_settings_get', {}).then(function (res) { if (res.success) renderSettings(res.data); });
    }
  }

  bindFilters();
  bindNav();
  loadTab();
})();
