{{-- ============================================================
  Komponen: Services Showcase — "THIS IS OUR THING"
  Replika section services two26.studio, tanpa dependency
  (vanilla JS, menggantikan GSAP + DrawSVGPlugin).

  Pemakaian:  <x-services-showcase />

  Kustomisasi cepat:
  - Teks heading   → cari "THIS IS OUR THING" di bawah
  - Label kecil    → "Our Services"
  - Daftar layanan → array $services di @php bawah
  - Slot tengah    → isi data-lottie="URL.json" untuk Lottie,
                     atau ganti <svg> default dengan <img>.
  ============================================================ --}}
@php
    $t26Services = [
        ['label' => 'Brand',       'mid' => 'Strategy',    'desc' => 'We find the idea, the angle and the reasons people should care.',   'side' => 'right', 'offset' => '-27%', 'icon' => 'spiral'],
        ['label' => 'Brand',       'mid' => 'Identity',    'desc' => 'We give your brand a distinctive look, feel and personality that feel unmistakably yours.', 'side' => 'left', 'offset' => '-17%', 'icon' => 'star'],
        ['label' => 'Web',         'mid' => 'Design',      'desc' => 'We turn your brand into a digital experience that looks great, feels right and is easy to navigate.', 'side' => 'right', 'offset' => '-5%', 'icon' => 'monitor'],
        ['label' => 'Illustration','',                     'desc' => 'We create distinctive illustrations that add character to your brand.', 'side' => 'right', 'offset' => '-30%', 'icon' => 'pencil'],
        ['label' => 'Motion',      'mid' => 'Design',      'desc' => 'We make your brand move, cause standing still just isn’t enough.',  'side' => 'left', 'offset' => '-10%', 'icon' => 'motion'],
        ['label' => 'Packaging',   'mid' => 'Design',      'desc' => 'We create packaging that catches the eye and works better for your brand.', 'side' => 'right', 'offset' => '-20%', 'icon' => 'box'],
        ['label' => 'Art',         'mid' => 'Direction',   'desc' => 'We keep all the moving parts moving in the same direction, from the big idea down to the smallest detail.', 'side' => 'left', 'offset' => '-21%', 'icon' => 'compass'],
    ];
@endphp

