const { app, BrowserWindow, dialog } = require('electron');
const { spawn } = require('child_process');
const path = require('path');
const http = require('http');

let mainWindow;
let laravelServer;

// Détection automatique : Développement vs Production (après compilation .exe)
const isDev = !app.isPackaged;
const projectPath = isDev 
  ? path.resolve(__dirname, '..') 
  : path.join(process.resourcesPath, 'laravel');

const phpPath = 'php'; // Utilise PHP depuis le PATH Windows
const port = 8765;
const url = `http://127.0.0.1:${port}`;

function startLaravel() {
  laravelServer = spawn(
    phpPath,
    ['artisan', 'serve', '--host=127.0.0.1', `--port=${port}`],
    {
      cwd: projectPath,
      stdio: 'ignore', // 'ignore' masque les logs console, 'inherit' les affiche pour le debug
      windowsHide: true // Cache la fenêtre noire du terminal PHP sous Windows
    }
  );

  laravelServer.on('error', (error) => {
    dialog.showErrorBox('Erreur Serveur', `Impossible de démarrer le serveur PHP : ${error.message}`);
    app.quit();
  });
}

// Fonction pour s'assurer que Laravel répond avant d'afficher la fenêtre (évite la page blanche)
function waitForServer(retries = 30) {
  http.get(url, (res) => {
    createWindow();
  }).on('error', (err) => {
    if (retries > 0) {
      setTimeout(() => waitForServer(retries - 1), 500);
    } else {
      dialog.showErrorBox('Erreur de démarrage', 'Le serveur Laravel met trop de temps à répondre.');
      app.quit();
    }
  });
}

function createWindow() {
  mainWindow = new BrowserWindow({
    width: 1440,
    height: 900,
    show: false,
    autoHideMenuBar: true, // Masque la barre de menu par défaut d'Electron
    webPreferences: {
      contextIsolation: true,
      nodeIntegration: false
    }
  });

  mainWindow.once('ready-to-show', () => mainWindow.show());
  mainWindow.loadURL(url);
  
  mainWindow.on('closed', () => {
    mainWindow = null;
  });
}

app.whenReady().then(() => {
  startLaravel();
  // Laisse une petite seconde à PHP/Artisan pour initialiser puis vérifie qu'il répond
  setTimeout(waitForServer, 1000);
});

// S'assure de tuer le processus PHP en arrière-plan lorsque l'application se ferme
app.on('before-quit', () => {
  if (laravelServer && !laravelServer.killed) {
    laravelServer.kill();
  }
});