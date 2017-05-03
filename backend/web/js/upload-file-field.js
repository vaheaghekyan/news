/**
 * Created by Oleksii on 16.06.2015.
 */
$.fn.uploadFileField = function( config ) {

    var cfg = {
            name : "upload_file",
            cls  : "uploadFileCnt",
            onSelect : function(){}
        },
        inputFile,
        selectedFile,
        targetInput = $(this),
        textField,
        selectFileMessage;
    cfg         = $.extend( cfg, config );
    inputFile   = $('<input type="file" name="' + cfg.name + '">');
    inputFile.css({
        visibility  : 'hidden'
    });
    inputFile.on('change', function( event ){

        console.log('File path changed to ' + event.target.files);
        selectedFile = event.target.files;
        selectFile();

    });
    targetInput.after(inputFile);
    targetInput.parent().addClass( cfg.cls );
    targetInput.parent().css({
        position : 'relative'
    });
    textField = targetInput.parents("tr").find("input[type=text]");
    targetInput.parents("tr").find("input[type!=file]").click(function(){

         inputFile.click();

    });

    function selectFile()
    {
        if ( !selectFileMessage ) {

           // selectFileMessage = $('<div class="selected-file">You have selected file for uploading</div>');
           // targetInput.after(selectFileMessage);
            textField.val("You have selected file for uploading");

        }
        cfg.onSelect();

    }
    return {
        getInput : function()
        {
            return inputFile;
        },
        reset : function(){

            selectFileMessage = false;
            textField.val("");
            inputFile.val("");

        }
    };
}