<section class="t26" id="t26-services">
    <style>
        /* ---------- Scaling system (meniru two26: semua satuan em) ---------- */
        .t26 {
            --t26-orange: #FF5400;
            --t26-black: #000;
            --t26-white: #FDF8F5;
            --t26-font-heading: 'Anton', var(--font-display, Georgia, serif);
            --t26-font-body: var(--font-sans, ui-sans-serif, system-ui, sans-serif);
            /* 16px pada desain 1440px, dibatasi 992–1920px */
            font-size: clamp(11.02px, 1.111vw, 21.33px);
        }
        @media screen and (max-width: 991px) {
            .t26 { font-size: clamp(12.26px, 1.918vw, 16.48px); }  /* ideal 834 */
        }
        @media screen and (max-width: 479px) {
            .t26 { font-size: clamp(10.66px, 4.102vw, 12.28px); }  /* ideal 390 */
        }

        .t26 * { box-sizing: border-box; margin: 0; padding: 0; }

        /* ---------- Header: label + heading raksasa ---------- */
        .t26-header {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            width: 100%;
            padding: 6em 1.125em 2.5em;
        }
        .t26-label {
            position: absolute;
            top: 6.4em;
            left: 1.125em;
            font-family: var(--t26-font-body);
            font-size: 1.125em;
            font-weight: 500;
            line-height: 100%;
            text-transform: uppercase;
            color: var(--t26-black);
        }
        .t26-heading-wrapper {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .t26-heading {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 5em;
            font-family: var(--t26-font-heading);
            font-weight: 400;
            font-size: 6.25em;
            line-height: 85%;
            text-transform: uppercase;
            color: var(--t26-black);
            pointer-events: none;
        }
        /* Salinan oranye di belakang (efek magnet dua lapis) */
        .t26-heading.is-orange {
            position: absolute;
            inset: 0;
            z-index: 1;
            margin: 0;
            color: var(--t26-orange);
            will-change: transform;
        }
        .t26-heading-wrapper:not(.t26-dual) .t26-heading.is-orange { display: none; }

        /* ---------- Body ---------- */
        .t26-body {
            position: relative;
            display: flex;
            flex-flow: column;
            align-items: center;
            width: 100%;
            min-height: 100dvh;
            max-width: 1440px;
            margin: 0 auto;
        }

        /* ---------- Sinar SVG (light di bawah, dark tergambar saat scroll) ---------- */
        .t26-path { position: relative; display: flex; width: 100%; }
        .t26-path-light { display: block; width: 100%; opacity: .25; z-index: 1; }
        .t26-path-dark {
            position: absolute;
            inset: .2em .2em auto;
            z-index: 3;
            display: block;
            width: 100%;
        }
        .t26-path svg, .t26-path svg path { overflow: visible; }
        .t26-path-dark path { will-change: stroke-dashoffset; }
        .t26-path-mobile { display: none; }

        /* ---------- Layers layanan ---------- */
        .t26-layers {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
            display: flex;
            flex-flow: column;
            justify-content: center;
            align-items: center;
            gap: 12em;
            width: 100%;
            height: 100%;
        }
        .t26-layer {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 5.2em;
        }
        .t26-block {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: .5em;
            font-size: .85em;
        }
        .t26-text {
            font-family: var(--t26-font-heading);
            font-size: 5em;
            line-height: 80%;
            text-transform: uppercase;
            color: var(--t26-orange);
            white-space: nowrap;
        }
        .t26-svg {
            width: 7.25em;
            flex-shrink: 0;
            overflow: hidden;
            transform-origin: center center;
            will-change: transform, width, opacity;
        }
        .t26-svg > * { width: 100%; height: 100%; object-fit: contain; display: block; }
        .t26-badge {
            position: absolute;
            bottom: -2em;
            right: -4em;
            max-width: 18em;
            padding: .75em 1.25em;
            border: 1px solid var(--t26-orange);
            border-radius: 100px;
            background-color: var(--t26-white);
            transform-origin: center center;
            will-change: transform, opacity;
        }
        .t26-badge p {
            font-family: var(--t26-font-body);
            font-size: .875em;
            font-weight: 500;
            line-height: 120%;
            color: var(--t26-orange);
        }

        /* Offset zig-zag tiap layer */
        .t26-side-right { right: var(--t26-off); }
        .t26-side-left  { left: var(--t26-off); }

        /* ---------- Responsif (meniru breakpoint asli) ---------- */
        @media screen and (max-width: 991px) {
            .t26-layers { gap: 7em; }
        }
        @media screen and (max-width: 767px) {
            .t26-heading { text-align: center; font-size: 4em; }
            .t26-path-desktop { display: none; }
            .t26-path-mobile { display: block; position: relative; width: 100%; opacity: .25; }
            .t26-path-mobile-dark {
                display: block;
                position: absolute;
                inset: -2px auto auto;
                width: 100%;
            }
            .t26-body { min-height: 0; overflow: hidden; }
            .t26-layers {
                position: static;
                transform: none;
                height: auto;
                gap: 9em;
                padding: 3em 0 5em;
            }
            .t26-layer { font-size: .7em; height: auto; }
            .t26-badge { display: none; }
        }
        @media screen and (max-width: 479px) {
            .t26-heading { font-size: 3em; }
            .t26-label { display: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            .t26 .t26-svg, .t26 .t26-badge { opacity: 1 !important; transform: none !important; width: 7.25em !important; }
            .t26-badge { transform: rotate(-5deg) !important; }
            .t26 .t26-path-dark path { stroke-dashoffset: 0 !important; }
        }
    </style>

    {{-- Header dengan heading dua lapis (hitam + oranye magnetik) --}}
    <div class="t26-header">
        <div class="t26-label">Our Services</div>
        <div class="t26-heading-wrapper t26-dual" data-t26-heading>
            <h2 class="t26-heading is-orange" aria-hidden="true">THIS IS OUR THING</h2>
            <h2 class="t26-heading">THIS IS OUR THING</h2>
        </div>
    </div>

    <div class="t26-body" data-t26-body>
        {{-- Sinar desktop: light (statik redup) + dark (tergambar saat scroll) --}}
        <div class="t26-path t26-path-desktop">
            <svg class="t26-path-light" viewBox="0 0 1616 2187" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M1601 15C1241.24 56.6913 1025.14 308.977 1139.71 377.393C1191.45 408.29 1240.74 339.444 1181.39 282.059C1075.45 179.627 937.582 215.243 741.458 298.287C701.638 315.147 622.383 350.59 552.298 410.201C487.298 465.486 487.725 549.018 534.765 563.77C581.804 578.523 632.714 579.634 681.058 587.392C729.731 595.202 767.011 617.17 797.836 657.756C833.766 705.065 838.864 772.034 862.127 825.767C890.491 891.283 966.341 911.2 1033.5 915.638C1130.56 922.052 1195.74 891.215 1286.16 899.069C1347.7 904.414 1457.75 932.208 1466.92 1059.42C1477.81 1210.68 1340.4 1252.99 1221.76 1286.05C1118.2 1314.91 997.775 1397.54 862.127 1334.5C757.468 1285.86 666.411 1293.34 650.401 1384.21C634.391 1475.07 710.968 1532.13 837.283 1542.81C963.598 1553.5 1146.44 1534.93 1181.39 1676C1226.53 1858.21 954.075 1839.5 797.836 1839.5C661.891 1839.5 422.283 1807.72 385.5 1700C340.427 1568 635.939 1584.37 465.5 1917C414.516 2016.5 310 2165.31 15 2165.31"
                      stroke="#FF5400" stroke-width="200" stroke-linecap="round" opacity="0.25"/>
            </svg>
            <svg class="t26-path-dark" viewBox="0 0 1616 2187" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path data-t26-draw stroke="#FF5400" stroke-width="200" stroke-linecap="round"
                      d="M1601 15C1241.24 56.6913 1025.14 308.977 1139.71 377.393C1191.45 408.29 1240.74 339.444 1181.39 282.059C1075.45 179.627 937.582 215.243 741.458 298.287C701.638 315.147 622.383 350.59 552.298 410.201C487.298 465.486 487.725 549.018 534.765 563.77C581.804 578.523 632.714 579.634 681.058 587.392C729.731 595.202 767.011 617.17 797.836 657.756C833.766 705.065 838.864 772.034 862.127 825.767C890.491 891.283 966.341 911.2 1033.5 915.638C1130.56 922.052 1195.74 891.215 1286.16 899.069C1347.7 904.414 1457.75 932.208 1466.92 1059.42C1477.81 1210.68 1340.4 1252.99 1221.76 1286.05C1118.2 1314.91 997.775 1397.54 862.127 1334.5C757.468 1285.86 666.411 1293.34 650.401 1384.21C634.391 1475.07 710.968 1532.13 837.283 1542.81C963.598 1553.5 1146.44 1534.93 1181.39 1676C1226.53 1858.21 954.075 1839.5 797.836 1839.5C661.891 1839.5 422.283 1807.72 385.5 1700C340.427 1568 635.939 1584.37 465.5 1917C414.516 2016.5 310 2165.31 15 2165.31"/>
            </svg>
        </div>

        {{-- Sinar mobile --}}
        <div class="t26-path t26-path-mobile">
            <svg viewBox="0 0 407 1556" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M542.705 10C465.727 48.981 92.8237 79.5116 92.8237 185.592C92.8237 291.673 352.712 336 346.322 440.868C339.933 545.736 104.278 543.251 92.8237 652.131C79.6013 777.82 353.287 765.564 346.322 898.5C339.358 1031.44 63.9084 911.5 68 1046C72.0916 1180.5 335.245 1170.41 328.309 1279.59C320.5 1402.5 -37.4522 1513.46 -135.704 1545.09"
                      stroke="#FF5400" stroke-width="20"/>
            </svg>
            <svg class="t26-path-mobile-dark" viewBox="0 0 407 1556" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path data-t26-draw d="M542.705 10C465.727 48.981 92.8237 79.5116 92.8237 185.592C92.8237 291.673 352.712 336 346.322 440.868C339.933 545.736 104.278 543.251 92.8237 652.131C79.6013 777.82 353.287 765.564 346.322 898.5C339.358 1031.44 63.9084 911.5 68 1046C72.0916 1180.5 335.245 1170.41 328.309 1279.59C320.5 1402.5 -37.4522 1513.46 -135.704 1545.09"
                      stroke="#FF5400" stroke-width="20"/>
            </svg>
        </div>

        {{-- 7 layer layanan --}}
        <div class="t26-layers" data-t26-layers>
            @foreach($t26Services as $svc)
                <div class="t26-layer">
                    <div class="t26-block t26-side-{{ $svc['side'] }}" style="--t26-off: {{ $svc['offset'] }}">
                        <div class="t26-text">{{ $svc['label'] }}</div>

                        <div class="t26-svg" data-t26-svg>
                            {{-- Ganti dengan Lottie: tambahkan data-lottie="https://.../anim.json" di div di atas --}}
                            @if($svc['icon'] === 'spiral')
                                <svg viewBox="0 0 100 100" fill="none"><path d="M50 50m0-30a30 30 0 1 1-21 9" stroke="currentColor" stroke-width="8" stroke-linecap="round" style="color: var(--t26-orange)"/><circle cx="50" cy="50" r="8" fill="var(--t26-orange)"/></svg>
                            @elseif($svc['icon'] === 'star')
                                <svg viewBox="0 0 100 100" fill="none"><path d="M50 8 62 38 94 40 69 61 77 92 50 74 23 92 31 61 6 40 38 38Z" stroke="var(--t26-orange)" stroke-width="6" stroke-linejoin="round"/></svg>
                            @elseif($svc['icon'] === 'monitor')
                                <svg viewBox="0 0 100 100" fill="none"><rect x="12" y="18" width="76" height="50" rx="6" stroke="var(--t26-orange)" stroke-width="6"/><path d="M35 84h30M50 68v16" stroke="var(--t26-orange)" stroke-width="6" stroke-linecap="round"/></svg>
                            @elseif($svc['icon'] === 'pencil')
                                <svg viewBox="0 0 100 100" fill="none"><path d="M20 80 62 38l14 14-42 42-18 4Z" stroke="var(--t26-orange)" stroke-width="6" stroke-linejoin="round"/><path d="M58 42 72 56" stroke="var(--t26-orange)" stroke-width="6"/></svg>
                            @elseif($svc['icon'] === 'motion')
                                <svg viewBox="0 0 100 100" fill="none"><path d="M12 50h56M52 30l24 20-24 20" stroke="var(--t26-orange)" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="86" cy="50" r="5" fill="var(--t26-orange)"/></svg>
                            @elseif($svc['icon'] === 'box')
                                <svg viewBox="0 0 100 100" fill="none"><path d="M50 10 88 30v40L50 90 12 70V30Z" stroke="var(--t26-orange)" stroke-width="6" stroke-linejoin="round"/><path d="M12 30l38 20 38-20M50 50v40" stroke="var(--t26-orange)" stroke-width="6"/></svg>
                            @else
                                <svg viewBox="0 0 100 100" fill="none"><circle cx="50" cy="50" r="38" stroke="var(--t26-orange)" stroke-width="6"/><path d="M50 12v18M50 70v18M12 50h18M70 50h18" stroke="var(--t26-orange)" stroke-width="6" stroke-linecap="round"/><circle cx="50" cy="50" r="10" fill="var(--t26-orange)"/></svg>
                            @endif
                        </div>

                        <div class="t26-text">{{ $svc['mid'] }}</div>

                        <div class="t26-badge" data-t26-badge>
                            <p>{{ $svc['desc'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<script>
    /* ============================================================
       Services Showcase — scrub engine vanilla (pengganti GSAP)
       - Sinar "dark" tergambar mengikuti scroll (stroke-dashoffset)
       - Slot ikon & badge mengembang dengan easing back.out(3.5)
       - Heading oranye bergerak magnetik 1–4px mengikuti kursor
       ============================================================ */
    (function () {
        'use strict';

        var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        /* ---------- back.out s(3.5), sama seperti GSAP ---------- */
        function backOut(t, s) {
            s = s || 3.5;
            var u = 1 - t;
            return 1 + (s + 1) * u * u * u + s * u * u;
        }
        function clamp01(v) { return Math.min(1, Math.max(0, v)); }

        /* ---------- 1. Heading magnetik dua lapis ---------- */
        function initMagneticHeading() {
            var wrapper = document.querySelector('[data-t26-heading]');
            var orange = wrapper && wrapper.querySelector('.t26-heading.is-orange');
            if (!wrapper || !orange || reduced) return;

            var MAX = 4;
            var zone = wrapper.closest('.t26-header') || wrapper;

            zone.addEventListener('pointermove', function (e) {
                var r = wrapper.getBoundingClientRect();
                var dx = e.clientX - (r.left + r.width / 2);
                var dy = e.clientY - (r.top + r.height / 2);
                var dist = Math.hypot(dx, dy);
                var maxR = Math.max(r.width, r.height) / 2 || 100;
                var intensity = Math.min(1, Math.max(0.25, dist / maxR));
                var angle = Math.atan2(dy, dx);
                orange.style.transform =
                    'translate(' + (Math.cos(angle) * intensity * MAX).toFixed(2) + 'px, ' +
                                    (Math.sin(angle) * intensity * MAX).toFixed(2) + 'px)';
            }, { passive: true });

            zone.addEventListener('pointerleave', function () {
                orange.style.transition = 'transform .6s cubic-bezier(.34,1.56,.64,1)';
                orange.style.transform = 'translate(0,0)';
                setTimeout(function () { orange.style.transition = ''; }, 650);
            });
        }

        /* ---------- 2. Scrub sinar + expansion layer ---------- */
        function initServicesScrub() {
            var section = document.querySelector('[data-t26-body]');
            if (!section) return;

            var paths = Array.prototype.slice.call(section.parentElement.querySelectorAll('[data-t26-draw]'));
            var layers = Array.prototype.slice.call(section.querySelectorAll('.t26-layer'));

            /* Ukur panjang tiap path & set kondisi awal */
            var draws = paths.map(function (p) {
                var len = p.getTotalLength ? p.getTotalLength() : 2500;
                p.style.strokeDasharray = len;
                p.style.strokeDashoffset = reduced ? 0 : len;
                return { el: p, len: len };
            });

            /* Ukur lebar alami slot ikon (untuk animasi width 0 → target) */
            var items = layers.map(function (layer, i, arr) {
                var svg = layer.querySelector('[data-t26-svg]');
                var badge = layer.querySelector('[data-t26-badge]');
                var targetW = 0;
                if (svg) {
                    targetW = svg.getBoundingClientRect().width ||
                              parseFloat(getComputedStyle(svg).width) || 40;
                }
                return {
                    point: arr.length > 1 ? i / (arr.length - 1) : 0,
                    svg: svg, badge: badge, targetW: targetW
                };
            });

            function reset() {
                items.forEach(function (it) {
                    if (it.svg) {
                        it.svg.style.width = '0px';
                        it.svg.style.transform = 'scale(0)';
                        it.svg.style.opacity = '0';
                    }
                    if (it.badge) {
                        it.badge.style.transform = 'scale(0) rotate(0deg)';
                        it.badge.style.opacity = '0';
                    }
                });
            }
            if (!reduced) reset();

            var STEP = 0.14;      /* durasi tiap expansion (fraksi timeline) */
            var LEAD = 0.04;      /* mulai sedikit sebelum titik layanan */
            var current = 0;      /* progress ter-smooth (scrub 1.2) */
            var target = 0;
            var ticking = false;

            function measure() {
                var rect = section.getBoundingClientRect();
                var vh = window.innerHeight || 1;
                /* start: "top 70%" — end: "bottom 70%" (persis ScrollTrigger asli) */
                var startY = rect.top + window.scrollY - vh * 0.7;
                var endY = rect.top + window.scrollY + rect.height - vh * 0.7;
                var p = (window.scrollY - startY) / Math.max(1, endY - startY);
                target = clamp01(p);
            }

            function apply(p) {
                /* Sinar tergambar 0→100% sepanjang scrub */
                draws.forEach(function (d) {
                    d.el.style.strokeDashoffset = d.len * (1 - clamp01(p));
                });

                /* Expansion per layer */
                items.forEach(function (it) {
                    var local = (p - it.point + LEAD) / STEP;
                    if (local <= 0) { return; }
                    var e = backOut(clamp01(local));

                    if (it.svg) {
                        it.svg.style.opacity = String(clamp01(local * 2));
                        it.svg.style.transform = 'scale(' + e.toFixed(4) + ')';
                        it.svg.style.width = (it.targetW * Math.min(1, e)).toFixed(2) + 'px';
                    }
                    if (it.badge) {
                        var e2 = backOut(clamp01((p - it.point + LEAD - 0.02) / STEP));
                        it.badge.style.opacity = String(clamp01(local * 2));
                        it.badge.style.transform = 'scale(' + e2.toFixed(4) + ') rotate(' + (-5 * e2).toFixed(2) + 'deg)';
                    }
                });
            }

            function frame() {
                measure();
                /* smoothing setara scrub: 1.2 */
                current += (target - current) * 0.12;
                if (Math.abs(target - current) < 0.0005) current = target;
                apply(current);
                if (current !== target || target > 0) {
                    requestAnimationFrame(frame);
                } else {
                    ticking = false;
                }
            }

            function kick() {
                if (!ticking) { ticking = true; requestAnimationFrame(frame); }
            }

            if (reduced) { apply(1); return; }

            window.addEventListener('scroll', kick, { passive: true });
            window.addEventListener('resize', kick);
            kick();
        }

        /* ---------- 3. Lottie opsional: [data-lottie] ---------- */
        function initLottieSlots() {
            if (typeof window.lottie === 'undefined') return;
            document.querySelectorAll('[data-t26-svg][data-lottie]').forEach(function (slot) {
                slot.innerHTML = '';
                window.lottie.loadAnimation({
                    container: slot,
                    renderer: 'svg',
                    loop: true,
                    autoplay: true,
                    path: slot.getAttribute('data-lottie'),
                });
            });
        }

        function init() {
            initMagneticHeading();
            initServicesScrub();
            initLottieSlots();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
