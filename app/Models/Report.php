<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
	use HasFactory;

	protected $fillable = [
		'report_id',
		'user_id',
		'submitter_name',
		'age',
		'gender',
		'address',
		'type',
		'photos',
		'resolution_photos',
		'description',
		'progress',
		'resolved_by',
		'resolved_at',
		'resolution_notes',
		'date_generated',
	];

	protected $casts = [
		'photos' => 'array',
		'resolution_photos' => 'array',
		'date_generated' => 'datetime',
		'resolved_at' => 'datetime',
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
