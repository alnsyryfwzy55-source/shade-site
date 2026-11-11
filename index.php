<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle     = null;
$activePage    = 'home';
$canonicalPath = '';

$services = $pdo->query('SELECT * FROM services ORDER BY sort_order LIMIT 6')->fetchAll();
$featured = $pdo->query(
    "SELECT p.*, c.slug AS cat_slug, c.name AS cat_name FROM projects p
     LEFT JOIN categories c ON c.id = p.category_id
     WHERE p.status = 'published'
     ORDER BY p.is_featured DESC, p.created_at DESC LIMIT 3"
)->fetchAll();

require __DIR__ . '/includes/site-header.php';
?>

<main>
  <section class="hero">
    <div class="container hero-inner">
      <div class="hero-text">
        <p class="eyebrow">مظلات وسواتر في الرياض</p>
        <h1><?= nl2br($s('hero_title')) ?></h1>
        <p class="hero-desc"><?= $s('hero_desc') ?></p>
        <div class="hero-actions">
          <a href="<?= e(asset_path('portfolio.php')) ?>" class="btn btn-accent">شاهد أعمالنا</a>
          <a href="<?= e(asset_path('contact.php')) ?>" class="btn btn-ghost">اطلب معاينة مجانية</a>
        </div>
      </div>

      <div class="hero-visual">
        <svg class="shade-sketch" viewBox="0 0 480 420" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="مظلة حديثة بأشعة شمس">
          <circle cx="390" cy="70" r="30" fill="var(--accent)" opacity="0.9"/>
          <path d="M390 30v14M390 96v14M350 70h14M416 70h14M361 41l10 10M409 41l-10 10M361 99l10-10M409 99l-10 10" stroke="var(--accent)" stroke-width="3" stroke-linecap="round"/>
          <!-- أعمدة المظلة -->
          <rect x="70" y="180" width="14" height="160" fill="var(--ink)"/>
          <rect x="330" y="180" width="14" height="160" fill="var(--ink)"/>
          <!-- سقف المظلة -->
          <path d="M50 180 Q220 110 360 180Z" fill="var(--accent-2)"/>
          <path d="M50 180 Q220 110 360 180" stroke="var(--ink)" stroke-width="2"/>
          <!-- خطوط ظل متعرجة على الأرض -->
          <path d="M60 360h300" stroke="var(--ink)" stroke-width="2" opacity="0.7"/>
          <path d="M100 360 90 385M150 360 140 385M200 360 190 385M250 360 240 385M300 360 290 385" stroke="var(--ink)" stroke-width="2" opacity="0.25"/>
        </svg>

        <div class="spec-badges" aria-hidden="true">
          <span class="spec-badge">هيكل مجلفن ضد الصدأ</span>
          <span class="spec-badge">ضمان <?= $s('stat3_value') ?> سنوات</span>
          <span class="spec-badge"><?= $s('stat2_value') ?> <?= $s('stat2_label') ?></span>
        </div>
      </div>
    </div>
  </section>

  <section class="about">
    <div class="container about-inner">
      <div class="about-media" aria-hidden="true"><div class="frame"></div></div>
      <div class="about-text">
        <p class="eyebrow">من نحن</p>
        <h2><?= $s('about_title') ?></h2>
        <p><?= $s('about_text') ?></p>
        <div class="stats">
          <div class="stat"><strong><?= $s('stat1_value') ?></strong><span><?= $s('stat1_label') ?></span></div>
          <div class="stat"><strong><?= $s('stat2_value') ?></strong><span><?= $s('stat2_label') ?></span></div>
          <div class="stat"><strong><?= $s('stat3_value') ?></strong><span><?= $s('stat3_label') ?></span></div>
        </div>
        <a href="<?= e(asset_path('about.php')) ?>" class="btn btn-ghost" style="margin-top:20px;">تعرّف علينا أكثر</a>
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

  <section class="services">
    <div class="container">
      <p class="eyebrow center">خدماتنا</p>
      <h2 class="center">مظلات وسواتر بكل الأشكال</h2>
      <div class="services-grid">
        <?php foreach ($services as $sv): ?>
        <article class="service-card">
          <div class="service-icon"><?= service_icon_svg($sv['icon']) ?></div>
          <h3><?= e($sv['title']) ?></h3>
          <p><?= e($sv['description']) ?></p>
        </article>
        <?php endforeach; ?>
        <?php if (empty($services)): ?><p>لم تتم إضافة خدمات بعد.</p><?php endif; ?>
      </div>
      <div class="center" style="margin-top:32px;">
        <a href="<?= e(asset_path('services.php')) ?>" class="btn btn-ghost">كل الخدمات بالتفصيل</a>
      </div>
    </div>
  </section>

  <?php if ($featured): ?>
  <section class="portfolio">
    <div class="container">
      <p class="eyebrow center">أعمال مختارة</p>
      <h2 class="center">مشاريع من ملفنا</h2>
      <div class="gallery">
        <?php foreach ($featured as $i => $p): ?>
        <a class="gallery-item<?= $i === 0 ? ' tall' : '' ?>" href="<?= e(asset_path('project.php?slug=' . $p['slug'])) ?>">
          <?php if (!empty($p['before_image']) && !empty($p['after_image'])): ?>
            <span class="ba-badge">قبل / بعد</span>
          <?php endif; ?>
          <?php if ($p['cover_image']): ?>
            <img class="portfolio-img" src="uploads/portfolio/<?= e($p['cover_image']) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
          <?php else: ?>
            <div class="portfolio-img" style="--tone:<?= placeholder_tone($p['id']) ?>"></div>
          <?php endif; ?>
          <div class="gallery-caption"><span><?= e($p['title']) ?></span><small><?= e($p['location']) ?></small></div>
        </a>
        <?php endforeach; ?>
      </div>
      <div class="center" style="margin-top:32px;">
        <a href="<?= e(asset_path('portfolio.php')) ?>" class="btn btn-ghost">كل الأعمال</a>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <section class="cta-band">
    <div class="container">
      <h2>جاهز تحمي مساحتك من الشمس؟</h2>
      <p>احصل على معاينة ميدانية مجانية وعرض سعر واضح بدون أي التزام، لأي منطقة داخل الرياض وما حولها.</p>
      <a href="<?= e(asset_path('contact.php')) ?>" class="btn btn-accent">اطلب معاينة الآن</a>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/site-footer.php'; ?>
