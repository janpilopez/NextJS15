<?php

namespace app\controllers;

use Yii;
use app\models\SysMedConsultaMedica;
use app\models\search\SysMedConsultaMedicaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\SysEmpresa;
use app\models\User;
use app\models\sysMedTurnoMedico;
use app\models\SysRrhhEmpleados;
use app\models\SysMedFichaMedica;
use Mpdf\Mpdf;
use app\models\SysMedPatologia;
use kartik\mpdf\Pdf;

/**
 * ConsultaMedicaController implements the CRUD actions for SysMedConsultaMedica model.
 */
class ConsultaMedicaController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'ghost-access'=> [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
        ];
    }
    
    /**
     * Lists all SysMedConsultaMedica models.
     * @return mixed
     */
    
    public function actionIndex()
    {
        $this->layout = '@app/views/layouts/main_emplados';
        $searchModel = new SysMedConsultaMedicaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Displays a single SysMedConsultaMedica model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        
        $db =  $_SESSION['db'];
        
        $model = $this->findModel($id);
        
        $id_categoria_patologia = '';
        
        $id_patologia = '';
        
        $patologia = SysMedPatologia::find()->where(['id'=> $model->id_sys_med_patologia])->one();
        
        if($patologia):
        
             $id_categoria_patologia = $patologia->id_sys_med_patologia_categoria;
             $id_patologia = $patologia->id;
        
        endif;
        
        $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
        
        $fotos =    Yii::$app->$db->createCommand("select foto_64,  foto, baze64 from sys_rrhh_empleados_foto cross apply (select foto as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$model->id_sys_rrhh_cedula}'")->queryOne();
        
        
        return $this->render('view', [
            'model' => $model ,
            'empleado'=> $empleado,
            'fotos'=> $fotos,
            'id_categoria_patologia' => $id_categoria_patologia,
            'id_patologia' => $id_patologia
        ]);
    }
    
    /**
     * Creates a new SysMedConsultaMedica model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($turno=null)
    {
        $db =  $_SESSION['db'];
        
        $id_categoria_patologia = '';
        
        $id_patologia = '';
        
        $model = new SysMedConsultaMedica();
        
        $modelTurno = sysMedTurnoMedico::find()->where(['id'=> $turno])->one();
        
        $consulta = SysMedConsultaMedica::find()->where(['id_sys_med_turno_medico' => $turno])->one();
        
        if(!$consulta):
        
                $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $modelTurno->id_sys_rrhh_cedula])->one();
                
                $model->id_sys_med_turno_medico = $modelTurno->id;
                
                $model->id_sys_rrhh_cedula = $empleado->id_sys_rrhh_cedula;
                
                $fotos =    Yii::$app->$db->createCommand("select foto_64,  foto, baze64 from sys_rrhh_empleados_foto cross apply (select foto as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$model->id_sys_rrhh_cedula}'")->queryOne();
                
                $ficha_medica = SysMedFichaMedica::find()->where(['id_sys_rrhh_cedula'=> $empleado->id_sys_rrhh_cedula])->one();
                
                $certificados = $this->getCertificadosMedicos($empleado->id_sys_rrhh_cedula);
                
                $historial_medico = $this->getHistorialMedico($empleado->id_sys_rrhh_cedula);
                
                if ($model->load(Yii::$app->request->post())) {
                    
                    $numTurno =  SysMedConsultaMedica::find()->select(['max(numero)'])->scalar() + 1;
                    
                    if($model->id_sys_med_patologia != null):
                    
                        $model->numero = $numTurno;
                        $model->usuario_enfermeria = Yii::$app->user->username;
                        $model->nota_enfermera = strtoupper($model->nota_enfermera);
                        
                        $model->notas_evolucion = strtoupper($model->notas_evolucion);
                        $model->prescripcion =  strtoupper($model->prescripcion);
                        
                        
                        
                        if($model->finalizada == 0):
                        
                            $model->usuario_medico = Yii::$app->user->username;
                            $model->finalizada = 1;
                            $model->fecha_consulta = date('Ymd H:i:s');
                            $turno = sysMedTurnoMedico::findOne($model->id_sys_med_turno_medico);
                            $turno->fin_atencion = $model->fecha_consulta;
                            $turno->atendido = 1;
                            $turno->save(false);
                        
                            //Enviar notificacion;
                            
                            
                        endif;
                        
                    
                    
                    else:
                    
                    
                        $model->numero = $numTurno;
                        $model->fecha_toma_datos = date('Ymd H:i:s');
                        $model->usuario_enfermeria = Yii::$app->user->username;
                        $model->nota_enfermera = strtoupper($model->nota_enfermera);
                        $model->finalizada = 0;
                    
                    endif;
                    
                    
                    
                    
                    if($model->save(false)){
                        
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido registrados  con éxito!',
                            'positonY' => 'top','positonX' => 'right']);
                    }
                    else{
                        
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'danger','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                            'positonY' => 'top','positonX' => 'right']);
                    }
                    return $this->redirect('index');
                }
                
                return $this->render('create', [
                    'model' => $model,
                    'modelTurno' => $modelTurno,
                    'empleado' => $empleado,
                    'fotos' => $fotos,
                    'id_categoria_patologia' => $id_categoria_patologia,
                    'id_patologia' => $id_patologia,
                    'certificados'=> $certificados,
                    'ficha_medica'=> $ficha_medica,
                    'historial_medico' => $historial_medico
                ]);
        else:
        
            return $this->redirect(['update?id='.$consulta->id]);
        
        endif;
    }
    
    /**
     * Updates an existing SysMedConsultaMedica model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionCreate2(){
        
        $model = new SysMedConsultaMedica();
        
        $empleado = new SysRrhhEmpleados();
        
        $ficha_medica = new SysMedFichaMedica();
        
        if ($model->load(Yii::$app->request->post())) {
            
            $numTurno =  SysMedConsultaMedica::find()->select(['max(numero)'])->scalar() + 1;
           
            $model->id_sys_med_turno_medico = $this->getGenerarTurno($model->id_sys_rrhh_cedula, $model->tipo_atencion);
            $model->usuario_medico = Yii::$app->user->username;
            
            
            
            if($model->id_sys_med_patologia != null):
            
                $model->numero = $numTurno;
                $model->usuario_enfermeria = Yii::$app->user->username;
                $model->nota_enfermera = strtoupper($model->nota_enfermera);
                
                $model->notas_evolucion = strtoupper($model->notas_evolucion);
                $model->prescripcion =  strtoupper($model->prescripcion);
            
                if($model->finalizada == 0):
                    
                    $model->usuario_medico = Yii::$app->user->username;
                    $model->finalizada = 1;
                    $model->fecha_consulta = date('Ymd H:i:s');
                    $turno = sysMedTurnoMedico::findOne($model->id_sys_med_turno_medico);
                    $turno->fin_atencion = $model->fecha_consulta;
                    $turno->atendido = 1;
                    $turno->save(false);
                    
                endif;
            
            
            else:
           
                $model->numero = $numTurno;
                $model->fecha_toma_datos = date('Ymd H:i:s');
                $model->usuario_enfermeria = Yii::$app->user->username;
                $model->nota_enfermera = strtoupper($model->nota_enfermera);
                $model->finalizada = 0;
            
            endif;
            
            if($model->save(false)){
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido registrados  con éxito!',
                    'positonY' => 'top','positonX' => 'right']);
            }
            else{
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                    'positonY' => 'top','positonX' => 'right']);
            }
            
            return $this->redirect('index');
        }
        
        return $this->render('create2', [
            'model' => $model,
            'empleado' => $empleado,
            'ficha_medica'=> $ficha_medica
        ]);
    }
    
    public function actionUpdate($id){
        
        $db =  $_SESSION['db'];
        
        $model = $this->findModel($id);
        
        $id_categoria_patologia = '';
        
        $id_patologia = '';
        
        $patologia = SysMedPatologia::find()->where(['id'=> $model->id_sys_med_patologia])->one();
        
        if($patologia):
        
            $id_categoria_patologia = $patologia->id_sys_med_patologia_categoria;
            $id_patologia = $patologia->id;
            
        endif;
        
       
        $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
        
        $ficha_medica = SysMedFichaMedica::find()->where(['id_sys_rrhh_cedula'=> $empleado->id_sys_rrhh_cedula])->one();
        
        $fotos =    Yii::$app->$db->createCommand("select foto_64,  foto, baze64 from sys_rrhh_empleados_foto cross apply (select foto as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$model->id_sys_rrhh_cedula}'")->queryOne();
        
        $certificados = $this->getCertificadosMedicos($model->id_sys_rrhh_cedula);
        
        $historial_medico = $this->getHistorialMedico($model->id_sys_rrhh_cedula);
        
        if ($model->load(Yii::$app->request->post())) {
            
            
            if($model->id_sys_med_turno_medico != null):
            
            
                $model->notas_evolucion = strtoupper($model->notas_evolucion);
                $model->prescripcion =  strtoupper($model->prescripcion);
                
                if($model->finalizada == 0):
                
                    $model->usuario_medico = Yii::$app->user->username;
                    $model->finalizada = 1;
                    $model->fecha_consulta = date('Ymd H:i:s');
                    $turno = sysMedTurnoMedico::findOne($model->id_sys_med_turno_medico);
                    $turno->fin_atencion = $model->fecha_consulta;
                    $turno->atendido = 1;
                    $turno->save(false);
                
                endif;
            
            endif;
            
            if($model->save(false)){
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido registrados  con éxito!',
                    'positonY' => 'top','positonX' => 'right']);
            }
            else{
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                    'positonY' => 'top','positonX' => 'right']);
            }
            return $this->redirect('index');
        }
        
        return $this->render('update', [
            'model' => $model,
            'empleado'=> $empleado,
            'fotos'=> $fotos,
            'id_categoria_patologia' => $id_categoria_patologia,
            'id_patologia' => $id_patologia,
            'certificados' => $certificados,
            'ficha_medica'=> $ficha_medica,
            'historial_medico' => $historial_medico
        ]);
    }
    
    public  function  actionObtenerpatologias(){
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $out = [];
        
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                
                $categoria = $parents[0];
                
                $out = (new \yii\db\Query())
                ->select(["id", "nombre as name"])
                ->from("sys_med_patologia")
                ->where("id_sys_med_patologia_categoria = '{$categoria}'")
                ->orderBy("nombre")
                ->all(SysMedConsultaMedica::getDb());
                
                return ['output'=>$out, 'selected'=>''];
            }
        }
        return ['output'=>'', 'selected'=>''];
    }
    
    public function actionListadocie10(){
        
        $datos= [];
        $datos = (new \yii\db\Query())->select(['id', 'descripcion'])
        ->from('sys_med_cie10')
        ->where("activo=1")
        ->orderBy("descripcion")
        ->all(SysMedConsultaMedica::getDb());
        
        return json_encode($datos);
    }
    
    public  function actionDatosconsulta($id_sys_rrhh_cedula){
        
        $empleados = [];
        $img       =  file_get_contents('img/sin_foto.jpg');
        $foto      = 'data:image/jpeg;base64, '.base64_encode($img);
        $certificados = [];
        $historial_atenciones = [];
        
        $fotoemp  = (new \yii\db\Query())
        ->select(["foto","baze64"])
        ->from("sys_rrhh_empleados_foto cross apply (select foto as '*' for xml path('')) T (baze64)")
        ->where("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
        ->one(SysRrhhEmpleados::getDb());
        
        if($fotoemp['baze64'] != null):
        
        $foto =  'data:image/jpeg;base64, '.$fotoemp['baze64'];
        
        endif;
        
        $empleados = (new \yii\db\Query())->select(["id_sys_adm_cargo","tipo_sangre","genero", "estado_civil", "discapacidad", "tipo_discapacidad","por_discapacidad","ide_discapacidad","fecha_nacimiento"])
        ->from('sys_rrhh_empleados')
        ->where("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
        ->one(SysMedConsultaMedica::getDb());
        
        $ficha_medica = (new \yii\db\Query())->select([
            "enf_cardiovasculares",
            "enf_metabolicos",
            "enf_neurologicos", 
            "enf_oftalmologicos",
            "enf_auditivas", 
            "infecciones_contagiosas",
            "enf_veneras",
            "traumatismos",
            "convulsiones",
            "alergias",
            "cirugias",
            "otras_patologias",
            "ant_familiar_padres",
            "ant_familiar_madre",
            "ant_familiar_otros"
        ])
            
        ->from('sys_med_ficha_medica')
        ->where("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
        ->one(SysMedConsultaMedica::getDb());
        
        $certificados = $this->getCertificadosMedicos($id_sys_rrhh_cedula);
        $historial_atenciones = $this->getHistorialMedico($id_sys_rrhh_cedula);
        
        return json_encode(['empleados'=>$empleados, 'foto'=> $foto, 'certificados'=> $certificados, 'ficha_medica'=> $ficha_medica, 'historial_atenciones'=> $historial_atenciones]);
        
    }
    
    private  function getCertificadosMedicos($id_sys_rrhh_cedula){
        
        $arraydata = [];
        
        $datos = (new \yii\db\Query())->select([
            "tipo_ausentismo",
            "entidad_emisora",
            "tipo",
            "fecha_ini",
            "fecha_fin",
            "diagnostico"
        ])
        ->from("sys_med_certificado_medico")
        ->where("id_sys_rrhh_cedula='{$id_sys_rrhh_cedula}'")
        ->orderby("id")
        ->all(SysMedConsultaMedica::getDb());
  
       
        foreach ( $datos as $item):
                    
                    $entidad = '';
        
                    if($item['entidad_emisora'] == 'I'):
                    
                        $entidad = 'IESS';
                    
                    elseif($item['entidad_emisora'] == 'M'):
                    
                        $entidad = 'MSP';
                    
                    elseif($item['entidad_emisora'] == 'P'):
                    
                        $entidad = 'PARTICULAR';
                    
                    else:
                        $entidad = 'OTROS';
                    endif;
     
                    array_push($arraydata,  [
                        "entidad_emisora"=> $entidad, 
                        "tipo" => $item["tipo"] == "D" ? "Día": "Horas", 
                        "tipo_ausentismo" =>  $item['tipo_ausentismo'] == "E" ? "Enfermedad" : "Accidente",
                        "fecha_ini" => date('Y-m-d H:i', strtotime($item['fecha_ini'])),
                        "fecha_fin" => date('Y-m-d H:i', strtotime($item['fecha_fin'])),
                        "diagnostico" => $item["diagnostico"]
                        ]);
        
              
        endforeach;
        
        
       return $arraydata; 
        
    }
    
    private  function getConsutasMedicas($fechaini, $fechafin){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("exec dbo.ObtenerConsultasMedicas @fecha_ini = '{$fechaini}', @fecha_fin = '{$fechafin}'")->queryAll();
    }
    
    private function getHistorialMedico($id_sys_rrhh_cedula){
        
        
        return (new \yii\db\Query())->select([
            "con.id",
            "numero",
            "cast(fecha_consulta as date) fecha_consulta",
            "cast(fecha_consulta  as time) hora_consulta",
            "patologia.nombre as patologia",
        ])
        ->from("sys_med_consulta_medica con")
        ->innerJoin("sys_med_patologia patologia", "con.id_sys_med_patologia = patologia.id")
        ->where("tipo =  'N'")
        ->andWhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
        ->orderby("numero desc")
        ->all(SysMedConsultaMedica::getDb());
        
    }
    
    /**
     * Deletes an existing SysMedConsultaMedica model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        // $this->findModel($id)->delete();
        
        return $this->redirect(['index']);
    }
    
    public function actionInforme($fechaini=null, $fechafin=null){
        
        $datos = [];
        $fechaini = date('Y-m-d');
        $fechafin = date('Y-m-d');
        
        
        if(Yii::$app->request->post()):
        
        $fechaini = $_POST['fechaini'] == null ? $fechaini : $_POST['fechaini'];
        $fechafin = $_POST['fechafin'] == null ? $fechafin : $_POST['fechafin'];
        
        $datos = $this->getConsutasMedicas($fechaini, $fechafin);
        
        endif;
        
        
        return $this->render('informe', [
            'datos' => $datos,
            'fechaini' => $fechaini,
            'fechafin' => $fechafin
        ]);
        
    }
    
    public function actionInformepdf($fechaini=null, $fechafin=null){
        
        
        $ini = $fechaini == null ? date('Y-m-d') : $fechaini;
        
        $fin = $fechafin == null ? date('Y-m-d') : $fechafin;
        
        $datos = $this->getConsutasMedicas($ini, $fin);
        
        $html =  $this->renderPartial('informepdf',['datos'=> $datos, 'fechaini'=> $ini, 'fechafin'=> $fin]);

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            
            // your html content input
            'content' => $html,
            'marginTop' => 20,
            'marginBottom' => 20,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}   .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 { border:0; padding:0; margin-left:-0.00001; } .fuente_table { font-size: 8px; }',
            
            // set mPDF properties on the fly
            //'options' => ['title' => 'Solicitud de Vacaciones'],
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' =>  'Atenciones Médicas',
                'SetHeader'=>['Sistema Gestión de Nómina - Informe de Atenciones Médicas||'],
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
            ]
        ]);
        
        // return the pdf output as per the destination setting
        return $pdf->render(); 
        
    }
    
    public  function actionMorbilidad(){
        
        $anio = date('Y');
        $mes  =  date('n');
        $datos = [];
        $atencionesxArea = [];
        $atencionesxSexo = [];
        $incidentesAccidentes = [];
        $incidentesXGenero = [];
        $accidentesXGenero = [];
        
        $this->layout = '@app/views/layouts/main_emplados';
        
        if(Yii::$app->request->post()):
        
            $anio = $_POST['anio'] == null ? date('Y') : $_POST['anio'];
            $mes  = $_POST['mes'] == null ? date('n') : $_POST['mes'];
            
            $datos = $this->getIndicadoresMorbilidad($anio, $mes);
            $atencionesxArea = $this->getAtencionesXAerea($anio, $mes);
            $atencionesxSexo = $this->getAtencionesXSexo($anio, $mes);
            $incidentesAccidentes = $this->getAtencionesXIncidentesAccidentes($anio, $mes);
            $incidentesXGenero = $this->getIncidenteXGenero($anio, $mes);
            $accidentesXGenero = $this->getAccidenteXGenero($anio, $mes);
            
        endif;
        
        return $this->render('morbilidad',[
            'datos'=> $datos, 
            'incidentesAccidentes'=> $incidentesAccidentes, 
            'atencionesxSexo' => $atencionesxSexo,  
            'atencionesxArea' => $atencionesxArea, 
            'anio'=> $anio, 
            'mes'=> $mes,
            'incidentesXGenero' => $incidentesXGenero,
            'accidentesXGenero' => $accidentesXGenero
        ]) ;
    }
    
    private function getAtencionesXAerea($anio, $mes){
        
        
        $arraydata = [];
        
        $datos =  (new \yii\db\Query())
        ->select(["are.area as name",  "count(*) as y"])
        ->from("sys_med_consulta_medica con")
        ->innerJoin("sys_rrhh_empleados emp", "con.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula")
        ->innerJoin("sys_adm_cargos car", "emp.id_sys_adm_cargo = car.id_sys_adm_cargo")
        ->innerJoin("sys_adm_departamentos dep", "car.id_sys_adm_departamento = dep.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas are", "dep.id_sys_adm_area = are.id_sys_adm_area")
        ->Where("YEAR(fecha_consulta)  = {$anio} and MONTH(fecha_consulta) = {$mes}")
        ->andWhere("tipo = 'N'")
        ->groupBy("are.area")
        ->all (SysRrhhEmpleados::getDb());
        
        foreach ( $datos as $data):
        array_push($arraydata,  ["name"=> $data["name"], "y" => floatval($data["y"])]);
        endforeach;
        
        return $arraydata;
    }
    
    private function getAtencionesXSexo($anio, $mes){
        
        $arraydata = [];
        
        $color = [1 => '#FFF263',2 => '#6AF9C4'];
        
        $i = 0;
        
        $datos =  (new \yii\db\Query())
        ->select(["emp.genero as name",  "count(*) as y"])
        ->from("sys_med_consulta_medica con")
        ->innerJoin("sys_rrhh_empleados emp", "con.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula")
        ->Where("YEAR(fecha_consulta)  = {$anio} and MONTH(fecha_consulta) = {$mes}")
        ->andWhere("tipo = 'N'")
        ->groupBy("emp.genero")
        ->all (SysRrhhEmpleados::getDb());
        
        foreach ( $datos as $data):
             $i++;
             array_push($arraydata,  ["name"=> $data["name"] == "F"? "FEMENINO": "MASCULINO", "y" => floatval($data["y"]), "color"=> $color[$i]]);
        endforeach;
        
        return $arraydata;
        
    }
    
    private function getAtencionesXIncidentesAccidentes($anio, $mes){
        
        $arraydata = [];
        
        $color = [
            1 => '#50B432',
            2 => '#ED561B'
        ];
        
        $i = 0;
        
        $datos =  (new \yii\db\Query())
        ->select(["con.tipo as name",  "count(*) as y"])
        ->from("sys_med_consulta_medica con")
        ->Where("YEAR(fecha_consulta)  = {$anio} and MONTH(fecha_consulta) = {$mes}")
        ->andWhere("tipo <> 'N'")
        ->groupBy("con.tipo")
        ->all (SysRrhhEmpleados::getDb());
        
        foreach ( $datos as $data):
            $i++;
            array_push($arraydata,  ["name"=> $data["name"] == "I"? "INCIDENTES": "ACCIDENTES", "y" => floatval($data["y"]), "color"=> $color[$i]]);
        endforeach;
        
        return $arraydata;
        
    }
    
    private function getIncidenteXGenero($anio, $mes){
        
        $arraydata = [];
        
        $color = [
            1 => '#FFF263',
            2 => '#6AF9C4'
        ];
        
        $i = 0;
        
        $datos =  (new \yii\db\Query())
        ->select(["emp.genero as name",  "count(*) as y"])
        ->from("sys_med_consulta_medica con")
        ->innerJoin("sys_rrhh_empleados emp", "con.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula")
        ->Where("YEAR(fecha_consulta)  = {$anio} and MONTH(fecha_consulta) = {$mes}")
        ->andWhere("tipo = 'I'")
        ->groupBy("emp.genero")
        ->all (SysRrhhEmpleados::getDb());
        
        foreach ( $datos as $data):
            $i++;
            array_push($arraydata,  ["name"=> $data["name"] == "F"? "FEMENINO": "MASCULINO", "y" => floatval($data["y"]), "color"=> $color[$i]]);
        endforeach;
        
        return $arraydata;
    }
    
    private function getAccidenteXGenero($anio, $mes){
        
        $arraydata = [];
        
        $color = [1 => '#FFF263',2 => '#6AF9C4'];
        
        $i = 0;
        
        $datos =  (new \yii\db\Query())
        ->select(["emp.genero as name",  "count(*) as y"])
        ->from("sys_med_consulta_medica con")
        ->innerJoin("sys_rrhh_empleados emp", "con.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula")
        ->Where("YEAR(fecha_consulta)  = {$anio} and MONTH(fecha_consulta) = {$mes}")
        ->andWhere("tipo = 'A'")
        ->groupBy("emp.genero")
        ->all (SysRrhhEmpleados::getDb());
        
        foreach ( $datos as $data):
            $i++;
            array_push($arraydata,  ["name"=> $data["name"] == "F"? "FEMENINO": "MASCULINO", "y" => floatval($data["y"]), "color"=> $color[$i]]);
        endforeach;
        
        return $arraydata;
        
    }
    
    private function getIndicadoresMorbilidad($anio, $mes){
        
        $arraydata = [];
        
        $i = 0;
        $cont = 0;
        
        $datos =  (new \yii\db\Query())
        ->select(["patologia.nombre as name", "COUNT(*) as y"])
        ->from("sys_med_consulta_medica con")
        ->innerJoin("sys_med_patologia patologia", "con.id_sys_med_patologia = patologia.id")
        ->Where("YEAR(fecha_consulta)  = {$anio} and MONTH(fecha_consulta) = {$mes}")
        ->andWhere("tipo = 'N'")
        ->groupBy("patologia.nombre")
        ->orderby("count(*) desc")
        ->all(SysRrhhEmpleados::getDb());
        
        foreach ( $datos as $data):
            
            $i++;
           
            if($i < 10):
                 array_push($arraydata,  ["name"=> $data["name"], "y" => floatval($data["y"])]);
            else:
                 $cont += floatval($data["y"]);
            endif;
            
        endforeach;
        
        if($cont > 0):
                array_push($arraydata,  ["name"=> "OTROS", "y" => floatval($cont)]);
        endif;
        
        return $arraydata;
        
        
    }
    
    private function getGenerarTurno($id_sys_rrhh_cedula, $id_sys_med_tipo_motivo){
        
        $model = new sysMedTurnoMedico();
        
        
        
        $numTurno =  sysMedTurnoMedico::find()->select(['max(numero)'])->scalar() + 1;
        $model->id_sys_rrhh_cedula = $id_sys_rrhh_cedula;
        $model->id_sys_med_tipo_motivo = $id_sys_med_tipo_motivo;
        $model->usuario_creacion = Yii::$app->user->username;
        $model->numero = $numTurno;
        $model->fecha  = date('Y-m-d');
        $model->fecha_creacion = date('Ymd H:i:s');
        $model->ini_atencion = date('Ymd H:i:s');
        $model->anulado = 0;
        $model->atendido = 1;
        $model->medico = Yii::$app->user->username;
        $model->atendido = 1;
        $model->comentario = "S/N";
        $model->fecha_atencion = date('Ymd H:i:s');
        $model->fin_atencion = date('Ymd H:i:s');
        $model->save(false);
        
        return (new \yii\db\Query())
        ->select(["id"])
        ->from("sys_med_turno_medico")
        ->where("numero  = {$numTurno}")
        ->scalar(SysRrhhEmpleados::getDb());
    }
    
    private function EnviarCorreo($to, $mensaje, $titulo){
        
        $db = $_SESSION['db'];
        
        $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();
        
        Yii::$app->mailer->setTransport([
            
            'class' => 'Swift_SmtpTransport',
            'host' => trim($empresa->mail_host),
            'username' => trim($empresa->mail_username),
            'password' => trim($empresa->mail_password),
            'port' => trim($empresa->mail_port),
            'encryption' => 'tls',
            'streamOptions' => [
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ],
        ]);
        
        Yii::$app->mailer->compose()
        ->setTo($to)
        ->setFrom([$empresa->mail_username => $empresa->razon_social])
        ->setSubject(''.$titulo.' - Gestión Nómina')
        ->setHtmlBody($mensaje)
        ->send();
    }
    
    private function getEmailCreacion($username){
        
        $user  = User::find()->where(['username'=> $username])->one();
        
        return  trim($user->email);
        
    }
    
    
    
    /**
    
     * 
     * 
     * 
     * Finds the SysMedConsultaMedica model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SysMedConsultaMedica the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysMedConsultaMedica::findOne($id)) !== null) {
            return $model;
        }
        
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
