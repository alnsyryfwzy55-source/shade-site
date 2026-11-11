<?php
require_once __DIR__ . '/includes/functions.php';

$categories = $pdo->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll();

$catSlug = $_GET['cat'] ?? '';
$activeCat = null;
foreach ($categories as $c) {
    if ($c['slug'] === $catSlug) {
        $activeCat = $c;
        break;
    }
}

$perPage = 9;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

$where  = "WHERE p.status = 'published'";
$params = [];
if ($activeCat) {
    $where .= ' AND p.category_id = ?';
    $params[] = $activeCat['id'];
}

$totalStmt = $pdo->prepare("SELECT COUNT(*) c FROM projects p $where");
$totalStmt->execute($params);
$total = (int) $totalStmt->fetch()['c'];
$totalPages = max(1, (int) ceil($total / $perPage));

$sql = "SELECT p.*, c.slug AS cat_slug, c.name AS cat_name FROM projects p
        LEFT JOIN categories c ON c.id = p.category_id
        $where
        ORDER BY p.is_featured DESC, p.sort_order, p.created_at DESC
        LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

function portfolio_page_url($page, $catSlug)
{
    $params = [];
    if ($catSlug) $params['cat'] = $catSlug;
    if ($page > 1) $params['page'] = $page;
    $qs = http_build_query($params);
    return asset_path('portfolio.php') . ($qs ? '?' . $qs : '');
}

$pageTitle       = ($activeCat ? e($activeCat['name']) . ' | ' : '') . 'أعمالنا في الرياض | ' . get_setting($pdo, 'site_name');
$pageDescription = 'استعرض أعمال المظلات والسواتر المنجزة في الرياض: مظلات سيارات وحدائق ومسابح، وسواتر خصوصية.';
$activePage      = 'portfolio';
$canonicalPath   = portfolio_page_url($page, $catSlug);

require __DIR__ . '/includes/site-header.php';
?>

<main>
  <section class="page-header">
    <div class="container">
      <div class="breadcrumb"><a href="<?= e(asset_path('index.php')) ?>">الرئيسية</a><span>/</span>أعمالنا</div>
      <h1><?= $activeCat ? e($activeCat['name']) : 'أعمالنا' ?></h1>
    </div>
  </section>

  <section class="portfolio">
    <div class="container">
      <div class="filters">
        <a href="<?= e(portfolio_page_url(1, '')) ?>" class="filter-btn <?= $catSlug === '' ? 'active' : '' ?>">الكل</a>
        <?php foreach ($categories as $cat): ?>
          <a href="<?= e(portfolio_page_url(1, $cat['slug'])) ?>" class="filter-btn <?= $catSlug === $cat['slug'] ? 'active' : '' ?>"><?= e($cat['name']) ?></a>
        <?php endforeach; ?>
      </div>

      <div class="gallery">
        <?php foreach ($projects as $i => $p): ?>
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

      <?php if (empty($projects)): ?>
        <p class="empty-state">لا توجد أعمال في هذا التصنيف بعد.</p>
      <?php endif; ?>

      <?php if ($totalPages > 1): ?>
        <div class="pagination">
          <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <?php if ($p === $page): ?>
              <span class="current"><?= $p ?></span>
            <?php else: ?>
              <a href="<?= e(portfolio_page_url($p, $catSlug)) ?>"><?= $p ?></a>
            <?php endif; ?>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/site-footer.php'; ?>
