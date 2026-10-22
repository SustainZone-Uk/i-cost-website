(() => {
  'use strict';
  const toggle = document.getElementById('menuToggle');
  const nav = document.getElementById('siteNav');
  const closeMenu = (restoreFocus = false) => {
    toggle.setAttribute('aria-expanded', 'false');
    toggle.setAttribute('aria-label', 'Open navigation');
    nav.classList.remove('is-open');
    if (restoreFocus) toggle.focus();
  };
  document.documentElement.classList.add('js');
  toggle.addEventListener('click', () => {
    const open = toggle.getAttribute('aria-expanded') !== 'true';
    toggle.setAttribute('aria-expanded', String(open));
    toggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
    nav.classList.toggle('is-open', open);
  });
  nav.querySelectorAll('a').forEach(link => link.addEventListener('click', () => closeMenu()));
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') closeMenu(true);
  });
  document.addEventListener('click', event => {
    if (!event.target.closest('.site-header')) closeMenu();
  });
  const desktop = window.matchMedia('(min-width: 861px)');
  desktop.addEventListener('change', () => closeMenu());

  // Progressive enhancement: all four captioned screenshots remain visible without JS.
  const tabs = [...document.querySelectorAll('[data-view]')];
  const panels = [...document.querySelectorAll('.workflow-panel')];
  const tablist = document.querySelector('.workflow-tabs');
  if (tablist) {
  tablist.setAttribute('role', 'tablist');
  tablist.setAttribute('aria-label', 'Explore the I-Cost workflow');
  tabs.forEach(tab => tab.setAttribute('role', 'tab'));
  panels.forEach(panel => panel.setAttribute('role', 'tabpanel'));
  const selectView = index => {
    tabs.forEach((tab, i) => {
      tab.setAttribute('aria-selected', String(i === index));
      tab.tabIndex = i === index ? 0 : -1;
      panels[i].hidden = i !== index;
    });
  };
  tabs.forEach((tab, index) => {
    tab.addEventListener('click', () => selectView(index));
    tab.addEventListener('keydown', event => {
      let next;
      if (event.key === 'ArrowRight' || event.key === 'ArrowDown') next = (index + 1) % tabs.length;
      if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') next = (index + tabs.length - 1) % tabs.length;
      if (event.key === 'Home') next = 0;
      if (event.key === 'End') next = tabs.length - 1;
      if (next === undefined) return;
      event.preventDefault();
      selectView(next);
      tabs[next].focus();
    });
  });
  selectView(0);
  }

  // Preserve the original subtle reveal, without hiding content if scripting fails.
  if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        entry.target.animate([{ opacity: 0.55, transform: 'translateY(10px)' }, { opacity: 1, transform: 'translateY(0)' }], { duration: 400, easing: 'ease-out' });
        observer.unobserve(entry.target);
      });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal').forEach(element => observer.observe(element));
  }
  const year = document.getElementById('year');
  if (year) year.textContent = String(new Date().getFullYear());
})();
