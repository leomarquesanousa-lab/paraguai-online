(() => {
 'use strict';
 const HOLD=5000,FADE=1200,motion=matchMedia('(prefers-reduced-motion: reduce)');
 document.querySelectorAll('[data-tech-hero-crossfade]').forEach(hero=>{
  if(hero.poHeroCrossfadeController)return;
  let images;try{images=JSON.parse(hero.dataset.techHeroCrossfade);}catch{return;}
  if(!Array.isArray(images)||(images=[...new Set(images.filter(x=>typeof x==='string'&&x))]).length<2)return;
  hero.poHeroCrossfadeController=true;
  let epoch=0,timer=null,layers=[],resizeFrame,current=0,pending=null,phase='idle';const cache=new Map();
  const load=url=>{if(!cache.has(url))cache.set(url,new Promise(resolve=>{const image=new Image();image.onload=async()=>{try{if(image.decode)await image.decode();resolve(image.naturalWidth>0);}catch{resolve(false);}};image.onerror=()=>resolve(false);image.src=url;}));return cache.get(url);};
  const background=(layer,url)=>{layer.dataset.imageUrl=url;const base=getComputedStyle(hero).backgroundImage;layer.style.backgroundImage=base.replace(/url\((?:"(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*'|[^)])*\)\s*$/,'url('+JSON.stringify(url)+')');};
  // Flush each reset before another opacity transition. Never animate the opaque base.
  const commit=layer=>{void getComputedStyle(layer).opacity;};
  const finish=()=>{
   if(pending===null||!layers.length)return;
   const [base,overlay]=layers;
   overlay.style.transition='none';overlay.style.opacity='1';commit(overlay);
   background(base,images[pending]);current=pending;pending=null;
   overlay.style.opacity='0';commit(overlay);phase='idle';
  };
  const pause=()=>{epoch++;clearTimeout(timer);timer=null;cancelAnimationFrame(resizeFrame);finish();};
  const schedule=(fn,delay)=>{clearTimeout(timer);timer=setTimeout(()=>{timer=null;fn();},delay);};
  function hold(token){
   if(token!==epoch||motion.matches||document.hidden)return;
   phase='holding';load(images[(current+1)%images.length]);schedule(()=>tick(token),HOLD);
  }
  async function tick(token){
   if(token!==epoch||phase!=='holding')return;phase='loading';
   let next=null;
   for(let step=1;step<images.length;step++){
    const candidate=(current+step)%images.length;const ready=await load(images[candidate]);
    if(token!==epoch||document.hidden||motion.matches)return;
    if(ready){next=candidate;break;}
   }
   if(next===null){hold(token);return;}
   const overlay=layers[1];pending=next;background(overlay,images[next]);
   overlay.style.transition='none';overlay.style.opacity='0';commit(overlay);
   overlay.style.transition='opacity '+FADE+'ms ease-in-out';overlay.style.opacity='1';commit(overlay);phase='fading';
   schedule(()=>{if(token!==epoch)return;finish();hold(token);},FADE);
  }
  async function start(){
   pause();if(motion.matches){layers.forEach(layer=>layer.remove());layers=[];current=0;phase='idle';return;}
   if(document.hidden)return;const token=epoch;
   if(!layers.length){
    if(!await load(images[current])||token!==epoch||document.hidden||motion.matches)return;
    layers=[document.createElement('div'),document.createElement('div')];
    layers.forEach((layer,index)=>{layer.className='po-hero-crossfade-layer';layer.setAttribute('aria-hidden','true');layer.style.transition='none';layer.style.zIndex=String(index);layer.style.opacity=index?'0':'1';});
    background(layers[0],images[current]);background(layers[1],images[current]);hero.prepend(...layers);commit(layers[1]);
   }
   hold(token);
  }
  const resize=()=>{cancelAnimationFrame(resizeFrame);resizeFrame=requestAnimationFrame(()=>layers.forEach(layer=>{if(layer.dataset.imageUrl)background(layer,layer.dataset.imageUrl);}));};
  motion.addEventListener('change',start);document.addEventListener('visibilitychange',start);window.addEventListener('resize',resize,{passive:true});start();
 });
})();
