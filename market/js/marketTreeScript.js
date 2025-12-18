/**
 * market tree: active category + раскрытие родителей
 * ориентируется НЕ на href, а на параметр ?c= и data-id
 */
(function () {
  if (window.__marketTreeScriptLoadedlist) return;
  window.__marketTreeScriptLoadedlist = true;

  document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('market-tree-list');
    if (!container) return;

    const params = new URLSearchParams(window.location.search);
    const currentCat = params.get('c'); // текущая категория market
    if (!currentCat) return;

    const showCollapse = (collapseEl) => {
      if (!collapseEl) return;

      try {
        if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
          bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false }).show();
        } else {
          collapseEl.classList.add('show');
        }
      } catch (e) {
        collapseEl.classList.add('show');
      }

      const id = collapseEl.id;
      if (!id) return;

      const btn = container.querySelector(`[data-bs-target="#${id}"]`);
      if (!btn) return;

      btn.setAttribute('aria-expanded', 'true');
      const icon = btn.querySelector('i');
      if (icon) {
        icon.classList.remove('fa-chevron-left');
        icon.classList.add('fa-chevron-down');
      }
    };

    // ищем элемент текущей категории по data-id
    const activeItem = container.querySelector(`.list-group-item[data-id="${CSS.escape(currentCat)}"]`);
    if (!activeItem) return;

    // подсветка ссылки текущей категории
    const activeLink = activeItem.querySelector('a[href]');
    if (activeLink) {
      activeLink.classList.add('active', 'fw-bold', 'text-primary');
    }

    // раскрываем все родительские collapse вверх по дереву
    let collapse = activeItem.closest('.collapse');
    while (collapse && container.contains(collapse)) {
      showCollapse(collapse);
      const parentItem = collapse.closest('.list-group-item');
      collapse = parentItem ? parentItem.closest('.collapse') : null;
    }

    // синхронизация иконок при ручном клике
    container.querySelectorAll('.toggle-subcats').forEach(btn => {
      const targetSel = btn.getAttribute('data-bs-target');
      if (!targetSel) return;

      const collapseEl = container.querySelector(targetSel);
      if (!collapseEl) return;

      const icon = btn.querySelector('i');

      const sync = () => {
        const shown = collapseEl.classList.contains('show');
        btn.setAttribute('aria-expanded', shown ? 'true' : 'false');
        if (!icon) return;
        icon.classList.toggle('fa-chevron-left', !shown);
        icon.classList.toggle('fa-chevron-down', shown);
      };

      collapseEl.addEventListener('shown.bs.collapse', sync);
      collapseEl.addEventListener('hidden.bs.collapse', sync);
      sync();
    });

  });
})();
