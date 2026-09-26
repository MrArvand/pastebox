<?php

declare(strict_types=1);
?>
<?php
$maxUploadSize = (int) ($maxUploadSize ?? 268435456);
$maxUploadSizeLabel = is_string($maxUploadSizeLabel ?? null) && $maxUploadSizeLabel !== ''
    ? $maxUploadSizeLabel
    : format_bytes($maxUploadSize);
?>
<section class="page-wrap">
    <div id="pasteFormError" class="alert-box alert-error"<?= (is_string($error) && $error !== '') ? '' : ' hidden' ?>>
        <span id="pasteFormErrorText"><?= is_string($error) && $error !== '' ? e($error) : '' ?></span>
    </div>
    <?php if (is_string($error) && $error !== ''): ?>
        <div class="alert-box alert-error composer-flash-error">
            <span><?= e($error) ?></span>
        </div>
    <?php endif; ?>

    <?php if (is_string($successLink) && $successLink !== ''): ?>
        <div class="alert-box alert-success">
            <span>پیست شما با موفقیت ایجاد شد.</span>
            <div class="success-row">
                <input id="pasteUrlInput" type="text" class="text-left success-url-input" value="<?= e($successLink) ?>" readonly inputmode="url" autocomplete="off" spellcheck="false">
                <div class="success-actions">
                    <button id="copyPasteUrlBtn" class="btn-secondary icon-btn copy-btn" type="button" aria-label="کپی لینک" title="کپی لینک">
                        <svg class="icon-copy" viewBox="0 0 24 24" fill="none" focusable="false" aria-hidden="true">
                            <path d="M6.59961 11.3974C6.59961 8.67119 6.59961 7.3081 7.44314 6.46118C8.28667 5.61426 9.64432 5.61426 12.3596 5.61426H15.2396C17.9549 5.61426 19.3125 5.61426 20.1561 6.46118C20.9996 7.3081 20.9996 8.6712 20.9996 11.3974V16.2167C20.9996 18.9429 20.9996 20.306 20.1561 21.1529C19.3125 21.9998 17.9549 21.9998 15.2396 21.9998H12.3596C9.64432 21.9998 8.28667 21.9998 7.44314 21.1529C6.59961 20.306 6.59961 18.9429 6.59961 16.2167V11.3974Z" fill="currentColor"/>
                            <path opacity="0.5" d="M4.17157 3.17157C3 4.34315 3 6.22876 3 10V12C3 15.7712 3 17.6569 4.17157 18.8284C4.78913 19.446 5.6051 19.738 6.79105 19.8761C6.59961 19.0353 6.59961 17.8796 6.59961 16.2167V11.3974C6.59961 8.6712 6.59961 7.3081 7.44314 6.46118C8.28667 5.61426 9.64432 5.61426 12.3596 5.61426H15.2396C16.8915 5.61426 18.0409 5.61426 18.8777 5.80494C18.7403 4.61146 18.4484 3.79154 17.8284 3.17157C16.6569 2 14.7712 2 11 2C7.22876 2 5.34315 2 4.17157 3.17157Z" fill="currentColor"/>
                        </svg>
                    </button>
                    <?php if (is_string($successCode ?? null) && $successCode !== ''): ?>
                        <button
                            id="toggleSuccessQrBtn"
                            class="btn-secondary icon-btn qr-toggle-btn"
                            type="button"
                            aria-expanded="false"
                            aria-controls="pasteSuccessQrPanel"
                            aria-label="نمایش کیوآر کد"
                            title="نمایش کیوآر کد"
                        >
                            <svg class="icon-qr" viewBox="0 0 24 24" fill="currentColor" focusable="false" aria-hidden="true">
                                <path d="M3 3h6v6H3V3zm2 2v2h2V5H5zm8-2h6v6h-6V3zm2 2v2h2V5h-2zM3 15h6v6H3v-6zm2 2v2h2v-2H5zm13-2h2v2h-2v-2zm-2 0h-2v2h2v-2zm-2 4h2v2h-2v-2zm4 0h2v2h-2v-2zm-4-8h2v2h-2v-2zm4 0h2v2h-2v-2z"/>
                            </svg>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <p id="pasteUrlCopyFeedback" class="copy-url-feedback" role="status" aria-live="polite" hidden></p>
            <?php if (is_string($successCode ?? null) && $successCode !== ''): ?>
                <div id="pasteSuccessQrPanel" class="paste-qr paste-qr--inline" hidden>
                    <p class="paste-qr-caption">کیوآر کد لینک پیست</p>
                    <img
                        class="paste-qr-img"
                        src="<?= e(app_url($successCode . '/qr.svg')) ?>"
                        alt="کیوآر کد لینک پیست"
                        width="160"
                        height="160"
                        loading="lazy"
                        decoding="async"
                    >
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="panel composer">
        <div class="panel-body">
            <form
                id="pasteForm"
                action="/paste"
                method="post"
                enctype="multipart/form-data"
                class="composer-grid"
                data-max-upload="<?= (int) ($maxUploadSize ?? 268435456) ?>"
            >
                <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">

                <div class="split">
                    <fieldset class="field">
                        <div class="editor-toolbar">
                            <label for="content" class="section-heading">متن پیست</label>
                            <span class="hint">Editor</span>
                        </div>
                        <textarea
                            id="content"
                            name="content"
                            placeholder="متن خود را اینجا وارد کنید..."
                            required
                            maxlength="50000"
                        ></textarea>
                        <p id="content_counter" class="hint text-left" aria-live="polite">0/50,000</p>
                    </fieldset>

                    <aside class="side-stack">
                        <fieldset class="option-card field">
                            <label for="expires_in" class="section-heading section-heading-with-icon">
                                <span class="option-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2.75C17.1086 2.75 21.25 6.89137 21.25 12C21.25 12.4142 21.5858 12.75 22 12.75C22.4142 12.75 22.75 12.4142 22.75 12C22.75 6.06294 17.9371 1.25 12 1.25C11.5858 1.25 11.25 1.58579 11.25 2C11.25 2.41421 11.5858 2.75 12 2.75Z" fill="currentColor"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 8.25C12.4142 8.25 12.75 8.58579 12.75 9V12.25H16C16.4142 12.25 16.75 12.5858 16.75 13C16.75 13.4142 16.4142 13.75 16 13.75H12C11.5858 13.75 11.25 13.4142 11.25 13V9C11.25 8.58579 11.5858 8.25 12 8.25Z" fill="currentColor"/>
                                        <path opacity="0.5" fill-rule="evenodd" clip-rule="evenodd" d="M9.09958 2.39808C9.24874 2.7845 9.05641 3.21868 8.66999 3.36785C8.52855 3.42245 8.38879 3.48042 8.2508 3.54168C7.87221 3.70975 7.42906 3.5391 7.261 3.16051C7.09293 2.78193 7.26358 2.33878 7.64217 2.17071C7.80267 2.09946 7.96526 2.03201 8.12981 1.96849C8.51623 1.81932 8.95041 2.01166 9.09958 2.39808ZM5.6477 4.2408C5.93337 4.54075 5.92178 5.01549 5.62183 5.30115C5.51216 5.40559 5.40505 5.5127 5.30061 5.62237C5.01495 5.92232 4.54021 5.93391 4.24026 5.64824C3.94031 5.36258 3.92873 4.88785 4.21439 4.5879C4.33566 4.46056 4.46002 4.3362 4.58736 4.21493C4.88731 3.92927 5.36204 3.94085 5.6477 4.2408ZM3.15997 7.26154C3.53856 7.42961 3.70921 7.87275 3.54114 8.25134C3.47988 8.38933 3.42191 8.52909 3.36731 8.67053C3.21814 9.05695 2.78396 9.24928 2.39754 9.10012C2.01112 8.95095 1.81878 8.51677 1.96795 8.13035C2.03147 7.9658 2.09892 7.80321 2.17017 7.64271C2.33824 7.26412 2.78139 7.09347 3.15997 7.26154ZM2.02109 11.0046C2.43518 11.0146 2.76276 11.3584 2.75275 11.7725C2.75092 11.8483 2.75 11.9243 2.75 12.0005C2.75 12.0768 2.75092 12.1528 2.75275 12.2286C2.76276 12.6427 2.43518 12.9865 2.02109 12.9965C1.60699 13.0065 1.26319 12.6789 1.25319 12.2648C1.25107 12.177 1.25 12.0889 1.25 12.0005C1.25 11.9122 1.25107 11.8241 1.25319 11.7363C1.26319 11.3222 1.60699 10.9946 2.02109 11.0046ZM21.6025 14.901C21.9889 15.0501 22.1812 15.4843 22.032 15.8707C21.9685 16.0353 21.9011 16.1979 21.8298 16.3584C21.6618 16.737 21.2186 16.9076 20.84 16.7395C20.4614 16.5715 20.2908 16.1283 20.4589 15.7497C20.5201 15.6117 20.5781 15.472 20.6327 15.3306C20.7819 14.9441 21.216 14.7518 21.6025 14.901ZM2.39754 14.901C2.78396 14.7518 3.21814 14.9441 3.36731 15.3306C3.42191 15.472 3.47988 15.6117 3.54114 15.7497C3.70921 16.1283 3.53856 16.5715 3.15997 16.7395C2.78139 16.9076 2.33824 16.737 2.17017 16.3584C2.09892 16.1979 2.03147 16.0353 1.96795 15.8707C1.81878 15.4843 2.01112 15.0501 2.39754 14.901ZM19.7597 18.3528C20.0597 18.6385 20.0713 19.1132 19.7856 19.4132C19.6643 19.5405 19.54 19.6649 19.4126 19.7861C19.1127 20.0718 18.638 20.0602 18.3523 19.7603C18.0666 19.4603 18.0782 18.9856 18.3782 18.6999C18.4878 18.5955 18.5949 18.4884 18.6994 18.3787C18.9851 18.0788 19.4598 18.0672 19.7597 18.3528ZM4.24026 18.3528C4.54021 18.0672 5.01495 18.0788 5.30061 18.3787C5.40506 18.4884 5.51216 18.5955 5.62183 18.6999C5.92178 18.9856 5.93337 19.4603 5.6477 19.7603C5.36204 20.0602 4.88731 20.0718 4.58736 19.7861C4.46003 19.6649 4.33566 19.5405 4.21439 19.4132C3.92873 19.1132 3.94031 18.6385 4.24026 18.3528ZM7.261 20.8406C7.42907 20.462 7.87221 20.2913 8.2508 20.4594C8.38879 20.5207 8.52855 20.5786 8.66999 20.6332C9.05641 20.7824 9.24874 21.2166 9.09958 21.603C8.95041 21.9894 8.51623 22.1818 8.12981 22.0326C7.96526 21.9691 7.80267 21.9016 7.64217 21.8304C7.26358 21.6623 7.09293 21.2192 7.261 20.8406ZM16.739 20.8406C16.9071 21.2192 16.7364 21.6623 16.3578 21.8304C16.1973 21.9016 16.0347 21.9691 15.8702 22.0326C15.4838 22.1818 15.0496 21.9894 14.9004 21.603C14.7513 21.2166 14.9436 20.7824 15.33 20.6332C15.4714 20.5786 15.6112 20.5207 15.7492 20.4594C16.1278 20.2913 16.5709 20.462 16.739 20.8406ZM11.004 21.9795C11.0141 21.5654 11.3579 21.2378 11.7719 21.2478C11.8477 21.2496 11.9237 21.2505 12 21.2505C12.0763 21.2505 12.1523 21.2496 12.2281 21.2478C12.6421 21.2378 12.9859 21.5654 12.996 21.9795C13.006 22.3935 12.6784 22.7373 12.2643 22.7474C12.1764 22.7495 12.0883 22.7505 12 22.7505C11.9117 22.7505 11.8236 22.7495 11.7357 22.7474C11.3216 22.7373 10.994 22.3935 11.004 21.9795Z" fill="currentColor"/>
                                    </svg>
                                </span>
                                <span>زمان انقضا</span>
                            </label>
                            <select id="expires_in" name="expires_in" required>
                                <option value="5m">۵ دقیقه</option>
                                <option value="10m">۱۰ دقیقه</option>
                                <option value="1h">۱ ساعت</option>
                                <option value="1d" selected>۱ روز</option>
                                <option value="7d">۷ روز</option>
                                <option value="14d">۱۴ روز</option>
                            </select>
                            <p class="hint">بعد از این زمان، پیست قابل بازیابی نیست.</p>
                        </fieldset>

                        <fieldset class="option-card field">
                            <div class="switch-row">
                                <div class="switch-row-copy">
                                    <p class="switch-row-title switch-row-title-with-icon">
                                        <span class="option-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.5" d="M2 12C2 8.22876 2 6.34315 3.17157 5.17157C4.34315 4 6.22876 4 10 4H14C17.7712 4 19.6569 4 20.8284 5.17157C22 6.34315 22 8.22876 22 12C22 15.7712 22 17.6569 20.8284 18.8284C19.6569 20 17.7712 20 14 20H10C6.22876 20 4.34315 20 3.17157 18.8284C2 17.6569 2 15.7712 2 12Z" fill="currentColor"/>
                                                <path d="M12.7504 10C12.7504 9.58579 12.4146 9.25 12.0004 9.25C11.5861 9.25 11.2504 9.58579 11.2504 10V10.7012L10.6429 10.3505C10.2842 10.1434 9.82553 10.2663 9.61842 10.625C9.41131 10.9837 9.53422 11.4424 9.89294 11.6495L10.4999 11.9999L9.8927 12.3505C9.53398 12.5576 9.41108 13.0163 9.61818 13.375C9.82529 13.7337 10.284 13.8566 10.6427 13.6495L11.2504 13.2987V14C11.2504 14.4142 11.5861 14.75 12.0004 14.75C12.4146 14.75 12.7504 14.4142 12.7504 14V13.2993L13.357 13.6495C13.7158 13.8566 14.1745 13.7337 14.3816 13.375C14.5887 13.0163 14.4658 12.5576 14.107 12.3505L13.4999 11.9999L14.1068 11.6495C14.4655 11.4424 14.5884 10.9837 14.3813 10.625C14.1742 10.2663 13.7155 10.1434 13.3568 10.3505L12.7504 10.7006V10Z" fill="currentColor"/>
                                                <path d="M6.73278 9.25C7.147 9.25 7.48278 9.58579 7.48278 10V10.7006L8.08923 10.3505C8.44795 10.1434 8.90664 10.2663 9.11375 10.625C9.32085 10.9837 9.19795 11.4424 8.83923 11.6495L8.23229 11.9999L8.83946 12.3505C9.19818 12.5576 9.32109 13.0163 9.11398 13.375C8.90687 13.7337 8.44818 13.8566 8.08946 13.6495L7.48278 13.2993V14C7.48278 14.4142 7.147 14.75 6.73278 14.75C6.31857 14.75 5.98278 14.4142 5.98278 14V13.2987L5.37513 13.6495C5.01641 13.8566 4.55771 13.7337 4.35061 13.375C4.1435 13.0163 4.26641 12.5576 4.62513 12.3505L5.23229 11.9999L4.62536 11.6495C4.26664 11.4424 4.14373 10.9837 4.35084 10.625C4.55795 10.2663 5.01664 10.1434 5.37536 10.3505L5.98278 10.7012V10C5.98278 9.58579 6.31857 9.25 6.73278 9.25Z" fill="currentColor"/>
                                                <path d="M18.0182 10C18.0182 9.58579 17.6824 9.25 17.2682 9.25C16.854 9.25 16.5182 9.58579 16.5182 10V10.7012L15.9108 10.3505C15.552 10.1434 15.0934 10.2663 14.8863 10.625C14.6791 10.9837 14.802 11.4424 15.1608 11.6495L15.7677 11.9999L15.1605 12.3505C14.8018 12.5576 14.6789 13.0163 14.886 13.375C15.0931 13.7337 15.5518 13.8566 15.9105 13.6495L16.5182 13.2987V14C16.5182 14.4142 16.854 14.75 17.2682 14.75C17.6824 14.75 18.0182 14.4142 18.0182 14V13.2993L18.6249 13.6495C18.9836 13.8566 19.4423 13.7337 19.6494 13.375C19.8565 13.0163 19.7336 12.5576 19.3749 12.3505L18.7677 11.9999L19.3746 11.6495C19.7334 11.4424 19.8563 10.9837 19.6492 10.625C19.442 10.2663 18.9834 10.1434 18.6246 10.3505L18.0182 10.7006V10Z" fill="currentColor"/>
                                            </svg>
                                        </span>
                                        <span>رمز عبور</span>
                                    </p>
                                </div>
                                <label class="switch" for="password_toggle">
                                    <input id="password_toggle" type="checkbox">
                                    <span class="switch-track"></span>
                                </label>
                            </div>
                            <div id="password_panel" class="toggle-panel">
                                <label for="password" class="hint">رمز عبور پیست</label>
                                <input id="password" name="password" type="password" placeholder="برای محافظت بیشتر" disabled>
                            </div>
                        </fieldset>

                        <fieldset class="option-card field">
                            <div class="switch-row">
                                <div class="switch-row-copy">
                                    <p class="switch-row-title switch-row-title-with-icon">
                                        <span class="option-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M11.2442 1.95482C12.9441 1.01506 15.0345 1.01506 16.7345 1.95482C17.3641 2.30291 17.9518 2.86575 18.9065 3.78014C18.9373 3.80965 18.9685 3.83952 19.0001 3.86977C19.2993 4.15623 19.3096 4.631 19.0231 4.93018C18.7366 5.22936 18.2619 5.23967 17.9627 4.9532C16.8844 3.92069 16.4452 3.50886 16.0087 3.26758C14.7604 2.57747 13.2183 2.57747 11.9699 3.26758C11.5334 3.50886 11.0943 3.92069 10.0159 4.9532L4.02651 10.6881C3.72732 10.9745 3.25256 10.9642 2.9661 10.665C2.67963 10.3659 2.68994 9.8911 2.98912 9.60464L8.97855 3.86977C9.01014 3.83952 9.04133 3.80965 9.07214 3.78014C10.0268 2.86575 10.6145 2.30291 11.2442 1.95482ZM14.945 6.76023C15.2314 6.46104 15.7062 6.45074 16.0054 6.7372C16.0326 6.76328 16.0596 6.78906 16.0863 6.81457C16.4533 7.16504 16.7689 7.46639 16.9459 7.81825C17.2622 8.44722 17.2622 9.18288 16.9459 9.81184C16.7689 10.1637 16.4533 10.4651 16.0863 10.8155C16.0596 10.841 16.0326 10.8668 16.0054 10.8929L8.62553 17.9591C8.32635 18.2455 7.85159 18.2352 7.56512 17.936C7.27866 17.6369 7.28897 17.1621 7.58815 16.8756L14.968 9.80945C15.4628 9.33562 15.5615 9.22595 15.6058 9.13786C15.7089 8.93291 15.7089 8.69718 15.6058 8.49224C15.5615 8.40415 15.4628 8.29447 14.968 7.82064C14.6688 7.53417 14.6585 7.05941 14.945 6.76023Z" fill="currentColor"/>
                                                <path opacity="0.5" d="M17.9628 4.95358C19.0429 5.9878 19.4703 6.40622 19.7194 6.81926C20.4269 7.99256 20.4269 9.43347 19.7194 10.6068C19.4703 11.0198 19.0429 11.4382 17.9628 12.4725L10.5295 19.5898C9.97082 20.1247 9.58528 20.493 9.26135 20.7543C8.94539 21.0092 8.7384 21.1195 8.55399 21.1725C8.19285 21.2763 7.80709 21.2763 7.44595 21.1725C7.26154 21.1195 7.05455 21.0092 6.73859 20.7543C6.41466 20.493 6.02912 20.1247 5.47047 19.5898C4.91163 19.0547 4.52727 18.6858 4.2548 18.3761C3.98794 18.0728 3.87862 17.8805 3.82747 17.7174C3.72418 17.388 3.72418 17.0378 3.82747 16.7084C3.87862 16.5453 3.98794 16.353 4.2548 16.0497C4.52727 15.74 4.91163 15.3711 5.47047 14.836L12.7968 7.82101C13.2887 7.35 13.4068 7.25113 13.5074 7.20474C13.7436 7.09583 14.0213 7.09583 14.2575 7.20474C14.3581 7.25113 14.4761 7.35 14.9681 7.82101C14.9682 7.82113 14.9679 7.8209 14.9681 7.82101C14.6689 7.53455 14.6585 7.05916 14.945 6.75998C15.2259 6.46658 15.6879 6.45099 15.9878 6.72068L15.9274 6.66272C15.558 6.30819 15.2461 6.00881 14.8856 5.84257C14.2508 5.54989 13.5141 5.54989 12.8793 5.84257C12.5188 6.00881 12.2069 6.30819 11.8375 6.66271L4.40899 13.7756C3.88009 14.282 3.44862 14.6951 3.12862 15.0589C2.79815 15.4345 2.53687 15.811 2.3962 16.2596C2.20127 16.8811 2.20127 17.5447 2.3962 18.1663C2.53687 18.6148 2.79815 18.9913 3.12862 19.3669C3.44862 19.7307 3.88006 20.1438 4.40896 20.6502L4.4568 20.696C4.98604 21.2027 5.41736 21.6157 5.79685 21.9218C6.18996 22.2389 6.57706 22.4834 7.03145 22.6141C7.66343 22.7958 8.3365 22.7958 8.96849 22.6141C9.42288 22.4834 9.80998 22.2389 10.2031 21.9218C10.5826 21.6157 11.0139 21.2028 11.5431 20.696L19.0979 13.4623C20.0489 12.552 20.6381 11.988 21.0039 11.3813C21.9987 9.73161 21.9987 7.69442 21.0039 6.04471C20.6381 5.43798 20.0489 4.874 19.0979 3.96371L19.0175 3.88672C19.2996 4.17468 19.3039 4.63666 19.0231 4.92993C18.7366 5.22911 18.262 5.24004 17.9628 4.95358Z" fill="currentColor"/>
                                            </svg>
                                        </span>
                                        <span>فایل پیوست</span>
                                    </p>
                                </div>
                                <label class="switch" for="attachment_toggle">
                                    <input id="attachment_toggle" type="checkbox">
                                    <span class="switch-track"></span>
                                </label>
                            </div>
                            <div id="attachment_panel" class="toggle-panel">
                                <p class="hint">انتخاب فایل</p>
                                <div
                                    id="attachment_drop_zone"
                                    class="file-picker"
                                    role="button"
                                    tabindex="0"
                                    aria-label="انتخاب یا رها کردن فایل"
                                >
                                    <span class="file-picker-cta">انتخاب فایل</span>
                                    <span class="file-picker-caption">یا فایل را اینجا رها کنید</span>
                                </div>
                                <input
                                    id="attachment"
                                    name="attachment"
                                    type="file"
                                    class="visually-hidden-file-input"
                                    disabled
                                    accept="<?= e($allowedFileAccept ?? '') ?>"
                                >
                                <p id="attachment_file_name" class="hint file-picker-selected">فایلی انتخاب نشده است.</p>
                                <div id="upload_progress_panel" class="upload-progress" hidden>
                                    <div class="upload-progress-head">
                                        <span id="upload_progress_label" class="upload-progress-label">در حال آپلود...</span>
                                        <span id="upload_progress_percent" class="upload-progress-percent" dir="ltr">0%</span>
                                    </div>
                                    <div class="upload-progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="upload_progress_bar_wrap">
                                        <div id="upload_progress_bar" class="upload-progress-fill" style="width: 0%"></div>
                                    </div>
                                </div>
                                <p class="hint">فرمت‌های مجاز: <?= e($allowedFormatsHint ?? 'Office، PDF، تصویر، صدا، ویدیو، فشرده و...') ?> — حداکثر <?= e($maxUploadSizeLabel ?? '256 MB') ?></p>
                            </div>
                        </fieldset>

                        <fieldset class="option-card field">
                            <div class="switch-row">
                                <div class="switch-row-copy">
                                    <p class="switch-row-title switch-row-title-with-icon">
                                        <span class="option-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.5" d="M12.8324 21.8013C15.9583 21.1747 20 18.926 20 13.1112C20 7.8196 16.1267 4.29593 13.3415 2.67685C12.7235 2.31757 12 2.79006 12 3.50492V5.3334C12 6.77526 11.3938 9.40711 9.70932 10.5018C8.84932 11.0607 7.92052 10.2242 7.816 9.20388L7.73017 8.36604C7.6304 7.39203 6.63841 6.80075 5.85996 7.3946C4.46147 8.46144 3 10.3296 3 13.1112C3 20.2223 8.28889 22.0001 10.9333 22.0001C11.0871 22.0001 11.2488 21.9955 11.4171 21.9858C11.863 21.9296 11.4171 22.085 12.8324 21.8013Z" fill="currentColor"/>
                                                <path d="M8 18.4442C8 21.064 10.1113 21.8742 11.4171 21.9858C11.863 21.9296 11.4171 22.085 12.8324 21.8013C13.871 21.4343 15 20.4922 15 18.4442C15 17.1465 14.1814 16.3459 13.5401 15.9711C13.3439 15.8564 13.1161 16.0008 13.0985 16.2273C13.0429 16.9454 12.3534 17.5174 11.8836 16.9714C11.4685 16.4889 11.2941 15.784 11.2941 15.3331V14.7439C11.2941 14.3887 10.9365 14.1533 10.631 14.3346C9.49507 15.0085 8 16.3949 8 18.4442Z" fill="currentColor"/>
                                            </svg>
                                        </span>
                                        <span>حذف پس از اولین مشاهده</span>
                                    </p>
                                </div>
                                <label class="switch" for="burn_after_reading">
                                    <input id="burn_after_reading" name="burn_after_reading" type="checkbox" value="1">
                                    <span class="switch-track"></span>
                                </label>
                            </div>
                        </fieldset>
                    </aside>
                </div>

                <button id="pasteSubmitBtn" type="submit" class="btn-primary btn-block">ایجاد پیست</button>
            </form>
        </div>
    </div>
