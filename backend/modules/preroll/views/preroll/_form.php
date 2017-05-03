<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model backend\modules\preroll\models\AdsGeolocationTags */
/* @var $form yii\widgets\ActiveForm */

View::registerJsFile("@web/components/tokeninput/jquery.tokeninput.js", [View::POS_END]);
View::registerCssFile("@web/components/tokeninput/token-input.css", [View::POS_END]);
?>
<script type="text/javascript">
    $(document).ready(function() {
        $("#input-country").tokenInput('<?=Url::to(["/preroll/preroll/get-countries"])?>', {
            preventDuplicates:true,
            <?php
            if($model->isNewRecord==false)
            {
                echo "prePopulate: [";
                foreach($countries as $country)
                    echo '{id: '.$country["countryId"].', name: "'.$country["countryName"].'"},';
                echo "],";
            }
            ?>
        });
    });
</script>

<div class="ads-geolocation-tags-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tagName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tagUrl')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <input type="text" id="input-country" name="countries" required/>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
