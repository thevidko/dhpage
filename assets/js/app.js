document.addEventListener('DOMContentLoaded', function() {

  const setText = (id, text) => {
    const element = document.getElementById(id);
    if (element) {
      element.textContent = text;
    } else {
      console.warn(`Element s ID "${id}" nebyl nalezen.`);
    }
  };

  fetch('config.json?v=' + new Date().getTime())
    .then(response => {
      if (!response.ok) throw new Error("Nepodařilo se načíst config.json");
      return response.json();
    })
    .then(config => {

      // --- ČÁST 1: OZNÁMENÍ (Upraveno) ---
      const infoPrijimame = document.getElementById('info-prijimame');
      const infoOznameni = document.getElementById('info-oznameni'); // Změněno z info-dovolena
      if (infoPrijimame && infoOznameni) {
        infoPrijimame.classList.add('d-none');
        infoOznameni.classList.add('d-none');
        
        if (config.zobrazitOznameni) { // Změněno
          const spanOznameni = infoOznameni.querySelector('span.h5');
          if (spanOznameni) {
            spanOznameni.textContent = config.oznameniText; // Změněno
          }
          infoOznameni.classList.remove('d-none');
        } else if (config.prijimameNovePacienty) {
          infoPrijimame.classList.remove('d-none');
        }
      }

      // --- ČÁST 2: OTEVÍRACÍ DOBA ---
      const oteviraciDobaEl = document.getElementById('oteviraci-doba-content');
      if (oteviraciDobaEl && config.oteviraciDoba) {
        oteviraciDobaEl.innerHTML = `
          <strong>Po:</strong> ${config.oteviraciDoba.po}<br>
          <strong>Út:</strong> ${config.oteviraciDoba.ut}<br>
          <strong>St:</strong> ${config.oteviraciDoba.st}<br>
          <strong>Čt:</strong> ${config.oteviraciDoba.ct}<br>
          <strong>Pá:</strong> ${config.oteviraciDoba.pa}<br>
          <strong>So:</strong> ${config.oteviraciDoba.so}<br>
          <strong>Ne:</strong> ${config.oteviraciDoba.ne}
        `;
      }

      // --- ČÁST 3: CENÍK ---
      if (config.cenik) {
        setText('cena-dospeli-vstupni', config.cenik.dospeli.vstupni);
        setText('cena-dospeli-kontrolni', config.cenik.dospeli.kontrolni);
        setText('cena-deti-do6let', config.cenik.deti.do6let);
        setText('cena-deti-do15let', config.cenik.deti.do15let);
        setText('cena-deti-nanecisto', config.cenik.deti.nanecisto);
      }

    })
    .catch(error => {
      console.error('Chyba při zpracování konfigurace:', error);
    });
});