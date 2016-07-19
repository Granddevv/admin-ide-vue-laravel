<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class MallaAcademica extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'malla_academica';

    protected $fillable = ['id', 'codigo_materia', 'nombre_materia', 'semestre', 'horas', 'estado'];

}
