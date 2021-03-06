<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Новое поступление';
$this->params['breadcrumbs'][] = $this->title;

app\modules\shop\assets\CreateIncomingAsset::register($this);
\app\modules\shop\assets\BackendAsset::register($this);
?>

<div class="incoming-create">

    <?php if(Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success" role="alert">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>

    <?php $form = ActiveForm::begin(); ?>
        <div class="form-group">
            <input class="new-input" data-info-service="<?=Url::toRoute(['/shop/product/product-info']);?>" type="text" value="" placeholder="Код или артикул + Enter" style="width: 300px;" />
        </div>
        <div id="incoming-list" style="width: 800px;">
        </div>

        <div class="form-group">
            <?= Html::submitButton('Добавить поступление', ['class' => 'btn btn-success']) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
