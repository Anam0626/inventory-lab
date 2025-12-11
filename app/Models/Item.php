<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public function borrowings() {
        return $this->hasMany(Borrowing::class);
    }   
}
