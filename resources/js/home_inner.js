import { Canvas } from 'glsl-canvas-js';
import { fragmentShader } from './../assets/shaders/back-gradient-inner';
var canvas_render = {
    person_image: document.querySelector('.person-photo img'),
    canvas: document.querySelector('canvas.background'),
    overlay: document.querySelector('.background-basic'),
    throttler: 0,

    f_rgb2hsl: function (r, g, b) {
        r /= 255, g /= 255, b /= 255;
        var max = Math.max(r, g, b), min = Math.min(r, g, b);
        var h, s, l = (max + min) / 2;

        if (max == min) {
            h = s = 0; // achromatic
        } else {
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }

        return [h, s, l];
    },
    f_hsl2rgb: function (h, s, l) {
        var r, g, b;

        if (s == 0) {
            r = g = b = l; // achromatic
        } else {
            var hue2rgb = function hue2rgb(p, q, t) {
                if (t < 0) t += 1;
                if (t > 1) t -= 1;
                if (t < 1 / 6) return p + (q - p) * 6 * t;
                if (t < 1 / 2) return q;
                if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
                return p;
            };

            var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
            var p = 2 * l - q;
            r = hue2rgb(p, q, h + 1 / 3);
            g = hue2rgb(p, q, h);
            b = hue2rgb(p, q, h - 1 / 3);
        }

        return [Math.round(r * 255), Math.round(g * 255), Math.round(b * 255)];
    },
    f_scroll: function (e, force = false) {
        if (this.throttler < 3 && !force) {
            this.throttler++;
        } else {
            this.throttler = 0;

            let op = (window.pageYOffset / 400);
            op = op >= 1 ? 1 : op;

            if (window.pageYOffset > 800) {
                this.canvas.style.display = "none";
            } else {
                this.canvas.style.display = "block";
            }

            this.overlay.style.opacity = op;
        }
    },
    f_color: function () {
        let s = Math.random();
        let b = Math.random();

        let result = [];
        if (s < 0.20) {
            result = [[205, 90, 76], [205, 81, 85]];
        } else if (s < 0.40) {
            result = [[142, 51, 65], [144, 48, 80]];
        } else if (s < 0.60) {
            result = [[266, 87, 85], [262, 78, 91]];
        } else if (s < 0.80) {
            result = [[51, 55, 63], [52, 49, 79]];
        } else {
            result = [[4, 71, 82], [4, 60, 89]];
        }/*  else {
            result = b < 0.5 ? [210, 13, 94] : [210, 40, 98];
        } */

        return result;
    },
    f_render: function (shader) {
        if (window.previousRendering) {
            window.previousRendering.destroy();
        }

        const glsl = new Canvas(this.canvas,
            {
                alpha: false,
                antialias: true,
                depth: false,
                stencil: false,
                mode: 'flat',
                powerPreference: 'high-performance',
                failIfMajorPerformanceCaveat: true,
                desynchronized: true,
                precision: 'mediump'
            });
        let played = false;

        glsl.on('error', e => {
            const error = String(e.error);
            console.error(error);
        });

        let seed = Math.round(1000 * Math.random()) / 1000;
        let colors = [];
        let gen_colors = this.f_color();

        console.log(gen_colors);

        colors.push(this.f_hsl2rgb(gen_colors[0][0] / 360, gen_colors[0][1] / 100, gen_colors[0][2] / 100));
        colors.push(this.f_hsl2rgb(gen_colors[1][0] / 360, gen_colors[1][1] / 100, gen_colors[1][2] / 100));
        colors.push(this.f_hsl2rgb(0, 0, 1));

        shader = shader.replace("%%SEED%%", seed);
        shader = shader.replace("%%SEED%%", seed);
        shader = shader.replace("%%SEED%%", seed);

        shader = shader.replace("%%CLR1%%", colors[0][0] + ", " + colors[0][1] + ", " + colors[0][2]);
        shader = shader.replace("%%CLR2%%", colors[1][0] + ", " + colors[1][1] + ", " + colors[1][2]);
        shader = shader.replace("%%CLR3%%", colors[2][0] + ", " + colors[2][1] + ", " + colors[2][2]);

        glsl.load(shader);


        if (!played) {
            glsl.play();
        }

        window.previousRendering = {
            destroy() {
                glsl.destroy();
            },
        };


        this.overlay.classList.remove('initial');
        this.f_scroll(null, true);

        window.onscroll = (e) => {
            this.f_scroll(e);
        };
    }
};

document.addEventListener("DOMContentLoaded", function (event) {
    canvas_render.f_render(fragmentShader);
});