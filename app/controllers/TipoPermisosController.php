<?php

namespace app\controllers;

use Yii;
use app\models\SysRrhhPermisos;
use app\models\SysRrhhPermisosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TipoPermisosController implements the CRUD actions for SysRrhhPermisos model.
 */
class TipoPermisosController extends Controller
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
     * Lists all SysRrhhPermisos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhPermisosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhPermisos model.
     * @param string $id_sys_rrhh_permiso
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
     * Creates a new SysRrhhPermisos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysRrhhPermisos();
        $model->subcidio = 0;

        if ($model->load(Yii::$app->request->post())) {
            
            
            $objModel = SysRrhhPermisos::findOne(['id_sys_rrhh_permiso' => $model->id_sys_rrhh_permiso]);
            
            if (!$objModel):
            
                $model->id_sys_empresa                = '001';
                $model->transaccion_usuario           = Yii::$app->user->username;
                
                if($model->save(false)):
                    
                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'success','duration' => 1500,
                        'icon' => 'glyphicons glyphicons-robot','message' => '¡Los datos se han registrado  con éxito!',
                        'positonY' => 'top','positonX' => 'right']);
                
                else:
                    
                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'danger','duration' => 1500,
                        'icon' => 'glyphicons glyphicons-robot','message' => '¡Ha ocurrido un error. Comuniquese con su administrador! ',
                        'positonY' => 'top','positonX' => 'right']);
                endif;
                
             else:
                 
                 Yii::$app->getSession()->setFlash('info', [
                 'type' => 'warning','duration' => 1500,
                 'icon' => 'glyphicons glyphicons-robot','message' => '¡El código del permiso ya se encuentra registrado!',
                 'positonY' => 'top','positonX' => 'right']);
                 
             endif;
             
          return $this->redirect('index');
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SysRrhhPermisos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_rrhh_permiso
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

    /**
     * Deletes an existing SysRrhhPermisos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_sys_rrhh_permiso
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
     * Finds the SysRrhhPermisos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_sys_rrhh_permiso
     * @param string $id_sys_empresa
     * @return SysRrhhPermisos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhPermisos::findOne(['id_sys_rrhh_permiso' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
