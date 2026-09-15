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

/**
 * Every reveal is a `fromTo`, never a `from`, and every one hands the
 * element back to CSS when it finishes.
 *
 * `from()` reads the element's *current* value as the destination, and it
 * reads it lazily, at the moment that element's slice of the stagger
 * starts. Any CSS transition covering opacity is still mid-flight at that
 * point, so the destination it captured was a fraction rather than 1 and
 * the card stopped fading there — permanently. Stating both ends removes
 * the guesswork; `clearProps` then drops the inline transform GSAP leaves
 * behind, which would otherwise outrank the hover-lift utility class.
 */
const REVEAL_CLEAR = 'opacity,transform,translate,rotate,scale';

function initReveal() {
    if (prefersReducedMotion) {
        // Elements are visible by default in CSS, so there is nothing to undo.
        return;
    }

    const dir = directionFactor();

    gsap.utils.toArray('[data-reveal]').forEach((el) => {
        const axis = el.dataset.reveal || 'up';
        const from = { opacity: 0 };
        const to = { opacity: 1 };

        if (axis === 'up' || axis === 'down') {
            from.y = axis === 'up' ? 32 : -32;
            to.y = 0;
        }

        if (axis === 'start' || axis === 'end') {
            from.x = (axis === 'start' ? -40 : 40) * dir;
            to.x = 0;
        }

        if (axis === 'scale') {
            from.scale = 0.94;
            to.scale = 1;
        }

        gsap.fromTo(el, from, {
            ...to,
            duration: 0.8,
            ease: 'expo.out',
            clearProps: REVEAL_CLEAR,
            scrollTrigger: {
                trigger: el,
                start: 'top 88%',
                once: true,
            },
        });
    });

    gsap.utils.toArray('[data-reveal-group]').forEach((group) => {
        // A static copy: `children` is a live collection and GSAP holds the
        // target list for the life of the tween.
        const children = gsap.utils.toArray(group.children);
        if (!children.length) return;

        gsap.fromTo(
            children,
            { opacity: 0, y: 28 },
            {
                opacity: 1,
                y: 0,
                duration: 0.7,
                ease: 'expo.out',
                stagger: 0.08,
                clearProps: REVEAL_CLEAR,
                scrollTrigger: {
                    trigger: group,
                    start: 'top 85%',
                    once: true,
                },
            },
        );
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
 * Hero
 *
 * Two rules govern everything here:
 *
 *   1. The h1 is the LCP element and is never faded in. An element at
 *      opacity 0 does not count as painted, so a fade would delay LCP by
 *      the full duration of the animation. It moves on transform only.
 *   2. The ambient background animates transform and nothing else, so it
 *      stays on the compositor and never triggers layout or paint.
 * ---------------------------------------------------------------------- */

function initHero() {
    const hero = document.querySelector('[data-hero]');
    if (!hero || prefersReducedMotion) return;

    const headline = hero.querySelector('[data-hero-headline]');
    const items = hero.querySelectorAll('[data-hero-item]');

    const intro = gsap.timeline({ defaults: { ease: 'expo.out' } });

    if (headline) {
        // Transform only — no opacity. See rule 1 above.
        intro.from(headline, { y: 18, duration: 0.9 });
    }

    if (items.length) {
        intro.from(
            items,
            { opacity: 0, y: 20, duration: 0.7, stagger: 0.07 },
            headline ? '-=0.65' : 0,
        );
    }

    // Orbit rings. The attribute carries the period in seconds; a negative
    // value reverses direction so the rings never move as one block.
    hero.querySelectorAll('[data-hero-orbit]').forEach((orbit) => {
        const period = parseFloat(orbit.dataset.heroOrbit) || 40;

        gsap.to(orbit, {
            rotation: period > 0 ? 360 : -360,
            duration: Math.abs(period),
            repeat: -1,
            ease: 'none',
            transformOrigin: 'center center',
        });
    });

    // Slow independent drift so the glows never settle into a static image.
    hero.querySelectorAll('[data-hero-glow]').forEach((glow, index) => {
        gsap.to(glow, {
            xPercent: gsap.utils.random(-12, 12),
            yPercent: gsap.utils.random(-10, 10),
            duration: gsap.utils.random(14, 22),
            delay: index * 0.6,
            repeat: -1,
            yoyo: true,
            ease: 'sine.inOut',
        });
    });
}

/* -------------------------------------------------------------------------
 * Lightbox
 *
 * Wraps an image gallery. Arrow keys and Escape are bound by the markup;
 * this only owns the state and the body scroll lock.
 * ---------------------------------------------------------------------- */

Alpine.data('lightbox', (images = []) => ({
    images,
    isOpen: false,
    current: 0,

    open(index) {
        this.current = index;
        this.isOpen = true;
        // Without this the page behind the overlay scrolls on arrow keys.
        document.body.style.overflow = 'hidden';
    },

    close() {
        if (!this.isOpen) return;

        this.isOpen = false;
        document.body.style.overflow = '';
    },

    next() {
        if (!this.isOpen) return;

        this.current = (this.current + 1) % this.images.length;
    },

    previous() {
        if (!this.isOpen) return;

        // Modulo on a negative index returns a negative in JS, so add the
        // length before wrapping.
        this.current = (this.current - 1 + this.images.length) % this.images.length;
    },
}));

/* -------------------------------------------------------------------------
 * Boot
 * ---------------------------------------------------------------------- */

window.Alpine = Alpine;
Alpine.plugin(collapse);
Alpine.start();

// Wait for fonts before measuring scroll positions: a late font swap shifts
// layout and would leave ScrollTrigger with stale start/end values.
const ready = document.fonts?.ready ?? Promise.resolve();

// The hero animates immediately rather than waiting on fonts: it is
// above the fold and a late start would be visible.
initHero();

ready.then(() => {
    initReveal();
    initCountUps();
    ScrollTrigger.refresh();
});
