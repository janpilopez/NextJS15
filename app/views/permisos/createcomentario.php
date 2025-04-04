<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhPermisos */

$this->params['breadcrumbs'][] = ['label' => 'Permisos Empleados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-rrhh-permisos-create">

  <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formcomentario', [
        'model' => $model,
    ]) ?>

</div>
