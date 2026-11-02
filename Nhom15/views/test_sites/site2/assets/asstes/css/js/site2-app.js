(function () {
    var $ = function (s, r) { return (r || document).querySelector(s); };
    var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
    var modal = $('#pkgModal'), current = null, cycle = 1;

    function money(n) { return n.toLocaleString('vi-VN') + 'đ'; }

    function track(type, detail) {
        try {
            var d = new URLSearchParams({ type: type, detail: detail });
            fetch('index.php?action=track', { method: 'POST', body: d, keepalive: true });
        } catch (e) { }
    }

    /* ===== Cửa sổ chi tiết gói ===== */
    function closeModal() {
        if (!modal) return;
        modal.classList.remove('open'); modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function updateLinks() {
        $('#mBuy').href = 'index.php?page=dang-ky-goi&package=' + encodeURIComponent(current) + '&cycle=' + cycle;
        $('#mConsult').href = 'index.php?page=ho-tro-ky-thuat&package=' + encodeURIComponent(current) + '#lien-he';
    }

    function renderPrice() {
        var p = window.PACKAGES[current];
        var month = p.price, year = Math.round(p.price * 12 * 0.8);
        $('#mPrice').textContent = money(cycle === 12 ? year : month);
        $('#mPer').textContent = cycle === 12 ? '/năm' : '/tháng';
        $('#mSave').textContent = cycle === 12 ? 'Tiết kiệm ' + money(month * 12 - year) + ' so với trả theo tháng.' : '';
        $$('.switch button').forEach(function (b) { b.classList.toggle('on', +b.dataset.cycle === cycle); });
        updateLinks();
    }

    function fillPkg(id) {
        var p = window.PACKAGES[id]; if (!p) return;
        current = id; cycle = 1;
        $('#mName').textContent = 'Gói ' + p.name;
        $('#mTag').textContent = p.tag;
        $('#mDesc').textContent = p.desc;
        $('#mBest').textContent = p.best;
        $('#mSpecs').innerHTML = '';
        Object.keys(p.specs).forEach(function (k) {
            var tr = document.createElement('tr'), a = document.createElement('td'), b = document.createElement('td');
            a.textContent = k; b.textContent = p.specs[k]; tr.appendChild(a); tr.appendChild(b); $('#mSpecs').appendChild(tr);
        });
        $('#mFeatures').innerHTML = '';
        p.features.forEach(function (f) { var li = document.createElement('li'); li.textContent = f; $('#mFeatures').appendChild(li); });
        $$('#mTabs button').forEach(function (t) { t.classList.toggle('on', t.dataset.tab === id); });
        renderPrice();
    }

    function openPkg(id) {
        if (!modal || !window.PACKAGES || !window.PACKAGES[id]) return;
        fillPkg(id);
        modal.classList.add('open'); modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        track('package_click', id);
    }

    $$('[data-package]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (e.target.closest('a')) return; // nút "Đăng ký" trong bảng giá: đi thẳng tới trang đăng ký
            openPkg(el.dataset.package);
        });
        el.addEventListener('keydown', function (e) {
            if (e.target === el && (e.key === 'Enter' || e.key === ' ')) { e.preventDefault(); openPkg(el.dataset.package); }
        });
    });

    if (modal) {
        modal.addEventListener('click', function (e) { if (e.target === modal || e.target.hasAttribute('data-close')) closeModal(); });
        $$('#mTabs button').forEach(function (t) {
            t.addEventListener('click', function () { fillPkg(t.dataset.tab); track('package_tab', t.dataset.tab); });
        });
        $$('.switch button').forEach(function (b) { b.addEventListener('click', function () { cycle = +b.dataset.cycle; renderPrice(); }); });
        $('#mBuy').addEventListener('click', function () { track('buy_click', current + ' / ' + cycle); });
        $('#mConsult').addEventListener('click', function () { track('consult_click', current); });
    }
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

    /* ===== Trang đăng ký & thanh toán: cập nhật tóm tắt đơn hàng ===== */
    var form = $('#orderForm');
    if (form && window.PACKAGES) {
        var calc = function () {
            var id = ($('input[name=package]:checked', form) || {}).value;
            var cy = +(($('input[name=cycle]:checked', form) || {}).value || 1);
            var p = window.PACKAGES[id]; if (!p) return;
            var base = p.price * cy, disc = cy === 12 ? Math.round(base * 0.2) : 0;
            var sub = base - disc, vat = Math.round(sub * 0.1);
            $('#sPkg').textContent = p.name;
            $('#sCycle').textContent = cy === 12 ? '12 tháng' : '1 tháng';
            $('#sBase').textContent = money(base);
            $('#sDiscRow').hidden = !disc;
            $('#sDisc').textContent = '-' + money(disc);
            $('#sVat').textContent = money(vat);
            $('#sTotal').textContent = money(sub + vat);
        };
        $$('input[name=package], input[name=cycle]', form).forEach(function (i) { i.addEventListener('change', calc); });
        form.addEventListener('submit', function () {
            var m = ($('input[name=method]:checked', form) || {}).value;
            track('checkout_submit', (($('input[name=package]:checked', form) || {}).value) + ' / ' + m);
        });
        calc();
    }

    /* ===== Menu mobile + FAQ ===== */
    var burger = $('#burger'); if (burger) burger.addEventListener('click', function () { $('#menu').classList.toggle('show'); });

    $$('.faq-q').forEach(function (q) {
        q.addEventListener('click', function () {
            var f = q.parentNode, open = f.classList.toggle('open');
            $('span', q).textContent = open ? '−' : '+';
        });
    });
})();