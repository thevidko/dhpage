// Tento soubor načítá data z config.json a řídí zobrazení prvků.

document.addEventListener('DOMContentLoaded', function() {
  
    // Načtení elementů z HTML stránky
    const infoPrijimame = document.getElementById('info-prijimame');
    const infoDovolena = document.getElementById('info-dovolena');
  
    // Kontrola, zda elementy existují, aby se předešlo chybám
    if (!infoPrijimame || !infoDovolena) {
      console.error('Chyba: Elementy pro oznámení nebyly nalezeny.');
      return;
    }
    
    // Načtení konfigurace z externího souboru config.json
    fetch('config.json?v=' + new Date().getTime()) // Přidáno kvůli cachování
      .then(response => {
        if (!response.ok) {
          throw new Error("Nepodařilo se načíst config.json");
        }
        return response.json();
      })
      .then(siteConfig => {
        
        // Skryjeme oba bloky, aby se předešlo problikávání
        infoPrijimame.classList.add('d-none');
        infoDovolena.classList.add('d-none');
  
        // Logika pro zobrazení bloku o dovolené (má vyšší prioritu)
        if (siteConfig.jeDovolena) {
          const spanDovolena = infoDovolena.querySelector('span.h5');
          if (spanDovolena) {
            spanDovolena.textContent = `DOVOLENÁ: V termínu od ${siteConfig.dovolenáOd} do ${siteConfig.dovolenáDo} neordinuji.`;
          }
          infoDovolena.classList.remove('d-none');
        }
        // Pokud není dovolená, řešíme zobrazení bloku o přijímání pacientů
        else if (siteConfig.prijimameNovePacienty) {
          infoPrijimame.classList.remove('d-none');
        }
      })
      .catch(error => {
        console.error('Chyba při zpracování konfigurace:', error);
        // Můžete sem přidat logiku pro případ, že se soubor nepodaří načíst
      });
  });