<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function publicResolved(Request $request)
    {
        $query = Report::query()->where('progress', 'resolved');

        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->query('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->query('date_to'));
        }

        $perPage = (int) $request->query('per_page', 12);
        $perPage = max(1, min(100, $perPage));

        $reports = $query->orderByDesc('updated_at')->paginate($perPage);
        return response()->json($reports);
    }

	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'submitter_name' => 'required|string|max:255',
			'age' => 'required|integer|min:0|max:120',
			'gender' => 'required|string|max:20',
			'address' => 'required|string',
			'type' => 'required|in:infrastructure,sanitation,community_welfare,behavoural_concerns',
			'description' => 'required|string',
			'photos' => 'sometimes|array',
			'photos.*' => 'file|image|max:5120',
		]);

		if ($validator->fails()) {
			return response()->json([
				'message' => 'Validation failed',
				'errors' => $validator->errors(),
			], 422);
		}

		$user = $request->user();

		$storedPhotos = [];
		if ($request->hasFile('photos')) {
			foreach ($request->file('photos') as $file) {
				$path = $file->store('reports', 'public');
				$storedPhotos[] = Storage::url($path); // e.g., /storage/reports/...
			}
		}

		$reportId = $this->generateReportId();

		$report = Report::create([
			'report_id' => $reportId,
			'user_id' => $user->id,
			'submitter_name' => $request->input('submitter_name'),
			'age' => $request->input('age'),
			'gender' => $request->input('gender'),
			'address' => $request->input('address'),
			'type' => $request->input('type'),
			'photos' => $storedPhotos,
			'description' => $request->input('description'),
			'progress' => 'pending',
			'date_generated' => now(),
		]);

		return response()->json([
			'message' => 'Report submitted successfully',
			'report' => $report,
		], 201);
	}

	public function index(Request $request)
	{
		$user = $request->user();
		$role = $user->role ?? 'citizen';
		if (!in_array($role, ['admin', 'mayor'])) {
			return response()->json(['message' => 'Forbidden'], 403);
		}

		$query = Report::query();

		if ($request->filled('status')) {
			$query->where('progress', $request->query('status'));
		}
		if ($request->filled('type')) {
			$query->where('type', $request->query('type'));
		}
		if ($request->filled('date_from')) {
			$query->whereDate('created_at', '>=', $request->query('date_from'));
		}
		if ($request->filled('date_to')) {
			$query->whereDate('created_at', '<=', $request->query('date_to'));
		}

		$perPage = (int) $request->query('per_page', 10);
		$perPage = max(1, min(100, $perPage));

		$reports = $query->orderByDesc('created_at')->paginate($perPage);
		return response()->json($reports);
	}

	public function mine(Request $request)
	{
		$reports = Report::where('user_id', $request->user()->id)
			->orderByDesc('created_at')
			->get();
		return response()->json(['reports' => $reports]);
	}

	public function show(Report $report, Request $request)
	{
		$user = $request->user();
		$role = $user->role ?? 'citizen';
		if ($report->user_id !== $user->id && !in_array($role, ['admin', 'mayor'])) {
			return response()->json(['message' => 'Forbidden'], 403);
		}
		return response()->json(['report' => $report]);
	}

	public function updateProgress(Report $report, Request $request)
	{
		$user = $request->user();
		$role = $user->role ?? 'citizen';
		if (!in_array($role, ['admin', 'mayor'])) {
			return response()->json(['message' => 'Forbidden'], 403);
		}

		$validator = Validator::make($request->all(), [
			'progress' => 'required|in:pending,in_review,assigned,resolved,rejected',
		]);
		if ($validator->fails()) {
			return response()->json([
				'message' => 'Validation failed',
				'errors' => $validator->errors(),
			], 422);
		}

		$report->progress = $request->input('progress');
		$report->save();

		return response()->json([
			'message' => 'Report updated successfully',
			'report' => $report,
		]);
	}

	public function uploadResolutionPhotos(Report $report, Request $request)
	{
		$user = $request->user();
		$role = $user->role ?? 'citizen';
		if (!in_array($role, ['admin', 'mayor'])) {
			return response()->json(['message' => 'Forbidden'], 403);
		}

		$validator = Validator::make($request->all(), [
			'photos' => 'required|array|min:1|max:5',
			'photos.*' => 'file|image|max:5120',
			'notes' => 'sometimes|string|nullable',
			'mark_resolved' => 'sometimes|boolean',
		]);
		if ($validator->fails()) {
			return response()->json([
				'message' => 'Validation failed',
				'errors' => $validator->errors(),
			], 422);
		}

		$stored = is_array($report->resolution_photos) ? $report->resolution_photos : [];
		if ($request->hasFile('photos')) {
			foreach ($request->file('photos') as $file) {
				$path = $file->store('reports/resolutions', 'public');
				$stored[] = Storage::url($path);
			}
		}

		$report->resolution_photos = $stored;
		$report->resolution_notes = $request->input('notes', $report->resolution_notes);
		$report->resolved_by = $report->resolved_by ?: $user->id;
		$report->resolved_at = $report->resolved_at ?: now();
		if ($request->boolean('mark_resolved')) {
			$report->progress = 'resolved';
		}
		$report->save();

		return response()->json([
			'message' => 'Resolution photos uploaded',
			'report' => $report,
		]);
	}

	private function generateReportId(): string
	{
		do {
			$candidate = 'RPR-' . date('Ymd') . '-' . strtoupper(Str::random(6));
		} while (Report::where('report_id', $candidate)->exists());
		return $candidate;
	}
}
