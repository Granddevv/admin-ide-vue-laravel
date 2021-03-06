<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class CicloDocentes extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'ciclo_docentes';

    protected $fillable = ['id', 'ciclo', 'docente'];

    public function cicloDetail(){
        return $this->hasOne(Ciclo::class, 'id', 'ciclo');
    }

    public function docenteDetail(){
        return $this->hasOne(Docente::class, 'id', 'docente');
    }

    public function materiasDocenteCiclo(){
        return $this->hasMany(MateriasCicloDocente::class, 'ciclo_docente', 'id') //se agrego el whereHas
            ->whereHas('materiaDetail', function($q){
                $q->where('activo', true);
            })->with('materiaDetail')->with('horariosMateriaDocente');
    }

    public function cargaDistributiva(){
        return $this->hasMany(HorariosDocentes::class, 'ciclo_docente', 'id')->with('distributivo');
    }
    
}
