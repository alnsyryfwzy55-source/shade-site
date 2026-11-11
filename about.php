<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle       = get_setting($pdo, 'site_name') . ' - من نحن | مظلات وسواتر الرياض';
$pageDescription = 'تعرّف على ' . get_setting($pdo, 'site_name') . ' المتخصصة في تصميم وتركيب المظلات والسواتر بالرياض، خبرتنا وآلية عملنا.';
$activePage      = 'about';
$canonicalPath   = 'about.php';

require __DIR__ . '/includes/site-header.php';
?>

<main>
  <section class="page-header">
    <div class="container">
      <div class="breadcrumb"><a href="<?= e(asset_path('index.php')) ?>">الرئيسية</a><span>/</span>من نحن</div>
      <h1><?= $s('about_title') ?></h1>
    </div>
  </section>

  <section class="about">
    <div class="container about-inner">
      <div class="about-media" aria-hidden="true"><div class="frame"></div></div>
      <div class="about-text">
        <p><?= $s('about_text') ?></p>
        <div class="stats">
          <div class="stat"><strong><?= $s('stat1_value') ?></strong><span><?= $s('stat1_label') ?></span></div>
          <div class="stat"><strong><?= $s('stat2_value') ?></strong><span><?= $s('stat2_label') ?></span></div>
          <div class="stat"><strong><?= $s('stat3_value') ?></strong><span><?= $s('stat3_label') ?></span></div>
        </div>
        <?php if (!empty($settings['coverage_areas'])): ?>
          <p style="margin-top:18px;"><strong>مناطق التغطية:</strong> <?= $s('coverage_areas') ?></p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <section class="why-us">
    <div class="container">
      <p class="eyebrow center">لماذا تختارنا</p>
      <h2 class="center">ظل يدوم لسنوات</h2>
      <div class="why-grid">
        <div class="why-card"><h3><?= $s('why_us_1_title') ?></h3><p><?= $s('why_us_1_text') ?></p></div>
        <div class="why-card"><h3><?= $s('why_us_2_title') ?></h3><p><?= $s('why_us_2_text') ?></p></div>
        <div class="why-card"><h3><?= $s('why_us_3_title') ?></h3><p><?= $s('why_us_3_text') ?></p></div>
      </div>
    </div>
  </section>

  <section class="process">
    <div class="container">
      <p class="eyebrow center">آلية العمل</p>
      <h2 class="center">من المعاينة إلى التركيب</h2>
      <div class="steps">
        <div class="step"><span class="step-num">01</span><h3>معاينة مجانية</h3><p>زيارة الموقع، أخذ القياسات الدقيقة، وتقديم عرض سعر واضح فورًا.</p></div>
        <div class="step"><span class="step-num">02</span><h3>التصميم والتصنيع</h3><p>تصنيع الهيكل المجلفن وقص القماش حسب القياسات في ورشتنا.</p></div>
        <div class="step"><span class="step-num">03</span><h3>التركيب</h3><p>فريق تركيب محترف ينجز العمل خلال أيام قليلة بدون إزعاج.</p></div>
        <div class="step"><span class="step-num">04</span><h3>تسليم وضمان</h3><p>معاينة نهائية معك وتسليم بطاقة ضمان مكتوبة على الهيكل والقماش.</p></div>
      </div>
    </div>
  </section>

  <section class="cta-band">
    <div class="container">
      <h2>لنبدأ مشروع الظل القادم</h2>
      <p>تواصل معنا اليوم واحجز موعد المعاينة المجانية في الرياض والمناطق المجاورة.</p>
      <a href="<?= e(asset_path('contact.php')) ?>" class="btn btn-accent">تواصل معنا</a>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/site-footer.php'; ?>
