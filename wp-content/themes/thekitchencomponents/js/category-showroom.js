(function () {
  'use strict';

  function initCategoryShowroom() {
    var section = document.querySelector('[data-category-showroom]');
    if (!section) return;

    var cards = Array.prototype.slice.call(section.querySelectorAll('[data-category-card]'));
    if (!cards.length) return;

    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduceMotion || !('IntersectionObserver' in window)) {
      cards.forEach(function (card) { card.classList.add('is-revealed'); });
    } else {
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-revealed');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.16, rootMargin: '0px 0px -5% 0px' });
      cards.forEach(function (card) { observer.observe(card); });
    }

    var finePointer = window.matchMedia && window.matchMedia('(min-width: 1200px) and (pointer: fine)').matches;
    if (!finePointer || reduceMotion) return;

    cards.forEach(function (card) {
      card.addEventListener('pointermove', function (event) {
        var rect = card.getBoundingClientRect();
        var x = ((event.clientX - rect.left) / rect.width - 0.5) * 8;
        var y = ((event.clientY - rect.top) / rect.height - 0.5) * 8;
        card.style.setProperty('--mx', x.toFixed(2) + 'px');
        card.style.setProperty('--my', y.toFixed(2) + 'px');
      });
      card.addEventListener('pointerleave', function () {
        card.style.setProperty('--mx', '0px');
        card.style.setProperty('--my', '0px');
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCategoryShowroom);
  } else {
    initCategoryShowroom();
  }
})();
