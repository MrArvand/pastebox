<?php

declare(strict_types=1);
?>
<section class="page-stack page-stack-narrow">
    <article class="surface-card">
        <div class="card-heading">
            <div class="heading-cluster">
                <span class="option-icon is-accent" aria-hidden="true">
                    <?php require BASE_PATH . '/app/Views/partials/icon-lock.php'; ?>
                </span>
                <div>
                    <span class="step-number">رمز</span>
                    <h2>این پیست محافظت شده است</h2>
                </div>
            </div>
        </div>

        <?php if (is_string($error) && $error !== ''): ?>
            <div class="alert-box alert-error">
                <span><?= e($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="post" action="/<?= e($code) ?>/unlock" class="stack">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <div>
                <label class="field-label" for="unlock_password">رمز عبور</label>
                <input id="unlock_password" class="text-input" type="password" name="password" placeholder="رمز عبور پیست را وارد کنید" required autocomplete="current-password">
                <p class="field-hint">پس از وارد کردن رمز صحیح، محتوای پیست نمایش داده می‌شود.</p>
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                <span>باز کردن پیست</span>
                <span class="btn-arrow" aria-hidden="true"></span>
            </button>
        </form>
    </article>
</section>
