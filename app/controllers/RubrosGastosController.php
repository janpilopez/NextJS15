<?php

namespace app\controllers;

use Yii;
use app\models\SysRrhhRubrosGastos;
use app\models\Search\SysRrhhRubrosGastosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RubrosGastosController implements the CRUD actions for SysRrhhRubrosGastos model.
 */
class RubrosGastosController extends Controller

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
     * Lists all SysRrhhRubrosGastos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhRubrosGastosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhRubrosGastos model.
     * @param string $id_sys_empresa
     * @param string $id_sys_rrhh_rubros_gastos
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        
        $model = $this->findModel($id);
        
        $model->detalle = $model->detalle;
        
        
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new SysRrhhRubrosGastos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysRrhhRubrosGastos();

        if ($model->load(Yii::$app->request->post())) {
            
            $codigo = SysRrhhRubrosGastos::find()->select(['max(CAST(id_sys_rrhh_rubros_gastos AS INT))'])->Where(['id_sys_empresa'=> '001'])->scalar();
            
            $model->id_sys_rrhh_rubros_gastos = $codigo + 1;
            $model->id_sys_empresa             = '001';
            
            if($model->save(false)){
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido registrados con éxito!',
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
        ]);
    }

    /**
     * Updates an existing SysRrhhRubrosGastos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_empresa
     * @param string $id_sys_rrhh_rubros_gastos
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $model->detalle = $model->detalle;

        if ($model->load(Yii::$app->request->post())) {
            
            $model->detalle = $model->detalle;
            
            if($model->save(false)){
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido actualizados con  éxito!',
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
        ]);
    }

    /**
     * Deletes an existing SysRrhhRubrosGastos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_sys_empresa
     * @param string $id_sys_rrhh_rubros_gastos
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id);

        return $this->redirect(['index']);
    }

    /**
     * Finds the SysRrhhRubrosGastos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_sys_empresa
     * @param string $id_sys_rrhh_rubros_gastos
     * @return SysRrhhRubrosGastos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhRubrosGastos::findOne(['id_sys_rrhh_rubros_gastos' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
