<?php

declare(strict_types=1);

$maxUploadSize = (int) ($maxUploadSize ?? 268435456);
$maxUploadSizeLabel = is_string($maxUploadSizeLabel ?? null) && $maxUploadSizeLabel !== ''
    ? $maxUploadSizeLabel
    : format_bytes($maxUploadSize);
$hasSuccess = is_string($successLink ?? null) && $successLink !== '';
$successCode = is_string($successCode ?? null) ? $successCode : '';
$hasCreatedCode = $hasSuccess && $successCode !== '';
$hasError = is_string($error ?? null) && $error !== '';
?>
<div id="pasteFormError" class="alert-box alert-error"<?= $hasError ? '' : ' hidden' ?>>
    <span id="pasteFormErrorText"><?= $hasError ? e($error) : '' ?></span>
</div>

<section class="workspace" aria-label="ساخت یا مشاهده پیست">
    <article class="create-card">
        <form
            id="pasteForm"
            action="/paste"
            method="post"
            enctype="multipart/form-data"
            data-max-upload="<?= (int) $maxUploadSize ?>"
        >
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">

            <?php if ($hasSuccess): ?>
                <div class="result-card">
                    <div class="result-head">
                        <span class="result-mark" aria-hidden="true">✓</span>
                        <div class="result-copy">
                            <strong>پیست امن شما ساخته شد</strong>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div id="burn_banner" class="burn-banner" role="status" hidden>
                <span class="burn-banner-icon" aria-hidden="true">
                    <svg class="duo-icon" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.5" d="M12.8324 21.8013C15.9583 21.1747 20 18.926 20 13.1112C20 7.8196 16.1267 4.29593 13.3415 2.67685C12.7235 2.31757 12 2.79006 12 3.50492V5.3334C12 6.77526 11.3938 9.40711 9.70932 10.5018C8.84932 11.0607 7.92052 10.2242 7.816 9.20388L7.73017 8.36604C7.6304 7.39203 6.63841 6.80075 5.85996 7.3946C4.46147 8.46144 3 10.3296 3 13.1112C3 20.2223 8.28889 22.0001 10.9333 22.0001C11.0871 22.0001 11.2488 21.9955 11.4171 21.9858C11.863 21.9296 11.4171 22.085 12.8324 21.8013Z" fill="currentColor"/>
                        <path d="M8 18.4442C8 21.064 10.1113 21.8742 11.4171 21.9858C11.863 21.9296 11.4171 22.085 12.8324 21.8013C13.871 21.4343 15 20.4922 15 18.4442C15 17.1465 14.1814 16.3459 13.5401 15.9711C13.3439 15.8564 13.1161 16.0008 13.0985 16.2273C13.0429 16.9454 12.3534 17.5174 11.8836 16.9714C11.4685 16.4889 11.2941 15.784 11.2941 15.3331V14.7439C11.2941 14.3887 10.9365 14.1533 10.631 14.3346C9.49507 15.0085 8 16.3949 8 18.4442Z" fill="currentColor"/>
                    </svg>
                </span>
                <div>
                    <strong>حالت یک‌بار مصرف روشن است</strong>
                    <span>پیست بلافاصله پس از اولین مشاهده، برای همیشه حذف می‌شود.</span>
                </div>
            </div>

            <div class="editor">
                <label class="visually-hidden" for="content">پیست شما</label>
                <textarea id="content" name="content" maxlength="50000" required placeholder="پیست، کد یا یادداشت محرمانه‌تان را اینجا بنویسید..."<?= $hasCreatedCode ? '' : ' autofocus' ?>></textarea>
                <span id="content_counter" class="char-count" aria-live="polite">۰ از ۵۰٬۰۰۰</span>
            </div>

            <div class="options-list">
                <div class="option-row" data-toggle-row>
                    <div class="option-copy">
                        <strong>سوختن پس از خواندن</strong>
                        <span>بعد از اولین مشاهده، خودکار حذف شود</span>
                    </div>
                    <label class="switch">
                        <input id="burn_after_reading" name="burn_after_reading" type="checkbox" value="1" aria-label="سوختن پس از خواندن">
                        <span class="switch-knob"></span>
                    </label>
                </div>

                <div class="option-row" data-toggle-row>
                    <div class="option-copy">
                        <strong>محافظت با رمز عبور</strong>
                        <span>یک لایه امنیت بیشتر برای پیست شما</span>
                    </div>
                    <label class="switch">
                        <input id="password_toggle" type="checkbox" aria-label="محافظت با رمز عبور">
                        <span class="switch-knob"></span>
                    </label>
                </div>
            </div>

            <div id="password_panel" class="password-panel" hidden>
                <p class="password-help">
                    <?php require BASE_PATH . '/app/Views/partials/icon-lock.php'; ?>
                    <span>رمز عبور را جداگانه برای گیرنده بفرستید.</span>
                </p>
                <label class="field-label" for="password">رمز عبور</label>
                <div class="password-input-wrap">
                    <input id="password" name="password" type="password" autocomplete="new-password" placeholder="یک رمز برای این پیست" disabled>
                    <button id="togglePasswordVisibility" type="button" aria-label="نمایش یا پنهان کردن رمز">نمایش</button>
                </div>
                <div class="strength-row">
                    <div id="strengthMeter" class="strength-meter" aria-hidden="true">
                        <span></span><span></span><span></span><span></span>
                    </div>
                    <span id="strengthLabel">قدرت رمز</span>
                </div>
            </div>

            <div class="expiry-panel">
                <div class="expiry-row">
                    <div class="option-copy">
                        <strong>زمان انقضا</strong>
                        <span>بعد از این زمان، پیست قابل بازیابی نیست.</span>
                    </div>
                    <label class="visually-hidden" for="expires_in">زمان انقضا</label>
                    <select id="expires_in" name="expires_in" class="expiry-select" required>
                        <option value="5m">۵ دقیقه</option>
                        <option value="10m">۱۰ دقیقه</option>
                        <option value="1h">۱ ساعت</option>
                        <option value="1d" selected>۱ روز</option>
                        <option value="7d">۷ روز</option>
                        <option value="14d">۱۴ روز</option>
                    </select>
                </div>
            </div>

            <div class="attachment-section">
                <label id="attachment_drop_zone" class="dropzone">
                    <input id="attachment" name="attachment" type="file" accept="<?= e($allowedFileAccept ?? '') ?>">
                    <span><strong>فایل را اینجا رها کنید</strong> یا برای انتخاب کلیک کنید</span>
                </label>

                <div id="file_preview" class="file-preview" hidden>
                    <span class="file-icon" aria-hidden="true">
                        <svg class="duo-icon" viewBox="0 0 24 24" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M11.2442 1.95482C12.9441 1.01506 15.0345 1.01506 16.7345 1.95482C17.3641 2.30291 17.9518 2.86575 18.9065 3.78014C18.9373 3.80965 18.9685 3.83952 19.0001 3.86977C19.2993 4.15623 19.3096 4.631 19.0231 4.93018C18.7366 5.22936 18.2619 5.23967 17.9627 4.9532C16.8844 3.92069 16.4452 3.50886 16.0087 3.26758C14.7604 2.57747 13.2183 2.57747 11.9699 3.26758C11.5334 3.50886 11.0943 3.92069 10.0159 4.9532L4.02651 10.6881C3.72732 10.9745 3.25256 10.9642 2.9661 10.665C2.67963 10.3659 2.68994 9.8911 2.98912 9.60464L8.97855 3.86977C9.01014 3.83952 9.04133 3.80965 9.07214 3.78014C10.0268 2.86575 10.6145 2.30291 11.2442 1.95482Z" fill="currentColor"/>
                            <path opacity="0.5" d="M14.945 6.76023C15.2314 6.46104 15.7062 6.45074 16.0054 6.7372C16.0326 6.76328 16.0596 6.78906 16.0863 6.81457C16.4533 7.16504 16.7689 7.46639 16.9459 7.81825C17.2622 8.44722 17.2622 9.18288 16.9459 9.81184C16.7689 10.1637 16.4533 10.4651 16.0863 10.8155C16.0596 10.841 16.0326 10.8668 16.0054 10.8929L8.62553 17.9591C8.32635 18.2455 7.85159 18.2352 7.56512 17.936C7.27866 17.6369 7.28897 17.1621 7.58815 16.8756L14.968 9.80945C15.4628 9.33562 15.5615 9.22595 15.6058 9.13786C15.7089 8.93291 15.7089 8.69718 15.6058 8.49224C15.5615 8.40415 15.4628 8.29447 14.968 7.82064C14.6688 7.53417 14.6585 7.05941 14.945 6.76023Z" fill="currentColor"/>
                        </svg>
                    </span>
                    <div>
                        <strong id="attachment_file_name" dir="ltr"></strong>
                        <span id="attachment_file_size">آماده ارسال</span>
                    </div>
                    <button id="removeAttachmentBtn" type="button" aria-label="حذف فایل">×</button>
                </div>

                <div id="upload_progress_panel" class="upload-progress" hidden>
                    <div class="upload-progress-head">
                        <span id="upload_progress_label" class="upload-progress-label">در حال آپلود...</span>
                        <span id="upload_progress_percent" class="upload-progress-percent" dir="ltr">0%</span>
                    </div>
                    <div class="upload-progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="upload_progress_bar_wrap">
                        <div id="upload_progress_bar" class="upload-progress-fill"></div>
                    </div>
                </div>
            </div>

            <button id="pasteSubmitBtn" type="submit" class="btn btn-primary create-button" disabled>
                <span class="btn-label">ساخت پیست امن</span>
                <span class="btn-arrow" aria-hidden="true"></span>
            </button>
        </form>
    </article>

    <aside class="access-panel"<?= $hasCreatedCode ? ' data-created="1"' : '' ?>>
        <div class="access-content">
            <?php if ($hasCreatedCode): ?>
                <h2>کد پیست</h2>
                <p>این چهار رقم را برای گیرنده بفرستید.</p>
                <div class="otp-group is-created" dir="ltr">
                    <?php foreach (str_split($successCode) as $index => $digit): ?>
                        <input class="otp-digit is-filled" type="text" value="<?= e($digit) ?>" readonly tabindex="-1" aria-label="رقم <?= $index + 1 ?> از کد پیست">
                    <?php endforeach; ?>
                </div>
                <input id="pasteUrlInput" type="text" class="visually-hidden" value="<?= e((string) $successLink) ?>" readonly tabindex="-1">
                <div class="access-actions">
                    <button id="copyPasteUrlBtn" class="btn btn-dark" type="button">کپی لینک</button>
                    <button id="toggleSuccessQrBtn" class="btn btn-ghost" type="button" aria-expanded="false" aria-controls="pasteSuccessQrPanel">کیوآر کد</button>
                </div>
                <p id="pasteUrlCopyFeedback" class="copy-url-feedback" role="status" aria-live="polite" hidden></p>
                <div id="pasteSuccessQrPanel" class="paste-qr" hidden>
                    <img class="paste-qr-img" src="<?= e('/' . $successCode . '/qr.svg') ?>" alt="کیوآر کد لینک پیست" width="160" height="160" loading="lazy" decoding="async">
                </div>
            <?php else: ?>
                <h2>کد دارید؟</h2>
                <p>چهار رقم را وارد کنید.</p>
                <form id="otpForm" class="otp-form">
                    <div id="otpGroup" class="otp-group" dir="ltr">
                        <?php for ($i = 0; $i < 4; $i++): ?>
                            <input class="otp-digit" type="text" inputmode="numeric" maxlength="1" autocomplete="<?= $i === 0 ? 'one-time-code' : 'off' ?>" aria-label="رقم <?= $i + 1 ?> از کد">
                        <?php endfor; ?>
                    </div>
                    <div id="otpMessage" class="otp-message" aria-live="polite">کد کامل یا معتبر نیست؛ دوباره بررسی کنید.</div>
                    <button id="otpSubmit" class="btn btn-dark otp-submit" type="submit">
                        <span class="btn-arrow" aria-hidden="true"></span>
                        <span class="btn-label">مشاهده پیست</span>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </aside>
