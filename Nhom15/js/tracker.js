// js/tracker.js
(function () {
    window.WebTracker = {
        init: function (config) {
            this.websiteId = config.websiteId;
            this.apiEndpoint = config.apiEndpoint || '../handle/api_track.php';
            this.sessionToken = this.getSessionToken();

            // Tự động ghi nhận lượt xem trang (Pageview) khi tải trang
            this.trackEvent('pageview', {
                title: document.title,
                url: window.location.href
            });

            this.bindEvents();
        },

        getSessionToken: function () {
            let token = sessionStorage.getItem('web_analytics_session');
            if (!token) {
                token = 'sess_' + Math.random().toString(36).substring(2) + Date.now();
                sessionStorage.setItem('web_analytics_session', token);
            }
            return token;
        },

        generateEventId: function () {
            return 'evt_' + Math.random().toString(36).substring(2) + Date.now();
        },

        trackEvent: function (eventType, payload = {}) {
            // Không thu nội dung trường nhập, mật khẩu hoặc IP đầy đủ (Yêu cầu an toàn)
            const sanitizedPayload = {
                ...payload,
                screen_resolution: window.innerWidth + 'x' + window.innerHeight
            };

            const data = {
                website_id: this.websiteId,
                session_token: this.sessionToken,
                event_id_unique: this.generateEventId(),
                event_type: eventType,
                page_url: window.location.href,
                payload_data: sanitizedPayload,
                timestamp: new Date().toISOString().slice(0, 19).replace('T', ' ')
            };

            fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data),
                // Giữ yêu cầu chạy tiếp ngay cả khi người dùng bấm link và chuyển sang trang khác
                keepalive: true
            })
                .then(response => response.json())
                .then(result => {
                    console.log('Tracking Success [' + eventType + ']:', result);
                    // Bắn sự kiện ra ngoài để giao diện cập nhật nhật ký trực tiếp
                    window.dispatchEvent(new CustomEvent('tracker:event', {
                        detail: { eventType: eventType, data: data, response: result }
                    }));
                })
                .catch(error => {
                    console.error('Tracking Error [' + eventType + ']:', error);
                });
        },

        bindEvents: function () {
            // 1. Tự động bắt và phân loại thông minh cú nhấp chuột vào nút bấm & liên kết
            document.addEventListener('click', (e) => {
                const target = e.target.closest('button, .btn, .track-click, a.track-link, [data-track-label], input[type="button"], input[type="submit"]');
                if (!target) return;

                // Nhãn tùy chỉnh (data-track-label) và tên sản phẩm (data-product) nếu có.
                // Ví dụ: <div data-product="Giày Nike"> ... <a class="btn">Xem chi tiết</a> </div>
                // sẽ được ghi nhận là "Xem chi tiết: Giày Nike".
                const customLabel = (target.getAttribute('data-track-label') || '').trim();
                const productEl = target.closest('[data-product]');
                const productName = productEl ? (productEl.getAttribute('data-product') || '').trim() : '';

                const rawText = (target.innerText || target.value || target.getAttribute('aria-label') || target.id || '').trim();
                const text = (customLabel || rawText).toLowerCase();
                const customEvent = target.getAttribute('data-event');
                const elementId = target.id || 'button_' + Math.random().toString(36).substring(7);

                let eventType = 'click_button';

                // Phân loại chuẩn xác: Tìm kiếm, Đăng ký, Tải về, Gửi form
                if (customEvent) {
                    eventType = customEvent;
                } else if (text.includes('tìm') || text.includes('search') || target.closest('form[role="search"], .search-box, .search-form')) {
                    eventType = 'search';
                } else if (text.includes('đăng ký') || text.includes('register') || text.includes('bắt đầu') || text.includes('start') || text.includes('sign up')) {
                    eventType = 'start';
                } else if (text.includes('tải') || text.includes('download')) {
                    eventType = 'download';
                } else if (target.type === 'submit' || text.includes('gửi') || text.includes('submit') || text.includes('hoàn tất')) {
                    eventType = 'submit';
                }

                // Tên thao tác: nhãn nút, kèm tên sản phẩm nếu nút nằm trong khối có data-product
                let elementText = (customLabel || target.innerText || target.value || target.id || 'Nút bấm').trim();
                if (productName) {
                    elementText += ': ' + productName;
                }

                const payload = {
                    element_text: elementText,
                    element_id: elementId,
                    element_tag: target.tagName.toLowerCase()
                };
                if (productName) {
                    payload.product_name = productName;
                }

                this.trackEvent(eventType, payload);
            });

            // 2. Bắt sự kiện gửi biểu mẫu (Form submit)
            document.addEventListener('submit', (e) => {
                const form = e.target;
                const formId = form.id || form.name || 'form_' + Math.random().toString(36).substring(7);
                const isSearch = form.getAttribute('role') === 'search' || form.classList.contains('search-form');

                this.trackEvent(isSearch ? 'search' : 'submit', {
                    form_id: formId,
                    action_name: isSearch ? 'Tìm kiếm thông tin' : 'Gửi biểu mẫu dữ liệu'
                });
            });
        }
    };
})();