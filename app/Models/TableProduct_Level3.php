<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableProduct_Level3 extends Model
{
    use HasFactory;

    protected $table='table_product_level3';
    protected $primaryKey='id';
    protected  $guarded=[];


    public function products_level1()
    {
        return $this->hasMany(related:TableProduct_Level1::class,foreignKey:'id_level1',localKey:'id');
    }

    public function products_level2()
    {
        return $this->hasMany(related:TableProduct_Level2::class,foreignKey:'id_level2',localKey:'id');
    }

}