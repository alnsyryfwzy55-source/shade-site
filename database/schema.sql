-- ============================================================
--  قاعدة بيانات موقع مظلات وسواتر
--  للاستيراد: افتح phpMyAdmin > أنشئ قاعدة بيانات فارغة > استورد هذا الملف
-- ============================================================

CREATE DATABASE IF NOT EXISTS shade_site
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE shade_site;

CREATE TABLE admins (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    username        VARCHAR(50)  NOT NULL UNIQUE,
    email           VARCHAR(150) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    reset_token     VARCHAR(255) DEFAULT NULL,
    reset_expires   DATETIME     DEFAULT NULL,
    failed_attempts INT DEFAULT 0,
    locked_until    DATETIME DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    slug       VARCHAR(120) NOT NULL UNIQUE,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE projects (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    title         VARCHAR(180) NOT NULL,
    slug          VARCHAR(200) NOT NULL UNIQUE,
    description   TEXT,
    location      VARCHAR(120),
    category_id   INT DEFAULT NULL,
    cover_image   VARCHAR(255) DEFAULT NULL,
    before_image  VARCHAR(255) DEFAULT NULL,
    after_image   VARCHAR(255) DEFAULT NULL,
    is_featured   TINYINT(1) DEFAULT 0,
    status        ENUM('published','draft') DEFAULT 'published',
    sort_order    INT DEFAULT 0,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE project_images (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    project_id  INT NOT NULL,
    image_path  VARCHAR(255) NOT NULL,
    sort_order  INT DEFAULT 0,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE services (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(150) NOT NULL,
    description TEXT,
    icon        VARCHAR(30) DEFAULT 'car_shade',
    sort_order  INT DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE messages (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(120) NOT NULL,
    phone      VARCHAR(40)  NOT NULL,
    message    TEXT NOT NULL,
    is_read    TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE settings (
    setting_key   VARCHAR(100) PRIMARY KEY,
    setting_value TEXT
) ENGINE=InnoDB;


-- ============================================================
--  بيانات ابتدائية (Seed Data)
-- ============================================================

INSERT INTO categories (name, slug, sort_order) VALUES
('مظلات سيارات',   'car-shades',    1),
('مظلات حدائق',    'garden-shades', 2),
('سواتر',           'privacy-screens', 3),
('مظلات مسابح',    'pool-shades',   4);

INSERT INTO services (title, description, icon, sort_order) VALUES
('مظلات سيارات',              'تصميم وتركيب مظلات سيارات بأقمشة عالية الجودة مقاومة للأشعة فوق البنفسجية والعوامل الجوية.',        'car_shade',       1),
('مظلات حدائق',                'مظلات وبرجولات حدائق أنيقة تمنحك مساحة خارجية مريحة طوال العام.',                                     'garden_shade',    2),
('سواتر خصوصية',              'سواتر بتصاميم متعددة (قماش، خشبية، معدنية) لحماية خصوصية منزلك أو مبناك.',                            'privacy_screen',  3),
('مظلات مسابح',                'مظلات مخصصة لحماية المسابح من الشمس والأتربة مع الحفاظ على الإطلالة.',                               'pool_shade',      4),
('تركيب أقمشة PVC/HDPE',      'توريد وتركيب أقمشة مظلات عالية الجودة (PVC وHDPE) بضمان يصل حتى 10 سنوات.',                          'fabric_install',  5),
('صيانة وتغيير الأقمشة',       'صيانة دورية وتغيير أقمشة المظلات المتضررة أو المتآكلة لإطالة عمر الهيكل.',                            'maintenance',     6);

INSERT INTO projects (title, slug, description, location, category_id, is_featured, status) VALUES
('مظلة سيارات فيلا خاصة', 'villa-car-shade-demo', 'مشروع تجريبي — عدّله أو احذفه من لوحة التحكم.', 'الرياض', 1, 1, 'published'),
('ساتر خصوصية سطح', 'roof-privacy-screen-demo', 'مشروع تجريبي — عدّله أو احذفه من لوحة التحكم.', 'الرياض', 3, 0, 'published');

INSERT INTO settings (setting_key, setting_value) VALUES
('site_name',         'ظلال'),
('site_tagline',      'للمظلات والسواتر'),
('meta_title',        'ظلال للمظلات والسواتر في الرياض | مظلات سيارات وحدائق ومسابح'),
('meta_description',  'شركة ظلال لتركيب المظلات والسواتر في الرياض: مظلات سيارات، حدائق، مسابح، وسواتر خصوصية بأقمشة عالية الجودة وضمان حقيقي.'),
('phone',              '0500000000'),
('whatsapp',           '966500000000'),
('email',              'info@thilal-shades.sa'),
('address',            'الرياض، المملكة العربية السعودية'),
('coverage_areas',     'الرياض والمناطق المجاورة'),
('instagram_url',      '#'),
('tiktok_url',         '#'),
('snapchat_url',       '#'),
('twitter_url',        '#'),
('map_embed_url',      ''),
('hero_title',         'ظل يليق بمساحتك'),
('hero_desc',          'مظلات سيارات وحدائق ومسابح، وسواتر خصوصية بتصاميم عصرية وأقمشة أصلية مقاومة للشمس، تركيب سريع وضمان حقيقي.'),
('about_title',        'حرفية تلمسها فوق رأسك'),
('about_text',         'ظلال فريق متخصص في تصميم وتركيب المظلات والسواتر بالرياض. نستخدم هياكل حديد مجلفن وأقمشة أصلية مقاومة للعوامل الجوية، ونلتزم بمواعيد التركيب مهما كانت المساحة.'),
('why_us_1_title', 'هياكل مجلفنة مضمونة'),
('why_us_1_text',  'حديد مجلفن ضد الصدأ مع دهان إلكتروستاتيك يدوم لسنوات طويلة دون تقشر.'),
('why_us_2_title', 'أقمشة أصلية مستوردة'),
('why_us_2_text',  'نستخدم أقمشة PVC وHDPE أصلية مقاومة للأشعة فوق البنفسجية بضمان مكتوب.'),
('why_us_3_title', 'تركيب سريع ونظيف'),
('why_us_3_text',  'فريق تركيب محترف ينجز أغلب المشاريع خلال يوم إلى ثلاثة أيام كحد أقصى.'),
('stat1_value', '+11'),  ('stat1_label', 'سنة خبرة'),
('stat2_value', '+450'), ('stat2_label', 'مظلة وساتر منجز'),
('stat3_value', '5'),    ('stat3_label', 'سنوات ضمان'),
('footer_text',        '© 2026 ظلال للمظلات والسواتر - الرياض');

-- ملاحظة: لا يوجد حساب مسؤول افتراضي بكلمة مرور ثابتة (لأسباب أمنية).
-- عند أول زيارة لصفحة admin/login.php سيتم توجيهك تلقائيًا لصفحة
-- admin/install.php لإنشاء أول حساب مسؤول باسم مستخدم وكلمة مرور من اختيارك.
