<?php

namespace app\controllers;

use app\models\Search\SysAdmCanastaBasicaSearch;
use Yii;
use app\models\SysAdmCanastaBasica;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\SysRrhhConceptos;

/**
 * HistorialSueldoController implements the CRUD actions for SysAdmCanastaBasica model.
 */
class CanastaBasicaController extends Controller
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
     * Lists all SysAdmCanastaBasica models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysAdmCanastaBasicaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysAdmCanastaBasica model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($anio)
    {
        return $this->render('view', [
            'model' => $this->findModel($anio),
        ]);
    }

    /**
     * Creates a new SysAdmCanastaBasica model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysAdmCanastaBasica();
        $model->anio = date('Y');
        
        if ($model->load(Yii::$app->request->post())) {
            
              $model->usuario_creacion = Yii::$app->user->username;
              $model->fecha_creacion = date('Ymd H:i:s');
              $model->activo = true;
              $model->save(false);
            
              Yii::$app->getSession()->setFlash('info', [
              'type' => 'success','duration' => 1500,
              'icon' => 'glyphicons glyphicons-robot',
              'message' => 'Los datos han sido registrados con éxito!',
              'positonY' => 'top','positonX' => 'right']);   
         
            return $this->redirect(['index']);
            
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SysAdmCanastaBasica model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($anio)
    {
        $model = $this->findModel($anio);

        if ($model->load(Yii::$app->request->post())) {
            
            $model->save();
            
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'success','duration' => 1500,
                'icon' => 'glyphicons glyphicons-robot',
                'message' => 'Datos actualizados con éxito!',
                'positonY' => 'top','positonX' => 'right']);
            
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /*public function actionAutorizar($anio){
        
        $model = $this->findModel($anio);
        
        if (!$model->autorizado):
        
        $model->user_autorization = Yii::$app->user->username;
        $model->date_autorization = date('Ymd H:i:s');
        $model->activo = true;
        $model->autorizado = true;
        $model->save(false);
        
        $this->ActualizarSueldoBasico($model->sueldo_basico);
        $this->ActualizarSueldoEmpleados($model);
        
        Yii::$app->getSession()->setFlash('info', [
            'type' => 'success','duration' => 1500,
            'icon' => 'glyphicons glyphicons-robot',
            'message' => 'Los datos han sido registrados con éxito!',
            'positonY' => 'top','positonX' => 'right']);
        
        else:
        
        Yii::$app->getSession()->setFlash('info', [
            'type' => 'warning','duration' => 1500,
            'icon' => 'glyphicons glyphicons-robot',
            'message' => 'El registro se encuentra autorizado!',
            'positonY' => 'top','positonX' => 'right']);
        
        endif;
        
        return $this->redirect(['index']);
        
    }*/
    
    /**
     * Deletes an existing SysAdmCanastaBasica model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($anio)
    {
        $this->findModel($anio)->delete();
        return $this->redirect(['index']);
    }

    /*private function ActualizarSueldoBasico($sueldo){
        
        $model = SysRrhhConceptos::findOne('SUELDO_BASICO');
        
        if ($model):
            $model->valor = $sueldo;
            $model->save(false);
        endif;
        
    }*/
    
    /*private  function ActualizarSueldoEmpleados($model){
       
        $sueldoAnt = $this->findModel($model->anio - 1);
        
        if(!$sueldoAnt):
          $sueldoAnt = $model;
        endif;
        
        
        $db =  $_SESSION['db'];
        
        $datos = Yii::$app->$db->createCommand("exec [dbo].[ObtenerEmpleadosXSueldoSectorial]  @sueldo_sectorial = {$sueldoAnt->sueldo_sectorial}")->queryAll();
        
        foreach ($datos as $dato){
            
            $fecha = date('Y-m-d');
            $anticipo = $model->sueldo_sectorial * 0.40;
            $user = Yii::$app->user->username;
        
            Yii::$app->$db->createCommand("exec [dbo].[ActualizarSueldoSectorialEmpleado] {$dato['id_sys_rrhh_empleados_sueldo_cod']},'{$fecha}','A',{$model->sueldo_sectorial}, 0.40,'{$dato['id_sys_rrhh_cedula']}','{$dato['id_sys_empresa']}', {$anticipo}, '{$user}'")->execute();
        }
    }*/

    /**
     * Finds the SysAdmCanastaBasica model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return SysAdmCanastaBasica the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($anio)
    {
        if (($model = SysAdmCanastaBasica::findOne($anio)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
