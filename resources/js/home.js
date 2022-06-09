import tippy, { roundArrow } from 'tippy.js';
import 'tippy.js/dist/svg-arrow.css';
import { Canvas } from 'glsl-canvas-js';
import { fragmentShader } from './../assets/shaders/back-gradient';

import Swiper, { Navigation, Pagination, Autoplay } from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/autoplay';

var icon_tooltips = tippy(
    '.tt-icon',
    {
        theme: 'fbki',
        placement: 'bottom',
        arrow: roundArrow,
        duration: [100, 0],
        offset: [null, 8],
        popperOptions: {
            strategy: 'fixed',
        },
        content(el) {
            return el.getAttribute('data-tt');
        },
    });

var anchor_controls = {
    "data": {
        "links": [],
        "navigation_links": [],
        "navigation_sections": [],
        "temp_index": null,
    },
    "f_init": function () {
        this.f_clear_hash();

        // Подсвечивание активной ссылки
        this.data.navigation_links = document.querySelectorAll(".navigation-menu > a[href^=\"#\"]");
        this.data.navigation_links.forEach(link => {
            this.data.navigation_sections.push(document.querySelector("section[id=\"" + link.getAttribute("href").substring(1) + "\"]"));
        });
        this.f_check_navlinks();
        window.addEventListener('scroll', this.f_check_navlinks, { passive: true });

        // Замена обычного скролла во всех ссылках на якори (#)
        this.data.links = document.querySelectorAll("a[href^=\"#\"]");
        this.data.links.forEach(anchor_elem => {
            anchor_elem.addEventListener('click', function (e) {
                e.preventDefault();

                anchor_controls.f_scroll_hash(this.getAttribute('href'));
            }, { passive: true });
        });
    },
    "f_clear_hash": function () {
        history.pushState("", document.title, window.location.pathname + window.location.search);
    },
    "f_scroll_hash": function (href) {
        anchor_controls.data.header_height = document.querySelector("header").offsetHeight;
        let destination_y = document.querySelector(href).getBoundingClientRect().top + window.pageYOffset - anchor_controls.data.header_height;

        window.scrollTo({ top: destination_y, behavior: 'smooth' });
    },
    "f_check_navlinks": function () {
        anchor_controls.data.temp_index = anchor_controls.data.navigation_sections.length;
        anchor_controls.data.header_height = document.querySelector("header").offsetHeight;
        anchor_controls.data.temp_index = -1;
        anchor_controls.data.scroll_y = Math.floor(window.scrollY + anchor_controls.data.header_height) + 10;

        for (let link_id = 0; link_id < anchor_controls.data.navigation_links.length; link_id++) {
            let nav_link = anchor_controls.data.navigation_links[link_id];

            if (anchor_controls.data.scroll_y >= anchor_controls.data.navigation_sections[link_id].offsetTop) {
                if ((link_id + 1 != anchor_controls.data.navigation_links.length) && (anchor_controls.data.scroll_y < anchor_controls.data.navigation_sections[link_id + 1].offsetTop)) {
                    // Не последний, скролл всё ещё ниже следующего
                    anchor_controls.data.temp_index = link_id;
                } else if (link_id + 1 == anchor_controls.data.navigation_links.length) {
                    // Последний, скролл выше его начала
                    anchor_controls.data.temp_index = link_id;
                } else {
                    nav_link.classList.remove('active');
                }
            } else {
                nav_link.classList.remove('active');
            }
        }

        if (anchor_controls.data.temp_index >= 0 && anchor_controls.data.temp_index < anchor_controls.data.navigation_links.length) {
            anchor_controls.data.navigation_links[anchor_controls.data.temp_index].classList.add('active');
        }
    }
};


var news_important = {
    "swiper": new Swiper('.news-important .swiper', {
        modules: [Navigation, Pagination, Autoplay],

        direction: 'horizontal',
        loop: true,
        spaceBetween: 60,
        autoHeight: true,
        // cssMode: true,
        speed: 600,
        grabCursor: true,
        autoplay: {
            delay: 7500,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true
        },
        on: {
            init: function () {
                // Авто-пауза по наведению
                document.querySelector('.news-important').addEventListener('mouseenter', () => {
                    news_important.swiper.autoplay.stop();
                }, { passive: true });
                document.querySelector('.news-important').addEventListener('mouseleave', () => {
                    news_important.swiper.autoplay.start();
                }, { passive: true });
            },
            slideChange: function (s) {
                document.querySelector('section#onboarding .news-important .news-btn-more').setAttribute('href', s.slides[s.activeIndex].getAttribute('data-link'));
            }
        },
    }),
    "f_init": function () {
        window.addEventListener("resize", (e) => {
            this.f_resize(e);
        }, { passive: true });

        this.f_resize();
    },
    "f_resize": function (e = null) {
        this.swiper.autoplay.start();
        this.swiper.update();
    }
};

var header_helper = {
    "data": {
        element: null,
        top: 48,
        timeout: null,
    },
    "f_init": function () {
        this.data.element = document.querySelector("header");

        if (window.pageYOffset > this.data.top) {
            this.data.element.classList.add("collapsed");
            this.data.element.classList.remove("expanded");
        } else {
            this.data.element.classList.remove("collapsed");
            this.data.element.classList.add("expanded");
        }
        this.data.element.classList.add("visible");

        setTimeout(() => {
            window.onscroll = (e) => {
                this.f_onscroll();
            };
        }, 100);

    },
    "f_onscroll": function () {
        if (window.pageYOffset > this.data.top) {
            this.data.element.classList.add("collapsed");
            this.data.element.classList.remove("expanded");

            if (this.data.timeout != null) {
                clearTimeout(this.data.timeout);
                this.data.timeout = null;
            }
        } else {
            if (this.data.timeout == null) {
                this.data.timeout = setTimeout(() => {
                    this.data.element.classList.remove("collapsed");
                    this.data.element.classList.add("expanded");

                    clearTimeout(this.data.timeout);
                    this.data.timeout = null;
                }, 250);
            }


        }
    }
};

var canvas_render = {
    f_render: function (shader) {
        if (window.previousRendering) {
            window.previousRendering.destroy();
        }

        const canvas = document.querySelector('section#onboarding canvas');
        const glsl = new Canvas(canvas,
            {
                alpha: false,
                antialias: false,
                depth: false,
                mode: 'flat',
                powerPreference: 'high-performance',
                failIfMajorPerformanceCaveat: true,
                desynchronized: true,
                precision: 'mediump',
            });
        let played = false;

        glsl.on('error', e => {
            const error = String(e.error);
            console.error(error);
            console.trace(error);
        });

        let seed = Math.round(1000 * Math.random()) / 1000;
        shader = shader.replace("%%SEED%%", seed);
        shader = shader.replace("%%SEED%%", seed);
        shader = shader.replace("%%SEED%%", seed);

        glsl.load(shader);

        if (!played) {
            glsl.play();
        }

        window.previousRendering = {
            destroy() {
                glsl.destroy();
            },
        };
    }
};

header_helper.f_init();
news_important.f_init();
document.addEventListener("DOMContentLoaded", function (event) {
    canvas_render.f_render(fragmentShader);
    anchor_controls.f_init();
});


