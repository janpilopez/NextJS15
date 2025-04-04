<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\SysRrhhPermisos;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmUsuariosDep */

$this->title = $model->id_sys_adm_usuarios_per;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios Permiso', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-adm-usuarios-dep-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id_sys_adm_usuarios_per], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id_sys_adm_usuarios_per], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_sys_adm_usuarios_per',
            [
                'attribute'=>'usuario_tipo',
                'value'=> function($model){ 
                    $tipos = Yii::$app->params['tipo_usuarios'];
                    return  $tipos[$model->usuario_tipo];
                } ,
            ], 
            [
                'attribute'=>'permiso',
                'value'=> function($model){ 
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
                'attribute'=>'estado',
                'value'=> function($model){ 
                    return  $model->estado == 'A' ? 'Activo': 'Inactivo';
                } ,
            ], 
        ],
    ]) ?>

</div>
