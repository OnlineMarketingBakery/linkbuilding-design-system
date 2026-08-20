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
})();
