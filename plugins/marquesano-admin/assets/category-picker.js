/* Progressive enhancement of the submitted native multiple select. No dependencies. */
(() => {
  'use strict';
  const normalize = value => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('pt-BR').trim();
  const filterCategories = (options, query) => { const needle = normalize(query); return options.filter(option => normalize(option.textContent).includes(needle)); };
  const toggleCategory = (options, value) => { const option = options.find(item => item.value === String(value)); if (option) option.selected = !option.selected; };
  const moveActive = (length, active, direction) => length ? (active + direction + length) % length : -1;
  function enhance(root) {
    const select = root.querySelector('select');
    if (!select || root.dataset.enhanced) return;
    const options = Array.from(select.options);
    const make = (tag, className, text) => { const element = document.createElement(tag); element.className = className; if (text) element.textContent = text; return element; };
    const chips = make('div', 'mp-category-tags');
    const trigger = make('button', 'mp-category-trigger'); trigger.type = 'button'; trigger.id = select.id + '-trigger';
    trigger.setAttribute('aria-expanded', 'false'); trigger.setAttribute('aria-controls', select.id + '-popup');
    trigger.setAttribute('aria-labelledby', 'mp-category-label ' + select.id + '-caption');
    const caption = make('span', '', 'Selecionar categorias'); caption.id = select.id + '-caption'; trigger.append(caption);
    const popup = make('div', 'mp-category-popup'); popup.id = select.id + '-popup'; popup.hidden = true;
    const search = make('input', 'mp-category-search'); search.type = 'search'; search.placeholder = 'Pesquisar categorias';
    search.setAttribute('role', 'combobox'); search.setAttribute('aria-label', 'Pesquisar categorias pelo nome'); search.setAttribute('aria-autocomplete', 'list'); search.setAttribute('aria-expanded', 'false'); search.setAttribute('autocomplete', 'off');
    const list = make('div', 'mp-category-list'); list.id = select.id + '-list'; list.setAttribute('role', 'listbox'); list.setAttribute('aria-multiselectable', 'true'); list.setAttribute('aria-label', 'Categorias disponíveis'); search.setAttribute('aria-controls', list.id);
    trigger.setAttribute('aria-haspopup', 'listbox'); trigger.setAttribute('aria-controls', list.id); trigger.setAttribute('aria-describedby', 'mp-category-help');
    const status = make('div', 'mp-category-status'); status.setAttribute('role', 'status'); status.setAttribute('aria-live', 'polite');
    popup.append(search, list, status);
    let visible = [], active = -1;
    const nodes = new Map(options.map(option => {
      const node = make('div', 'mp-category-option', option.textContent); node.id = select.id + '-option-' + option.value; node.setAttribute('role', 'option');
      node.addEventListener('mousedown', event => event.preventDefault());
      node.addEventListener('click', () => { toggleCategory(options, option.value); sync(); search.focus(); });
      list.append(node); return [option, node];
    }));
    function markActive(scroll = false) {
      nodes.forEach(node => node.classList.remove('is-active'));
      const node = visible[active] && nodes.get(visible[active]);
      if (node) { node.classList.add('is-active'); search.setAttribute('aria-activedescendant', node.id); if (scroll) node.scrollIntoView({ block: 'nearest' }); }
      else search.removeAttribute('aria-activedescendant');
    }
    function filter() {
      visible = filterCategories(options, search.value); const matches = new Set(visible);
      nodes.forEach((node, option) => { node.hidden = !matches.has(option); }); active = -1; markActive();
      status.textContent = (visible.length ? visible.length + ' categorias encontradas.' : 'Nenhuma categoria encontrada.') + ' ' + options.filter(option => option.selected).length + ' selecionadas.';
    }
    function close(focus = false) { popup.hidden = true; trigger.setAttribute('aria-expanded', 'false'); search.setAttribute('aria-expanded', 'false'); search.removeAttribute('aria-activedescendant'); if (focus) trigger.focus(); }
    function open() { popup.hidden = false; trigger.setAttribute('aria-expanded', 'true'); search.setAttribute('aria-expanded', 'true'); search.value = ''; filter(); search.focus(); }
    function sync() {
      chips.replaceChildren();
      options.forEach(option => {
        nodes.get(option).setAttribute('aria-selected', String(option.selected));
        if (!option.selected) return;
        const chip = make('span', 'mp-category-tag'); chip.title = option.textContent;
        const text = make('span', '', option.dataset.name || option.textContent);
        const remove = make('button', '', '×'); remove.type = 'button'; remove.setAttribute('aria-label', 'Remover categoria ' + option.textContent);
        remove.addEventListener('click', () => { option.selected = false; sync(); trigger.focus(); }); chip.append(text, remove); chips.append(chip);
      });
      const count = options.filter(option => option.selected).length;
      caption.textContent = count ? 'Adicionar categorias (' + count + ' selecionadas)' : 'Selecionar categorias';
      const previous = visible[active]; filter(); active = visible.indexOf(previous); markActive();
    }
    trigger.addEventListener('click', () => popup.hidden ? open() : close());
    trigger.addEventListener('keydown', event => { if (event.key === 'ArrowDown' || event.key === 'ArrowUp') { event.preventDefault(); open(); active = event.key === 'ArrowDown' ? 0 : visible.length - 1; markActive(true); } });
    search.addEventListener('input', filter);
    search.addEventListener('keydown', event => {
      if (event.key === 'ArrowDown' || event.key === 'ArrowUp') { event.preventDefault(); active = active < 0 ? (event.key === 'ArrowDown' ? 0 : visible.length - 1) : moveActive(visible.length, active, event.key === 'ArrowDown' ? 1 : -1); markActive(true); }
      if (event.key === 'Enter') { event.preventDefault(); if (visible[active]) { toggleCategory(options, visible[active].value); sync(); } }
    });
    root.addEventListener('keydown', event => { if (event.key === 'Escape' && !popup.hidden) { event.preventDefault(); close(true); } });
    root.addEventListener('focusout', event => { if (!root.contains(event.relatedTarget)) close(); });
    document.addEventListener('click', event => { if (!root.contains(event.target)) close(); });
    select.addEventListener('change', sync);
    root.append(chips, trigger, popup); sync(); select.hidden = true; root.querySelector('label').htmlFor = trigger.id; root.dataset.enhanced = '1';
  }
  if (typeof module !== 'undefined' && module.exports) module.exports = { filterCategories, toggleCategory, moveActive };
  if (typeof document !== 'undefined') document.querySelectorAll('.mp-category-picker').forEach(enhance);
})();
