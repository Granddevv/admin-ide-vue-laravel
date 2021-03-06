<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class MateriasCicloDocente extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'ciclo_materias_docente';

    protected $fillable = ['ciclo_docente', 'materia', 'id', 'activo'];


    public function materiaDetail(){
        return $this->belongsTo(MallaAcademica::class, 'materia', 'id');
    }

    public function materiaDetailAll(){
        return $this->belongsTo(MallaAcademica::class, 'materia', 'id');
    }

    public function docenteDetail(){
        return $this->belongsTo(CicloDocentes::class, 'ciclo_docente')->with('docenteDetail');
    }

    public function horarioMateriaDocente(){
        return $this->hasOne(HorariosCursos::class, 'ciclo_materia_docente', 'id');
    }

    public function horariosMateriaDocente(){
        return $this->hasMany(HorariosCursos::class, 'ciclo_materia_docente', 'id');
    }

    public function horarioDetalleMateriasDocente()
    {
        /*
        return DB::select("select cmd.id id_ciclo_materia_docente,
      cd.docente id_docente,
      d.nombres,
      d.apellidos,
      d.abreviatura,
      d.tipo_contrato,
      d.identificacion,
      ma.id id_materia,
      ma.nombre_materia,
      hc.dia,
      hc.hora_inicio,
      hc.hora_fin,
      hc.num_horas
  from ciclo_materias_docente cmd,
        ciclo_docentes cd,
        docentes d,
        malla_academica ma,
        horarios_cursos hc
  where cmd.id = $this->id
    AND cd.docente = d.id
  AND cmd.ciclo_docente = cd.id
  and ma.id = cmd.materia
  and hc.ciclo_materia_docente = cmd.id;");
        */
        return DB::select("select cmd.id id_ciclo_materia_docente,
      cd.docente id_docente,
      d.nombres,
      d.apellidos,
      d.abreviatura,
      d.tipo_contrato,
      d.identificacion,
      ma.id id_materia,
      ma.nombre_materia,
      hc.dia,
      hc.hora_inicio,
      hc.hora_fin,
      hc.num_horas,
      js.catalogo_semestre semestre,
      js.id ciclo_jornada_semestre
  from ciclo_materias_docente cmd,
        ciclo_docentes cd,
        docentes d,
        malla_academica ma,
        jornadas_semestres js,
        horarios_cursos hc
  where cmd.id = $this->id
    AND cd.docente = d.id
  AND cmd.ciclo_docente = cd.id
  and ma.id = cmd.materia
    and hc.ciclo_jornada_semestre = js.id
  and hc.ciclo_materia_docente = cmd.id");
    }

}
