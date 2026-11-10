const ridgesCanvas = document.querySelector('[data-ridges-canvas]');
const cursorCanvas = document.querySelector('[data-arc-cursor]');

if (ridgesCanvas instanceof HTMLCanvasElement && cursorCanvas instanceof HTMLCanvasElement) {
    const ridgesContext = ridgesCanvas.getContext('2d');
    const cursorContext = cursorCanvas.getContext('2d');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const pointerFine = window.matchMedia('(pointer: fine)').matches;
    const pointerTrail = [];
    let width = 0;
    let height = 0;
    let pixelRatio = 1;
    let animationFrame = 0;

    const resize = () => {
        width = window.innerWidth;
        height = window.innerHeight;
        pixelRatio = Math.min(window.devicePixelRatio || 1, 2);

        [ridgesCanvas, cursorCanvas].forEach((canvas) => {
            canvas.width = Math.round(width * pixelRatio);
            canvas.height = Math.round(height * pixelRatio);
            canvas.style.width = `${width}px`;
            canvas.style.height = `${height}px`;
        });
    };

    const drawRidges = (time) => {
        if (!ridgesContext) return;

        ridgesContext.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
        ridgesContext.clearRect(0, 0, width, height);
        ridgesContext.globalCompositeOperation = 'screen';

        const colors = [
            [45, 212, 191],
            [99, 102, 241],
            [167, 139, 250],
            [56, 189, 248],
        ];
        const phase = reducedMotion ? 0 : time * 0.00018;
        const spacing = Math.max(34, height * 0.075);

        colors.forEach((color, layer) => {
            const baseY = height * (0.28 + layer * 0.12);
            const amplitude = height * (0.035 + layer * 0.006);
            const phaseOffset = layer * 0.8;

            ridgesContext.beginPath();
            for (let x = -24; x <= width + 24; x += 16) {
                const wave = Math.sin(x * 0.0032 + phase + phaseOffset) * amplitude
                    + Math.sin(x * 0.0014 - phase * 0.72 + phaseOffset) * amplitude * 0.55;
                const y = baseY + wave;

                if (x === -24) {
                    ridgesContext.moveTo(x, y);
                } else {
                    ridgesContext.lineTo(x, y);
                }
            }

            const glow = ridgesContext.createLinearGradient(0, baseY - spacing, width, baseY + spacing);
            glow.addColorStop(0, `rgba(${color.join(', ')}, 0)`);
            glow.addColorStop(0.5, `rgba(${color.join(', ')}, ${0.22 - layer * 0.025})`);
            glow.addColorStop(1, `rgba(${color.join(', ')}, 0)`);
            ridgesContext.strokeStyle = glow;
            ridgesContext.lineWidth = 2.6;
            ridgesContext.shadowColor = `rgba(${color.join(', ')}, 0.8)`;
            ridgesContext.shadowBlur = 36;
            ridgesContext.stroke();
        });

        ridgesContext.globalCompositeOperation = 'source-over';
        ridgesContext.shadowBlur = 0;
    };

    const drawCursorArc = (time) => {
        if (!cursorContext) return;

        cursorContext.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
        cursorContext.clearRect(0, 0, width, height);

        const activeTrail = pointerTrail.filter((point) => time - point.time < 360);
        pointerTrail.splice(0, pointerTrail.length, ...activeTrail);

        if (activeTrail.length < 2) return;

        for (let index = 1; index < activeTrail.length; index += 1) {
            const from = activeTrail[index - 1];
            const to = activeTrail[index];
            const age = Math.max(0, time - to.time);
            const opacity = Math.max(0, 1 - age / 360) * (index / activeTrail.length);
            const distance = Math.hypot(to.x - from.x, to.y - from.y);
            const steps = Math.max(1, Math.ceil(distance / 8));

            cursorContext.beginPath();
            cursorContext.moveTo(from.x, from.y);

            for (let step = 1; step <= steps; step += 1) {
                const progress = step / steps;
                const jitter = step === steps ? 0 : Math.sin(index * 17 + step * 9) * 5;
                cursorContext.lineTo(
                    from.x + (to.x - from.x) * progress + jitter,
                    from.y + (to.y - from.y) * progress - jitter * 0.7,
                );
            }

            cursorContext.strokeStyle = `rgba(125, 211, 252, ${opacity * 0.85})`;
            cursorContext.lineWidth = 1.5;
            cursorContext.shadowColor = `rgba(56, 189, 248, ${opacity})`;
            cursorContext.shadowBlur = 12;
            cursorContext.stroke();
        }

        const head = activeTrail[activeTrail.length - 1];
        const headAge = Math.max(0, time - head.time);
        const headOpacity = Math.max(0, 1 - headAge / 240);
        cursorContext.beginPath();
        cursorContext.arc(head.x, head.y, 2.2, 0, Math.PI * 2);
        cursorContext.fillStyle = `rgba(224, 242, 254, ${headOpacity})`;
        cursorContext.shadowColor = 'rgba(56, 189, 248, 0.95)';
        cursorContext.shadowBlur = 16;
        cursorContext.fill();
        cursorContext.shadowBlur = 0;
    };

    const animate = (time) => {
        drawRidges(time);
        drawCursorArc(time);

        if (!reducedMotion || pointerTrail.length > 0) {
            animationFrame = window.requestAnimationFrame(animate);
        }
    };

    resize();
    drawRidges(0);

    if (!reducedMotion) {
        animationFrame = window.requestAnimationFrame(animate);
    }

    if (pointerFine && !reducedMotion) {
        window.addEventListener('pointermove', (event) => {
            if (event.pointerType === 'touch') return;

            pointerTrail.push({ x: event.clientX, y: event.clientY, time: performance.now() });
            if (pointerTrail.length > 26) pointerTrail.shift();

            if (!animationFrame) {
                animationFrame = window.requestAnimationFrame(animate);
            }
        }, { passive: true });
    }

    window.addEventListener('resize', () => {
        resize();
        drawRidges(performance.now());
    }, { passive: true });
}
