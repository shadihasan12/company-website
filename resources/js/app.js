import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

/* -------------------------------------------------------------------------
 * Motion preferences and direction
 * ---------------------------------------------------------------------- */

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/**
 * In RTL the visual "from the left" is the opposite screen edge, so every
 * horizontal offset is mirrored. Returns 1 for LTR and -1 for RTL.
 */
const directionFactor = () => (document.documentElement.dir === 'rtl' ? -1 : 1);

/* -------------------------------------------------------------------------
 * Theme
 * ---------------------------------------------------------------------- */

const THEME_KEY = 'theme';

/**
 * Reads the stored theme, falling back to the OS setting and then to dark.
 * Storage can throw in private browsing, so every access is guarded.
 */
function storedTheme() {
    try {
        const value = localStorage.getItem(THEME_KEY);
        if (value === 'light' || value === 'dark') {
            return value;
        }
    } catch {
        // Storage unavailable — fall through to the OS preference.
    }

    return window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
}

function applyTheme(theme) {
    document.documentElement.classList.toggle('dark', theme === 'dark');
}

Alpine.store('theme', {
    current: storedTheme(),

    init() {
        applyTheme(this.current);
    },

    toggle() {
        this.current = this.current === 'dark' ? 'light' : 'dark';
        applyTheme(this.current);

        try {
            localStorage.setItem(THEME_KEY, this.current);
        } catch {
            // Non-fatal: the theme still applies for this page view.
        }
    },

    get isDark() {
        return this.current === 'dark';
    },
});

/* -------------------------------------------------------------------------
 * Scroll reveal
 *
 * Any element tagged `data-reveal` fades and rises into view once. Grouping
 * children under `data-reveal-group` staggers them instead of animating each
 * on its own trigger, which keeps ScrollTrigger instance counts low.
 * ---------------------------------------------------------------------- */

function initReveal() {
    if (prefersReducedMotion) {
        // Elements are visible by default in CSS, so there is nothing to undo.
        return;
    }

    const dir = directionFactor();

    gsap.utils.toArray('[data-reveal]').forEach((el) => {
        const axis = el.dataset.reveal || 'up';
        const from = { opacity: 0, duration: 0.8, ease: 'expo.out' };

        if (axis === 'up') from.y = 32;
        if (axis === 'down') from.y = -32;
        if (axis === 'start') from.x = -40 * dir;
        if (axis === 'end') from.x = 40 * dir;
        if (axis === 'scale') from.scale = 0.94;

        gsap.from(el, {
            ...from,
            scrollTrigger: {
                trigger: el,
                start: 'top 88%',
                once: true,
            },
        });
    });

    gsap.utils.toArray('[data-reveal-group]').forEach((group) => {
        const children = group.children;
        if (!children.length) return;

        gsap.from(children, {
            opacity: 0,
            y: 28,
            duration: 0.7,
            ease: 'expo.out',
            stagger: 0.08,
            scrollTrigger: {
                trigger: group,
                start: 'top 85%',
                once: true,
            },
        });
    });
}

/* -------------------------------------------------------------------------
 * Count-up statistics
 *
 * `data-countup="2500"` animates 0 -> 2500 when scrolled into view, keeping
 * any prefix/suffix already in the markup via data-prefix / data-suffix.
 * ---------------------------------------------------------------------- */

function initCountUps() {
    gsap.utils.toArray('[data-countup]').forEach((el) => {
        const target = parseFloat(el.dataset.countup);
        if (Number.isNaN(target)) return;

        const decimals = parseInt(el.dataset.decimals ?? '0', 10);
        const prefix = el.dataset.prefix ?? '';
        const suffix = el.dataset.suffix ?? '';
        // Arabic pages use Arabic-Indic digits, matching the page locale.
        const locale = document.documentElement.lang || 'en';
        const format = (value) =>
            prefix + value.toLocaleString(locale, {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals,
            }) + suffix;

        if (prefersReducedMotion) {
            el.textContent = format(target);
            return;
        }

        const counter = { value: 0 };

        gsap.to(counter, {
            value: target,
            duration: 1.6,
            ease: 'expo.out',
            onUpdate: () => {
                el.textContent = format(counter.value);
            },
            scrollTrigger: {
                trigger: el,
                start: 'top 90%',
                once: true,
            },
        });
    });
}

/* -------------------------------------------------------------------------
 * Boot
 * ---------------------------------------------------------------------- */

window.Alpine = Alpine;
Alpine.plugin(collapse);
Alpine.start();

// Wait for fonts before measuring scroll positions: a late font swap shifts
// layout and would leave ScrollTrigger with stale start/end values.
const ready = document.fonts?.ready ?? Promise.resolve();

ready.then(() => {
    initReveal();
    initCountUps();
    ScrollTrigger.refresh();
});
