/**
 * Created by Oleksii on 16.06.2015.
 */
var createStoryModule = (function()
{

    var progress_bar_upload_image=$(".progress_bar_upload_image");

    var uploadFile1,
        uploadFile2,
        cfg           = {
            //wwCountryId             : null,
            storyId                 : null,
            storyPublishUrl         : null,
            storySchedulePublishUrl : null,
            datePublished           : null,
            storyGetUrl             : '/story/view',
            mode                    : null
        },
        countriesCnt  = ".countries-list",
        publishBtn    = ".publish",
        scheduleBtn   = ".schedule",
        categoriesSelector = "input[name='category[]']:checked",
        loadingBg       = 'loading-bg',
        previewBtn      = '#preview-btn',
        form            = "#create-event-form";


    function getSelectedCategories()
    {

        var list = $( categoriesSelector),
            data = [];
        list.each(function(){
            data.push( $(this).val() );
        });
        return data;

    }

    function actionPreview( storyId )
    {
        var params = {
            url     : cfg.storyGetUrl,
            method  : 'POST',
            data    : {story_id : storyId},
            dataType:"json",
            success : function ( data )
            {
                //console.log(data.result);
                bootboxPreviewStory(data.result);

            }
        };
        $.ajax( params );
    }

    //before form submit check if all required fields are filled
    //http://ctrlq.org/code/19226-missing-required-fields-on-form-submit
    function checkRequiredFields()
    {
        var fields = $(':input[required]').serializeArray();
        var return_true_false=true;

        $.each(fields, function(i, field)
        {
            if (!field.value)
            {
               swal("Oops...", "Fill all required fields first.", "error");
               return_true_false=false;
            }
        });
        return return_true_false;
    }

    /*
    *  check if there is <script type="text/javascript"></script> inside clipkit code, it mustn't be there because I'm calling clipkit js code on bottom of page
    */
    function clipkitScriptTag()
    {
        var js_clipkit_story = $(".js_clipkit_story");
        if(js_clipkit_story.length > 0)
        {
            var str = js_clipkit_story.val();
            var n = str.search("script");
            // </script> exists
            if(n > -1)
            {
                swal("Oops...", "Remove <script>...</script> from Clipkit code textarea", "error");
                return false;
            }

        }
        return true;
    }

    /*
    *  check image name, it should't be special chars, only alphanumberic, ".", "_" and "-"
    */
    function imageNameCheck(string)
    {
        //http://stackoverflow.com/questions/336210/regular-expression-for-alphanumeric-and-underscores
        var res = string.match(/^[a-z0-9._ -]+$/i);
        if(res==null)
        {
            swal("Oops...", "Only English alphabet, numbers, space, '.', '-' and '_' is allowed for image/video name", "error");
            return false;
        }
        else
            return true;
    }

    /*
    *  check if user want to schedule story in past
    */
    function checkScheduleDate()
    {
        var date_published_input=$(".date_published");
        if(date_published_input.val()!="")
        {
            var now=$("#server_time").val();   // server time
            var now=Date.parse(now);   //parse it so it becomes unix timestamp

            var schedule_date=new Date(date_published_input.val()); // date from text input
            var schedule_date=Date.parse(schedule_date); //parse it so it becomes unix timestamp

            if(schedule_date < now)
            {
                swal("Oops...", "You cannot publish story in the past", "error");
                return false;
            }
            else
                return true;
        }
    }

    return {

        init: function( config ){

            cfg = $.extend( cfg, config );
            countriesCnt    = $( countriesCnt );
            publishBtn      = $( publishBtn );
            scheduleBtn     = $( scheduleBtn );
            loadingBg       = $('<div class="' + loadingBg + '" style="display: none;"><div>Please wait...</div></div>');
            previewBtn      = $( previewBtn );
            finishBtn      = $("#save-story");
            form            = $( form );
            var action  = form.attr("action");
            if ( action.search('mode=preview') !== -1 ) {

                form.attr("action", action.replace("&mode=preview", ""));

            }

            if ( cfg.mode == 'preview' && cfg.storyId ) {

                actionPreview( cfg.storyId );

            }

            $("#save-story").click(function()
            {
                //if this is clipkit story
                return clipkitScriptTag();
            });



            //clicking on FINISH button
            finishBtn.click(function(e)
            {
                //--------------CHECKING STUFF BEFORE SUBMITTING--------------
                if(checkScheduleDate()==false)
                    e.preventDefault();

                //stop interval of autosaving
                clearInterval(autoSaveStory);

                //if this is clipkit story check for <script> tag
                //check for required fields
                //check for image name
                if($(".js_clipkit_story").length > 0)
                {
                    if(checkRequiredFields()==false || clipkitScriptTag()==false)
                        e.preventDefault();
                }
                else if($("input[name=image_name]").length > 0)
                {
                    var image_name=$("input[name=image_name]").val();
                    if(checkRequiredFields()==false || imageNameCheck(image_name)==false)
                        e.preventDefault();
                }

            });

            //clicking on PREVIEW button
            previewBtn.click(function(e)
            {
                //stop interval of autosaving
                clearInterval(autoSaveStory);

                var action  = form.attr("action");

                if ( action.search('mode=preview') === -1 ) {

                    if (action.search(/\?/) === -1)
                    {

                        action += "?mode=preview";
                        form.attr("action", action);

                    }
                    else
                    {

                        action += "&mode=preview";
                        form.attr("action", action);


                    }
                }
                form.attr("action", action);

                //--------------CHECKING STUFF BEFORE SUBMITTING--------------
                if(checkScheduleDate()==false)
                    e.preventDefault();
                //if this is clipkit story check for <script> tag
                //check for required fields
                //check for image name
                if($(".js_clipkit_story").length > 0)
                {
                    if(checkRequiredFields()==true && clipkitScriptTag()==true)
                        form.submit();
                }
                else if($("input[name=image_name]").length > 0)
                {
                    var image_name=$("input[name=image_name]").val();
                    if(checkRequiredFields()==true && imageNameCheck(image_name)==true)
                        form.submit();
                }
                else
                    form.submit();

                return false;

            });

            $('html, body').append( loadingBg );
            /*countriesCnt.find("input").click(function()
            {

                if ( countriesCnt.find("input:checked").length == 0 ) {

                    countriesCnt.find("input[value="+cfg.wwCountryId+"]").click();

                }

            });*/
          /*  publishBtn.click(function(){

                var params = {
                    url     : cfg.storyPublishUrl,
                    method  : 'PUT',
                    data    : {storyId : cfg.storyId, categories: getSelectedCategories() },
                    dataType: 'json',
                    success : function ( response ) {

                        if ( response.date ) {

                            $("#published-date").text( response.date );
                            var modal = new ModalBootstrap({
                                title: 'Message',
                                body: 'Your story has been successfully published.',
                                buttons: [{class : 'btn-primary confirm', text : 'Ok'}]
                            });
                            modal.show();

                        } else {

                            var modal = new ModalBootstrap({
                                title: 'Alert',
                                body: 'Sorry, you can not publish this story. Please check all information. At least one category should be assigned.',
                                buttons: [{class : 'btn-primary confirm', text : 'Ok'}]
                            });
                            modal.show();

                        }

                    }
                };
                $.ajax( params );

            }); */

            var params = {
                autoclose: true,
                format: 'yyyy-mm-dd',
                startDate: '0d'
            };
            if ( scheduleBtn.datepicker ) {

                scheduleBtn.datepicker(params).on('changeDate', function (e) {
                    var date = "";
                    if (e.date) {

                        var month = String(e.date.getMonth() + 1);
                        if (month.length == 1) {

                            month = "0" + month;
                        }
                        var day = String(e.date.getDate());
                        if (day.length == 1) {

                            day = "0" + day;

                        }
                        date = e.date.getFullYear() + "-" + month + "-" + day;

                    }

                    var params = {
                        url: cfg.storySchedulePublishUrl,
                        method: 'PUT',
                        data: {storyId: cfg.storyId, date: date, categories: getSelectedCategories()},
                        dataType: 'json',
                        success: function (response) {

                            if (response.date) {

                                $("#published-date").text(response.date);
                                var modal = new ModalBootstrap({
                                    title: 'Message',
                                    body: 'Your story will be published on ' + response.date,
                                    buttons: [{class : 'btn-primary confirm', text : 'Ok'}]
                                });
                                modal.show();

                            } else {

                                var modal = new ModalBootstrap({
                                    title: 'Alert',
                                    body: 'Sorry, you can not publish this story. Please check all information. At least one category should be assigned.',
                                    buttons: [{
                                        class: 'btn-primary confirm', text: 'OK'
                                    }]
                                });
                                modal.show();

                            }

                        }
                    };
                    $.ajax(params);

                });
                if (cfg.datePublished) {

                    scheduleBtn.datepicker('update', cfg.datePublished);

                }

            }

            uploadFile1 = $("#upload-file-1").uploadFileField({
                name : 'image',
                onSelect: function(){
                    loadingBg.fadeIn();
                    //console.log( "test" );
                    //console.log( uploadFile1.getInput().val() );
                    var formData = new FormData();
                    formData.append("file", uploadFile1.getInput()[0].files[0]);

                    $.ajax({
                        url     : '/story/upload-image',
                        type    : 'POST',
                        dataType: 'json',
                        data    : formData,
                        enctype : 'multipart/form-data',
                        processData: false,  // tell jQuery not to process the data
                        contentType: false,  // tell jQuery not to set contentType
                        //upload progress bar http://www.dave-bond.com/blog/2010/01/JQuery-ajax-progress-HMTL5/
                        xhr: function()
                        {
                            var xhr = new window.XMLHttpRequest();
                            //Upload progress
                            xhr.upload.addEventListener("progress", function(evt)
                            {
                              if (evt.lengthComputable)
                              {
                                var percentComplete = evt.loaded / evt.total;
                                //Do something with upload progress
                                var percentVal = Math.round(percentComplete)*100 + '%'; //*100 because progress bar goes from 0 to 1, but in css from 0 to 100%
                                progress_bar_upload_image.css("width",percentVal);
                                progress_bar_upload_image.text(percentVal);
                              }
                            }, false);
                            //Download progress
                           /* xhr.addEventListener("progress", function(evt)
                            {
                              if (evt.lengthComputable)
                              {
                                var percentComplete = evt.loaded / evt.total;
                                //Do something with download progress
                                console.log(percentComplete);
                              }
                            }, false);*/
                            return xhr;
                        },
                        success : function( response )
                        {
                            uploadFile1.getInput().parent().find(".help-block-error").text( "" );
                            loadingBg.fadeOut();
                            if ( response.success )
                            {

                                var modal = new ModalBootstrap({
                                    title : 'Crop Picture',
                                    body  : '<div class="crop-aria"><img class="crop-img" src="' + response.fileName + '"></div>'
                                });
                                modal.show();

                                //560 - 445
                                var k = 1;
                               // if ( response.width > response.height ) {

                                    k = 560/response.width;

                                //}
                                var cropper = $('.crop-aria img').cropper({
                                    aspectRatio: 960 / 762,
                                    autoCropArea: 0.65,
                                    minCropBoxWidth: 960 * k,
                                    minCropBoxHeight: 762 * k,
                                    strict: true,
                                    guides: false,
                                    highlight: false,
                                    dragCrop: false,
                                    cropBoxMovable: true,
                                    cropBoxResizable: true,
                                    zoomable: false,
                                    built: function()
                                    {

                                    }
                                });

                                //cancel cropping picture and delete temp image
                                modal.getWin().find("button[class*=cancel]").click(function ()
                                {
                                   //reset progress bar to 0
                                    progress_bar_upload_image.css("width",0);
                                    progress_bar_upload_image.text(0);

                                    uploadFile1.reset();
                                    $.ajax({
                                        url   : '/story/delete-temp',
                                        type  : 'POST',
                                        data  : {fileName : response.fileName},
                                        success : function ( response ) {

                                            console.log('Temp file deleted');
                                            console.log( response );

                                        }
                                    });


                                });
                                modal.getWin().find("button[class*=confirm]").click(function () {

                                    var imagedata   = uploadFile1.getInput().parent().find("input[name=imagedata]"),
                                        cropData    = cropper.cropper("getData", true);
                                    if ( imagedata.length == 0) {

                                        imagedata = $('<input type="hidden" name="imagedata">');
                                        uploadFile1.getInput().after( imagedata );

                                    }
                                    imagedata.val( response.fileName + ";" + cropData['x'] + ";" + cropData['y'] + ";" + cropData['width'] + ";" + cropData['height']);

                                });

                            }
                            else
                            {
                                //reset progress bar to 0
                                progress_bar_upload_image.css("width",0);
                                progress_bar_upload_image.text(0);

                                uploadFile1.reset();
                                uploadFile1.getInput().parent().find(".help-block-error").text( response.error );

                            }

                        },
                        error : function( response ){

                            loadingBg.fadeOut();
                            console.log("RESPONSE fail");
                            console.log( response );

                        }
                    });

                }
            });
            uploadFile2 = $("#upload-file-2").uploadFileField({
                name : 'video'
            });


        }

    }

})();