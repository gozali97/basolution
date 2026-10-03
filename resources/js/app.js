import AOS from 'aos';

const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
const aosStartEvent = 'aos:ready';

const initAos = () => {
    document.querySelectorAll('main section h1, main section h2, main section article, main section img, main section form')
        .forEach((element, index) => {
            element.dataset.aos ||= element.tagName === 'IMG' ? 'zoom-in' : 'fade-up';
            element.dataset.aosDelay ||= String(Math.min((index % 5) * 100, 400));
        });

    document.querySelectorAll('[data-aos]').forEach((element) => {
        element.dataset.aosDuration ||= '800';
        element.dataset.aosOnce = 'false';
    });

    AOS.init({
        startEvent: aosStartEvent,
        duration: 800,
        easing: 'ease-in-out',
        once: false,
        offset: 100,
        delay: 0,
        disable: () => reducedMotion,
    });
    document.documentElement.classList.add('aos-ready');

    requestAnimationFrame(() => requestAnimationFrame(() => {
        document.dispatchEvent(new Event(aosStartEvent));
    }));
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAos, { once: true });
} else {
    initAos();
}

window.addEventListener('load', () => AOS.refreshHard(), { once: true });

// One slider for the hero (data-autoplay) and testimonials (manual). Slides are stacked; aria-hidden drives visibility in CSS.
document.querySelectorAll('[data-slider]').forEach((root) => {
    const slides = [...root.querySelectorAll('[data-slide]')];
    if (slides.length < 2) return;

    const dots = [...root.querySelectorAll('[data-dot]')];
    const live = root.querySelector('[data-live]');
    const toggle = root.querySelector('[data-toggle]');
    const delay = Number(root.dataset.autoplay) || 0;
    let current = 0;
    let playing = delay > 0 && !reducedMotion;

    const show = (n) => {
        current = (n + slides.length) % slides.length;
        slides.forEach((s, i) => (i === current ? s.removeAttribute('aria-hidden') : s.setAttribute('aria-hidden', 'true')));
        dots.forEach((d, i) => (i === current ? d.setAttribute('aria-current', 'true') : d.removeAttribute('aria-current')));
    };

    const render = () => {
        if (!toggle) return;
        toggle.setAttribute('aria-label', playing ? 'Jeda slide' : 'Putar slide');
        toggle.querySelector('[data-icon="pause"]').toggleAttribute('hidden', !playing);
        toggle.querySelector('[data-icon="play"]').toggleAttribute('hidden', playing);
        live?.setAttribute('aria-live', playing ? 'off' : 'polite');
    };

    dots.forEach((d, i) => d.addEventListener('click', () => show(i)));
    root.querySelector('[data-prev]')?.addEventListener('click', () => show(current - 1));
    root.querySelector('[data-next]')?.addEventListener('click', () => show(current + 1));
    toggle?.addEventListener('click', () => {
        playing = !playing;
        render();
    });
    root.querySelectorAll('[data-controls]').forEach((c) => c.classList.replace('hidden', 'flex'));
    render();

    if (delay) {
        // Hover or keyboard focus inside the slider holds the current slide (WCAG 2.2.2).
        setInterval(() => {
            const focused = root.contains(document.activeElement) && document.activeElement.matches(':focus-visible');
            if (playing && !document.hidden && !root.matches(':hover') && !focused) show(current + 1);
        }, delay);
    }
});
