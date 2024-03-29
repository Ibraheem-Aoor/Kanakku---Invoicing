<?php
namespace Kanakku\Models;

use Illuminate\Database\Eloquent\Model;
use Kanakku\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['path', 'view', 'name'];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getPathAttribute($value)
    {
        return url($value);
    }
}
