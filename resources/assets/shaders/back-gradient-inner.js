export const fragmentShader = `#version 300 es

#ifdef GL_ES
precision highp float;
#endif

#define GLSLIFY 1

uniform float u_time;
uniform vec2 u_resolution;
out vec4 fragColor;

vec4 permute(vec4 x) {
    return mod(((x * 34.0) + 1.0) * x, 289.0);
}
vec4 taylorInvSqrt(vec4 r) {
    return 1.79284291400159 - 0.85373472095314 * r;
}

float snoise(vec3 v) {
    const vec2 C = vec2(1.0 / 6.0, 1.0 / 3.0);
    const vec4 D = vec4(0.0, 0.5, 1.0, 2.0);

        vec3 i = floor(v + dot(v, C.yyy));
    vec3 x0 = v - i + dot(i, C.xxx);

        vec3 g = step(x0.yzx, x0.xyz);
    vec3 l = 1.0 - g;
    vec3 i1 = min(g.xyz, l.zxy);
    vec3 i2 = max(g.xyz, l.zxy);

        vec3 x1 = x0 - i1 + 1.0 * C.xxx;
    vec3 x2 = x0 - i2 + 2.0 * C.xxx;
    vec3 x3 = x0 - 1. + 3.0 * C.xxx;

        i = mod(i, 289.0);
    vec4 p = permute(permute(permute(i.z + vec4(0.0, i1.z, i2.z, 1.0)) + i.y + vec4(0.0, i1.y, i2.y, 1.0)) + i.x + vec4(0.0, i1.x, i2.x, 1.0));

            float n_ = 1.0 / 7.0;     vec3 ns = n_ * D.wyz - D.xzx;

    vec4 j = p - 49.0 * floor(p * ns.z * ns.z);  
    vec4 x_ = floor(j * ns.z);
    vec4 y_ = floor(j - 7.0 * x_);    
    vec4 x = x_ * ns.x + ns.yyyy;
    vec4 y = y_ * ns.x + ns.yyyy;
    vec4 h = 1.0 - abs(x) - abs(y);

    vec4 b0 = vec4(x.xy, y.xy);
    vec4 b1 = vec4(x.zw, y.zw);

    vec4 s0 = floor(b0) * 2.0 + 1.0;
    vec4 s1 = floor(b1) * 2.0 + 1.0;
    vec4 sh = -step(h, vec4(0.0));

    vec4 a0 = b0.xzyw + s0.xzyw * sh.xxyy;
    vec4 a1 = b1.xzyw + s1.xzyw * sh.zzww;

    vec3 p0 = vec3(a0.xy, h.x);
    vec3 p1 = vec3(a0.zw, h.y);
    vec3 p2 = vec3(a1.xy, h.z);
    vec3 p3 = vec3(a1.zw, h.w);

        vec4 norm = taylorInvSqrt(vec4(dot(p0, p0), dot(p1, p1), dot(p2, p2), dot(p3, p3)));
    p0 *= norm.x;
    p1 *= norm.y;
    p2 *= norm.z;
    p3 *= norm.w;

        vec4 m = max(0.6 - vec4(dot(x0, x0), dot(x1, x1), dot(x2, x2), dot(x3, x3)), 0.0);
    m = m * m;
    return 60.0 * dot(m * m, vec4(dot(p0, x0), dot(p1, x1), dot(p2, x2), dot(p3, x3)));
}

#define SRGB_TO_LINEAR(c) pow((c), vec3(2.2))
#define LINEAR_TO_SRGB(c) pow((c), vec3(1.0 / 2.2))
#define SRGB(r, g, b) SRGB_TO_LINEAR(vec3(float(r), float(g), float(b)) / 255.0)

float gradientNoise(in vec2 uv) {
    const vec3 magic = vec3(0.06711056, 0.00583715, 52.9829189);
    return fract(magic.z * fract(dot(uv, magic.xy)));
}

mat2 rotate2d(float _angle) {
    return mat2(cos(_angle), -sin(_angle), sin(_angle), cos(_angle));
}

void main() {
	
    vec3 u_color1 = vec3(%%CLR1%%);
    vec3 u_color2 = vec3(%%CLR2%%);
    vec3 u_color3 = vec3(%%CLR3%%);
	
    vec3 COLOR0 = SRGB(u_color1.x, u_color1.y, u_color1.z);
    vec3 COLOR1 = SRGB(u_color2.x, u_color2.y, u_color2.z);
    vec3 COLOR2 = SRGB(u_color3.x, u_color3.y, u_color3.z);

    vec2 coord = gl_FragCoord.yx;
    vec2 ogCoord = coord;
    coord -= .5 * coord;
    coord = rotate2d(u_time * .05) * coord;
    coord += .5 * coord;

    vec2 a = 1.1 * u_resolution.xy;     vec2 b = 1.0 * u_resolution.xy;     
        vec2 ba = b - a;
    float t = dot(coord - a, ba) / dot(ba, ba);
        t = smoothstep(0.0, 1.0, clamp(t, 0.0, 1.0));

    float ctime = fract(u_time * .025);

        float thirdtime = ctime;
    ctime = fract(ctime * 3.);
    ctime = ((sin(ctime * 3.14) + 1.) - 1.) * .333;
    ctime = clamp(ctime, 0., 1.);
    ctime *= ctime;
    ctime += thirdtime;
    ctime = clamp(ctime, 0., 1.);

    float cStop1 = min(1., max(0., ctime - 0.0) * 3.);
    float cStop2 = min(1., max(0., ctime - 0.33) * 3.);
    float cStop3 = min(1., max(0., ctime - 0.66) * 3.);

    vec3 col1 = mix(COLOR0, COLOR1, cStop1);
    col1 = mix(col1, COLOR2, cStop2);
    col1 = mix(col1, COLOR0, cStop3);
    vec3 col2 = mix(COLOR1, COLOR2, cStop1);
    col2 = mix(col2, COLOR0, cStop2);
    col2 = mix(col2, COLOR1, cStop3);

    vec3 color = mix(col1, col2, clamp(t - snoise(vec3(vec2(%%SEED%% * 153. + coord.y * .00125, %%SEED%% * 516. + coord.x * .00125), u_time * .125 + %%SEED%% * 1241.)), 0., 1.));

    color = LINEAR_TO_SRGB(color);

    color += (1.0 / 255.0) * gradientNoise(coord) - (0.5 / 255.0);

    fragColor = vec4(color, 1.0);
}
`