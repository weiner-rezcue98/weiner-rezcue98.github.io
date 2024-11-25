function openIframe(url, title) {
    // Obtém os elementos necessários
    const iframeContainer = document.getElementById('iframeContainer');
    const iframe = document.getElementById('contentIframe');
    const iframeTitle = document.getElementById('iframeTitle');
  
    if (!iframeContainer || !iframe || !iframeTitle) {
      console.error("Elementos do iframe não encontrados no DOM.");
      return;
    }
  
    // Atualiza o título
    if (title) {
      iframeTitle.textContent = title; // Define o título dinamicamente
      iframeTitle.style.color = 'black'; // Garante a cor correta
    } else {
      iframeTitle.textContent = "Título não definido";
    }
  
    // Configura o iframe
    iframe.src = url;
  
    // Mostra o container
    iframeContainer.style.display = 'block';
  
    // Rola para o container
    iframeContainer.scrollIntoView({ behavior: 'smooth' });
  }
  
  function closeIframe() {
    const iframeContainer = document.getElementById('iframeContainer');
    const iframe = document.getElementById('contentIframe');
  
    if (!iframeContainer || !iframe) {
      console.error("Elementos do iframe não encontrados para fechar.");
      return;
    }
  
    // Limpa o iframe e oculta o container
    iframe.src = '';
    iframeContainer.style.display = 'none';
  }
  