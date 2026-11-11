<?php
require_once __DIR__ . '/includes/functions.php';
header('Content-Type: application/xml; charset=utf-8');

$projects   = $pdo->query("SELECT slug, created_at FROM projects WHERE status = 'published'")->fetchAll();
$categories = $pdo->query('SELECT slug FROM categories ORDER BY sort_order')->fetchAll();

$staticPages = ['index.php', 'about.php', 'services.php', 'portfolio.php', 'contact.php'];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <?php foreach ($staticPages as $page): ?>
  <url>
    <loc><?= e(full_url($page)) ?></loc>
    <changefreq>weekly</changefreq>
    <priority><?= $page === 'index.php' ? '1.0' : '0.7' ?></priority>
  </url>
  <?php endforeach; ?>

  <?php foreach ($categories as $cat): ?>
  <url>
    <loc><?= e(full_url('portfolio.php?cat=' . $cat['slug'])) ?></loc>
    <changefreq>weekly</changefreq>
    <priority>0.6</priority>
  </url>
  <?php endforeach; ?>

  <?php foreach ($projects as $p): ?>
  <url>
    <loc><?= e(full_url('project.php?slug=' . $p['slug'])) ?></loc>
    <lastmod><?= date('Y-m-d', strtotime($p['created_at'])) ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
  <?php endforeach; ?>
</urlset>