</section>

<script>
    (() => {
        const bindTogglePanel = (toggleId, panelId, inputId) => {
            const toggle = document.getElementById(toggleId);
            const panel = document.getElementById(panelId);
            const input = document.getElementById(inputId);
            if (!toggle || !panel || !input) return;

            const update = () => {
                const enabled = toggle.checked;
                panel.classList.toggle("is-visible", enabled);
                input.disabled = !enabled;
            };

            toggle.addEventListener("change", update);
            update();
        };

        bindTogglePanel("password_toggle", "password_panel", "password");
        bindTogglePanel("attachment_toggle", "attachment_panel", "attachment");

        document.querySelectorAll(".switch-row").forEach((row) => {
            row.addEventListener("click", (event) => {
                const target = event.target;
                if (!(target instanceof Element)) return;
                if (target.closest("label.switch, input, button, a, select, textarea")) return;

                const checkbox = row.querySelector('label.switch input[type="checkbox"]');
                if (!(checkbox instanceof HTMLInputElement) || checkbox.disabled) return;

                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event("change", { bubbles: true }));
            });
        });

        const attachmentInput = document.getElementById("attachment");
        const attachmentName = document.getElementById("attachment_file_name");
        const attachmentToggle = document.getElementById("attachment_toggle");
        const attachmentPanel = document.getElementById("attachment_panel");
        const attachmentDropZone = document.getElementById("attachment_drop_zone");
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

        if (attachmentInput && attachmentName && attachmentToggle && attachmentPanel) {
            const isFileDrag = (event) => {
                const types = event.dataTransfer?.types;
                if (!types) return false;
                return Array.from(types).includes("Files");
            };

            const enableAttachmentPanel = () => {
                attachmentToggle.checked = true;
                attachmentPanel.classList.add("is-visible");
                attachmentInput.disabled = false;
            };

            const updateAttachmentName = () => {
                const fileName = attachmentInput.files && attachmentInput.files[0]
                    ? attachmentInput.files[0].name
                    : "فایلی انتخاب نشده است.";
                attachmentName.textContent = fileName;
                resetUploadProgress();
            };

            const assignFile = (file) => {
                if (!(file instanceof File)) return;
                enableAttachmentPanel();
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                attachmentInput.files = dataTransfer.files;
                updateAttachmentName();
            };

            const allowFileDrop = (event) => {
                if (!isFileDrag(event)) return;
                event.preventDefault();
                event.stopPropagation();
                if (event.dataTransfer) {
                    event.dataTransfer.dropEffect = "copy";
                }
            };

            attachmentInput.addEventListener("change", updateAttachmentName);
            attachmentToggle.addEventListener("change", () => {
                if (!attachmentToggle.checked) {
                    attachmentInput.disabled = true;
                    attachmentInput.value = "";
                    attachmentName.textContent = "فایلی انتخاب نشده است.";
                    resetUploadProgress();
                } else {
                    attachmentInput.disabled = false;
                }
            });
            updateAttachmentName();

            const bindDropTarget = (element) => {
                if (!element) return;

                element.addEventListener("dragenter", (event) => {
                    allowFileDrop(event);
                    enableAttachmentPanel();
                    element.classList.add("is-dragover");
                });

                element.addEventListener("dragover", allowFileDrop);

                element.addEventListener("dragleave", (event) => {
                    if (event.relatedTarget instanceof Node && element.contains(event.relatedTarget)) {
                        return;
                    }
                    element.classList.remove("is-dragover");
                });

                element.addEventListener("drop", (event) => {
                    if (!isFileDrag(event)) return;
                    event.preventDefault();
                    event.stopPropagation();
                    element.classList.remove("is-dragover");
                    document.body.classList.remove("is-file-dragging");
                    const file = event.dataTransfer?.files?.[0];
                    if (file) assignFile(file);
                });
            };

            bindDropTarget(attachmentDropZone);
            bindDropTarget(attachmentPanel);

            if (attachmentDropZone) {
                attachmentDropZone.addEventListener("click", () => {
                    enableAttachmentPanel();
                    attachmentInput.click();
                });

                attachmentDropZone.addEventListener("keydown", (event) => {
                    if (event.key !== "Enter" && event.key !== " ") return;
                    event.preventDefault();
                    enableAttachmentPanel();
                    attachmentInput.click();
                });
            }

            let fileDragDepth = 0;
            document.addEventListener("dragenter", (event) => {
                if (!isFileDrag(event)) return;
                fileDragDepth += 1;
                allowFileDrop(event);
                enableAttachmentPanel();
                document.body.classList.add("is-file-dragging");
            });

            document.addEventListener("dragleave", (event) => {
                if (!isFileDrag(event)) return;
                fileDragDepth = Math.max(0, fileDragDepth - 1);
                if (fileDragDepth === 0) {
                    document.body.classList.remove("is-file-dragging");
                    attachmentDropZone?.classList.remove("is-dragover");
                    attachmentPanel.classList.remove("is-dragover");
                }
            });

            document.addEventListener("dragover", (event) => {
                if (!isFileDrag(event)) return;
                allowFileDrop(event);
            });

            document.addEventListener("drop", (event) => {
                if (!isFileDrag(event)) return;
                fileDragDepth = 0;
                document.body.classList.remove("is-file-dragging");
                if (event.target instanceof Element && event.target.closest("#attachment_panel")) {
                    return;
                }
                event.preventDefault();
                const file = event.dataTransfer?.files?.[0];
                if (file) assignFile(file);
            });
        }

        const pasteForm = document.getElementById("pasteForm");
        const pasteSubmitBtn = document.getElementById("pasteSubmitBtn");
        const formErrorHost = document.querySelector(".page-wrap");

        const showFormError = (message) => {
            let box = document.getElementById("pasteFormError");
            if (!box && formErrorHost) {
                box = document.createElement("div");
                box.id = "pasteFormError";
                box.className = "alert-box alert-error";
                box.innerHTML = "<span></span>";
                formErrorHost.prepend(box);
            }
            if (!box) return;
            const span = box.querySelector("span");
            if (span) span.textContent = message;
            box.scrollIntoView({ behavior: "smooth", block: "nearest" });
        };

        const clearFormError = () => {
            document.getElementById("pasteFormError")?.remove();
        };

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

        if (pasteForm && pasteSubmitBtn) {
            const maxUpload = Math.max(0, Number(pasteForm.getAttribute("data-max-upload")) || 268435456);

            pasteForm.addEventListener("submit", (event) => {
                event.preventDefault();
                clearFormError();

                const contentInput = document.getElementById("content");
                if (contentInput instanceof HTMLTextAreaElement && !contentInput.value.trim()) {
                    contentInput.reportValidity();
                    return;
                }

                if (attachmentToggle?.checked && attachmentInput?.files?.[0]) {
                    const file = attachmentInput.files[0];
                    if (file.size > maxUpload) {
                        showFormError(`حجم فایل بیش از حد مجاز است (حداکثر ${formatBytes(maxUpload)}).`);
                        return;
                    }
                }

                const formData = new FormData(pasteForm);
                const xhr = new XMLHttpRequest();
                xhr.open("POST", pasteForm.getAttribute("action") || "/paste", true);
                xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
                xhr.setRequestHeader("Accept", "application/json");

                pasteSubmitBtn.disabled = true;
                pasteSubmitBtn.textContent = "در حال ارسال...";
                setUploadProgress(0, "در حال آماده‌سازی...");

                xhr.upload.addEventListener("progress", (progressEvent) => {
                    if (!progressEvent.lengthComputable) {
                        setUploadProgress(0, "در حال آپلود...");
                        return;
                    }
                    const percent = (progressEvent.loaded / progressEvent.total) * 100;
                    const fileLabel = attachmentInput?.files?.[0]?.name || "فایل";
                    setUploadProgress(percent, `در حال آپلود ${fileLabel}...`);
                });

                xhr.addEventListener("load", () => {
                    pasteSubmitBtn.disabled = false;
                    pasteSubmitBtn.textContent = "ایجاد پیست";

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
                    showFormError(
                        typeof payload?.error === "string" && payload.error !== ""
                            ? payload.error
                            : "خطا در ایجاد پیست. دوباره تلاش کنید."
                    );
                });

                xhr.addEventListener("error", () => {
                    pasteSubmitBtn.disabled = false;
                    pasteSubmitBtn.textContent = "ایجاد پیست";
                    resetUploadProgress();
                    showFormError("ارتباط با سرور برقرار نشد. اتصال اینترنت را بررسی کنید.");
                });

                xhr.addEventListener("abort", () => {
                    pasteSubmitBtn.disabled = false;
                    pasteSubmitBtn.textContent = "ایجاد پیست";
                    resetUploadProgress();
                });

                xhr.send(formData);
            });
        }

        const contentInput = document.getElementById("content");
        const contentCounter = document.getElementById("content_counter");
        if (contentInput && contentCounter) {
            const maxLength = Number(contentInput.getAttribute("maxlength")) || 50000;
            const updateCounter = () => {
                contentCounter.textContent = `${contentInput.value.length.toLocaleString("en-US")}/${maxLength.toLocaleString("en-US")}`;
            };

            contentInput.addEventListener("input", updateCounter);
            updateCounter();
        }

        const copyBtn = document.getElementById("copyPasteUrlBtn");
        const pasteUrlInput = document.getElementById("pasteUrlInput");
        const copyFeedback = document.getElementById("pasteUrlCopyFeedback");
        let copyFeedbackTimer = 0;

        const showCopyFeedback = (message) => {
            if (!copyFeedback) return;
            copyFeedback.textContent = message;
            copyFeedback.removeAttribute("hidden");
            window.clearTimeout(copyFeedbackTimer);
            copyFeedbackTimer = window.setTimeout(() => {
                copyFeedback.setAttribute("hidden", "");
                copyFeedback.textContent = "";
            }, 2200);
        };

        /**
         * Copy without relying only on Clipboard API (often blocked on http / non‑secure origins).
         */
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

        if (copyBtn && pasteUrlInput) {
            copyBtn.addEventListener("click", async () => {
                const text = pasteUrlInput.value;
                const copied = await copyTextToClipboard(text, pasteUrlInput);
                if (copied) {
                    copyBtn.classList.add("is-copied");
                    copyBtn.setAttribute("title", "کپی شد");
                    copyBtn.setAttribute("aria-label", "کپی شد");
                    showCopyFeedback("کپی شد");
                    window.setTimeout(() => {
                        copyBtn.classList.remove("is-copied");
                        copyBtn.setAttribute("title", "کپی لینک");
                        copyBtn.setAttribute("aria-label", "کپی لینک");
                    }, 2000);
                } else {
                    pasteUrlInput.focus();
                    pasteUrlInput.select();
                    showCopyFeedback("لینک انتخاب شد؛ Ctrl+C را بزنید");
                }
            });
        }

        const qrToggleBtn = document.getElementById("toggleSuccessQrBtn");
        const qrPanel = document.getElementById("pasteSuccessQrPanel");
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
