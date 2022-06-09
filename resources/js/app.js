import { createApp } from 'vue';
window.axios = require('axios');

import Home_OnboardingDummy from './components/home/onboarding_dummy.vue';
import Home_NewsDummy from './components/home/news_dummy.vue';
import Home_MediaDummy from './components/home/media_dummy.vue';
import Home_AboutDummy from './components/home/about_dummy.vue';

const app = createApp({
    mixins: [],
    components: {
        "home_onboarding": Home_OnboardingDummy,
        "home_news": Home_NewsDummy,
        "home_media": Home_MediaDummy,
        "home_about": Home_AboutDummy
    },
    data() {
        return {
            page_data: {
                path: "",
                host: ""
            },
            sections: {
                "onboarding": {
                    slug: "/",
                    nearby: ["news"]
                },
                "news": {
                    slug: "/news",
                    nearby: ["onboarding", "media"]
                },
                "media": {
                    slug: "/media",
                    nearby: ["news", "about"]
                },
                "about": {
                    slug: "/about",
                    nearby: ["media"]
                }
            },
            section_now: -1,
            header_visible: false,
            csrf: document.querySelector('meta[name="csrf-token"]').content
        };
    },
    beforeMount() {
        this.page_data.path = window.location.pathname.trim();
        this.page_data.host = window.location.hostname.trim();

        this.sections_index = -1;

        // ! ИЗМЕНИТЬ ПЕРЕД ПУБЛИКАЦИЕЙ
        if (this.page_data.path == "/") {
            this.section_now = "onboarding";
        } else {
            this.section_now = this.page_data.path.substring(1);
        }
    },
    mounted() {
        console.log(this.page_data);

        if (!this.page_data.path.startsWith("/admin")) {
            // Инициализация секций
            try {
                this.sections_index = Object.keys(this.sections).indexOf(this.section_now);
                this.section_changed();

                if (this.sections_index > 0) {
                    this.header_visible = true;
                }

                window.addEventListener("resize", this.onResize);
                document.addEventListener('DOMContentLoaded', this.onResize);
            } catch (e) {
                console.log("section error!");
            }

            var vapp = this;
            // Главная страница
            slider.init({
                pager: false,
                speed: 600,
                easing: "ease",
                beforeChange: function (index, page, toIndex) {
                    if (!vapp.header_visible && toIndex > 0) {
                        vapp.header_visible = true;
                    }

                    if (toIndex == 0) {
                        vapp.header_visible = false;
                    }

                    vapp.section_now = Object.keys(vapp.sections)[toIndex];
                    vapp.section_changed();

                    window.history.replaceState({}, null, vapp.sections[vapp.section_now].slug);
                }
            }, this.sections_index);
        }
    },
    methods: {
        onResize: function () {
            document.querySelector(".slider").style.height = window.innerHeight + "px";
        },
        expand_toggle: function (expand_selector) {
            document.querySelector(expand_selector).classList.toggle("exp-active");
        },
        tab_select: function (selector, tab) {
            document.querySelector(selector).dataset.tab = tab;
        },
        section_changed: async function () {
            if (this.sections_index != -1) {
                this.sections[this.section_now].nearby.forEach(section => {
                    try {
                        if (this.$refs[section] != undefined && !this.$refs[section].loaded) {
                            this.$refs[section].load();
                        }
                    } catch (e) {
                        console.warn("error " + section);
                    }

                });
            }
        }
    }
});

app.mount('#app');