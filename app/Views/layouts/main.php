<?php

declare(strict_types=1);
?>
<!doctype html>
<html lang="fa" dir="rtl" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'PasteBox') ?></title>
    <meta name="description" content="PasteBox - پیست‌باکس، اشتراک‌گذاری امن متن و فایل">
    <link rel="stylesheet" href="<?= e(asset_url('assets/css/app.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('assets/css/redesign.css')) ?>">
    <script>
        (() => {
            const saved = localStorage.getItem("pastebox-theme");
            if (saved) {
                document.documentElement.setAttribute("data-theme", saved);
            }
        })();
    </script>
    <link rel="icon" type="image/png" href="<?= e(app_url('favicon-96x96.png')) ?>" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="<?= e(app_url('favicon.svg')) ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= e(app_url('apple-touch-icon.png')) ?>">
    <meta name="apple-mobile-web-app-title" content="PasteBox">
    <link rel="manifest" href="<?= e(app_url('site.webmanifest')) ?>">
</head>
<body>
<div class="bg-grid"></div>
<div class="bg-effect bg-effect-a"></div>
<div class="bg-effect bg-effect-b"></div>

<div class="site-shell">
    <header class="topbar">
        <a href="/" class="brand">
            <span class="brand-logo" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" role="img" focusable="false">
                    <path d="M422.72,42.667h-60.053V32.491c0-17.92-14.571-32.491-32.47-32.491H181.803c-17.899,0-32.469,14.571-32.469,32.491v10.176H89.28c-25.707,0-46.613,20.907-46.613,46.613v376.107C42.667,491.093,63.573,512,89.28,512h333.44c25.707,0,46.613-20.907,46.613-46.613V89.28C469.333,63.573,448.427,42.667,422.72,42.667z M192,42.667h128v42.667H192V42.667z M234.667,298.667c-5.461,0-10.923-2.091-15.083-6.251l-6.251-6.251V384c0,11.776-9.536,21.333-21.333,21.333s-21.333-9.557-21.333-21.333v-97.835l-6.251,6.251c-4.16,4.16-9.621,6.251-15.083,6.251c-5.461,0-10.923-2.091-15.083-6.251c-8.341-8.341-8.341-21.824,0-30.165l42.645-42.645c1.963-1.984,4.331-3.541,6.955-4.629c5.205-2.155,11.093-2.155,16.299,0c2.624,1.088,4.992,2.645,6.955,4.629l42.645,42.645c8.341,8.341,8.341,21.824,0,30.165C245.589,296.576,240.128,298.667,234.667,298.667z M377.749,356.416l-42.645,42.645c-1.963,1.984-4.331,3.541-6.955,4.629c-2.603,1.067-5.376,1.643-8.149,1.643c-2.773,0-5.547-0.576-8.149-1.643c-2.624-1.088-4.992-2.645-6.955-4.629l-42.645-42.645c-8.341-8.341-8.341-21.824,0-30.165s21.824-8.341,30.165,0l6.251,6.251v-97.835c0-11.776,9.536-21.333,21.333-21.333s21.333,9.557,21.333,21.333v97.835l6.251-6.251c8.341-8.341,21.824-8.341,30.165,0S386.091,348.075,377.749,356.416z"/>
                </svg>
            </span>
            <div>
                <p class="brand-title">PasteBox</p>
                <p class="brand-subtitle">Secure paste sharing</p>
            </div>
        </a>

        <div class="topbar-actions">
            <a class="ghost-btn icon-btn plus-btn" href="/" aria-label="پیست جدید" title="پیست جدید">
                <span aria-hidden="true">+</span>
            </a>
            <div class="switch-wrapper">
                <label class="switch" for="switch-sun-moon">
                    <input type="checkbox" id="switch-sun-moon">
                    <span class="slider"></span>
                </label>
            </div>
            <script>
                (() => {
                    const input = document.getElementById("switch-sun-moon");
                    if (input) {
                        input.checked = document.documentElement.getAttribute("data-theme") === "dark";
                    }
                })();
            </script>
        </div>
    </header>

    <main class="content-wrap">
        <?= $content ?>
    </main>

    <footer class="site-footer">
        <ul class="site-footer-list" role="list">
            <li class="site-footer-item site-footer-stat" role="listitem">
                <span class="site-footer-stat-label">تا کنون</span>
                <strong
                    id="footerPasteCount"
                    class="footer-paste-count"
                    dir="ltr"
                    data-target="<?= (int) ($sharedPastesCount ?? 0) ?>"
                ><?= e(number_format((int) ($sharedPastesCount ?? 0), 0, '.', ',')) ?></strong>
                <span class="site-footer-stat-tail">بار از پیست باکس استفاده شده</span>
            </li>
            <li class="site-footer-sep" aria-hidden="true" role="presentation"><span>·</span></li>
            <li class="site-footer-item" role="listitem">
                <span>توسعه داده شده با ❤ توسط</span>
                <a href="https://arvand.dev" target="_blank" rel="noopener noreferrer">Arvand.dev</a>
            </li>
            <li class="site-footer-sep" aria-hidden="true" role="presentation"><span>·</span></li>
            <li class="site-footer-item" role="listitem">
                <a class="footer-donate" href="https://donofa.ir/pastebox/" target="_blank" rel="noopener noreferrer">
                    <svg class="footer-donate-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
                        <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/>
                    </svg>
                    حمایت از PasteBox
                </a>
            </li>
        </ul>
    </footer>
