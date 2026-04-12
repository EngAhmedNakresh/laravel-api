<?php

namespace App\Models;

use App\Support\PublicAssetUrl;
use App\Traits\HasLocalizedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, HasLocalizedAttributes, SoftDeletes;

    protected $fillable = [
        'name_en',
        'name_ar',
        'specialization_en',
        'specialization_ar',
        'bio_en',
        'bio_ar',
        'image',
    ];

    protected $appends = ['image_url'];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return PublicAssetUrl::from($this->image);
    }
}
