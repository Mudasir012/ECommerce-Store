document.addEventListener('DOMContentLoaded', () => {
  initQuantityControls();
  showToast();
  initScrollReveal();
  initNavTransparency();
  initQuickView();
});

function initQuantityControls() {
  document.querySelectorAll('.qty-dec, .qty-inc').forEach(btn => {
    btn.addEventListener('click', () => {
      const container = btn.closest('.input-group');
      const input = container.querySelector('input[type="number"]');
      if (!input) return;
      let val = parseInt(input.value, 10) || 1;
      val = btn.classList.contains('qty-dec') ? Math.max(1, val - 1) : Math.min(99, val + 1);
      input.value = val;
      input.dispatchEvent(new Event('change', { bubbles: true }));
    });
  });
}

function initScrollReveal() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

  document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
}

function initNavTransparency() {
  const nav = document.getElementById('mainNav');
  const hero = document.getElementById('hero');
  if (!nav || !hero) return;

  // Only on homepage
  const isHome = window.location.pathname === '/' || window.location.pathname.endsWith('index.php');
  if (!isHome) return;

  nav.classList.add('navbar-transparent');

  const onScroll = () => {
    const heroBottom = hero.offsetTop + hero.offsetHeight;
    if (window.scrollY > heroBottom - 100) {
      nav.classList.add('scrolled');
    } else {
      nav.classList.remove('scrolled');
    }
  };

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
}

function initQuickView() {
  document.querySelectorAll('.product-card__quick-add').forEach(el => {
    el.addEventListener('click', (e) => {
      e.preventDefault();
      const card = el.closest('.product-card');
      if (card) window.location.href = card.getAttribute('href');
    });
  });
}

function initCheckoutSteps() {
  // Step highlighting is handled server-side via CSS classes
}

function showToast() {
  const params = new URLSearchParams(window.location.search);
  const msg = params.get('toast');
  if (!msg) return;

  const container = document.querySelector('.toast-container') || (() => {
    const el = document.createElement('div');
    el.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    el.style.zIndex = '9999';
    document.body.appendChild(el);
    return el;
  })();

  const id = 'toast-' + Date.now();
  container.insertAdjacentHTML('beforeend', `
    <div id="${id}" class="toast show align-items-center text-bg-dark border-0" role="alert">
      <div class="d-flex">
        <div class="toast-body small">${decodeURIComponent(msg)}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  `);

  setTimeout(() => {
    const el = document.getElementById(id);
    if (el) { el.classList.remove('show'); setTimeout(() => el.remove(), 300); }
  }, 3000);

  const url = new URL(window.location.href);
  url.searchParams.delete('toast');
  window.history.replaceState({}, '', url);
}
