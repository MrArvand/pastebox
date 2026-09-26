<?php

declare(strict_types=1);
?>
<section class="page-stack">
    <article class="surface-card">
        <h1 class="visually-hidden"><?= e((string) $paste['short_code']) ?></h1>

        <?php if (!empty($burnAfterReading)): ?>
            <div class="burn-banner" role="status">
                <span class="burn-banner-icon" aria-hidden="true">
                    <svg class="duo-icon" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.5" d="M12.8324 21.8013C15.9583 21.1747 20 18.926 20 13.1112C20 7.8196 16.1267 4.29593 13.3415 2.67685C12.7235 2.31757 12 2.79006 12 3.50492V5.3334C12 6.77526 11.3938 9.40711 9.70932 10.5018C8.84932 11.0607 7.92052 10.2242 7.816 9.20388L7.73017 8.36604C7.6304 7.39203 6.63841 6.80075 5.85996 7.3946C4.46147 8.46144 3 10.3296 3 13.1112C3 20.2223 8.28889 22.0001 10.9333 22.0001C11.0871 22.0001 11.2488 21.9955 11.4171 21.9858C11.863 21.9296 11.4171 22.085 12.8324 21.8013Z" fill="currentColor"/>
                        <path d="M8 18.4442C8 21.064 10.1113 21.8742 11.4171 21.9858C11.863 21.9296 11.4171 22.085 12.8324 21.8013C13.871 21.4343 15 20.4922 15 18.4442C15 17.1465 14.1814 16.3459 13.5401 15.9711C13.3439 15.8564 13.1161 16.0008 13.0985 16.2273C13.0429 16.9454 12.3534 17.5174 11.8836 16.9714C11.4685 16.4889 11.2941 15.784 11.2941 15.3331V14.7439C11.2941 14.3887 10.9365 14.1533 10.631 14.3346C9.49507 15.0085 8 16.3949 8 18.4442Z" fill="currentColor"/>
                    </svg>
                </span>
                <div>
                    <strong>این پیست یک‌بار مصرف بود</strong>
                    <span>بعد از همین نمایش، برای همیشه حذف شد.</span>
                </div>
            </div>
        <?php endif; ?>

        <?php if (is_string($pasteUrl ?? '') && $pasteUrl !== ''): ?>
            <div class="paste-share">
                <div class="paste-share-main">
                    <input id="pasteViewUrlInput" class="paste-share-url" type="text" value="<?= e($pasteUrl) ?>" readonly inputmode="url" autocomplete="off" spellcheck="false" aria-label="لینک اشتراک‌گذاری">
                    <button id="copyPasteViewUrlBtn" class="btn btn-dark paste-share-btn" type="button">کپی</button>
                    <button id="toggleViewQrBtn" class="btn btn-ghost paste-share-btn" type="button" aria-expanded="false" aria-controls="pasteViewQrPanel">کیوآر</button>
                </div>
                <div class="paste-share-meta">
                    <span>ایجاد <span class="paste-meta-date" data-gregorian="<?= e((string) $paste['created_at']) ?>"><?= e((string) $paste['created_at']) ?></span></span>
                    <span>انقضا <span class="paste-meta-date" data-gregorian="<?= e((string) $paste['expires_at']) ?>"><?= e((string) $paste['expires_at']) ?></span></span>
                    <?php if (!empty($attachments) && is_array($attachments)): ?>
                        <span><?= e((string) count($attachments)) ?> فایل</span>
                    <?php endif; ?>
                </div>
            </div>
            <p id="pasteViewUrlCopyFeedback" class="copy-url-feedback" role="status" aria-live="polite" hidden></p>
            <div id="pasteViewQrPanel" class="paste-qr" hidden>
                <img class="paste-qr-img" src="<?= e('/' . (string) $paste['short_code'] . '/qr.svg') ?>" alt="کیوآر کد لینک این پیست" width="160" height="160" loading="lazy" decoding="async">
            </div>
        <?php endif; ?>

        <div class="paste-body-shell">
            <div class="paste-in-pre-toolbar">
                <span id="pasteContentCopyFeedback" class="paste-in-pre-feedback" role="status" aria-live="polite" hidden></span>
                <button id="copyPasteBtn" class="icon-btn copy-btn paste-in-pre-copy" type="button" aria-label="کپی پیست" title="کپی پیست">
                    <svg class="duo-icon icon-copy" viewBox="0 0 24 24" fill="none" focusable="false" aria-hidden="true">
                        <path d="M6.59961 11.3974C6.59961 8.67119 6.59961 7.3081 7.44314 6.46118C8.28667 5.61426 9.64432 5.61426 12.3596 5.61426H15.2396C17.9549 5.61426 19.3125 5.61426 20.1561 6.46118C20.9996 7.3081 20.9996 8.6712 20.9996 11.3974V16.2167C20.9996 18.9429 20.9996 20.306 20.1561 21.1529C19.3125 21.9998 17.9549 21.9998 15.2396 21.9998H12.3596C9.64432 21.9998 8.28667 21.9998 7.44314 21.1529C6.59961 20.306 6.59961 18.9429 6.59961 16.2167V11.3974Z" fill="currentColor"/>
                        <path opacity="0.5" d="M4.17157 3.17157C3 4.34315 3 6.22876 3 10V12C3 15.7712 3 17.6569 4.17157 18.8284C4.78913 19.446 5.6051 19.738 6.79105 19.8761C6.59961 19.0353 6.59961 17.8796 6.59961 16.2167V11.3974C6.59961 8.6712 6.59961 7.3081 7.44314 6.46118C8.28667 5.61426 9.64432 5.61426 12.3596 5.61426H15.2396C16.8915 5.61426 18.0409 5.61426 18.8777 5.80494C18.7403 4.61146 18.4484 3.79154 17.8284 3.17157C16.6569 2 14.7712 2 11 2C7.22876 2 5.34315 2 4.17157 3.17157Z" fill="currentColor"/>
                    </svg>
                </button>
            </div>
            <pre class="paste-pre-block"><code id="pasteContent"><?= e((string) $paste['content']) ?></code></pre>
        </div>
    </article>

    <?php if (!empty($attachments) && is_array($attachments)): ?>
        <article class="surface-card">
            <h2 class="attachments-title">فایل‌های پیوست</h2>
            <div class="attachment-list">
                <?php foreach ($attachments as $attachment): ?>
                    <a class="attachment-item" href="/attachment/<?= e((string) $attachment['id']) ?>/download">
                        <span><?= e((string) $attachment['original_name']) ?></span>
                        <span class="hint" dir="ltr"><?= e(format_bytes((int) ($attachment['size_bytes'] ?? 0))) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </article>
    <?php endif; ?>
