<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class HorariosCursos extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'horarios_cursos';

    protected $fillable = ['id','ciclo_materia_docente','ciclo_jornada_semestre','dia','hora_inicio','hora_fin'];

    public function materiasDocente(){
        return $this->hasMany(MateriasDocente::class,'id', 'ciclo_materia_docente');
    }

    public function jornadaSemestre(){
        return $this->hasMany(JornadasSemestre::class,'id', 'ciclo_jornada_semestre');
    }

}
