<?php
require_once __DIR__ . '/../includes/functions.php';

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$id    = $_GET['id'] ?? $_POST['id'] ?? '';
$error = '';
$success = false;
$admin = null;

if ($token && $id) {
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE id = ? AND reset_token = ? AND reset_expires > NOW()');
    $stmt->execute([$id, hash('sha256', $token)]);
    $admin = $stmt->fetch();
}

if (!$admin) {
    $error = 'رابط الاسترجاع غير صالح أو انتهت صلاحيته. يرجى طلب رابط جديد.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['password_confirm'] ?? '';

    if (mb_strlen($password) < 8) {
        $error = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
    } elseif ($password !== $confirm) {
        $error = 'كلمتا المرور غير متطابقتين';
    } else {
        $stmt = $pdo->prepare('UPDATE admins SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?');
        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $admin['id']]);
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>إعادة تعيين كلمة المرور</title>
<link href="https://fonts.googleapis.com/css2?family=Changa:wght@600;700&family=Alexandria:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css">
<script>
  (function(){
    var saved = localStorage.getItem('admin-theme');
    var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    document.documentElement.setAttribute('data-theme', theme);
  })();
</script>
</head>
<body class="auth-page">
  <div class="auth-card">
    <h1>كلمة مرور جديدة</h1>

    <?php if ($success): ?>
      <div class="alert alert-ok">تم تغيير كلمة المرور بنجاح.</div>
      <div class="auth-links"><a href="login.php">تسجيل الدخول الآن</a></div>

    <?php elseif ($error): ?>
      <div class="alert alert-err"><?= e($error) ?></div>
      <div class="auth-links"><a href="forgot-password.php">طلب رابط جديد</a></div>

    <?php else: ?>
      <p class="sub">أدخل كلمة مرور جديدة لحساب <?= e($admin['username']) ?></p>
      <form method="post" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="token" value="<?= e($token) ?>">
        <input type="hidden" name="id" value="<?= e($id) ?>">
        <div class="field-float">
          <input type="password" name="password" id="f-password" placeholder=" " required minlength="8" autofocus>
          <label for="f-password">كلمة المرور الجديدة (8 أحرف على الأقل)</label>
        </div>
        <div class="field-float">
          <input type="password" name="password_confirm" id="f-password-confirm" placeholder=" " required minlength="8">
          <label for="f-password-confirm">تأكيد كلمة المرور</label>
        </div>
        <button type="submit" class="btn-admin">حفظ كلمة المرور</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
