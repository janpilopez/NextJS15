<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SysRrhhCuadrillas */

$this->title = 'Registrar Grupos';
$this->params['breadcrumbs'][] = ['label' => 'Grupos Agendamiento', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sys-rrhh-cuadrillas-create">

  <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldet' => $modeldet,
        'update'=> $update,
        'esupdate'=> $esupdate,
    ]) ?>

</div>
