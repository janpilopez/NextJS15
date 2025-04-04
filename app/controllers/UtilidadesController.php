<?php

namespace app\controllers;

use Yii;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhUtilidadesCab;
use app\models\Search\SysRrhhUtilidadesCabSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\SysRrhhEmpleadosContratos;
use app\models\SysRrhhUtilidadesDet;
use app\models\SysEmpresa;

/**
 * UtilidadesController implements the CRUD actions for SysRrhhUtilidadesCab model.
 */
class UtilidadesController extends Controller
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
     * Lists all SysRrhhUtilidadesCab models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhUtilidadesCabSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhUtilidadesCab model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
    
        $db = $_SESSION['db'];
        $model    = $this->findModel($id);
        $empresas = Yii::$app->$db->createCommand("exec UtilidadesListarEmpresas @db_name= '{$db}'")->queryAll(); 
        $empresa = '';
        $estado = '';
        $estados = ['' => 'Todos',  'A'=> 'Activo', 'I'=>'Inactivo'];
        
       
        
       
        if (Yii::$app->request->post()):
        
             $empresa    =  $_POST['empresa'] == null ?  '': $_POST['empresa']; 
             $estado =  $_POST['estado'] == null ?  '': $_POST['estado']; 
        
             
        endif;
        
        $modeldet = $this->getDetalleUtilidades($model->id_sys_empresa, $model->anio, $empresa, $estado);
       
        
        return $this->render('view', [
            'model' => $model,
            'modeldet'=> $modeldet,
            'empresas' => $empresas,
            'estados' => $estados,
            'empresa' => $empresa,
            'estado' => $estado
        ]);
        
    }
    
    public function  actionViewxls($id, $empresa, $estado){
             
        $model = $this->findModel($id);
        $modeldet = $this->getDetalleUtilidades($model->id_sys_empresa, $model->anio, $empresa, $estado);
        
        return $this->render('viewxls', [
            'model' => $model,
            'modeldet'=> $modeldet
        ]);
    }

    /**
     * Creates a new SysRrhhUtilidadesCab model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model      = new SysRrhhUtilidadesCab();
        $model->anio =  date('Y') -  1;
        
        if ($model->load(Yii::$app->request->post())) {
            
            $model->id_sys_empresa      = '001';
            $model->usuario_creacion    = Yii::$app->user->username;
            $model->estado              = 'G';
            $model->fecha               = date('Y-m-d');
            
            $periodo = SysRrhhUtilidadesCab::find()->where(['anio'=> $model->anio])->one();
            
            if(!$periodo):
            
                if($model->save(false)):
                    
                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'success','duration' => 1500,
                        'icon' => 'glyphicons glyphicons-robot',
                        'message' => 'Los datos han sido registrados con éxito!',
                        'positonY' => 'top','positonX' => 'right']);
              
                else:
                    
                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'danger','duration' => 1500,
                        'icon' => 'glyphicons glyphicons-robot',
                        'message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                        'positonY' => 'top','positonX' => 'right']);
                endif;
                
              else:
              
                 Yii::$app->getSession()->setFlash('info', [
                  'type' => 'warning','duration' => 1500,
                  'icon' => 'glyphicons glyphicons-robot',
                  'message' => 'El periodo ya ha se encuentra registrado! ',
                  'positonY' => 'top','positonX' => 'right']);
              
              endif;
            
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SysRrhhUtilidadesCab model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->anio]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SysRrhhUtilidadesCab model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
       $model = $this->findModel($id);
       $model->estado = 'A';
       $model->save(false);
       
        return $this->redirect(['index']);
    }

    public function actionProcesar($id){
        
 
        $model         = $this->findModel($id);
        $model->estado = 'P';
        $model->usuario_actualizacion   = Yii::$app->user->username;
        $model->fecha_actualizacion     = date('Ymd H:i:s');
       if ($model->save()): 
       
           Yii::$app->getSession()->setFlash('info', [
               'type' => 'success','duration' => 1500,
               'icon' => 'glyphicons glyphicons-robot',
               'message' => 'El proceso se realizado con éxito!',
               'positonY' => 'top','positonX' => 'right']);
       
       else:
       
           Yii::$app->getSession()->setFlash('info', [
               'type' => 'warning','duration' => 1500,
               'icon' => 'glyphicons glyphicons-robot',
               'message' => 'Ha ocurrido un error. Consulte con su administrador!',
               'positonY' => 'top','positonX' => 'right']);
       
       endif;
       
       return $this->redirect('index');
           
    }
    
    public function actionLiquidar($id){
        
        $db              = $_SESSION['db'];
        $model           = $this->findModel($id);
        
        if($model->estado != 'P') :
        
            Yii::$app->$db->createCommand("delete  from sys_rrhh_utilidades_det where anio = '{$model->anio}'")->execute();
            $valorXEmpleado = floatval(($model->valor_uti_empleado/($model->valor_uti_empleado +$model->valor_uti_carga))*$model->valor_uti);
            $valorXCarga    = floatval(($model->valor_uti_carga/($model->valor_uti_empleado +$model->valor_uti_carga))*$model->valor_uti);
            
            $datosEmpleados = [];
            
            $totalCargas   = 0;
            
            $listEmpleados = $this->getEmpleadosUtilidadesXAnio( intval($model->anio));
            
            $empleadosExt = (new \yii\db\Query())->select([
                "comp.nombre as compania",
                "id_sys_rrhh_cedula",
                "nombres",
                "cargas_familiares",
                "dias_laborados",
                "emp.estado"
            ])
            ->from("sys_rrhh_empresa_servicios_empleados emp")
            ->innerJoin("sys_rrhh_empresa_servicios comp", "emp.id_sys_rrhh_empresa_servicios = comp.id_sys_rrhh_empresa_servicio")
            ->where("emp.estado = 'A'")
            ->orderBY("nombres")
            ->all(SysRrhhEmpleados::getDb());
            
            foreach ($listEmpleados as $item):
            
            array_push($datosEmpleados,[
                "id_sys_rrhh_cedula"=> $item["id_sys_rrhh_cedula"],
                "id_sys_empresa" => '001',
                "nombres" =>$item["nombres"],
                "dias_laborados" => intval($item["dias"]),
                "dias_cargas" => intval($item["dias"]*intval($item['cargas'])),
                "cargas_familiares" => intval($item['cargas']),
                "estado" => $item['estado']
            ]);
            
            endforeach;
            
            foreach ($empleadosExt as $item):
            
            array_push($datosEmpleados,[
                "id_sys_rrhh_cedula"=> $item["id_sys_rrhh_cedula"],
                "id_sys_empresa" => '002',
                "nombres" =>$item["nombres"],
                "dias_laborados" => intval($item["dias_laborados"]),
                "dias_cargas" => intval($item["dias_laborados"]*intval($item['cargas_familiares'])),
                "cargas_familiares" => intval($item['cargas_familiares']),
                "estado" => $item['estado']
            ]);
            
            endforeach;
            
            
            $totalDias = floatval(array_sum(array_column($datosEmpleados, 'dias_laborados')));
            $totalCargas = floatval(array_sum(array_column($datosEmpleados, 'dias_cargas')));
            
            //$totalEmpleado = count($datosEmpleados) + count($empleadosExt);
            //$totalCargas = intval(array_sum(array_column($datosEmpleados, 'cargas_familiares'))) + intval(array_sum(array_column($empleadosExt, 'cargas_familiares')));
            
            $utilidadXEmpleado = floatval($valorXEmpleado)/$totalDias;
            
         
            $utilidadXCarga = $totalCargas != 0 ?   floatval($valorXCarga)/$totalCargas : 0;
            
            
            
            foreach ($datosEmpleados as $item):
            
            $this->RegistraUtilidad(
                $model->anio,
                $item['id_sys_rrhh_cedula'],
                $item["cargas_familiares"],
                ($utilidadXEmpleado * intval($item['dias_laborados'])),
                ($utilidadXCarga * intval($item['dias_cargas'])),
                $item["dias_laborados"],
                $item['id_sys_empresa'],
                $item["estado"]
                );
            
            endforeach;
        
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'success','duration' => 1500,
                'icon' => 'glyphicons glyphicons-robot',
                'message' => 'El proceso se realizado con éxito!',
                'positonY' => 'top','positonX' => 'right']);
        
        else:
        
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'success','duration' => 1500,
                'icon' => 'glyphicons glyphicons-robot',
                'message' => 'El periodo utilidades '.$model->anio.' se encuentra liquidado!',
                'positonY' => 'top','positonX' => 'right']);
        
        
        endif;
        
        return $this->redirect('index');
        
    }
    
    private function  getEmpleadosUtilidadesXAnio($anio){
       
      $db = $_SESSION['db'];   
      return  Yii::$app->$db->createCommand("exec [dbo].[ObtenerUtilidadesXAnio] {$anio}")->queryAll();
    }
    
    private function RegistraUtilidad($anio, $id_sys_rrhh_cedula, $cargas_familiares, $uti_empleados, $uti_cargas, $dias, $id_sys_empresa, $estado){
        
        
        $db = $_SESSION['db'];
        $transaction = \Yii::$app->$db->beginTransaction();
        
        try {
            //insertamos el sueldo del empleador
            $user = Yii::$app->user->identity->username;    
            Yii::$app->$db->createCommand("exec RegistrarDetalleUtilidad @anio = '{$anio}', @id_sys_rrhh_cedula =  '{$id_sys_rrhh_cedula}', @cargas_familiares = {$cargas_familiares}, @uti_empleados = {$uti_empleados}, @uti_cargas = {$uti_cargas}, @id_sys_empresa = '{$id_sys_empresa}', @dias = {$dias}, @usuario_creacion = '{$user}', @estado= '{$estado}'")->execute();
            
            $transaction->commit();

        } catch (\ErrorException $e) {
            
            $transaction->rollBack();
            echo json_encode($e->getMessage());
 
        }
        
    }
    
    private function getDetalleUtilidades($empresa, $anio, $razon_social, $estado){
        
        
        $db = $_SESSION['db'];
        
        $datos  = Yii::$app->$db->createCommand("EXEC Utilidades @empresa = '{$empresa}', @anio = '{$anio}', @razon_social = '{$razon_social}', @estado = '{$estado}'")->queryAll();
        
        return $datos;
    }
    
    /**
     * Finds the SysRrhhUtilidadesCab model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return SysRrhhUtilidadesCab the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhUtilidadesCab::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
