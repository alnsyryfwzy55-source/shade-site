<?php
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare(
    "SELECT p.*, c.name AS cat_name, c.slug AS cat_slug FROM projects p
     LEFT JOIN categories c ON c.id = p.category_id
     WHERE p.slug = ? AND p.status = 'published'"
);
$stmt->execute([$slug]);
$project = $stmt->fetch();

if (!$project) {
    http_response_code(404);
    $pageTitle     = 'الصفحة غير موجودة | ' . get_setting($pdo, 'site_name');
    $canonicalPath = 'project.php';
    require __DIR__ . '/includes/site-header.php';
    echo '<main><section class="page-header"><div class="container"><h1>عذرًا، هذا العمل غير موجود</h1>
          <p><a href="' . e(asset_path('portfolio.php')) . '" class="btn btn-accent">تصفّح كل الأعمال</a></p></div></section></main>';
    require __DIR__ . '/includes/site-footer.php';
    exit;
}

$galleryImages = get_project_images($pdo, $project['id']);
$hasBeforeAfter = !empty($project['before_image']) && !empty($project['after_image']);

$relatedStmt = $pdo->prepare(
    "SELECT * FROM projects WHERE category_id <=> ? AND id != ? AND status = 'published' ORDER BY created_at DESC LIMIT 3"
);
$relatedStmt->execute([$project['category_id'], $project['id']]);
$related = $relatedStmt->fetchAll();

$pageTitle       = e($project['title']) . ' | ' . get_setting($pdo, 'site_name');
$pageDescription = mb_substr(strip_tags($project['description'] ?? ''), 0, 155) ?: ('عمل مظلات وسواتر: ' . $project['title']);
$activePage      = 'portfolio';
$canonicalPath   = 'project.php?slug=' . $project['slug'];

$breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'الرئيسية', 'item' => full_url('index.php')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'أعمالنا', 'item' => full_url('portfolio.php')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $project['title'], 'item' => full_url($canonicalPath)],
    ],
];

require __DIR__ . '/includes/site-header.php';
?>

<main>
  <section class="project-hero">
    <div class="container">
      <div class="breadcrumb">
        <a href="<?= e(asset_path('index.php')) ?>">الرئيسية</a><span>/</span>
        <a href="<?= e(asset_path('portfolio.php')) ?>">أعمالنا</a><span>/</span>
        <?= e($project['title']) ?>
      </div>

      <h1><?= e($project['title']) ?></h1>

      <?php if ($hasBeforeAfter): ?>
        <div class="ba-slider" data-ba>
          <img src="uploads/portfolio/<?= e($project['after_image']) ?>" alt="بعد التركيب - <?= e($project['title']) ?>">
          <div class="ba-before-wrap">
            <img src="uploads/portfolio/<?= e($project['before_image']) ?>" alt="قبل التركيب - <?= e($project['title']) ?>">
          </div>
          <div class="ba-line"></div>
          <span class="ba-tag before">قبل</span>
          <span class="ba-tag after">بعد</span>
          <div class="ba-handle">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 6-6 6 6 6M15 6l6 6-6 6"/></svg>
          </div>
          <input type="range" class="ba-range" min="0" max="100" value="50" aria-label="اسحب لمقارنة قبل وبعد">
        </div>
        <p class="ba-hint">↔ اسحب الشريط لمشاهدة الفرق قبل وبعد التركيب</p>
      <?php elseif ($project['cover_image']): ?>
        <img class="project-cover" src="uploads/portfolio/<?= e($project['cover_image']) ?>" alt="<?= e($project['title']) ?>">
      <?php else: ?>
        <div class="project-cover-placeholder" style="background:<?= placeholder_tone($project['id']) ?>"></div>
      <?php endif; ?>

      <div class="project-meta">
        <?php if ($project['location']): ?><div><span>الموقع</span><strong><?= e($project['location']) ?></strong></div><?php endif; ?>
        <?php if ($project['cat_name']): ?><div><span>نوع العمل</span><strong><?= e($project['cat_name']) ?></strong></div><?php endif; ?>
        <div><span>سنة التنفيذ</span><strong><?= e(date('Y', strtotime($project['created_at']))) ?></strong></div>
      </div>

      <?php if ($project['description']): ?>
        <p><?= nl2br(e($project['description'])) ?></p>
      <?php endif; ?>

      <?php if ($galleryImages): ?>
        <h2 style="margin-top:32px;">صور إضافية من العمل</h2>
        <div class="project-gallery">
          <?php foreach ($galleryImages as $img): ?>
            <img src="uploads/portfolio/<?= e($img['image_path']) ?>" alt="<?= e($project['title']) ?>" loading="lazy">
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div style="margin-top:36px;">
        <a href="<?= e(asset_path('contact.php')) ?>" class="btn btn-accent">اطلب عملًا مشابهًا</a>
      </div>
    </div>
  </section>

  <?php if ($related): ?>
  <section class="related-projects">
    <div class="container">
      <p class="eyebrow center">أعمال ذات صلة</p>
      <h2 class="center">قد يعجبك أيضًا</h2>
      <div class="gallery">
        <?php foreach ($related as $rp): ?>
        <a class="gallery-item" href="<?= e(asset_path('project.php?slug=' . $rp['slug'])) ?>">
          <?php if (!empty($rp['before_image']) && !empty($rp['after_image'])): ?>
            <span class="ba-badge">قبل / بعد</span>
          <?php endif; ?>
          <?php if ($rp['cover_image']): ?>
            <img class="portfolio-img" src="uploads/portfolio/<?= e($rp['cover_image']) ?>" alt="<?= e($rp['title']) ?>" loading="lazy">
          <?php else: ?>
            <div class="portfolio-img" style="--tone:<?= placeholder_tone($rp['id']) ?>"></div>
          <?php endif; ?>
          <div class="gallery-caption"><span><?= e($rp['title']) ?></span><small><?= e($rp['location']) ?></small></div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
</main>

<?php require __DIR__ . '/includes/site-footer.php'; ?>
