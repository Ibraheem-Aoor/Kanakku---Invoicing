<?php
namespace Kanakku\Models;

use Kanakku\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Kanakku\Models\User;
use Kanakku\Models\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;
    const BILLING_TYPE = 'billing';
    const SHIPPING_TYPE = 'shipping';

    protected $guarded = ['id'];

    public function getCountryNameAttribute()
    {
        $name = $this->country ? $this->country->name : null;

        return $name;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
