<?php
namespace Kanakku\Models;

use Illuminate\Database\Eloquent\Model;
use Kanakku\Models\Estimate;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstimateTemplate extends Model
{
    use HasFactory;
    protected $fillable = ['path', 'view', 'name'];

    public function estimates()
    {
        return $this->hasMany(Estimate::class);
    }

    public function getPathAttribute($value)
    {
        return url($value);
    }
}
