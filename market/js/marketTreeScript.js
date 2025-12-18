
/**
 * Guard: run script only once per page.
 */
(function () {
  if (window.__marketTreeScriptLoadedlist) return;
  window.__marketTreeScriptLoadedlist = true;

  document.addEventListener('DOMContentLoaded', function () {

    const normalizePath = (href) => {
      try {
        return new URL(href, window.location.origin).pathname.replace(/\/+$/, '');
      } catch (e) {
        return '';
      }
    };

    const showCollapse = (container, collapseEl) => {
      if (!collapseEl || !container.contains(collapseEl)) return;

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

    const processTree = (container) => {
      if (container.dataset.treeInitialized) return;
      container.dataset.treeInitialized = '1';

      const currentPath = window.location.pathname.replace(/\/+$/, '');

      let activeLink = null;
      let bestLen = 0;

      container.querySelectorAll('a[href]').forEach(a => {
        const href = normalizePath(a.getAttribute('href'));
        if (!href) return;

        if (currentPath === href || currentPath.startsWith(href + '/')) {
          if (href.length > bestLen) {
            bestLen = href.length;
            activeLink = a;
          }
        }
      });

      if (activeLink) {
        activeLink.classList.add('active', 'fw-bold', 'text-primary');

        let collapse = activeLink.closest('.collapse');
        while (collapse && container.contains(collapse)) {
          showCollapse(container, collapse);
          const parentItem = collapse.closest('.list-group-item');
          collapse = parentItem ? parentItem.closest('.collapse') : null;
        }
      }

      // sync icons on manual toggle
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
    };

    const container = document.getElementById('market-tree-list');
    if (container) processTree(container);

  });
})();
