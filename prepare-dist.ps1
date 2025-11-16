Write-Host "🧹 Nettoyage du dossier dist..." -ForegroundColor Cyan
Remove-Item -Recurse -Force ".\dist\vendor" -ErrorAction SilentlyContinue
Remove-Item -Recurse -Force ".\dist\node_modules" -ErrorAction SilentlyContinue
Remove-Item -Force ".\dist\.env.local" -ErrorAction SilentlyContinue
Remove-Item -Force ".\dist\.env.local.php" -ErrorAction SilentlyContinue

Write-Host "🔍 Vérification des clés Stripe..." -ForegroundColor Yellow
$skKeys = Select-String -Path ".\dist\*" -Pattern "sk_test_" -SimpleMatch -List
$pkKeys = Select-String -Path ".\dist\*" -Pattern "pk_test_" -SimpleMatch -List

if ($skKeys -or $pkKeys) {
    Write-Host "⚠️ ATTENTION : des clés Stripe ont été trouvées dans le dossier dist !" -ForegroundColor Red
    Write-Host "Merci de vérifier vos fichiers avant l’envoi." -ForegroundColor Red
    if ($skKeys) { Write-Host "Clés privées trouvées :" ($skKeys | Select-Object -ExpandProperty Path) }
    if ($pkKeys) { Write-Host "Clés publiques trouvées :" ($pkKeys | Select-Object -ExpandProperty Path) }
    exit 1
} else {
    Write-Host "✅ Aucune clé Stripe trouvée." -ForegroundColor Green
}

Write-Host "📦 Création du fichier ZIP..." -ForegroundColor Green
$zipPath = ".\mon_projet_symfony_dist.zip"
if (Test-Path $zipPath) { Remove-Item $zipPath -Force }
Compress-Archive -Path .\dist -DestinationPath $zipPath

Write-Host "🎉 Fichier ZIP créé avec succès : $zipPath" -ForegroundColor Cyan
Write-Host "Tu peux maintenant l’envoyer à ton mentor 🚀"
