// dashboard.js - tab switching and active menu handling
document.addEventListener('DOMContentLoaded', function(){
  const menu = document.getElementById('dashboardMenu');
  const items = menu.querySelectorAll('.menu-item');
  const tabs = document.querySelectorAll('.tab-content');

  function showTab(name){
    tabs.forEach(t => {
      if (t.id === name) t.classList.remove('hidden'); else t.classList.add('hidden');
    });
    items.forEach(mi => {
      if (mi.dataset.tab === name) mi.classList.add('active'); else mi.classList.remove('active');
    });
    // update hash for persistence
    history.replaceState(null, '', '#'+name);
  }

  items.forEach(mi => {
    mi.addEventListener('click', function(e){
      e.preventDefault();
      const tab = this.dataset.tab;
      showTab(tab);
    });
  });

  // open tab from hash if present
  const initial = (location.hash ? location.hash.replace('#','') : 'overview');
  // if hash not match any tab, fallback
  const allowed = Array.from(items).map(i => i.dataset.tab);
  showTab(allowed.includes(initial) ? initial : 'overview');
});
