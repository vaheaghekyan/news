<?php
use yii\helpers\Url;
use yii\helpers\Html;
use backend\models\Language;
use common\components\Helpers as CommonHelpers;
$frontend_language_id_cookie = CommonHelpers::getCookie(\Yii::$app->params['frontend_language_id_cookie']);

$dropdown = Html::dropDownList ('language', $frontend_language_id_cookie, Language::dropDownActiveLanguages(true), ['class'=>'form-control', 'id'=>'change_language', 'prompt'=>'']);
$dropdown = str_replace("\n", "", $dropdown);
$dropdown = str_replace("\"", "'", $dropdown);
?>

<script>
$(document).on('change','#change_language',function(){
    window.location="<?= Url::to(['/site/change-language']) ?>?language="+$(this).val();
});

/*
* show bootbox dialog to choose edition
*/
function bootboxDialogEdition()
{
    $.ajax(
    {
        url: "<?= Url::to(['/story/edition', "nativeLanguage"=>true])?>",
        type: "POST",
        dataType: "json",
        success: function(data)
        {
            bootbox.dialog(
            {
                title: "<?= Yii::t('app', 'Settings')?>",
                message: "<div class='snap-drawer snap-drawer-right'>"+
                      "<p class='sidebar-divider'><b><?= Yii::t('app', 'Language')?></b></p>"+
                      "<div class='container no-bottom' style='padding-left:0px'>"+
                        "<div class='sidebar-form no-bottom'><?= $dropdown ?>"+
                        "</div>"+
                      "</div>"+
                      "<p class='sidebar-divider'><b><?= Yii::t('app', 'Edition')?></b></p>"+
                      "<p style='padding-top: 0; padding-left: 0; padding-right: 0;'><a style='padding-left: 0; padding-right: 0;' class='btn btn-block btn-primary' href='javascript:showhideEditions();'><i class='fa fa-globe'></i><em> <?= Yii::t('app', 'Change edition')?></em></a></p>"+
                    "<div id='editions' style='display: none;'>"+data.result+"</div>",
                buttons: {
                    success:
                    {
                        label: "<?= Yii::t('app','Save'); ?>",
                        className: "btn-success",
                        callback: function ()
                        {
                            //save that edition
                            if($('input[name="edition[]"]:checked').length <= 0) {
                                  swal({
                                      title: "Ooops ...",
                                      text: "<?= Yii::t('app','Select at least one edition!'); ?>",
                                      type: "warning",
                                      confirmButtonText: "OK" });
                            }
                            else
                                $("#change_edition_form").submit();
                           /* $.ajax(
                            {
                                url: "<?= Url::to(['/site/change-edition'])?>",
                                type: "POST",
                                data:
                                dataType: "json",
                                success: function(data)
                                {

                                }
                            });*/
                        }
                    },
                    danger:
                    {
                        label: "<?= Yii::t('app','Cancel'); ?>",
                        className: "btn-danger",
                        callback: function ()
                        {

                        }
                    }

                }
            });
        }

    });

}

/*
* Show sweet alert to choose email adress to send story to
*/
function shareEmail(title, url) {
    swal({
        title: "Share via Email",
        text: "Email:",
        type: "input",
        inputType: "email",
        showCancelButton: true,
        closeOnConfirm: false,
        animation: "slide-from-top",
        inputPlaceholder: "Email" },
        function(inputValue) {
            $.ajax(
            {
                url: "<?= Url::to(['/story/shareemail']) ?>",
                data: ({email: inputValue, title: title, url: url}),
                type: "POST",
                dataType: "json",
                success: function() {}

            });
            swal("", "", "success");
    });
}
</script>