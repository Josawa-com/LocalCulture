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
   SCROLLBAR KUSTOM BERGAMBAR
   Scrollbar asli disembunyikan via CSS; elemen #custom-scrollbar
   menggantikannya: thumb berisi gambar (public/gambar/scrollbar.png)
   bergerak mengikuti scroll, persis perilaku thumb asli.
   ============================================================ */
(function () {
    // Sisipkan markup scrollbar kustom sekali di akhir body
    if (document.getElementById('custom-scrollbar')) {
        return;
    }

    const wrap = document.createElement('div');
    wrap.id = 'custom-scrollbar';
    wrap.setAttribute('aria-hidden', 'true');
    wrap.innerHTML = `
        <div class="cs-track"></div>
        <div class="cs-thumb">
            <img src="/gambar/Scrollbar.png" alt="">
        </div>
    `;
    document.body.appendChild(wrap);

    const thumb = wrap.querySelector('.cs-thumb');

    let ticking = false;

    const update = () => {
        const doc = document.documentElement;
        const maxScroll = doc.scrollHeight - window.innerHeight;

        if (maxScroll <= 0) {
            thumb.style.top = '0px';
            return;
        }

        const progress = window.scrollY / maxScroll;
        const trackTop = 8;                                   /* sama dgn .cs-track top */
        const trackBottom = 8;                                /* sama dgn .cs-track bottom */
        const travel = window.innerHeight - trackTop - trackBottom - thumb.offsetHeight;

        thumb.style.top = `${trackTop + progress * Math.max(0, travel)}px`;
    };

    const onScroll = () => {
        if (!ticking) {
            ticking = true;
            requestAnimationFrame(() => {
                update();
                ticking = false;
            });
        }
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll);

    // Posisi awal (setelah gambar termuat agar tinggi thumb akurat)
    if (thumb.querySelector('img')) {
        thumb.querySelector('img').addEventListener('load', update, { once: true });
    }
    update();
})();

/* ============================================================
   DRAG SCROLLBAR KUSTOM: thumb bisa di-drag (mouse & sentuh)
   untuk menggulir halaman — meniru perilaku scrollbar asli.
   Juga tap/klik pada jalur = lompat ke posisi tersebut.
   ARSITEKTUR BEBAS-THRASH:
   - Geometri di-cache; measureGeo hanya saat init/resize/load/
     drag-start — TIDAK di dalam event scroll.
   - Event scroll HANYA memperbarui transform thumb (murah).
   - Saat drag, thumb di-set langsung di moveDrag (bukan lewat
     reaksi event scroll) — satu sumber kebenaran, tanpa loop.
   ============================================================ */
