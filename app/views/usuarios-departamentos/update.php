<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmUsuariosDep */

$this->title = 'Usuarios Departamentos ';
$this->params['breadcrumbs'][] = ['label' => 'Usuarios Departamentos', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sys_adm_usuarios_dep, 'url' => ['view', 'id' => $model->id_sys_adm_usuarios_dep]];
//$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sys-adm-usuarios-dep-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
