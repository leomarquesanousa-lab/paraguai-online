/* Native frontend controls. No dependencies and no commerce simulation. */
(() => {
  'use strict';
  const library = document.querySelector('#mp-library');
  let activeMedia = null;
  const ids = picker => picker.querySelector('input[type=hidden]').value.split(',').filter(Boolean);
  const message = (picker, text) => { picker.querySelector('.mp-media-message').textContent = text; };
  function mediaInfo(picker) {
    const label = picker.querySelector('.mp-media-current');
    if (label) label.textContent = ids(picker).length ? 'Imagem selecionada: #' + ids(picker).join(', #') : 'Nenhuma imagem selecionada';
    const open = picker.querySelector('[data-library]');
    if (open && picker.dataset.multiple !== '1') open.textContent = ids(picker).length ? 'Substituir imagem' : 'Abrir biblioteca';
  }
  function syncMediaOrder(picker) {
    if (picker.dataset.reorderable !== '1') return;
    const entries = [...picker.querySelectorAll('.mp-media-entry')];
    picker.querySelector('input[type=hidden]').value = entries.map(entry => entry.querySelector('.mp-image').dataset.id).join(',');
    entries.forEach((entry, index) => { const number = entry.querySelector('.mp-banner-position'); if (number) number.textContent = 'Banner ' + (index + 2); entry.querySelector('[data-move-media="-1"]').disabled = index === 0; entry.querySelector('[data-move-media="1"]').disabled = index === entries.length - 1; });
  }
  function orderedMedia(picker, image) {
    if (picker.dataset.reorderable !== '1') return; if (image.closest('.mp-media-entry')) { syncMediaOrder(picker); return; }
    const entry = document.createElement('div'); entry.className = 'mp-media-entry';
    image.before(entry); entry.append(image);
    if (picker.dataset.galleryContext === 'hero-gallery') { const position = document.createElement('strong'); position.className = 'mp-banner-position'; entry.prepend(position); const name = document.createElement('span'); name.className = 'mp-banner-name'; name.textContent = (image.dataset.title || 'Imagem') + ' (#' + image.dataset.id + ')'; entry.append(name); }
    const controls = document.createElement('div'); controls.className = 'mp-media-order';
    for (const [direction, label, text] of [[-1, 'Mover imagem para antes', '←'], [1, 'Mover imagem para depois', '→']]) {
      const move = document.createElement('button'); move.type = 'button'; move.className = 'mp-secondary'; move.dataset.moveMedia = direction; move.setAttribute('aria-label', label); move.title = label; move.textContent = text; controls.append(move);
    }
    entry.append(controls); syncMediaOrder(picker);
  }
  function addImage(picker, id, url, title = '') {
    const hero = picker.closest('.po-hero-banners');
    if (hero && picker.dataset.galleryContext === 'hero-gallery' && String(id) === hero.dataset.originalId) { message(picker, 'O Banner 1 já é o original. Escolha outra imagem para adicionar.'); return; }
    const values = picker.dataset.multiple === '1' ? ids(picker) : [];
    if (values.includes(String(id))) return;
    if (values.length >= 20) { message(picker, 'Limite de 20 imagens por seleção.'); return; }
    if (picker.dataset.multiple !== '1') picker.querySelector('.mp-media-preview').replaceChildren();
    values.push(String(id)); picker.querySelector('input[type=hidden]').value = values.join(',');
    const button = document.createElement('button'); button.type = 'button'; button.className = 'mp-image'; button.dataset.id = id; button.dataset.title = title; button.setAttribute('aria-label', 'Remover imagem selecionada');
    const image = document.createElement('img'); image.src = url; image.alt = 'Imagem selecionada';
    const label = document.createElement('span'); label.textContent = 'Remover'; button.append(image, label); picker.querySelector('.mp-media-preview').append(button); orderedMedia(picker, button); mediaInfo(picker);
  }

  async function selectHero(picker,id,url,title) {
    if(picker.dataset.galleryContext !== 'hero-gallery') { addImage(picker,id,url,title); return; }
    const section=picker.closest('.po-hero-banners');
    if(String(id)===section.dataset.originalId){message(picker,'O Banner 1 já é o original.');return;}
    const request=async fields=>{const data=new FormData();data.append('panel_action','hero_adapt');data.append('_wpnonce',section.dataset.adaptNonce);for(const [key,value] of Object.entries(fields))data.append(key,value);const response=await fetch(document.body.dataset.uploadUrl,{method:'POST',body:data,credentials:'same-origin'});const result=await response.json();if(!response.ok||!result.success)throw new Error(result.data?.message||'Falha ao adaptar imagem.');return result.data;};
    try {
      const info=await request({source:id,mode:'info'});
      if(info.width===1896&&info.height===829){addImage(picker,id,info.url,info.title);return;}
      const dialog=document.createElement('dialog');dialog.className='po-hero-crop';
      dialog.innerHTML='<h2>Prévia do banner — 1896 × 829 px</h2><p>Será criada uma cópia. A imagem original permanece intacta.</p><div class="po-crop-preview"><img alt="Prévia do recorte"></div><label>Enquadramento horizontal<input type="range" min="0" max="100" value="50" data-axis="x"></label><label>Enquadramento vertical<input type="range" min="0" max="100" value="50" data-axis="y"></label><p class="po-crop-error" role="status"></p><button type="button" data-crop-confirm>Criar cópia e adicionar banner</button> <button type="button" data-crop-cancel>Cancelar</button>';
      document.body.append(dialog);const image=dialog.querySelector('img');image.src=info.url;
      const horizontal=dialog.querySelector('[data-axis="x"]'),vertical=dialog.querySelector('[data-axis="y"]');
      const update=()=>{image.style.objectPosition=horizontal.value+'% '+vertical.value+'%';};horizontal.oninput=update;vertical.oninput=update;update();
      dialog.addEventListener('close',()=>dialog.remove(),{once:true});dialog.querySelector('[data-crop-cancel]').onclick=()=>dialog.close();
      const button=dialog.querySelector('[data-crop-confirm]');button.disabled=true;image.onload=()=>{button.disabled=false;};image.onerror=()=>{dialog.querySelector('.po-crop-error').textContent='Não foi possível carregar a prévia. Nenhuma cópia foi criada.';};
      button.onclick=async()=>{button.disabled=true;try{const copy=await request({source:id,mode:'create',x:horizontal.value,y:vertical.value});addImage(picker,copy.id,copy.url,copy.title);message(picker,'Cópia criada. Salve alterações para associar o banner. Para substituir, remova a seleção anterior e adicione outra imagem. A remoção não apaga anexos.');dialog.close();}catch(error){dialog.querySelector('.po-crop-error').textContent=error.message;button.disabled=false;}};
      await new Promise(resolve => { dialog.addEventListener('close', resolve, {once:true}); dialog.showModal(); });
    } catch(error){message(picker,error.message);}
  }
  document.addEventListener('click', event => {
    const move = event.target.closest('[data-move-media]');
    if (move) {
      const picker = move.closest('.mp-media'), entry = move.closest('.mp-media-entry'), preview = entry.parentElement, entries = [...preview.children], index = entries.indexOf(entry), next = index + Number(move.dataset.moveMedia);
      if (next >= 0 && next < entries.length) { preview.insertBefore(entry, next < index ? entries[next] : entries[next].nextSibling); syncMediaOrder(picker); message(picker, 'Ordem atualizada. Salve o formulário para aplicar.'); (move.disabled ? entry.querySelector('[data-move-media]:not(:disabled)') || entry.querySelector('.mp-image') : move).focus(); }
      return;
    }
    const image = event.target.closest('.mp-image');
    if (image) { const picker = image.closest('.mp-media'); picker.querySelector('input[type=hidden]').value = ids(picker).filter(id => id !== image.dataset.id).join(','); (image.closest('.mp-media-entry') || image).remove(); syncMediaOrder(picker); mediaInfo(picker); }
    const open = event.target.closest('[data-library]');
    if (open && library) { activeMedia = open.closest('.mp-media'); library.showModal(); }
    if (event.target.closest('[data-close-library]') && library) library.close();
    const choice = event.target.closest('[data-select-image]');
    if (choice && activeMedia) { selectHero(activeMedia, choice.dataset.selectImage, activeMedia.dataset.previewSize === 'medium_large' ? (choice.dataset.imagePreviewUrl || choice.dataset.imageUrl) : choice.dataset.imageUrl, choice.querySelector('span')?.textContent || ''); library.close(); }
    if (event.target.closest('[data-remove-variation]')) event.target.closest('.mp-variation').remove();
    const add = event.target.closest('#mp-add-variation');
    if (add) {
      const list = document.querySelector('#mp-variations');
      if (list.children.length >= 50) { alert('Limite de 50 variações por edição.'); return; }
      const fragment = document.querySelector('#mp-variation-template').content.cloneNode(true); const index = add.dataset.index;
      fragment.querySelectorAll('[name]').forEach(element => { element.name = element.name.replaceAll('__INDEX__', index); });
      list.append(fragment); add.dataset.index = Number(index) + 1;
    }
  });
  document.addEventListener('change', async event => {
    if (!event.target.matches('.mp-media input[type=file]')) return;
    const picker = event.target.closest('.mp-media'); const files = Array.from(event.target.files);
    for (const file of files) {
      if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 5 * 1024 * 1024) { message(picker, 'Use JPEG, PNG ou WebP de até 5 MB.'); continue; }
      const data = new FormData(); data.append('panel_action', 'upload'); data.append('_wpnonce', document.body.dataset.uploadNonce); data.append('image', file);
      message(picker, 'Enviando ' + file.name + '…'); event.target.disabled = true;
      try {
        const response = await fetch(document.body.dataset.uploadUrl, { method: 'POST', body: data, credentials: 'same-origin' });
        if (!response.headers.get('content-type')?.includes('application/json')) throw new Error('Sessão expirada ou envio indisponível. Entre novamente.');
        const result = await response.json(); if (!response.ok || !result.success) throw new Error(result.data?.message || 'Não foi possível enviar a imagem.');
        await selectHero(picker, result.data.id, picker.dataset.previewSize === 'medium_large' ? (result.data.preview_url || result.data.url) : result.data.url, file.name); message(picker, 'Imagem enviada. Salve o formulário para associar a imagem selecionada.');
      } catch (error) { message(picker, error.message); } finally { event.target.disabled = false; }
    }
    event.target.value = '';
  });
  document.querySelectorAll('.mp-media[data-reorderable="1"] .mp-image').forEach(image => orderedMedia(image.closest('.mp-media'), image));
  document.addEventListener('submit', event => {
    if (event.target.dataset.confirm && !confirm(event.target.dataset.confirm)) { event.preventDefault(); return; }
    if (event.target.method.toLowerCase() === 'post') {
      event.target.querySelectorAll('button[type=submit],button:not([type])').forEach(button => { button.disabled = true; button.textContent = 'Processando…'; });
    }
  });
})();
