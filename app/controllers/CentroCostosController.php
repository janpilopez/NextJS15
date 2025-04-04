<?php

namespace app\controllers;

use Yii;
use app\models\SysAdmCcostos;
use app\models\Search\SysAdmCcostosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CentroCostosController implements the CRUD actions for SysAdmCcostos model.
 */
class CentroCostosController extends Controller
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
     * Lists all SysAdmCcostos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysAdmCcostosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysAdmCcostos model.
     * @param string $id_sys_adm_ccosto
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

    /**
     * Creates a new SysAdmCcostos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysAdmCcostos();

        if ($model->load(Yii::$app->request->post())) {
          
            
            $model->id_sys_empresa = '001';
            
            $objccosto = $this->ValidaCentroCostos($model->id_sys_adm_ccosto, '001');
            
            if(!$objccosto):
            
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
            
            else:
                
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'warning','duration' => 1500,
                'icon' => 'glyphicons glyphicons-robot','message' => 'El Centro de costos no pudo ser resgistrado porque ya existe! ',
                'positonY' => 'top','positonX' => 'right']);
            
            endif;
            
            return $this->redirect('index');
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SysAdmCcostos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_adm_ccosto
     * @param string $id_sys_empresa
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
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    private function ValidaCentroCostos($codigo){
        
        
      return  SysAdmCcostos::find()->where(['id_sys_adm_ccosto' => $codigo])->one();
        
    }
    
    
    
    /**
     * Deletes an existing SysAdmCcostos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_sys_adm_ccosto
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
       $model = $this->findModel($id);
       $model->estado = 'I';
       $model->save(false);

        return $this->redirect(['index']);
    }

    /**
     * Finds the SysAdmCcostos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_sys_adm_ccosto
     * @param string $id_sys_empresa
     * @return SysAdmCcostos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysAdmCcostos::findOne(['id_sys_adm_ccosto' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
