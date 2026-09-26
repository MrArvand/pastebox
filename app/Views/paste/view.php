<?php

declare(strict_types=1);
?>
<section class="page-wrap page-wrap-wide">
    <div class="hero view-hero view-hero--split">
        <div class="view-hero-top">
            <h1 class="view-title">پیست <?= e((string) $paste['short_code']) ?></h1>
            <div class="meta-chips">
                <span class="meta-chip">ایجاد: <span class="paste-meta-date" data-gregorian="<?= e((string) $paste['created_at']) ?>"><?= e((string) $paste['created_at']) ?></span></span>
                <span class="meta-chip">انقضا: <span class="paste-meta-date" data-gregorian="<?= e((string) $paste['expires_at']) ?>"><?= e((string) $paste['expires_at']) ?></span></span>
                <?php if (!empty($attachments) && is_array($attachments)): ?>
                    <span class="meta-chip"><?= e((string) count($attachments)) ?> فایل پیوست</span>
                <?php endif; ?>
            </div>
            <?php if (!empty($burnAfterReading)): ?>
                <p class="hint burn-badge">Burn after reading فعال بود و این پیست بعد از همین نمایش حذف شد.</p>
            <?php endif; ?>
        </div>

        <?php if (is_string($pasteUrl ?? '') && $pasteUrl !== ''): ?>
            <div class="view-hero-share" dir="ltr">
                <span class="view-paste-share-title">لینک اشتراک‌گذاری</span>
                <div class="success-row">
                    <input
                        id="pasteViewUrlInput"
                        type="text"
                        class="text-left success-url-input"
                        value="<?= e($pasteUrl) ?>"
                        readonly
                        inputmode="url"
                        autocomplete="off"
                        spellcheck="false"
                    >
                    <div class="success-actions">
                        <button id="copyPasteViewUrlBtn" class="btn-secondary icon-btn copy-btn" type="button" aria-label="کپی لینک" title="کپی لینک">
                            <svg class="icon-copy" viewBox="0 0 24 24" fill="none" focusable="false" aria-hidden="true">
                                <path d="M6.59961 11.3974C6.59961 8.67119 6.59961 7.3081 7.44314 6.46118C8.28667 5.61426 9.64432 5.61426 12.3596 5.61426H15.2396C17.9549 5.61426 19.3125 5.61426 20.1561 6.46118C20.9996 7.3081 20.9996 8.6712 20.9996 11.3974V16.2167C20.9996 18.9429 20.9996 20.306 20.1561 21.1529C19.3125 21.9998 17.9549 21.9998 15.2396 21.9998H12.3596C9.64432 21.9998 8.28667 21.9998 7.44314 21.1529C6.59961 20.306 6.59961 18.9429 6.59961 16.2167V11.3974Z" fill="currentColor"/>
                                <path opacity="0.5" d="M4.17157 3.17157C3 4.34315 3 6.22876 3 10V12C3 15.7712 3 17.6569 4.17157 18.8284C4.78913 19.446 5.6051 19.738 6.79105 19.8761C6.59961 19.0353 6.59961 17.8796 6.59961 16.2167V11.3974C6.59961 8.6712 6.59961 7.3081 7.44314 6.46118C8.28667 5.61426 9.64432 5.61426 12.3596 5.61426H15.2396C16.8915 5.61426 18.0409 5.61426 18.8777 5.80494C18.7403 4.61146 18.4484 3.79154 17.8284 3.17157C16.6569 2 14.7712 2 11 2C7.22876 2 5.34315 2 4.17157 3.17157Z" fill="currentColor"/>
                            </svg>
                        </button>
                        <button
                            id="toggleViewQrBtn"
                            class="btn-secondary icon-btn qr-toggle-btn"
                            type="button"
                            aria-expanded="false"
                            aria-controls="pasteViewQrPanel"
                            aria-label="نمایش کیوآر کد"
                            title="نمایش کیوآر کد"
                        >
                            <svg class="icon-qr" viewBox="0 0 24 24" fill="currentColor" focusable="false" aria-hidden="true">
                                <path d="M3 3h6v6H3V3zm2 2v2h2V5H5zm8-2h6v6h-6V3zm2 2v2h2V5h-2zM3 15h6v6H3v-6zm2 2v2h2v-2H5zm13-2h2v2h-2v-2zm-2 0h-2v2h2v-2zm-2 4h2v2h-2v-2zm4 0h2v2h-2v-2zm-4-8h2v2h-2v-2zm4 0h2v2h-2v-2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <p id="pasteViewUrlCopyFeedback" class="copy-url-feedback view-hero-share-feedback" role="status" aria-live="polite" hidden></p>
                <div id="pasteViewQrPanel" class="paste-qr paste-qr--in-hero" hidden>
                    <p class="paste-qr-caption">کیوآر کد لینک پیست</p>
                    <img
                        class="paste-qr-img"
                        src="<?= e(app_url((string) $paste['short_code'] . '/qr.svg')) ?>"
                        alt="کیوآر کد لینک این پیست"
                        width="160"
                        height="160"
                        loading="lazy"
                        decoding="async"
                    >
                </div>
            </div>
        <?php endif; ?>
    </div>

    <article class="paste-content paste-content-editor">
        <div class="paste-body-shell">
            <div class="paste-in-pre-toolbar" dir="ltr">
                <span id="pasteContentCopyFeedback" class="paste-in-pre-feedback" role="status" aria-live="polite" hidden></span>
                <button id="copyPasteBtn" class="btn-secondary icon-btn copy-btn paste-in-pre-copy" type="button" aria-label="کپی پیست" title="کپی پیست">
                    <svg class="icon-copy" viewBox="0 0 24 24" fill="none" focusable="false" aria-hidden="true">
                        <path d="M6.59961 11.3974C6.59961 8.67119 6.59961 7.3081 7.44314 6.46118C8.28667 5.61426 9.64432 5.61426 12.3596 5.61426H15.2396C17.9549 5.61426 19.3125 5.61426 20.1561 6.46118C20.9996 7.3081 20.9996 8.6712 20.9996 11.3974V16.2167C20.9996 18.9429 20.9996 20.306 20.1561 21.1529C19.3125 21.9998 17.9549 21.9998 15.2396 21.9998H12.3596C9.64432 21.9998 8.28667 21.9998 7.44314 21.1529C6.59961 20.306 6.59961 18.9429 6.59961 16.2167V11.3974Z" fill="currentColor"/>
                        <path opacity="0.5" d="M4.17157 3.17157C3 4.34315 3 6.22876 3 10V12C3 15.7712 3 17.6569 4.17157 18.8284C4.78913 19.446 5.6051 19.738 6.79105 19.8761C6.59961 19.0353 6.59961 17.8796 6.59961 16.2167V11.3974C6.59961 8.6712 6.59961 7.3081 7.44314 6.46118C8.28667 5.61426 9.64432 5.61426 12.3596 5.61426H15.2396C16.8915 5.61426 18.0409 5.61426 18.8777 5.80494C18.7403 4.61146 18.4484 3.79154 17.8284 3.17157C16.6569 2 14.7712 2 11 2C7.22876 2 5.34315 2 4.17157 3.17157Z" fill="currentColor"/>
                    </svg>
                </button>
            </div>
            <pre class="paste-pre-block"><code id="pasteContent"><?= e((string) $paste['content']) ?></code></pre>
        </div>
    </article>

    <?php if (!empty($attachments) && is_array($attachments)): ?>
        <div class="panel">
            <div class="panel-body">
                <h2 class="attachments-title">فایل های پیوست</h2>
                <div class="attachment-list">
                <?php foreach ($attachments as $attachment): ?>
                    <a
                        class="attachment-item"
                        href="/attachment/<?= e((string) $attachment['id']) ?>/download"
                    >
                        <span><?= e((string) $attachment['original_name']) ?></span>
                        <span class="hint" dir="ltr"><?= e(format_bytes((int) ($attachment['size_bytes'] ?? 0))) ?></span>
                    </a>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<script src="<?= e(asset_url('assets/js/jalaali.min.js')) ?>"></script>
