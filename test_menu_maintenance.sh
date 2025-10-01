#!/bin/bash

echo "🔍 VÉRIFICATION DE LA CONFIGURATION MAINTENANCE - ZenFleet"
echo "=========================================================="
echo ""

echo "1️⃣ Fichier catalyst.blade.php modifié :"
if grep -q "Maintenance avec sous-menus" /home/lynx/projects/zenfleet/resources/views/layouts/admin/catalyst.blade.php; then
    echo "   ✅ Menu avec sous-menus trouvé"
else
    echo "   ❌ Menu avec sous-menus NON trouvé"
fi

echo ""
echo "2️⃣ Sous-menus présents :"
grep -c "Surveillance\|Planifications\|Demandes réparation\|Opérations" /home/lynx/projects/zenfleet/resources/views/layouts/admin/catalyst.blade.php | xargs -I {} echo "   ✅ {} sous-menus trouvés"

echo ""
echo "3️⃣ Font-semibold appliqué :"
if grep -q "font-semibold" /home/lynx/projects/zenfleet/resources/views/layouts/admin/catalyst.blade.php; then
    echo "   ✅ Font-semibold présent dans le fichier"
else
    echo "   ❌ Font-semibold NON trouvé"
fi

echo ""
echo "4️⃣ Contrôleur SurveillanceController :"
if [ -f "/home/lynx/projects/zenfleet/app/Http/Controllers/Admin/Maintenance/SurveillanceController.php" ]; then
    echo "   ✅ Fichier existe"
else
    echo "   ❌ Fichier n'existe pas"
fi

echo ""
echo "5️⃣ Vue surveillance :"
if [ -f "/home/lynx/projects/zenfleet/resources/views/admin/maintenance/surveillance/index.blade.php" ]; then
    echo "   ✅ Fichier existe"
else
    echo "   ❌ Fichier n'existe pas"
fi

echo ""
echo "6️⃣ Route surveillance :"
docker compose exec -u zenfleet_user php php artisan route:list 2>/dev/null | grep -q "surveillance" && echo "   ✅ Route existe" || echo "   ❌ Route n'existe pas"

echo ""
echo "7️⃣ Types de maintenance dans la base :"
docker compose exec -u zenfleet_user php php artisan tinker --execute="echo 'Total: ' . App\Models\MaintenanceType::count() . ' types';" 2>/dev/null

echo ""
echo "8️⃣ Contrôleurs corrigés :"
if grep -q "Charger les types de maintenance actifs" /home/lynx/projects/zenfleet/app/Http/Controllers/Admin/MaintenanceOperationController.php; then
    echo "   ✅ MaintenanceOperationController corrigé"
else
    echo "   ❌ MaintenanceOperationController NON corrigé"
fi

if grep -q "Charger les types de maintenance actifs" /home/lynx/projects/zenfleet/app/Http/Controllers/Admin/MaintenanceScheduleController.php; then
    echo "   ✅ MaintenanceScheduleController corrigé"
else
    echo "   ❌ MaintenanceScheduleController NON corrigé"
fi

echo ""
echo "=========================================================="
echo "✨ Vérification terminée !"
echo ""
