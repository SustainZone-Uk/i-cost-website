// i18n.js (27 Nov 2026): the Spanish, French and German pages of i-cost.co.uk (built by icost-tools/website-tools/i18n.py).
// On every page that has language versions (hreflang links in its head):
//  - a language switch in the menu;
//  - on an English page, a bar that suggests the visitor's own language when their browser asks for Spanish, French or
//    German. The choice is remembered on this device (localStorage, no cookie, no location lookup); someone who chose a
//    language before goes straight to that version of the page.
// On a translated page: text the page's scripts put on the screen (hero cards, live screens) is swapped from
// window.ICOST_I18N, and the footer says the page is translated and that the I-Cost app is in English.
(() => {
  'use strict';
  const UI = {
    en: { name: 'English', short: 'EN', label: 'Language' },
    es: { name: 'Español', short: 'ES', label: 'Idioma', bar: 'Esta página también está en español.', go: 'Ver en español', stay: 'Seguir en inglés',
      note: 'Página traducida del inglés. La aplicación I-Cost está en inglés.' },
    fr: { name: 'Français', short: 'FR', label: 'Langue', bar: 'Cette page existe aussi en français.', go: 'Voir en français', stay: 'Rester en anglais',
      note: 'Page traduite de l’anglais. L’application I-Cost est en anglais.' },
    de: { name: 'Deutsch', short: 'DE', label: 'Sprache', bar: 'Diese Seite gibt es auch auf Deutsch.', go: 'Auf Deutsch ansehen', stay: 'Auf Englisch bleiben',
      note: 'Aus dem Englischen übersetzte Seite. Die I-Cost-App ist auf Englisch.' },
  };
  const root = document.documentElement;
  const cur = (root.getAttribute('lang') || 'en').slice(0, 2).toLowerCase();
  const alts = {};
  document.querySelectorAll('link[rel="alternate"][hreflang]').forEach(l => {
    const h = l.getAttribute('hreflang');
    if (h !== 'x-default' && UI[h.slice(0, 2).toLowerCase()]) { alts[h.slice(0, 2).toLowerCase()] = l.getAttribute('href'); }
  });
  if (!alts.en) { return; }
  const store = {
    get() { try { return localStorage.getItem('icost.lang'); } catch (e) { return null; } },
    set(v) { try { localStorage.setItem('icost.lang', v); } catch (e) { /* private mode */ } },
  };
  // the same page on this site (keeps a local preview on its own host), with the part of the page being looked at
  const here = u => { try { return new URL(u, location.href).pathname + location.hash; } catch (e) { return u; } };
  const go = l => { store.set(l); location.href = here(alts[l]); };

  // someone who chose a language before goes straight to that version (people only: crawlers keep no choice)
  const saved = store.get();
  if (cur === 'en' && saved && saved !== 'en' && alts[saved]) { location.replace(here(alts[saved])); return; }

  const style = document.createElement('style');
  style.textContent = [
    '.lang-sw{position:relative;display:inline-flex;align-items:center}',
    '.lang-sw>button{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border:1px solid rgba(255,255,255,.22);border-radius:999px;background:transparent;color:inherit;font:inherit;font-size:.86rem;font-weight:700;cursor:pointer;letter-spacing:.02em}',
    '.lang-sw>button:hover,.lang-sw>button[aria-expanded="true"]{background:rgba(255,255,255,.1)}',
    '.lang-sw svg{width:15px;height:15px}',
    '.lang-sw ul{position:absolute;right:0;top:calc(100% + 8px);z-index:60;min-width:150px;margin:0;padding:6px;list-style:none;border-radius:12px;background:#0b2340;border:1px solid rgba(255,255,255,.16);box-shadow:0 14px 34px rgba(0,0,0,.35)}',
    '.lang-sw ul[hidden]{display:none}',
    '.lang-sw li a{display:flex;justify-content:space-between;gap:12px;padding:8px 10px;border-radius:8px;color:#fff;text-decoration:none;font-size:.9rem}',
    '.lang-sw li a:hover,.lang-sw li a[aria-current]{background:rgba(255,255,255,.1)}',
    '.lang-sw li a span{opacity:.6;font-weight:700}',
    '.lang-bar{position:fixed;left:50%;bottom:16px;transform:translateX(-50%);z-index:80;display:flex;flex-wrap:wrap;align-items:center;gap:8px 12px;max-width:calc(100% - 32px);padding:10px 12px 10px 16px;border-radius:14px;background:#0b2340;color:#fff;box-shadow:0 14px 34px rgba(0,0,0,.3);font-size:.95rem}',
    '.lang-bar button{padding:7px 14px;border-radius:999px;border:0;font:inherit;font-size:.9rem;font-weight:700;cursor:pointer}',
    '.lang-bar .lang-go{background:#0ea1e4;color:#fff}.lang-bar .lang-stay{background:rgba(255,255,255,.12);color:#fff}',
    '.lang-note{margin:10px 0 0;font-size:.82rem;opacity:.7}',
    '@media (max-width:900px){.lang-sw{margin:6px 0}.lang-sw ul{left:0;right:auto}}',
  ].join('');
  document.head.appendChild(style);

  const globe = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.6 3.8 9S14.5 18.4 12 21c-2.5-2.6-3.8-5.6-3.8-9S9.5 5.6 12 3z"/></svg>';

  const ready = fn => (document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', fn) : fn());
  ready(() => {
    // 1. the language switch in the menu
    const nav = document.getElementById('siteNav') || document.querySelector('.site-nav');
    if (nav) {
      const box = document.createElement('div');
      box.className = 'lang-sw';
      const items = Object.keys(UI).filter(l => alts[l]).map(l =>
        '<li><a href="' + here(alts[l]) + '" hreflang="' + l + '" lang="' + l + '" data-lang="' + l + '"' + (l === cur ? ' aria-current="true"' : '') + '>' + UI[l].name + ' <span>' + UI[l].short + '</span></a></li>').join('');
      box.innerHTML = '<button type="button" aria-haspopup="true" aria-expanded="false" aria-label="' + (UI[cur] || UI.en).label + ': ' + (UI[cur] || UI.en).name + '">' + globe + (UI[cur] || UI.en).short + '</button><ul hidden>' + items + '</ul>';
      const btn = box.querySelector('button'), list = box.querySelector('ul');
      btn.addEventListener('click', e => { e.stopPropagation(); const open = list.hidden; list.hidden = !open; btn.setAttribute('aria-expanded', open ? 'true' : 'false'); });
      document.addEventListener('click', e => { if (!box.contains(e.target)) { list.hidden = true; btn.setAttribute('aria-expanded', 'false'); } });
      box.addEventListener('keydown', e => { if (e.key === 'Escape') { list.hidden = true; btn.setAttribute('aria-expanded', 'false'); btn.focus(); } });
      list.addEventListener('click', e => { const a = e.target.closest('a[data-lang]'); if (a) { e.preventDefault(); go(a.getAttribute('data-lang')); } });
      const trial = nav.querySelector('.button-trial');
      nav.insertBefore(box, trial || null);
    }

    // 2. English page: suggest the visitor's language
    if (cur === 'en' && !saved) {
      const want = (navigator.languages && navigator.languages.length ? navigator.languages : [navigator.language || ''])
        .map(x => String(x).slice(0, 2).toLowerCase()).find(x => x === 'en' || (alts[x] && UI[x].bar));
      if (want && want !== 'en') {
        const t = UI[want];
        const bar = document.createElement('div');
        bar.className = 'lang-bar';
        bar.setAttribute('role', 'region');
        bar.setAttribute('lang', want);
        bar.setAttribute('aria-label', t.label);
        bar.innerHTML = '<span>' + t.bar + '</span><button type="button" class="lang-go">' + t.go + '</button><button type="button" class="lang-stay">' + t.stay + '</button>';
        bar.querySelector('.lang-go').addEventListener('click', () => go(want));
        bar.querySelector('.lang-stay').addEventListener('click', () => { store.set('en'); bar.remove(); });
        document.body.appendChild(bar);
      }
    }

    // 3. translated page: text from the page's scripts, and a note in the footer
    if (cur !== 'en' && UI[cur]) {
      const map = window.ICOST_I18N || {};
      const fix = n => {
        const raw = n.nodeValue;
        if (!raw || raw.length > 400) { return; }
        const key = raw.replace(/\s+/g, ' ').trim();
        const t = map[key];
        if (t) { n.nodeValue = raw.match(/^\s*/)[0] + t + raw.match(/\s*$/)[0]; }
      };
      const sweep = el => {
        if (el.nodeType === 3) { fix(el); return; }
        if (el.nodeType !== 1 || el.tagName === 'SCRIPT' || el.tagName === 'STYLE') { return; }
        const w = document.createTreeWalker(el, NodeFilter.SHOW_TEXT);
        for (let n = w.nextNode(); n; n = w.nextNode()) { fix(n); }
      };
      if (Object.keys(map).length) {
        sweep(document.body);
        new MutationObserver(list => list.forEach(m => m.addedNodes.forEach(sweep))).observe(document.body, { childList: true, subtree: true });
      }
      const foot = document.querySelector('footer .shell') || document.querySelector('footer');
      if (foot) {
        const p = document.createElement('p');
        p.className = 'lang-note';
        p.textContent = UI[cur].note;
        foot.appendChild(p);
      }
      // links to the English pages remember the choice of English only when the visitor uses the switch
    }
  });
})();
