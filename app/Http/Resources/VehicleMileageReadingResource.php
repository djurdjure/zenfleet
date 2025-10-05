<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * VehicleMileageReadingResource - API Resource for mileage readings
 *
 * Formats mileage reading data for API responses with:
 * - Formatted dates and values
 * - Related data (vehicle, user, organization)
 * - Computed fields (consistency, difference)
 * - Conditional fields based on permissions
 *
 * @version 1.0-Enterprise
 */
class VehicleMileageReadingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'vehicle_id' => $this->vehicle_id,

            // 📊 Mileage Data
            'mileage' => $this->mileage,
            'formatted_mileage' => $this->formatted_mileage,

            // 📅 Dates
            'recorded_at' => $this->recorded_at->toIso8601String(),
            'recorded_at_human' => $this->recorded_at->diffForHumans(),
            'recorded_at_formatted' => $this->recorded_at->format('d/m/Y H:i'),

            // 🔧 Recording Method
            'recording_method' => $this->recording_method,
            'is_manual' => $this->is_manual,
            'is_automatic' => $this->is_automatic,

            // 👤 Recorded By
            'recorded_by_id' => $this->recorded_by_id,
            'recorded_by' => $this->whenLoaded('recordedBy', function () {
                return [
                    'id' => $this->recordedBy->id,
                    'name' => $this->recordedBy->name,
                    'email' => $this->recordedBy->email,
                ];
            }),

            // 🚗 Vehicle Data
            'vehicle' => $this->whenLoaded('vehicle', function () {
                return [
                    'id' => $this->vehicle->id,
                    'registration_plate' => $this->vehicle->registration_plate,
                    'brand' => $this->vehicle->brand,
                    'model' => $this->vehicle->model,
                    'current_mileage' => $this->vehicle->current_mileage,
                    'formatted_current_mileage' => $this->vehicle->formatted_current_mileage,
                ];
            }),

            // 🏢 Organization
            'organization' => $this->whenLoaded('organization', function () {
                return [
                    'id' => $this->organization->id,
                    'name' => $this->organization->name,
                ];
            }),

            // 📝 Notes
            'notes' => $this->notes,

            // 📊 Computed Fields
            'statistics' => [
                'difference_from_previous' => $this->getMileageDifference(),
                'is_consistent' => $this->isConsistent(),
            ],

            // 🕐 Timestamps
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),

            // 🔐 Permissions (conditional)
            'permissions' => $this->when($request->user(), function () use ($request) {
                return [
                    'can_update' => $request->user()->can('update', $this->resource),
                    'can_delete' => $request->user()->can('delete', $this->resource),
                ];
            }),
        ];
    }
}
