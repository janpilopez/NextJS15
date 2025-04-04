<?php

namespace app\controllers;

use Exception;
use Yii;
use app\models\SysRrhhFiniquitoCab;
use app\models\Search\SysRrhhFiniquitoCabSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\models\SysRrhhConceptos;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosPeriodoVacaciones;
use app\models\SysRrhhEmpleadosSueldos;
use app\models\SysRrhhEmpleadosRolMov;
use app\models\SysRrhhFiniquitoDet;
use app\models\SysRrhhEmpleadosRolCab;
use Mpdf\Mpdf;
use app\models\SysRrhhContratos;

/**
 * FiniquitoController implements the CRUD actions for SysRrhhFiniquitoCab model.
 */
class FiniquitoController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all SysRrhhFiniquitoCab models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhFiniquitoCabSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhFiniquitoCab model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        
        
        $html =    $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
        
        $mpdf = new Mpdf([
            'format' => 'A4',
            // 'orientation' => 'L'
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('Finiquito.pdf', 'I');
        exit();
    
    }

    /**
     * Creates a new SysRrhhFiniquitoCab model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model     = new SysRrhhFiniquitoCab();
        $modeldet  = [new SysRrhhFiniquitoDet()];
        
        if ($model->load(Yii::$app->request->post())) {
            
              
            $detallefiniquito = Yii::$app->request->post('SysRrhhFiniquitoDet');
            
            $db = $_SESSION['db'];
                       
            $transaction = \Yii::$app->$db->beginTransaction();
            
            try {
                
                $codigo                           =  SysRrhhFiniquitoCab::find()->select(['max(CAST(id_sys_rrhh_finiquito_cab AS INT))'])->Where(['id_sys_empresa'=> '001'])->scalar()  + 1 ;
                $model->id_sys_rrhh_finiquito_cab =  $codigo;
                $model->id_sys_empresa            =  '001';
                $model->fecha_registro            =   date('Y-m-d');
                $model->estado                    =  'G';
                $model->usuario_creacion          =   Yii::$app->user->username;
                $model->fecha_creacion            =   date('Ymd H:i:s');
                $model->save(false);
                
                if ($flag = $model->save(false)) {
                    
                    foreach ($detallefiniquito  as $detalle) {
                        
                        
                                $id    =  SysRrhhFiniquitoDet::find()->select(['max(id_sys_rrhh_finiquito_det)'])->Where(['id_sys_empresa'=> '001'])->scalar()  + 1 ;
                        
                                $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_finiquito_det(id_sys_rrhh_finiquito_det, id_sys_rrhh_finiquito_cab, tipo, valor, descripcion, id_sys_empresa, usuario_creacion) values ({$id},'{$codigo}','{$detalle['tipo']}', {$detalle['valor']}, '{$detalle['descripcion']}', '001', '".Yii::$app->user->username."')");
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
                        'icon' => 'glyphicons glyphicons-robot','message' => 'El acta de finiquito fue registrado con éxito!',
                        'positonY' => 'top','positonX' => 'right']);
                }
                
                
            }catch (Exception $e) {
             
                $transaction->rollBack(); 
                throw new Exception($e);
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error('.$e->getMessage().'). Comuniquese con su administrador! ',
                    'positonY' => 'top','positonX' => 'right']);
                
            }
            
  
            return $this->redirect('index');
           
        }

        return $this->render('create', [
            'model' => $model,
            'modeldet'=> $modeldet
        ]);
    }

    /**
     * Updates an existing SysRrhhFiniquitoCab model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $modeldet = [];

        $db = $_SESSION['db'];
        
        $datosdetalle = SysRrhhFiniquitoDet::find()->where(['id_sys_rrhh_finiquito_cab'=> $model->id_sys_rrhh_finiquito_cab])->all();
        
        if($datosdetalle):
        
                foreach ($datosdetalle as $detalle):
                
                      $obj                            = new SysRrhhFiniquitoDet();
                      $obj->id_sys_rrhh_finiquito_det = $detalle['id_sys_rrhh_finiquito_det'];
                      $obj->descripcion               = $detalle['descripcion'];
                      $obj->valor                     = $detalle['valor'];
                      $obj->tipo                      = $detalle['tipo'];
                      array_push($modeldet, $obj);
                      
                endforeach;
        else:

           array_push($modeldet, new SysRrhhFiniquitoDet());
     
        endif;
        
        if ($model->load(Yii::$app->request->post())) {
            
            $oldIDs          = ArrayHelper::map($datosdetalle, 'id_sys_rrhh_finiquito_det', 'id_sys_rrhh_finiquito_det');
            $arraydet        = Yii::$app->request->post('SysRrhhFiniquitoDet');
       
            
            if ($arraydet){
                
                $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($arraydet, 'id_sys_rrhh_finiquito_det', 'id_sys_rrhh_finiquito_det')));
                
            }else{
                
                  if($model->estado != 'L'):
                        SysRrhhFiniquitoDet::deleteAll(['id_sys_rrhh_finiquito_det' => $oldIDs]);
                  endif;
            }
            
            
            if(!empty($deletedIDs)){
                
                if($model->estado != 'L'):
                
                       SysRrhhFiniquitoDet::deleteAll(['id_sys_rrhh_finiquito_det' => $deletedIDs]);
                
                endif;
            }
            
            $transaction = \Yii::$app->$db->beginTransaction();
            
            try {
             
                
                $model->fecha_actualizacion   = date('Ymd H:i:s');
                $model->usuario_actualizacion = Yii::$app->user->username;
                $model->save(false);
                
                if ($flag = $model->save(false)) {
                                      
                    if ($arraydet){
                        
                        foreach ($arraydet as $index => $detalle) {
                            
                            
                            if($detalle['id_sys_rrhh_finiquito_det'] != ''){
                                
                                
                                        $md = SysRrhhFiniquitoDet::find()
                                        ->where(['id_sys_rrhh_finiquito_det'=> $detalle['id_sys_rrhh_finiquito_det']])
                                        ->one();
                                     
                                        $md->tipo                           = $detalle['tipo'];
                                        $md->valor                          = $detalle['valor'];
                                        $md->descripcion                    = $detalle['descripcion'];
                                    
                                        if (! ($flag = $md->save(false))) {
                                            $transaction->rollBack();
                                            break;
                                        }
                            }
                            else{
                          
                                $id    =  SysRrhhFiniquitoDet::find()->select(['max(id_sys_rrhh_finiquito_det)'])->Where(['id_sys_empresa'=> '001'])->scalar()  + 1 ;
                                
                                
                                $nflag =  Yii::$app->$db->createCommand("insert into sys_rrhh_finiquito_det(id_sys_rrhh_finiquito_det, id_sys_rrhh_finiquito_cab, id_sys_empresa, tipo, valor, descripcion) values ({$id},'{$model->id_sys_rrhh_finiquito_cab}', '001', '{$detalle['tipo']}', {$detalle['valor']}, '{$detalle['descripcion']}')");
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
        
                }
                
                if ($flag) {
                    $transaction->commit();
                     Yii::$app->getSession()->setFlash('info', [
                        'type' => 'success','duration' => 1500,
                        'icon' => 'glyphicons glyphicons-robot','message' => 'El acta de finiquito fue registrado con éxito!',
                        'positonY' => 'top','positonX' => 'right']);
                     
                      return $this->redirect(['index']);
                }
                
                
            }catch (Exception $e) {
                return  $e->getMessage();
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error('.$e->getMessage().'). Comuniquese con su administrador! ',
                    'positonY' => 'top','positonX' => 'right']);
                
            }
            
            
        }

        return $this->render('update', [
            'model' => $model,
            'modeldet'=> $modeldet
        ]);
    }

    /**
     * Deletes an existing SysRrhhFiniquitoCab model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model =  $this->findModel($id);
      
        $model->estado = 'A';
        $model->comentario = 'Documento Anulado'.date('Y-m-d H:i:s');
        return $this->redirect(['index']);
    } 
   
    public function actionObtenercontrato(){
        
        $id_sys_rrhh_cedula  =  trim(Yii::$app->request->post('id_sys_rrhh_cedula'));
        
        return json_encode($this->getContrato($id_sys_rrhh_cedula));
   
    }
   
    public function actionObtenerdetallefiniquito($id_sys_rrhh_cedula, $fecha_salida, $fecha_ingreso, $id_causa_salida, $sueldo ){
        
       
        
        $detalle            =  [];
        
       /* $id_sys_rrhh_cedula =  trim(Yii::$app->request->post('id_sys_rrhh_cedula'));
          $fecha_salida       =  trim(Yii::$app->request->post('fecha_salida'));
          $fecha_ingreso      =  trim(Yii::$app->request->post('fecha_ingreso'));
          $id_causa_salida    =  trim(Yii::$app->request->post('id_causa_salida'));
          $sueldo             =  trim(Yii::$app->request->post('sueldo'));
       */
        
       //Decimos  
        $decimos =  $this->getDecimos($id_sys_rrhh_cedula, $fecha_ingreso, $fecha_salida);
        
        if($decimos):
            
             foreach ($decimos as $decimo):
             
                  $detalle [] = $decimo;
        
             endforeach;
        endif;
                
        //Vacaciones 
        $vacaciones = $this->getVacaciones($id_sys_rrhh_cedula, $fecha_ingreso, $fecha_salida);
        
        if($vacaciones):
           
             foreach ($vacaciones as $vacacion):
               
                 $detalle[] = $vacacion;
             
             endforeach;
        
        endif;
        
        
        //Bofinificacion por desahucio
        $desahucio = $this->getDesahucio($id_sys_rrhh_cedula, $fecha_ingreso, $fecha_salida, $id_causa_salida, $sueldo);
        
        if($desahucio):
        
            foreach ($desahucio as $des):
            
             $detalle[] =  $des;   
        
            endforeach;
 
        endif;
        
        // Solo aplica para despido unilateral
        
        $despido = $this->getDespido($id_sys_rrhh_cedula, $fecha_ingreso, $fecha_salida, $id_causa_salida, $sueldo);
        
        
        if($despido):
        
            foreach ($despido as $des):
            
              $detalle[] =  $des;  
        
            
            endforeach;
        
        endif;
        
        return json_encode($detalle);
        
    }
    
    private function getDetalleFiniquito($id_sys_rrhh_cedula, $fecha_salida){
        
    }
    private function getDecimos($id_sys_rrhh_cedula, $fecha_ingreso, $fecha_salida){
        
        
        $detalle        = [];
        
        $db             = $_SESSION['db'];
        
        $anio_salida    = date('Y', strtotime($fecha_salida));
        
        $mes_salida     = date('n', strtotime($fecha_salida));
        
        $mes_ingreso    = date('n', strtotime($fecha_ingreso));
        
        $anio_ingreso  =  date('Y', strtotime($fecha_ingreso));
    
        $anio_fin       = $anio_salida;
        
        $anio_ini       = $anio_fin - 1 ;
         
        $total_dec_ter  = 0;
  
        $total_dec_cua  = 0;
              
        $decimo_tercero = SysRrhhEmpleadosRolCab::find()->where(['anio'=> $anio_salida])->andWhere(['periodo'=> '70'])->one();
        
        if($decimo_tercero):
        
             if($fecha_ingreso >= $decimo_tercero->fecha_ini && $fecha_ingreso <= $decimo_tercero->fecha_fin):
            
                if($mes_ingreso == 12):
                
                    $total_dec_ter  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_TERCERO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes = '12' and anio = '{$anio_ini}'")->queryScalar();
                    $total_dec_ter  = $total_dec_ter + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_TERCERO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes <= '{$mes_salida}' and anio = '{$anio_fin}'")->queryScalar();
                
                else:
                
                    $total_dec_ter  = $total_dec_ter + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_TERCERO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes >= '{$mes_ingreso}' and mes <= '{$mes_salida}' and anio = '{$anio_fin}'")->queryScalar();
                
                endif;
                

            else:
            
                $total_dec_ter  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_TERCERO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes = '12' and anio = '{$anio_ini}'")->queryScalar();
            
                $total_dec_ter  = $total_dec_ter + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_TERCERO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes <= '{$mes_salida}' and anio = '{$anio_fin}'")->queryScalar();
           
            endif;
           
        else:
        
            $total_dec_ter  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_TERCERO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes = '12' and anio = '{$anio_ini}'")->queryScalar();
        
            $total_dec_ter  = $total_dec_ter + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_TERCERO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes <= '{$mes_salida}' and anio = '{$anio_fin}'")->queryScalar();
        
        endif;
        
        if($total_dec_ter > 0):
        
            $detalle[] = ['DESCRIPCION'=> 'DECIMO TERCERO', 'VALOR' => $total_dec_ter, 'TIPO' => 'I', 'DIAS'=> 0];
        
        endif;
        
    
        $decimo_cuarto = SysRrhhEmpleadosRolCab::find()->where(['anio'=> $anio_salida])->andWhere(['periodo'=> '71'])->one();
        
        
        if($decimo_cuarto):
            
                 if($fecha_ingreso >= $decimo_cuarto->fecha_ini && $fecha_ingreso <= $decimo_cuarto->fecha_fin):
            
                   if($mes_ingreso > 2):
                   
                        $total_dec_cua = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) from sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_CUARTO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes >= $mes_ingreso and anio = '{$anio_ini}'")->queryScalar();
                   
                        $total_dec_cua = $total_dec_cua + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_CUARTO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes < 3 and anio = '{$anio_fin}'")->queryScalar();
                   
                   
                   else:
                   
                        $total_dec_cua = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) from sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_CUARTO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes > 3 and anio = '{$anio_ini}'")->queryScalar();
                   
                        $total_dec_cua = $total_dec_cua + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_CUARTO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes <= $mes_ingreso and anio = '{$anio_fin}'")->queryScalar();
                   
                   
                   endif;
            
               
            else:
            
                $total_dec_cua = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) from sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_CUARTO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes >= 3 and anio = '{$anio_ini}'")->queryScalar();
            
                $total_dec_cua = $total_dec_cua + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_CUARTO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes < 3 and anio = '{$anio_fin}'")->queryScalar();
            
            
            endif;
        
        else:
        
            $total_dec_cua = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) from sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_CUARTO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes >= 3 and anio = '{$anio_ini}'")->queryScalar();
        
            $total_dec_cua = $total_dec_cua + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_CUARTO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes < 3 and anio = '{$anio_fin}'")->queryScalar();
            
        endif;
        
        
        if($total_dec_cua):
        
          $detalle[] = ['DESCRIPCION'=> 'DECIMO CUARTO', 'VALOR' => $total_dec_cua, 'TIPO' => 'I', 'DIAS'=> 0];
        
        endif;
        
        
        
        $aniovacini =  $anio_ingreso;
        $aniovacfin =  $anio_salida;
        
        $valorvac  = 0;
        
        if($anio_ingreso != $anio_salida):
        
            if($mes_ingreso == 1):
            
                $aniovacfin++;
                $aniovacini = $anio_salida;
                $valorvac  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes >= {$mes_ingreso} and anio = '{$anio_salida}'")->queryScalar();
                
            else:
            
                
                $aniovacini = $anio_salida -1;
                $aniovacfin = $anio_salida;
                
                if($aniovacini ==  2019):
                
                    $valorvac   =   Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes >= '{$mes_ingreso}' and  nrp_per_mes < 10  and nrp_per_anio = '{$aniovacini}';")->queryScalar();
                
                endif;
                
                $valorvac   =   $valorvac + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}' and  mes >= {$mes_ingreso} and anio = '{$aniovacini}' and estado = 'P'")->queryScalar();
                $valorvac   =   $valorvac + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes <= {$mes_ingreso} and anio = '{$aniovacfin}' and estado = 'P'")->queryScalar();
                
                
            
            endif;
        
        else:
        
            $aniovacfin++;
            $valorvac  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes >= {$mes_ingreso} and anio = '{$anio_salida}'")->queryScalar();
            
        endif;
        
        
        if($anio_ingreso != $anio_salida):
        
            $next    =  $anio_salida + 1;
            $anioant =  $anio_salida - 1;
            if($anio_salida >= 2020):
            
                
                if($mes_ingreso == 1):
                
                    $valorvac   =   Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}' and  mes >= {$mes_ingreso} and anio = '{$anio_salida}'")->queryScalar();
                
                else:
                
                    $valorvac   =   Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}' and  mes >= {$mes_ingreso} and anio = '{$anioant}'")->queryScalar();
                    $valorvac   =   $valorvac + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes <= {$mes_ingreso} and anio = '{$anio_salida}'")->queryScalar();
                    
                endif;
                
            
            elseif($anio_salida == 2019):
            
                $valorvac   =   Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes >= '{$mes_ingreso}' and  nrp_per_mes < 10  and nrp_per_anio = '{$anio_salida}';")->queryScalar();
                $valorvac   =    $valorvac +  Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}' and mes > 9 and  anio = '{$next}'")->queryScalar();
                $valorvac   =    $valorvac + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes <= {$mes_ingreso} and anio = '{$next}'")->queryScalar();
                
            endif;
            
        
        else:
        
            $valorvac  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes <= {$mes_ingreso} and anio = '{$anio_salida}'")->queryScalar();
        
        endif;
        
        
        $detalle[]      = ['DESCRIPCION'=> 'Vacaciones Periodo '. $aniovacini.'-'.$aniovacfin, 'VALOR' => $valorvac, 'TIPO' => 'I', 'DIAS'=> $valorvac ];
        
        
        
        
   
       /*
      
        $total_dec_ter = 0;
        
        $total_dec_cua = 0;
        
        $decimo_tercero  =  SysRrhhEmpleadosRolCab::find()->Where(['periodo'=> '70'])->orderBy(['fecha_registro'=> SORT_DESC])->one();
        
       if($decimo_tercero):
        
            if($decimo_tercero->anio < $anio_fin):
            
                $total_dec_ter = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_TERCERO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes = '12' and anio = '{$anio_ini}'")->queryScalar();
            
                $total_dec_ter = $total_dec_ter + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_TERCERO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes <= '{$messalida}' and anio = '{$anio_fin}'")->queryScalar();
            
            else:
            
                $total_dec_ter = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_TERCERO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes = '12' and anio = '{$anio_ini}'")->queryScalar();
            
            endif;
            
        endif;
        
        
        $decimo_cuarto  =  SysRrhhEmpleadosRolCab::find()->Where(['periodo'=> '71'])->orderBy(['fecha_registro'=> SORT_DESC])->one();
       
        if($decimo_cuarto):
        
            if($total_dec_ter > 0):
                    
                $detalle[] = ['DESCRIPCION'=> 'DECIMO TERCERO', 'VALOR' => $total_dec_ter, 'TIPO' => 'I', 'DIAS'=> 0];
            
            endif;
            
        endif;
        
        $total_dec_cua = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_CUARTO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes >= 3 and anio = '{$anio_ini}'")->queryScalar();
        
        $total_dec_cua = $total_dec_cua + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_CUARTO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes < 3 and anio = '{$anio_fin}'")->queryScalar();
        
        
        if($total_dec_cua > 0):
        
            $detalle[] = ['DESCRIPCION'=> 'DECIMO CUARTO', 'VALOR' => $total_dec_cua, 'TIPO' => 'I', 'DIAS'=> 0];
        
        endif;
        
        */
        /*
        $totaldectercero    =  0;
        
        $totaldeccuarto     =  0;
       
        $aniosalida         =  date('Y', strtotime($fecha_salida));
        
        $messalida          =  date('n', strtotime($fecha_salida));
        
        $anioingreso        =  date('Y', strtotime($fecha_ingreso));
        
        $mesingreso         =  date('n', strtotime($fecha_ingreso));
        
        $diaslab            =  0;
        
        $empleado           = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->one();
       
        $mesdecimo          = 0;
        
        $aniodecimo         = 0;
        
        $valorvac           = 0;
        
      
        if($empleado):
        
                if($empleado->decimo == 'N'):
                        
                        //Decimo Tercer Sueldo
                        
                        $decimotercero      =  SysRrhhEmpleadosRolCab::find()->Where(['periodo'=> '70'])->orderBy(['fecha_registro'=> SORT_DESC])->one();
        
                        if($decimotercero):
                        
                        
                            if($decimotercero->anio < $aniosalida):
                                
                                $totaldectercero      =   Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_TERCERO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes = '12' and anio = '{$decimotercero->anio}'")->queryScalar();
                                $totaldectercero      =   $totaldectercero + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_TERCERO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes <= '{$messalida}' and anio = '{$aniosalida}'")->queryScalar();
                                
                             else:
                                
                                $totaldectercero     =   Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='DECIMO_TERCERO' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes = '12' and anio = '{$aniosalida}'")->queryScalar();
                                
                             endif;
                                
                                
                                $detalle[] = ['DESCRIPCION'=> 'DECIMO TERCERO', 'VALOR' => $totaldectercero, 'TIPO' => 'I', 'DIAS'=> 0];
                                
                         endif;
                                   
                         //Decimo Cuarto Sueldo
                         $decimocuarto = SysRrhhEmpleadosRolCab::find()->Where(['periodo'=> '71'])->orderBy(['fecha_registro'=> SORT_DESC])->one();
                                
                                
                        if($decimocuarto):
                                
                               //sueldo basico valor
                               $SueldoBasico      = SysRrhhConceptos::find()->select('valor')->where(['concepto_sueldo'=> 'SU'])->andwhere(['pago'=> '2'])->andWhere(['>', 'valor', '0'])->andWhere(['tipo_valor'=> 'V'])->scalar();
                                
                               //2019
                               $aniodecimo   = date('Y', strtotime($decimocuarto->fecha_fin_liq));
                               //03
                               $mesdecimo    = date('n', strtotime($decimocuarto->fecha_fin_liq)) + 1;
                       
                              //sueldo basico
                        
                               if($fecha_ingreso > $aniodecimo.'-'.str_pad($mesdecimo, 2, "0", STR_PAD_LEFT).'-01'):
                                    
                                      $date1           = new \DateTime($fecha_ingreso);
                                      $date2           = new \DateTime($fecha_salida);
                                      $diff            = $date1->diff($date2);
                                     
                                     if($aniosalida != $anioingreso):
                                                               
                                        $meses = (12 -  date('m', strtotime($fecha_ingreso))) + (date('m', strtotime($fecha_salida)) - 1);
                 
                                     else:
                                     
                                         $meses =  date('m', strtotime($fecha_salida)) - date('m', strtotime($fecha_ingreso)) - 1;
                                     
                                     endif;
                                      
                                     $diasentrada = 30 - date('d', strtotime($fecha_ingreso)) + 1;
                                      
                                     $diaslab      =  ($meses * 30) + date('d', strtotime($fecha_salida))  + $diasentrada;
          
                               else:
                               
                                       $date1     = new \DateTime($aniodecimo.'-'.str_pad($mesdecimo, 2, "0", STR_PAD_LEFT).'-01');
                                       $date2     = new \DateTime($fecha_salida);
                                       $diff      = $date1->diff($date2);
                                       // will output 2 days
                                       $meses = ( $diff->y * 12 ) + $diff->m;
                                       // will output 2 days
                                       $diaslab      =( $meses * 30) + date('d', strtotime($fecha_salida));
                               
                                       
                               endif;

                            $totaldeccuarto = ($SueldoBasico* $diaslab)/360; 
                            $detalle[]      = ['DESCRIPCION'=> 'DECIMO CUARTO', 'VALOR' => $totaldeccuarto, 'TIPO' => 'I', 'DIAS'=> $diaslab ];
                                
                      endif;
                      
                      
                     //Provicion Vacaciones 
                     
                    $aniovacini =  $anioingreso;
                    $aniovacfin =  $aniosalida;
                      
                     
                     if($anioingreso != $aniosalida):
                      
                         if($mesingreso == 1):
                         
                               $aniovacfin++;
                               $aniovacini = $aniosalida;
                               $valorvac  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes >= {$mesingreso} and anio = '{$aniosalida}'")->queryScalar();
                         
                         
                         
                         else:
                         
                                         
                                 $aniovacini = $aniosalida -1;        
                                 $aniovacfin = $aniosalida;
                                 
                                 if($aniovacini ==  2019):
                                 
                                     $valorvac   =   Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes >= '{$mesingreso}' and  nrp_per_mes < 10  and nrp_per_anio = '{$aniovacini}';")->queryScalar();
                                 
                                 endif;
                                 
                                 $valorvac   =   $valorvac + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}' and  mes >= {$mesingreso} and anio = '{$aniovacini}' and estado = 'P'")->queryScalar();
                                 $valorvac   =   $valorvac + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes <= {$mesingreso} and anio = '{$aniovacfin}' and estado = 'P'")->queryScalar();
                                 
                                 
                         
                         endif;
                     
                      else:
                      
                             $aniovacfin++;
                             $valorvac  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes >= {$mesingreso} and anio = '{$aniosalida}'")->queryScalar();
                      
                      endif;
                      
                     
                     if($anioingreso != $aniosalida):
                               
                                  $next    =  $aniosalida + 1;
                                  $anioant =  $aniosalida - 1;
                                  if($aniosalida >= 2020):
                                  
                                  
                                     if($mesingreso == 1):
                                     
                                         $valorvac   =   Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}' and  mes >= {$mesingreso} and anio = '{$aniosalida}'")->queryScalar();
                                     
                                     else:
                                     
                                         $valorvac   =   Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}' and  mes >= {$mesingreso} and anio = '{$anioant}'")->queryScalar();
                                         $valorvac   =   $valorvac + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes <= {$mesingreso} and anio = '{$aniosalida}'")->queryScalar();
                                     
                                     endif;
                                  
                
                                  elseif($aniosalida == 2019):
                                  
                                        $valorvac   =   Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes >= '{$mesingreso}' and  nrp_per_mes < 10  and nrp_per_anio = '{$aniosalida}';")->queryScalar();
                                        $valorvac   =    $valorvac +  Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}' and mes > 9 and  anio = '{$next}'")->queryScalar();
                                        $valorvac   =    $valorvac + Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes <= {$mesingreso} and anio = '{$next}'")->queryScalar();
                                  endif;
                                  
                         
                         else:
                              
                                   $valorvac  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes <= {$mesingreso} and anio = '{$aniosalida}'")->queryScalar();
                           
                         endif;
                     
                       
                       $detalle[]      = ['DESCRIPCION'=> 'Vacaciones Periodo '. $aniovacini.'-'.$aniovacfin, 'VALOR' => $valorvac, 'TIPO' => 'I', 'DIAS'=> $valorvac ];
                    
                      
                      
                      
  
                      
                endif;
         endif;
         */
        return $detalle;
    }
    
    private function getVacaciones ($id_sys_rrhh_cedula, $fecha_ingreso, $fecha_salida){
        
        
        $detalle = [];
        
        
        $aniosalida         =  date('Y', strtotime($fecha_salida));
        $mesingreso         =  date('n', strtotime($fecha_ingreso));
        $messalida          =  date('n', strtotime($fecha_salida));
        $anioingreso        =  date('Y', strtotime($fecha_ingreso));
        
        $valorvac           =  0;
       
        //Verificamos periodos pendientes de vacaciones
        $periodovacaciones =  (new \yii\db\Query())->select(["dias_disponibles", "(dias_disponibles - dias_otorgados) as diaspendientes", "sys_rrhh_empleados_periodo_vacaciones.id_sys_adm_periodo_vacaciones", "anio_vac", "anio_vac_hab", "periodo"])
        ->from("sys_rrhh_empleados_periodo_vacaciones")
        ->innerJoin("sys_adm_periodo_vacaciones", "sys_rrhh_empleados_periodo_vacaciones.id_sys_adm_periodo_vacaciones = sys_adm_periodo_vacaciones.id_sys_adm_periodo_vacaciones")
        ->where("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
        ->andwhere("(dias_disponibles - dias_otorgados) > 0")
        ->andwhere("sys_rrhh_empleados_periodo_vacaciones.id_sys_empresa = '001'")
        ->all(SysRrhhEmpleadosPeriodoVacaciones::getDb());
        
        if($periodovacaciones):
        
                foreach ($periodovacaciones as $periodos):
                
                        $valorvac = $this->getValProvacaciones($id_sys_rrhh_cedula, $periodos['anio_vac'], $periodos['anio_vac_hab'], $periodos['id_sys_adm_periodo_vacaciones'], $mesingreso);
                        
                        if($valorvac > 0):
                        
                                 $valordia =  $valorvac / $periodos['dias_disponibles'];
                        
                                 $detalle[] = ['DESCRIPCION'=> 'Vacaciones '.$periodos['periodo'], 'VALOR' => $valordia * $periodos['diaspendientes'], 'TIPO' => 'I', 'DIAS'=> $periodos['diaspendientes']];
                        
                        endif;
                
                endforeach;
                       
        endif;
      
      return $detalle;
      
    }
    
    private function getDesahucio($id_sys_rrhh_cedula, $fecha_ingreso, $fecha_salida, $id_causa_salida, $sueldo){
        
            $detalle    = [];
        
            if($id_causa_salida == 'V' || $id_causa_salida == 'R'):
            
                 $anios =  (new \yii\db\Query())
                ->select(["(cast(datediff(dd,fecha_ingreso, fecha_salida) / 365.25 as int))"])
                ->from("sys_rrhh_empleados_contratos")
                ->Where("fecha_salida  = '{$fecha_salida}'")
                ->andWhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
                ->andWhere("id_sys_rrhh_causa_salida = '{$id_causa_salida}'")
                ->scalar(SysRrhhEmpleados::getDb());
               
                $desahucio  = 0;
                  
                if($anios > 0):
                
                
                    $sueldo = $this->getUltimaRemuneracion($id_sys_rrhh_cedula, $fecha_salida);
     
                    $desahucio = ($sueldo * 0.25) * $anios;
                    $detalle[] = ['DESCRIPCION'=> 'BONIFICACIÓN POR DESAHUCIO', 'VALOR' => $desahucio, 'TIPO' => 'I', 'DIAS'=> $anios];

                endif;
            
            endif;
        
        return $detalle;
    }
    
    private function getUltimaRemuneracion($id_sys_rrhh_cedula, $fecha_salida){
        
    
        
        $rol = SysRrhhEmpleadosRolMov::find()->
            where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])
            ->andWhere(['periodo'=> '2'])
            ->andWhere(['id_sys_rrhh_concepto'=> 'SUELDO'])
            ->andWhere(['estado'=> 'P'])
            ->andWhere(['cantidad' => '30'])
            ->orderBy(['anio'=> SORT_DESC])
            ->one();
       
        if($rol):
        
                $remuneracion  =  (new \yii\db\Query())->select(
                    [
                        "SUM(mov.valor)"
                    ])
                    ->from("sys_rrhh_empleados_rol_mov mov")
                    ->innerJoin('sys_rrhh_conceptos con',"mov.id_sys_rrhh_concepto = con.id_sys_rrhh_concepto")
                    ->where("con.tipo = 'I'")
                    ->andwhere("aporta_iess = 'S'")
                    ->andwhere("mes = '{$rol->mes}'")
                    ->andwhere("anio = '{$rol->anio}'")
                    ->andwhere("periodo = '{$rol->periodo}'")
                    ->andWhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
                    ->scalar(SysRrhhEmpleados::getDb());
                
                return $remuneracion;
        
       endif;
       
       return 0;
    }
   
    private function getDespido($id_sys_rrhh_cedula, $fecha_ingreso, $fecha_salida, $id_sys_causa_salida, $sueldo){
        
           
          $anios =  (new \yii\db\Query())
          ->select(["(cast(datediff(dd,fecha_ingreso, fecha_salida) / 365.25 as int))"])
          ->from("sys_rrhh_empleados_contratos")
          ->Where("fecha_salida  = '{$fecha_salida}'")
          ->andWhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
          ->andWhere("id_sys_rrhh_causa_salida = '{$id_sys_causa_salida}'")
          ->scalar(SysRrhhEmpleados::getDb());
        
          $contrato  =  (new \yii\db\Query())->select(["id_sys_rrhh_contrato"])
          ->from("sys_rrhh_empleados")
          ->where("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
          ->one(SysRrhhEmpleados::getDb());
          
          $detalle = [];
        
          if($contrato['id_sys_rrhh_contrato'] == 1 || $contrato['id_sys_rrhh_contrato'] == 2):
          
          
              if($id_sys_causa_salida == 'R'):
              
                  if($anios > 0):
                    
                     if($anios > 3):
                         $detalle[] = ['DESCRIPCION'  => 'BONIFICACION POR DESPIDO', 'VALOR' => floatval($anios * $sueldo), 'TIPO' => 'I', 'DIAS'=> $anios];     
                      else:
                         $detalle[] = ['DESCRIPCION' => 'BONIFICACION POR DESPIDO', 'VALOR' => floatval(3 * $sueldo), 'TIPO' => 'I', 'DIAS'=> $anios];
                      endif;
                    
                   else:
     
                       $dias =  (new \yii\db\Query())
                       ->select(["(cast(datediff(dd,fecha_ingreso, fecha_salida) / 1 as int))"])
                       ->from("sys_rrhh_empleados_contratos")
                       ->Where("fecha_salida  = '{$fecha_salida}'")
                       ->andWhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
                       ->andWhere("id_sys_rrhh_causa_salida = '{$id_sys_causa_salida}'")
                       ->scalar(SysRrhhEmpleados::getDb());
                          
                      if($dias > 90):
                           $detalle[] = ['DESCRIPCION' => 'BONIFICACION POR DESPIDO', 'VALOR' => floatval(3 * $sueldo), 'TIPO' => 'I', 'DIAS'=> $anios];
                      endif;
                      
                   endif;
              
              endif;
              
          endif;      
        
        return $detalle;
        
     
    }
     
    private function getContrato($id_sys_rrhh_cedula){
        
        
          $datos   = [];
        
        
          $salario = 0;
          
          $contrato  =  (new \yii\db\Query())->select(
            [
               
                "fecha_ingreso",
                "fecha_salida",
                "descripcion",
                "id_sys_rrhh_empleados_contrato_cod",
                "contratos.id_sys_rrhh_causa_salida"
            ])
            ->from("sys_rrhh_empleados_contratos contratos")
            ->innerJoin('sys_rrhh_causa_salida motivo','motivo.id_sys_rrhh_causa_salida=contratos.id_sys_rrhh_causa_salida')
            ->where("contratos.id_sys_empresa = '001'")
            ->andWhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
            ->orderby("fecha_ingreso desc")
            ->one(SysRrhhEmpleados::getDb());
        
        if($contrato['fecha_salida'] != ''):
            
            //busca sueldo
            $sueldo = SysRrhhEmpleadosSueldos::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])->orderBy(['fecha'=> SORT_DESC])->one();
         
            //buscar el sueldo de la ultima remuneracion 
             $anio  =  date('Y', strtotime($contrato['fecha_salida']));
             $mes   =  date('n', strtotime($contrato['fecha_salida']));
       
             $rol   = SysRrhhEmpleadosRolMov::find()
            ->where(['anio'=> $anio])
            ->andWhere(['mes'=> $mes])
            ->andwhere(['periodo'=>'2'])
            ->andWhere(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])
            ->andWhere(['id_sys_rrhh_concepto' => 'SUELDO'])
            ->one();
            
             if($rol):
             
             
                 if($rol->valor > $sueldo['sueldo'] ):
                 
                    $salario = $rol->valor;
                 
                 
                 else:
                 
                    $salario =  $sueldo['sueldo'];
                
                 
                 endif;
         
                 $datos[] = array(
                     'fecha_ingreso'=> $contrato['fecha_ingreso'],
                     'fecha_salida'=> $contrato['fecha_salida'],
                     'causa_salida'=> $contrato['descripcion'],
                     'id_sys_rrhh_empleados_contrato'=> $contrato['id_sys_rrhh_empleados_contrato_cod'],
                     'id_causa_salida'=> $contrato['id_sys_rrhh_causa_salida'],
                     'sueldo'=> $salario);
               
             endif;
      
             
        endif;
        
        
        return $datos;
        
       
    }
    
    private function getValProvacaciones($id_sys_rrhh_cedula, $aniovac, $aniohab, $codperiodo, $mesingreso){
        
       
        $anio_act         = 0;
        $anio_ant         = 0;
        $anio_ant_gestion = 0;
        
        $db = $_SESSION['db'];
        
        if($aniohab < 2019):
        
                //revisar en el PBs
                $anio_ant  = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes >= '{$mesingreso}'  and nrp_per_anio = '{$aniovac}';")->queryScalar();
                $anio_act  = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes < '{$mesingreso}'  and nrp_per_anio = '{$aniohab}';")->queryScalar();
                
                return ( $anio_act + $anio_ant);
        
        else:
        
                if($aniohab > 2020):
                
                        
                        //revisar en vacaciones en gestion
                        $anio_ant  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes >= '{$mesingreso}' and anio = '{$aniovac}'")->queryScalar();
                        $anio_act  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes < '{$mesingreso}' and anio = '{$aniohab}'")->queryScalar();
                        
                else:
                        //revisar periodo 2018 and 2019
                        if($aniohab == 2019):
                                
                                if($mesingreso > 9):
                                  
                                    //revisar en el pbs anio 2018 y 2019
                                    $anio_ant          = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes >= '{$mesingreso}'  and nrp_per_anio = '{$aniovac}';")->queryScalar();
                                    $anio_act          = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes < '{$mesingreso}'  and nrp_per_anio = '{$aniohab}';")->queryScalar();
                                    
                                    //revisar gestion
                                    $anio_ant_gestion  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}' and mes > 9")->queryScalar();
                                    
                                    return ($anio_act + $anio_ant + $anio_ant_gestion);
                                
                                else :
                                
                                    //revisar en el pbs anio 2018 y 2019 hasta septiembre
                                    $anio_ant  = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes >= '{$mesingreso}'  and nrp_per_anio = '{$aniovac}';")->queryScalar();
                                    $anio_act  = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes < '{$mesingreso}'  and nrp_per_anio = '{$aniohab}';")->queryScalar();
                                    
                                    return ($anio_act + $anio_ant);
                                    
                     
                                endif;
                        
                        ///revisar vacaciones del periodo 2019 hasta 2020
                        elseif($aniohab == 2020):
                        
                                if($mesingreso > 9):
                                
                                    //revisar en el pbs anio 2019 y 2020
                                    $anio_ant          = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes >= '{$mesingreso}'  and nrp_per_anio = '{$aniovac}';")->queryScalar();
                                    $anio_act          = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes < '{$mesingreso}'  and nrp_per_anio = '{$aniohab}';")->queryScalar();
                                    
                                    //revisar gestion
                                    $anio_ant_gestion  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}' and mes > 9")->queryScalar();
                                    
                                    return ($anio_act + $anio_ant + $anio_ant_gestion);
                                    
                                else:
                                
                                    //revisar en el pbs anio 2019 y 2020 hasta septiembre
                                    $anio_act          = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes >= '{$mesingreso}'  and nrp_per_anio = '{$aniovac}';")->queryScalar();
                                    $anio_ant          = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes < '{$mesingreso}'   and nrp_per_anio = '{$aniohab}';")->queryScalar();
                                    
                                    $anio_act_gestion  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}' and mes <  '{$mesingreso}' and anio = '{$aniohab}'")->queryScalar();
                                    $anio_ant_gestion  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}' and mes > 9 and anio = '{$aniovac}'")->queryScalar();
                                    
                                    return ($anio_act + $anio_ant+ $anio_ant_gestion + $anio_act_gestion);
                                
                                endif;
                        endif;
                endif;
        endif;
        
        return 0;
        
    }
    
    
    /**
     * Finds the SysRrhhFiniquitoCab model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return SysRrhhFiniquitoCab the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhFiniquitoCab::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
