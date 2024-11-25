function openIframe(url) {
    const iframeContainer = document.getElementById('iframeContainer');
    const iframe = document.getElementById('contentIframe');

    // Atualiza o URL do iframe
    iframe.src = url;

    // Mostra o container do iframe
    iframeContainer.style.display = 'block';

    // Rola até o iframe
    iframeContainer.scrollIntoView({ behavior: 'smooth' });
  }