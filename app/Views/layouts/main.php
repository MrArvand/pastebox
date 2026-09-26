<?php

declare(strict_types=1);
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#070708">
    <title><?= e($title ?? 'PasteBox') ?></title>
    <meta name="description" content="PasteBox - پیست‌باکس، اشتراک‌گذاری امن پیست و فایل">
    <link rel="stylesheet" href="<?= e(asset_url('assets/css/app.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('assets/css/dot-field.css')) ?>">
    <link rel="icon" type="image/png" href="<?= e(app_url('favicon-96x96.png')) ?>" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="<?= e(app_url('favicon.svg')) ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= e(app_url('apple-touch-icon.png')) ?>">
    <meta name="apple-mobile-web-app-title" content="PasteBox">
    <link rel="manifest" href="<?= e(app_url('site.webmanifest')) ?>">
</head>
<body>
<div class="app-shell">
    <div class="dot-field-backdrop" aria-hidden="true">
        <div
            id="dotField"
            class="dot-field-container"
            data-dot-radius="1.15"
            data-dot-spacing="26"
            data-cursor-radius="240"
            data-cursor-force="0.08"
            data-bulge-only="true"
            data-bulge-strength="18"
            data-glow-radius="0"
            data-sparkle="false"
            data-wave-amplitude="0"
            data-dot-dim="rgba(244, 244, 242, 0.16)"
            data-dot-mid="rgba(244, 244, 242, 0.42)"
            data-dot-hot="rgba(214, 255, 62, 0.95)"
        ></div>
    </div>

    <header class="site-header">
        <a href="/" class="brand" aria-label="پیست‌باکس">
            <span class="brand-name" dir="ltr">Paste<span>Box</span></span>
        </a>
    </header>

    <main class="main-content">
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
                <span>توسعه داده شده با</span>
                <svg class="footer-heart" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
                    <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/>
                </svg>
                <span>توسط</span>
                <a href="https://arvand.dev" target="_blank" rel="noopener noreferrer">Arvand dev</a>
            </li>
            <li class="site-footer-sep" aria-hidden="true" role="presentation"><span>·</span></li>
            <li class="site-footer-item" role="listitem">
                <a class="footer-donate" href="https://donofa.ir/pastebox/" target="_blank" rel="noopener noreferrer">
                    حمایت از PasteBox
                </a>
            </li>
        </ul>
    </footer>
</div>

<script src="<?= e(asset_url('assets/js/dot-field.js')) ?>" defer></script>
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
                if (typeof done === "function") done();
                return;
            }

            const start = performance.now();
            const tick = (now) => {
                const t = Math.min(1, (now - start) / durationMs);
                const value = Math.round(from + (to - from) * easeOutCubic(t));
                el.textContent = formatCount(value);
                if (t < 1) {
                    requestAnimationFrame(tick);
                    return;
                }
                displayed = to;
                el.textContent = formatCount(displayed);
                el.setAttribute("data-target", String(displayed));
                if (typeof done === "function") done();
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
                runAnimation(parseShown(), next, prefersReducedMotion ? 0 : 520);
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
            if (document.visibilityState === "visible") void syncFromServer();
        });
    })();
</script>
</body>
</html>
