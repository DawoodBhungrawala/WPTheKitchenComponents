document.addEventListener('DOMContentLoaded', function () {
  var hero = document.querySelector('[data-hero-carousel]');
  if (!hero) return;

  var slides = Array.prototype.slice.call(hero.querySelectorAll('[data-hero-slide]'));
  var copies = Array.prototype.slice.call(hero.querySelectorAll('[data-hero-copy]'));
  var dots = Array.prototype.slice.call(hero.querySelectorAll('[data-hero-dot]'));
  var prev = hero.querySelector('[data-hero-prev]');
  var next = hero.querySelector('[data-hero-next]');
  var current = 0;
  var timer = null;
  var interval = 5000;
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function show(index) {
    current = (index + slides.length) % slides.length;
    slides.forEach(function (slide, i) { slide.classList.toggle('is-active', i === current); });
    copies.forEach(function (copy, i) { copy.classList.toggle('is-active', i === current); });
    dots.forEach(function (dot, i) {
      dot.classList.toggle('is-active', i === current);
      dot.setAttribute('aria-current', i === current ? 'true' : 'false');
    });
  }

  function stop() { if (timer) { clearInterval(timer); timer = null; } }
  function start() {
    stop();
    if (!reduceMotion && slides.length > 1) timer = setInterval(function () { show(current + 1); }, interval);
  }

  if (prev) prev.addEventListener('click', function () { show(current - 1); start(); });
  if (next) next.addEventListener('click', function () { show(current + 1); start(); });
  dots.forEach(function (dot, i) { dot.addEventListener('click', function () { show(i); start(); }); });
  hero.addEventListener('mouseenter', stop);
  hero.addEventListener('mouseleave', start);
  hero.addEventListener('focusin', stop);
  hero.addEventListener('focusout', start);
  document.addEventListener('visibilitychange', function () { document.hidden ? stop() : start(); });

  show(0);
  start();
});
