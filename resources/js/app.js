// Dropdown provinsi dinamis di halaman register
const islandSelect = document.getElementById('island_id');
const provinceSelect = document.getElementById('province_id');

if (islandSelect && provinceSelect) {
    const allOptions = [...provinceSelect.options];

    const filterProvinces = () => {
        const islandId = islandSelect.value;

        allOptions.forEach((option) => {
            if (!option.value) {
                return;
            }
            option.hidden = option.dataset.parent !== islandId;
        });

        provinceSelect.value = '';
    };

    islandSelect.addEventListener('change', filterProvinces);

    // Restore pilihan setelah validation error
    const oldIsland = islandSelect.getAttribute('data-old');
    if (oldIsland) {
        islandSelect.value = oldIsland;
        filterProvinces();
        const oldProvince = provinceSelect.getAttribute('data-old');
        if (oldProvince) {
            provinceSelect.value = oldProvince;
        }
    }
}


/* ============================================================
   SMOOTH SCROLL (lerp)
   Roda mouse & keyboard tidak menempel langsung ke halaman, tetapi
   menggerakkan "target" yang dikejar posisi nyata secara perlahan.

   PENGATURAN (ubah bila perlu):
     SMOOTHNESS : 0.075 -> makin KECIL makin LAMBAT & halus
     WHEEL_STEP : pengali jarak satu putaran roda
     KEY_STEP   : porsi tinggi layar untuk PageUp/PageDown/Spasi

   Prinsip keamanan:
   - Tidak pernah mencegat scroll bila halaman memang tidak bisa
     di-scroll, bila kursor di atas elemen yang punya scroll sendiri,
     atau bila itu scroll horizontal (trackpad).
   - Scroll dari sumber lain (scrollbar native, anchor #, tombol
     Back/Forward, skrip lain) SELALU dihormati: animasi berhenti
     dan posisinya diikuti.
   - `prefers-reduced-motion` -> tidak aktif sama sekali.
   ============================================================ */
