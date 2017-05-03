<?php
use yii\helpers\Url;
?>
<script>
$(document).ready(function()
{
    $('select[name=language]').change(function(){

        document.location.href = '<?=Url::to(['/site/change-language'])?>?languageCode=' + $(this).val() + "&route=<?=Yii::$app->controller->getRoute()?>";

    });
});


//FUNCTIONS

//when user wants to preview a story call this modal popup
function bootboxPreviewStory(message)
{
    bootbox.dialog(
    {
        title: "<?= Yii::t('app', 'Story preview') ?>",
        message: message,
        onEscape:true,
        backdrop:true,
        buttons: {
            success: {
                label: "OK",
                className: "btn-success",
                //callback: function () { }
            }
        }
    });
}

/*
* on modal popup(for now) there is textarea to report story
*/
function reportStory(storyid)
{
    var message=$("#report_msg");
    if(message.val()=="")
    {
        swal("Oops...", "Enter message", "error");
        return false;
    }

    $.ajax(
    {
        url : '<?= Url::to(["/ajax/report-story"])?>',
        type: "POST",
        data : {storyid:storyid, message:message.val()},
        dataType : "json",
        success:function(data)
        {
            if(data.message=="true")
            {
                swal("Good job!", "<?= Yii::t('app', 'Everything went fine') ?>", "success");
                message.val("");
            }
            else
                swal("Oops...", "<?= Yii::t('app', 'Something was wrong') ?>", "error");
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            swal("Oops...", "<?= Yii::t('app', 'Something was wrong') ?>", "error");
        }
    });
}

//------------SERVER TIME---------------
//http://www.javascriptkit.com/script/script2/servertime.shtml
var currenttime = '<?= date("F d, Y H:i:s", time())?>'; //PHP method of getting server date

///////////Stop editting here/////////////////////////////////

var montharray=new Array("<?= Yii::t('app', 'January')?>","<?= Yii::t('app', 'February')?>","<?= Yii::t('app', 'March')?>","<?= Yii::t('app', 'April')?>","<?= Yii::t('app', 'May')?>","<?= Yii::t('app', 'Jun')?>e","<?= Yii::t('app', 'July')?>","<?= Yii::t('app', 'August')?>","<?= Yii::t('app', 'September')?>","<?= Yii::t('app', 'October')?>","<?= Yii::t('app', 'November')?>","<?= Yii::t('app', 'December')?>");
var serverdate=new Date(currenttime);

function padlength(what)
{
    var output=(what.toString().length==1)? "0"+what : what
    return output
}

function displaytime()
{
    serverdate.setSeconds(serverdate.getSeconds()+1);
    var datestring=montharray[serverdate.getMonth()]+" "+padlength(serverdate.getDate())+", "+serverdate.getFullYear();
    var timestring=padlength(serverdate.getHours())+":"+padlength(serverdate.getMinutes())+":"+padlength(serverdate.getSeconds());
    $(".servertime").text(datestring+" "+timestring);
}

window.onload=function()
{
    setInterval("displaytime()", 1000);
}

</script>