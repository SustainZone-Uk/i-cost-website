/**
 * I-Cost Blogs & Insights Controller
 * Manages category filtering, live multi-keyword search, and UI state.
 */
(() => {
  'use strict';

  document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('blogSearchInput');
    const searchClear = document.getElementById('searchClear');
    const resultsCount = document.getElementById('resultsCount');
    const categoryChips = document.querySelectorAll('.cat-chip');
    const articleCards = document.querySelectorAll('.article-card[data-article-id]');
    const featuredCard = document.getElementById('featuredArticleCard');
    const noResults = document.getElementById('noResultsMessage');
    const resetFiltersBtn = document.getElementById('resetFiltersBtn');

    if (!articleCards.length) return;

    let activeCategory = 'All Articles';
    let searchQuery = '';

    // URL parameter check (e.g. ?category=Construction+Cost+Control)
    const urlParams = new URLSearchParams(window.location.search);
    const initialCategory = urlParams.get('category');
    if (initialCategory) {
      const matchingChip = Array.from(categoryChips).find(c => c.dataset.category.toLowerCase() === initialCategory.toLowerCase());
      if (matchingChip) {
        activeCategory = matchingChip.dataset.category;
        categoryChips.forEach(c => {
          const isActive = c === matchingChip;
          c.classList.toggle('active', isActive);
          c.setAttribute('aria-pressed', String(isActive));
        });
      }
    }

    const filterArticles = () => {
      const q = searchQuery.trim().toLowerCase();
      let visibleCount = 0;

      articleCards.forEach(card => {
        const status = (card.getAttribute('data-status') || 'Published').toLowerCase();
        if (status === 'draft') {
          card.style.display = 'none';
          return;
        }

        const cat = card.getAttribute('data-category') || '';
        const title = (card.querySelector('.card-title')?.textContent || '').toLowerCase();
        const summary = (card.querySelector('.card-summary')?.textContent || '').toLowerCase();
        const keywords = (card.getAttribute('data-keywords') || '').toLowerCase();

        const matchesCategory = (activeCategory === 'All Articles' || cat.toLowerCase() === activeCategory.toLowerCase());
        const matchesSearch = !q || title.includes(q) || summary.includes(q) || cat.toLowerCase().includes(q) || keywords.includes(q);

        if (matchesCategory && matchesSearch) {
          card.style.display = 'flex';
          visibleCount++;
        } else {
          card.style.display = 'none';
        }
      });

      // Handle featured card visibility
      if (featuredCard) {
        const featStatus = (featuredCard.getAttribute('data-status') || 'Published').toLowerCase();
        if (featStatus === 'draft') {
          featuredCard.closest('.featured-section').style.display = 'none';
        } else {
          const featCat = featuredCard.getAttribute('data-category') || '';
          const featTitle = (featuredCard.querySelector('.featured-title')?.textContent || '').toLowerCase();
          const featSummary = (featuredCard.querySelector('.featured-summary')?.textContent || '').toLowerCase();
          const featKeywords = (featuredCard.getAttribute('data-keywords') || '').toLowerCase();

          const featMatchesCat = (activeCategory === 'All Articles' || featCat.toLowerCase() === activeCategory.toLowerCase());
          const featMatchesSearch = !q || featTitle.includes(q) || featSummary.includes(q) || featCat.toLowerCase().includes(q) || featKeywords.includes(q);

          if (featMatchesCat && featMatchesSearch) {
            featuredCard.closest('.featured-section').style.display = 'block';
          } else {
            featuredCard.closest('.featured-section').style.display = 'none';
          }
        }
      }

      // Update count using safe DOM nodes
      if (resultsCount) {
        const publishedCards = Array.from(articleCards).filter(c => (c.getAttribute('data-status') || 'Published').toLowerCase() !== 'draft');
        const total = publishedCards.length;
        resultsCount.textContent = '';
        if (q || activeCategory !== 'All Articles') {
          const prefix = document.createTextNode('Showing ');
          const strong = document.createElement('strong');
          strong.textContent = String(visibleCount);
          const suffix = document.createTextNode(` of ${total} articles`);
          resultsCount.append(prefix, strong, suffix);
        } else {
          const prefix = document.createTextNode('All ');
          const strong = document.createElement('strong');
          strong.textContent = String(total);
          const suffix = document.createTextNode(' articles');
          resultsCount.append(prefix, strong, suffix);
        }
      }

      // Update no results display
      if (noResults) {
        if (visibleCount === 0) {
          noResults.classList.add('visible');
        } else {
          noResults.classList.remove('visible');
        }
      }

      // Toggle search clear button
      if (searchClear) {
        searchClear.classList.toggle('visible', q.length > 0);
      }
    };

    // Category Click Handling
    categoryChips.forEach(chip => {
      chip.addEventListener('click', () => {
        categoryChips.forEach(c => {
          c.classList.remove('active');
          c.setAttribute('aria-pressed', 'false');
        });
        chip.classList.add('active');
        chip.setAttribute('aria-pressed', 'true');
        activeCategory = chip.getAttribute('data-category') || 'All Articles';

        // Update URL query state without page reload
        const newUrl = new URL(window.location);
        if (activeCategory === 'All Articles') {
          newUrl.searchParams.delete('category');
        } else {
          newUrl.searchParams.set('category', activeCategory);
        }
        window.history.replaceState({}, '', newUrl);

        filterArticles();
      });
    });

    // Search Input Handling
    if (searchInput) {
      searchInput.addEventListener('input', e => {
        searchQuery = (e.target.value || '').slice(0, 100);
        filterArticles();
      });

      searchInput.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
          searchInput.value = '';
          searchQuery = '';
          filterArticles();
        }
      });
    }

    if (searchClear) {
      searchClear.addEventListener('click', () => {
        if (searchInput) {
          searchInput.value = '';
          searchInput.focus();
        }
        searchQuery = '';
        filterArticles();
      });
    }

    if (resetFiltersBtn) {
      resetFiltersBtn.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        searchQuery = '';
        activeCategory = 'All Articles';
        categoryChips.forEach((c, idx) => {
          const isFirst = idx === 0;
          c.classList.toggle('active', isFirst);
          c.setAttribute('aria-pressed', String(isFirst));
        });
        const newUrl = new URL(window.location);
        newUrl.searchParams.delete('category');
        window.history.replaceState({}, '', newUrl);
        filterArticles();
      });
    }

    // Run initial filter in case query params exist
    filterArticles();
  });
})();
