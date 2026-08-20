(() => {
  const header = document.querySelector('.lbds-header');
  const toggle = document.querySelector('.lbds-nav-toggle');
  if (header && toggle) {
    toggle.addEventListener('click', () => {
      const open = header.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  const cards = document.querySelectorAll('.lbds-card');
  if (!cards.length || !('IntersectionObserver' in window)) {
    cards.forEach((el) => el.classList.add('is-inview'));
    return;
  }

  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-inview');
        io.unobserve(entry.target);
      });
    },
    { threshold: 0.12, rootMargin: '0px 0px -8% 0px' }
  );

  cards.forEach((el, i) => {
    el.style.setProperty('--lbds-delay', `${Math.min(i * 0.06, 0.3)}s`);
    io.observe(el);
  });
})();
