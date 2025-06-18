<?php

namespace App\Http\Controllers\Admin\Handover;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Handover\VehicleHandoverForm;
use App\Models\Handover\VehicleHandoverDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class VehicleHandoverController extends Controller
{
    /**
     * Display a listing of the handover forms.
     */
    public function index()
    {
        $handoverForms = VehicleHandoverForm::with(['assignment.vehicle', 'assignment.driver'])
            ->where('is_latest_version', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.vehicle-handovers.index', compact('handoverForms'));
    }

    /**
     * Show the form for creating a new handover form.
     */
    public function create(Assignment $assignment)
    {
        return view("admin.handovers.vehicles.create", compact("assignment"));
    }

     

    /**
     * Store a newly created handover form in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'issue_date' => 'required|date',
            'assignment_reason' => 'required|string|max:255',
            'current_mileage' => 'required|integer',
            'general_observations' => 'nullable|string',
            'additional_observations' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create the handover form
            $handoverForm = VehicleHandoverForm::create([
                'assignment_id' => $validated['assignment_id'],
                'issue_date' => $validated['issue_date'],
                'assignment_reason' => $validated['assignment_reason'],
                'current_mileage' => $validated['current_mileage'],
                'general_observations' => $validated['general_observations'],
                'additional_observations' => $validated['additional_observations'],
                'is_latest_version' => true,
            ]);
            
            // Mark this form as the latest version
            $handoverForm->markAsLatestVersion();
            
            // Process all the control items
            $this->processControlItems($request, $handoverForm);
            
            DB::commit();
            
            return redirect()->route('admin.vehicle-handovers.show', $handoverForm)
                ->with('success', 'Fiche de remise créée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création de la fiche de remise: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified handover form.
     */
    public function show(VehicleHandoverForm $vehicleHandover)
    {
        $vehicleHandover->load(['assignment.vehicle', 'assignment.driver', 'details']);
        
        // Group details by category for easier display
        $detailsByCategory = $vehicleHandover->details->groupBy('category');
        
        return view('admin.vehicle-handovers.show', compact('vehicleHandover', 'detailsByCategory'));
    }

    /**
     * Show the form for editing the specified handover form.
     */
    public function edit(VehicleHandoverForm $vehicleHandover)
    {
        $vehicleHandover->load(['assignment.vehicle', 'assignment.driver', 'details']);
        
        // Convert details to a more usable format for the form
        $detailsMap = [];
        foreach ($vehicleHandover->details as $detail) {
            $detailsMap[$detail->item] = $detail->status;
        }
        
        return view('admin.vehicle-handovers.edit', compact('vehicleHandover', 'detailsMap'));
    }

    /**
     * Update the specified handover form in storage.
     */
    public function update(Request $request, VehicleHandoverForm $vehicleHandover)
    {
        // Validate the request
        $validated = $request->validate([
            'issue_date' => 'required|date',
            'assignment_reason' => 'required|string|max:255',
            'current_mileage' => 'required|integer',
            'general_observations' => 'nullable|string',
            'additional_observations' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Update the handover form
            $vehicleHandover->update([
                'issue_date' => $validated['issue_date'],
                'assignment_reason' => $validated['assignment_reason'],
                'current_mileage' => $validated['current_mileage'],
                'general_observations' => $validated['general_observations'],
                'additional_observations' => $validated['additional_observations'],
            ]);
            
            // Delete existing details
            $vehicleHandover->details()->delete();
            
            // Process all the control items
            $this->processControlItems($request, $vehicleHandover);
            
            DB::commit();
            
            return redirect()->route('admin.vehicle-handovers.show', $vehicleHandover)
                ->with('success', 'Fiche de remise mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour de la fiche de remise: ' . $e->getMessage());
        }
    }

    /**
     * Upload a signed handover form.
     */
    public function uploadSigned(Request $request, VehicleHandoverForm $vehicleHandover)
    {
        $request->validate([
            'signed_form' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);
        
        try {
            // Delete old file if exists
            if ($vehicleHandover->signed_form_path) {
                Storage::disk('public')->delete($vehicleHandover->signed_form_path);
            }
            
            // Store the new file
            $path = $request->file('signed_form')->store('handover_forms', 'public');
            
            // Update the handover form
            $vehicleHandover->update([
                'signed_form_path' => $path,
            ]);
            
            return redirect()->route('admin.vehicle-handovers.show', $vehicleHandover)
                ->with('success', 'Fiche de remise signée téléchargée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors du téléchargement de la fiche signée: ' . $e->getMessage());
        }
    }

    /**
     * Process all control items from the request and save them as details.
     */
    private function processControlItems(Request $request, VehicleHandoverForm $handoverForm)
    {
        // Process papers control
        $this->saveControlCategory($request, $handoverForm, 'papers', [
            'papers_registration' => 'Carte Grise',
            'papers_insurance' => 'Assurance',
            'papers_sticker' => 'Vignette',
            'papers_technical_control' => 'Contrôle technique',
            'papers_fuel_card' => 'Carte Carburant',
            'papers_circulation_permit' => 'Permis de circuler',
        ]);
        
        // Process tires control
        $this->saveControlCategory($request, $handoverForm, 'pneumatique', [
            'tire_front_left' => 'Roue AV Gauche',
            'tire_front_right' => 'Roue AV Droite',
            'tire_rear_left' => 'Roue AR Gauche',
            'tire_rear_right' => 'Roue AR Droite',
            'tire_spare' => 'Roue de Secours',
            'tire_hubcaps' => 'Enjoliveurs',
        ]);
        
        // Process exterior control
        $this->saveControlCategory($request, $handoverForm, 'exterieur', [
            'ext_windows' => 'Vitres',
            'ext_windshield' => 'Pare-brise',
            'ext_mirror_left' => 'Rétroviseur Gauche',
            'ext_mirror_right' => 'Rétroviseur Droit',
            'ext_locks' => 'Verrouillage',
            'ext_handles' => 'Poignées',
            'ext_strips' => 'Baguettes',
            'ext_front_lights' => 'Feux avant',
            'ext_rear_lights' => 'Feux arrières',
            'ext_wipers' => 'Essuie-glaces',
            'ext_body' => 'Carrosserie Générale',
        ]);
        
        // Process interior control
        $this->saveControlCategory($request, $handoverForm, 'interieur', [
            'int_triangle' => 'Triangle',
            'int_jack' => 'Cric',
            'int_wrench' => 'Manivelle/Clé',
            'int_vest' => 'Gilet',
            'int_mats' => 'Tapis',
            'int_extinguisher' => 'Extincteur',
            'int_first_aid' => 'Trousse de secours',
            'int_mirror' => 'Rétroviseur intérieur',
            'int_sunshade' => 'Pare-soleil',
            'int_radio' => 'Autoradio',
            'int_cleanliness' => 'Propreté',
        ]);
        
        // Process motorcycle specific items if applicable
        if ($request->has('vehicle_type') && $request->input('vehicle_type') === 'moto') {
            $this->saveControlCategory($request, $handoverForm, 'moto', [
                'int_helmet' => 'Casque',
                'int_topcase' => 'Top-case',
            ]);
        }
    }

    /**
     * Save a category of control items.
     */
    private function saveControlCategory(Request $request, VehicleHandoverForm $handoverForm, $category, array $items)
    {
        foreach ($items as $field => $label) {
            if ($request->has($field)) {
                VehicleHandoverDetail::create([
                    'handover_form_id' => $handoverForm->id,
                    'category' => $category,
                    'item' => $label,
                    'status' => $request->input($field),
                ]);
            }
        }
    }
}

