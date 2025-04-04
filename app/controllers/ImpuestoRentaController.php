<?php

namespace app\controllers;

use Yii;
use app\models\SysRrhhImpuestoRenta;
use app\models\SysRrhhImpuestoRentaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ImpuestoRentaController implements the CRUD actions for SysRrhhImpuestoRenta model.
 */
class ImpuestoRentaController extends Controller
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
     * Lists all SysRrhhImpuestoRenta models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhImpuestoRentaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhImpuestoRenta model.
     * @param string $id_sys_empresa
     * @param string $id_sys_rrhh_impuesto_renta
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new SysRrhhImpuestoRenta model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysRrhhImpuestoRenta();

        if ($model->load(Yii::$app->request->post())) {
            
            $codigo = SysRrhhImpuestoRenta::find()->select(['max(CAST(id_sys_rrhh_impuesto_renta AS INT))'])->Where(['id_sys_empresa'=> '001'])->scalar();
            
            $model->id_sys_rrhh_impuesto_renta = $codigo + 1;
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
     * Updates an existing SysRrhhImpuestoRenta model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_empresa
     * @param string $id_sys_rrhh_impuesto_renta
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            
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
            
           // return $this->redirect(['view', 'id_sys_empresa' => $model->id_sys_empresa, 'id_sys_rrhh_impuesto_renta' => $model->id_sys_rrhh_impuesto_renta]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SysRrhhImpuestoRenta model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_sys_empresa
     * @param string $id_sys_rrhh_impuesto_renta
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SysRrhhImpuestoRenta model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_sys_empresa
     * @param string $id_sys_rrhh_impuesto_renta
     * @return SysRrhhImpuestoRenta the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhImpuestoRenta::findOne(['id_sys_rrhh_impuesto_renta' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
