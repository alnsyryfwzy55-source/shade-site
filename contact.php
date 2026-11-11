<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'تواصل معنا | ' . get_setting($pdo, 'site_name') . ' - مظلات الرياض';
$pageDescription = 'تواصل مع ' . get_setting($pdo, 'site_name') . ' لطلب معاينة مجانية لمظلات وسواتر في الرياض عبر الهاتف أو واتساب.';
$activePage      = 'contact';
$canonicalPath   = 'contact.php';

require __DIR__ . '/includes/site-header.php';
?>

<main>
  <section class="page-header">
    <div class="container">
      <div class="breadcrumb"><a href="<?= e(asset_path('index.php')) ?>">الرئيسية</a><span>/</span>تواصل معنا</div>
      <h1>اطلب معاينتك المجانية</h1>
    </div>
  </section>

  <section class="contact">
    <div class="container contact-inner">
      <div class="contact-info">
        <p class="eyebrow">معلومات التواصل</p>
        <h2>تواصل معنا مباشرة</h2>
        <p class="contact-desc">راسلنا أو اتصل مباشرة، ونرد عليك خلال ساعات العمل.</p>
        <ul class="contact-list">
          <li><a href="tel:<?= e($phone_href) ?>">📞 <?= $s('phone') ?></a></li>
          <li><a href="https://wa.me/<?= e($settings['whatsapp'] ?? '') ?>" target="_blank" rel="noopener">💬 واتساب: <?= $s('phone') ?></a></li>
          <li><a href="mailto:<?= $s('email') ?>">✉️ <?= $s('email') ?></a></li>
          <li>📍 <?= $s('address') ?></li>
          <?php if (!empty($settings['coverage_areas'])): ?><li>🚚 نغطي: <?= $s('coverage_areas') ?></li><?php endif; ?>
        </ul>

        <?php if (!empty($settings['map_embed_url'])): ?>
          <div class="map-embed">
            <iframe src="<?= e($settings['map_embed_url']) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="موقعنا على الخريطة"></iframe>
          </div>
        <?php endif; ?>
      </div>

      <form class="contact-form" id="contactForm">
        <label>الاسم<input type="text" name="name" required maxlength="120"></label>
        <label>رقم الهاتف / واتساب<input type="tel" name="phone" required maxlength="40"></label>
        <label>تفاصيل الطلب<textarea name="message" rows="5" required maxlength="3000" placeholder="نوع المظلة/الساتر، الموقع، والمساحة التقريبية..."></textarea></label>
        <button type="submit" class="btn btn-accent">إرسال الطلب</button>
        <p class="form-status" id="formStatus" role="status"></p>
      </form>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/site-footer.php'; ?>
