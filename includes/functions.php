<?php
require_once __DIR__ . '/../config/db.php';

/* ========================================================
   دوال عامة
   ======================================================== */

function e($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function slugify($text)
{
    $text = trim($text);
    $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text);
    $text = trim($text, '-');
    $text = mb_strtolower($text, 'UTF-8');
    if ($text === '') {
        $text = 'item-' . time();
    }
    return $text;
}

function unique_slug(PDO $pdo, $table, $slug, $ignoreId = null)
{
    $base = $slug;
    $i = 2;
    while (true) {
        $sql = "SELECT id FROM `$table` WHERE slug = ?";
        $params = [$slug];
        if ($ignoreId) {
            $sql .= ' AND id != ?';
            $params[] = $ignoreId;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        if (!$stmt->fetch()) {
            return $slug;
        }
        $slug = $base . '-' . $i;
        $i++;
    }
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function flash_set($type, $message)
{
    $_SESSION['flash'][$type] = $message;
}

function flash_get($type)
{
    $msg = $_SESSION['flash'][$type] ?? null;
    unset($_SESSION['flash'][$type]);
    return $msg;
}

/* ========================================================
   الإعدادات العامة (جدول settings)
   ======================================================== */

function get_all_settings(PDO $pdo)
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $cache = [];
    foreach ($pdo->query('SELECT setting_key, setting_value FROM settings') as $row) {
        $cache[$row['setting_key']] = $row['setting_value'];
    }
    return $cache;
}

function get_setting(PDO $pdo, $key, $default = '')
{
    $all = get_all_settings($pdo);
    return $all[$key] ?? $default;
}

function set_setting(PDO $pdo, $key, $value)
{
    $stmt = $pdo->prepare(
        'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );
    $stmt->execute([$key, $value]);
}

/* ========================================================
   الحماية: تسجيل الدخول و CSRF
   ======================================================== */

function require_login()
{
    if (empty($_SESSION['admin_id'])) {
        redirect('login.php');
    }
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf()
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        die('⚠️ انتهت صلاحية الجلسة أو الطلب غير صالح. أعد تحميل الصفحة وحاول مجددًا.');
    }
}

function verify_link_token($token)
{
    return hash_equals($_SESSION['csrf_token'] ?? '', $token ?? '');
}

/* ========================================================
   رفع الصور وضغطها تلقائيًا إلى WebP
   ======================================================== */

function upload_image_as_webp($fileInputName, $targetDir, $maxWidth = null)
{
    if (empty($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (!extension_loaded('gd')) {
        throw new Exception(
            'مكتبة معالجة الصور (GD) غير مفعّلة في إعدادات PHP على السيرفر. ' .
            'في XAMPP: افتح php.ini وأزل الفاصلة المنقوطة قبل ";extension=gd" ثم أعد تشغيل Apache.'
        );
    }

    $file = $_FILES[$fileInputName];
    $maxWidth = $maxWidth ?: MAX_IMAGE_WIDTH;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('حدث خطأ أثناء رفع الصورة (كود: ' . $file['error'] . ')');
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        $mb = round(MAX_UPLOAD_SIZE / 1024 / 1024, 1);
        throw new Exception("حجم الصورة أكبر من الحد المسموح ({$mb} ميجابايت)");
    }

    $info = @getimagesize($file['tmp_name']);
    if (!$info) {
        throw new Exception('الملف المرفوع ليس صورة صالحة');
    }

    switch ($info['mime']) {
        case 'image/jpeg':
            if (!function_exists('imagecreatefromjpeg')) {
                throw new Exception('السيرفر لا يدعم معالجة صور JPG حاليًا (دعم GD-JPEG غير مفعّل).');
            }
            $src = imagecreatefromjpeg($file['tmp_name']);
            break;
        case 'image/png':
            if (!function_exists('imagecreatefrompng')) {
                throw new Exception('السيرفر لا يدعم معالجة صور PNG حاليًا (دعم GD-PNG غير مفعّل).');
            }
            $src = imagecreatefrompng($file['tmp_name']);
            imagepalettetotruecolor($src);
            break;
        case 'image/webp':
            if (!function_exists('imagecreatefromwebp')) {
                throw new Exception('السيرفر لا يدعم معالجة صور WebP حاليًا (دعم GD-WebP غير مفعّل).');
            }
            $src = imagecreatefromwebp($file['tmp_name']);
            break;
        default:
            throw new Exception('صيغة الصورة غير مدعومة. الرجاء استخدام JPG أو PNG أو WebP');
    }

    if (!$src) {
        throw new Exception('تعذّر قراءة الصورة، جرّب ملفًا آخر');
    }

    imagealphablending($src, true);
    imagesavealpha($src, true);

    $width  = imagesx($src);
    $height = imagesy($src);
    if ($width > $maxWidth) {
        $newHeight = (int) round($height * ($maxWidth / $width));
        $resized = imagecreatetruecolor($maxWidth, $newHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $src, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);
        imagedestroy($src);
        $src = $resized;
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    if (function_exists('imagewebp')) {
        $filename = uniqid('img_', true) . '.webp';
        $fullPath = rtrim($targetDir, '/') . '/' . $filename;
        imagewebp($src, $fullPath, WEBP_QUALITY);
    } else {
        $filename = uniqid('img_', true) . '.jpg';
        $fullPath = rtrim($targetDir, '/') . '/' . $filename;
        imagejpeg($src, $fullPath, WEBP_QUALITY);
    }

    imagedestroy($src);
    return $filename;
}

function upload_multiple_images_as_webp($fileInputName, $targetDir)
{
    if (empty($_FILES[$fileInputName]) || empty($_FILES[$fileInputName]['name'][0])) {
        return [];
    }

    $count = count($_FILES[$fileInputName]['name']);
    $uploaded = [];

    for ($i = 0; $i < $count; $i++) {
        if ($_FILES[$fileInputName]['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        $_FILES['__gallery_tmp__'] = [
            'name'     => $_FILES[$fileInputName]['name'][$i],
            'type'     => $_FILES[$fileInputName]['type'][$i],
            'tmp_name' => $_FILES[$fileInputName]['tmp_name'][$i],
            'error'    => $_FILES[$fileInputName]['error'][$i],
            'size'     => $_FILES[$fileInputName]['size'][$i],
        ];
        $filename = upload_image_as_webp('__gallery_tmp__', $targetDir);
        unset($_FILES['__gallery_tmp__']);
        if ($filename) {
            $uploaded[] = $filename;
        }
    }

    return $uploaded;
}

function delete_uploaded_file($targetDir, $filename)
{
    if (!$filename) {
        return;
    }
    $path = rtrim($targetDir, '/') . '/' . $filename;
    if (is_file($path)) {
        @unlink($path);
    }
}

/* ========================================================
   معرض صور المشروع (project_images)
   ======================================================== */
function get_project_images(PDO $pdo, $projectId)
{
    $stmt = $pdo->prepare('SELECT * FROM project_images WHERE project_id = ? ORDER BY sort_order, id');
    $stmt->execute([$projectId]);
    return $stmt->fetchAll();
}

/* ========================================================
   أدوات SEO
   ======================================================== */

function full_url($path = '')
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function asset_path($path)
{
    return ltrim($path, '/');
}

/* ========================================================
   إحصائيات لوحة التحكم
   ======================================================== */

/** عدد الرسائل الواردة لكل يوم خلال آخر 7 أيام (لرسم بياني بسيط بالداشبورد) */
function get_messages_last_7_days(PDO $pdo)
{
    $stmt = $pdo->prepare(
        "SELECT DATE(created_at) d, COUNT(*) c FROM messages
         WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
         GROUP BY DATE(created_at)"
    );
    $stmt->execute();
    $rows = $stmt->fetchAll();
    $byDate = [];
    foreach ($rows as $r) {
        $byDate[$r['d']] = (int) $r['c'];
    }

    $result = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i day"));
        $result[] = [
            'label' => date('D', strtotime($date)),
            'count' => $byDate[$date] ?? 0,
        ];
    }
    return $result;
}

/* ========================================================
   الأيقونات (SVG مضمّنة) - مواضيع مظلات وسواتر
   ======================================================== */
function service_icon_svg($key)
{
    $icons = [
        // مظلات سيارات (سقف مقوس + سيارة)
        'car_shade' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M2 10c3-4 17-4 20 0"/><path d="M4 18h16"/><path d="M5 18v-5M19 18v-5"/><circle cx="8" cy="19" r="1"/><circle cx="16" cy="19" r="1"/></svg>',
        // مظلات حدائق (قوس + نبتة)
        'garden_shade' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 12a9 9 0 0 1 18 0"/><path d="M3 12h18"/><path d="M12 12v9"/><path d="M9 21h6"/></svg>',
        // سواتر خصوصية (ألواح رأسية)
        'privacy_screen' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 21V7l4-4 4 4v14"/><path d="M12 21V7l4-4 4 4v14"/><path d="M4 21h16"/></svg>',
        // مظلات مسابح (موجة + قوس)
        'pool_shade' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 10a8 8 0 0 1 16 0"/><path d="M2 18c1.5 1.3 3 1.3 4.5 0s3-1.3 4.5 0 3 1.3 4.5 0 3-1.3 4.5 0"/><path d="M4 14h16"/></svg>',
        // تركيب أقمشة PVC/HDPE (طبقات قماش)
        'fabric_install' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 6h16v4H4z"/><path d="M6 14h12v4H6z"/><path d="M4 10v-1M20 10v-1M6 18v1M18 18v1"/></svg>',
        // صيانة وتغيير الأقمشة (مفتاح صيانة)
        'maintenance' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.77z"/></svg>',
    ];
    return $icons[$key] ?? $icons['car_shade'];
}

/** ألوان احتياطية لعرض بطاقات المشاريع التي لا تملك صورة بعد */
function placeholder_tone($seed)
{
    $tones = ['#E0A526', '#3B5166', '#B8791E', '#23262B', '#C98A2E', '#2E4258'];
    return $tones[$seed % count($tones)];
}
