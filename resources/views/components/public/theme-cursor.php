<style>
@media(pointer:fine){body,body a,body button,body input,body textarea,body select,body label{cursor:none!important}}
</style>

<canvas data-theme-trail class="pointer-events-none fixed inset-0 z-[100] h-full w-full" aria-hidden="true"></canvas>
<div data-theme-cursor class="pointer-events-none fixed left-0 top-0 z-[101] hidden will-change-transform" aria-hidden="true">
    <svg class="absolute left-0 top-0 z-30 h-5.5 w-5.5 drop-shadow-[0_2px_4px_rgba(16,46,80,.35)]" viewBox="0 0 24 24">
        <path d="M2.7 2.2 20 13.4l-7.2 1.35-4.1 7.05-6-19.6Z" fill="#102E50" stroke="#FFFFFF" stroke-width="1.5" stroke-linejoin="round"/>
    </svg>
</div>

<script>
(() => {
    if (matchMedia('(pointer:coarse)').matches) return;
    const canvas = document.querySelector('[data-theme-trail]');
    const cursor = document.querySelector('[data-theme-cursor]');
    if (!canvas || !cursor) return;
    const context = canvas.getContext('2d');
    const trail = [];
    const particles = [];
    let width = 0, height = 0, ratio = 1;

    function resize() {
        ratio = Math.min(devicePixelRatio || 1, 2);
        width = innerWidth; height = innerHeight;
        canvas.width = width * ratio; canvas.height = height * ratio;
        context.setTransform(ratio, 0, 0, ratio, 0, 0);
    }

    function move(event) {
        cursor.classList.remove('hidden');
        const interactive = event.target.closest('a,button,label,input,select,textarea');
        cursor.style.transform = `translate3d(${event.clientX}px,${event.clientY}px,0) scale(${interactive ? 1.2 : 1})`;
        
        // Add coordinate to trailing line
        trail.push({
            x: event.clientX,
            y: event.clientY,
            life: 1.0
        });
        if (trail.length > 15) {
            trail.shift();
        }

        // Spawn spray particles
        const count = 3;
        for (let i = 0; i < count; i++) {
            const angle = Math.random() * Math.PI * 2;
            const speed = Math.random() * 2.2 + 0.6;
            particles.push({
                x: event.clientX,
                y: event.clientY,
                vx: Math.cos(angle) * speed,
                vy: Math.sin(angle) * speed,
                size: Math.random() * 2.4 + 1.2,
                life: 1.0,
                decay: Math.random() * 0.04 + 0.03
            });
        }
    }

    function draw() {
        context.clearRect(0, 0, width, height);
        
        // Age the trailing line
        for (let index = trail.length - 1; index >= 0; index--) {
            trail[index].life -= 0.06;
            if (trail[index].life <= 0) { 
                trail.splice(index, 1); 
            }
        }

        // Age and move spray particles
        for (let index = particles.length - 1; index >= 0; index--) {
            const p = particles[index];
            p.x += p.vx;
            p.y += p.vy;
            p.life -= p.decay;
            if (p.life <= 0) {
                particles.splice(index, 1);
            }
        }
        
        // Draw trailing solid line
        if (trail.length > 1) {
            context.lineCap = 'round';
            context.lineJoin = 'round';
            for (let i = 1; i < trail.length; i++) {
                const p1 = trail[i - 1];
                const p2 = trail[i];
                context.beginPath();
                context.strokeStyle = `rgba(16, 46, 80, ${p2.life * 0.6})`;
                context.lineWidth = 3.0 * p2.life;
                context.moveTo(p1.x, p1.y);
                context.lineTo(p2.x, p2.y);
                context.stroke();
            }
        }

        // Draw spray particles
        for (let i = 0; i < particles.length; i++) {
            const p = particles[i];
            context.beginPath();
            context.fillStyle = `rgba(16, 46, 80, ${p.life * 0.75})`;
            context.arc(p.x, p.y, p.size * p.life, 0, Math.PI * 2);
            context.fill();
        }
        
        requestAnimationFrame(draw);
    }

    addEventListener('resize', resize, {passive:true});
    addEventListener('pointermove', move, {passive:true});
    document.documentElement.addEventListener('mouseleave', () => cursor.classList.add('hidden'));
    resize(); draw();
})();
</script>
