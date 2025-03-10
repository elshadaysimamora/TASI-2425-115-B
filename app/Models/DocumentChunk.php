<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Pgvector\Laravel\Vector;

class DocumentChunk extends Model
{
    use HasFactory;

    protected $casts = ['embeding' => Vector::class];
    protected $guarded = [];


    // Konversi embedding ke array
    public function getEmbeddingAttribute($value)
    {
        return json_decode($value, true);
    }

    // Relasi balik ke dokumen
    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
