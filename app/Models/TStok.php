<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TStok extends Model
{
    use SoftDeletes;
    protected $table = 't_stok';
    protected $primaryKey = 'stok_id';
    protected $fillable = ['barang_id', 'supplier_id', 'user_id', 'stok_jumlah', 'stok_tanggal'];

    protected $casts = ['stok_tanggal' => 'datetime',];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(MBarang::class, 'barang_id', 'barang_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(MSupplier::class, 'supplier_id', 'supplier_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(MUser::class, 'user_id', 'user_id');
    }
}
