/**
 * Jasanika 2 Header Scripts
 *
 * Modular WordPress Framework
 * Version 0.28
 * M28 — Dynamic Header Builder
 *
 * Responsibilities:
 * - Sticky header management (scroll classes)
 * - Search toggle (desktop expand / mobile overlay)
 * - Mobile navigation hamburger menu
 * - Responsive behavior
 */
(function () {
    'use strict';

    var header = document.getElementById('jas-header');

    if (!header) {
        return;
    }

    // ---------------------------------------------------------------
    //  State
    // ---------------------------------------------------------------

    var isSticky = header.classList.contains('jas-header--sticky');
    var lastScrollY = 0;
    var isSearchOpen = false;
    var isMobileNavOpen = false;

    // ---------------------------------------------------------------
    //  Sticky Header
    // ---------------------------------------------------------------

    if (isSticky) {
        var onScroll = function () {
            var scrollY = window.scrollY || window.pageYOffset;

            if (scrollY > 50) {
                header.classList.add('jas-header--scrolled');
            } else {
                header.classList.remove('jas-header--scrolled');
            }

            lastScrollY = scrollY;
        };

        window.addEventListener('scroll', onScroll, { passive: true });
        // Initial check
        onScroll();
    }

    // ---------------------------------------------------------------
    //  Search Toggle
    // ---------------------------------------------------------------

    var searchToggle = header.querySelector('.jas-search-toggle');
    var searchArea = header.querySelector('.jas-header__search');
    var mobileSearchOverlay = null;

    if (searchToggle) {
        searchToggle.addEventListener('click', function (e) {
            e.preventDefault();

            if (isMobile()) {
                // Mobile: create/remove fullscreen overlay
                if (!isSearchOpen) {
                    openMobileSearch();
                } else {
                    closeMobileSearch();
                }
                searchToggle.setAttribute('aria-expanded', String(isSearchOpen));
            } else {
                // Desktop: toggle inline search area
                if (searchArea) {
                    isSearchOpen = !isSearchOpen;
                    searchArea.hidden = !isSearchOpen;
                    searchToggle.setAttribute('aria-expanded', String(isSearchOpen));

                    if (isSearchOpen) {
                        var input = searchArea.querySelector('input[type="search"]');
                        if (input) {
                            input.focus();
                        }
                    }
                }
            }
        });
    }

    /**
     * Open the mobile fullscreen search overlay.
     */
    function openMobileSearch() {
        if (mobileSearchOverlay) {
            mobileSearchOverlay.remove();
            mobileSearchOverlay = null;
        }

        mobileSearchOverlay = document.createElement('div');
        mobileSearchOverlay.className = 'jas-mobile-search-overlay jas-mobile-search-overlay--open';

        var closeBtn = document.createElement('button');
        closeBtn.className = 'jas-mobile-search-overlay__close';
        closeBtn.setAttribute('aria-label', 'Close search');
        closeBtn.innerHTML = '&times;';
        closeBtn.addEventListener('click', function () {
            closeMobileSearch();
            searchToggle.setAttribute('aria-expanded', 'false');
        });

        mobileSearchOverlay.appendChild(closeBtn);

        // Clone search form
        if (searchArea) {
            var form = searchArea.querySelector('.search-form');
            if (form) {
                var clonedForm = form.cloneNode(true);
                clonedForm.addEventListener('submit', function () {
                    closeMobileSearch();
                });
                mobileSearchOverlay.appendChild(clonedForm);

                var input = clonedForm.querySelector('input[type="search"]');
                if (input) {
                    setTimeout(function () {
                        input.focus();
                    }, 100);
                }
            }
        }

        document.body.appendChild(mobileSearchOverlay);
        isSearchOpen = true;
        document.body.style.overflow = 'hidden';
    }

    /**
     * Close the mobile fullscreen search overlay.
     */
    function closeMobileSearch() {
        if (mobileSearchOverlay) {
            mobileSearchOverlay.remove();
            mobileSearchOverlay = null;
        }
        isSearchOpen = false;
        document.body.style.overflow = '';
    }

    // Close mobile search on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isSearchOpen && isMobile()) {
            closeMobileSearch();
            if (searchToggle) {
                searchToggle.setAttribute('aria-expanded', 'false');
            }
        }
    });

    // ---------------------------------------------------------------
    //  Mobile Navigation Toggle
    // ---------------------------------------------------------------

    var mobileToggle = header.querySelector('.jas-mobile-nav-toggle');
    var headerNav = document.getElementById('jas-header-nav');

    if (mobileToggle && headerNav) {
        mobileToggle.addEventListener('click', function (e) {
            e.preventDefault();
            isMobileNavOpen = !isMobileNavOpen;

            headerNav.classList.toggle('jas-mobile-nav--open', isMobileNavOpen);
            header.classList.toggle('jas-mobile-nav--open', isMobileNavOpen);
            mobileToggle.setAttribute('aria-expanded', String(isMobileNavOpen));
        });

        // Close mobile nav when clicking a link
        headerNav.addEventListener('click', function (e) {
            var link = e.target.closest('a');
            if (link && isMobileNavOpen) {
                isMobileNavOpen = false;
                headerNav.classList.remove('jas-mobile-nav--open');
                header.classList.remove('jas-mobile-nav--open');
                mobileToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // ---------------------------------------------------------------
    //  Helpers
    // ---------------------------------------------------------------

    /**
     * Check whether the viewport is at mobile breakpoint.
     */
    function isMobile() {
        return window.innerWidth < 768;
    }

    /**
     * Handle resize for cleanup.
     */
    var resizeTimer = null;
    window.addEventListener('resize', function () {
        if (resizeTimer) {
            cancelAnimationFrame(resizeTimer);
        }
        resizeTimer = requestAnimationFrame(function () {
            // Close mobile search when resizing to desktop
            if (!isMobile() && isSearchOpen) {
                closeMobileSearch();
                if (searchToggle) {
                    searchToggle.setAttribute('aria-expanded', 'false');
                }
                if (searchArea) {
                    searchArea.hidden = true;
                }
            }
        });
    }, { passive: true });

})();