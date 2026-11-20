// Scroll animations for the homepage sections (20 Nov 2026). Each section plays once when it is well into view.
// Dark sections: navy-glass pop-ups (as in the hero). Light sections: white lift cards. Pop-ups are decoration (aria-hidden).
(() => {
  'use strict';
  if (!('IntersectionObserver' in window)) return;
  const main = document.getElementById('main');
  if (!main) return;
  document.documentElement.classList.add('sa-on');
  const IMG = '/assets/images/photos/';
  const wait = ms => new Promise(r => setTimeout(r, ms));
  const $ = (root, sel) => root.querySelector(sel);
  const $$ = (root, sel) => [...root.querySelectorAll(sel)];

  // Is the section dark? Read its background colour.
  const isDark = sec => {
    let el = sec, c;
    while (el && el !== document.documentElement) {
      c = getComputedStyle(el).backgroundColor;
      const m = c.match(/[\d.]+/g);
      if (m && (m.length < 4 || +m[3] > .5)) { const [r, g, b] = m.map(Number); return (0.299 * r + 0.587 * g + 0.114 * b) < 110; }
      el = el.parentElement;
    }
    return false;
  };

  // Count a number up, keeping what is around it (pound sign, %, commas, decimals).
  const countUp = (el, ms = 1300) => {
    const text = el.textContent, m = text.match(/-?[\d,]*\.?\d+/);
    if (!m) return;
    const raw = m[0], end = parseFloat(raw.replace(/,/g, '')), dp = (raw.split('.')[1] || '').length, commas = raw.includes(',');
    const pre = text.slice(0, m.index), post = text.slice(m.index + raw.length), t0 = performance.now();
    const show = v => { let s = v.toFixed(dp); if (commas) s = Number(s).toLocaleString('en-GB', { minimumFractionDigits: dp, maximumFractionDigits: dp }); el.textContent = pre + s + post; };
    const tick = t => { const p = Math.min(1, (t - t0) / ms); show(end * (1 - Math.pow(1 - p, 3))); if (p < 1) requestAnimationFrame(tick); else el.textContent = text; };
    show(0); requestAnimationFrame(tick);
  };

  // A pop-up card over a host element.
  const pop = (host, dark, o) => {
    if (getComputedStyle(host).position === 'static') host.classList.add('sa-host');
    const d = document.createElement('div');
    d.className = 'sa-pop ' + (dark ? 'dark' : 'light') + (o.wide ? ' sa-wide' : '');
    d.setAttribute('aria-hidden', 'true');
    d.style.cssText = (o.pos || '') + ';--ac:' + (o.ac || '#4fd4df') + ';--sx:' + (o.sx || '0') ;
    d.innerHTML = '<div class="sa-ph"><i>' + (o.ic || '&#10003;') + '</i><span><b>' + o.t + '</b>' + (o.s ? '<small>' + o.s + '</small>' : '') + '</span></div>' + (o.rows || []).map(r => '<div class="sa-row"><span>' + r[0] + '</span><span class="' + (r[2] || '') + '">' + r[1] + '</span></div>').join('');
    host.appendChild(d);
    requestAnimationFrame(() => requestAnimationFrame(() => d.classList.add('on')));
    return d;
  };

  // Which items fade in, per section (in order). Side for the slide comes from the column.
  const ITEMS = '.j-phase, .pairs li, .stats li, .intel-grid > li, .roles li, .track-stages li, .modules-grid .module, .track-actions li, .metrics .metric, .report-list li, .carbon-example > div, .anywhere-list li, .team-grid > div, .team-cards li, .about-facts li, .trial-list li, .contact-options li, .value-example';

  // Section scenes: extra detail that plays after the items come in.
  const scenes = {
    journey: async (sec) => {
      const steps = $$(sec, '.j-phase li');
      steps.forEach(li => { const t = document.createElement('span'); t.className = 'sa-tick'; t.setAttribute('aria-hidden', 'true'); t.innerHTML = '&#10003;'; li.appendChild(t); });
      await wait(900);
      for (const li of steps) { li.classList.add('lit'); await wait(260); li.classList.replace('lit', 'done'); }
    },
    problems: async (sec, dark) => {
      const v = $(sec, '.value-example strong'); if (v) setTimeout(() => countUp(v, 1600), 500);
      await wait(1100);
      for (const li of $$(sec, '.pairs li')) { li.classList.add('fixed'); await wait(420); }
    },
    evidence: async (sec, dark) => {
      $$(sec, '.stats strong').forEach((s, i) => setTimeout(() => countUp(s, 1400), 400 + i * 250));
      await wait(1500);
      const host = $(sec, '.shell');
      pop(host, dark, { wide: 1, pos: 'right:0;top:30px', sx: '30px', ac: '#ff4f8b', ic: '&pound;', t: 'Price check', s: 'Plasterboard 12.5mm, per sheet', rows: [['AI guess', '&pound;6.80', 'sa-bad'], ['Your last order', '&pound;7.85', 'sa-ok'], ['Missed by the guess', '+15%']] });
    },
    intel: async (sec) => {
      const bars = $$(sec, '.intel-bar i'); bars.forEach(b => b.classList.add('sa-zero'));
      const pips = $$(sec, '.intel-row .up, .intel-row .new, .tag-ok, .tag-ask, .intel-demo-head b, .intel-cols span, .intel-demo .chip');
      pips.forEach(p => p.classList.add('sa-pip'));
      await wait(1000);
      bars.forEach((b, i) => setTimeout(() => b.classList.remove('sa-zero'), i * 220));
      for (const p of pips) { p.classList.add('on'); await wait(170); }
    },
    who: async (sec, dark) => {
      const host = $(sec, '.photo-card'); if (!host) return;
      await wait(900);
      pop(host, dark, { pos: 'left:-18px;top:18px', sx: '-24px', ac: '#0a8f5a', ic: '&#8593;', t: 'Forecast margin 10.3%', s: 'Directors see it today, not at the final account' });
      await wait(1300);
      pop(host, dark, { pos: 'right:-14px;top:42%', sx: '24px', ac: '#c8125e', ic: '&pound;', t: '3 quotes in, best &pound;17,960', s: 'Buyers order in one click' });
      await wait(1300);
      pop(host, dark, { pos: 'left:8%;bottom:-16px', sx: '-24px', ac: '#f0a830', ic: '&#9201;', t: '42 h approved this week', s: 'Site managers, from their phones' });
    },
    tracker: async (sec, dark) => {
      const pipe = $(sec, '.track-stages'); pipe.classList.add('sa-pipe');
      await wait(1100);
      for (const li of $$(pipe, 'li')) { li.classList.add('lit'); await wait(330); li.classList.replace('lit', 'done'); }
      const host = $(sec, '.track-shot .product-frame');
      if (host) { pop(host, dark, { pos: 'right:18px;top:60px', sx: '24px', ac: '#2fd3e6', ic: '&#9635;', t: 'QR delivery note scanned', s: 'DN-2231 &middot; Supplier B', rows: [['24 &times; Plasterboard 12.5mm', 'Delivered &#10003;', 'sa-ok']] }); }
    },
    platform: async (sec) => {
      const tabs = $$(sec, '.workflow-tab'); if (tabs.length < 2) return;
      let i = 0, stopped = false, timer;
      const T = 6500;
      const bars = tabs.map(t => { const b = document.createElement('span'); b.className = 'sa-tabbar'; b.setAttribute('aria-hidden', 'true'); t.appendChild(b); return b; });
      const run = () => {
        bars.forEach(b => { b.classList.remove('run'); b.style.width = '0'; });
        if (stopped) return;
        const b = bars[i]; b.style.setProperty('--t', T + 'ms'); void b.offsetWidth; b.style.width = ''; b.classList.add('run');
        timer = setTimeout(() => { i = (i + 1) % tabs.length; tabs[i].click(); run(); }, T);
      };
      const stop = () => { stopped = true; clearTimeout(timer); bars.forEach(b => { b.classList.remove('run'); b.style.width = '0'; }); };
      tabs.forEach(t => t.addEventListener('pointerdown', stop, { once: true }));
      tabs.forEach(t => t.addEventListener('keydown', stop, { once: true }));
      i = Math.max(0, tabs.findIndex(t => t.getAttribute('aria-selected') === 'true'));
      await wait(800); run();
    },
    features: async (sec) => {
      const lis = $$(sec, '.module ul li'); lis.forEach(l => l.classList.add('sa-pip'));
      await wait(1300);
      for (const li of lis) { li.classList.add('on'); await wait(45); }
    },
    steps: async (sec) => {
      const pipe = $(sec, '.track-stages'); if (!pipe) return; pipe.classList.add('sa-pipe');
      await wait(1100);
      for (const li of $$(pipe, 'li')) { li.classList.add('lit'); await wait(600); li.classList.replace('lit', 'done'); }
    },
    visibility: async (sec, dark) => {
      const host = $(sec, '.dashboard-large .product-frame'); if (!host) return;
      await wait(1300);
      pop(host, dark, { pos: 'right:-10px;top:90px', sx: '24px', ac: '#3f7df0', ic: '&#9638;', t: 'Committed so far', s: 'Orders, labour and invoices', rows: [['&pound;232,236', 'live', 'sa-ok']] });
      await wait(1200);
      pop(host, dark, { pos: 'left:-10px;top:42%', sx: '-24px', ac: '#f0a830', ic: '!', t: 'Forecast margin 3.3%', s: 'Below the 5% target: act now', rows: [['Materials over budget', '&minus;&pound;1,010', 'sa-bad']] });
    },
    reports: async (sec) => {
      const fm = $$(sec, '.report-formats li'); const cols = ['#c8125e', '#2b5fd9', '#0a8f5a', '#0b1f2e'];
      await wait(1400);
      for (let k = 0; k < fm.length; k++) { fm[k].style.setProperty('--ac', cols[k % cols.length]); fm[k].classList.add('chip'); await wait(260); }
    },
    carbon: async (sec, dark) => {
      $$(sec, '.carbon-example dd').forEach((d, i) => setTimeout(() => countUp(d, 1100), 600 + i * 450));
      await wait(2600);
      pop($(sec, '.shell'), dark, { wide: 1, pos: 'right:0;top:34px', sx: '30px', ac: '#20a66a', ic: 'CO&#8322;', t: 'Same line, cost and carbon', s: 'Concrete C32/40 slab, 42 m&sup3;', rows: [['Cost', '&pound;5,250'], ['Embodied carbon', '13.4 tCO&#8322;e', 'sa-ok']] });
    },
    anywhere: async (sec, dark) => {
      const host = $(sec, '.photo-card'); if (!host) return;
      await wait(1000);
      pop(host, dark, { pos: 'right:-16px;top:24px', sx: '24px', ac: '#f0a830', ic: '&#9201;', t: 'Log my hours', s: 'Kingsway House &middot; today', rows: [['8.5 h', 'Sent for approval &#10003;', 'sa-ok']] });
      await wait(1300);
      pop(host, dark, { pos: 'left:-14px;bottom:24px', sx: '-24px', ac: '#2fd3e6', ic: '&#9635;', t: 'Delivery checked on site', s: 'The office sees it straight away' });
    },
    team: async (sec) => {
      const lis = $$(sec, '.team-grid li'); lis.forEach(l => l.classList.add('sa-pip'));
      await wait(1100);
      for (const li of lis) { li.classList.add('on'); await wait(110); }
    },
    trial: async (sec) => {
      const lis = $$(sec, '.trial-list li'); lis.forEach(l => l.classList.add('sa-pip'));
      await wait(500);
      for (const li of lis) { li.classList.add('on'); await wait(220); }
    },
  };
  // Which scene each section plays, and a photo for empty space where there is room.
  const PLAN = [
    ['.journey', 'journey'],
    ['#benefits', 'problems'],
    ['#real-prices', 'evidence', 'pexels-blueprints-site.webp', '70% 40%'],
    ['#intelligence', 'intel'],
    ['#who', 'who'],
    ['#item-tracker', 'tracker'],
    ['#platform', 'platform'],
    ['#features', 'features', 'pexels-timber-frame.webp', '60% 30%'],
    ['#xero', 'steps'],
    ['#revit', 'steps'],
    ['.visibility', 'visibility'],
    ['#reports', 'reports', 'pexels-office-plans.webp', '50% 40%'],
    ['.leakage', 'carbon', 'pexels-green-building-cranes.webp', '60% 50%'],
    ['#anywhere', 'anywhere'],
    ['.team-section', 'team', 'pexels-steel-beams-yard.webp', '50% 50%'],
    ['#about', null],
    ['#free-trial', 'trial'],
    ['#contact', null, 'pexels-team-plans.webp', '50% 35%'],
  ];

  const prep = [];
  PLAN.forEach(([sel, scene, img, pos]) => {
    const sec = $(main, sel); if (!sec) return;
    const dark = isDark(sec);
    sec.classList.add(dark ? 'sa-dark' : 'sa-light');
    const items = $$(sec, ITEMS).filter(el => !el.closest('.hero'));
    const vw = window.innerWidth;
    items.forEach((el, i) => { el.classList.add('sa'); el.style.setProperty('--i', String(i % 12)); const r = el.getBoundingClientRect(); el.style.setProperty('--sx', (r.left + r.width / 2 > vw / 2 ? '26px' : '-26px')); });
    if (img && window.matchMedia('(min-width: 861px)').matches) {
      sec.classList.add('sa-bg');
      const bg = getComputedStyle(sec).backgroundColor, al = (bg.match(/[\d.]+/g) || []);
      if (al.length === 3 || (al.length === 4 && +al[3] > .5)) sec.style.setProperty('--sa-c', bg);
      sec.style.setProperty('--sa-pos', pos || 'center');
      sec.dataset.saImg = IMG + img;
    }
    prep.push({ sec, scene, dark });
  });

  // Load the background photos only when a section is near.
  const near = new IntersectionObserver(es => es.forEach(e => {
    if (!e.isIntersecting) return;
    const s = e.target; near.unobserve(s);
    const url = s.dataset.saImg; const im = new Image();
    im.onload = () => { s.style.setProperty('--sa-img', 'url("' + url + '")'); s.classList.add('sa-img-on'); };
    im.src = url;
  }), { rootMargin: '400px 0px' });
  prep.forEach(p => { if (p.sec.dataset.saImg) near.observe(p.sec); });

  // Play each section once, when about a quarter of it is on screen.
  const play = p => { p.sec.classList.add('sa-in'); if (p.scene && scenes[p.scene]) scenes[p.scene](p.sec, p.dark).catch(() => {}); };
  const io = new IntersectionObserver(es => es.forEach(e => {
    if (!e.isIntersecting) return;
    io.unobserve(e.target);
    const p = prep.find(x => x.sec === e.target); if (p) play(p);
  }), { threshold: 0.22, rootMargin: '0px 0px -10% 0px' });
  prep.forEach(p => io.observe(p.sec));
})();
