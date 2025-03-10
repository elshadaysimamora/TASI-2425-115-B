<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relasi dengan chunks
    public function chunks()
    {
        return $this->hasMany(DocumentChunk::class, 'document_id');
    }
}
