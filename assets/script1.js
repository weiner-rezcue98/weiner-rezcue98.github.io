 // Ativar os tooltips
 var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
 var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
   return new bootstrap.Tooltip(tooltipTriggerEl);
 });

 // Função de pesquisa no site
 function searchSite() {
   const query = document.getElementById('searchInput').value.toLowerCase();
   const items = document.querySelectorAll('.icon-item');

   items.forEach(item => {
     const text = item.textContent.toLowerCase();
     if (text.includes(query)) {
       item.style.display = 'block';
     } else {
       item.style.display = 'none';
     }
   });

   if (query === '') {
     items.forEach(item => item.style.display = 'block');
   }
 }

 // Função para alternar a exibição do menu
 function toggleMenu() {
   const menu = document.getElementById('menu');
   menu.style.display = menu.style.display === 'flex' ? 'none' : 'flex';
 }