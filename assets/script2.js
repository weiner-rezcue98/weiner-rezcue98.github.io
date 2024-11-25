 // Alternar contraste
 function toggleContrast() {
    document.body.classList.toggle('high-contrast');
  }

  // Aumentar tamanho do texto
  function increaseFontSize() {
    document.body.classList.remove('small-font');
    document.body.classList.add('large-font');
  }

  // Reduzir tamanho do texto
  function decreaseFontSize() {
    document.body.classList.remove('large-font');
    document.body.classList.add('small-font');
  }