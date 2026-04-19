<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class TPenjualan extends Model
{
    protected $table = 't_penjualan';
    protected $primaryKey = 'penjualan_id';
    protected $fillable = ['user_id', 'penjualan_tanggal', 'pembeli', 'penjualan_kode'];

    protected $casts = ['penjualan_tanggal' => 'datetime',];

    public function user(): BelongsTo
    {
        return $this->belongsTo(MUser::class, 'user_id', 'user_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(TPenjualanDetail::class, 'penjualan_id', 'penjualan_id');
    }

    public function getTotalAttribute()
    {
        return $this->details()->sum(DB::raw('harga * jumlah'));
    }
}
