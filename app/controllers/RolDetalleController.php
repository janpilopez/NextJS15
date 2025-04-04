<?php

namespace app\controllers;

use Yii;
use app\models\SysRrhhEmpleadosRolMov;
use app\models\search\SysRrhhEmpleadosRolMovSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\SysRrhhEmpleadosRolLiq;

/**
 * RolDetalleController implements the CRUD actions for SysRrhhEmpleadosRolMov model.
 */
class RolDetalleController extends Controller
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
     * Lists all SysRrhhEmpleadosRolMov models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhEmpleadosRolMovSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhEmpleadosRolMov model.
     * @param string $anio
     * @param string $mes
     * @param string $periodo
     * @param string $id_sys_rrhh_cedula
     * @param string $id_sys_rrhh_concepto
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($anio, $mes, $periodo, $id_sys_rrhh_cedula, $id_sys_rrhh_concepto, $id_sys_empresa)
    {
        return $this->render('view', [
            'model' => $this->findModel($anio, $mes, $periodo, $id_sys_rrhh_cedula, $id_sys_rrhh_concepto, $id_sys_empresa),
        ]);
    }

    /**
     * Creates a new SysRrhhEmpleadosRolMov model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysRrhhEmpleadosRolMov();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'anio' => $model->anio, 'mes' => $model->mes, 'periodo' => $model->periodo, 'id_sys_rrhh_cedula' => $model->id_sys_rrhh_cedula, 'id_sys_rrhh_concepto' => $model->id_sys_rrhh_concepto, 'id_sys_empresa' => $model->id_sys_empresa]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SysRrhhEmpleadosRolMov model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $anio
     * @param string $mes
     * @param string $periodo
     * @param string $id_sys_rrhh_cedula
     * @param string $id_sys_rrhh_concepto
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($anio, $mes, $periodo, $id_sys_rrhh_cedula, $id_sys_rrhh_concepto, $id_sys_empresa)
    {
        $model = $this->findModel($anio, $mes, $periodo, $id_sys_rrhh_cedula, $id_sys_rrhh_concepto, $id_sys_empresa);

        if ($model->load(Yii::$app->request->post())) {
            
            if($model->estado != 'P'):
            
               $model->save(false);
            
            
               
            endif;
            
            return $this->render('update', [
                'model' => $model,
            ]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
       
    /**
     * Deletes an existing SysRrhhEmpleadosRolMov model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $anio
     * @param string $mes
     * @param string $periodo
     * @param string $id_sys_rrhh_cedula
     * @param string $id_sys_rrhh_concepto
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($anio, $mes, $periodo, $id_sys_rrhh_cedula, $id_sys_rrhh_concepto, $id_sys_empresa)
    {
        $this->findModel($anio, $mes, $periodo, $id_sys_rrhh_cedula, $id_sys_rrhh_concepto, $id_sys_empresa)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SysRrhhEmpleadosRolMov model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $anio
     * @param string $mes
     * @param string $periodo
     * @param string $id_sys_rrhh_cedula
     * @param string $id_sys_rrhh_concepto
     * @param string $id_sys_empresa
     * @return SysRrhhEmpleadosRolMov the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($anio, $mes, $periodo, $id_sys_rrhh_cedula, $id_sys_rrhh_concepto, $id_sys_empresa)
    {
        if (($model = SysRrhhEmpleadosRolMov::findOne(['anio' => $anio, 'mes' => $mes, 'periodo' => $periodo, 'id_sys_rrhh_cedula' => $id_sys_rrhh_cedula, 'id_sys_rrhh_concepto' => $id_sys_rrhh_concepto, 'id_sys_empresa' => $id_sys_empresa])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
