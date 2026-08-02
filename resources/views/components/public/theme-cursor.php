<style>
@media(pointer:fine){body,body a,body button,body input,body textarea,body select,body label{cursor:none!important}}
@keyframes gift-cursor-float{0%,100%{transform:translateY(0) rotate(-3deg)}50%{transform:translateY(-2px) rotate(3deg)}}
@keyframes gift-cursor-ring{0%,100%{transform:scale(.9);opacity:.45}50%{transform:scale(1.16);opacity:.08}}
[data-theme-cursor] [data-cursor-gift]{animation:gift-cursor-float 1.2s ease-in-out infinite}
[data-theme-cursor] [data-cursor-ring]{animation:gift-cursor-ring 1.35s ease-in-out infinite}
@media(prefers-reduced-motion:reduce){[data-theme-cursor] [data-cursor-gift],[data-theme-cursor] [data-cursor-ring]{animation:none}}
</style>
<canvas data-theme-spray class="pointer-events-none fixed inset-0 z-[100] h-full w-full" aria-hidden="true"></canvas>
<div data-theme-cursor class="pointer-events-none fixed left-0 top-0 z-[101] hidden will-change-transform" aria-hidden="true">
    <svg class="absolute left-0 top-0 z-20 h-5 w-5 drop-shadow-sm" viewBox="0 0 24 24" fill="none"><path d="M3 2.5 19 13l-7.2 1.35L8 21.5 3 2.5Z" fill="white" stroke="#0B182E" stroke-width="1.8" stroke-linejoin="round"/></svg>
    <span data-cursor-ring class="absolute left-3 top-3 h-12 w-12 rounded-full border-2 border-blue-300 bg-blue-400/15"></span>
    <span class="absolute left-3 top-3 flex h-12 w-12 items-center justify-center rounded-full border border-white/50 bg-[#0B182E] shadow-[0_8px_24px_rgba(11,24,46,.38)]">
        <img data-cursor-gift src="/assets/images/giftvibe-mark.svg" alt="" class="h-8 w-8 rounded-full object-cover ring-2 ring-white/80">
    </span>
</div>
<script>
(()=>{if(matchMedia('(pointer:coarse)').matches)return;const c=document.querySelector('[data-theme-spray]'),cursor=document.querySelector('[data-theme-cursor]');if(!c||!cursor)return;const x=c.getContext('2d'),dots=[];let w=0,h=0,dpr=1,last=0;function size(){dpr=Math.min(devicePixelRatio||1,2);w=innerWidth;h=innerHeight;c.width=w*dpr;c.height=h*dpr;x.setTransform(dpr,0,0,dpr,0,0)}function move(e){cursor.classList.remove('hidden');cursor.style.transform=`translate3d(${e.clientX}px,${e.clientY}px,0) scale(${e.target.closest('a,button,label')?1.1:1})`;const now=performance.now();if(now-last<18)return;last=now;for(let i=0;i<4;i++){const a=Math.random()*Math.PI*2,s=1+Math.random()*1.4;dots.push({x:e.clientX+36+(Math.random()-.5)*26,y:e.clientY+36+(Math.random()-.5)*26,vx:Math.cos(a)*s,vy:Math.sin(a)*s,r:1+Math.random()*1.7,life:1,t:Math.random()>.3})}}function draw(){x.clearRect(0,0,w,h);for(let i=dots.length-1;i>=0;i--){const p=dots[i];p.x+=p.vx;p.y+=p.vy;p.vx*=.97;p.vy*=.97;p.life-=.03;if(p.life<=0){dots.splice(i,1);continue}x.beginPath();x.fillStyle=p.t?`rgba(30,64,175,${p.life*.72})`:`rgba(148,191,255,${p.life*.82})`;x.shadowColor='rgba(59,130,246,.65)';x.shadowBlur=6;x.arc(p.x,p.y,p.r,0,Math.PI*2);x.fill()}requestAnimationFrame(draw)}addEventListener('resize',size,{passive:true});addEventListener('pointermove',move,{passive:true});document.documentElement.addEventListener('mouseleave',()=>cursor.classList.add('hidden'));size();draw()})();
</script>
