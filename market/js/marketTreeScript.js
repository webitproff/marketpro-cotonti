/**
 * Guard: run script only once per page.
 * If you still render this template inside nested trees, the script won't re-run.
 */
(function(){
  if (window.__marketTreeScriptLoaded) return;
  window.__marketTreeScriptLoaded = true;

  document.addEventListener('DOMContentLoaded', () => {
    const normalizePath = (href) => {
      try {
        return new URL(href, window.location.origin).pathname.replace(/\/+$/, '');
      } catch (e) {
        return ('/' + (href || '')).replace(/\/+$/, '');
      }
    };

    const processTree = (container) => {
      if (container.dataset.treeInitialized) return;
      container.dataset.treeInitialized = '1';

      // 1) Fix relative / incorrect hrefs for items inside this container
      container.querySelectorAll('.list-group-item[data-id]').forEach(item => {
        const dataId = item.dataset.id;
        if (!dataId) return;
        const a = item.querySelector(':scope a[href]');
        if (!a) return;

        const hrefPath = normalizePath(a.getAttribute('href'));
        if (hrefPath.endsWith('/' + dataId) || hrefPath === '/' + dataId) return;

        // Find logical parent inside same container: nearest ancestor .collapse -> parent .list-group-item
        let parentItem = item.closest('.collapse') ? item.closest('.collapse').closest('.list-group-item') : null;
        if (!parentItem) {
          parentItem = item.parentElement ? item.parentElement.closest('.list-group-item') : null;
        }
        if (!parentItem || !container.contains(parentItem)) return;

        const parentA = parentItem.querySelector(':scope a[href]');
        if (!parentA) return;
        const parentPath = normalizePath(parentA.getAttribute('href'));
        if (!parentPath) return;

        const newPath = parentPath + '/' + dataId;
        if (newPath !== hrefPath) {
          a.setAttribute('href', newPath.startsWith('/') ? newPath : '/' + newPath);
        }
      });

      // 2) Find active link inside this container (longest prefix match)
      const currentPath = window.location.pathname.replace(/\/+$/, '');
      let activeLink = null;
      let bestLen = 0;
      container.querySelectorAll('a[href]').forEach(a => {
        const hrefRaw = a.getAttribute('href');
        if (!hrefRaw) return;
        const href = normalizePath(hrefRaw);
        if (!href) return;
        if (currentPath === href || currentPath.startsWith(href + '/')) {
          if (href.length > bestLen) {
            bestLen = href.length;
            activeLink = a;
          }
        }
      });

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
        if (id) {
          const btn = container.querySelector(`[data-bs-target="#${id}"], [data-bs-target='${"#"+id}']`);
          if (btn) btn.setAttribute('aria-expanded', 'true');
          if (btn) {
            const icon = btn.querySelector('i');
            if (icon) { icon.classList.remove('fa-chevron-left'); icon.classList.add('fa-chevron-down'); }
          }
        }
      };

      if (activeLink) {
        activeLink.classList.add('text-primary', 'fw-bold', 'active');

        // show containing collapse(s) inside this container
        let containingCollapse = activeLink.closest('.collapse');
        if (containingCollapse && container.contains(containingCollapse)) {
          showCollapse(containingCollapse);
          let parentItem = containingCollapse.closest('.list-group-item');
          while (parentItem && container.contains(parentItem)) {
            const parentCollapse = parentItem.closest('.collapse');
            if (parentCollapse && container.contains(parentCollapse)) showCollapse(parentCollapse);
            parentItem = parentItem.parentElement ? parentItem.parentElement.closest('.list-group-item') : null;
          }
        } else {
          let parentItem = activeLink.closest('.list-group-item');
          while (parentItem && container.contains(parentItem)) {
            const childCollapse = parentItem.querySelector(':scope > .collapse');
            if (childCollapse) showCollapse(childCollapse);
            parentItem = parentItem.parentElement ? parentItem.parentElement.closest('.list-group-item') : null;
          }
        }
      }

      // 3) Sync icons on manual toggle and initial state (only inside container)
      container.querySelectorAll('.toggle-subcats').forEach(btn => {
        const targetSel = btn.getAttribute('data-bs-target') || btn.dataset.bsTarget;
        if (!targetSel) return;
        const collapseEl = container.querySelector(targetSel);
        if (!collapseEl) return;
        const icon = btn.querySelector('i');

        const sync = () => {
          const shown = collapseEl.classList.contains('show');
          if (!icon) return;
          if (shown) {
            icon.classList.remove('fa-chevron-left');
            icon.classList.add('fa-chevron-down');
            btn.setAttribute('aria-expanded', 'true');
          } else {
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-left');
            btn.setAttribute('aria-expanded', 'false');
          }
        };

        if (collapseEl.addEventListener) {
          collapseEl.addEventListener('shown.bs.collapse', sync);
          collapseEl.addEventListener('hidden.bs.collapse', sync);
        }
        sync();
      });
    };

    // Process the tree using the id "market-tree"
    const container = document.getElementById('market-tree-offcanvas');
    if (container) processTree(container);
  });
})();
/**
 * Guard: run script only once per page.
 * If you still render this template inside nested trees, the script won't re-run.
 */
