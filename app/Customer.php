<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ["dni", "id_reg", "id_com",  "email", "name", "last_name", "address", "date_reg", "status"];

    protected $hidden = ['created_at', 'updated_at'];
}
