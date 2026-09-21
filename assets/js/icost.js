(() => {
  const body = document.body;
  const intro = document.getElementById("brandIntro");
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  const completeIntro = () => {
    intro?.classList.add("is-complete");
    body.classList.remove("is-loading");
  };

  if (reduceMotion || sessionStorage.getItem("icost-intro-seen")) {
    completeIntro();
  } else {
    window.setTimeout(() => {
      completeIntro();
      sessionStorage.setItem("icost-intro-seen", "true");
    }, 2700);
  }

  const header = document.getElementById("siteHeader");
  const progress = document.getElementById("scrollProgress");
  const heroProduct = document.querySelector(".hero-product");
  let framePending = false;

  const updateScrollState = () => {
    const top = window.scrollY;
    const scrollable = Math.max(document.documentElement.scrollHeight - window.innerHeight, 1);
    header?.classList.toggle("is-scrolled", top > 24);
    if (progress) progress.style.width = `${Math.min((top / scrollable) * 100, 100)}%`;
    if (heroProduct && !reduceMotion && top < window.innerHeight * 1.1) {
      heroProduct.style.setProperty("--hero-shift", `${Math.round(top * 0.08)}px`);
    }
    framePending = false;
  };

  window.addEventListener("scroll", () => {
    if (!framePending) {
      framePending = true;
      window.requestAnimationFrame(updateScrollState);
    }
  }, { passive: true });
  updateScrollState();

  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add("is-visible");
      revealObserver.unobserve(entry.target);
    });
  }, { threshold: 0.16, rootMargin: "0px 0px -7% 0px" });

  document.querySelectorAll(".reveal").forEach((element) => revealObserver.observe(element));

  const leakage = document.querySelector(".leakage");
  if (leakage) {
    const leakageObserver = new IntersectionObserver(([entry]) => {
      if (entry.isIntersecting) leakage.classList.add("is-visible");
    }, { threshold: 0.24 });
    leakageObserver.observe(leakage);
  }

  const screens = [...document.querySelectorAll(".journey-stage .screen")];
  const steps = [...document.querySelectorAll(".journey-step")];
  const screenFrame = document.querySelector(".screen-frame");
  const screenLabel = document.getElementById("screenLabel");
  const labels = ["Live cost tracking", "Project workflow", "Cost and carbon estimating", "Site activity and timesheets"];
  let activeScreen = 0;

  const setActiveScreen = (index) => {
    if (index === activeScreen || !screens.length) return;
    activeScreen = index;
    steps.forEach((step, stepIndex) => step.classList.toggle("is-active", stepIndex === index));
    screens.forEach((screen, screenIndex) => screen.classList.toggle("is-active", screenIndex === index));
    if (screenLabel) screenLabel.textContent = labels[index];
    screenFrame?.classList.remove("is-changing");
    window.requestAnimationFrame(() => screenFrame?.classList.add("is-changing"));
  };

  if (window.matchMedia("(min-width: 841px)").matches) {
    const stepObserver = new IntersectionObserver((entries) => {
      const visible = entries
        .filter((entry) => entry.isIntersecting)
        .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];
      if (!visible) return;
      setActiveScreen(Number(visible.target.dataset.screen));
    }, { threshold: [0.38, 0.55, 0.72], rootMargin: "-18% 0px -18% 0px" });
    steps.forEach((step) => stepObserver.observe(step));
  }

  const counters = [...document.querySelectorAll("[data-count]")];
  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      const element = entry.target;
      const target = Number(element.dataset.count);
      const prefix = element.dataset.prefix || "";
      const duration = reduceMotion ? 1 : 1200;
      const start = performance.now();

      const tick = (now) => {
        const ratio = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - ratio, 3);
        const value = Math.round(target * eased).toLocaleString("en-GB");
        element.textContent = `${prefix}${value}`;
        if (ratio < 1) window.requestAnimationFrame(tick);
      };

      window.requestAnimationFrame(tick);
      counterObserver.unobserve(element);
    });
  }, { threshold: 0.5 });
  counters.forEach((counter) => counterObserver.observe(counter));

  const menuToggle = document.getElementById("menuToggle");
  const siteNav = document.getElementById("siteNav");
  const closeMenu = () => {
    menuToggle?.setAttribute("aria-expanded", "false");
    menuToggle?.setAttribute("aria-label", "Open navigation");
    siteNav?.classList.remove("is-open");
  };

  menuToggle?.addEventListener("click", () => {
    const open = menuToggle.getAttribute("aria-expanded") === "true";
    menuToggle.setAttribute("aria-expanded", String(!open));
    menuToggle.setAttribute("aria-label", open ? "Open navigation" : "Close navigation");
    siteNav?.classList.toggle("is-open", !open);
  });

  siteNav?.querySelectorAll("a").forEach((link) => link.addEventListener("click", closeMenu));
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") closeMenu();
  });

  const year = document.getElementById("year");
  if (year) year.textContent = String(new Date().getFullYear());
})();
