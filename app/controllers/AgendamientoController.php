<?php

namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\SysRrhhCuadrillasJornadasCab;
use app\models\SysRrhhCuadrillasJornadasMov;
use app\models\SysRrhhCuadrillas;
use kartik\mpdf\Pdf;
use app\models\search\SysRrhhCuadrillasJornadasMovSearch;
use app\models\SysRrhhEmpleadosRolCab;

/**
 * AgendamientoController implements the CRUD actions for SysRrhhCuadrillasJornadasCab model.
 */
class AgendamientoController extends Controller
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
     * Lists all SysRrhhCuadrillasJornadasCab models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhCuadrillasJornadasMovSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCrearjornada(){
        
   
            $db          =  $_SESSION['db'];
            $datos       =  Yii::$app->request->post('datos');
            $obj         =  json_decode($datos);
          
            $horariocab  =  $obj->horariocab;
            $horariodet  =  $obj->horariodet;
  
            $codcuadrilla = SysRrhhCuadrillasJornadasCab::find()->select(['max(CAST(id_sys_rrhh_cuadrillas_jornadas_cab AS INT))'])->scalar() + 1;
           
            
            if (count($horariocab) > 0 && count($horariodet) > 0){
                
                
                $transaction = \Yii::$app->$db->beginTransaction();
                
                $flag = true;
                
                $fechainicio =  $horariocab[0]->fechainicio;
                $fechafin    =  $horariocab[0]->fechafin;
                $ban         =  0;
                $datos       = [];
                
                for($i=$fechainicio;$i<=$fechafin;$i = date("Y-m-d", strtotime($i ."+ 1 days"))){
                    
                    
                    $datos = (new \yii\db\Query())
                    ->select('*')
                    ->from("sys_rrhh_cuadrillas_jornadas_cab")
                    ->where("id_sys_rrhh_cuadrillas = {$horariocab[0]->id_sys_cuadrilla}")
                    ->andwhere("'{$i}'>= fecha_inicio")
                    ->andwhere("'{$i}' <= fecha_fin")
                    ->andwhere("estado = 'A'")
                    ->all(SysRrhhCuadrillasJornadasCab::getDb());
                    
                    if (count($datos) > 0 ){
                        $fecha= $i;
                        $ban = 1 ;
                        break ;
                    }
                }
                
                if ($ban != 1) {
                    
                    $newhorariocab = new SysRrhhCuadrillasJornadasCab();
                    $newhorariocab->id_sys_rrhh_cuadrillas_jornadas_cab   = $codcuadrilla;
                    $newhorariocab->id_sys_rrhh_cuadrillas                = $horariocab[0]->id_sys_cuadrilla;
                    $newhorariocab->fecha_inicio                          = $horariocab[0]->fechainicio;
                    $newhorariocab->fecha_fin                             = $horariocab[0]->fechafin;
                    $newhorariocab->semana                                = $this->getSemana($horariocab[0]->fechainicio);
                    $newhorariocab->transaccion_usuario                   = Yii::$app->user->username;
                    $newhorariocab->estado                                = 'A';
                    
                    if ($newhorariocab->save(false))
                    {
                        
                        foreach ($horariodet as $data){

                            $codcuadrilladet =  SysRrhhCuadrillasJornadasMov::find()->select(['max(CAST(id_sys_rrhh_cuadrillas_jornadas_mov AS INT))'])->scalar();
                           
                            $newhorariodet                                      = new SysRrhhCuadrillasJornadasMov();
                            $newhorariodet->id_sys_rrhh_cuadrillas_jornadas_mov = $codcuadrilladet + 1;
                            $newhorariodet->id_sys_rrhh_cuadrillas_jornadas_cab = $codcuadrilla;
                            $newhorariodet->id_sys_rrhh_cedula                  = $data->cedula;
                            $newhorariodet->fecha_laboral                       = $data->fecha;
                            $newhorariodet->id_sys_rrhh_jornada                 = $data->jornada == '' ? null: $data->jornada;
                            $newhorariodet->id_sys_empresa                      = '001';
                            $newhorariodet->save(false);
                            
                            
                            if(!$flag = $newhorariodet->save(false)){
                                
                                break;
                                
                            }
                            
                        }
                        
                        if($flag){
                            $transaction->commit();
                            echo  json_encode(['data' => [ 'estado' => false,'mensaje' => 'Los datos se ha registrado con exito!']]);
                            
                        }else{
                            $transaction->rollBack();
                            echo  json_encode(['data' => ['estado' => false, 'mensaje' => 'Ha ocurrido un error al guardar el permiso!']]);
                        }
                        
                        
                    }
                   
                    
                }else{
                    
                    echo json_encode(['data' => ['estado' => false, 'mensaje' => 'Ya existe un agendamiento para la fecha. '.$fecha. '. Por favor revise su agendamiento.']]);
                }
                
            }else{
                
                echo json_encode(['data' => ['estado' => false, 'mensaje' => 'No se recibieron los parametros!']]);
                
            }
          
      
        
    }
    
    public function actionAjustaragenda($id){
        
       
      
        if (Yii::$app->request->post()){
            
             $cuadrilla          =  $_POST['cuadrillas']; 
             $fecha_laboral      =  $_POST['fechalaboral'];
             $cedula             =  isset($_POST['cedulaemp']) ? $_POST['cedulaemp']: null ;
             $jornada            =  $_POST['sysjornadas'];
             $codagenda          =  $_POST['codagenda'];
             $db                 = $_SESSION['db'];
             
             if ($cuadrilla != null && $fecha_laboral != null && $cedula != null){
                 
                    $count =  SysRrhhCuadrillasJornadasMov::find()
                   ->where(['id_sys_rrhh_cuadrillas_jornadas_cab'=> $id])
                   ->andWhere(['id_sys_rrhh_cedula'=> $cedula])
                   ->andWhere(['fecha_laboral'=> $fecha_laboral])
                   ->count();
                   
                   if ($count > 0 ){
                       
                       if ($fecha_laboral  > $this->getFechaUltimoCorte()){

                                $existe = SysRrhhCuadrillasJornadasMov::find()->where(['fecha_laboral'=>$fecha_laboral])->andWhere(['id_sys_rrhh_cedula'=>$cedula])->one();

                                if($existe){

                                    Yii::$app->$db->createCommand("delete sys_rrhh_cuadrillas_jornadas_mov where id_sys_rrhh_cuadrillas_jornadas_mov = '{$existe['id_sys_rrhh_cuadrillas_jornadas_mov']}'")->execute();
                           
                                    $codcuadrilladet =  SysRrhhCuadrillasJornadasMov::find()->select(['max(CAST(id_sys_rrhh_cuadrillas_jornadas_mov AS INT))'])->scalar() + 1;
                            
                                    $newmodel = new SysRrhhCuadrillasJornadasMov();
                                                
                                    $newmodel->id_sys_rrhh_cuadrillas_jornadas_cab = $codagenda;
                                    $newmodel->id_sys_rrhh_cedula                  = $cedula;
                                    $newmodel->fecha_laboral                       = $fecha_laboral;
                                    $newmodel->id_sys_rrhh_jornada                 = $jornada == ''? null: $jornada;
                                    $newmodel->id_sys_rrhh_cuadrillas_jornadas_mov = $codcuadrilladet;
                                    $newmodel->id_sys_empresa                      = '001';     
                                    
                                }else{

                                    $codcuadrilladet =  SysRrhhCuadrillasJornadasMov::find()->select(['max(CAST(id_sys_rrhh_cuadrillas_jornadas_mov AS INT))'])->scalar() + 1;
                            
                                    $newmodel = new SysRrhhCuadrillasJornadasMov();
                                                
                                    $newmodel->id_sys_rrhh_cuadrillas_jornadas_cab = $codagenda;
                                    $newmodel->id_sys_rrhh_cedula                  = $cedula;
                                    $newmodel->fecha_laboral                       = $fecha_laboral;
                                    $newmodel->id_sys_rrhh_jornada                 = $jornada == ''? null: $jornada;
                                    $newmodel->id_sys_rrhh_cuadrillas_jornadas_mov = $codcuadrilladet;
                                    $newmodel->id_sys_empresa                      = '001';

                                }
                                
                                if ($newmodel->save(false)){
                                                   
                                     Yii::$app->getSession()->setFlash('info', [
                                     'type' => 'success','duration' => 1500,
                                     'icon' => 'glyphicons glyphicons-robot','message' => 'La agenda ha sido actualizada con exito!',
                                     'positonY' => 'top','positonX' => 'right']);
                                                   
                                 }else{
                                                   
                                      Yii::$app->getSession()->setFlash('info', [
                                      'type' => 'danger','duration' => 1500,
                                      'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador!',
                                      'positonY' => 'top','positonX' => 'right']);
                                                   
                                  }
                       
                       }else{
                           
                           
                           Yii::$app->getSession()->setFlash('info', [
                               'type' => 'warning','duration' => 1500,
                               'icon' => 'glyphicons glyphicons-robot','message' => 'La fecha ingresada debe ser mayor a la fecha actual de hoy!',
                               'positonY' => 'top','positonX' => 'right']);
                           
                       }
                        
                   }else{
                       
                       Yii::$app->getSession()->setFlash('info', [
                       'type' => 'warning','duration' => 1500,
                       'icon' => 'glyphicons glyphicons-robot','message' => 'La fecha seleccionada no se encuentra dentro de la agenda.',
                       'positonY' => 'top','positonX' => 'right']);
                      
                   }
                           
             }else{
                 
                     Yii::$app->getSession()->setFlash('info', [
                     'type' => 'warning','duration' => 1500,
                     'icon' => 'glyphicons glyphicons-robot','message' => 'Existen campos vacios. Por favor revisar',
                     'positonY' => 'top','positonX' => 'right']);
                 
             }
            
        }
       
        return $this->render('_ajustaragenda', ['codagenda'=> $id]);
        
    }
    public function actionEmpleadoscuadrillas(){
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $idcuadrilla =  $parents[0];
                     
                 $datos =  (new \yii\db\Query())
                ->select(["sys_rrhh_cuadrillas_empleados.id_sys_rrhh_cedula as id","sys_rrhh_empleados.nombres  as name"])
                ->from("sys_rrhh_cuadrillas_empleados")
                ->join('INNER JOIN', 'sys_rrhh_empleados','sys_rrhh_cuadrillas_empleados.id_sys_rrhh_cedula = sys_rrhh_empleados.id_sys_rrhh_cedula')
                ->andwhere("sys_rrhh_cuadrillas_empleados.id_sys_rrhh_cuadrilla = '{$idcuadrilla}'")
                ->orderby("sys_rrhh_empleados.nombres")
                ->all(SysRrhhCuadrillas::getDb());
              
                             
                return ['output'=>$datos, 'selected'=>''];
            }
        }
        return ['output'=>'', 'selected'=>''];
        
        
        
        
        
        
        
        
        
    }
    
    
    private function getFechaUltimoCorte(){
        
        
      $fecha =  SysRrhhEmpleadosRolCab::find()->select('MAX(fecha_fin_liq)')->where(['periodo'=> '2'])->andWhere(['estado'=> 'P'])->scalar();
        
      if($fecha == 0):
      
        $fecha = date('Y').'-01-01';
      
      endif;
      
      return $fecha;
        
    }
    
    
    
    /**
     * Displays a single SysRrhhCuadrillasJornadasCab model.
     * @param string $id_sys_rrhh_cuadrillas_jornadas_cab
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    
    public function actionVerpdf($id)
    {
      
        
       /*  $html =  $this->renderAjax('_agendapdf', [
            'model' => $this->findModel($id),
        ]);
        
        $mpdf = new Mpdf([
            'format' => 'A4',
            'orientation' => 'L'
        ]);
        //$mpdf->packTableData = true;
        //$mpdf->SetCompression(true);
        $mpdf->WriteHTML($html);
        $mpdf->Output('pdf/horariolaboral.pdf', 'D');
        exit();
        */
        $html =  $this->renderPartial('_agendapdf',['model'=> $this->findModel($id)]);
        
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            
            // your html content input
            'content' => $html,
            'marginTop' => 20,
            'marginBottom' => 19,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px} .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 { border:0; padding:0;margin-left:-0.00001; }  th, td {padding:2px;} h3 {margin:0px;}',
            
            // set mPDF properties on the fly
            //'options' => ['title' => 'Solicitud de Vacaciones'],
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' =>  'Agendamiento Laboral',
                'SetHeader'=>['Sistema Gestión de Nómina - Agendamiento Laboral||'],
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
            ]
        ]);
        
        // return the pdf output as per the destination setting
        return $pdf->render(); 
  
      
    }
    
    /**
     * Creates a new SysRrhhCuadrillasJornadasCab model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysRrhhCuadrillasJornadasCab();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id_sys_rrhh_cuadrillas_jornadas_cab' => $model->id_sys_rrhh_cuadrillas_jornadas_cab]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SysRrhhCuadrillasJornadasCab model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_rrhh_cuadrillas_jornadas_cab
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id_sys_rrhh_cuadrillas_jornadas_cab' => $model->id_sys_rrhh_cuadrillas_jornadas_cab]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SysRrhhCuadrillasJornadasCab model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_sys_rrhh_cuadrillas_jornadas_cab
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
       $model = $this->findModel($id);
       $model->estado = 'I';
       $model->save(false);
       
       Yii::$app->getSession()->setFlash('info', [
           'type' => 'success','duration' => 1500,
           'icon' => 'glyphicons glyphicons-robot','message' => 'El registro fue eliminado con éxito!',
           'positonY' => 'top','positonX' => 'right']);
       
       return $this->redirect(['index']);
    }
    
    
    private function getSemana ($fecha){
        
        
        $fecha = date("Y-m-d",strtotime($fecha."+ 1 days")); 
              
        $dia   = substr($fecha,8,2);
        $mes   = substr($fecha,5,2);
        $anio  = substr($fecha,0,4); 
        
        $semana = date('W',  mktime(0,0,0,$mes,$dia,$anio));  
        
     
        return $semana;
        
    }
    
    
    public function actionConsultavacaciones($identificacion, $fecha){
        
        //revisar si esta de vacaciones
        
        $vacaciones =  [];
        $vacaciones = (new \yii\db\Query())
        ->select('*')
        ->from("sys_rrhh_vacaciones_solicitud")
        ->where("'{$fecha}' >= fecha_inicio and   '{$fecha}'<= fecha_fin")
        ->andwhere("id_sys_rrhh_cedula = '{$identificacion}'")
        ->all(SysRrhhCuadrillasJornadasMov::getDb());
        
        if(count($vacaciones) > 0):
        
             echo  json_encode(['data' => [ 'estado' => true]]);
        
        else:
        
             echo  json_encode(['data'=>['estado'=> false]]);
        
        endif;
        
    }
    public function actionTienevacaciones($identificacion, $fechainicio, $fechafin){
        
        
         $vacaciones =  [];
         $vacaciones = (new \yii\db\Query())
        ->select('*')
        ->from("sys_rrhh_vacaciones_solicitud")
        ->where("'{$fechainicio}' >= fecha_inicio and '{$fechainicio}'<= fecha_fin")
        ->andwhere("id_sys_rrhh_cedula = '{$identificacion}'")
        ->all(SysRrhhCuadrillasJornadasMov::getDb());
        
        
        if(count($vacaciones) > 0 ):
        
            echo json_encode(['data'=> ['estado'=> true]]);     
        
        else:
        
                $vacaciones =  [];
                $vacaciones = (new \yii\db\Query())
                ->select('*')
                ->from("sys_rrhh_vacaciones_solicitud")
                ->where("'{$fechainicio}' >= fecha_inicio and '{$fechainicio}'<= fecha_fin")
                ->andwhere("id_sys_rrhh_cedula = '{$identificacion}'")
                ->all(SysRrhhCuadrillasJornadasMov::getDb());
        
                if(count($vacaciones)):
                
                   echo json_encode(['data'=> ['estado'=> true]]);
                
                else:       
                
                   echo json_encode(['data'=> ['estado'=> false]]);

                endif;
      
        endif;
           
    }
    
    
    
    

    /**
     * Finds the SysRrhhCuadrillasJornadasCab model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_sys_rrhh_cuadrillas_jornadas_cab
     * @param string $id_sys_empresa
     * @return SysRrhhCuadrillasJornadasCab the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhCuadrillasJornadasCab::findOne(['id_sys_rrhh_cuadrillas_jornadas_cab' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