</section>

<p class="trust-row">
    <span>بدون ثبت‌نام</span>
    <span>حذف خودکار</span>
    <span>بدون تبلیغات</span>
</p>

<script>
    (() => {
        const toEnglishDigits = (value) => String(value)
            .replace(/[۰-۹]/g, (digit) => String("۰۱۲۳۴۵۶۷۸۹".indexOf(digit)))
            .replace(/[٠-٩]/g, (digit) => String("٠١٢٣٤٥٦٧٨٩".indexOf(digit)));

        const formatBytes = (bytes) => {
            if (bytes < 1024) return `${bytes} B`;
            const units = ["KB", "MB", "GB"];
            let value = bytes;
            let unitIndex = -1;
            do {
                value /= 1024;
                unitIndex += 1;
            } while (value >= 1024 && unitIndex < units.length - 1);
            const rounded = value >= 100 || value === Math.floor(value)
                ? String(Math.round(value))
                : value.toFixed(1).replace(/\.0$/, "");
            return `${rounded} ${units[unitIndex]}`;
        };

        document.querySelectorAll("[data-toggle-row]").forEach((row) => {
            row.addEventListener("click", (event) => {
                const target = event.target;
                if (!(target instanceof Element)) return;
                if (target.closest("label.switch, input, button, a, select, textarea")) return;
                const checkbox = row.querySelector('input[type="checkbox"]');
                if (!(checkbox instanceof HTMLInputElement) || checkbox.disabled) return;
                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event("change", { bubbles: true }));
            });
        });

        const burnToggle = document.getElementById("burn_after_reading");
        const burnBanner = document.getElementById("burn_banner");
        const syncBurn = () => {
            const enabled = burnToggle instanceof HTMLInputElement && burnToggle.checked;
            burnToggle?.closest(".option-row")?.classList.toggle("option-active", enabled);
            if (burnBanner) {
                if (enabled) burnBanner.removeAttribute("hidden");
                else burnBanner.setAttribute("hidden", "");
            }
        };
        burnToggle?.addEventListener("change", syncBurn);
        syncBurn();

        const passwordToggle = document.getElementById("password_toggle");
        const passwordPanel = document.getElementById("password_panel");
        const passwordInput = document.getElementById("password");
        const passwordVisibility = document.getElementById("togglePasswordVisibility");
        const strengthMeter = document.getElementById("strengthMeter");
        const strengthLabel = document.getElementById("strengthLabel");
        const strengthLabels = ["خیلی کوتاه", "ضعیف", "متوسط", "خوب", "عالی"];

        const syncStrength = () => {
            if (!(passwordInput instanceof HTMLInputElement) || !strengthMeter || !strengthLabel) return;
            const password = passwordInput.value;
            const strength = Math.min(4,
                (password.length >= 8 ? 1 : 0) +
                (/[A-Z]/.test(password) ? 1 : 0) +
                (/\d/.test(password) ? 1 : 0) +
                (/[^A-Za-z0-9]/.test(password) ? 1 : 0)
            );
            strengthMeter.className = `strength-meter level-${strength}`;
            strengthMeter.querySelectorAll("span").forEach((bar, index) => {
                bar.classList.toggle("filled", password !== "" && index < strength);
            });
            strengthLabel.textContent = password ? strengthLabels[strength] : "قدرت رمز";
        };

        const syncPassword = () => {
            const enabled = passwordToggle instanceof HTMLInputElement && passwordToggle.checked;
            passwordToggle?.closest(".option-row")?.classList.toggle("option-active", enabled);
            if (passwordPanel) {
                if (enabled) passwordPanel.removeAttribute("hidden");
                else passwordPanel.setAttribute("hidden", "");
            }
            if (passwordInput instanceof HTMLInputElement) {
                passwordInput.disabled = !enabled;
                if (!enabled) passwordInput.value = "";
            }
            syncStrength();
        };
        passwordToggle?.addEventListener("change", syncPassword);
        passwordInput?.addEventListener("input", syncStrength);
        passwordVisibility?.addEventListener("click", () => {
            if (!(passwordInput instanceof HTMLInputElement)) return;
            const showing = passwordInput.type === "text";
            passwordInput.type = showing ? "password" : "text";
            passwordVisibility.textContent = showing ? "نمایش" : "پنهان";
        });
        syncPassword();

        const attachmentInput = document.getElementById("attachment");
        const attachmentDropZone = document.getElementById("attachment_drop_zone");
        const filePreview = document.getElementById("file_preview");
        const attachmentName = document.getElementById("attachment_file_name");
        const attachmentSize = document.getElementById("attachment_file_size");
        const removeAttachmentBtn = document.getElementById("removeAttachmentBtn");
        const uploadProgressPanel = document.getElementById("upload_progress_panel");
        const uploadProgressBar = document.getElementById("upload_progress_bar");
        const uploadProgressBarWrap = document.getElementById("upload_progress_bar_wrap");
        const uploadProgressPercent = document.getElementById("upload_progress_percent");
        const uploadProgressLabel = document.getElementById("upload_progress_label");

        const resetUploadProgress = () => {
            if (!uploadProgressPanel) return;
            uploadProgressPanel.setAttribute("hidden", "");
            if (uploadProgressBar) uploadProgressBar.style.width = "0%";
            if (uploadProgressPercent) uploadProgressPercent.textContent = "0%";
            if (uploadProgressBarWrap) uploadProgressBarWrap.setAttribute("aria-valuenow", "0");
        };

        const setUploadProgress = (percent, label) => {
            if (!uploadProgressPanel) return;
            const clamped = Math.max(0, Math.min(100, Math.round(percent)));
            uploadProgressPanel.removeAttribute("hidden");
            if (uploadProgressBar) uploadProgressBar.style.width = `${clamped}%`;
            if (uploadProgressPercent) uploadProgressPercent.textContent = `${clamped}%`;
            if (uploadProgressBarWrap) uploadProgressBarWrap.setAttribute("aria-valuenow", String(clamped));
            if (uploadProgressLabel && label) uploadProgressLabel.textContent = label;
        };

        const renderFile = () => {
            const file = attachmentInput instanceof HTMLInputElement ? attachmentInput.files?.[0] : null;
            if (!file || !filePreview || !attachmentDropZone) {
                filePreview?.setAttribute("hidden", "");
                attachmentDropZone?.removeAttribute("hidden");
                resetUploadProgress();
                return;
            }
            if (attachmentName) attachmentName.textContent = file.name;
            if (attachmentSize) attachmentSize.textContent = `${formatBytes(file.size)} · آماده ارسال`;
            attachmentDropZone.setAttribute("hidden", "");
            filePreview.removeAttribute("hidden");
            resetUploadProgress();
        };

        const assignFile = (file) => {
            if (!(file instanceof File) || !(attachmentInput instanceof HTMLInputElement)) return;
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            attachmentInput.files = dataTransfer.files;
            renderFile();
        };

        const isFileDrag = (event) => {
            const types = event.dataTransfer?.types;
            return !!types && Array.from(types).includes("Files");
        };

        if (attachmentInput instanceof HTMLInputElement && attachmentDropZone) {
            attachmentInput.addEventListener("change", renderFile);
            removeAttachmentBtn?.addEventListener("click", () => {
                attachmentInput.value = "";
                renderFile();
            });

            ["dragenter", "dragover"].forEach((name) => {
                attachmentDropZone.addEventListener(name, (event) => {
                    if (!isFileDrag(event)) return;
                    event.preventDefault();
                    attachmentDropZone.classList.add("dragging");
                });
            });
            attachmentDropZone.addEventListener("dragleave", () => attachmentDropZone.classList.remove("dragging"));
            attachmentDropZone.addEventListener("drop", (event) => {
                if (!isFileDrag(event)) return;
                event.preventDefault();
                event.stopPropagation();
                attachmentDropZone.classList.remove("dragging");
                const file = event.dataTransfer?.files?.[0];
                if (file) assignFile(file);
            });

            document.addEventListener("dragover", (event) => {
                if (isFileDrag(event)) event.preventDefault();
            });
            document.addEventListener("drop", (event) => {
                if (!isFileDrag(event)) return;
                if (event.target instanceof Element && event.target.closest("#attachment_drop_zone")) return;
                event.preventDefault();
                const file = event.dataTransfer?.files?.[0];
                if (file) assignFile(file);
            });
            renderFile();
        }

        const contentInput = document.getElementById("content");
        const contentCounter = document.getElementById("content_counter");
        const pasteSubmitBtn = document.getElementById("pasteSubmitBtn");
        const submitLabel = pasteSubmitBtn?.querySelector(".btn-label");
        const syncComposer = () => {
            if (!(contentInput instanceof HTMLTextAreaElement)) return;
            const maxLength = Number(contentInput.getAttribute("maxlength")) || 50000;
            if (contentCounter) {
                contentCounter.textContent = `${contentInput.value.length.toLocaleString("fa-IR")} از ${maxLength.toLocaleString("fa-IR")}`;
            }
            if (pasteSubmitBtn instanceof HTMLButtonElement && submitLabel?.textContent !== "در حال ارسال...") {
                pasteSubmitBtn.disabled = contentInput.value.trim() === "";
            }
        };
        contentInput?.addEventListener("input", syncComposer);
        syncComposer();

        const pasteForm = document.getElementById("pasteForm");
        const showFormError = (message) => {
            const box = document.getElementById("pasteFormError");
            const text = document.getElementById("pasteFormErrorText");
            if (!box || !text) return;
            text.textContent = message;
            box.removeAttribute("hidden");
            box.scrollIntoView({ behavior: "smooth", block: "nearest" });
        };
        const clearFormError = () => {
            document.getElementById("pasteFormError")?.setAttribute("hidden", "");
        };

        if (pasteForm && pasteSubmitBtn instanceof HTMLButtonElement) {
            const maxUpload = Math.max(0, Number(pasteForm.getAttribute("data-max-upload")) || 268435456);
            pasteForm.addEventListener("submit", (event) => {
                event.preventDefault();
                clearFormError();
                if (!(contentInput instanceof HTMLTextAreaElement) || !contentInput.value.trim()) {
                    contentInput?.reportValidity();
                    return;
                }
                const file = attachmentInput instanceof HTMLInputElement ? attachmentInput.files?.[0] : null;
                if (file && file.size > maxUpload) {
                    showFormError(`حجم فایل بیش از حد مجاز است (حداکثر ${formatBytes(maxUpload)}).`);
                    return;
                }

                const formData = new FormData(pasteForm);
                const xhr = new XMLHttpRequest();
                xhr.open("POST", pasteForm.getAttribute("action") || "/paste", true);
                xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
                xhr.setRequestHeader("Accept", "application/json");
                pasteSubmitBtn.disabled = true;
                if (submitLabel) submitLabel.textContent = "در حال ارسال...";
                setUploadProgress(0, "در حال آماده‌سازی...");

                xhr.upload.addEventListener("progress", (progressEvent) => {
                    if (!progressEvent.lengthComputable) {
                        setUploadProgress(0, "در حال آپلود...");
                        return;
                    }
                    const percent = (progressEvent.loaded / progressEvent.total) * 100;
                    setUploadProgress(percent, `در حال آپلود ${file?.name || "پیست"}...`);
                });

                const restoreSubmit = () => {
                    if (submitLabel) submitLabel.textContent = "ساخت پیست امن";
                    syncComposer();
                };

                xhr.addEventListener("load", () => {
                    restoreSubmit();
                    let payload = null;
                    try {
                        payload = JSON.parse(xhr.responseText);
                    } catch (_) {
                        showFormError("پاسخ سرور نامعتبر بود. دوباره تلاش کنید.");
                        resetUploadProgress();
                        return;
                    }
                    if (xhr.status >= 200 && xhr.status < 300 && payload?.ok) {
                        setUploadProgress(100, "آپلود کامل شد");
                        window.location.assign(payload.redirect || "/");
                        return;
                    }
                    resetUploadProgress();
                    showFormError(typeof payload?.error === "string" && payload.error !== "" ? payload.error : "خطا در ایجاد پیست. دوباره تلاش کنید.");
                });
                xhr.addEventListener("error", () => {
                    restoreSubmit();
                    resetUploadProgress();
                    showFormError("ارتباط با سرور برقرار نشد. اتصال اینترنت را بررسی کنید.");
                });
                xhr.addEventListener("abort", () => {
                    restoreSubmit();
                    resetUploadProgress();
                });
                xhr.send(formData);
            });
        }

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

        const copyBtn = document.getElementById("copyPasteUrlBtn");
        const pasteUrlInput = document.getElementById("pasteUrlInput");
        const copyFeedback = document.getElementById("pasteUrlCopyFeedback");
        let copyFeedbackTimer = 0;
        if (copyBtn && pasteUrlInput instanceof HTMLInputElement) {
            const copyLabel = copyBtn.textContent;
            copyBtn.addEventListener("click", async () => {
                const copied = await copyTextToClipboard(pasteUrlInput.value, pasteUrlInput);
                if (copyFeedback) {
                    copyFeedback.textContent = copied ? "کپی شد" : "لینک انتخاب شد؛ Ctrl+C را بزنید";
                    copyFeedback.removeAttribute("hidden");
                    window.clearTimeout(copyFeedbackTimer);
                    copyFeedbackTimer = window.setTimeout(() => copyFeedback.setAttribute("hidden", ""), 2200);
                }
                if (copied) {
                    copyBtn.classList.add("is-copied");
                    copyBtn.textContent = "کپی شد";
                    window.setTimeout(() => {
                        copyBtn.classList.remove("is-copied");
                        copyBtn.textContent = copyLabel;
                    }, 2000);
                }
            });
        }

        const qrToggleBtn = document.getElementById("toggleSuccessQrBtn");
        const qrPanel = document.getElementById("pasteSuccessQrPanel");
        qrToggleBtn?.addEventListener("click", () => {
            if (!qrPanel) return;
            const willShow = qrPanel.hasAttribute("hidden");
            if (willShow) qrPanel.removeAttribute("hidden");
            else qrPanel.setAttribute("hidden", "");
            qrToggleBtn.setAttribute("aria-expanded", willShow ? "true" : "false");
            qrToggleBtn.setAttribute("aria-label", willShow ? "پنهان کردن کیوآر کد" : "نمایش کیوآر کد");
        });

        document.querySelector(".access-panel[data-created]")?.scrollIntoView({ block: "nearest" });

        const otpForm = document.getElementById("otpForm");
        const otpDigits = Array.from(document.querySelectorAll(".otp-digit"));
        const otpGroup = document.getElementById("otpGroup");
        const otpMessage = document.getElementById("otpMessage");
        const otpSubmit = document.getElementById("otpSubmit");
        const otpLabel = otpSubmit?.querySelector(".btn-label");

        const otpInvalidMessage = "کد کامل یا معتبر نیست؛ دوباره بررسی کنید.";

        const setOtpError = (visible, message = otpInvalidMessage) => {
            if (visible && otpMessage) otpMessage.textContent = message;
            otpGroup?.classList.toggle("has-error", visible);
            otpMessage?.classList.toggle("visible", visible);
        };

        const restoreOtpSubmit = () => {
            if (otpSubmit instanceof HTMLButtonElement) otpSubmit.disabled = false;
            if (otpLabel) otpLabel.textContent = "مشاهده پیست";
        };

        otpDigits.forEach((input, index) => {
            if (!(input instanceof HTMLInputElement)) return;
            input.addEventListener("input", () => {
                const digit = toEnglishDigits(input.value).replace(/\D/g, "").slice(-1);
                input.value = digit;
                input.classList.toggle("is-filled", digit !== "");
                setOtpError(false);
                if (digit && otpDigits[index + 1] instanceof HTMLInputElement) otpDigits[index + 1].focus();
            });
            input.addEventListener("keydown", (event) => {
                if (event.key === "Backspace" && input.value === "" && otpDigits[index - 1] instanceof HTMLInputElement) {
                    otpDigits[index - 1].focus();
                }
            });
            input.addEventListener("paste", (event) => {
                const text = toEnglishDigits(event.clipboardData?.getData("text") || "").replace(/\D/g, "").slice(0, 4);
                if (text === "") return;
                event.preventDefault();
                otpDigits.forEach((digitInput, digitIndex) => {
                    if (digitInput instanceof HTMLInputElement) {
                        digitInput.value = text[digitIndex] || "";
                        digitInput.classList.toggle("is-filled", digitInput.value !== "");
                    }
                });
                const focusIndex = Math.min(text.length, otpDigits.length - 1);
                if (otpDigits[focusIndex] instanceof HTMLInputElement) otpDigits[focusIndex].focus();
            });
        });

        otpForm?.addEventListener("submit", async (event) => {
            event.preventDefault();
            const code = otpDigits.map((input) => input instanceof HTMLInputElement ? input.value : "").join("");
            if (!/^\d{4}$/.test(code)) {
                setOtpError(true);
                return;
            }
            setOtpError(false);
            if (otpSubmit instanceof HTMLButtonElement) otpSubmit.disabled = true;
            if (otpLabel) otpLabel.textContent = "در حال بررسی...";

            try {
                const res = await fetch(`/api/pastes/${code}`, {
                    headers: { Accept: "application/json" },
                    cache: "no-store",
                    credentials: "same-origin",
                });
                const data = await res.json().catch(() => null);
                if (res.ok && data?.ok) {
                    window.location.assign(`/${code}`);
                    return;
                }
                const message = typeof data?.error === "string" && data.error !== ""
                    ? data.error
                    : otpInvalidMessage;
                setOtpError(true, message);
            } catch (_) {
                setOtpError(true, "ارتباط با سرور برقرار نشد. اتصال اینترنت را بررسی کنید.");
            }
            restoreOtpSubmit();
        });
    })();
</script>
