<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmUsuariosDep */

$this->title = $model->id_sys_adm_usuarios_dep;
$this->params['breadcrumbs'][] = ['label' => 'Sys Adm Usuarios Deps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-adm-usuarios-dep-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id_sys_adm_usuarios_dep], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id_sys_adm_usuarios_dep], [
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
            'id_sys_adm_usuarios_dep',
            'id_usuario',
            'area',
            'departamento',
            'id_sys_empresa',
        ],
    ]) ?>

</div>
