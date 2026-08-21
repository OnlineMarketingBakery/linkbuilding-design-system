/**
 * Masterblog 3.0 — sticky chrome, reveal, FAQ, category filters, reading progress.
 */
(function () {
  'use strict';

  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* Sticky chrome shadow */
  var chrome = document.querySelector('.site-chrome');
  if (chrome) {
    var onScroll = function () {
      chrome.classList.toggle('is-scrolled', window.scrollY > 8);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  /* Scroll reveal */
  if (!reduce && 'IntersectionObserver' in window) {
    var reveals = document.querySelectorAll('.reveal');
    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) {
            e.target.classList.add('is-visible');
            io.unobserve(e.target);
          }
        });
      },
      { rootMargin: '0px 0px -40px 0px', threshold: 0.08 }
    );
    reveals.forEach(function (el) {
      io.observe(el);
    });
  } else {
    document.querySelectorAll('.reveal').forEach(function (el) {
      el.classList.add('is-visible');
    });
  }

  /* FAQ accordion */
  document.addEventListener('click', function (e) {
    var q = e.target.closest && e.target.closest('.faq-q');
    if (!q) return;
    var item = q.closest('.faq-item');
    if (!item) return;
    var open = item.classList.contains('open') || item.classList.contains('is-open');
    var parent = item.parentElement;
    if (parent) {
      parent.querySelectorAll('.faq-item').forEach(function (sib) {
        sib.classList.remove('open', 'is-open');
        var a = sib.querySelector('.faq-a');
        if (a) a.hidden = true;
      });
    }
    if (!open) {
      item.classList.add('open', 'is-open');
      var ans = item.querySelector('.faq-a');
      if (ans) ans.hidden = false;
    }
  });

  /* Home / category card filters */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest && e.target.closest('[data-tabs] .cat-strip button[data-cat]');
    if (!btn) return;
    var wrap = btn.closest('[data-tabs]');
    if (!wrap) return;
    wrap.querySelectorAll('.cat-strip button').forEach(function (b) {
      b.classList.remove('active');
    });
    btn.classList.add('active');
    var cat = btn.getAttribute('data-cat');
    wrap.querySelectorAll('.cards-grid .card').forEach(function (c) {
      var show = cat === 'all' || c.getAttribute('data-cat') === cat;
      c.classList.toggle('hidden', !show);
    });
  });

  /* Tabs (components / business) */
  document.addEventListener('click', function (e) {
    var tab = e.target.closest && e.target.closest('[data-tabgroup] .tab-btn[data-tab]');
    if (!tab) return;
    var group = tab.closest('[data-tabgroup]');
    if (!group) return;
    var id = tab.getAttribute('data-tab');
    group.querySelectorAll('.tab-btn').forEach(function (b) {
      b.classList.toggle('active', b === tab);
    });
    group.querySelectorAll('.tab-panel').forEach(function (p) {
      p.classList.toggle('active', p.getAttribute('data-panel') === id);
    });
  });

  /* Article reading progress */
  var bar = document.querySelector('[data-progress]');
  if (bar) {
    var update = function () {
      var doc = document.documentElement;
      var scrollTop = window.scrollY || doc.scrollTop;
      var height = doc.scrollHeight - doc.clientHeight;
      var pct = height > 0 ? Math.min(100, (scrollTop / height) * 100) : 0;
      bar.style.width = pct + '%';
    };
    update();
    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update, { passive: true });
  }
})();
