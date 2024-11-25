 // Alternar visibilidade do menu
 function toggleMenu() {
    const menu = document.getElementById('menu');
    menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
  }

  // Filtrar itens do menu
  function filterMenu() {
    const query = document.getElementById('menuSearch').value.toLowerCase();
    const items = document.querySelectorAll('#menuItems a');

    items.forEach(item => {
      const text = item.textContent.toLowerCase();
      item.style.display = text.includes(query) ? 'block' : 'none';
    });
  }