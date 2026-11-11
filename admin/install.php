<?php
require_once __DIR__ . '/../includes/functions.php';

// إن وُجد مسؤول واحد على الأقل، امنع الوصول لهذه الصفحة نهائيًا
$count = $pdo->query('SELECT COUNT(*) c FROM admins')->fetch()['c'];
if ($count > 0) {
    redirect('login.php');
}

$errors = [];
$old = ['username' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['password_confirm'] ?? '';
    $old = ['username' => $username, 'email' => $email];

    if (mb_strlen($username) < 3) $errors[] = 'اسم المستخدم يجب أن يكون 3 أحرف على الأقل';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'البريد الإلكتروني غير صالح';
    if (mb_strlen($password) < 8) $errors[] = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
    if ($password !== $confirm) $errors[] = 'كلمتا المرور غير متطابقتين';

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO admins (username, email, password_hash) VALUES (?, ?, ?)');
        $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
        flash_set('success', 'تم إنشاء حسابك بنجاح، سجّل الدخول الآن.');
        redirect('login.php');
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تثبيت لوحة التحكم</title>
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
    <h1>مرحبًا 👋</h1>
    <p class="sub">هذه أول مرة تُشغَّل فيها لوحة التحكم. أنشئ حساب المسؤول الرئيسي.</p>

    <?php foreach ($errors as $err): ?><div class="alert alert-err"><?= e($err) ?></div><?php endforeach; ?>

    <form method="post" class="admin-form">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <div class="field-float">
        <input type="text" name="username" id="f-username" placeholder=" " required value="<?= e($old['username']) ?>">
        <label for="f-username">اسم المستخدم</label>
      </div>
      <div class="field-float">
        <input type="email" name="email" id="f-email" placeholder=" " required value="<?= e($old['email']) ?>">
        <label for="f-email">البريد الإلكتروني (لاسترجاع كلمة المرور)</label>
      </div>
      <div class="field-float">
        <input type="password" name="password" id="f-password" placeholder=" " required minlength="8">
        <label for="f-password">كلمة المرور (8 أحرف على الأقل)</label>
      </div>
      <div class="field-float">
        <input type="password" name="password_confirm" id="f-password-confirm" placeholder=" " required minlength="8">
        <label for="f-password-confirm">تأكيد كلمة المرور</label>
      </div>
      <button type="submit" class="btn-admin">إنشاء الحساب والبدء</button>
    </form>
  </div>
</body>
</html>
