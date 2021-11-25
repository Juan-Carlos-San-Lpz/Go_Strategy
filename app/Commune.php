<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    protected $fillable = ["id_com", "id_reg", "description",  "status"];
}
