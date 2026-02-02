# DevTest - Repository

Questo repository contiene il progetto **DevTest** con gli ultimi aggiornamenti e funzionalità implementate.

---

## Aggiornamenti principali

- **Filtro Referenti**  
  Implementata la logica per filtrare i referenti in base al team e al loro scope (`start` o `end`). Ora la lista dei referenti mostra solo quelli pertinenti al team del shipment.

- **Rimosso `window.location.reload()`**  
  La UI si aggiorna automaticamente grazie alla reattività di Vue. L'aggiunta di un referente avviene senza ricaricare la pagina, migliorando l'esperienza utente.

- **Alcune Fix**
  - Controllo l'aggiunta del referente in base al pivot (`Start`, `End`) in quanto i referenti venivano aggiunti solamente allo Start.  
  - In `Shipment` e `Show`, filtro solo i referenti per quel team (allo stato attuale venivano mostrati anche referenti di altri team).

- **checkEmailExists API**  
  Creata una nuova chiamata API per verificare in tempo reale se un'email è già presente tra i referenti, evitando duplicati e migliorando l'esperienza utente.

---

## Funzionalità principali

- Gestione dei referenti per shipment (`start` e `end`)  
- Validazione email in tempo reale tramite API dedicata  
- Aggiornamento dinamico della UI senza refresh  
- Filtraggio referenti per team  

---