(function () {
    const wrap = document.getElementById('custom-scrollbar');
    const thumb = wrap ? wrap.querySelector('.cs-thumb') : null;
    if (!wrap || !thumb) {
        return;
    }

    const doc = document.documentElement;

    let dragging = false;
    let startY = 0;
    let startScrollY = 0;

    /* Geometri ter-cache — di-refresh hanya pada momen langka */
    let geo = { trackTop: 8, travel: 100, maxScroll: 0, thumbH: 120 };

    const measureGeo = () => {
        const wrapRect = wrap.getBoundingClientRect();
        const thumbH = thumb.offsetHeight || 120;
        const trackTop = 8;
        const maxScroll = Math.max(0, doc.scrollHeight - window.innerHeight);
        const travel = Math.max(1, wrapRect.height - trackTop - 8 - thumbH);
        geo = { trackTop, travel, maxScroll, thumbH };
    };

    /* Murah: hanya transform — tanpa layout read.
       progress DI-CLAMP 0..1 supaya thumb tidak pernah keluar layar
       (penyebab "scrollbar tenggelam" saat tinggi halaman berubah). */
    const setThumbFromScrollY = (scrollY) => {
        const progress = geo.maxScroll > 0
            ? Math.min(1, Math.max(0, scrollY / geo.maxScroll))
            : 0;
        thumb.style.transform = `translateY(${geo.trackTop + progress * geo.travel}px)`;
    };

    /* --- Drag mulai (mouse & sentuh) --- */
    const startDrag = (clientY) => {
        dragging = true;
        startY = clientY;
        startScrollY = window.scrollY;
        measureGeo(); /* cache SEKALI di awal drag */
        setThumbFromScrollY(window.scrollY);
    };

    thumb.addEventListener('mousedown', (e) => {
        e.preventDefault();
        startDrag(e.clientY);
    });
    thumb.addEventListener('touchstart', (e) => {
        startDrag(e.touches[0].clientY);
    }, { passive: true });

    /* --- Gerak drag: scrollTo + update thumb. TANPA layout read. --- */
    const moveDrag = (clientY) => {
        if (!dragging) {
            return;
        }
        const deltaY = clientY - startY;
        const progress = Math.min(1, Math.max(0, (startScrollY / geo.maxScroll) + (deltaY / geo.travel)));
        const top = progress * geo.maxScroll;
        window.scrollTo(0, top);            /* halaman ikut */
        setThumbFromScrollY(top);           /* thumb langsung posisi (tanpa nunggu event scroll) */
    };

    window.addEventListener('mousemove', (e) => moveDrag(e.clientY));
    window.addEventListener('touchmove', (e) => {
        if (dragging) e.preventDefault();
        moveDrag(e.touches[0].clientY);
    }, { passive: false });

    /* --- Drag selesai --- */
    const endDrag = () => {
        dragging = false;
    };
    window.addEventListener('mouseup', endDrag);
    window.addEventListener('touchend', endDrag);
    window.addEventListener('touchcancel', endDrag);

    /* --- Tap/klik pada jalur (di luar thumb): lompat ke posisi itu --- */
    wrap.addEventListener('mousedown', (e) => {
        if (e.target === thumb || thumb.contains(e.target)) {
            return;
        }
        const offsetInTrack = e.clientY - wrap.getBoundingClientRect().top - geo.trackTop - geo.thumbH / 2;
        const progress = Math.min(1, Math.max(0, offsetInTrack / geo.travel));
        window.scrollTo({ top: progress * geo.maxScroll, behavior: 'smooth' });
    });
    wrap.addEventListener('touchstart', (e) => {
        if (e.target === thumb || thumb.contains(e.target)) {
            return;
        }
        const offsetInTrack = e.touches[0].clientY - wrap.getBoundingClientRect().top - geo.trackTop - geo.thumbH / 2;
        const progress = Math.min(1, Math.max(0, offsetInTrack / geo.travel));
        window.scrollTo({ top: progress * geo.maxScroll, behavior: 'smooth' });
    }, { passive: true });

    /* Scroll lewat cara lain (wheel/keyboard/animasi halaman):
       cukup update transform thumb — TANPA measureGeo (anti-thrash). */
    window.addEventListener('scroll', () => {
        if (!dragging) {
            setThumbFromScrollY(window.scrollY);
        }
    }, { passive: true });

    /* Geometri di-refresh hanya pada momen langka: */
    window.addEventListener('resize', measureGeo);
    window.addEventListener('load', measureGeo);
    if (thumb.querySelector('img')) {
        thumb.querySelector('img').addEventListener('load', measureGeo, { once: true });
    }

    /* Tinggi halaman BERUBAH saat gambar lazy-load / animasi reveal.
       ResizeObserver memantau itu tanpa layout read di jalur scroll —
       inilah kunci agar thumb tidak "tenggelam" di tengah halaman. */
    if (typeof ResizeObserver !== 'undefined') {
        const ro = new ResizeObserver(() => {
            measureGeo();
            setThumbFromScrollY(window.scrollY);
        });
        ro.observe(document.body);
        ro.observe(doc);
    }

    measureGeo();
    setThumbFromScrollY(window.scrollY);
})();