</div>

<script>
    (() => {
        const el = document.getElementById("footerPasteCount");
        if (!el) return;

        const statsUrl = <?= json_encode(app_url('api/stats/pastes'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        const pollMs = 4000;

        const formatCount = (n) => n.toLocaleString("en-US");
        const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

        const easeOutCubic = (t) => 1 - (1 - t) ** 3;

        let displayed = 0;
        let pollTimer = null;
        let lastServerCount = 0;

        const parseShown = () =>
            Math.max(0, Math.floor(Number(String(el.textContent).replace(/,/g, "")) || 0));

        const runAnimation = (from, to, durationMs, done) => {
            if (prefersReducedMotion || durationMs <= 0 || from === to) {
                displayed = to;
                el.textContent = formatCount(displayed);
                el.setAttribute("data-target", String(displayed));
                if (typeof done === "function") {
                    done();
                }
                return;
            }

            const start = performance.now();
            const tick = (now) => {
                const t = Math.min(1, (now - start) / durationMs);
                const eased = easeOutCubic(t);
                const value = Math.round(from + (to - from) * eased);
                el.textContent = formatCount(value);
                if (t < 1) {
                    requestAnimationFrame(tick);
                } else {
                    displayed = to;
                    el.textContent = formatCount(displayed);
                    el.setAttribute("data-target", String(displayed));
                    if (typeof done === "function") {
                        done();
                    }
                }
            };
            requestAnimationFrame(tick);
        };

        const syncFromServer = async () => {
            try {
                const res = await fetch(statsUrl, { cache: "no-store", credentials: "same-origin" });
                if (!res.ok) return;
                const data = await res.json();
                const next = Math.max(0, Math.floor(Number(data.count) || 0));
                if (next <= lastServerCount) return;
                lastServerCount = next;
                const from = parseShown();
                runAnimation(from, next, prefersReducedMotion ? 0 : 520);
            } catch (_) {
                /* ignore network errors */
            }
        };

        const startPolling = () => {
            if (pollTimer !== null) return;
            pollTimer = window.setInterval(syncFromServer, pollMs);
        };

        const initialTarget = Math.max(0, Math.floor(Number.parseInt(el.getAttribute("data-target") ?? "0", 10) || 0));
        lastServerCount = initialTarget;

        const entranceMs = prefersReducedMotion || initialTarget === 0 ? 0 : 1300;

        runAnimation(0, initialTarget, entranceMs, () => {
            lastServerCount = Math.max(lastServerCount, displayed);
            startPolling();
            void syncFromServer();
        });

        document.addEventListener("visibilitychange", () => {
            if (document.visibilityState === "visible") {
                void syncFromServer();
            }
        });
    })();

    (() => {
        const root = document.documentElement;
        const input = document.getElementById("switch-sun-moon");
        if (!input) return;
        let transitionTimer;

        const updateLabel = () => {
            const isDark = root.getAttribute("data-theme") === "dark";
            input.setAttribute("aria-label", isDark ? "رفتن به تم روشن" : "رفتن به تم تیره");
            input.setAttribute("title", isDark ? "تم روشن" : "تم تیره");
        };

        input.addEventListener("change", () => {
            const next = input.checked ? "dark" : "light";
            root.classList.add("theme-transition");
            root.setAttribute("data-theme", next);
            localStorage.setItem("pastebox-theme", next);
            updateLabel();
            window.clearTimeout(transitionTimer);
            transitionTimer = window.setTimeout(() => {
                root.classList.remove("theme-transition");
            }, 450);
        });

        updateLabel();
    })();
</script>
</body>
</html>