(function () {
    'use strict';

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    // Perangkat sentuh punya momentum bawaan — biarkan native.
    if (window.matchMedia('(hover: none) and (pointer: coarse)').matches) {
        return;
    }

    var SMOOTHNESS = 0.075;
    var WHEEL_STEP = 1.0;
    var KEY_STEP = 0.85;

    var target = window.scrollY;      // posisi yang dituju
    var current = window.scrollY;     // posisi yang sedang ditampilkan
    var applied = window.scrollY;     // nilai terakhir yang kita kirim ke browser
    var maxScroll = 0;
    var raf = null;
    var running = false;

    /* Halaman memakai `scroll-behavior: smooth` di CSS. Itu akan membuat
       setiap scrollTo() kita ikut dianimasikan CSS sehingga bertabrakan
       dengan lerp. Karena kita yang mengatur animasi, matikan. */
    document.documentElement.style.scrollBehavior = 'auto';

    function refreshMax() {
        maxScroll = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
    }

    function clamp(v) {
        return Math.min(maxScroll, Math.max(0, v));
    }

    function applyScroll(y) {
        applied = y;
        window.scrollTo(0, y);
    }

    function stop() {
        if (raf !== null) {
            cancelAnimationFrame(raf);
            raf = null;
        }
        running = false;
    }

    /* Resync ke posisi halaman saat ini (dipakai setelah scroll eksternal). */
    function resync() {
        target = window.scrollY;
        current = window.scrollY;
        applied = window.scrollY;
    }

    /* Apakah node berada di dalam elemen yang punya scroll sendiri?
       (dropdown, panel, dsb — jangan diambil alih) */
    function ownsScroll(node) {
        if (!(node instanceof Element)) {
            return false;
        }

        for (var el = node; el && el !== document.body && el !== document.documentElement; el = el.parentElement) {
            var style = window.getComputedStyle(el);

            if ((style.overflowY === 'auto' || style.overflowY === 'scroll')
                && el.scrollHeight > el.clientHeight + 1) {
                return true;
            }
        }

        return false;
    }

    function tick() {
        /* Deteksi lompatan eksternal yang terjadi SEBELUM event 'scroll'
           sempat menyala (mis. user men-drag scrollbar native). Tanpa ini
           animasi akan menarik halaman kembali ke posisi lama. */
        if (Math.abs(window.scrollY - applied) > 3) {
            stop();
            resync();

            return;
        }

        current += (target - current) * SMOOTHNESS;

        // Sudah sangat dekat -> hentikan agar tidak bergetar.
        if (Math.abs(target - current) < 0.4) {
            current = target;
            running = false;
            raf = null;
            applyScroll(clamp(current));

            return;
        }

        applyScroll(clamp(current));
        raf = requestAnimationFrame(tick);
    }

    function start() {
        if (running) {
            return;
        }
        running = true;
        raf = requestAnimationFrame(tick);
    }

    function nudge(delta) {
        refreshMax();
        target = clamp(target + delta);
        start();
    }

    /* ---------- Roda mouse / trackpad ---------- */
    window.addEventListener('wheel', function (e) {
        // Zoom (ctrl+wheel), sudah dicegat pihak lain, atau elemen ber-scroll sendiri.
        if (e.ctrlKey || e.defaultPrevented || ownsScroll(e.target)) {
            return;
        }

        // Scroll horizontal (trackpad) -> biarkan native.
        if (Math.abs(e.deltaX) > Math.abs(e.deltaY)) {
            return;
        }

        // Halaman memang tidak bisa di-scroll -> jangan dicegat.
        refreshMax();
        if (maxScroll <= 0) {
            return;
        }

        // Mode 0 = piksel, 1 = baris, 2 = halaman.
        var unit = e.deltaMode === 1 ? 16 : (e.deltaMode === 2 ? window.innerHeight : 1);

        e.preventDefault();
        nudge(e.deltaY * unit * WHEEL_STEP);
    }, { passive: false });

    /* ---------- Keyboard ---------- */
    window.addEventListener('keydown', function (e) {
        if (e.defaultPrevented || e.ctrlKey || e.metaKey || e.altKey) {
            return;
        }

        var el = e.target;
        var tag = el && el.tagName ? el.tagName.toLowerCase() : '';

        // Jangan ambil alih saat mengetik, atau saat fokus di tombol/link
        // (Spasi & Enter di sana punya arti sendiri).
        if (tag === 'input' || tag === 'textarea' || tag === 'select'
            || tag === 'button' || tag === 'a'
            || (el && el.isContentEditable)) {
            return;
        }

        refreshMax();
        if (maxScroll <= 0) {
            return;
        }

        var viewport = window.innerHeight;
        var handled = true;

        switch (e.key) {
            case 'PageDown':
                nudge(viewport * KEY_STEP);
                break;
            case 'PageUp':
                nudge(-viewport * KEY_STEP);
                break;
            case ' ':
                nudge(e.shiftKey ? -viewport * KEY_STEP : viewport * KEY_STEP);
                break;
            case 'Home':
                target = 0;
                start();
                break;
            case 'End':
                target = maxScroll;
                start();
                break;
            case 'ArrowDown':
                nudge(110);
                break;
            case 'ArrowUp':
                nudge(-110);
                break;
            default:
                handled = false;
        }

        if (handled) {
            e.preventDefault();
        }
    }, { passive: false });

    /* ---------- Scroll dari sumber lain ----------
       Scrollbar native (drag/klik), anchor #, Back/Forward, atau skrip
       lain. Selama animasi kita berjalan, event yang berasal dari kita
       sendiri diabaikan (scrollY == applied). */
    window.addEventListener('scroll', function () {
        var external = Math.abs(window.scrollY - applied) > 3;

        if (!external) {
            return;
        }

        stop();
        resync();
    }, { passive: true });

    window.addEventListener('resize', function () {
        refreshMax();
        target = clamp(target);
        current = clamp(current);
        applied = window.scrollY;
    });

    /* Tinggi halaman berubah saat gambar lazy-load / animasi reveal. */
    if (typeof ResizeObserver !== 'undefined') {
        new ResizeObserver(function () {
            refreshMax();
        }).observe(document.body);
    }

    refreshMax();

    window.addEventListener('pagehide', stop);
})();
