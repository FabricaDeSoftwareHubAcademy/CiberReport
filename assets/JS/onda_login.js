(function () {
    const canvas = document.getElementById('wave-divider');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const geral = document.querySelector('.geral');
    const ladoEsq = document.querySelector('.lado-esq');

    let width, height, boundaryX;
    let phase = 0;

    const BAND_WIDTH = 45;     // alcance horizontal da onda a partir da borda
    const AMPLITUDE = 14;      // altura da ondulação
    const FREQUENCY = 0.012;   // "aperto" da onda no eixo Y
    const SPEED = 0.035;       // velocidade do loop

    const COLOR_LEFT = '#0d1b3d';
    const COLOR_RIGHT = '#f4f4f4';
    const COLOR_GLOW = 'rgba(45, 212, 191, 0.55)'; // teal do logo

    function resize() {
        const rect = geral.getBoundingClientRect();
        width = rect.width;
        height = rect.height;

        const dpr = window.devicePixelRatio || 1;
        canvas.width = width * dpr;
        canvas.height = height * dpr;
        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        boundaryX = ladoEsq.getBoundingClientRect().right - rect.left;
    }

    function buildPoints(amplitude, freq, offsetPhase, step = 6) {
        const points = [];
        for (let y = 0; y <= height; y += step) {
            const x = boundaryX + amplitude * Math.sin(y * freq + offsetPhase);
            points.push({ x, y });
        }
        // garante que o último ponto bata exatamente no fim
        points.push({
            x: boundaryX + amplitude * Math.sin(height * freq + offsetPhase),
            y: height
        });
        return points;
    }

    function fillSide(points, side, color) {
        const edgeX = side === 'left' ? boundaryX - BAND_WIDTH : boundaryX + BAND_WIDTH;

        ctx.beginPath();
        ctx.moveTo(points[0].x, 0);
        points.forEach(p => ctx.lineTo(p.x, p.y));
        ctx.lineTo(edgeX, height);
        ctx.lineTo(edgeX, 0);
        ctx.closePath();
        ctx.fillStyle = color;
        ctx.fill();
    }

    function draw() {
        ctx.clearRect(0, 0, width, height);

        const main = buildPoints(AMPLITUDE, FREQUENCY, phase);
        fillSide(main, 'left', COLOR_LEFT);
        fillSide(main, 'right', COLOR_RIGHT);

        // linha de brilho teal seguindo a crista da onda
        const glow = buildPoints(AMPLITUDE, FREQUENCY, phase, 4);
        ctx.beginPath();
        ctx.moveTo(glow[0].x, glow[0].y);
        glow.forEach(p => ctx.lineTo(p.x, p.y));
        ctx.strokeStyle = COLOR_GLOW;
        ctx.lineWidth = 2;
        ctx.shadowColor = COLOR_GLOW;
        ctx.shadowBlur = 8;
        ctx.stroke();
        ctx.shadowBlur = 0;

        phase += SPEED;
        requestAnimationFrame(draw);
    }

    window.addEventListener('resize', resize);
    resize();
    draw();
})();