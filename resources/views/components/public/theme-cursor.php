<style>
@media(pointer:fine){body,body a,body button,body input,body textarea,body select,body label{cursor:none!important}}
@keyframes gv-cursor-orbit{to{transform:rotate(360deg)}}
@keyframes gv-cursor-pulse{0%,100%{transform:scale(.82);opacity:.45}50%{transform:scale(1.08);opacity:.12}}
[data-cursor-orbit]{animation:gv-cursor-orbit 3.2s linear infinite}
[data-cursor-aura]{animation:gv-cursor-pulse 1.6s ease-in-out infinite}
@media(prefers-reduced-motion:reduce){[data-cursor-orbit],[data-cursor-aura]{animation:none}}
</style>

<canvas data-theme-trail class="pointer-events-none fixed inset-0 z-[100] h-full w-full" aria-hidden="true"></canvas>
<div data-theme-cursor class="pointer-events-none fixed left-0 top-0 z-[101] hidden will-change-transform" aria-hidden="true">
    <svg class="absolute left-0 top-0 z-30 h-5 w-5 drop-shadow-[0_2px_3px_rgba(16,46,80,.35)]" viewBox="0 0 24 24">
        <path d="M2.7 2.2 20 13.4l-7.2 1.35-4.1 7.05-6-19.6Z" fill="#2563EB" stroke="none"/>
    </svg>
    <span data-cursor-aura class="absolute left-3 top-3 h-11 w-11 rounded-full border-2 border-primary/70 bg-primary/25"></span>
    <span class="absolute left-[15px] top-[15px] grid h-9 w-9 place-items-center rounded-full border-2 border-primary/20 bg-white shadow-[0_10px_30px_rgba(37,99,235,.30)]">
        <span class="text-[10px] font-black tracking-[-.04em] text-primary">GV</span>
        <span data-cursor-orbit class="absolute inset-[-5px] rounded-full border-2 border-dashed border-primary">
            <span class="absolute -right-0.5 top-1 h-2.5 w-2.5 rounded-full border-2 border-white bg-primary shadow-sm"></span>
        </span>
    </span>
</div>

<script>
(() => {
    if (matchMedia('(pointer:coarse)').matches) return;
    const canvas = document.querySelector('[data-theme-trail]');
    const cursor = document.querySelector('[data-theme-cursor]');
    if (!canvas || !cursor) return;
    const context = canvas.getContext('2d');
    const trail = [];
    let width = 0, height = 0, ratio = 1, last = 0;

    function resize() {
        ratio = Math.min(devicePixelRatio || 1, 2);
        width = innerWidth; height = innerHeight;
        canvas.width = width * ratio; canvas.height = height * ratio;
        context.setTransform(ratio, 0, 0, ratio, 0, 0);
    }

    function move(event) {
        cursor.classList.remove('hidden');
        const interactive = event.target.closest('a,button,label,input,select,textarea');
        cursor.style.transform = `translate3d(${event.clientX}px,${event.clientY}px,0) scale(${interactive ? 1.12 : 1})`;
        const now = performance.now();
        if (now - last < 28) return;
        last = now;
        trail.push({x:event.clientX + 25,y:event.clientY + 25,r:interactive ? 2.8 : 2.1,life:1,color:'37,99,235'});
    }

    function draw() {
        context.clearRect(0, 0, width, height);
        for (let index = trail.length - 1; index >= 0; index--) {
            const point = trail[index];
            point.life -= .045; point.r += .025;
            if (point.life <= 0) { trail.splice(index, 1); continue; }
            context.beginPath();
            context.fillStyle = `rgba(${point.color},${point.life * .82})`;
            context.shadowColor = 'rgba(37,99,235,.55)';
            context.shadowBlur = 7;
            context.arc(point.x, point.y, point.r, 0, Math.PI * 2);
            context.fill();
            context.shadowBlur = 0;
        }
        requestAnimationFrame(draw);
    }

    addEventListener('resize', resize, {passive:true});
    addEventListener('pointermove', move, {passive:true});
    document.documentElement.addEventListener('mouseleave', () => cursor.classList.add('hidden'));
    resize(); draw();
})();
</script>
