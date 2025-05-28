<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EducationalArea;
use App\Models\SubjectGroup;
use App\Models\PlacementRecord;
use Carbon\Carbon;

class PublicSearchController extends Controller
{
    /**
     * Display the search page and search results.
     */
    public function index(Request $request)
    {
        $educationalAreas = EducationalArea::orderBy('name')->get();
        $subjectGroupsForFilter = SubjectGroup::orderBy('name')->get();

        // Generate academic years (example: 5 years back + current + 2 years forward)
        $currentThaiYear = Carbon::now()->year + 543;
        $academicYears = [];
        for ($i = $currentThaiYear + 2; $i >= $currentThaiYear - 5; $i--) {
            $academicYears[] = $i;
        }

        // Query placement records
        $query = PlacementRecord::with(['educationalArea', 'subjectGroups']) // Eager load subjectGroups
                                ->orderBy('announcement_date', 'desc')
                                ->orderBy('academic_year', 'desc')
                                ->orderBy('round_number', 'asc');

        // Apply filters
        if ($request->filled('educational_area_id')) {
            $query->where('educational_area_id', $request->educational_area_id);
        }
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }
        // Filter by subject_group_id (checks if the placement record is associated with this subject group)
        if ($request->filled('subject_group_id')) {
            $query->whereHas('subjectGroups', function ($q) use ($request) {
                $q->where('subject_groups.id', $request->subject_group_id);
            });
        }

        $placements = $query->paginate(15)->withQueryString(); // Paginate and keep query string

        return view('frontend.search', compact(
            'educationalAreas',
            'subjectGroupsForFilter',
            'academicYears',
            'placements',
            'request' // Pass request to view for old() and selected values
        ));
    }

    /**
     * Display the details of a specific placement record.
     * Uses Route Model Binding.
     */
    public function showDetails(PlacementRecord $placementRecord)
    {
        // Eager load necessary relationships if not already loaded or to ensure they are fresh
        $placementRecord->load(['educationalArea', 'subjectGroups', 'attachments']);

        // Query for other related rounds
        // (same educational area, academic year, and at least one common subject group, but different round)
        // This can be complex if "same subject groups" means an exact match of all subject groups.
        // For simplicity, we'll find records with at least one common subject group.
        // Or, you might decide to only filter by educational_area_id and academic_year for related rounds.

        $relatedRoundsQuery = PlacementRecord::where('educational_area_id', $placementRecord->educational_area_id)
                                        ->where('academic_year', $placementRecord->academic_year)
                                        ->where('id', '!=', $placementRecord->id); // Exclude current record

        // If you want to ensure at least one common subject group:
        if ($placementRecord->subjectGroups->isNotEmpty()) {
            $subjectGroupIds = $placementRecord->subjectGroups->pluck('id')->toArray();
            $relatedRoundsQuery->whereHas('subjectGroups', function ($q) use ($subjectGroupIds) {
                $q->whereIn('subject_groups.id', $subjectGroupIds);
            });
        }

        $relatedRounds = $relatedRoundsQuery->orderBy('round_number', 'asc')->get();

        return view('frontend.details', compact('placementRecord', 'relatedRounds'));
    }
}