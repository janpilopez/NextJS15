<?php

use app\models\SysAdmAreas;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\SysAdmDepartamentos;
use app\models\SysDocumento;
use app\models\SysGrupoAutorizacion;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search\SysDocumentoAutorizacionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Autorización de Documentos';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-documento-autorizacion-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Nuevo', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                
                'label'=> 'Documento',
                'filter'=> ArrayHelper::map(SysDocumento::find()->all(), 'id', 'documento'),
                'attribute'=>'id_sys_documento',
                'format' => 'raw',
                'value'=> function($model){
                    
                      $documento = SysDocumento::find()->where(['id'=> $model->id_sys_documento])->one();
                      
                      if($documento):
                       return   $documento->documento;
                      else:
                        return  'S/D';
                      endif;
                } ,
             ],
            [
                
                'label'=> 'Grupo Autorización',
                'filter'=> ArrayHelper::map(SysGrupoAutorizacion::find()->all(), 'id', 'nombre'),
                'attribute'=>'id_grupo_autorizacion',
                'format' => 'raw',
                'value'=> function($model){
                $grupo  =  SysGrupoAutorizacion::find()->where(['id'=>$model->id_grupo_autorizacion])->one();
                    if($grupo):
                        return $grupo->nombre;
                    endif;
                } ,
            ],
            
            [
                'label'=>'Area',
                'attribute'=>'area',
                'format' => 'raw',
                'value'=> function($model){
        
                    $area = SysAdmAreas::find()->where(['id_sys_adm_area'=> $model->id_sys_area])->one();
                    
                    if($area):
                         return $area->area;
                    else:
                         return 'Todos'; 
                    endif;
                
                }
             ],
             [
                 'label'=>'Departamento',
                 'attribute'=>'Departamento',
                 'format' => 'raw',
                 'value'=> function($model){
                 
                 $departamento = SysAdmDepartamentos::find()->where(['id_sys_adm_departamento'=> $model->id_sys_departamento])->one();
                 
                 if($departamento):
                     return $departamento->departamento;
                 else:
                    return 'Todos';
                 endif;
                 
                 }
             ],
            //'usuario_transaccion',
            //'fecha_transaccion',
            //'estado',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
