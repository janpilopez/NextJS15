<?php

use app\models\SysRrhhPermisos;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;
use app\controllers\EmpresasController;
use app\models\SysEmpresa;
use app\models\SysAdmAreas;
use app\models\SysAdmDepartamentos;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SysAdmUsuariosDepSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Usuarios Permisos';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-adm-usuarios-per-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Agregar', ['registrar'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
           // 'id_sys_adm_usuarios_dep',
            // 'id_usuario'
         
            [
                'label'=>'Tipo Usuario',
                'attribute'=>'usuario_tipo',
                'value'=> function($model){
                    $tipos = Yii::$app->params['tipo_usuarios'];
                    return $tipos[$model->usuario_tipo];
                } ,
             ],
             [
                'label'=>'Permiso',
                'attribute'=>'permiso',
                'value'=> function($model){
                    //$tipos = Yii::$app->params['tipo_usuarios'];
                    //return $tipos[$model->usuario_tipo];
                    if($model->permiso != null):
                
                        $permiso  = SysRrhhPermisos::find()->where(['id_sys_rrhh_permiso'=> $model->permiso])->one();
                
                        if($permiso):
                        
                           return $permiso->permiso;
                        else:
                            return 's/n';
                        endif;
                    else:
                       return "Todos";
                    endif;
                } ,
             ],
             [
                 'label'=>'Estado',
                 'attribute'=>'estado',
                 'value'=> function($model){
                         return $model->estado == 'A' ? 'Activo': 'Inactivo';
                         //SysAdmUsuariosDep
                 } ,
                 ],
             
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