<script>
    (() => {
        const parseGregorianLocal = (ymdHis) => {
            const m = String(ymdHis).trim().match(/^(\d{4})-(\d{2})-(\d{2})(?: (\d{2}):(\d{2}):(\d{2}))?$/);
            if (!m) return null;
            const y = Number(m[1]);
            const mo = Number(m[2]);
            const d = Number(m[3]);
            const h = m[4] !== undefined ? Number(m[4]) : 0;
            const mi = m[5] !== undefined ? Number(m[5]) : 0;
            const s = m[6] !== undefined ? Number(m[6]) : 0;
            return new Date(y, mo - 1, d, h, mi, s);
        };

        const pad2 = (n) => String(n).padStart(2, "0");

        const formatJalaliChip = (dt) => {
            if (!dt || Number.isNaN(dt.getTime())) return null;
            const jal = window.jalaali && typeof window.jalaali.toJalaali === "function"
                ? window.jalaali.toJalaali(dt)
                : null;
            if (!jal) return null;
            return `${jal.jy}/${pad2(jal.jm)}/${pad2(jal.jd)}، ${pad2(dt.getHours())}:${pad2(dt.getMinutes())}:${pad2(dt.getSeconds())}`;
        };

        document.querySelectorAll(".paste-meta-date[data-gregorian]").forEach((el) => {
            const raw = el.getAttribute("data-gregorian");
            if (!raw) return;
            const dt = parseGregorianLocal(raw);
            const formatted = formatJalaliChip(dt);
            if (formatted) {
                el.textContent = formatted;
            }
        });

        const copyTextToClipboard = async (text, fallbackInput) => {
            if (typeof navigator.clipboard !== "undefined" && typeof navigator.clipboard.writeText === "function") {
                try {
                    await navigator.clipboard.writeText(text);
                    return true;
                } catch (e) {
                    /* use legacy path */
                }
            }
            try {
                const textarea = document.createElement("textarea");
                textarea.value = text;
                textarea.setAttribute("readonly", "readonly");
                textarea.style.position = "fixed";
                textarea.style.left = "-9999px";
                textarea.style.top = "0";
                textarea.style.opacity = "0";
                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();
                textarea.setSelectionRange(0, text.length);
                const ok = document.execCommand("copy");
                document.body.removeChild(textarea);
                if (ok) {
                    return true;
                }
            } catch (e) {
                /* try visible input */
            }
            if (fallbackInput instanceof HTMLInputElement) {
                try {
                    fallbackInput.focus();
                    fallbackInput.select();
                    fallbackInput.setSelectionRange(0, text.length);
                    return document.execCommand("copy");
                } catch (e) {
                    return false;
                }
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
        const pasteContentCopyFeedback = document.getElementById("pasteContentCopyFeedback");
        const showContentCopyFeedback = bindFeedback(pasteContentCopyFeedback);

        if (copyPasteBtn && pasteContent) {
            copyPasteBtn.addEventListener("click", async () => {
                const text = pasteContent.textContent ?? "";
                const copied = await copyTextToClipboard(text, null);
                if (copied) {
                    copyPasteBtn.classList.add("is-copied");
                    copyPasteBtn.setAttribute("title", "کپی شد");
                    copyPasteBtn.setAttribute("aria-label", "کپی شد");
                    showContentCopyFeedback("کپی شد");
                    window.setTimeout(() => {
                        copyPasteBtn.classList.remove("is-copied");
                        copyPasteBtn.setAttribute("title", "کپی پیست");
                        copyPasteBtn.setAttribute("aria-label", "کپی پیست");
                    }, 2000);
                } else {
                    showContentCopyFeedback("کپی نشد؛ دوباره تلاش کنید");
                }
            });
        }

        const copyViewUrlBtn = document.getElementById("copyPasteViewUrlBtn");
        const pasteViewUrlInput = document.getElementById("pasteViewUrlInput");
        const pasteViewUrlCopyFeedback = document.getElementById("pasteViewUrlCopyFeedback");
        const showViewUrlCopyFeedback = bindFeedback(pasteViewUrlCopyFeedback);

        if (copyViewUrlBtn && pasteViewUrlInput) {
            copyViewUrlBtn.addEventListener("click", async () => {
                const text = pasteViewUrlInput.value;
                const copied = await copyTextToClipboard(text, pasteViewUrlInput);
                if (copied) {
                    copyViewUrlBtn.classList.add("is-copied");
                    copyViewUrlBtn.setAttribute("title", "کپی شد");
                    copyViewUrlBtn.setAttribute("aria-label", "کپی شد");
                    showViewUrlCopyFeedback("کپی شد");
                    window.setTimeout(() => {
                        copyViewUrlBtn.classList.remove("is-copied");
                        copyViewUrlBtn.setAttribute("title", "کپی لینک");
                        copyViewUrlBtn.setAttribute("aria-label", "کپی لینک");
                    }, 2000);
                } else {
                    pasteViewUrlInput.focus();
                    pasteViewUrlInput.select();
                    showViewUrlCopyFeedback("لینک انتخاب شد؛ Ctrl+C را بزنید");
                }
            });
        }

        const qrToggleBtn = document.getElementById("toggleViewQrBtn");
        const qrPanel = document.getElementById("pasteViewQrPanel");
        if (qrToggleBtn && qrPanel) {
            const showLabels = { title: "پنهان کردن کیوآر کد", label: "پنهان کردن کیوآر کد" };
            const hideLabels = { title: "نمایش کیوآر کد", label: "نمایش کیوآر کد" };

            qrToggleBtn.addEventListener("click", () => {
                const willShow = qrPanel.hasAttribute("hidden");
                if (willShow) {
                    qrPanel.removeAttribute("hidden");
                    qrToggleBtn.setAttribute("aria-expanded", "true");
                    qrToggleBtn.setAttribute("title", showLabels.title);
                    qrToggleBtn.setAttribute("aria-label", showLabels.label);
                } else {
                    qrPanel.setAttribute("hidden", "");
                    qrToggleBtn.setAttribute("aria-expanded", "false");
                    qrToggleBtn.setAttribute("title", hideLabels.title);
                    qrToggleBtn.setAttribute("aria-label", hideLabels.label);
                }
            });
        }
    })();
</script>
