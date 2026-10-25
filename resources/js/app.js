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
   ============================================================ */
(function () {
    const wrap = document.getElementById('custom-scrollbar');
    const thumb = wrap ? wrap.querySelector('.cs-thumb') : null;
    if (!wrap || !thumb) {
        return;
    }

    let dragging = false;
    let startY = 0;
    let startScrollY = 0;

    const doc = document.documentElement;

    /* Posisi scroll Y dari posisi Y thumb (saat drag) */
    const scrollYFromThumb = (clientY) => {
        const wrapRect = wrap.getBoundingClientRect();
        const thumbH = thumb.offsetHeight || 60;
        const trackTop = 8;
        const travel = wrapRect.height - trackTop - 8 - thumbH;
        const offsetInTrack = clientY - wrapRect.top - trackTop - thumbH / 2;
        const progress = Math.min(1, Math.max(0, offsetInTrack / Math.max(1, travel)));
        const maxScroll = doc.scrollHeight - window.innerHeight;
        return progress * Math.max(0, maxScroll);
    };

    /* Posisi thumb dari scroll Y saat ini (sinkron saat halaman scroll) */
    const syncFromScroll = () => {
        const maxScroll = doc.scrollHeight - window.innerHeight;
        const progress = maxScroll > 0 ? window.scrollY / maxScroll : 0;
        const wrapRect = wrap.getBoundingClientRect();
        const thumbH = thumb.offsetHeight || 60;
        const trackTop = 8;
        const travel = wrapRect.height - trackTop - 8 - thumbH;
        thumb.style.top = `${trackTop + progress * Math.max(0, travel)}px`;
    };

    /* --- Drag mulai (mouse & sentuh) --- */
    const startDrag = (clientY) => {
        dragging = true;
        startY = clientY;
        startScrollY = window.scrollY;
        thumb.style.transition = 'none';
    };

    thumb.addEventListener('mousedown', (e) => {
        e.preventDefault();
        startDrag(e.clientY);
    });
    thumb.addEventListener('touchstart', (e) => {
        startDrag(e.touches[0].clientY);
    }, { passive: true });

    /* --- Gerak: hitung delta dari posisi awal drag --- */
    const moveDrag = (clientY) => {
        if (!dragging) {
            return;
        }
        const wrapRect = wrap.getBoundingClientRect();
        const thumbH = thumb.offsetHeight || 60;
        const trackTop = 8;
        const travel = wrapRect.height - trackTop - 8 - thumbH;
        const maxScroll = doc.scrollHeight - window.innerHeight;
        const deltaY = clientY - startY;
        const progress = Math.min(1, Math.max(0, (startScrollY / Math.max(1, maxScroll)) + (deltaY / Math.max(1, travel))));
        window.scrollTo({ top: progress * Math.max(0, maxScroll), behavior: 'auto' });
    };

    window.addEventListener('mousemove', (e) => moveDrag(e.clientY));
    window.addEventListener('touchmove', (e) => moveDrag(e.touches[0].clientY), { passive: true });

    /* --- Drag selesai --- */
    const endDrag = () => {
        dragging = false;
        thumb.style.transition = '';
        syncFromScroll();
    };
    window.addEventListener('mouseup', endDrag);
    window.addEventListener('touchend', endDrag);
    window.addEventListener('touchcancel', endDrag);

    /* --- Tap/klik pada jalur (di luar thumb): lompat ke posisi itu --- */
    wrap.addEventListener('mousedown', (e) => {
        if (e.target === thumb || thumb.contains(e.target)) {
            return;
        }
        window.scrollTo({ top: scrollYFromThumb(e.clientY), behavior: 'smooth' });
    });
    wrap.addEventListener('touchstart', (e) => {
        if (e.target === thumb || thumb.contains(e.target)) {
            return;
        }
        window.scrollTo({ top: scrollYFromThumb(e.touches[0].clientY), behavior: 'smooth' });
    }, { passive: true });

    /* Selalu sinkronkan thumb saat halaman discroll lewat cara lain */
    window.addEventListener('scroll', syncFromScroll, { passive: true });
    window.addEventListener('resize', syncFromScroll);
})();
