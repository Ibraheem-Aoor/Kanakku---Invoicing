<?php

namespace Kanakku\Models;

use Kanakku\Models\Address;
use Illuminate\Database\Eloquent\Model;
use Kanakku\Models\User;
use Kanakku\Models\CompanySetting;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model implements HasMedia
{
    use InteractsWithMedia;

    use HasFactory;

    protected $fillable = ['name', 'logo', 'unique_hash'];

    protected $appends = ['logo', 'logo_path'];

    public function getLogoPathAttribute()
    {
        $logo = $this->getMedia('logo')->first();

        $isSystem = FileDisk::whereSetAsDefault(true)->first()->isSystem();

        if ($logo) {
            if ($isSystem) {
                return $logo->getPath();
            } else {
                return $logo->getFullUrl();
            }
        }

        return null;
    }

    public function getLogoAttribute()
    {
        $logo = $this->getMedia('logo')->first();

        if ($logo) {
            return $logo->getFullUrl();
        }

        return null;
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function settings()
    {
        return $this->hasMany(CompanySetting::class);
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }
}
