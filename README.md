# Guida all’avvio del progetto (Vite + PHP API + MySQL)

Questa documentazione spiega, passo‑passo, come far partire il progetto sul proprio computer.

---

## Prerequisiti

| Strumento | Versione minima | Perché |
|-----------|----------------|--------|
| **Node.js** | ≥ 18 | Necessario per Vite |
| **pnpm** | ≥ 8 | Gestore dei pacchetti usato dal progetto |
| **Apache** | – | Serve i file PHP |
| **PHP** | ≥ 8.0 | Esegue l’API |
| **MySQL** (o MariaDB) | – | Database `catalog` |
| **phpMyAdmin** (o altro client MySQL) | – | Creazione e importazione del database |
| **Git** (opzionale) | – | Solo se vuoi versionare il codice |

Assicurarsi che Apache, PHP e MySQL siano avviati.

---

## 1 Installare le dipendenze del front‑end

Aprire il terminale, spostarsi nella cartella principale del progetto e lanciare:

```bash
pnpm install
```

> Se `pnpm` non è installato, eseguire `npm i -g pnpm` prima.

---

## 2 Avviare il server di sviluppo Vite

```bash
pnpm run dev
```

Il server parte su **http://localhost:5173** (porta predefinita).  
Lasciare il terminale aperto: Vite ricompila automaticamente le modifiche.

---

## 3 Deploy dell’API PHP

### 3.1 Copia dei file

Copiare l’intera cartella `src-php/apicatalogo` nella directory di Apache `htdocs` (o nella cartella `www` a seconda della propria configurazione). Il risultato deve essere:

```
/path/to/htdocs/
└─ apicatalogo/
   ├─ index.php
   ├─ config.php
   └─ …altri file PHP…
```

### 3.2 Verifica

Aprire il browser e visitare:

```
http://localhost/apicatalogo/
```

In caso di errori controllare i log di apache

---

## 4 Creare il database MySQL

1. Accedere a **phpMyAdmin** (di solito `http://localhost/phpmyadmin`).  
2. Andare alla scheda **Database** → **Crea database**.  
   - **Nome:** `catalog`  
   - **Collation:** `utf8mb4_general_ci` (o quella di default).  
   - Cliccare **Crea**.

---

## 5 Importare lo schema e i dati

1. Selezionare il database **catalog** appena creato.  
2. Aprire la scheda **Importa**.  
3. Premere **Scegli file** e selezionare `public/catalog.sql` (file presente nella cartella del progetto).  
4. Mantenere le impostazioni predefinite (formato SQL, charset `utf8mb4`).  
5. Cliccare **Esegui**.

Al termine dovrebbero comparire le tabelle (`titles`, `title_images`, ecc.).

è possibile usare i dati in formato json presenti nel percorso `public/film&serie.json` (con opportuna conversione) come dati di esempio per il database.

---

## 6 Configurare la connessione dell’API

Modificare il file `apicatalogo/config.php` (o il file di configurazione usato dall’API) inserendo i parametri del proprio server MySQL:

```php
<?php
return [
    'db' => [
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'dbname'   => 'catalog',
        'user'     => 'root',          // cambiare se si usa un altro utente
        'password' => '',              // aggiungere la password se necessaria
        'charset'  => 'utf8mb4',
    ],
];
```

Salvare il file.

---

## 7 Testare l’intero stack

1. **Front‑end:** aprire `http://localhost:5173` nel browser.  
2. L’applicazione dovrebbe caricare i dati dall’API (`http://localhost/apicatalogo/...`) e mostrare titoli, immagini, ecc.

Se tutto è visualizzato correttamente, il progetto è pronto per l’uso.

---

## Risoluzione problemi comuni

| Problema | Possibili cause | Come verificare |
|----------|----------------|-----------------|
| **Vite non carica i moduli** | Il server Vite non è in esecuzione | Controllare il terminale dove si ha lanciato `pnpm run dev`. |
| **API restituisce 500 / “Connection refused”** | Credenziali DB errate o MySQL non avviato | Verificare le impostazioni in `config.php` e che il servizio MySQL sia attivo. |
| **Immagini mancanti** | `title_images.url` vuoto o URL non valido | Controllare la tabella `title_images` in phpMyAdmin; l’URL di default è quello fornito. |
| **404 su `/apicatalogo`** | Cartella copiata nel percorso sbagliato | Assicurarsi che la cartella sia dentro `htdocs` (o la root di Apache). |
| **Importazione SQL fallita** | File troppo grande o charset errato | Verificare `upload_max_filesize` e `post_max_size` in `php.ini`; usare charset `utf8mb4`. |

---

## Avvio rapido (una riga per Linux/macOS)

```bash
# 1 Installa dipendenze
pnpm install

# 2 Avvia Vite in background
pnpm run dev &

# 3 Copia API (adatta il percorso di htdocs)
cp -R src-php/apicatalogo /path/to/htdocs/apicatalogo

# 4 Crea DB e importa dati (richiede password MySQL)
mysql -u root -p -e "CREATE DATABASE catalog CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
mysql -u root -p catalog < public/catalog.sql
```

Dopo aver eseguito questi comandi, aprire:

- **Front‑end:** `http://localhost:5173`  
- **API:** `http://localhost/apicatalogo/`

---

**NB** è possibile generare una build del progetto con:
```bash
pnpm run build
```