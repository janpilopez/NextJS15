<?php

namespace app\controllers;
use yii\helpers\FileHelper;
use app\models\Model;
use app\models\Search\SysSsooRegistroEntregaDetalleSearch;
use app\models\Search\SysSsooRegistroEntregaEppSearch;
use app\models\SysAdmDepartamentos;
use app\models\SysRrhhEmpleados;
use app\models\SysSsooEPP;
use app\models\SysSsooInspeccionEpp;
use app\models\SysSsooRegistroEntregaDetalle;
use app\models\SysSsooRegistroEntregaEpp;
use app\models\User;
use app\services\FileService;
use DateTime;
use Exception;
use kartik\mpdf\Pdf;
use Prophecy\Util\StringUtil;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * AreasController implements the CRUD actions for SysAccesoProveedores model.
 */
class RegistroEppController extends Controller
{
    public function behaviors()
    {
        return [
            'ghost-access'=> [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
        ];
    }

    public $actividades = [
        "Saca amarillo" => "Saca amarillo",
        "Corte" => "Corte",
        "Pelado" => "Pelado",
        "Limpieza" => "Limpieza",
        "Pulido" => "Pulido",
        "Emparillado" => "Emparillado",
        "Traslado y paletizado" => "Traslado y paletizado",
        "Glaseo" => "Glaseo",
        "Empaque" => "Empaque",
        "Sellado" => "Sellado",
        "Detector de metales" => "Detector de metales",
        "Saca Coches" => "Saca Coches",
        "Volteo" => "Volteo",
        "Despellejado" => "Despellejado",
        "Retiro de scrap" => "Retiro de scrap",
        "Recepción de subproducto" => "Recepción de subproducto",
        "Saca amarillo" => "Saca amarillo",
        "Collares" => "Collares",
        "Lomos" => "Lomos",
        "Trozos" => "Trozos",
        "Migas" => "Migas",
        "Pesaje" => "Pesaje",
        "Sellado" => "Sellado",
        "Separador de espinas" => "Separador de espinas",
        "Pasa bandejas" => "Pasa bandejas",
        "Limpieza de sala" => "Limpieza de sala",
        "Cocina" => "Cocina",
        "Tarjetero" => "Tarjetero",
        "Traslado" => "Traslado",
        "Plaqueros y banda" => "Plaqueros y banda",
        "Registro" => "Registro",
        "Traslado de coches" => "Traslado de coches",
        "Paletizado" => "Paletizado",
        "Operador de montacargas" => "Operador de montacargas"
    ];
    
    public function actionIndex()
    {
        $searchModel = new SysSsooRegistroEntregaEppSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'modelDetalle' => SysSsooRegistroEntregaDetalle::find()->where(['id_ssoo_registro_entrega' => $id])->all(),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelDetalle = SysSsooRegistroEntregaDetalle::find()
            ->where(['id_ssoo_registro_entrega' => $id])
            ->andWhere(['estado_asignacion' => 'ASIGNADO'])
            ->all();
        $db = $_SESSION['db'];

        if ($model->load(Yii::$app->request->post())) {
            try {
                $transaction = \Yii::$app->$db->beginTransaction();
                $model->save(true, array('observacion'));
                

                $detalles = Model::createMultipleLoads(SysSsooRegistroEntregaDetalle::classname());
                Model::loadMultiple($modelDetalle, Yii::$app->request->post());
                foreach ($modelDetalle as $detalle) {
                    if ($detalle->getOldAttribute('estado_asignacion') == "ASIGNADO"
                    && $detalle->estado_asignacion == "RETIRADO") {
                        $detalle->fecha_fin = date("Ymd H:i:s");
                        // $detalle->fecha_fin = date("Ymd H:i:s", strtotime("+ 1 day"));
                        $detalle->save(true, array('estado_asignacion', 'fecha_fin'));
                    }
                }
         
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success',
                    'duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot',
                    'message' => 'Los datos han sido actualizados con  éxito!',
                    'positonY' => 'top',
                    'positonX' => 'right'
                ]);
                $transaction->commit();
    
                return $this->redirect('index');
            } catch (\Throwable $th) {
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger',
                    'duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot',
                    'message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                    'positonY' => 'top',
                    'positonX' => 'right'
                ]);    
                $transaction->rollback();
                return $th;
            }
        }
        return $this->render('update', [
            'model' => $model,
            'inputDisable' => 1,
            'modelDetalle' => $modelDetalle,
            'listaActividades' => $this->actividades
        ]);
    }

    function createModelDetalle($detalles, $model, $firma_base64 = null, $evidencia_base64 = null){
        $message = '';
        $evidenciaUrl = '';
        $firmaUrl = '';
        $servicio = new FileService();
        //FIRMA PRINCIPAL GUARDADA
        if ($firma_base64 != null) {
            $firmaUrl = $servicio->convertBase64ToUploadedFiletoPng($firma_base64, '/ssoo/epp',"epp_firma_".strval($model->id_sys_rrhh_cedula)."_");
        }
        if ($evidencia_base64 != null) {
            $evidenciaUrl = $servicio->convertBase64ToUploadedFiletoPng($evidencia_base64, '/ssoo/epp',"epp_".strval($model->id_sys_rrhh_cedula)."_");
        }
        // var_dump($firma_base64);
        // var_dump($evidencia_base64);exit();
        foreach ($detalles as $index => $detalle) {
            if ($detalle->id_sys_ssoo_epp) {

                $verificacionDobleProducto = SysSsooRegistroEntregaDetalle::find()
                ->leftJoin("sys_ssoo_registro_entrega_epp", "sys_ssoo_registro_entrega_epp.id_sys_ssoo_registro_entrega_epp = sys_ssoo_registro_entrega_detalle.id_ssoo_registro_entrega")
                ->where([
                    "id_sys_ssoo_epp" => $detalle->id_sys_ssoo_epp, "estado_asignacion" => 'ASIGNADO',
                    "sys_ssoo_registro_entrega_epp.id_sys_rrhh_cedula" => $model->id_sys_rrhh_cedula
                    ])
                ->one();
                if ($verificacionDobleProducto) {
                    $message = $message . "Atención: El producto ".$verificacionDobleProducto->epp->nombre." ya tiene/dispone una asignacion activa. \n";
                    continue;
                } 
                // $fileInstance = UploadedFile::getInstance($detalle, "[{$index}]file");                
                // $ruta = $servicio->saveFile('/ssoo/epp', $fileInstance, "epp_".strval($model->id_sys_rrhh_cedula));
    
                $newDetalle = new SysSsooRegistroEntregaDetalle();
                $identity = Yii::$app->user->identity;
                $newDetalle->id_ssoo_registro_entrega = $model->id_sys_ssoo_registro_entrega_epp;
                $newDetalle->id_empleado_registro = $identity->cedula;
                $newDetalle->username_empleado_registro = $identity->username;
                $newDetalle->id_sys_ssoo_epp = $detalle->id_sys_ssoo_epp;
                // $newDetalle->image_url = $ruta;
                $newDetalle->image_url = $evidenciaUrl;
                $newDetalle->firma_empleado_url = $firmaUrl;
                $newDetalle->estado_asignacion = 'ASIGNADO';
                $newDetalle->estado  = $detalle->estado;
                $fechaRegistro = new DateTime($detalle->fecha_registro);  // Convierte la fecha de registro a DateTime
                $newDetalle->fecha_registro = $fechaRegistro->format("Ymd H:i:s");
                $newDetalle->vida_util = $detalle->vida_util;
                // $newDetalle->vida_util = $epp->vida_util;
                $fechaVencimiento = $fechaRegistro->modify("+$detalle->vida_util days");
                $newDetalle->fecha_vencimiento = $fechaVencimiento->format("Ymd H:i:s");
                $newDetalle->save(); 
            }
        }
        return $message;
    }

    public function actionCreate()
    {
        $model = new SysSsooRegistroEntregaEpp();
        $modelDetalle = [new SysSsooRegistroEntregaDetalle()];
        $db = $_SESSION['db'];
        $message = null;
        
        if ($model->load(Yii::$app->request->post())) {
            $firma_base64 = Yii::$app->request->post('signature_data');
            $transaction = \Yii::$app->$db->beginTransaction();
            $existe = SysSsooRegistroEntregaEpp::find()->where(['id_sys_rrhh_cedula' => $model->id_sys_rrhh_cedula])->one();
            if ($existe) {
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'info',
                    'duration' => 3000,
                    'icon' => 'glyphicons glyphicons-robot',
                    'message' => 'No procesado. El usuario ya tiene un registro, agregue uno en su lista',
                    'positonY' => 'top',
                    'positonX' => 'right'
                ]);
    
                return $this->redirect(['index']);
            }

            try {
                if (!$model->validate()) {
                    // Si no pasa la validación, muestra los errores
                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'danger',
                        'duration' => 1500,
                        'icon' => 'glyphicons glyphicons-robot',
                        'message' => 'Error en el modelo principal: ' . implode(", ", $model->errors),
                        'positonY' => 'top',
                        'positonX' => 'right'
                    ]);
                    return $this->render('create', ['model' => $model, 'modelDetalle' => $modelDetalle]);
                } else {
                    $model->estado  = strtoupper($model->estado);
                    $model->id_sys_rrhh_cedula  = strtoupper($model->id_sys_rrhh_cedula);
                    $model->observacion  = $model->observacion;
                    $model->fecha_registro = date("Ymd H:i:s"); 
                    $model->save();


                    //evidencias
                    $evidencia_base64 = Yii::$app->request->post('evidencia_data');
                    $detalles = Model::createMultipleLoads(SysSsooRegistroEntregaDetalle::classname());
                    Model::loadMultiple($detalles, Yii::$app->request->post());
                    $message = $this->createModelDetalle($detalles, $model, $firma_base64, $evidencia_base64);
                    // if ($message == null) {
                    //     break;
                    // }
                    $transaction->commit();
                }


                // $transaction->commit();
            } catch (Exception $e) {

                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger',
                    'duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot',
                    'message' => 'Ha ocurrido un error. Comuniquese con su administrador!' . $e->getMessage(),
                    'positonY' => 'top',
                    'positonX' => 'right'
                ]);
                return $e;
            }


            Yii::$app->getSession()->setFlash('info', [
                'type' => 'success',
                'duration' => 3000,
                'icon' => 'glyphicons glyphicons-robot',
                'message' => 'El proceso ha sido registrado con éxito! '.$message,
                'positonY' => 'top',
                'positonX' => 'right'
            ]);

            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'modelDetalle' => $modelDetalle,
            'listaActividades' => $this->actividades
        ]);
    }

    public function actionCreateadd($id)
    {
        $message = '';
        $model = $this->findModel($id);
        $modelDetalle = [new SysSsooRegistroEntregaDetalle()];
        $db = $_SESSION['db'];

        if ($model->load(Yii::$app->request->post())) {
            $firma_base64 = Yii::$app->request->post('signature_data');

            $transaction = \Yii::$app->$db->beginTransaction();
            $detalles = Model::createMultipleLoads(SysSsooRegistroEntregaDetalle::classname());
            Model::loadMultiple($detalles, Yii::$app->request->post());
            try {
                if (!$model->validate()) {
                    // Si no pasa la validación, muestra los errores
                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'danger',
                        'duration' => 1500,
                        'icon' => 'glyphicons glyphicons-robot',
                        'message' => 'Error en el modelo principal: ' . implode(", ", $model->errors),
                        'positonY' => 'top',
                        'positonX' => 'right'
                    ]);
                    return $this->render('create', ['model' => $model, 'modelDetalle' => $modelDetalle]);
                } else {
                    $evidencia_base64 = Yii::$app->request->post('evidencia_data');
                    $model->save(true, array('observacion'));
                    $message = $this->createModelDetalle($detalles, $model, $firma_base64, $evidencia_base64);
                    $transaction->commit();
                }


                // $transaction->commit();
            } catch (Exception $e) {

                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger',
                    'duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot',
                    'message' => 'Ha ocurrido un error. Comuniquese con su administrador!' . $e->getMessage(),
                    'positonY' => 'top',
                    'positonX' => 'right'
                ]);
                return $e;
            }


            Yii::$app->getSession()->setFlash('info', [
                'type' => 'success',
                'duration' => 3000,
                'icon' => 'glyphicons glyphicons-robot',
                'message' => 'El proceso ha sido registrado con éxito!  '. $message,
                'positonY' => 'top',
                'positonX' => 'right'
            ]);

            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'modelDetalle' => $modelDetalle,
            'listaActividades' => $this->actividades
        ]);
    }

    public function actionInspeccion($id)
    {
        $db = $_SESSION['db'];
        $model = SysSsooRegistroEntregaDetalle::findOne(['sys_ssoo_registro_entrega_detalle_id' =>$id]);
   
        $modelDetalle = new SysSsooInspeccionEpp();
        if ($modelDetalle->load(Yii::$app->request->post())) {
            $transaction = \Yii::$app->$db->beginTransaction();
            try {
                if (!$modelDetalle->validate()) {
                    // Si no pasa la validación, muestra los errores
                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'danger',
                        'duration' => 1500,
                        'icon' => 'glyphicons glyphicons-robot',
                        'message' => 'Error en el modelo principal: ' . implode(", ", $model->errors),
                        'positonY' => 'top',
                        'positonX' => 'right'
                    ]);
                    return $this->render('create', ['model' => $model, 'modelDetalle' => $modelDetalle]);
                } else {
                    if (strtolower($modelDetalle->tipo_inspeccion) == 'alargar') {
                        
                        $model->vida_util += $modelDetalle->tiempo_resultado_inspeccion;

                        $fechaVencimiento = new DateTime($model->fecha_vencimiento);
                        $fechaVencimiento->modify("+$modelDetalle->tiempo_resultado_inspeccion days");
                        $model->fecha_vencimiento = $fechaVencimiento->format("Ymd H:i:s");

                        
                    }else if (strtolower($modelDetalle->tipo_inspeccion) == 'acortar' ) {

                        $model->vida_util -= $modelDetalle->tiempo_resultado_inspeccion;
                        
                        $fechaVencimiento = new DateTime($model->fecha_vencimiento);
                        $fechaVencimiento->modify("-$modelDetalle->tiempo_resultado_inspeccion days");
                        $model->fecha_vencimiento = $fechaVencimiento->format("Ymd H:i:s");
                        
                        //CALCULOS DE DIAS
                        $fechaRegistro = new DateTime($model->fecha_registro);  // Convierte el string a DateTime
                        $fechaActual = new DateTime();  // Fecha actual
                        $intervalo = $fechaRegistro->diff($fechaActual);
                        $diasTranscurridos = $intervalo->days;  // Días transcurridos desde la fecha de registro
                        
                        //validacion fecha fuera de plazo
                        if ($diasTranscurridos >= $model->vida_util) {
                            Yii::$app->getSession()->setFlash('info', [
                                'type' => 'success',
                                'duration' => 3000,
                                'icon' => 'glyphicons glyphicons-robot',
                                'message' => 'El registro no cumple con tiempo de vida util minimo de 1 día!, agregue mas tiempo al EPP primero',
                                'positonY' => 'top',
                                'positonX' => 'right'
                            ]);
                            return $this->redirect(['update', 'id' => $model->registroepp->id_sys_ssoo_registro_entrega_epp]);
                        }
                    
                    }else {
                        $modelDetalle->tiempo_resultado_inspeccion = 0;
                    }


                    $fileInstance = UploadedFile::getInstance($modelDetalle, "file");   
                    $servicio = new FileService();
                    $modelDetalle->tipo_inspeccion = strtolower($modelDetalle->tipo_inspeccion);
                    $modelDetalle->fecha_registro = date("Ymd H:i:s");

                    $ruta = $servicio->saveFile('/ssoo/epp/insp', $fileInstance, "epp_insp_".strval($model->registroepp->id_sys_rrhh_cedula));

                    $modelDetalle->image_url = $ruta;


                    $modelDetalle->save(false);
                    $model->save(false,['vida_util', 'fecha_vencimiento']);
                    $transaction->commit();;
                }


                // $transaction->commit();
            } catch (Exception $e) {

                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger',
                    'duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot',
                    'message' => 'Ha ocurrido un error. Comuniquese con su administrador!' . $e->getMessage(),
                    'positonY' => 'top',
                    'positonX' => 'right'
                ]);
                return $e;
            }


            Yii::$app->getSession()->setFlash('info', [
                'type' => 'success',
                'duration' => 3000,
                'icon' => 'glyphicons glyphicons-robot',
                'message' => 'El proceso ha sido registrado con éxito!',
                'positonY' => 'top',
                'positonX' => 'right'
            ]);

            return $this->redirect(['update', 'id' => $model->registroepp->id_sys_ssoo_registro_entrega_epp]);
        } 

        return $this->render('inspeccion', [
            'model' => $model,
            'modelDetalle' => $modelDetalle,
        ]);
    }

    public function actionViewinspeccion($id)
    {
        $model = SysSsooRegistroEntregaDetalle::findOne(['sys_ssoo_registro_entrega_detalle_id' =>$id]);
        $modelDetalle = SysSsooInspeccionEpp::find()->where(['id_sys_ssoo_registro_entrega_detalle' => $id])->all();
        if (count($modelDetalle) <= 0) {
            $modelDetalle = new SysSsooInspeccionEpp();
        }
        return $this->render('viewinspeccion', [
            'model' => $model,
            'modelDetalle' => $modelDetalle,
        ]);
    }

    public function actionListado()
    {
        $searchModel = new SysSsooRegistroEntregaDetalleSearch();
        $params = Yii::$app->request->queryParams;
           $dataProvider = $searchModel->search($params);

        // Si hay filtros personalizados de días restantes, aplicarlos
        if (isset(Yii::$app->request->queryParams['diasRestantes'])) {
            $diasRestantes = Yii::$app->request->queryParams['diasRestantes'];
            
            // Aplica el filtro de días restantes
            $dataProvider->query->andFilterWhere(['>=', 'diasRestantes', $diasRestantes['from']]);
            $dataProvider->query->andFilterWhere(['<=', 'diasRestantes', $diasRestantes['to']]);
        }
      
        return $this->render('listado-epp', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionEppasignadoempleado($id){
        
        $db =  $_SESSION['db'];
        $model = $this->findModel($id);
        $modelDetalle = SysSsooRegistroEntregaDetalle::find()->where(['id_ssoo_registro_entrega' => $id])->all();
        $db = $_SESSION['db'];
  
        $html = $this->renderPartial('update', [
            'model' => $model,
            'inputDisable' => 1,
            'modelDetalle' => $modelDetalle
        ]);
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
            'marginTop' => 19,
            'marginBottom' => 19,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px} .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 { border:0;padding:0;margin-left:-0.00001;} .fuente_table {font-size: 8px;}',
            
            // set mPDF properties on the fly
            //'options' => ['title' => 'Solicitud de Vacaciones'],
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' =>  'Informe de Asistencia',
                'SetHeader'=>['Sistema Gestión de Nómina - Informe Asistencia Laboral||'],
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
            ]
        ]);
        
        // return the pdf output as per the destination setting
        return $pdf->render();
        
    }

    public function actionInformeEppAnio(){
        $anio = "";
        $dataProvider = (Yii::$app->request->queryParams);
        if ($dataProvider) {
            $anio = ($dataProvider['anio'] ?? null);
        }

        $inicioAnio = null;
        $finAnio = null;
        if ($anio == null || $anio == '' || $anio == ' ' ) {
            $anio = date('Y');
            $inicioAnio = ("$anio-01-01T00:00:00.000");
            $finAnio = ("$anio-12-31T23:59:59.000");
        }else{
            $inicioAnio = ("$anio-01-01T00:00:00.000");
            $finAnio = ("$anio-12-31T23:59:59.000");
        }
        $detalles = SysSsooRegistroEntregaDetalle::find()
        ->where(['estado_asignacion' => 'ASIGNADO' ])
        ->andWhere(['between', 'fecha_vencimiento', $inicioAnio, $finAnio])
        ->with(['registroepp', 'epp', 'registroepp.empleado', 'registroepp.empleado.sysAdmCargo',
        'registroepp.empleado.sysAdmCargo.sysAdmDepartamento'])->all();
        //agrupar
        $registros = [];
        $vencimientos = [];
        foreach ($detalles as $key => $registro){
            // Si el registro con el mismo id_sys_ssoo_epp ya existe
            if (array_key_exists($registro->id_sys_ssoo_epp, $registros)) {
                // Ejemplo: actualiza el registro o haz lo que sea necesario
            } else {
                // Si no existe, simplemente agrega el registro al array
                $registros[$registro->id_sys_ssoo_epp] = $registro;
            }
            
            //REGISTRAMOS TODOS LOS EPP POR NUMERO DE MES
            if (!isset($vencimientos[$registro->id_sys_ssoo_epp][$registro->mesVencimiento])) {
                // Si no existe, inicializamos el array
                $vencimientos[$registro->id_sys_ssoo_epp][$registro->mesVencimiento] = [];
            }
            $vencimientos[$registro->id_sys_ssoo_epp][$registro->mesvencimiento][] = $registro->fecha_vencimiento ?? NULL;
            
        };
    
        return $this->render('listado-informe-epp-anio', [
            'registros' => $registros,
            'vencimientos' => $vencimientos,
            'anio' => $anio
        ]);
    }

    public function actionInformeEppSemana()
    {
        $anio = '';
        $db = $_SESSION['db'];
        
        $resultados = Yii::$app->$db->createCommand("SELECT * from View_SSOO_detalle_registros_epp where estado_asignacion = 'ASIGNADO'")->queryAll(); 
        $departamentos = SysAdmDepartamentos::find()->all();

        //AGRUPACION        
        $dataProvider = (Yii::$app->request->queryParams);
        if ($dataProvider) {
            $anio = ($dataProvider['anio'] ?? null);
            $semana = ($dataProvider['semana'] ?? null);
            $nombre_epp = ($dataProvider['nombre_epp'] ?? null);
            $selectedDepartamento = ($dataProvider['selectedDepartamento'] ?? null);
            $empleado = ($dataProvider['empleado'] ?? null);
            if ($anio == null || $anio == '' || $anio == ' ' ) {
                $resultados = array_filter($resultados, function($k) use ($anio) {
                    return $k['numeroAnio'] == $anio;
                } );
            }
            if($semana != null || $semana != '' ){
                $resultados = array_filter($resultados, function($k) use ($semana) {
                    return $k['numeroSemana'] == $semana;
                } );
            }
            if($nombre_epp != null || $nombre_epp != '' ){
                $resultados = array_filter($resultados, function($k) use ($nombre_epp) {
                    return str_contains(strtoupper($k['nombreEpp']), strtoupper($nombre_epp));
                } );
            }
            if($empleado != null || $empleado != '' ){
                $resultados = array_filter($resultados, function($k) use ($empleado) {
                    return str_contains(strtoupper($k['nombreEmpleado']), strtoupper($empleado)) ||
                    str_contains(strtoupper($k['id_sys_rrhh_cedula']), strtoupper($empleado));
                } );
            }
            if($selectedDepartamento != null || $selectedDepartamento != '' ){
                $resultados = array_filter($resultados, function($k) use ($selectedDepartamento) {
                    return str_contains(strtoupper($k['departamento']), strtoupper($selectedDepartamento));
                } );
            }
        }
        $totales = [];
        //SUMATORIA POR MESES
        for ($i=1; $i <= 12; $i++) { 
            $totales[$i] = count(
                array_filter($resultados, function($k) use ($i) {
                    return $k['numeroMes'] == $i;
                }));
        };


        //precarga de anio
        if($anio == null || $anio == ''){
            $anio = date('Y');
            $resultados = array_filter($resultados, function($k) use ($anio) {
                return $k['numeroAnio'] == $anio;
            } );
        }

        return $this->render('listado-informe-epp-semana', [
            'registros' => $resultados,
            'anio' => $anio,
            "semana" => $semana ?? '',
            "nombre_epp" => $nombre_epp ?? '',
            "totales" => $totales ?? [],
            "departamentos" => $departamentos,
            "selectedDepartamento" => $selectedDepartamento ?? '',
            "empleado" => $empleado ?? ''
        ]);    
    }

    public function actionInformeEppCategoriaAnio()
    {
        $anio = "";
        $dataProvider = (Yii::$app->request->queryParams);
        if ($dataProvider) {
            $anio = ($dataProvider['anio'] ?? null);
        }

        $inicioAnio = null;
        $finAnio = null;
        if ($anio == null || $anio == '' || $anio == ' ' ) {
            $anio = date('Y');
            $inicioAnio = ("$anio-01-01T00:00:00.000");
            $finAnio = ("$anio-12-31T23:59:59.000");
        }else{
            $inicioAnio = ("$anio-01-01T00:00:00.000");
            $finAnio = ("$anio-12-31T23:59:59.000");
        }
        $db = $_SESSION['db'];
        $registros = Yii::$app->$db
            ->createCommand("SELECT * from View_SSOO_detalle_registros_epp where estado_asignacion = 'ASIGNADO'
            AND fecha_vencimiento BETWEEN :inicioAnio AND :finAnio")
            ->bindParam(':inicioAnio', $inicioAnio)
            ->bindParam(':finAnio', $finAnio)              
            ->queryAll(); 

        $agrupados = [];
        foreach ($registros as $key => $registro) {
            $clave = $registro['nombreEpp'];
            $idEpp = $registro['id_sys_ssoo_epp'];
            $mes = $registro['numeroMes'];
            
            // Si ya existe el registro, sumar el mes correspondiente
            if (isset($agrupados[$clave])) {
                $agrupados[$clave]['meses'][$registro['numeroMes']]++;
            } else {
                // Si no existe, crear una nueva entrada con el valor del mes
                $agrupados[$clave] = [
                    'nombreEpp' => $registro['nombreEpp'],
                    'id_sys_ssoo_epp' => $registro['id_sys_ssoo_epp'],
                    // 'nombreEmpleado' => $registro['nombreEmpleado'],
                    // 'id_sys_rrhh_cedula' => $registro['id_sys_rrhh_cedula'],
                    // 'departamento' => $registro['departamento'],
                    'meses' => array_fill(1, 12, 0) // Inicializamos los 12 meses con 0
                ];
                $agrupados[$clave]['meses'][$registro['numeroMes']] = 1; // Sumamos 1 al mes correspondiente
            }
        }
        return $this->render('listado-informe-epp-categoria-anio', [
            'registros' => $agrupados,
            'anio' =>" $anio"
        ]);
    }

    public function actionGetCargo($cedula)
    {
        $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula' => $cedula])->one();
        
        if ($empleado) {
            return \yii\helpers\Json::encode([
                'cargo' => $empleado->sysAdmCargo, 
                'area' => $empleado->sysAdmCargo->sysAdmDepartamento
            ]); // Ajusta según el atributo correcto
        } else {
            return \yii\helpers\Json::encode(['cargo' => 'Empleado no encontrado']);
        }
    }

    protected function findModel($id)
    {
        if (($model = SysSsooRegistroEntregaEpp::findOne(['id_sys_ssoo_registro_entrega_epp' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
