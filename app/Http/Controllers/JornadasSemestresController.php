<?php

namespace App\Http\Controllers;

use App\Entities\Ciclo;
use App\Entities\CicloDocentes;
use App\Entities\HorariosCursos;
use App\Entities\JornadasSemestre;
use App\Entities\MateriasCicloDocente;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\JornadasSemestreCreateRequest;
use App\Http\Requests\JornadasSemestreUpdateRequest;
use App\Repositories\JornadasSemestreRepository;
use App\Validators\JornadasSemestreValidator;


class JornadasSemestresController extends Controller
{

    /**
     * @var JornadasSemestreRepository
     */
    protected $repository;

    /**
     * @var JornadasSemestreValidator
     */
    protected $validator;

    public function __construct(JornadasSemestreRepository $repository, JornadasSemestreValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->skipPresenter();

        $perPage = request()->has('per_page') ? (int) request()->per_page :10 ;

        if (request()->has('sort')) {
            list($sortCol, $sortDir) = explode('|', request()->sort);
            $jornadasemestre = $this->repository
                ->with('semestre')
                ->with('aula')
                ->with('jornada')
                ->with('descripcionCiclo')
                ->orderBy('catalogo_semestre', $sortDir)
                ->paginate($perPage);
        } else {
            $jornadasemestre = $this->repository
                ->with('semestre')
                ->with('aula')
                ->with('jornada')
                ->with('descripcionCiclo')
                ->orderBy('catalogo_semestre', 'asc')
                ->paginate($perPage);
        }

        return response()->json($jornadasemestre);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  JornadasSemestreCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try {

            $cicloVigente = CiclosController::currentCiclo();

            $data = $request->only(['catalogo_semestre', 'catalogo_jornada', 'catalogo_aula']);

            $this->validator->with($data)->passesOrFail(ValidatorInterface::RULE_CREATE);

            $data['ciclo'] = $cicloVigente->id;

            $existe = JornadasSemestre::where($data)->get()->toArray();

            if(array_has($existe, 0)){
                return response()->json([
                    'error'   => true,
                    'message' => 'Ya se ha registrado esta jornada a este semestre con el mismo curso!'
                ], 403);
            }

            $jornadasSemestre = $this->repository->create($data);

            return response()->json($jornadasSemestre);

        } catch (ValidatorException $e) {
            return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jornadasSemestre = $this->repository->find($id);

        return response()->json($jornadasSemestre);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $jornadasSemestre = $this->repository->find($id);

        return view('jornadasSemestres.edit', compact('jornadasSemestre'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  JornadasSemestreUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     */
    public function update(Request $request, $id)
    {

        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $jornadasSemestre = $this->repository->update($id, $request->all());

            $response = [
                'message' => 'JornadasSemestre updated.',
                $jornadasSemestre->toArray(),
            ];

            return response()->json($response);

        } catch (ValidatorException $e) {

            return response()->json([
                'error'   => true,
                'message' => $e->getMessageBag()
            ]); 
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        return response()->json([
            'message' => 'JornadasSemestre deleted.',
            'deleted' => $deleted,
        ]);
    }


    /**
     * @param $id
     * @return mixed
     * Method Get
     */
    public function horarioJornadaSemestre($id)
    {
        //falta escribir el codigo
        $data = JornadasSemestre::with('descripcionCiclo')
            ->with('aula')
            ->with('jornada')
            ->with('semestre')
            ->with('materiasNormalesSemestre')
            ->with('materiasEspecialesSemestre')
            ->with('horario')
            ->find($id);

        $horario = HorariosCursos::where('ciclo_jornada_semestre', $id)
            ->orderBy('id')
            ->orderBy('dia')
            ->orderBy('hora_inicio')
            ->with('materiaDocente')
            ->get();

        return response()->json(array(
                'data' => $data,
                'materias_docentes_disponibles' => $data->materiasDocentesQry(),
                'horario' => $horario
            )
        );

    }

    /**
     * @param $id
     * Method Post
     * Guarda un horario
     */
    public function saveHorarioJornadaSemestre(Request $request, $id){
        try{

            $request->only('horario');
            $data = $request['horario'];
            //1.- validar los datos que se reciben
            $hasError = $this->validaDocentes($data);
            if($hasError){
                return response()->json([
                    'error'   => true,
                    'message' => $hasError
                ], 403);
            }
            //2.- Eliminar los registros existentes para el id
            $delete = HorariosCursos::where('ciclo_jornada_semestre', $id)->delete();
            //3.- Hacer un bulk insert
            $resp = DB::table('horarios_cursos')->insert($data);

            return response()->json(array('data' => $resp, 'removes' => $delete));

        }catch (\Exception $e) {
            return response()->json([
                'error'   => true,
                'message' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine()
            ], 500);
        }

    }



    public function dataHorarioValidator(){
        $horaIni = Carbon::create(2000, 1, 1, 18, 00);
        $horaFin = Carbon::create(2000, 1, 1, 22, 40);
        return $horaIni->diffInHours($horaFin) . 'H' . $horaIni->diffInMinutes($horaFin);
    }

    public function validaDocentes($data){
        foreach ($data as $item => $val) {
            $materiaDocente = MateriasCicloDocente::find($val['ciclo_materia_docente']);
            $materiasDocente = MateriasCicloDocente::where('ciclo_docente', $materiaDocente->ciclo_docente)->get();
            foreach ($materiasDocente as $itemMat => $valMat){
                $tmpModel = MateriasCicloDocente::find($valMat['id']);
                $horario = $tmpModel->horarioDetalleMateriasDocente();
                foreach ($horario as $itemHorario => $valHorario) {
                    $fIniHorario = Carbon::createFromFormat('Y-m-d H:i', '2000-01-01 ' . $valHorario->hora_inicio);
                    $fFinHorario = Carbon::createFromFormat('Y-m-d H:i', '2000-01-01 ' . $valHorario->hora_fin);
                    $flagIni = Carbon::createFromFormat('Y-m-d H:i', '2000-01-01 ' . $val['hora_inicio'])->between($fIniHorario,$fFinHorario);
                    $flagFin = Carbon::createFromFormat('Y-m-d H:i', '2000-01-01 ' . $val['hora_fin'])->between($fIniHorario,$fFinHorario);
                    if(($flagFin || $flagIni ) && $val['dia'] == $valHorario->dia){
                        return 'No se guardaron los cambios. La hora que trata de asignar a ' . $valHorario->abreviatura . '. ' . $valHorario->nombres .' '. $valHorario->apellidos .' choca con la materia ' . $valHorario->nombre_materia . ' (' . $valHorario->dia . ' de ' .$valHorario->hora_inicio . ' a ' . $valHorario->hora_fin . ' | ' . $valHorario->semestre;
                    }
                }
            }
            return false;
        }
    }


}
