<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de Remise de Véhicule</title>
    <link rel="stylesheet" href="vehicle_handover_form.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Afficher/masquer les champs spécifiques aux motos
            const vehicleTypeSelect = document.getElementById('vehicle_type');
            const motorcycleFields = document.querySelectorAll('.motorcycle-only');
            
            function toggleMotorcycleFields() {
                const isMoto = vehicleTypeSelect.value === 'moto';
                motorcycleFields.forEach(field => {
                    field.style.display = isMoto ? 'flex' : 'none';
                });
            }
            
            vehicleTypeSelect.addEventListener('change', toggleMotorcycleFields);
            toggleMotorcycleFields(); // Exécuter au chargement
            
            // Gestion de l'impression
            document.getElementById('print-btn').addEventListener('click', function() {
                window.print();
            });
            
            // Gestion du téléchargement de la fiche signée
            document.getElementById('upload-form').addEventListener('submit', function(e) {
                e.preventDefault();
                // Logique de téléchargement à implémenter côté serveur
                alert('Fonctionnalité de téléchargement à implémenter côté serveur');
            });
        });
    </script>
</head>
<body>
    <form id="handover-form" method="POST" action="/vehicle-handover/store">
        @csrf
        <div class="header">
            <img src="/logo.png" alt="Logo de l'entreprise" class="logo">
            <h1>FICHE DE REMISE DE VÉHICULE</h1>
        </div>
        
        <div class="info-section">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">ID Fiche de remise :</div>
                    <div class="info-value">
                        <input type="text" name="handover_id" id="handover_id" value="{{ $handoverId ?? 'AUTO' }}" readonly>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">ID Affectation :</div>
                    <div class="info-value">
                        <input type="text" name="assignment_id" id="assignment_id" value="{{ $assignment->id ?? '' }}" readonly>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Date d'émission :</div>
                    <div class="info-value">
                        <input type="date" name="issue_date" id="issue_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Motif d'affectation :</div>
                    <div class="info-value">
                        <input type="text" name="assignment_reason" id="assignment_reason" value="{{ $assignment->reason ?? '' }}" required>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Conducteur affecté :</div>
                    <div class="info-value">
                        <input type="text" name="driver_name" id="driver_name" value="{{ $assignment->driver->full_name ?? '' }}" required>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Matricule employé :</div>
                    <div class="info-value">
                        <input type="text" name="employee_id" id="employee_id" value="{{ $assignment->driver->employee_id ?? '' }}" required>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Immatriculation :</div>
                    <div class="info-value">
                        <input type="text" name="registration_plate" id="registration_plate" value="{{ $assignment->vehicle->registration_plate ?? '' }}" required>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Modèle du véhicule :</div>
                    <div class="info-value">
                        <input type="text" name="vehicle_model" id="vehicle_model" value="{{ $assignment->vehicle->brand }} {{ $assignment->vehicle->model }}" required>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Type de véhicule :</div>
                    <div class="info-value">
                        <select name="vehicle_type" id="vehicle_type" required>
                            <option value="voiture" {{ $assignment->vehicle->type == 'voiture' ? 'selected' : '' }}>Voiture</option>
                            <option value="moto" {{ $assignment->vehicle->type == 'moto' ? 'selected' : '' }}>Moto</option>
                            <option value="utilitaire" {{ $assignment->vehicle->type == 'utilitaire' ? 'selected' : '' }}>Utilitaire</option>
                        </select>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Kilométrage actuel :</div>
                    <div class="info-value">
                        <input type="number" name="current_mileage" id="current_mileage" value="{{ $assignment->vehicle->current_mileage ?? '' }}" required>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="vehicle-sketch">
            <div class="sketch-image">
                <!-- Image du véhicule sera chargée dynamiquement selon le type -->
                <img id="vehicle-diagram" src="/images/vehicle-diagram.png" alt="Schéma du véhicule" style="max-width: 100%; max-height: 100%;">
            </div>
            <div class="observations">
                <h3>Observations générales</h3>
                <textarea name="general_observations" id="general_observations" placeholder="Notez ici les observations générales sur l'état du véhicule..."></textarea>
            </div>
        </div>
        
        <!-- CONTRÔLE PAPIERS -->
        <div class="control-section">
            <h2>CONTRÔLE PAPIERS</h2>
            <div class="control-grid">
                <div class="control-item">
                    <div class="control-label">Carte Grise</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="papers_registration" value="oui" required> Oui</label>
                        <label class="control-option"><input type="radio" name="papers_registration" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="papers_registration" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Assurance</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="papers_insurance" value="oui" required> Oui</label>
                        <label class="control-option"><input type="radio" name="papers_insurance" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="papers_insurance" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Vignette</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="papers_sticker" value="oui" required> Oui</label>
                        <label class="control-option"><input type="radio" name="papers_sticker" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="papers_sticker" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Contrôle technique</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="papers_technical_control" value="oui" required> Oui</label>
                        <label class="control-option"><input type="radio" name="papers_technical_control" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="papers_technical_control" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Carte Carburant</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="papers_fuel_card" value="oui" required> Oui</label>
                        <label class="control-option"><input type="radio" name="papers_fuel_card" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="papers_fuel_card" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Permis de circuler</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="papers_circulation_permit" value="oui" required> Oui</label>
                        <label class="control-option"><input type="radio" name="papers_circulation_permit" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="papers_circulation_permit" value="N/A"> N/A</label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- PNEUMATIQUE -->
        <div class="control-section">
            <h2>PNEUMATIQUE</h2>
            <div class="control-grid">
                <div class="control-item">
                    <div class="control-label">Roue AV Gauche</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="tire_front_left" value="bon" required> Bon</label>
                        <label class="control-option"><input type="radio" name="tire_front_left" value="mauvais"> Mauvais</label>
                        <label class="control-option"><input type="radio" name="tire_front_left" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Roue AV Droite</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="tire_front_right" value="bon" required> Bon</label>
                        <label class="control-option"><input type="radio" name="tire_front_right" value="mauvais"> Mauvais</label>
                        <label class="control-option"><input type="radio" name="tire_front_right" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Roue AR Gauche</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="tire_rear_left" value="bon" required> Bon</label>
                        <label class="control-option"><input type="radio" name="tire_rear_left" value="mauvais"> Mauvais</label>
                        <label class="control-option"><input type="radio" name="tire_rear_left" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Roue AR Droite</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="tire_rear_right" value="bon" required> Bon</label>
                        <label class="control-option"><input type="radio" name="tire_rear_right" value="mauvais"> Mauvais</label>
                        <label class="control-option"><input type="radio" name="tire_rear_right" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Roue de Secours</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="tire_spare" value="bon" required> Bon</label>
                        <label class="control-option"><input type="radio" name="tire_spare" value="mauvais"> Mauvais</label>
                        <label class="control-option"><input type="radio" name="tire_spare" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Enjoliveurs</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="tire_hubcaps" value="bon" required> Bon</label>
                        <label class="control-option"><input type="radio" name="tire_hubcaps" value="mauvais"> Mauvais</label>
                        <label class="control-option"><input type="radio" name="tire_hubcaps" value="N/A"> N/A</label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- CONTRÔLE ÉTAT EXTÉRIEUR ET ACCESSOIRES -->
        <div class="control-section">
            <h2>CONTRÔLE ÉTAT EXTÉRIEUR ET ACCESSOIRES</h2>
            <div class="control-grid">
                <div class="control-item">
                    <div class="control-label">Vitres</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="ext_windows" value="bon" required> Bon</label>
                        <label class="control-option"><input type="radio" name="ext_windows" value="mauvais"> Mauvais</label>
                        <label class="control-option"><input type="radio" name="ext_windows" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Pare-brise</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="ext_windshield" value="bon" required> Bon</label>
                        <label class="control-option"><input type="radio" name="ext_windshield" value="mauvais"> Mauvais</label>
                        <label class="control-option"><input type="radio" name="ext_windshield" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Rétroviseur Gauche</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="ext_mirror_left" value="bon" required> Bon</label>
                        <label class="control-option"><input type="radio" name="ext_mirror_left" value="mauvais"> Mauvais</label>
                        <label class="control-option"><input type="radio" name="ext_mirror_left" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Rétroviseur Droit</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="ext_mirror_right" value="bon" required> Bon</label>
                        <label class="control-option"><input type="radio" name="ext_mirror_right" value="mauvais"> Mauvais</label>
                        <label class="control-option"><input type="radio" name="ext_mirror_right" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Verrouillage</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="ext_locks" value="bon" required> Bon</label>
                        <label class="control-option"><input type="radio" name="ext_locks" value="mauvais"> Mauvais</label>
                        <label class="control-option"><input type="radio" name="ext_locks" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Poignées</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="ext_handles" value="bon" required> Bon</label>
                        <label class="control-option"><input type="radio" name="ext_handles" value="mauvais"> Mauvais</label>
                        <label class="control-option"><input type="radio" name="ext_handles" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Baguettes</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="ext_strips" value="bon" required> Bon</label>
                        <label class="control-option"><input type="radio" name="ext_strips" value="mauvais"> Mauvais</label>
                        <label class="control-option"><input type="radio" name="ext_strips" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Feux avant</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="ext_front_lights" value="bon" required> Bon</label>
                        <label class="control-option"><input type="radio" name="ext_front_lights" value="mauvais"> Mauvais</label>
                        <label class="control-option"><input type="radio" name="ext_front_lights" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Feux arrières</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="ext_rear_lights" value="bon" required> Bon</label>
                        <label class="control-option"><input type="radio" name="ext_rear_lights" value="mauvais"> Mauvais</label>
                        <label class="control-option"><input type="radio" name="ext_rear_lights" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Essuie-glaces</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="ext_wipers" value="bon" required> Bon</label>
                        <label class="control-option"><input type="radio" name="ext_wipers" value="mauvais"> Mauvais</label>
                        <label class="control-option"><input type="radio" name="ext_wipers" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Carrosserie Générale</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="ext_body" value="bon" required> Bon</label>
                        <label class="control-option"><input type="radio" name="ext_body" value="mauvais"> Mauvais</label>
                        <label class="control-option"><input type="radio" name="ext_body" value="N/A"> N/A</label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- CONTRÔLE ÉTAT INTÉRIEUR ET ACCESSOIRES -->
        <div class="control-section">
            <h2>CONTRÔLE ÉTAT INTÉRIEUR ET ACCESSOIRES</h2>
            <div class="control-grid">
                <div class="control-item">
                    <div class="control-label">Triangle</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="int_triangle" value="oui" required> Oui</label>
                        <label class="control-option"><input type="radio" name="int_triangle" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="int_triangle" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Cric</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="int_jack" value="oui" required> Oui</label>
                        <label class="control-option"><input type="radio" name="int_jack" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="int_jack" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Manivelle/Clé</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="int_wrench" value="oui" required> Oui</label>
                        <label class="control-option"><input type="radio" name="int_wrench" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="int_wrench" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Gilet</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="int_vest" value="oui" required> Oui</label>
                        <label class="control-option"><input type="radio" name="int_vest" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="int_vest" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Tapis</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="int_mats" value="oui" required> Oui</label>
                        <label class="control-option"><input type="radio" name="int_mats" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="int_mats" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Extincteur</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="int_extinguisher" value="oui" required> Oui</label>
                        <label class="control-option"><input type="radio" name="int_extinguisher" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="int_extinguisher" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Trousse de secours</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="int_first_aid" value="oui" required> Oui</label>
                        <label class="control-option"><input type="radio" name="int_first_aid" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="int_first_aid" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Rétroviseur intérieur</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="int_mirror" value="oui" required> Oui</label>
                        <label class="control-option"><input type="radio" name="int_mirror" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="int_mirror" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Pare-soleil</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="int_sunshade" value="oui" required> Oui</label>
                        <label class="control-option"><input type="radio" name="int_sunshade" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="int_sunshade" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Autoradio</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="int_radio" value="oui" required> Oui</label>
                        <label class="control-option"><input type="radio" name="int_radio" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="int_radio" value="N/A"> N/A</label>
                    </div>
                </div>
                <div class="control-item">
                    <div class="control-label">Propreté</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="int_cleanliness" value="oui" required> Oui</label>
                        <label class="control-option"><input type="radio" name="int_cleanliness" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="int_cleanliness" value="N/A"> N/A</label>
                    </div>
                </div>
                <!-- Champs spécifiques aux motos -->
                <div class="control-item motorcycle-only">
                    <div class="control-label">Casque</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="int_helmet" value="oui"> Oui</label>
                        <label class="control-option"><input type="radio" name="int_helmet" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="int_helmet" value="N/A" checked> N/A</label>
                    </div>
                </div>
                <div class="control-item motorcycle-only">
                    <div class="control-label">Top-case</div>
                    <div class="control-options">
                        <label class="control-option"><input type="radio" name="int_topcase" value="oui"> Oui</label>
                        <label class="control-option"><input type="radio" name="int_topcase" value="non"> Non</label>
                        <label class="control-option"><input type="radio" name="int_topcase" value="N/A" checked> N/A</label>
                    </div>
                </div>
            </div>
            
            <div class="control-item">
                <div class="control-label">Observations complémentaires :</div>
                <div class="info-value" style="width: 100%;">
                    <textarea name="additional_observations" id="additional_observations" style="width: 100%; height: 60px;"></textarea>
                </div>
            </div>
        </div>
        
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-upload">
                    <label for="driver_signature">Signature du chauffeur :</label>
                    <input type="file" name="driver_signature" id="driver_signature" accept="image/*">
                </div>
                <div class="signature-line">Signature du chauffeur</div>
            </div>
            <div class="signature-box">
                <div class="signature-upload">
                    <label for="manager_signature">Signature du responsable :</label>
                    <input type="file" name="manager_signature" id="manager_signature" accept="image/*">
                </div>
                <div class="signature-line">Signature du responsable hiérarchique</div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn">Enregistrer</button>
            <button type="button" id="print-btn" class="btn">Imprimer</button>
            <a href="{{ route('admin.assignments.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
    
    <!-- Formulaire pour télécharger la fiche signée -->
    <div style="margin-top: 30px; display: none;" id="upload-signed-form-container">
        <h3>Télécharger la fiche signée</h3>
        <form id="upload-form" method="POST" action="/vehicle-handover/upload-signed" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="handover_id" value="{{ $handoverId ?? '' }}">
            <div style="margin-bottom: 10px;">
                <label for="signed_form">Fichier scanné de la fiche signée :</label>
                <input type="file" name="signed_form" id="signed_form" accept="application/pdf,image/*" required>
            </div>
            <button type="submit" class="btn">Télécharger</button>
        </form>
    </div>
</body>
</html>

