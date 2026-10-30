(() => {
 'use strict';
 const header=document.querySelector('.tech-header'),nav=document.querySelector('#po-main-menu'),toggle=document.querySelector('.po-menu-toggle');
 if(!header||!nav||!toggle)return;
 header.classList.add('po-nav-enhanced');toggle.hidden=false;
 const mobile=matchMedia('(max-width:767px)'),hover=matchMedia('(hover:hover) and (pointer:fine)');
 const close=()=>{nav.querySelectorAll('details[open]').forEach(d=>d.open=false);header.classList.remove('po-nav-open');toggle.setAttribute('aria-expanded','false');};
 toggle.addEventListener('click',()=>{const open=!header.classList.contains('po-nav-open');if(!open)close();else{header.classList.add('po-nav-open');toggle.setAttribute('aria-expanded','true');}});
 nav.querySelectorAll('details').forEach(d=>d.addEventListener('toggle',()=>{d.querySelector(':scope > summary')?.setAttribute('aria-expanded',String(d.open));if(d.open&&d.classList.contains('po-nav-dropdown'))nav.querySelectorAll('.po-nav-dropdown').forEach(other=>{if(other!==d)other.open=false;});}));
 nav.querySelectorAll('.po-nav-dropdown').forEach(d=>{d.addEventListener('mouseenter',()=>{if(!mobile.matches&&hover.matches)d.open=true;});d.addEventListener('mouseleave',()=>{if(!mobile.matches&&hover.matches&&!d.contains(document.activeElement))d.open=false;});});
 document.addEventListener('pointerdown',e=>{if(!nav.contains(e.target)&&!toggle.contains(e.target))close();});
 document.addEventListener('keydown',e=>{if(e.key!=='Escape')return;const open=[...nav.querySelectorAll('details[open]')].pop();if(open){open.open=false;open.querySelector(':scope > summary')?.focus();e.preventDefault();}else if(header.classList.contains('po-nav-open')){close();toggle.focus();e.preventDefault();}});
 nav.addEventListener('focusout',()=>setTimeout(()=>{if(!nav.contains(document.activeElement)&&!toggle.contains(document.activeElement))close();},0));
 mobile.addEventListener('change',close);
})();