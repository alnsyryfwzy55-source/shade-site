<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'خدمات المظلات والسواتر في الرياض | ' . get_setting($pdo, 'site_name');
$pageDescription = 'مظلات سيارات، مظلات حدائق، سواتر خصوصية، مظلات مسابح، وتركيب أقمشة PVC/HDPE في الرياض.';
$activePage      = 'services';
$canonicalPath   = 'services.php';

$services = $pdo->query('SELECT * FROM services ORDER BY sort_order')->fetchAll();

require __DIR__ . '/includes/site-header.php';
?>

<main>
  <section class="page-header">
    <div class="container">
      <div class="breadcrumb"><a href="<?= e(asset_path('index.php')) ?>">الرئيسية</a><span>/</span>خدماتنا</div>
      <h1>خدمات المظلات والسواتر</h1>
      <p style="max-width:60ch;margin:12px auto 0;">حلول ظل متكاملة لكل مساحة، من موقف السيارة حتى حديقة المنزل والمسبح.</p>
    </div>
  </section>

  <section class="services">
    <div class="container">
      <div class="services-grid">
        <?php foreach ($services as $sv): ?>
        <article class="service-card" id="service-<?= $sv['id'] ?>">
          <div class="service-icon"><?= service_icon_svg($sv['icon']) ?></div>
          <h3><?= e($sv['title']) ?></h3>
          <p><?= e($sv['description']) ?></p>
        </article>
        <?php endforeach; ?>
        <?php if (empty($services)): ?><p>لم تتم إضافة خدمات بعد.</p><?php endif; ?>
      </div>
    </div>
  </section>

  <section class="cta-band">
    <div class="container">
      <h2>تحتاج مقاس أو تصميم غير عادي؟</h2>
      <p>نصمم مظلات وسواتر مخصصة حسب مساحتك تمامًا، تواصل معنا وسنرسل فريقًا للمعاينة.</p>
      <a href="<?= e(asset_path('contact.php')) ?>" class="btn btn-accent">اسألنا الآن</a>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/site-footer.php'; ?>
