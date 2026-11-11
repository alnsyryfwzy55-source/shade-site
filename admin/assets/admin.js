// تبديل الوضع الليلي/النهاري مع حفظ التفضيل
const themeToggle = document.getElementById('themeToggle');
if (themeToggle) {
  themeToggle.addEventListener('click', () => {
    const current = document.documentElement.getAttribute('data-theme') || 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('admin-theme', next);
  });
}

// فتح/إغلاق قائمة الجوال العلوية
const topnavToggle = document.getElementById('topnavToggle');
const topnavMobile = document.getElementById('topnavMobile');
if (topnavToggle && topnavMobile) {
  topnavToggle.addEventListener('click', () => {
    topnavMobile.classList.toggle('open');
  });
}

// تأكيد قبل أي عملية حذف
document.querySelectorAll('[data-confirm]').forEach(link => {
  link.addEventListener('click', (e) => {
    if (!confirm(link.dataset.confirm)) {
      e.preventDefault();
    }
  });
});
