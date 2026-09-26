(() => {
    const TWO_PI = Math.PI * 2;

    const readNumber = (element, name, fallback) => {
        const raw = element.dataset[name];
        if (raw == null || raw === "") return fallback;
        const value = Number(raw);
        return Number.isFinite(value) ? value : fallback;
    };

    const readBoolean = (element, name, fallback) => {
        const raw = element.dataset[name];
        if (raw == null || raw === "") return fallback;
        return raw === "true" || raw === "1";
    };

    const mountDotField = (container) => {
        const canvas = document.createElement("canvas");
        canvas.className = "dot-field-canvas";
        canvas.setAttribute("aria-hidden", "true");

        const glowId = `dot-field-glow-${Math.random().toString(36).slice(2, 9)}`;
        const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        svg.classList.add("dot-field-glow");
        svg.setAttribute("aria-hidden", "true");

        const defs = document.createElementNS("http://www.w3.org/2000/svg", "defs");
        const gradient = document.createElementNS("http://www.w3.org/2000/svg", "radialGradient");
        gradient.setAttribute("id", glowId);
        const stopInner = document.createElementNS("http://www.w3.org/2000/svg", "stop");
        stopInner.setAttribute("offset", "0%");
        const stopOuter = document.createElementNS("http://www.w3.org/2000/svg", "stop");
        stopOuter.setAttribute("offset", "100%");
        stopOuter.setAttribute("stop-color", "transparent");
        gradient.append(stopInner, stopOuter);
        defs.append(gradient);

        const glow = document.createElementNS("http://www.w3.org/2000/svg", "circle");
        glow.setAttribute("cx", "-9999");
        glow.setAttribute("cy", "-9999");
        glow.setAttribute("fill", `url(#${glowId})`);
        glow.style.opacity = "0";
        glow.style.willChange = "opacity";
        svg.append(defs, glow);
        container.append(canvas, svg);

        const ctx = canvas.getContext("2d", { alpha: true });
        if (!ctx) return;

        const props = {
            dotRadius: readNumber(container, "dotRadius", 1.5),
            dotSpacing: readNumber(container, "dotSpacing", 14),
            cursorRadius: readNumber(container, "cursorRadius", 500),
            cursorForce: readNumber(container, "cursorForce", 0.1),
            bulgeOnly: readBoolean(container, "bulgeOnly", true),
            bulgeStrength: readNumber(container, "bulgeStrength", 67),
            sparkle: readBoolean(container, "sparkle", false),
            waveAmplitude: readNumber(container, "waveAmplitude", 0),
            dotDim: container.dataset.dotDim || "rgba(244, 244, 242, 0.16)",
            dotMid: container.dataset.dotMid || "rgba(244, 244, 242, 0.38)",
            dotHot: container.dataset.dotHot || "rgba(214, 255, 62, 0.95)",
        };

        glow.setAttribute("r", String(readNumber(container, "glowRadius", 160)));

        const dots = [];
        const mouse = { x: -9999, y: -9999, prevX: -9999, prevY: -9999, speed: 0 };
        const size = { w: 0, h: 0 };
        let glowOpacity = 0;
        let engagement = 0;
        let frameCount = 0;
        let rafId = 0;
        let resizeTimer = 0;
        let settleFrames = 0;
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

        const buildDots = (w, h) => {
            const step = props.dotRadius + props.dotSpacing;
            const cols = Math.max(0, Math.floor(w / step));
            const rows = Math.max(0, Math.floor(h / step));
            const padX = (w % step) / 2;
            const padY = (h % step) / 2;
            dots.length = 0;

            for (let row = 0; row < rows; row += 1) {
                for (let col = 0; col < cols; col += 1) {
                    const ax = padX + col * step + step / 2;
                    const ay = padY + row * step + step / 2;
                    dots.push({ ax, ay, sx: ax, sy: ay, vx: 0, vy: 0, x: ax, y: ay });
                }
            }
        };

        const draw = (time) => {
            const { w, h } = size;
            ctx.clearRect(0, 0, w, h);
            if (!w || !h || dots.length === 0) return;

            const reach = Math.max(props.cursorRadius, 1);
            const grad = ctx.createRadialGradient(mouse.x, mouse.y, 0, mouse.x, mouse.y, reach);
            grad.addColorStop(0, props.dotHot);
            grad.addColorStop(0.42, props.dotMid);
            grad.addColorStop(1, props.dotDim);
            ctx.fillStyle = grad;
            ctx.beginPath();

            const rad = props.dotRadius / 2;
            for (let i = 0; i < dots.length; i += 1) {
                const dot = dots[i];
                let drawX = dot.sx;
                let drawY = dot.sy;
                if (props.waveAmplitude > 0) {
                    drawY += Math.sin(dot.ax * 0.03 + time) * props.waveAmplitude;
                    drawX += Math.cos(dot.ay * 0.03 + time * 0.7) * props.waveAmplitude * 0.5;
                }

                let radius = rad;
                if (props.sparkle) {
                    const hash = ((i * 2654435761) ^ (frameCount >> 3)) >>> 0;
                    if (hash % 100 < 3) radius = rad * 1.8;
                }

                ctx.moveTo(drawX + radius, drawY);
                ctx.arc(drawX, drawY, radius, 0, TWO_PI);
            }

            ctx.fill();
        };

        const paintFrame = () => {
            frameCount += 1;
            const m = mouse;
            const { w, h } = size;
            const time = frameCount * 0.02;
            const targetEngagement = Math.min(m.speed / 5, 1);
            engagement += (targetEngagement - engagement) * 0.06;
            if (engagement < 0.001) engagement = 0;

            glowOpacity += (engagement - glowOpacity) * 0.08;
            glow.setAttribute("cx", String(m.x));
            glow.setAttribute("cy", String(m.y));
            glow.style.opacity = String(glowOpacity);

            const cr = props.cursorRadius;
            const crSq = cr * cr;
            const isBulge = props.bulgeOnly;
            let maxOffset = 0;

            for (let i = 0; i < dots.length; i += 1) {
                const dot = dots[i];
                const dx = m.x - dot.ax;
                const dy = m.y - dot.ay;
                const distSq = dx * dx + dy * dy;

                if (distSq < crSq && engagement > 0.01) {
                    const dist = Math.sqrt(distSq);
                    if (isBulge) {
                        const falloff = 1 - dist / cr;
                        const push = falloff * falloff * props.bulgeStrength * engagement;
                        const angle = Math.atan2(dy, dx);
                        dot.sx += (dot.ax - Math.cos(angle) * push - dot.sx) * 0.15;
                        dot.sy += (dot.ay - Math.sin(angle) * push - dot.sy) * 0.15;
                    } else {
                        const angle = Math.atan2(dy, dx);
                        const move = (500 / dist) * (m.speed * props.cursorForce);
                        dot.vx += Math.cos(angle) * -move;
                        dot.vy += Math.sin(angle) * -move;
                    }
                } else if (isBulge) {
                    dot.sx += (dot.ax - dot.sx) * 0.1;
                    dot.sy += (dot.ay - dot.sy) * 0.1;
                }

                if (!isBulge) {
                    dot.vx *= 0.9;
                    dot.vy *= 0.9;
                    dot.x = dot.ax + dot.vx;
                    dot.y = dot.ay + dot.vy;
                    dot.sx += (dot.x - dot.sx) * 0.1;
                    dot.sy += (dot.y - dot.sy) * 0.1;
                }

                maxOffset = Math.max(maxOffset, Math.abs(dot.sx - dot.ax), Math.abs(dot.sy - dot.ay));
            }

            if (!w || !h) return;
            draw(time);

            const idle = !props.sparkle
                && props.waveAmplitude <= 0
                && engagement < 0.001
                && glowOpacity < 0.01
                && maxOffset < 0.2;
            settleFrames = idle ? settleFrames + 1 : 0;
        };

        const tick = () => {
            paintFrame();
            if (settleFrames > 30) {
                rafId = 0;
                return;
            }
            rafId = requestAnimationFrame(tick);
        };

        const wake = () => {
            settleFrames = 0;
            if (reduceMotion || rafId) return;
            rafId = requestAnimationFrame(tick);
        };

        const doResize = () => {
            const rect = container.getBoundingClientRect();
            const w = rect.width;
            const h = rect.height;
            canvas.width = Math.max(1, Math.floor(w * dpr));
            canvas.height = Math.max(1, Math.floor(h * dpr));
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            size.w = w;
            size.h = h;
            buildDots(w, h);
            settleFrames = 0;
            if (reduceMotion) {
                draw(0);
                return;
            }
            wake();
        };

        const resize = () => {
            window.clearTimeout(resizeTimer);
            resizeTimer = window.setTimeout(doResize, 100);
        };

        const onMouseMove = (event) => {
            const rect = container.getBoundingClientRect();
            mouse.x = event.clientX - rect.left;
            mouse.y = event.clientY - rect.top;
            wake();
        };

        const updateMouseSpeed = () => {
            const dx = mouse.prevX - mouse.x;
            const dy = mouse.prevY - mouse.y;
            const dist = Math.sqrt(dx * dx + dy * dy);
            mouse.speed += (dist - mouse.speed) * 0.5;
            if (mouse.speed < 0.001) mouse.speed = 0;
            mouse.prevX = mouse.x;
            mouse.prevY = mouse.y;
        };

        doResize();
        window.addEventListener("resize", resize);

        let speedInterval = 0;
        if (!reduceMotion) {
            speedInterval = window.setInterval(updateMouseSpeed, 20);
            window.addEventListener("mousemove", onMouseMove, { passive: true });
            document.addEventListener("visibilitychange", () => {
                if (document.hidden) {
                    cancelAnimationFrame(rafId);
                    rafId = 0;
                    return;
                }
                wake();
            });
        }

        window.addEventListener("pagehide", () => {
            cancelAnimationFrame(rafId);
            window.clearInterval(speedInterval);
            window.clearTimeout(resizeTimer);
            window.removeEventListener("resize", resize);
            window.removeEventListener("mousemove", onMouseMove);
        }, { once: true });
    };

    const boot = () => {
        const container = document.getElementById("dotField");
        if (!container) return;
        mountDotField(container);
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", boot, { once: true });
    } else {
        boot();
    }
})();
