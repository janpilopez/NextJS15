<?php

namespace app\controllers;

use app\models\SysRrhhEmpleadosAtencion;
use app\models\SysRrhhEmpleadosCargos;
use app\models\SysRrhhEmpleadosDocumentos;
use DateTime;
use kartik\mpdf\Pdf;
use Mpdf\Mpdf;
use Exception;
use Yii;
use app\models\Model;
use app\models\SysRrhhEmpleados;
use app\models\Search\SysRrhhEmpleadosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\bootstrap\ActiveForm;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\models\SysRrhhEmpleadosHorario;
use app\models\SysRrhhEmpleadosNucleoFamiliar;
use app\models\SysRrhhEmpleadosJornada;
use app\models\SysRrhhEmpleadosHaberes;
use app\models\SysRrhhContratos;
use app\models\SysRrhhEmpleadosContratos;
use app\models\SysRrhhEmpleadosSueldos;
use app\models\SysRrhhEmpleadosGastos;
use app\models\SysRrhhEmpleadosFoto;
use app\models\SysRrhhEmpleadosUniformes;
use app\models\SysRrhhHorarioCab;
use phpDocumentor\Reflection\Types\Null_;

/**
 * EmpleadosController implements the CRUD actions for SysRrhhEmpleados model.
 */
class EmpleadosController extends Controller
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
     * Lists all SysRrhhEmpleados models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '@app/views/layouts/main_emplados';
        
        $searchModel = new SysRrhhEmpleadosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = ['pageSize' => 10];
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhEmpleados model.
     * @param string $id_sys_rrhh_cedula
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id_sys_rrhh_cedula, $id_sys_empresa)
    {
       
        $contrato = [];
        
        
        $contrato =  (new \yii\db\Query())
        ->select(["fecha_ingreso", "(cast(datediff(dd,fecha_ingreso,GETDATE()) / 365.25 as int)) as anios"])
        ->from("sys_rrhh_empleados_contratos")
        ->Where("id_sys_rrhh_cedula='{$id_sys_rrhh_cedula}'")
        ->andWhere("activo = 1")
        ->all(SysRrhhEmpleados::getDb());
        
        return $this->render('view', [
            'model' => $this->findModel($id_sys_rrhh_cedula, $id_sys_empresa),
            'contrato'=> $contrato
        ]);
        
        
        
    }

    /**
     * Creates a new SysRrhhEmpleados model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->layout = '@app/views/layouts/main_emplados';
        
        $nucleofamiliar = [new SysRrhhEmpleadosNucleoFamiliar()];
        $horarios       = [new SysRrhhEmpleadosHorario()];
        $haberes        = [new SysRrhhEmpleadosHaberes()];
        $contratos      = [new SysRrhhEmpleadosContratos()];
        $cargos         = [new SysRrhhEmpleadosCargos()];
        $sueldos        = [new SysRrhhEmpleadosSueldos()];
        $gastos         = [new SysRrhhEmpleadosGastos()];
        $fotos          = '';
        $documentos     = '';
        
        $db =  $_SESSION['db'];

        $model = new SysRrhhEmpleados();
        //$documento = new SysRrhhEmpleadosDocumentos();
             
        if ($model->load(Yii::$app->request->post())) {

            
            
            $empleado = $this->validaEmpleado(trim($model->id_sys_rrhh_cedula));
            
            //if ($model->validate()) {
                // toda la entrada es válida

            if(!$empleado):
            
                   
                    $nucleofamiliar = Model::createNucleoFamiliar(SysRrhhEmpleadosNucleoFamiliar::classname());
                    Model::loadMultiple($nucleofamiliar, Yii::$app->request->post());
                    
                    $horarios = Model::createHorarios(SysRrhhEmpleadosHorario::classname());
                    Model::loadMultiple($horarios, Yii::$app->request->post());
                    
                    $haberes  =  Model::createHaberes(SysRrhhEmpleadosHaberes::className());
                    Model::loadMultiple($haberes, Yii::$app->request->post());
                    
                    $contratos =  Model::createContratos(SysRrhhEmpleadosContratos::className());
                    Model::loadMultiple($contratos, Yii::$app->request->post());

                    $cargos = Model::createCargos(SysRrhhEmpleadosCargos::className());
                    Model::loadMultiple($cargos, Yii::$app->request->post());
                    
                    $sueldos =  Model::createContratos(SysRrhhEmpleadosSueldos::className());
                    Model::loadMultiple($sueldos, Yii::$app->request->post());
                    
                    $gastos =  Model::createContratos(SysRrhhEmpleadosGastos::className());
                    Model::loadMultiple($gastos, Yii::$app->request->post());
                    
                    $transaction = \Yii::$app->$db->beginTransaction();
                    
                    $user_transaccion = Yii::$app->user->username;
                   
                    
                    try {
                        
                        $validacion = $this->validarCI($model->id_sys_rrhh_cedula);

                        if($validacion == 'Cedula Correcta'){
                        
                            if ($model->validate()) {
                                $model->transaccion_usuario           = $user_transaccion;
                                $model->id_sys_empresa                = '001';
                                $model->nombres                       = trim($model->apellidos.' '.$model->nombre);
                                $model->nombre                        = trim($model->nombre);
                                $model->apellidos                     = trim($model->apellidos);
                                $model->fecha_nacimiento              = $model->fecha_nacimiento;
                                $model->codigo_temp                   = $this->getCodigoComedor($model->id_sys_rrhh_cedula, $model->fecha_nacimiento);
                                $model->email                         = trim($model->email);
                            
                                if(!empty($model->numero_uniforme )){
                                    $model->numero_uniforme = $model->numero_uniforme;
                                    
                                    $existente = SysRrhhEmpleadosUniformes::find()->select('id_sys_rrhh_cedula')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                                    $ids = ArrayHelper::getValue($existente, 'id_sys_rrhh_cedula');
                                    if( $ids != $model->id_sys_rrhh_cedula){
                                        $codigo = Yii::$app->$db->createCommand("select ISNULL(max(id_sys_rrhh_empleado_uniforme),0) + 1 from sys_rrhh_empleados_uniformes")->queryScalar();
                                        Yii::$app->$db->createCommand("INSERT INTO sys_rrhh_empleados_uniformes(id_sys_rrhh_empleado_uniforme,fecha_entrega,id_sys_rrhh_cedula,numero_uniforme,id_sys_empresa) VALUES ('$codigo',GETDATE(),'$model->id_sys_rrhh_cedula','$model->numero_uniforme','001')")->execute();
                                    }else{
                                        Yii::$app->$db->createCommand("update sys_rrhh_empleados_uniformes set numero_uniforme='$model->numero_uniforme' where id_sys_rrhh_cedula='$model->id_sys_rrhh_cedula'")->execute();
                                    }
                                    
                                }else{
                                    $model->numero_uniforme = Null;
                                }
                                
                                if ($flag = $model->save(false)) {
                                    
                                    
                                    $model->file             =  UploadedFile::getInstance($model, 'file');
                                    
                
                                    if ($model->file){
                                        
                                        
                                        $modelfoto =  SysRrhhEmpleadosFoto::find()->where(['id_sys_empresa'=> $model->id_sys_empresa, 'id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                                        
                                        
                                        if ($modelfoto){
                                            
                                            $model->file->saveAs("C:/fotos/".$model->id_sys_rrhh_cedula.'.'.$model->file->extension);
                                            
                                            $ruta =  "C:\'fotos\'".$model->id_sys_rrhh_cedula.'.'.$model->file->extension;
                                            
                                            $ruta = str_replace("'", "", $ruta);
                                            
                                            
                                            Yii::$app->$db->createCommand("update sys_rrhh_empleados_foto
                                                    set foto = (
                                                    SELECT *
                                                    FROM OPENROWSET(BULK '".$ruta."', SINGLE_BLOB) test)
                                                    where id_sys_rrhh_cedula = '{$model->id_sys_rrhh_cedula}'")->execute();
                                            
                                            
                                            
                                        }else{
                                            
                                            $model->file->saveAs("C:/fotos/".$model->id_sys_rrhh_cedula.'.'.$model->file->extension);
                                            
                                            $ruta =  "C:\'fotos\'".$model->id_sys_rrhh_cedula.'.'.$model->file->extension;
                                            
                                            $ruta = str_replace("'", "", $ruta);
                                            
                                            Yii::$app->$db->createCommand("Insert sys_rrhh_empleados_foto (id_sys_rrhh_cedula, foto, id_sys_empresa)
                                                                        Select '{$model->id_sys_rrhh_cedula}', BulkColumn, '001'
                                                                        from Openrowset (Bulk '".$ruta."', Single_Blob) as Image")->execute();
                                            
                                        }
                                        
                                    }

                                    $model->file2             =  UploadedFile::getInstances($model, 'file2');
                                    
                                    if ($model->file2){
                                        
                                        foreach($model->file2 as $file){

                                            $codnucleo        =  SysRrhhEmpleadosDocumentos::find()->select('max(idDocumento)')->scalar() + 1;

                                            $file->saveAs('pdf/expedientes/'. $codnucleo .'_'. $model->id_sys_rrhh_cedula .'.'. $file->extension);
                                            $ruta = "pdf/expedientes/". $codnucleo ."_". $model->id_sys_rrhh_cedula .".". $file->extension;
                                    
                                            $nflag            =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_llamados_atencion_documentos (idDocumento, id_sys_rrhh_cedula, ruta) values ('{$codnucleo}','{$model->id_sys_rrhh_cedula}','{$ruta}')");
                                            
                                            $nflag->execute();
                                            
                                            if(!$nflag){
                                                $flag = false;
                                                $transaction->rollBack();
                                                break;
                                            }
                                        }
                                
                                    }
                                                   
                                    //nucleo familiar 
                                    foreach ($nucleofamiliar as $index => $modeldetalle) {
                                        
                                        if(empty($modeldetalle['nombres'])){

                                        }else{
                                            $codnucleo        =  SysRrhhEmpleadosNucleoFamiliar::find()->select('max(id_sys_rrhh_empleados_fam_cod)')->scalar() + 1;
                                        
                                            // $nflag            =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_nucleo_familiar (id_sys_rrhh_empleados_fam_cod, id_sys_rrhh_cedula, id_sys_empresa, nombres, parentesco, utilidad, profesion, fecha_nacimiento, discapacidad) values ('{$codnucleo}','{$model->id_sys_rrhh_cedula}','{$model->id_sys_empresa}','{$modeldetalle['nombres']}','{$modeldetalle['parentesco']}','{$modeldetalle['utilidad']}','{$modeldetalle['profesion']}', '".$modeldetalle['fecha_nacimiento']."','{$modeldetalle['discapacidad']}' )");
                                            $nflag            =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_nucleo_familiar (id_sys_rrhh_empleados_fam_cod, nombres, parentesco, profesion, fecha_nacimiento, id_sys_rrhh_cedula, id_sys_empresa, discapacidad, transaccion_usuario, utilidad, tribunal, rentas) values ('{$codnucleo}','{$modeldetalle['nombres']}','{$modeldetalle['parentesco']}','{$modeldetalle['profesion']}','".$modeldetalle['fecha_nacimiento']."','{$model->id_sys_rrhh_cedula}','{$model->id_sys_empresa}','{$modeldetalle['discapacidad']}','{$user_transaccion}','{$modeldetalle['utilidad']}','{$modeldetalle['tribunal']}','{$modeldetalle['rentas']}')");
                                            
                                            $nflag->execute();
                                            
                                            if(!$nflag){
                                                $flag = false;
                                                $transaction->rollBack();
                                                break;
                                            }
                                        }
                                        
                                                    
                                    }
                                    
                                    //horarios 
                                    
                                    foreach ($horarios as $index => $modeldetalle) {
                                        
                                        //gernerar por query ya el driver odbc sysbase  no soporta lastid
                                        
                                        if(empty($modeldetalle['id_sys_rrhh_horario'])){

                                        }else{
                                            $codhorario =  SysRrhhEmpleadosHorario::find()->select('max(id_sys_rrhh_empleados_horario)')->scalar() + 1;
                                        
                                            $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_horario (id_sys_rrhh_empleados_horario, id_sys_rrhh_cedula, id_sys_rrhh_horario, id_sys_empresa, transaccion_usuario) values ('{$codhorario}','{$model->id_sys_rrhh_cedula}', '{$modeldetalle['id_sys_rrhh_horario']}', '{$model->id_sys_empresa}', '{$user_transaccion}')");
                    
                                            $nflag->execute();
                                            
                                            if(!$nflag){
                                                $flag = false;
                                                $transaction->rollBack();
                                                break;
                                            }
                                        }
                                        
                                    }
                                    
                                    //haberes 
                                    foreach ($haberes as $index => $modeldetalle) {
                                    
                                        if(empty($modeldetalle['cantidad'])){

                                        }else{
                                            $codhaberes  = SysRrhhEmpleadosHaberes::find()->select('max(id_sys_rrhh_empleados_haber)')->scalar() + 1;
                                        
                                            $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_haberes (id_sys_rrhh_empleados_haber, id_sys_rrhh_cedula, id_sys_empresa, id_sys_rrhh_concepto,  decimo, anio_ini, mes_ini, anio_fin, mes_fin, pago, cantidad, transaccion_usuario) values ('{$codhaberes}','{$model->id_sys_rrhh_cedula}','{$model->id_sys_empresa}', '{$modeldetalle['id_sys_rrhh_concepto']}','{$modeldetalle['decimo']}','{$modeldetalle['anio_ini']}','{$modeldetalle['mes_ini']}','{$modeldetalle['anio_fin']}','{$modeldetalle['mes_fin']}','{$modeldetalle['pago']}', '{$modeldetalle['cantidad']}', '{$user_transaccion}')");
                        
                                            $nflag->execute();
                                            
                                            if(!$nflag){
                                                $flag = false;
                                                $transaction->rollBack();
                                                break;
                                            }
                                        }
                                        
                                        
                                    }
                                    
                                    //contratos 
                                    foreach ($contratos as $index => $modeldetalle) {
                                        
                                        if(empty($modeldetalle['fecha_ingreso'])){

                                        }else{
                                    
                                            $codcontrato  =  SysRrhhEmpleadosContratos::find()->select('max(id_sys_rrhh_empleados_contrato_cod)')->scalar() + 1;
                                            
                        
                                            $fechaingreso  = $modeldetalle['fecha_ingreso'] == ''? 'null' : "'".$modeldetalle['fecha_ingreso']."'";
                                            $fechasalida   = $modeldetalle['fecha_salida']  == ''? 'null' : "'".$modeldetalle['fecha_salida']."'";
                                            
                        
                                            $nflag        =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_contratos ( id_sys_rrhh_empleados_contrato_cod, id_sys_rrhh_cedula, id_sys_empresa, fecha_ingreso, fecha_salida, cargo, id_sys_rrhh_causa_salida, activo, transaccion_usuario) values ('{$codcontrato}','{$model->id_sys_rrhh_cedula}','{$model->id_sys_empresa}', ".$fechaingreso.",".$fechasalida.",'{$modeldetalle['cargo']}','{$modeldetalle['id_sys_rrhh_causa_salida']}', ".$modeldetalle['activo'].", '{$user_transaccion}')");
                                            
                                            
                                            $nflag->execute();
                                            
                                            if(!$nflag){
                                                $flag = false;
                                                $transaction->rollBack();
                                                break;
                                            }
                                        
                                        }
                                    }

                                    foreach ($cargos as $index => $modeldetalle) {
                                        
                                        
                                        if(empty($modeldetalle['fecha_ingreso'])){

                                        }else{

                                            $codcargo  =  SysRrhhEmpleadosCargos::find()->select('max(id_sys_rrhh_empleados_cargo_cod)')->scalar() + 1;
                                            
                        
                                            $fechaingreso  = $modeldetalle['fecha_ingreso'] == ''? 'null' : "'".$modeldetalle['fecha_ingreso']."'";
                                            $fechasalida   = $modeldetalle['fecha_salida']  == ''? 'null' : "'".$modeldetalle['fecha_salida']."'";
                                            
                        
                                            $nflag        =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_cargos ( id_sys_rrhh_empleados_cargo_cod, id_sys_rrhh_cedula, fecha_ingreso, fecha_salida, cargo, departamento,activo, transaccion_usuario) values ('{$codcargo}','{$model->id_sys_rrhh_cedula}', ".$fechaingreso.",".$fechasalida.",'{$modeldetalle['cargo']}','{$modeldetalle['departamento']}',".$modeldetalle['activo'].", '{$user_transaccion}')");
                                            
                                            
                                            $nflag->execute();
                                            
                                            if(!$nflag){
                                                $flag = false;
                                                $transaction->rollBack();
                                                break;
                                            }
                                        }
                                        
                                        
                                    }
                                    //Sueldos
                                    foreach ($sueldos as $index => $modeldetalle) {
                                        
                                        if(empty($modeldetalle['sueldo'])){

                                        }else{
                                            $fecha        = $modeldetalle['fecha'] == '' ? 'null' : "'".$modeldetalle['fecha']."'";
                                            
                                            $sueldo       = $modeldetalle['sueldo'] == '' ? 0 :  $modeldetalle['sueldo'];
                                            
                                            $anticipo     = $modeldetalle['sueldo_anticipo'] == '' ? 0 :  $modeldetalle['sueldo_anticipo'];
                                            
                                            $poranticipo  = $modeldetalle['por_anticipo'] == '' ? 0 :  $modeldetalle['por_anticipo'];
                                            
                                            $codsueldo    = SysRrhhEmpleadosSueldos::find()->select('max(id_sys_rrhh_empleados_sueldo_cod)')->scalar() + 1;
                                            
                                            $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_sueldos (id_sys_rrhh_empleados_sueldo_cod, id_sys_rrhh_cedula, id_sys_empresa, fecha, estado, sueldo, por_anticipo, sueldo_anticipo, transaccion_usuario) values ('{$codsueldo}','{$model->id_sys_rrhh_cedula}','{$model->id_sys_empresa}',".$fecha.",'{$modeldetalle['estado']}',".$sueldo.", ".$poranticipo.",".$anticipo.", '{$user_transaccion}')");
                                            $nflag->execute();
                                            
                                            if(!$nflag){
                                                $flag = false;
                                                $transaction->rollBack();
                                                break;
                                            }
                                        }   
                                        
                                    }
                                    //gastos
                                    
                                    foreach ($gastos as $index => $modeldetalle) {
                                        
                                        
                                        
                                        if(empty($modeldetalle['cantidad'])){

                                        }else{
                                            $codgasto   =  SysRrhhEmpleadosGastos::find()->select('max(id_sys_rrhh_empleados_gasto)')->scalar() + 1;
                                        
                                            $nflag     =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_gastos (id_sys_rrhh_empleados_gasto, id_sys_rrhh_cedula, id_sys_empresa, id_sys_rrhh_rubros_gastos, cantidad, transaccion_usuario) values ('{$codgasto}','{$model->id_sys_rrhh_cedula}','{$model->id_sys_empresa}','{$modeldetalle['id_sys_rrhh_rubros_gastos']}', '{$modeldetalle['cantidad']}', '{$user_transaccion}')");
                                            
                                            $nflag->execute();
                                            
                                            if(!$nflag){
                                                $flag = false;
                                                $transaction->rollBack();
                                                break;
                                            }    
                                        }
                                        
                                    }
                                    
                                    if ($flag) {
                                        $transaction->commit();
                                        Yii::$app->getSession()->setFlash('info', [
                                            'type' => 'success','duration' => 1500,
                                            'icon' => 'glyphicons glyphicons-robot','message' => 'El empleado ha sido registro con exito!',
                                            'positonY' => 'top','positonX' => 'right']);
                                        
                                        return $this->redirect(['update', 'id_sys_rrhh_cedula' => $model->id_sys_rrhh_cedula, 'id_sys_empresa' => '001']);
                                    } 
                            
                                }

                            
                            } else {

                            $model->getErrors();
                    
                            }

                        }else{

                            $transaction->rollBack();
                            Yii::$app->getSession()->setFlash('info', [
                                'type' => 'warning','duration' => 1500,
                                'icon' => 'glyphicons glyphicons-robot','message' => $validacion,
                                'positonY' => 'top','positonX' => 'right']);

                            return $this->redirect(['create']);

                        }
                            
                    }catch (Exception $e) {
                        $transaction->rollBack();
                        throw new Exception($e);
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'danger','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                            'positonY' => 'top','positonX' => 'right']);
                        return $this->redirect(['index']);
                        
                    }
             else:
                 
                      Yii::$app->getSession()->setFlash('info', [
                     'type' => 'warning','duration' => 1500,
                     'icon' => 'glyphicons glyphicons-robot','message' => 'El empleador ya ha sido registrado anteriormente. Por favor actualizar datos!',
                     'positonY' => 'top','positonX' => 'right']);
                    
                      return $this->redirect(['update', 'id_sys_rrhh_cedula' => $model->id_sys_rrhh_cedula, 'id_sys_empresa' => '001']);
                      
             endif;
           
            
            
            
        }

        return $this->render('create', [
            'model' => $model,
            'nucleofamiliar'=> $nucleofamiliar,
            'horarios'=> $horarios,
            'haberes'=> $haberes,
            'contratos'=> $contratos,
            'cargos' => $cargos,
            'sueldos'=> $sueldos,
            'gastos'=> $gastos,
            'fotos' => $fotos,
            'documentos' => $documentos,
            'update'=> 0,
        ]);
    }

    /**
     * Updates an existing SysRrhhEmpleados model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_rrhh_cedula
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id_sys_rrhh_cedula, $id_sys_empresa)
    {
        
        $this->layout = '@app/views/layouts/main_emplados';
        
        $model = $this->findModel($id_sys_rrhh_cedula, $id_sys_empresa);
 
        
        $model->nombre    =  $model->nombre;
        $model->apellidos =  $model->apellidos;
        $model->nombres   =  $model->nombre.' '.$model->apellidos;

        if($model->codigo_temp == NULL):

            $model->codigo_temp = $this->getCodigoComedor($model->id_sys_rrhh_cedula, $model->fecha_nacimiento);
        
        endif;
        
        $nucleofamiliar = [];
        $horarios       = [];
        $haberes        = [];
        $contratos      = [];
        $cargos         = [];
        $sueldos        = [];
        $gastos         = [];
        $documentos     = [];
        $fotos          = '';

        
        $db =  $_SESSION['db'];
    
        $datosnucleo= SysRrhhEmpleadosNucleoFamiliar::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula, 'id_sys_empresa'=> $id_sys_empresa])->all();
        
        //nucleo familiar
        if ($datosnucleo){
            foreach ($datosnucleo as $data){
                $objnucleo                                 = new SysRrhhEmpleadosNucleoFamiliar();
                $objnucleo->id_sys_rrhh_empleados_fam_cod  = $data->id_sys_rrhh_empleados_fam_cod;
                $objnucleo->id_sys_rrhh_cedula             = $data->id_sys_rrhh_cedula;
                $objnucleo->id_sys_empresa                 = $data->id_sys_empresa;
                $objnucleo->nombres                        = $data->nombres;
                $objnucleo->parentesco                     = $data->parentesco;
                $objnucleo->utilidad                       = $data->utilidad;
                $objnucleo->tribunal                       = $data->tribunal;
                $objnucleo->rentas                         = $data->rentas;
                $objnucleo->profesion                      = $data->profesion;
                $objnucleo->fecha_nacimiento               = $data->fecha_nacimiento; 
                $objnucleo->discapacidad                   = $data->discapacidad;
                $objnucleo->edad                           = 2;
                array_push($nucleofamiliar, $objnucleo);
            }
          }else{
             array_push($nucleofamiliar, new SysRrhhEmpleadosNucleoFamiliar());
           }
         //fin de nucle familiar
         
         //horarios 
           $datoshorarios = SysRrhhEmpleadosHorario::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula, 'id_sys_empresa'=> $id_sys_empresa])->all();
           
           //nucleo familiar
           if ($datoshorarios){
               foreach ($datoshorarios as $data){
                   
                   $objhorario                                 = new SysRrhhEmpleadosHorario();
                   $objhorario->id_sys_rrhh_empleados_horario  = $data->id_sys_rrhh_empleados_horario;
                   $objhorario->id_sys_rrhh_horario            = $data->id_sys_rrhh_horario;
                   $objhorario->id_sys_rrhh_cedula             = $data->id_sys_rrhh_cedula;
                   $objhorario->id_sys_empresa                 = $data->id_sys_empresa;
          
     
                   array_push($horarios, $objhorario);
               }
           }else{
               array_push($horarios, new SysRrhhEmpleadosHorario());
           }
         //fin de jornadas laborales   
          
          //haberes 
           $datoshaberes  = SysRrhhEmpleadosHaberes::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula, 'id_sys_empresa'=> $id_sys_empresa])->all();
           
           if ($datoshaberes){
               
           foreach ($datoshaberes as $data){
                   $objhaber                                = new SysRrhhEmpleadosHaberes();
                   $objhaber->id_sys_rrhh_empleados_haber    = $data->id_sys_rrhh_empleados_haber;
                   $objhaber->id_sys_rrhh_cedula             = $data->id_sys_rrhh_cedula;
                   $objhaber->id_sys_empresa                 = $data->id_sys_empresa;
                   $objhaber->id_sys_rrhh_concepto           = $data->id_sys_rrhh_concepto;
                   $objhaber->decimo                         = $data->decimo;
                   $objhaber->anio_ini                       = intval($data->anio_ini);
                   $objhaber->mes_ini                        = intval($data->mes_ini);
                   $objhaber->anio_fin                       = intval($data->anio_fin);
                   $objhaber->mes_fin                        = intval($data->mes_fin);
                   $objhaber->pago                           = $data->pago;
                   $objhaber->cantidad                       = $data->cantidad;
                   $objhaber->estado                         = $data->estado;
                   $objhaber->transaccion_usuario            = $data->transaccion_usuario;
                   
                   array_push($haberes, $objhaber);
              }
               
           }else{
               array_push($haberes, new SysRrhhEmpleadosHaberes());
           }
           
           $datoscontratos  = SysRrhhEmpleadosContratos::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula, 'id_sys_empresa'=> $id_sys_empresa])->all();
           
           if ($datoscontratos){
               
               foreach ($datoscontratos as $data){
                   
                   $objcontratos  = new SysRrhhEmpleadosContratos();
                   $objcontratos->id_sys_rrhh_empleados_contrato_cod = $data->id_sys_rrhh_empleados_contrato_cod;
                   $objcontratos->id_sys_rrhh_cedula                 = $data->id_sys_rrhh_cedula;
                   $objcontratos->id_sys_empresa                     = $data->id_sys_empresa;
                   $objcontratos->fecha_ingreso                      = $data->fecha_ingreso;   
                   $objcontratos->fecha_salida                       = $data->fecha_salida; 
                   $objcontratos->cargo                              = $data->cargo;
                   $objcontratos->id_sys_rrhh_causa_salida           = $data->id_sys_rrhh_causa_salida;
                   $objcontratos->transaccion_usuario                = $data->transaccion_usuario;
                   $objcontratos->activo                             = $data->activo;
                   
                   array_push($contratos, $objcontratos);
                   
               }    
           }else{
               
               array_push($contratos, new SysRrhhEmpleadosContratos());
               
           }

           $datoscargos  = SysRrhhEmpleadosCargos::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])->all();
           
           if ($datoscargos){
               
               foreach ($datoscargos as $data){
                   
                   $objcargos  = new SysRrhhEmpleadosCargos();
                   $objcargos->id_sys_rrhh_empleados_cargo_cod = $data->id_sys_rrhh_empleados_cargo_cod;
                   $objcargos->id_sys_rrhh_cedula                 = $data->id_sys_rrhh_cedula;
                   $objcargos->fecha_ingreso                      = $data->fecha_ingreso;   
                   $objcargos->fecha_salida                       = $data->fecha_salida; 
                   $objcargos->cargo                              = $data->cargo;
                   $objcargos->departamento                       = $data->departamento;
                   $objcargos->transaccion_usuario                = $data->transaccion_usuario;
                   $objcargos->activo                             = $data->activo;
                   
                   array_push($cargos, $objcargos);
                   
               }    
           }else{
               
               array_push($cargos, new SysRrhhEmpleadosCargos());
               
           }
           
           
           $datossueldos  = SysRrhhEmpleadosSueldos::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula, 'id_sys_empresa'=> $id_sys_empresa])->all();
           
           
           if ($datossueldos){
               
               
               foreach ($datossueldos as $data){
                   
                   $objsueldo = new SysRrhhEmpleadosSueldos();
                   $objsueldo->id_sys_rrhh_empleados_sueldo_cod = $data->id_sys_rrhh_empleados_sueldo_cod;
                   $objsueldo->id_sys_rrhh_cedula                 = $data->id_sys_rrhh_cedula;
                   $objsueldo->id_sys_empresa                     = $data->id_sys_empresa;
                   $objsueldo->fecha                              = $data->fecha;
                   $objsueldo->estado                             = $data->estado;
                   $objsueldo->sueldo                             = $data->sueldo;
                   $objsueldo->por_anticipo                       = $data->por_anticipo;
                   $objsueldo->sueldo_anticipo                    = $data->sueldo_anticipo;
                   $objsueldo->transaccion_usuario                = $data->transaccion_usuario;
                    
                   array_push($sueldos, $objsueldo); 
                   
               }
              
               
           }else{
               
               array_push($sueldos, new SysRrhhEmpleadosSueldos());  
               
           }
           
           $datosgastos   = SysRrhhEmpleadosGastos::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula, 'id_sys_empresa'=> $id_sys_empresa])->all();
           
           if($datosgastos){
               
               foreach ($datosgastos as $data ) {
                   
                   $objgastos= new  SysRrhhEmpleadosGastos();
                   
                   $objgastos->id_sys_rrhh_empleados_gasto =  $data->id_sys_rrhh_empleados_gasto;
                   $objgastos->id_sys_rrhh_cedula          =  $data->id_sys_rrhh_cedula;
                   $objgastos->id_sys_empresa              =  $data->id_sys_empresa;
                   $objgastos->id_sys_rrhh_rubros_gastos   =  $data->id_sys_rrhh_rubros_gastos;
                   $objgastos->cantidad                    =  $data->cantidad;
                   
                   array_push($gastos, $objgastos); 
                   
               }
                 
           }else{
               
               array_push($gastos, new SysRrhhEmpleadosGastos());  
             
           }
           
           $datosdocumentos   = SysRrhhEmpleadosDocumentos::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])->all();
           
           if($datosdocumentos){
               
               foreach ($datosdocumentos as $data ) {
                   
                   $objdocumentos= new  SysRrhhEmpleadosDocumentos();
                   
                   $objdocumentos->idDocumento           =  $data->idDocumento;
                   $objdocumentos->id_sys_rrhh_cedula    =  $data->id_sys_rrhh_cedula;
                   $objdocumentos->ruta                  =  $data->ruta;
                
                   
                   array_push($documentos, $objdocumentos); 
                   
               }
                 
           }else{
               
               array_push($documentos, new SysRrhhEmpleadosDocumentos());  
             
           }

           
           $fotos =   Yii::$app->$db->createCommand("select foto_64,  foto, baze64 from sys_rrhh_empleados_foto cross apply (select foto as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$model->id_sys_rrhh_cedula}'")->queryOne();
           
           SysRrhhEmpleadosFoto::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula, 'id_sys_empresa'=> $id_sys_empresa])->one();
           
           
        if ($model->load(Yii::$app->request->post())) {
            
            
            $oldIDs    = ArrayHelper::map($nucleofamiliar, 'id_sys_rrhh_empleados_fam_cod', 'id_sys_rrhh_empleados_fam_cod');
            $oldIDshor = ArrayHelper::map($horarios, 'id_sys_rrhh_empleados_horario', 'id_sys_rrhh_empleados_horario');
            $oldIDshab = ArrayHelper::map($haberes, 'id_sys_rrhh_empleados_haber', 'id_sys_rrhh_empleados_haber');
            //$oldIDsuel = ArrayHelper::map($sueldos, 'id_sys_rrhh_empleados_sueldo_cod', 'id_sys_rrhh_empleados_sueldo_cod');
            //$oldIDcont = ArrayHelper::map($contratos,'id_sys_rrhh_empleados_contrato_cod', 'id_sys_rrhh_empleados_contrato_cod');
            $oldIDgast = ArrayHelper::map($gastos,'id_sys_rrhh_empleados_gasto', 'id_sys_rrhh_empleados_gasto');
            
            $arraynucleo    = Yii::$app->request->post('SysRrhhEmpleadosNucleoFamiliar');
            $arrayhorario   = Yii::$app->request->post('SysRrhhEmpleadosHorario');
            $arrayhaberes   = Yii::$app->request->post('SysRrhhEmpleadosHaberes');
            $arraysueldos   = Yii::$app->request->post('SysRrhhEmpleadosSueldos');
            $arraycontratos = Yii::$app->request->post('SysRrhhEmpleadosContratos');
            $arraycargos    = Yii::$app->request->post('SysRrhhEmpleadosCargos');
            $arraygastos    = Yii::$app->request->post('SysRrhhEmpleadosGastos');
            
           
           
            if ($arraynucleo){
            
               $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($arraynucleo, 'id_sys_rrhh_empleados_fam_cod', 'id_sys_rrhh_empleados_fam_cod')));
             
            }else{
                
                SysRrhhEmpleadosNucleoFamiliar::deleteAll(['id_sys_rrhh_empleados_fam_cod' => $oldIDs]);
            }
            
            if ($arrayhorario) {
                
                $deletedIDshor = array_diff($oldIDshor, array_filter(ArrayHelper::map($arrayhorario, 'id_sys_rrhh_empleados_horario', 'id_sys_rrhh_empleados_horario')));
                
            }else{
                
                SysRrhhEmpleadosHorario::deleteAll(['id_sys_rrhh_empleados_horario'=> $oldIDshor]);
                
 
            }
            
            
            if ($arraygastos){
                
                $deletedIDgast = array_diff($oldIDgast, array_filter(ArrayHelper::map($arraygastos, 'id_sys_rrhh_empleados_gasto', 'id_sys_rrhh_empleados_gasto')));
                
            }else{
                
                
                SysRrhhEmpleadosGastos::deleteAll(['id_sys_rrhh_empleados_gasto'=> $oldIDgast]);
                
                
            }
            
                   
            if(!empty($deletedIDs)){
                
                SysRrhhEmpleadosNucleoFamiliar::deleteAll(['id_sys_rrhh_empleados_fam_cod' => $deletedIDs]);
            }
            
            if (!empty($deletedIDshor)){
                
                SysRrhhEmpleadosHorario::deleteAll(['id_sys_rrhh_empleados_horario'=> $deletedIDshor]);
                
            }
     
            if (!empty($deletedIDshab)){
                
                SysRrhhEmpleadosHaberes::deleteAll(['id_sys_rrhh_empleados_haber'=> $deletedIDshab]);
                
            }
            
            if (!empty($deletedIDgast)){
                
                SysRrhhEmpleadosGastos::deleteAll(['id_sys_rrhh_empleados_gasto'=> $deletedIDgast]);
                
            }
            
            
            $transaction = \Yii::$app->$db->beginTransaction();
            
            $user_transaccion = Yii::$app->user->username;
            
            try {
               
                $model->file             =  UploadedFile::getInstance($model, 'file');

                $model->nombres          = trim($model->apellidos.' '.$model->nombre);
                
                $model->nombre           = trim($model->nombre);
                
                $model->apellidos        = trim($model->apellidos);
                
                $model->email            = trim($model->email);
                    
                $model->fecha_nacimiento =   $model->fecha_nacimiento;
                
                $model->transaccion_usuario = $user_transaccion;

                if(!empty($model->numero_uniforme )){
                    $existente = SysRrhhEmpleadosUniformes::find()->select('id_sys_rrhh_cedula')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                    $ids = ArrayHelper::getValue($existente, 'id_sys_rrhh_cedula');
                    if( $ids != $model->id_sys_rrhh_cedula){
                        $codigo = Yii::$app->$db->createCommand("select ISNULL(max(id_sys_rrhh_empleado_uniforme),0) + 1 from sys_rrhh_empleados_uniformes")->queryScalar();                            
                        Yii::$app->$db->createCommand("INSERT INTO sys_rrhh_empleados_uniformes(id_sys_rrhh_empleado_uniforme,fecha_entrega,id_sys_rrhh_cedula,numero_uniforme,id_sys_empresa) VALUES ('$codigo',GETDATE(),'$model->id_sys_rrhh_cedula','$model->numero_uniforme','001')")->execute();
                    }else{
                        Yii::$app->$db->createCommand("update sys_rrhh_empleados_uniformes set numero_uniforme='$model->numero_uniforme' where id_sys_rrhh_cedula='$model->id_sys_rrhh_cedula'")->execute();
                    }
                }else{
                    $model->numero_uniforme = Null;
                }
                
                if ($model->file){
                
                        $modelfoto =  SysRrhhEmpleadosFoto::find()->where(['id_sys_empresa'=> $model->id_sys_empresa, 'id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                       
                        if ($modelfoto){
                            
                             $model->file->saveAs("C:/fotos/".$model->id_sys_rrhh_cedula.'.'.$model->file->extension);
                        
                             $ruta =  "C:\'fotos\'".$model->id_sys_rrhh_cedula.'.'.$model->file->extension;
                        
                             $ruta = str_replace("'", "", $ruta);
                            
                         
                             Yii::$app->$db->createCommand("update sys_rrhh_empleados_foto
                                    set foto = (
                                    SELECT *
                                    FROM OPENROWSET(BULK '".$ruta."', SINGLE_BLOB) test)
                                    where id_sys_rrhh_cedula = '{$model->id_sys_rrhh_cedula}'")->execute();
                             
                            
              
                        }else{
                            
                                $model->file->saveAs("C:/fotos/".$model->id_sys_rrhh_cedula.'.'.$model->file->extension);
                                
                                $ruta =  "C:\'fotos\'".$model->id_sys_rrhh_cedula.'.'.$model->file->extension;
                                
                                $ruta = str_replace("'", "", $ruta);
                            
                              
                                 Yii::$app->$db->createCommand("Insert sys_rrhh_empleados_foto (id_sys_rrhh_cedula, foto, id_sys_empresa)
                                                        Select '{$model->id_sys_rrhh_cedula}', BulkColumn, '001'
                                                         from Openrowset (Bulk '".$ruta."', Single_Blob) as Image")->execute();
                                 
                          }
                    }

                    $model->file2             =  UploadedFile::getInstances($model, 'file2');
                                    
                    if ($model->file2){
                                        
                        foreach($model->file2 as $file){

                            $codnucleo        =  SysRrhhEmpleadosDocumentos::find()->select('max(idDocumento)')->scalar() + 1;

                            $file->saveAs('pdf/expedientes/'. $codnucleo .'_'. $model->id_sys_rrhh_cedula .'.'. $file->extension);
                            $ruta = "pdf/expedientes/". $codnucleo ."_". $model->id_sys_rrhh_cedula .".". $file->extension;
                                    
                            Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_llamados_atencion_documentos (idDocumento, id_sys_rrhh_cedula, ruta) values ('{$codnucleo}','{$model->id_sys_rrhh_cedula}','{$ruta}')")->execute();
                                      
                        }
                                
                    }
                 
                  
                if ($flag = $model->save(false)) {
                    
                           //nucleo familiar 
                            if ($arraynucleo){
                                
                                    foreach ($arraynucleo as $index => $modeldetalle) {
                                        
                                          
                                            if($modeldetalle['id_sys_rrhh_empleados_fam_cod'] != ''){
                                                
                                               
                                            
                                                
                                                $md =  SysRrhhEmpleadosNucleoFamiliar::find()
                                                ->where(['id_sys_empresa'=> $model->id_sys_empresa, 'id_sys_rrhh_empleados_fam_cod'=> $modeldetalle['id_sys_rrhh_empleados_fam_cod']])
                                                ->one();
                                               
                                                    $md->id_sys_rrhh_cedula             = $model->id_sys_rrhh_cedula;
                                                    $md->id_sys_empresa                 = $model->id_sys_empresa;
                                                    $md->nombres                        = $modeldetalle['nombres'];
                                                    $md->parentesco                     = $modeldetalle['parentesco'];
                                                    $md->utilidad                       = $modeldetalle['utilidad'];
                                                    $md->tribunal                       = $modeldetalle['tribunal'];
                                                    $md->rentas                         = $modeldetalle['rentas'];
                                                    $md->profesion                      = $modeldetalle['profesion'];
                                                    $md->fecha_nacimiento               = $modeldetalle['fecha_nacimiento'];
                                                    $md->discapacidad                   = $modeldetalle['discapacidad'];
                                                    $md->transaccion_usuario            = $user_transaccion;
                                                    
                                                if (! ($flag = $md->save(false))) {
                                                    $transaction->rollBack();
                                                    break;
                                                }
                                            }
                                            else{

                                                if(empty($modeldetalle['nombres'])){

                                                }else{
                                                
                                                    //gernerar por query ya el driver odbc sysbase  no soporta lastid 
                                               
                                                    $codnucleo =  SysRrhhEmpleadosNucleoFamiliar::find()->select('max(id_sys_rrhh_empleados_fam_cod)')->scalar() + 1;
                                                    
                                                    $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_nucleo_familiar (id_sys_rrhh_empleados_fam_cod, id_sys_rrhh_cedula, id_sys_empresa, nombres, parentesco, utilidad, profesion, fecha_nacimiento, discapacidad, tribunal, rentas, transaccion_usuario) values ('{$codnucleo}','{$model->id_sys_rrhh_cedula}','{$model->id_sys_empresa}','{$modeldetalle['nombres']}','{$modeldetalle['parentesco']}','{$modeldetalle['utilidad']}','{$modeldetalle['profesion']}', '".$modeldetalle['fecha_nacimiento']."','{$modeldetalle['discapacidad']}', {$modeldetalle['tribunal']},{$modeldetalle['rentas']}, '{$user_transaccion}')");
                                                    $nflag->execute();
                                                    
                                                    if(!$nflag){
                                                        $flag = false;
                                                        $transaction->rollBack();
                                                        break;
                                                    }
                                                    
                                                }
                                            }
                                            
                                        
                                    }
                                       
                            }else{
                                
                                $flag= true;
                            }
                            //jornadadas
                    
                            if ($arrayhorario){
                                
                                foreach ($arrayhorario as $index => $modeldetalle) {
                                    
                                    
                                    if($modeldetalle['id_sys_rrhh_empleados_horario'] != ''){
                                       
                                       $horario   = SysRrhhEmpleadosHorario::find()->where(['id_sys_empresa'=> $model->id_sys_empresa, 'id_sys_rrhh_empleados_horario'=> $modeldetalle['id_sys_rrhh_empleados_horario']])->one();
                                       
                                       $horario->id_sys_rrhh_cedula  =  $model->id_sys_rrhh_cedula;
                                       $horario->id_sys_empresa      =  $model->id_sys_empresa;
                                       $horario->id_sys_rrhh_horario = $modeldetalle['id_sys_rrhh_horario'];
                                       $horario->transaccion_usuario  = $user_transaccion;
                                       
                                       if (! ($flag = $horario->save(false))) {
                                           $transaction->rollBack();
                                           break;
                                       }
                                      
                                       
                                    }
                                    else{
                                        
                                        $codhorario =  SysRrhhEmpleadosHorario::find()->select('max(id_sys_rrhh_empleados_horario)')->scalar() + 1 ;
                                     
                                        $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_horario (id_sys_rrhh_empleados_horario, id_sys_rrhh_cedula, id_sys_rrhh_horario, id_sys_empresa, transaccion_usuario) values ('{$codhorario}','{$model->id_sys_rrhh_cedula}', '{$modeldetalle['id_sys_rrhh_horario']}', '{$model->id_sys_empresa}', '{$user_transaccion}')");
                                        $nflag->execute();
                                        
                                        if(!$nflag){
                                            $flag = false;
                                            $transaction->rollBack();
                                            break;
                                        }
                                       
                                    }
                                
                                }
                                
                            }else{
                                
                                $flag= true;
                            }
                    
                            //haberes 
                            if ($arrayhaberes){
                                
                                foreach ($arrayhaberes as $index => $modeldetalle) {
                                    
                                    
                                    if($modeldetalle['id_sys_rrhh_empleados_haber'] != ''){
                                        
                                        
                                            $haber     = SysRrhhEmpleadosHaberes::find()->where(['id_sys_empresa'=> $model->id_sys_empresa, 'id_sys_rrhh_empleados_haber'=> $modeldetalle['id_sys_rrhh_empleados_haber']])->one();
                                        
                                            $haber->id_sys_rrhh_cedula             = $model->id_sys_rrhh_cedula;
                                            $haber->id_sys_empresa                 = $model->id_sys_empresa;
                                            $haber->id_sys_rrhh_concepto           = $modeldetalle['id_sys_rrhh_concepto'];
                                            $haber->decimo                         = $modeldetalle['decimo'];
                                            $haber->anio_ini                       =  intval($modeldetalle['anio_ini']);
                                            $haber->mes_ini                        =  intval($modeldetalle['mes_ini']);
                                            $haber->anio_fin                       =  intval($modeldetalle['anio_fin']);
                                            $haber->mes_fin                        =  intval($modeldetalle['mes_fin']);
                                            $haber->pago                           = $modeldetalle['pago'];
                                            $haber->cantidad                       = $modeldetalle['cantidad'];
                                            $haber->transaccion_usuario            = $user_transaccion;
                                            
                                            if (! ($flag = $haber->save(false))) {
                                                $transaction->rollBack();
                                                break;
                                            }
                                        
                                    }
                                    else{

                                        if(empty($modeldetalle['cantidad'])){

                                        }else{
                                                            
                                            $codhaberes  = SysRrhhEmpleadosHaberes::find()->select('max(id_sys_rrhh_empleados_haber)')->scalar() + 1 ;
                                        
                                            $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_haberes (id_sys_rrhh_empleados_haber, id_sys_rrhh_cedula, id_sys_empresa, id_sys_rrhh_concepto,  decimo, anio_ini, mes_ini, anio_fin, mes_fin, pago, cantidad, transaccion_usuario) values ('{$codhaberes}','{$model->id_sys_rrhh_cedula}','{$model->id_sys_empresa}', '{$modeldetalle['id_sys_rrhh_concepto']}','{$modeldetalle['decimo']}','{$modeldetalle['anio_ini']}','{$modeldetalle['mes_ini']}','{$modeldetalle['anio_fin']}','{$modeldetalle['mes_fin']}','{$modeldetalle['pago']}', '{$modeldetalle['cantidad']}', '{$user_transaccion}')");
                                            $nflag->execute();
                                            
                                            if(!$nflag){
                                                $flag = false;
                                                $transaction->rollBack();
                                                break;
                                            }
                                      
                                        }
                                    }
                                    
                              }
                                
                            }else{
                                
                                $flag= true;
                            }
                            
                            //sueldos de los empleadores
                            
                            if ($arraysueldos){
                                
                                foreach ($arraysueldos as $index => $modeldetalle) {
                                    
                                    
                                    if($modeldetalle['id_sys_rrhh_empleados_sueldo_cod'] != ''){
                                        
                                        
                                        
                            
                                        $sueldo    = SysRrhhEmpleadosSueldos::find()->where(['id_sys_empresa'=> $model->id_sys_empresa, 'id_sys_rrhh_empleados_sueldo_cod'=> $modeldetalle['id_sys_rrhh_empleados_sueldo_cod']])->one();
                                        
                                        $sueldo->id_sys_rrhh_cedula            = $model->id_sys_rrhh_cedula;
                                        $sueldo->id_sys_empresa                = $model->id_sys_empresa;
                                        $sueldo->fecha                         = $modeldetalle['fecha'] == '' ? null : $modeldetalle['fecha']; 
                                        $sueldo->estado                        = $modeldetalle['estado'];
                                        $sueldo->sueldo                        = $modeldetalle['sueldo'];
                                        $sueldo->por_anticipo                  = $modeldetalle['por_anticipo'];
                                        $sueldo->sueldo_anticipo               = $modeldetalle['sueldo_anticipo'];
                                        $sueldo->transaccion_usuario           = $user_transaccion;
                                     
                                        
                                        if (! ($flag = $sueldo->save(false))) {
                                            $transaction->rollBack();
                                            break;
                                        }
                                        
                                    }
                                    else{
                                        
                                        if(empty($modeldetalle['sueldo'])){

                                        }else{
                                        
                                            $fecha        = $modeldetalle['fecha'] == '' ? 'null' : "'".$modeldetalle['fecha']."'";
                                            
                                            $sueldo       = $modeldetalle['sueldo'] == '' ? 0 :  $modeldetalle['sueldo'];
                                            
                                            $anticipo     = $modeldetalle['sueldo_anticipo'] == '' ? 0 :  $modeldetalle['sueldo_anticipo'];
                                            
                                            $poranticipo  = $modeldetalle['por_anticipo'] == '' ? 0 :  $modeldetalle['por_anticipo'];
                                                                            
                                            $codsueldo    = SysRrhhEmpleadosSueldos::find()->select('max(id_sys_rrhh_empleados_sueldo_cod)')->scalar() + 1;
                                    
                                            $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_sueldos (id_sys_rrhh_empleados_sueldo_cod, id_sys_rrhh_cedula, id_sys_empresa, fecha, estado, sueldo, por_anticipo, sueldo_anticipo, transaccion_usuario) values ('{$codsueldo}','{$model->id_sys_rrhh_cedula}','{$model->id_sys_empresa}',".$fecha.",'{$modeldetalle['estado']}',".$sueldo.", ".$poranticipo.",".$anticipo.", '{$user_transaccion}')");
                                            $nflag->execute();
                                            
                                            if(!$nflag){
                                                $flag = false;
                                                $transaction->rollBack();
                                                break;
                                            }
                                        
                                        }
                                    }
                                    
                                }
                                
                            }else{
                                
                                $flag= true;
                            }
                            
                            ////contratos empleados 
                            if ($arraycontratos){
                                
                                foreach ($arraycontratos as $index => $modeldetalle) {
                                    
                                    
                                    if($modeldetalle['id_sys_rrhh_empleados_contrato_cod'] != ''){
                                        
                             
                                        $contrato    = SysRrhhEmpleadosContratos::find()->where(['id_sys_empresa'=> $model->id_sys_empresa, 'id_sys_rrhh_empleados_contrato_cod'=> $modeldetalle['id_sys_rrhh_empleados_contrato_cod']])->one();
                                        
                                        $contrato->id_sys_rrhh_cedula            = $model->id_sys_rrhh_cedula;
                                        $contrato->id_sys_empresa                = $model->id_sys_empresa;
                                        $contrato->fecha_ingreso                 = $modeldetalle['fecha_ingreso'] == ''? null : $modeldetalle['fecha_ingreso']; 
                                        $contrato->fecha_salida                  = $modeldetalle['fecha_salida'] == '' ? null : $modeldetalle['fecha_salida']; 
                                        $contrato->cargo                         = $modeldetalle['cargo'];
                                        $contrato->id_sys_rrhh_causa_salida      = $modeldetalle['id_sys_rrhh_causa_salida'];
                                        $contrato->activo                        = $modeldetalle['activo'];
                                        $contrato->transaccion_usuario           = $user_transaccion;
                                        if (! ($flag = $contrato->save(false))) {
                                            $transaction->rollBack();
                                            break;
                                        }
                                        
                                    }
                                    else{
                                        
                                        if(empty($modeldetalle['fecha_ingreso'])){

                                        }else{
                                        
                                            $codcontrato  =  SysRrhhEmpleadosContratos::find()->select('max(id_sys_rrhh_empleados_contrato_cod)')->scalar() + 1 ;
                                        
                                            
                                            $fechaingreso  = $modeldetalle['fecha_ingreso'] == ''? 'null' : "'".$modeldetalle['fecha_ingreso']."'";
                                            $fechasalida   = $modeldetalle['fecha_salida']  == ''? 'null' : "'".$modeldetalle['fecha_salida']."'";
                                            
                                            
                                            $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_contratos ( id_sys_rrhh_empleados_contrato_cod, id_sys_rrhh_cedula, id_sys_empresa, fecha_ingreso, fecha_salida, cargo,id_sys_rrhh_causa_salida, activo, transaccion_usuario) values ('{$codcontrato}','{$model->id_sys_rrhh_cedula}','{$model->id_sys_empresa}',".$fechaingreso.", ".$fechasalida.",'{$modeldetalle['cargo']}','{$modeldetalle['id_sys_rrhh_causa_salida']}', ".$modeldetalle['activo'].", '{$user_transaccion}')");
                                            $nflag->execute();
                                            
                                            if(!$nflag){
                                                $flag = false;
                                                $transaction->rollBack();
                                                break;
                                            }
                                        }
                                        
                                    }
                                    
                                }
                                
                            }else{
                                
                                $flag= true;
                            }

                            ////contratos empleados 
                            if ($arraycargos){
                                
                                foreach ($arraycargos as $index => $modeldetalle) {
                                    
                                    
                                    if($modeldetalle['id_sys_rrhh_empleados_cargo_cod'] != ''){
                                        
                             
                                        $cargo    = SysRrhhEmpleadosCargos::find()->where(['id_sys_rrhh_empleados_cargo_cod'=> $modeldetalle['id_sys_rrhh_empleados_cargo_cod']])->one();
                                        
                                        $cargo->id_sys_rrhh_cedula            = $model->id_sys_rrhh_cedula;
                                        $cargo->fecha_ingreso                 = $modeldetalle['fecha_ingreso'] == ''? null : $modeldetalle['fecha_ingreso']; 
                                        $cargo->fecha_salida                  = $modeldetalle['fecha_salida'] == '' ? null : $modeldetalle['fecha_salida']; 
                                        $cargo->cargo                         = $modeldetalle['cargo'];
                                        $cargo->departamento                  = $modeldetalle['departamento'];
                                        $cargo->activo                        = $modeldetalle['activo'];
                                        $cargo->transaccion_usuario           = $user_transaccion;
                                        if (! ($flag = $cargo->save(false))) {
                                            $transaction->rollBack();
                                            break;
                                        }
                                        
                                    }
                                    else{
                                        
                                     
                                        if(empty($modeldetalle['fecha_ingreso'])){

                                        }else{

                                            $codcargo  =  SysRrhhEmpleadosCargos::find()->select('max(id_sys_rrhh_empleados_cargo_cod)')->scalar() + 1 ;
                                        
                                            
                                            $fechaingreso  = $modeldetalle['fecha_ingreso'] == ''? 'null' : "'".$modeldetalle['fecha_ingreso']."'";
                                            $fechasalida   = $modeldetalle['fecha_salida']  == ''? 'null' : "'".$modeldetalle['fecha_salida']."'";
                                            
                                            
                                            $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_cargos ( id_sys_rrhh_empleados_cargo_cod, id_sys_rrhh_cedula, fecha_ingreso, fecha_salida, cargo, departamento, activo, transaccion_usuario) values ('{$codcargo}','{$model->id_sys_rrhh_cedula}',".$fechaingreso.", ".$fechasalida.",'{$modeldetalle['cargo']}','{$modeldetalle['departamento']}', ".$modeldetalle['activo'].", '{$user_transaccion}')");
                                            $nflag->execute();
                                            
                                            if(!$nflag){
                                                $flag = false;
                                                $transaction->rollBack();
                                                break;
                                            }

                                        }
                                        
                                        
                                    }
                                    
                                }
                                
                            }else{
                                
                                $flag= true;
                            }
                            
                            
                            ////Rubros y Gastos personales 
                            
                            if ($arraygastos){
                                
                                foreach ($arraygastos as $index => $modeldetalle) {
                                    
                                    
                                    if($modeldetalle['id_sys_rrhh_empleados_gasto'] != ''){
                                        
                                        
                                        $gasto    =  SysRrhhEmpleadosGastos::find()->where(['id_sys_empresa'=> $model->id_sys_empresa, 'id_sys_rrhh_empleados_gasto'=> $modeldetalle['id_sys_rrhh_empleados_gasto']])->one();
                                        
                                        $gasto->id_sys_rrhh_cedula            = $model->id_sys_rrhh_cedula;
                                        $gasto->id_sys_empresa                = $model->id_sys_empresa;
                                        $gasto->id_sys_rrhh_rubros_gastos     = $modeldetalle['id_sys_rrhh_rubros_gastos'];
                                        $gasto->cantidad                      = $modeldetalle['cantidad'];
                                        $gasto->transaccion_usuario           = $user_transaccion;

                                        if (! ($flag = $gasto->save(false))) {
                                            $transaction->rollBack();
                                            break;
                                        }
                                        
                                    }
                                    else{
                                        
                                        if(empty($modeldetalle['cantidad'])){

                                        }else{

                                            $codgasto   =  SysRrhhEmpleadosGastos::find()->select('max(id_sys_rrhh_empleados_gasto)')->scalar() + 1; ;
                                        
                                            $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_gastos (id_sys_rrhh_empleados_gasto, id_sys_rrhh_cedula, id_sys_empresa, id_sys_rrhh_rubros_gastos, cantidad, transaccion_usuario) values ('{$codgasto}','{$model->id_sys_rrhh_cedula}','{$model->id_sys_empresa}','{$modeldetalle['id_sys_rrhh_rubros_gastos']}', '{$modeldetalle['cantidad']}', '{$user_transaccion}')");
                                            $nflag->execute();
                                            
                                            if(!$nflag){
                                                $flag = false;
                                                $transaction->rollBack();
                                                break;
                                            }
                                        }
                                        
                                    }
                                    
                                }
                                
                            }else{
                                
                                $flag= true;
                            }
                       
                    if ($flag) {
                        $transaction->commit();
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido actualizados con éxito! ',
                            'positonY' => 'top','positonX' => 'right']);
                        return $this->redirect(['index']);
                    }
                }
                
            }catch (Exception $e) {
          
                $transaction->rollBack();
               return   $e->getMessage();
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                    'positonY' => 'top','positonX' => 'right']);
                return $this->redirect(['index']);
            }
         
        }else{
      
     
        return $this->render('update', [
            'model' => $model,
            'nucleofamiliar'=> $nucleofamiliar,
            'horarios'=> $horarios,
            'haberes'=> $haberes,
            'sueldos'=> $sueldos,
            'contratos'=> $contratos,
            'cargos'=> $cargos,
            'gastos'=> $gastos,
            'documentos' => $documentos,
            'fotos'=> $fotos['baze64'] ?? '',
            'update'=>1,
        ]);
        }
        
    }

    public function actionUpdatebloqueo($id_sys_rrhh_cedula, $id_sys_empresa)
    {
        
        $this->layout = '@app/views/layouts/main_emplados';
        
        $model = $this->findModel($id_sys_rrhh_cedula, $id_sys_empresa);
 
        
        $model->nombre    =  $model->nombre;
        $model->apellidos =  $model->apellidos;
        $model->nombres   =  $model->nombre.' '.$model->apellidos;
        
        
        $nucleofamiliar = [];
        $horarios       = [];
        $haberes        = [];
        $contratos      = [];
        $cargos         = [];
        $sueldos        = [];
        $gastos         = [];
        $documentos     = [];
        $fotos          = '';

        
        $db =  $_SESSION['db'];
    
        $datosnucleo= SysRrhhEmpleadosNucleoFamiliar::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula, 'id_sys_empresa'=> $id_sys_empresa])->all();
        
        //nucleo familiar
        if ($datosnucleo){
            foreach ($datosnucleo as $data){
                $objnucleo                                 = new SysRrhhEmpleadosNucleoFamiliar();
                $objnucleo->id_sys_rrhh_empleados_fam_cod  = $data->id_sys_rrhh_empleados_fam_cod;
                $objnucleo->id_sys_rrhh_cedula             = $data->id_sys_rrhh_cedula;
                $objnucleo->id_sys_empresa                 = $data->id_sys_empresa;
                $objnucleo->nombres                        = $data->nombres;
                $objnucleo->parentesco                     = $data->parentesco;
                $objnucleo->utilidad                       = $data->utilidad;
                $objnucleo->tribunal                       = $data->tribunal;
                $objnucleo->rentas                         = $data->rentas;
                $objnucleo->profesion                      = $data->profesion;
                $objnucleo->fecha_nacimiento               = $data->fecha_nacimiento; 
                $objnucleo->discapacidad                   = $data->discapacidad;
                $objnucleo->edad                           = 2;
                array_push($nucleofamiliar, $objnucleo);
            }
          }else{
             array_push($nucleofamiliar, new SysRrhhEmpleadosNucleoFamiliar());
           }
         //fin de nucle familiar
         
         //horarios 
           $datoshorarios = SysRrhhEmpleadosHorario::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula, 'id_sys_empresa'=> $id_sys_empresa])->all();
           
           //nucleo familiar
           if ($datoshorarios){
               foreach ($datoshorarios as $data){
                   
                   $objhorario                                 = new SysRrhhEmpleadosHorario();
                   $objhorario->id_sys_rrhh_empleados_horario  = $data->id_sys_rrhh_empleados_horario;
                   $objhorario->id_sys_rrhh_horario            = $data->id_sys_rrhh_horario;
                   $objhorario->id_sys_rrhh_cedula             = $data->id_sys_rrhh_cedula;
                   $objhorario->id_sys_empresa                 = $data->id_sys_empresa;
          
     
                   array_push($horarios, $objhorario);
               }
           }else{
               array_push($horarios, new SysRrhhEmpleadosHorario());
           }
         //fin de jornadas laborales   
          
          //haberes 
           $datoshaberes  = SysRrhhEmpleadosHaberes::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula, 'id_sys_empresa'=> $id_sys_empresa])->all();
           
           if ($datoshaberes){
               
           foreach ($datoshaberes as $data){
                   $objhaber                                = new SysRrhhEmpleadosHaberes();
                   $objhaber->id_sys_rrhh_empleados_haber    = $data->id_sys_rrhh_empleados_haber;
                   $objhaber->id_sys_rrhh_cedula             = $data->id_sys_rrhh_cedula;
                   $objhaber->id_sys_empresa                 = $data->id_sys_empresa;
                   $objhaber->id_sys_rrhh_concepto           = $data->id_sys_rrhh_concepto;
                   $objhaber->decimo                         = $data->decimo;
                   $objhaber->anio_ini                       = intval($data->anio_ini);
                   $objhaber->mes_ini                        = intval($data->mes_ini);
                   $objhaber->anio_fin                       = intval($data->anio_fin);
                   $objhaber->mes_fin                        = intval($data->mes_fin);
                   $objhaber->pago                           = $data->pago;
                   $objhaber->cantidad                       = $data->cantidad;
                   $objhaber->estado                         = $data->estado;
                   $objhaber->transaccion_usuario            = $data->transaccion_usuario;
                   
                   array_push($haberes, $objhaber);
              }
               
           }else{
               array_push($haberes, new SysRrhhEmpleadosHaberes());
           }
           
           $datoscontratos  = SysRrhhEmpleadosContratos::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula, 'id_sys_empresa'=> $id_sys_empresa])->all();
           
           if ($datoscontratos){
               
               foreach ($datoscontratos as $data){
                   
                   $objcontratos  = new SysRrhhEmpleadosContratos();
                   $objcontratos->id_sys_rrhh_empleados_contrato_cod = $data->id_sys_rrhh_empleados_contrato_cod;
                   $objcontratos->id_sys_rrhh_cedula                 = $data->id_sys_rrhh_cedula;
                   $objcontratos->id_sys_empresa                     = $data->id_sys_empresa;
                   $objcontratos->fecha_ingreso                      = $data->fecha_ingreso;   
                   $objcontratos->fecha_salida                       = $data->fecha_salida; 
                   $objcontratos->cargo                              = $data->cargo;
                   $objcontratos->id_sys_rrhh_causa_salida           = $data->id_sys_rrhh_causa_salida;
                   $objcontratos->transaccion_usuario                = $data->transaccion_usuario;
                   $objcontratos->activo                             = $data->activo;
                   
                   array_push($contratos, $objcontratos);
                   
               }    
           }else{
               
               array_push($contratos, new SysRrhhEmpleadosContratos());
               
           }

           $datoscargos  = SysRrhhEmpleadosCargos::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])->all();
           
           if ($datoscargos){
               
               foreach ($datoscargos as $data){
                   
                   $objcargos  = new SysRrhhEmpleadosCargos();
                   $objcargos->id_sys_rrhh_empleados_cargo_cod    = $data->id_sys_rrhh_empleados_cargo_cod;
                   $objcargos->id_sys_rrhh_cedula                 = $data->id_sys_rrhh_cedula;
                   $objcargos->fecha_ingreso                      = $data->fecha_ingreso;   
                   $objcargos->fecha_salida                       = $data->fecha_salida; 
                   $objcargos->cargo                              = $data->cargo;
                   $objcargos->departamento                       = $data->departamento;
                   $objcargos->transaccion_usuario                = $data->transaccion_usuario;
                   $objcargos->activo                             = $data->activo;
                   
                   array_push($cargos, $objcargos);
                   
               }    
           }else{
               
               array_push($cargos, new SysRrhhEmpleadosCargos());
               
           }
           
           $datossueldos  = SysRrhhEmpleadosSueldos::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula, 'id_sys_empresa'=> $id_sys_empresa])->all();
           
           
           if ($datossueldos){
               
               
               foreach ($datossueldos as $data){
                   
                   $objsueldo = new SysRrhhEmpleadosSueldos();
                   $objsueldo->id_sys_rrhh_empleados_sueldo_cod = $data->id_sys_rrhh_empleados_sueldo_cod;
                   $objsueldo->id_sys_rrhh_cedula                 = $data->id_sys_rrhh_cedula;
                   $objsueldo->id_sys_empresa                     = $data->id_sys_empresa;
                   $objsueldo->fecha                              = $data->fecha;
                   $objsueldo->estado                             = $data->estado;
                   $objsueldo->sueldo                             = $data->sueldo;
                   $objsueldo->por_anticipo                       = $data->por_anticipo;
                   $objsueldo->sueldo_anticipo                    = $data->sueldo_anticipo;
                   $objsueldo->transaccion_usuario                = $data->transaccion_usuario;
                    
                   array_push($sueldos, $objsueldo); 
                   
               }
              
               
           }else{
               
               array_push($sueldos, new SysRrhhEmpleadosSueldos());  
               
           }
           
           $datosgastos   = SysRrhhEmpleadosGastos::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula, 'id_sys_empresa'=> $id_sys_empresa])->all();
           
           if($datosgastos){
               
               foreach ($datosgastos as $data ) {
                   
                   $objgastos= new  SysRrhhEmpleadosGastos();
                   
                   $objgastos->id_sys_rrhh_empleados_gasto =  $data->id_sys_rrhh_empleados_gasto;
                   $objgastos->id_sys_rrhh_cedula          =  $data->id_sys_rrhh_cedula;
                   $objgastos->id_sys_empresa              =  $data->id_sys_empresa;
                   $objgastos->id_sys_rrhh_rubros_gastos   =  $data->id_sys_rrhh_rubros_gastos;
                   $objgastos->cantidad                    =  $data->cantidad;
                   
                   array_push($gastos, $objgastos); 
                   
               }
                 
           }else{
               
               array_push($gastos, new SysRrhhEmpleadosGastos());  
             
           }

           $datosdocumentos   = SysRrhhEmpleadosDocumentos::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])->all();
           
           if($datosdocumentos){
               
               foreach ($datosdocumentos as $data ) {
                   
                   $objdocumentos= new  SysRrhhEmpleadosDocumentos();
                   
                   $objdocumentos->idDocumento           =  $data->idDocumento;
                   $objdocumentos->id_sys_rrhh_cedula    =  $data->id_sys_rrhh_cedula;
                   $objdocumentos->ruta                  =  $data->ruta;
                
                   
                   array_push($documentos, $objdocumentos); 
                   
               }
                 
           }else{
               
               array_push($documentos, new SysRrhhEmpleadosDocumentos());  
             
           }
           
           $fotos =   Yii::$app->$db->createCommand("select foto_64,  foto, baze64 from sys_rrhh_empleados_foto cross apply (select foto as '*' for xml path('')) T (baze64) where id_sys_rrhh_cedula = '{$model->id_sys_rrhh_cedula}'")->queryOne();
           
           SysRrhhEmpleadosFoto::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula, 'id_sys_empresa'=> $id_sys_empresa])->one();
           
           
        if ($model->load(Yii::$app->request->post())) {
            
            
            $oldIDs    = ArrayHelper::map($nucleofamiliar, 'id_sys_rrhh_empleados_fam_cod', 'id_sys_rrhh_empleados_fam_cod');
            $oldIDshor = ArrayHelper::map($horarios, 'id_sys_rrhh_empleados_horario', 'id_sys_rrhh_empleados_horario');
            $oldIDshab = ArrayHelper::map($haberes, 'id_sys_rrhh_empleados_haber', 'id_sys_rrhh_empleados_haber');
            //$oldIDsuel = ArrayHelper::map($sueldos, 'id_sys_rrhh_empleados_sueldo_cod', 'id_sys_rrhh_empleados_sueldo_cod');
            //$oldIDcont = ArrayHelper::map($contratos,'id_sys_rrhh_empleados_contrato_cod', 'id_sys_rrhh_empleados_contrato_cod');
            $oldIDgast = ArrayHelper::map($gastos,'id_sys_rrhh_empleados_gasto', 'id_sys_rrhh_empleados_gasto');
            
            $arraynucleo    = Yii::$app->request->post('SysRrhhEmpleadosNucleoFamiliar');
            $arrayhorario   = Yii::$app->request->post('SysRrhhEmpleadosHorario');
            $arrayhaberes   = Yii::$app->request->post('SysRrhhEmpleadosHaberes');
            $arraysueldos   = Yii::$app->request->post('SysRrhhEmpleadosSueldos');
            $arraycontratos = Yii::$app->request->post('SysRrhhEmpleadosContratos');
            $arraygastos    = Yii::$app->request->post('SysRrhhEmpleadosGastos');
            
           
           
            if ($arraynucleo){
            
               $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($arraynucleo, 'id_sys_rrhh_empleados_fam_cod', 'id_sys_rrhh_empleados_fam_cod')));
             
            }else{
                
                SysRrhhEmpleadosNucleoFamiliar::deleteAll(['id_sys_rrhh_empleados_fam_cod' => $oldIDs]);
            }
            
            if ($arrayhorario) {
                
                $deletedIDshor = array_diff($oldIDshor, array_filter(ArrayHelper::map($arrayhorario, 'id_sys_rrhh_empleados_horario', 'id_sys_rrhh_empleados_horario')));
                
            }else{
                
                SysRrhhEmpleadosHorario::deleteAll(['id_sys_rrhh_empleados_horario'=> $oldIDshor]);
                
 
            }
            
            
            if ($arraygastos){
                
                $deletedIDgast = array_diff($oldIDgast, array_filter(ArrayHelper::map($arraygastos, 'id_sys_rrhh_empleados_gasto', 'id_sys_rrhh_empleados_gasto')));
                
            }else{
                
                
                SysRrhhEmpleadosGastos::deleteAll(['id_sys_rrhh_empleados_gasto'=> $oldIDgast]);
                
                
            }
            
                   
            if(!empty($deletedIDs)){
                
                SysRrhhEmpleadosNucleoFamiliar::deleteAll(['id_sys_rrhh_empleados_fam_cod' => $deletedIDs]);
            }
            
            if (!empty($deletedIDshor)){
                
                SysRrhhEmpleadosHorario::deleteAll(['id_sys_rrhh_empleados_horario'=> $deletedIDshor]);
                
            }
     
            if (!empty($deletedIDshab)){
                
                SysRrhhEmpleadosHaberes::deleteAll(['id_sys_rrhh_empleados_haber'=> $deletedIDshab]);
                
            }
            
            if (!empty($deletedIDgast)){
                
                SysRrhhEmpleadosGastos::deleteAll(['id_sys_rrhh_empleados_gasto'=> $deletedIDgast]);
                
            }
            
            
            $transaction = \Yii::$app->$db->beginTransaction();
            
            $user_transaccion = Yii::$app->user->username;
            
            try {
               
                $model->file             =  UploadedFile::getInstance($model, 'file');

                $model->nombres          = trim($model->apellidos.' '.$model->nombre);
                
                $model->nombre           = trim($model->nombre);
                
                $model->apellidos        = trim($model->apellidos);
                
                $model->email            = trim($model->email);
                    
                $model->fecha_nacimiento =   $model->fecha_nacimiento;
                
                $model->transaccion_usuario = $user_transaccion;

                if(!empty($model->numero_uniforme )){
                    $existente = SysRrhhEmpleadosUniformes::find()->select('id_sys_rrhh_cedula')->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                    $ids = ArrayHelper::getValue($existente, 'id_sys_rrhh_cedula');
                    if( $ids != $model->id_sys_rrhh_cedula){
                        $codigo = Yii::$app->$db->createCommand("select ISNULL(max(id_sys_rrhh_empleado_uniforme),0) + 1 from sys_rrhh_empleados_uniformes")->queryScalar();                            
                        Yii::$app->$db->createCommand("INSERT INTO sys_rrhh_empleados_uniformes(id_sys_rrhh_empleado_uniforme,fecha_entrega,id_sys_rrhh_cedula,numero_uniforme,id_sys_empresa) VALUES ('$codigo',GETDATE(),'$model->id_sys_rrhh_cedula','$model->numero_uniforme','001')")->execute();
                    }else{
                        Yii::$app->$db->createCommand("update sys_rrhh_empleados_uniformes set numero_uniforme='$model->numero_uniforme' where id_sys_rrhh_cedula='$model->id_sys_rrhh_cedula'")->execute();
                    }
                }else{
                    $model->numero_uniforme = Null;
                }
                
                if ($model->file){
                
                        $modelfoto =  SysRrhhEmpleadosFoto::find()->where(['id_sys_empresa'=> $model->id_sys_empresa, 'id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                       
                        if ($modelfoto){
                            
                             $model->file->saveAs("C:/fotos/".$model->id_sys_rrhh_cedula.'.'.$model->file->extension);
                        
                             $ruta =  "C:\'fotos\'".$model->id_sys_rrhh_cedula.'.'.$model->file->extension;
                        
                             $ruta = str_replace("'", "", $ruta);
                            
                         
                             Yii::$app->$db->createCommand("update sys_rrhh_empleados_foto
                                    set foto = (
                                    SELECT *
                                    FROM OPENROWSET(BULK '".$ruta."', SINGLE_BLOB) test)
                                    where id_sys_rrhh_cedula = '{$model->id_sys_rrhh_cedula}'")->execute();
                             
                            
              
                        }else{
                            
                                $model->file->saveAs("C:/fotos/".$model->id_sys_rrhh_cedula.'.'.$model->file->extension);
                                
                                $ruta =  "C:\'fotos\'".$model->id_sys_rrhh_cedula.'.'.$model->file->extension;
                                
                                $ruta = str_replace("'", "", $ruta);
                            
                              
                                 Yii::$app->$db->createCommand("Insert sys_rrhh_empleados_foto (id_sys_rrhh_cedula, foto, id_sys_empresa)
                                                        Select '{$model->id_sys_rrhh_cedula}', BulkColumn, '001'
                                                         from Openrowset (Bulk '".$ruta."', Single_Blob) as Image")->execute();
                                 
                          }
                    }
                 
                    $model->file2             =  UploadedFile::getInstances($model, 'file2');
                                    
                    if ($model->file2){
                                        
                        foreach($model->file2 as $file){

                            $codnucleo        =  SysRrhhEmpleadosDocumentos::find()->select('max(idDocumento)')->scalar() + 1;

                            $file->saveAs('pdf/expedientes/'. $codnucleo .'_'. $model->id_sys_rrhh_cedula .'.'. $file->extension);
                            $ruta = "pdf/expedientes/". $codnucleo ."_". $model->id_sys_rrhh_cedula .".". $file->extension;
                                    
                            Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_llamados_atencion_documentos (idDocumento, id_sys_rrhh_cedula, ruta) values ('{$codnucleo}','{$model->id_sys_rrhh_cedula}','{$ruta}')")->execute();
                                      
                        }
                                
                    }
                
                  
                if ($flag = $model->save(false)) {
                    
                           //nucleo familiar 
                            if ($arraynucleo){
                                
                                    foreach ($arraynucleo as $index => $modeldetalle) {
                                        
                                          
                                            if($modeldetalle['id_sys_rrhh_empleados_fam_cod'] != ''){
                                                
                                               
                                            
                                                
                                                $md =  SysRrhhEmpleadosNucleoFamiliar::find()
                                                ->where(['id_sys_empresa'=> $model->id_sys_empresa, 'id_sys_rrhh_empleados_fam_cod'=> $modeldetalle['id_sys_rrhh_empleados_fam_cod']])
                                                ->one();
                                               
                                                    $md->id_sys_rrhh_cedula             = $model->id_sys_rrhh_cedula;
                                                    $md->id_sys_empresa                 = $model->id_sys_empresa;
                                                    $md->nombres                        = $modeldetalle['nombres'];
                                                    $md->parentesco                     = $modeldetalle['parentesco'];
                                                    $md->utilidad                       = $modeldetalle['utilidad'];
                                                    $md->tribunal                       = $modeldetalle['tribunal'];
                                                    $md->rentas                         = $modeldetalle['rentas'];
                                                    $md->profesion                      = $modeldetalle['profesion'];
                                                    $md->fecha_nacimiento               = $modeldetalle['fecha_nacimiento'];
                                                    $md->discapacidad                   = $modeldetalle['discapacidad'];
                                                    $md->transaccion_usuario            = $user_transaccion;
                                                    
                                                if (! ($flag = $md->save(false))) {
                                                    $transaction->rollBack();
                                                    break;
                                                }
                                            }
                                            else{
                                                
                                               //gernerar por query ya el driver odbc sysbase  no soporta lastid 
                                               
                                                $codnucleo =  SysRrhhEmpleadosNucleoFamiliar::find()->select('max(id_sys_rrhh_empleados_fam_cod)')->scalar() + 1;
                                                
                                                $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_nucleo_familiar (id_sys_rrhh_empleados_fam_cod, id_sys_rrhh_cedula, id_sys_empresa, nombres, parentesco, utilidad, profesion, fecha_nacimiento, discapacidad, tribunal, rentas, transaccion_usuario) values ('{$codnucleo}','{$model->id_sys_rrhh_cedula}','{$model->id_sys_empresa}','{$modeldetalle['nombres']}','{$modeldetalle['parentesco']}','{$modeldetalle['utilidad']}','{$modeldetalle['profesion']}', '".$modeldetalle['fecha_nacimiento']."','{$modeldetalle['discapacidad']}', {$modeldetalle['tribunal']},{$modeldetalle['rentas']}, '{$user_transaccion}')");
                                                $nflag->execute();
                                                
                                                if(!$nflag){
                                                    $flag = false;
                                                    $transaction->rollBack();
                                                    break;
                                                }
                                                
                                                
                                            }
                                            
                                        
                                    }
                                       
                            }else{
                                
                                $flag= true;
                            }
                            //jornadadas
                    
                            if ($arrayhorario){
                                
                                foreach ($arrayhorario as $index => $modeldetalle) {
                                    
                                    
                                    if($modeldetalle['id_sys_rrhh_empleados_horario'] != ''){
                                       
                                       $horario   = SysRrhhEmpleadosHorario::find()->where(['id_sys_empresa'=> $model->id_sys_empresa, 'id_sys_rrhh_empleados_horario'=> $modeldetalle['id_sys_rrhh_empleados_horario']])->one();
                                       
                                       $horario->id_sys_rrhh_cedula  =  $model->id_sys_rrhh_cedula;
                                       $horario->id_sys_empresa      =  $model->id_sys_empresa;
                                       $horario->id_sys_rrhh_horario = $modeldetalle['id_sys_rrhh_horario'];
                                       $horario->transaccion_usuario  = $user_transaccion;
                                       
                                       if (! ($flag = $horario->save(false))) {
                                           $transaction->rollBack();
                                           break;
                                       }
                                      
                                       
                                    }
                                    else{
                                        
                                        $codhorario =  SysRrhhEmpleadosHorario::find()->select('max(id_sys_rrhh_empleados_horario)')->scalar() + 1 ;
                                     
                                        $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_horario (id_sys_rrhh_empleados_horario, id_sys_rrhh_cedula, id_sys_rrhh_horario, id_sys_empresa, transaccion_usuario) values ('{$codhorario}','{$model->id_sys_rrhh_cedula}', '{$modeldetalle['id_sys_rrhh_horario']}', '{$model->id_sys_empresa}', '{$user_transaccion}')");
                                        $nflag->execute();
                                        
                                        if(!$nflag){
                                            $flag = false;
                                            $transaction->rollBack();
                                            break;
                                        }
                                       
                                    }
                                
                                }
                                
                            }else{
                                
                                $flag= true;
                            }
                    
                            //haberes 
                            if ($arrayhaberes){
                                
                                foreach ($arrayhaberes as $index => $modeldetalle) {
                                    
                                    
                                    if($modeldetalle['id_sys_rrhh_empleados_haber'] != ''){
                                        
                                        
                                            $haber     = SysRrhhEmpleadosHaberes::find()->where(['id_sys_empresa'=> $model->id_sys_empresa, 'id_sys_rrhh_empleados_haber'=> $modeldetalle['id_sys_rrhh_empleados_haber']])->one();
                                        
                                            $haber->id_sys_rrhh_cedula             = $model->id_sys_rrhh_cedula;
                                            $haber->id_sys_empresa                 = $model->id_sys_empresa;
                                            $haber->id_sys_rrhh_concepto           = $modeldetalle['id_sys_rrhh_concepto'];
                                            $haber->decimo                         = $modeldetalle['decimo'];
                                            $haber->anio_ini                       =  intval($modeldetalle['anio_ini']);
                                            $haber->mes_ini                        =  intval($modeldetalle['mes_ini']);
                                            $haber->anio_fin                       =  intval($modeldetalle['anio_fin']);
                                            $haber->mes_fin                        =  intval($modeldetalle['mes_fin']);
                                            $haber->pago                           = $modeldetalle['pago'];
                                            $haber->cantidad                       = $modeldetalle['cantidad'];
                                            $haber->transaccion_usuario            = $user_transaccion;
                                            
                                            if (! ($flag = $haber->save(false))) {
                                                $transaction->rollBack();
                                                break;
                                            }
                                        
                                    }
                                    else{
                                                            
                                        $codhaberes  = SysRrhhEmpleadosHaberes::find()->select('max(id_sys_rrhh_empleados_haber)')->scalar() + 1 ;
                                       
                                        $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_haberes (id_sys_rrhh_empleados_haber, id_sys_rrhh_cedula, id_sys_empresa, id_sys_rrhh_concepto,  decimo, anio_ini, mes_ini, anio_fin, mes_fin, pago, cantidad, transaccion_usuario) values ('{$codhaberes}','{$model->id_sys_rrhh_cedula}','{$model->id_sys_empresa}', '{$modeldetalle['id_sys_rrhh_concepto']}','{$modeldetalle['decimo']}','{$modeldetalle['anio_ini']}','{$modeldetalle['mes_ini']}','{$modeldetalle['anio_fin']}','{$modeldetalle['mes_fin']}','{$modeldetalle['pago']}', '{$modeldetalle['cantidad']}', '{$user_transaccion}')");
                                        $nflag->execute();
                                        
                                        if(!$nflag){
                                            $flag = false;
                                            $transaction->rollBack();
                                            break;
                                        }
                                      
                                        
                                    }
                                    
                              }
                                
                            }else{
                                
                                $flag= true;
                            }
                            
                            //sueldos de los empleadores
                            
                            if ($arraysueldos){
                                
                                foreach ($arraysueldos as $index => $modeldetalle) {
                                    
                                    
                                    if($modeldetalle['id_sys_rrhh_empleados_sueldo_cod'] != ''){
                                        
                                        
                                        
                            
                                        $sueldo    = SysRrhhEmpleadosSueldos::find()->where(['id_sys_empresa'=> $model->id_sys_empresa, 'id_sys_rrhh_empleados_sueldo_cod'=> $modeldetalle['id_sys_rrhh_empleados_sueldo_cod']])->one();
                                        
                                        $sueldo->id_sys_rrhh_cedula            = $model->id_sys_rrhh_cedula;
                                        $sueldo->id_sys_empresa                = $model->id_sys_empresa;
                                        $sueldo->fecha                         = $modeldetalle['fecha'] == '' ? null : $modeldetalle['fecha']; 
                                        $sueldo->estado                        = $modeldetalle['estado'];
                                        $sueldo->sueldo                        = $modeldetalle['sueldo'];
                                        $sueldo->por_anticipo                  = $modeldetalle['por_anticipo'];
                                        $sueldo->sueldo_anticipo               = $modeldetalle['sueldo_anticipo'];
                                        $sueldo->transaccion_usuario           = $user_transaccion;
                                     
                                        
                                        if (! ($flag = $sueldo->save(false))) {
                                            $transaction->rollBack();
                                            break;
                                        }
                                        
                                    }
                                    else{
                                        
                                        
                                        
                                        $fecha        = $modeldetalle['fecha'] == '' ? 'null' : "'".$modeldetalle['fecha']."'";
                                        
                                        $sueldo       = $modeldetalle['sueldo'] == '' ? 0 :  $modeldetalle['sueldo'];
                                        
                                        $anticipo     = $modeldetalle['sueldo_anticipo'] == '' ? 0 :  $modeldetalle['sueldo_anticipo'];
                                        
                                        $poranticipo  = $modeldetalle['por_anticipo'] == '' ? 0 :  $modeldetalle['por_anticipo'];
                                                                        
                                        $codsueldo    = SysRrhhEmpleadosSueldos::find()->select('max(id_sys_rrhh_empleados_sueldo_cod)')->scalar() + 1;
                                
                                        $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_sueldos (id_sys_rrhh_empleados_sueldo_cod, id_sys_rrhh_cedula, id_sys_empresa, fecha, estado, sueldo, por_anticipo, sueldo_anticipo, transaccion_usuario) values ('{$codsueldo}','{$model->id_sys_rrhh_cedula}','{$model->id_sys_empresa}',".$fecha.",'{$modeldetalle['estado']}',".$sueldo.", ".$poranticipo.",".$anticipo.", '{$user_transaccion}')");
                                        $nflag->execute();
                                        
                                        if(!$nflag){
                                            $flag = false;
                                            $transaction->rollBack();
                                            break;
                                        }
                                        
                                        
                                    }
                                    
                                }
                                
                            }else{
                                
                                $flag= true;
                            }
                            
                            ////contratos empleados 
                            if ($arraycontratos){
                                
                                foreach ($arraycontratos as $index => $modeldetalle) {
                                    
                                    
                                    if($modeldetalle['id_sys_rrhh_empleados_contrato_cod'] != ''){
                                        
                             
                                        $contrato    = SysRrhhEmpleadosContratos::find()->where(['id_sys_empresa'=> $model->id_sys_empresa, 'id_sys_rrhh_empleados_contrato_cod'=> $modeldetalle['id_sys_rrhh_empleados_contrato_cod']])->one();
                                        
                                        $contrato->id_sys_rrhh_cedula            = $model->id_sys_rrhh_cedula;
                                        $contrato->id_sys_empresa                = $model->id_sys_empresa;
                                        $contrato->fecha_ingreso                 = $modeldetalle['fecha_ingreso'] == ''? null : $modeldetalle['fecha_ingreso']; 
                                        $contrato->fecha_salida                  = $modeldetalle['fecha_salida'] == '' ? null : $modeldetalle['fecha_salida']; 
                                        $contrato->cargo                         = $modeldetalle['cargo'];
                                        $contrato->id_sys_rrhh_causa_salida      = $modeldetalle['id_sys_rrhh_causa_salida'];
                                        $contrato->activo                        = $modeldetalle['activo'];
                                        $contrato->transaccion_usuario           = $user_transaccion;
                                        if (! ($flag = $contrato->save(false))) {
                                            $transaction->rollBack();
                                            break;
                                        }
                                        
                                    }
                                    else{
                                        
                                     
                                        $codcontrato  =  SysRrhhEmpleadosContratos::find()->select('max(id_sys_rrhh_empleados_contrato_cod)')->scalar() + 1 ;
                                       
                                        
                                         $fechaingreso  = $modeldetalle['fecha_ingreso'] == ''? 'null' : "'".$modeldetalle['fecha_ingreso']."'";
                                         $fechasalida   = $modeldetalle['fecha_salida']  == ''? 'null' : "'".$modeldetalle['fecha_salida']."'";
                                        
                                        
                                        $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_contratos ( id_sys_rrhh_empleados_contrato_cod, id_sys_rrhh_cedula, id_sys_empresa, fecha_ingreso, fecha_salida, cargo, id_sys_rrhh_causa_salida, activo, transaccion_usuario) values ('{$codcontrato}','{$model->id_sys_rrhh_cedula}','{$model->id_sys_empresa}',".$fechaingreso.", ".$fechasalida.",'{$modeldetalle['cargo']}','{$modeldetalle['id_sys_rrhh_causa_salida']}', ".$modeldetalle['activo'].", '{$user_transaccion}')");
                                        $nflag->execute();
                                        
                                        if(!$nflag){
                                            $flag = false;
                                            $transaction->rollBack();
                                            break;
                                        }
                                        
                                        
                                    }
                                    
                                }
                                
                            }else{
                                
                                $flag= true;
                            }
                            
                            ////Rubros y Gastos personales 
                            
                            if ($arraygastos){
                                
                                foreach ($arraygastos as $index => $modeldetalle) {
                                    
                                    
                                    if($modeldetalle['id_sys_rrhh_empleados_gasto'] != ''){
                                        
                                        
                                        $gasto    =  SysRrhhEmpleadosGastos::find()->where(['id_sys_empresa'=> $model->id_sys_empresa, 'id_sys_rrhh_empleados_gasto'=> $modeldetalle['id_sys_rrhh_empleados_gasto']])->one();
                                        
                                        $gasto->id_sys_rrhh_cedula            = $model->id_sys_rrhh_cedula;
                                        $gasto->id_sys_empresa                = $model->id_sys_empresa;
                                        $gasto->id_sys_rrhh_rubros_gastos     = $modeldetalle['id_sys_rrhh_rubros_gastos'];
                                        $gasto->cantidad                      = $modeldetalle['cantidad'];
                                        $gasto->transaccion_usuario           = $user_transaccion;

                                        if (! ($flag = $gasto->save(false))) {
                                            $transaction->rollBack();
                                            break;
                                        }
                                        
                                    }
                                    else{
                                        
                                        $codgasto   =  SysRrhhEmpleadosGastos::find()->select('max(id_sys_rrhh_empleados_gasto)')->scalar() + 1; ;
                                      
                                        $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_empleados_gastos (id_sys_rrhh_empleados_gasto, id_sys_rrhh_cedula, id_sys_empresa, id_sys_rrhh_rubros_gastos, cantidad, transaccion_usuario) values ('{$codgasto}','{$model->id_sys_rrhh_cedula}','{$model->id_sys_empresa}','{$modeldetalle['id_sys_rrhh_rubros_gastos']}', '{$modeldetalle['cantidad']}', '{$user_transaccion}')");
                                        $nflag->execute();
                                        
                                        if(!$nflag){
                                            $flag = false;
                                            $transaction->rollBack();
                                            break;
                                        }
                                        
                                    }
                                    
                                }
                                
                            }else{
                                
                                $flag= true;
                            }
                       
                    if ($flag) {
                        $transaction->commit();
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido actualizados con éxito! ',
                            'positonY' => 'top','positonX' => 'right']);
                        return $this->redirect(['index']);
                    }
                }
                
            }catch (Exception $e) {
          
                $transaction->rollBack();
               return   $e->getMessage();
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                    'positonY' => 'top','positonX' => 'right']);
                return $this->redirect(['index']);
            }
         
        }else{
      
     
        return $this->render('updatebloqueo', [
            'model' => $model,
            'nucleofamiliar'=> $nucleofamiliar,
            'horarios'=> $horarios,
            'haberes'=> $haberes,
            'sueldos'=> $sueldos,
            'contratos'=> $contratos,
            'cargos' => $cargos,
            'gastos'=> $gastos,
            'documentos' => $documentos,
            'fotos'=> $fotos['baze64'],
            'update'=>1,
        ]);
        }
        
    }

    public  function actionContrato($id_sys_rrhh_cedula, $id_sys_empresa){

        $db =  $_SESSION['db'];
        $datos = Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDatosFormatoContratoEmpleado] @id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")->queryOne();
       
     
        $html =    $this->renderPartial('_impresioncontratos',['datos'=> $datos ]);
    
       /* $mpdf = new Mpdf([
            'format' => 'A4',
            // 'orientation' => 'L'
        ]);
        $mpdf->WriteHTML($html);
         $mpdf->Output('SolicitudVacaciones.pdf', 'I');
        exit();
       */
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
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:10px} .sin_margen{margin: 0px;} .line{ border-bottom: 1px solid black; margin-top: 20px;} .title{font-size: 10px;font-weight: bold;} .subtitle{font-size: 10px;} .negrita{font-weight: bold;} table {width: 100%} td { margin: 1px;} .margen-left{ margin: 45px;}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Certificado Laboral'],
            // call mPDF methods on the fly
            'methods' => [
                'SetHeader'=>[''],
                'SetFooter'=>[''],
            ]
        ]);
        
        // return the pdf output as per the destination setting
        return $pdf->render(); 
        
    }
    
    public function actionDocumento($id_llamado)
    {
        $this->redirect(Yii::$app->homeUrl.'pdf/certificados-laborales/2045.pdf');
    }

    public  function actionImprimecredencial($id_sys_rrhh_cedula, $id_sys_empresa){

        $datos =  (new \yii\db\Query())
        ->select(["sys_rrhh_empleados.id_sys_rrhh_cedula","nombres","nombre","apellidos","tipo_sangre","departamento","color","color_fuente","codigo_temp as barra","sys_adm_cargos.id_sys_adm_cargo"])
        ->from("sys_rrhh_empleados")
        ->innerJoin("sys_adm_cargos","sys_rrhh_empleados.id_sys_adm_cargo = sys_adm_cargos.id_sys_adm_cargo")
        ->innerJoin("sys_adm_departamentos","sys_adm_cargos.id_sys_adm_departamento = sys_adm_departamentos.id_sys_adm_departamento")
        ->where("sys_rrhh_empleados.id_sys_empresa = '{$id_sys_empresa}'")
        ->andwhere("sys_rrhh_empleados.id_sys_rrhh_cedula  = '{$id_sys_rrhh_cedula}'")
        ->one(SysRrhhEmpleados::getDb());
       
     
        return   $this->render('_credencial',['datos'=>$datos]);
      

    }
    
    public function actionImprimecredenciallote(){
  
        $obj     =  json_decode(Yii::$app->request->post('datos'));
  
        return  $this->renderPartial('_imprimelotecredencial',['empleados'=> $obj->empleados ]);
        
 
        
    }
    
    
    
    public function actionCredenciallote(){
        
        
        $this->layout = '@app/views/layouts/main_emplados';
        
        $empleados  =  SysRrhhEmpleados::find()->where(['estado'=> 'A'])->orderBy('nombres')->all();
        return $this->render('_credenciallote', ['empleados'=> $empleados]);
        
    }
    
    
    
    public function actionEmpleadosarea($area){
        
            $emp   = [];
            
            $datos = [];
            
            $datos = SysRrhhEmpleados::find()->select(['id_sys_rrhh_cedula', 'nombres'])
            ->joinWith(['sysAdmCargo'])
            ->join('join', 'sys_adm_departamentos', 'sys_adm_cargos.id_sys_adm_departamento =  sys_adm_departamentos.id_sys_adm_departamento')
            ->andWhere(["sys_rrhh_empleados.id_sys_empresa"=> "001"])
            ->andWhere(['sys_rrhh_empleados.estado'=> 'A'])
            ->andFilterWhere(["sys_adm_departamentos.id_sys_adm_area"=> $area])
            ->orderBy(['nombres'=>SORT_ASC])
            ->all();
            
            
            foreach ($datos as $data):
            
                $emp [] = array('id_sys_rrhh_cedula'=> $data['id_sys_rrhh_cedula'], 'nombres'=> $data['nombres']);
            
            
            endforeach;
            
            return json_encode($emp);
    }
    
    public function actionEmpleadosdepartamento($area, $departamento){
        
              
                $emp    = [];
                
                $datos  = [];
                
                $datos = SysRrhhEmpleados::find()->select(['id_sys_rrhh_cedula', 'nombres'])
                ->joinWith(['sysAdmCargo'])
                ->join('join', 'sys_adm_departamentos', 'sys_adm_cargos.id_sys_adm_departamento =  sys_adm_departamentos.id_sys_adm_departamento')
                ->andWhere(["sys_rrhh_empleados.id_sys_empresa"=> "001"])
                ->andWhere(['sys_rrhh_empleados.estado'=> 'A'])
                ->andWhere(["sys_adm_departamentos.id_sys_adm_area"=> $area])
                ->andFilterWhere(["sys_adm_departamentos.id_sys_adm_departamento"=>$departamento])
                ->orderBy(['nombres'=>SORT_ASC])
                ->all();
                
                
                foreach ($datos as $data):
                
                    $emp [] = array('id_sys_rrhh_cedula'=> $data['id_sys_rrhh_cedula'], 'nombres'=> $data['nombres']);
                
                
                endforeach;
                
                return json_encode($emp);
        
    }
    

    /**
     * Deletes an existing SysRrhhEmpleados model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_sys_rrhh_cedula
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id_sys_rrhh_cedula, $id_sys_empresa)
    {
        //$this->findModel($id_sys_rrhh_cedula, $id_sys_empresa)->delete();

        return $this->redirect(['index']);
    }

    
  
    
    private function getCodigoComedor($id_sys_rrhh_cedula, $fechanacimiento){
        
        $codigo = intval($id_sys_rrhh_cedula)/5;
        
        $dia    = date('d', strtotime($fechanacimiento));
        
        
        return  intval($codigo).''.$dia;
        
        
    }
    
    private function validaEmpleado($id_sys_rrhh_cedula){
        
        
        return  SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula' => $id_sys_rrhh_cedula])->one();
        
    }

    private function validarCI($strCedula){
        //aqui explico la logica de la validacion de una cedula de ecuador
        //El decimo Digito es un resultante de un calculo
        //Se trabaja con los 9 digitos de la cedula
        //Cada digito de posicion impar se lo duplica, si este es mayor que 9 se resta 9
        //Se suman todos los resultados de posicion impar
        //Ahora se suman todos los digitos de posicion par
        //se suman los dos resultados
        //se resta de la decena inmediata superior
        //este es el decimo digito
        //si la suma nos resulta 10, el decimo digito es cero
     
        if(is_null($strCedula) || empty($strCedula)){//compruebo si que el numero enviado es vacio o null
          
            return 'Por Favor Ingrese la Cedula.';
        
        }else{//caso contrario sigo el proceso
            
            if(is_numeric($strCedula)){
              
                $total_caracteres=strlen($strCedula);// se suma el total de caracteres
                
                if($total_caracteres==10){//compruebo que tenga 10 digitos la cedula
                    
                    $nro_region=substr($strCedula, 0,2);//extraigo los dos primeros caracteres de izq a der
            
                    if($nro_region>=1 && $nro_region<=24){// compruebo a que region pertenece esta cedula//
                
                        $ult_digito=substr($strCedula, -1,1);//extraigo el ultimo digito de la cedula
                        //extraigo los valores pares//
                        $valor2=substr($strCedula, 1, 1);
                        $valor4=substr($strCedula, 3, 1);
                        $valor6=substr($strCedula, 5, 1);
                        $valor8=substr($strCedula, 7, 1);
                        $suma_pares=($valor2 + $valor4 + $valor6 + $valor8);
                        //extraigo los valores impares//
                        $valor1=substr($strCedula, 0, 1);
                        $valor1=($valor1 * 2);
                        if($valor1>9){ $valor1=($valor1 - 9); }else{ }
                            $valor3=substr($strCedula, 2, 1);
                            $valor3=($valor3 * 2);
                        if($valor3>9){ $valor3=($valor3 - 9); }else{ }
                            $valor5=substr($strCedula, 4, 1);
                            $valor5=($valor5 * 2);
                        if($valor5>9){ $valor5=($valor5 - 9); }else{ }
                            $valor7=substr($strCedula, 6, 1);
                            $valor7=($valor7 * 2);
                        if($valor7>9){ $valor7=($valor7 - 9); }else{ }
                            $valor9=substr($strCedula, 8, 1);
                            $valor9=($valor9 * 2);
                        if($valor9>9){ $valor9=($valor9 - 9); }else{ }
            
                        $suma_impares=($valor1 + $valor3 + $valor5 + $valor7 + $valor9);
                        
                        
                        $suma=($suma_pares + $suma_impares);
                        
                        $dis=substr($suma, 0,1);//extraigo el primer numero de la suma
                        
                        $dis=(($dis + 1)* 10);//luego ese numero lo multiplico x 10, consiguiendo asi la decena inmediata superior
                        
                        $digito=($dis - $suma);

                        if($digito==10){ $digito='0'; }else{ }//si la suma nos resulta 10, el decimo digito es cero
                        
                        if ($digito==$ult_digito){//comparo los digitos final y ultimo
                            
                            return 'Cedula Correcta';

                        }else{
                           
                            return 'Cedula Incorrecta';
                        }

                    }elseif($nro_region == 30){
                        $ult_digito=substr($strCedula, -1,1);//extraigo el ultimo digito de la cedula
                        //extraigo los valores pares//
                        $valor2=substr($strCedula, 1, 1);
                        $valor4=substr($strCedula, 3, 1);
                        $valor6=substr($strCedula, 5, 1);
                        $valor8=substr($strCedula, 7, 1);
                        $suma_pares=($valor2 + $valor4 + $valor6 + $valor8);
                        //extraigo los valores impares//
                        $valor1=substr($strCedula, 0, 1);
                        $valor1=($valor1 * 2);
                        if($valor1>9){ $valor1=($valor1 - 9); }else{ }
                        $valor3=substr($strCedula, 2, 1);
                        $valor3=($valor3 * 2);
                        if($valor3>9){ $valor3=($valor3 - 9); }else{ }
                            $valor5=substr($strCedula, 4, 1);
                            $valor5=($valor5 * 2);
                        if($valor5>9){ $valor5=($valor5 - 9); }else{ }
                            $valor7=substr($strCedula, 6, 1);
                            $valor7=($valor7 * 2);
                        if($valor7>9){ $valor7=($valor7 - 9); }else{ }
                            $valor9=substr($strCedula, 8, 1);
                            $valor9=($valor9 * 2);
                        if($valor9>9){ $valor9=($valor9 - 9); }else{ }
    
                        $suma_impares=($valor1 + $valor3 + $valor5 + $valor7 + $valor9);
                        $suma=($suma_pares + $suma_impares);
                        $dis=substr($suma, 0,1);//extraigo el primer numero de la suma
                        $dis=(($dis + 1)* 10);//luego ese numero lo multiplico x 10, consiguiendo asi la decena inmediata superior
                        $digito=($dis - $suma);
                            if($digito==10){ $digito='0'; }else{ }//si la suma nos resulta 10, el decimo digito es cero
                            if ($digito==$ult_digito){//comparo los digitos final y ultimo
                                return 'Cedula Correcta';
                            }else{
                                return 'Cedula Incorrecta';
                            }
                    }else{

                        return 'Este Nro de Cedula no corresponde a ninguna provincia del ecuador';
            
                    }
         
                }else{
               
                    return "Es un Numero y tiene mas de 10 digitos";
                
                }

            }else{

                return 'Este Nro de Cedula no corresponde a ninguna provincia del ecuador';
            
            }
        }
     }

    
    
    
    /**
     * Finds the SysRrhhEmpleados model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_sys_rrhh_cedula
     * @param string $id_sys_empresa
     * @return SysRrhhEmpleados the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id_sys_rrhh_cedula, $id_sys_empresa)
    {
        if (($model = SysRrhhEmpleados::findOne(['id_sys_rrhh_cedula' => $id_sys_rrhh_cedula, 'id_sys_empresa' => $id_sys_empresa])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