</section>

<script src="<?= e(asset_url('assets/js/jalaali.min.js')) ?>"></script>
<script>
    (() => {
        const parseGregorianLocal = (ymdHis) => {
            const m = String(ymdHis).trim().match(/^(\d{4})-(\d{2})-(\d{2})(?: (\d{2}):(\d{2}):(\d{2}))?$/);
            if (!m) return null;
            return new Date(Number(m[1]), Number(m[2]) - 1, Number(m[3]), m[4] !== undefined ? Number(m[4]) : 0, m[5] !== undefined ? Number(m[5]) : 0, m[6] !== undefined ? Number(m[6]) : 0);
        };
        const pad2 = (n) => String(n).padStart(2, "0");
        const formatJalaliChip = (dt) => {
            if (!dt || Number.isNaN(dt.getTime())) return null;
            const jal = window.jalaali && typeof window.jalaali.toJalaali === "function" ? window.jalaali.toJalaali(dt) : null;
            if (!jal) return null;
            return `${jal.jy}/${pad2(jal.jm)}/${pad2(jal.jd)}، ${pad2(dt.getHours())}:${pad2(dt.getMinutes())}:${pad2(dt.getSeconds())}`;
        };
        document.querySelectorAll(".paste-meta-date[data-gregorian]").forEach((el) => {
            const formatted = formatJalaliChip(parseGregorianLocal(el.getAttribute("data-gregorian") || ""));
            if (formatted) el.textContent = formatted;
        });

        const copyTextToClipboard = async (text, fallbackInput) => {
            if (navigator.clipboard?.writeText) {
                try {
                    await navigator.clipboard.writeText(text);
                    return true;
                } catch (_) { /* fallback */ }
            }
            try {
                const textarea = document.createElement("textarea");
                textarea.value = text;
                textarea.setAttribute("readonly", "readonly");
                textarea.style.position = "fixed";
                textarea.style.left = "-9999px";
                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();
                const ok = document.execCommand("copy");
                document.body.removeChild(textarea);
                if (ok) return true;
            } catch (_) { /* try input */ }
            if (fallbackInput instanceof HTMLInputElement) {
                fallbackInput.focus();
                fallbackInput.select();
                return document.execCommand("copy");
            }
            return false;
        };

        const bindFeedback = (el, duration = 2200) => {
            let timer = 0;
            return (message) => {
                if (!el) return;
                el.textContent = message;
                el.removeAttribute("hidden");
                window.clearTimeout(timer);
                timer = window.setTimeout(() => {
                    el.setAttribute("hidden", "");
                    el.textContent = "";
                }, duration);
            };
        };

        const copyPasteBtn = document.getElementById("copyPasteBtn");
        const pasteContent = document.getElementById("pasteContent");
        const showContentCopyFeedback = bindFeedback(document.getElementById("pasteContentCopyFeedback"));
        copyPasteBtn?.addEventListener("click", async () => {
            const copied = await copyTextToClipboard(pasteContent?.textContent ?? "", null);
            showContentCopyFeedback(copied ? "کپی شد" : "کپی نشد؛ دوباره تلاش کنید");
            if (copied) {
                copyPasteBtn.classList.add("is-copied");
                window.setTimeout(() => copyPasteBtn.classList.remove("is-copied"), 2000);
            }
        });

        const copyViewUrlBtn = document.getElementById("copyPasteViewUrlBtn");
        const pasteViewUrlInput = document.getElementById("pasteViewUrlInput");
        const showViewUrlCopyFeedback = bindFeedback(document.getElementById("pasteViewUrlCopyFeedback"));
        if (copyViewUrlBtn && pasteViewUrlInput instanceof HTMLInputElement) {
            const copyLabel = copyViewUrlBtn.textContent;
            copyViewUrlBtn.addEventListener("click", async () => {
                const copied = await copyTextToClipboard(pasteViewUrlInput.value, pasteViewUrlInput);
                showViewUrlCopyFeedback(copied ? "کپی شد" : "لینک انتخاب شد؛ Ctrl+C را بزنید");
                if (copied) {
                    copyViewUrlBtn.classList.add("is-copied");
                    copyViewUrlBtn.textContent = "کپی شد";
                    window.setTimeout(() => {
                        copyViewUrlBtn.classList.remove("is-copied");
                        copyViewUrlBtn.textContent = copyLabel;
                    }, 2000);
                }
            });
        }

        const qrToggleBtn = document.getElementById("toggleViewQrBtn");
        const qrPanel = document.getElementById("pasteViewQrPanel");
        qrToggleBtn?.addEventListener("click", () => {
            if (!qrPanel) return;
            const willShow = qrPanel.hasAttribute("hidden");
            if (willShow) qrPanel.removeAttribute("hidden");
            else qrPanel.setAttribute("hidden", "");
            qrToggleBtn.setAttribute("aria-expanded", willShow ? "true" : "false");
            qrToggleBtn.setAttribute("aria-label", willShow ? "پنهان کردن کیوآر کد" : "نمایش کیوآر کد");
        });
    })();
</script>