(function(){
  if (window.__marketTreeScriptLoadedlist) return;
  window.__marketTreeScriptLoadedlist = true;

  document.addEventListener('DOMContentLoaded', () => {
    const normalizePath = (href) => {
      try {
        return new URL(href, window.location.origin).pathname.replace(/\/+$/, '');
      } catch (e) {
        return ('/' + (href || '')).replace(/\/+$/, '');
      }
    };

    const processTree = (container) => {
      if (container.dataset.treeInitialized) return;
      container.dataset.treeInitialized = '1';

      // 1) Fix relative / incorrect hrefs for items inside this container
      container.querySelectorAll('.list-group-item[data-id]').forEach(item => {
        const dataId = item.dataset.id;
        if (!dataId) return;
        const a = item.querySelector(':scope a[href]');
        if (!a) return;

        const hrefPath = normalizePath(a.getAttribute('href'));
        if (hrefPath.endsWith('/' + dataId) || hrefPath === '/' + dataId) return;

        // Find logical parent inside same container: nearest ancestor .collapse -> parent .list-group-item
        let parentItem = item.closest('.collapse') ? item.closest('.collapse').closest('.list-group-item') : null;
        if (!parentItem) {
          parentItem = item.parentElement ? item.parentElement.closest('.list-group-item') : null;
        }
        if (!parentItem || !container.contains(parentItem)) return;

        const parentA = parentItem.querySelector(':scope a[href]');
        if (!parentA) return;
        const parentPath = normalizePath(parentA.getAttribute('href'));
        if (!parentPath) return;

        const newPath = parentPath + '/' + dataId;
        if (newPath !== hrefPath) {
          a.setAttribute('href', newPath.startsWith('/') ? newPath : '/' + newPath);
        }
      });

      // 2) Find active link inside this container (longest prefix match)
      const currentPath = window.location.pathname.replace(/\/+$/, '');
      let activeLink = null;
      let bestLen = 0;
      container.querySelectorAll('a[href]').forEach(a => {
        const hrefRaw = a.getAttribute('href');
        if (!hrefRaw) return;
        const href = normalizePath(hrefRaw);
        if (!href) return;
        if (currentPath === href || currentPath.startsWith(href + '/')) {
          if (href.length > bestLen) {
            bestLen = href.length;
            activeLink = a;
          }
        }
      });

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
        if (id) {
          const btn = container.querySelector(`[data-bs-target="#${id}"], [data-bs-target='${"#"+id}']`);
          if (btn) btn.setAttribute('aria-expanded', 'true');
          if (btn) {
            const icon = btn.querySelector('i');
            if (icon) { icon.classList.remove('fa-chevron-left'); icon.classList.add('fa-chevron-down'); }
          }
        }
      };

      if (activeLink) {
        activeLink.classList.add('text-primary', 'fw-bold', 'active');

        // show containing collapse(s) inside this container
        let containingCollapse = activeLink.closest('.collapse');
        if (containingCollapse && container.contains(containingCollapse)) {
          showCollapse(containingCollapse);
          let parentItem = containingCollapse.closest('.list-group-item');
          while (parentItem && container.contains(parentItem)) {
            const parentCollapse = parentItem.closest('.collapse');
            if (parentCollapse && container.contains(parentCollapse)) showCollapse(parentCollapse);
            parentItem = parentItem.parentElement ? parentItem.parentElement.closest('.list-group-item') : null;
          }
        } else {
          let parentItem = activeLink.closest('.list-group-item');
          while (parentItem && container.contains(parentItem)) {
            const childCollapse = parentItem.querySelector(':scope > .collapse');
            if (childCollapse) showCollapse(childCollapse);
            parentItem = parentItem.parentElement ? parentItem.parentElement.closest('.list-group-item') : null;
          }
        }
      }

      // 3) Sync icons on manual toggle and initial state (only inside container)
      container.querySelectorAll('.toggle-subcats').forEach(btn => {
        const targetSel = btn.getAttribute('data-bs-target') || btn.dataset.bsTarget;
        if (!targetSel) return;
        const collapseEl = container.querySelector(targetSel);
        if (!collapseEl) return;
        const icon = btn.querySelector('i');

        const sync = () => {
          const shown = collapseEl.classList.contains('show');
          if (!icon) return;
          if (shown) {
            icon.classList.remove('fa-chevron-left');
            icon.classList.add('fa-chevron-down');
            btn.setAttribute('aria-expanded', 'true');
          } else {
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-left');
            btn.setAttribute('aria-expanded', 'false');
          }
        };

        if (collapseEl.addEventListener) {
          collapseEl.addEventListener('shown.bs.collapse', sync);
          collapseEl.addEventListener('hidden.bs.collapse', sync);
        }
        sync();
      });
    };

    // Process the tree using the id "market-tree"
    const container = document.getElementById('market-tree-list');
    if (container) processTree(container);
  });
})
();
