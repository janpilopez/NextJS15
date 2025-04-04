<?php

namespace app\controllers;

use Yii;
use app\models\SysEmpresa;
use app\models\Search\SysEmpresaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
/**
 * EmpresasController implements the CRUD actions for SysEmpresa model.
 */
class EmpresasController extends Controller
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
     * Lists all SysEmpresa models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysEmpresaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Displays a single SysEmpresa model.
     * @param string $id
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
     * Creates a new SysEmpresa model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysEmpresa();
        
      

        if ($model->load(Yii::$app->request->post())) {
           
            $model->razon_social    =strtoupper($model->razon_social);
           $model->file       = UploadedFile::getInstance($model, 'file');
            $model->cred       = UploadedFile::getInstance($model, 'cred');
            
              if(!is_null($model->file)){
                        
                        $ruta = Yii::getAlias('@webroot').'/logo/'.$model->ruc;
                        if (!file_exists($ruta)) {
                            mkdir($ruta, 0777, true);
                        }
                   $model->file->saveAs($ruta.'/'.$model->file->baseName.'.'.$model->file->extension);
                        
                 }
                 
            
                if(!is_null($model->cred)){
                    
                    $model->cred->saveAs($ruta.'/'.$model->cred->baseName.'.'.$model->cred->extension);
                    $model->credencial    = $model->cred->baseName.'.'.$model->cred->extension;
                }
                
              
                
            
                $transaction  = \Yii::$app->db->beginTransaction();
                
                try {
                    
                    
                    if ($model->save(false)) {
                        
                        $transaction->commit();
                        
                        return $this->redirect(['index']);
                        
                        
                    } 
                    
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    echo   $e->getMessage();
                }
                
                
            }   
           
       
        

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SysEmpresa model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            
            
            $model->file       = UploadedFile::getInstance($model, 'file');
            $model->cred       = UploadedFile::getInstance($model, 'cred');
            $model->razon_social    =strtoupper($model->razon_social);
           
            $ruta = Yii::getAlias('@webroot').'/logo/'.$model->ruc;
            if (!file_exists($ruta)) {
                mkdir($ruta, 0777, true);
            }
            
            
            
            if(!is_null($model->cred)){
                
                $model->cred->saveAs($ruta.'/'.$model->cred->baseName.'.'.$model->cred->extension);
                $model->credencial    = $model->cred->baseName.'.'.$model->cred->extension;
            }
            
            if(!is_null($model->file)){
                $model->file->saveAs($ruta.'/'.$model->file->baseName.'.'.$model->file->extension);
                $ruta = Yii::getAlias('@webroot').'/logo/'.$model->ruc;
                $model->logo          = $model->file->baseName.'.'.$model->file->extension;
            }
            
            try {
                
                
                if ($model->save(false)) {
                    
                    return $this->redirect(['index']);
                    
                    
                }  else {
                    
                    var_dump ($model->getErrors()); die();
                }
                
            } catch (\Exception $e) {
                
                return  $e->getMessage();
            }
            
        
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SysEmpresa model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id);

        return $this->redirect(['index']);
    }
    /**
     * Finds the SysEmpresa model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return SysEmpresa the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysEmpresa::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
