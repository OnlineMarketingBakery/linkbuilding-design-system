(() => {
  // FAQ accordion toggle (Article)
  document.querySelectorAll('.faq-q').forEach((btn) => {
    btn.addEventListener('click', () => {
      const item = btn.closest('.faq-item');
      if (!item) return;
      item.classList.toggle('is-open');
      const ans = item.querySelector('.faq-a');
      if (ans) ans.hidden = !item.classList.contains('is-open');
    });
  });
  document.querySelectorAll('.faq-a').forEach((a) => {
    if (!a.closest('.faq-item')?.classList.contains('is-open')) a.hidden = true;
  });

  // Sticky chrome: shadow / denser padding when scrolled
  const chrome = document.querySelector('.site-chrome');
  if (chrome) {
    const onScroll = () => {
      chrome.classList.toggle('is-scrolled', window.scrollY > 8);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  // Scroll reveal (once)
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const reveals = document.querySelectorAll('.reveal');
  if (reduce) {
    reveals.forEach((el) => el.classList.add('is-visible'));
  } else if (reveals.length && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        });
      },
      { rootMargin: '0px 0px -8% 0px', threshold: 0.08 }
    );
    reveals.forEach((el) => io.observe(el));
  } else {
    reveals.forEach((el) => el.classList.add('is-visible'));
  }
})();
