const fs = require('fs');
const path = require('path');
const JavaScriptObfuscator = require('javascript-obfuscator');

// Chemin vers le main.js à l'intérieur du dossier généré par electron-builder
const targetFile = path.join(__dirname, 'dist', 'win-unpacked', 'resources', 'app', 'main.js');

if (fs.existsSync(targetFile)) {
  const code = fs.readFileSync(targetFile, 'utf8');
  
  const obfuscatedCode = JavaScriptObfuscator.obfuscate(code, {
    compact: true,
    controlFlowFlattening: true,
    deadCodeInjection: true
  }).getObfuscatedCode();

  fs.writeFileSync(targetFile, obfuscatedCode, 'utf8');
  console.log('✅ Fichier main.js protégé avec succès après le build !');
} else {
  console.log('⚠️ Attention : Fichier main.js non trouvé dans le dossier de build.');
}