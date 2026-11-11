<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'لوحة التحكم') ?> | لوحة التحكم</title>
<link href="https://fonts.googleapis.com/css2?family=Changa:wght@600;700&family=Alexandria:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css">
<script>
  // تطبيق الوضع الليلي فورًا قبل الرسم لتفادي وميض الشاشة (FOUC)
  (function(){
    var saved = localStorage.getItem('admin-theme');
    var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    document.documentElement.setAttribute('data-theme', theme);
  })();
</script>
</head>
<body>

<nav class="admin-topnav">
  <div class="topnav-inner">
    <a href="dashboard.php" class="topnav-logo"><?= e(get_setting($pdo, 'site_name', 'الموقع')) ?><span>لوحة التحكم</span></a>

    <div class="topnav-links">
      <a href="dashboard.php"  class="<?= ($activeNav ?? '') === 'dashboard'  ? 'active' : '' ?>">الرئيسية</a>
      <a href="projects.php"   class="<?= ($activeNav ?? '') === 'projects'   ? 'active' : '' ?>">الأعمال</a>
      <a href="categories.php" class="<?= ($activeNav ?? '') === 'categories' ? 'active' : '' ?>">التصنيفات</a>
      <a href="services.php"   class="<?= ($activeNav ?? '') === 'services'   ? 'active' : '' ?>">الخدمات</a>
      <a href="messages.php"   class="<?= ($activeNav ?? '') === 'messages'   ? 'active' : '' ?>">الرسائل</a>
      <a href="settings.php"   class="<?= ($activeNav ?? '') === 'settings'   ? 'active' : '' ?>">الإعدادات</a>
      <a href="profile.php"    class="<?= ($activeNav ?? '') === 'profile'    ? 'active' : '' ?>">حسابي</a>
    </div>

    <div class="topnav-actions">
      <span class="topnav-user">مرحبًا، <?= e($current_admin_username ?? '') ?></span>
      <button class="theme-toggle" id="themeToggle" type="button" aria-label="تبديل الوضع الليلي">
        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
        <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8z"/></svg>
      </button>
      <a href="../index.php" target="_blank" class="topnav-logout" style="color:var(--a-muted);">↗ الموقع</a>
      <a href="logout.php" class="topnav-logout">خروج</a>
    </div>

    <button class="topnav-toggle" id="topnavToggle" aria-label="فتح القائمة">
      <span></span><span></span><span></span>
    </button>
  </div>

  <div class="topnav-mobile" id="topnavMobile">
    <a href="dashboard.php"  class="<?= ($activeNav ?? '') === 'dashboard'  ? 'active' : '' ?>">الرئيسية</a>
    <a href="projects.php"   class="<?= ($activeNav ?? '') === 'projects'   ? 'active' : '' ?>">الأعمال</a>
    <a href="categories.php" class="<?= ($activeNav ?? '') === 'categories' ? 'active' : '' ?>">التصنيفات</a>
    <a href="services.php"   class="<?= ($activeNav ?? '') === 'services'   ? 'active' : '' ?>">الخدمات</a>
    <a href="messages.php"   class="<?= ($activeNav ?? '') === 'messages'   ? 'active' : '' ?>">الرسائل</a>
    <a href="settings.php"   class="<?= ($activeNav ?? '') === 'settings'   ? 'active' : '' ?>">الإعدادات</a>
    <a href="profile.php"    class="<?= ($activeNav ?? '') === 'profile'    ? 'active' : '' ?>">حسابي</a>
    <a href="../index.php" target="_blank">↗ عرض الموقع</a>
    <a href="logout.php" style="color:var(--a-err-ink);">⏻ تسجيل الخروج</a>
  </div>
</nav>

<div class="admin-content">
  <div class="page-title-row">
    <h1><?= e($pageTitle ?? '') ?></h1>
  </div>
  <?php if ($msg = flash_get('success')): ?><div class="alert alert-ok"><?= e($msg) ?></div><?php endif; ?>
  <?php if ($msg = flash_get('error')): ?><div class="alert alert-err"><?= e($msg) ?></div><?php endif; ?>
