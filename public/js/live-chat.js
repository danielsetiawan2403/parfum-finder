/*
 * Chat "realtime" tanpa websocket (cocok untuk hosting gratis seperti InfinityFree).
 * Caranya polling: browser bertanya ke server tiap beberapa detik, "ada pesan baru?".
 *
 * Hemat jatah hosting:
 *  - berhenti bertanya saat tab disembunyikan, lalu langsung bertanya lagi saat tab dibuka;
 *  - satu permintaan pada satu waktu (tidak menumpuk kalau server lambat);
 *  - kalau server error, jeda makin panjang (maksimal 30 detik);
 *  - kalau sesi habis atau akses ditolak, polling berhenti.
 */
(function (window, document) {
    'use strict';

    var JSON_HEADERS = { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' };

    function withQuery(url, key, value) {
        return url + (url.indexOf('?') === -1 ? '?' : '&') + key + '=' + encodeURIComponent(value);
    }

    /**
     * Ambil pesan baru berkala dan tambahkan ke kotak chat.
     *
     * opts.box      elemen kotak chat (yang bisa di-scroll)
     * opts.pollUrl  alamat endpoint pesan baru
     * opts.render   function(pesan) -> elemen yang ditambahkan ke box
     * opts.lastId   id pesan terakhir yang sudah tampil
     * opts.interval jeda antar permintaan (ms), default 4000
     */
    function watchMessages(opts) {
        var box = opts.box;
        var base = opts.interval || 4000;
        var delay = base;
        var lastId = opts.lastId || 0;
        var seen = {};
        var timer = null;
        var busy = false;
        var fails = 0;
        var stopped = false;

        Array.prototype.forEach.call(box.querySelectorAll('[data-id]'), function (el) {
            seen[el.getAttribute('data-id')] = true;
        });

        function nearBottom() {
            return box.scrollHeight - box.scrollTop - box.clientHeight < 80;
        }

        // Tambah pesan yang belum tampil. Pesan yang sudah ada (id sama) dilewati,
        // jadi pesan yang baru saya kirim tidak dobel saat polling ikut membawanya.
        function add(list) {
            var stick = nearBottom();
            var added = false;

            list.forEach(function (m) {
                if (seen[m.id]) { return; }
                seen[m.id] = true;
                if (m.id > lastId) { lastId = m.id; }

                var el = opts.render(m);
                if (el) {
                    el.setAttribute('data-id', m.id);
                    box.appendChild(el);
                    added = true;
                }
            });

            if (added) {
                Array.prototype.forEach.call(box.querySelectorAll('[data-empty]'), function (el) {
                    el.parentNode.removeChild(el);
                });
                if (stick) { box.scrollTop = box.scrollHeight; }
            }
            return added;
        }

        function schedule() {
            if (stopped) { return; }
            clearTimeout(timer);
            timer = setTimeout(poll, delay);
        }

        function poll() {
            if (stopped) { return; }
            if (busy || document.hidden) { schedule(); return; }
            busy = true;

            fetch(withQuery(opts.pollUrl, 'after', lastId), {
                headers: JSON_HEADERS,
                credentials: 'same-origin',
                cache: 'no-store'
            }).then(function (res) {
                if (res.status === 401 || res.status === 403 || res.status === 404) {
                    stopped = true;
                    return null;
                }
                if (!res.ok) { throw new Error('HTTP ' + res.status); }
                return res.json();
            }).then(function (data) {
                busy = false;
                fails = 0;
                delay = base;
                if (data && data.messages && data.messages.length) { add(data.messages); }
                schedule();
            }).catch(function () {
                // Balasan bukan JSON (misalnya halaman tantangan dari hosting) atau koneksi putus: coba lagi nanti.
                busy = false;
                fails += 1;
                delay = Math.min(30000, base * Math.pow(2, fails));
                schedule();
            });
        }

        document.addEventListener('visibilitychange', function () {
            if (!document.hidden && !stopped) {
                clearTimeout(timer);
                poll();
            }
        });

        schedule();

        return { add: add };
    }

    /**
     * Kirim form lewat AJAX supaya halaman tidak dimuat ulang.
     * Kalau JavaScript tidak jalan, form tetap terkirim seperti biasa.
     *
     * opts.onMessage  function(pesan) dipanggil setelah pesan tersimpan
     * opts.onError    function(teks) dipanggil saat gagal (teks kosong = hapus pesan error)
     */
    function bindForm(form, opts) {
        var textarea = form.querySelector('textarea');
        var button = form.querySelector('button[type="submit"]');
        var sending = false;

        function setSending(on) {
            sending = on;
            if (button) { button.disabled = on; }
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (sending) { return; }

            if (!textarea.value.trim()) {
                opts.onError('Pesan tidak boleh kosong.');
                return;
            }

            setSending(true);

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: JSON_HEADERS,
                credentials: 'same-origin'
            }).then(function (res) {
                return res.json().catch(function () { return {}; }).then(function (data) {
                    return { ok: res.ok, status: res.status, data: data };
                });
            }).then(function (r) {
                setSending(false);

                if (r.ok && r.data.message) {
                    textarea.value = '';
                    opts.onError('');
                    opts.onMessage(r.data.message);
                    textarea.focus();
                    return;
                }

                if (r.status === 419) {
                    opts.onError('Sesi habis. Muat ulang halaman, lalu kirim lagi.');
                } else if (r.data && r.data.errors && r.data.errors.body) {
                    opts.onError(r.data.errors.body[0]);
                } else {
                    opts.onError('Pesan belum terkirim. Coba lagi.');
                }
            }).catch(function () {
                setSending(false);
                opts.onError('Koneksi bermasalah. Pesan belum terkirim.');
            });
        });

        // Ctrl+Enter (atau Cmd+Enter) mengirim pesan.
        textarea.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
                e.preventDefault();
                form.dispatchEvent(new Event('submit', { cancelable: true }));
            }
        });
    }

    /**
     * Muat ulang sebuah bagian halaman dari server berkala (dipakai untuk daftar chat admin).
     * Server mengirim potongan HTML-nya saja; DOM hanya diganti kalau isinya berubah.
     */
    function watchHtml(opts) {
        var base = opts.interval || 6000;
        var delay = base;
        var fails = 0;
        var timer = null;
        var busy = false;
        var stopped = false;
        var current = opts.target.innerHTML;

        function schedule() {
            if (stopped) { return; }
            clearTimeout(timer);
            timer = setTimeout(refresh, delay);
        }

        function refresh() {
            if (stopped) { return; }
            if (busy || document.hidden) { schedule(); return; }
            busy = true;

            fetch(opts.url, {
                headers: { 'Accept': 'text/html', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                cache: 'no-store'
            }).then(function (res) {
                if (res.status === 401 || res.status === 403) { stopped = true; return null; }
                if (!res.ok) { throw new Error('HTTP ' + res.status); }
                return res.text();
            }).then(function (html) {
                busy = false;
                fails = 0;
                delay = base;
                if (html !== null && html !== current && html.indexOf('<!DOCTYPE') === -1) {
                    opts.target.innerHTML = html;
                    current = html;
                }
                schedule();
            }).catch(function () {
                busy = false;
                fails += 1;
                delay = Math.min(30000, base * Math.pow(2, fails));
                schedule();
            });
        }

        document.addEventListener('visibilitychange', function () {
            if (!document.hidden && !stopped) {
                clearTimeout(timer);
                refresh();
            }
        });

        schedule();
    }

    window.LiveChat = {
        watchMessages: watchMessages,
        bindForm: bindForm,
        watchHtml: watchHtml
    };
})(window, document);
