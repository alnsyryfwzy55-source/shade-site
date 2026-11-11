// قائمة الجوال
const navToggle = document.getElementById('navToggle');
const mainNav = document.getElementById('mainNav');

if (navToggle && mainNav) {
  navToggle.addEventListener('click', () => {
    const isOpen = mainNav.classList.toggle('mobile-open');
    navToggle.classList.toggle('open', isOpen);
    navToggle.setAttribute('aria-expanded', isOpen);
  });

  mainNav.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      mainNav.classList.remove('mobile-open');
      navToggle.classList.remove('open');
    });
  });
}

// نموذج التواصل
const contactForm = document.getElementById('contactForm');
const formStatus = document.getElementById('formStatus');

if (contactForm) {
  contactForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const submitBtn = contactForm.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    formStatus.textContent = 'جاري الإرسال...';
    formStatus.className = 'form-status';

    try {
      const res = await fetch('api/contact.php', {
        method: 'POST',
        body: new FormData(contactForm),
      });
      const data = await res.json();

      if (data.success) {
        formStatus.textContent = 'تم استلام طلبك، سنتواصل معك قريبًا.';
        formStatus.className = 'form-status ok';
        contactForm.reset();
      } else {
        formStatus.textContent = data.error || 'حدث خطأ، حاول مجددًا.';
        formStatus.className = 'form-status err';
      }
    } catch (err) {
      formStatus.textContent = 'تعذّر الاتصال بالخادم، حاول مجددًا.';
      formStatus.className = 'form-status err';
    } finally {
      submitBtn.disabled = false;
    }
  });
}

// سلايدر مقارنة "قبل / بعد" التفاعلي
function initBeforeAfterSliders() {
  document.querySelectorAll('[data-ba]').forEach(slider => {
    const range = slider.querySelector('.ba-range');
    const beforeWrap = slider.querySelector('.ba-before-wrap');
    const handle = slider.querySelector('.ba-handle');
    const line = slider.querySelector('.ba-line');
    if (!range || !beforeWrap) return;

    function setContainerWidth() {
      const rect = slider.getBoundingClientRect();
      slider.style.setProperty('--ba-img-w', rect.width + 'px');
    }

    function update() {
      const val = range.value;
      beforeWrap.style.width = val + '%';
      if (handle) handle.style.insetInlineStart = val + '%';
      if (line) line.style.insetInlineStart = val + '%';
    }

    setContainerWidth();
    update();
    window.addEventListener('resize', setContainerWidth);
    range.addEventListener('input', update);
  });
}

document.addEventListener('DOMContentLoaded', initBeforeAfterSliders);
