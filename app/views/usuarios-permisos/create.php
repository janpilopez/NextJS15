<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmUsuariosDep */

$this->title = 'Usuarios Permisos';
$this->params['breadcrumbs'][] = ['label' => 'Usuarios Permisos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-adm-usuarios-per-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
