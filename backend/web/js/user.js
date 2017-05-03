/**
 * Created by Oleksii on 15.06.2015.
 */

var userModule = (function() {

    var cfg = {
            editUrl        : "",
            deleteUrl      : "",
            getUrl         : "",
            languages      : ""
         },
        dataTable,
        dataFilter = {

        },
        filterAuthors           = $("#filter-authors"),
        filterKeywords          = $("#filter-keywords"),//From Application , From Database
        filterRoles             = $("#filter-roles"),
        newUserLink             = $("#new-user"),
        languagesSelectorId     = "#languages-selector",
        selectName              = "UserForm[languages][]",
        profileImgCntId         = "#profile-image",
        profileImgCnt,
        languagesSelector,
        deleteModal,
        uploadFile;


    function createForm( btn, title )
    {
        var form = new ModalBootstrap({
            winAttrs: {
                class               : 'modal edit',
                tabindex            : '-1',
                role                : 'dialog',
                'aria-labelledby'   : '',
                'aria-hidden'       : 'true'
            },
            btnAttrs: {},
            title   : title,
            body    : '<form class="user-dialog">'+
            '<input type="text" name="UserForm[name]" class="form-control" placeholder="Name" />' +
            '<input name="UserForm[id]" type="hidden">'+
            '<input name="UserForm[oldEmail]" type="hidden" />'+
            '<input type="text" name="UserForm[email]" class="form-control" placeholder="Email" />'+
            '<input type="password" name="UserForm[password]" class="form-control" placeholder="Password" />' +
            '<select name="UserForm[role]" class="form-control">'+
            '<option value="">Role</option><option value="'+ ROLE_EDITOR +'">Editor</option>'+
            '<option value="' + ROLE_SENIOREDITOR + '">Senior Editor</option>'+
            '<option value="' + ROLE_ADMIN + '">Admin</option>' +
            '<option value="' + ROLE_SUPERADMIN + '">Super Admin</option>'+
            '<option value="' + ROLE_MARKETER + '">Marketer</option>'+
            '</select>' +
            '<table id="create" class="table">'+
            '<tr><td colspan="2" id="profile-image"></td></tr>'+
            '<tr><td><input type="button" value="Browse" class="btn btn-primary"></td>'+
            '<td><input type="text" id="upload-file-1"></td></tr></table>' +
            '<input type="checkbox" id="selectall"/> <b>Select All</b>'+
            '<div id="languages-selector"></div>' +
            '</form>',
            buttons: [
                {class: 'btn-success save', text: btn},
                {class: 'btn-primary cancel', text: 'Cancel'}
            ]
        });

        form.getWin().find(".modal-header").hide();
        return form;
    }

    function actionEdit( userId )
    {
       // document.location.href = cfg.storyEditUrl + "?storyId=" + storyId;
        $.ajax({
            url         : cfg.getUrl,
            method      : 'GET',
            dataType    : 'json',
            data        : { id: userId },
            success: function ( response )
            {

                var form = createForm('Save', 'Edit user #' + userId),
                    languagesSelector = form.getWin().find(languagesSelectorId),
                    selected,
                    option,
                    profileImgCnt = form.getWin().find( profileImgCntId );

                uploadFile = form.getWin().find("#upload-file-1").uploadFileField({
                    name : 'image'
                });

               for ( var i=0; i < cfg.languages.length; i++ )
                {

                    option = $('<div class="option"><input value="'+cfg.languages[i]['id'] + '" type="checkbox" name="'+selectName+'" class="checkbox_languages" >' + cfg.languages[i]['name'] + '</div>');

                    for ( var j=0; j < response.languages.length; j++) {

                        if ( response.languages[j] == cfg.languages[i]['id'] ) {

                            option.find("input").attr('checked', 'checked');
                            break;

                        }
                    }
                    languagesSelector.append( option );

                }
                 if ( response.role == ROLE_SUPERADMIN ) {

                    languagesSelector.find("input").attr("checked", "checked");
                    languagesSelector.find("input").attr('disabled', true);

                } else {

                    languagesSelector.find("input").attr('disabled', false);

                }
               for ( var i in response ) {

                    form.getWin().find("[name='UserForm[" + i + "]']").val( response[i] );

                }
                if ( response.image ) {

                    profileImgCnt.append('<img src="' + response.image + '?r='+Math.random()+'" height=50 >');

                } else {

                    profileImgCnt.html('');


                }
                form.show();
                form.getWin().find("button[class*=cancel]").click(function () {

                    form.getWin().modal("hide");

                });

                //check all language checboxes, all at once
                form.getWin().find("#selectall").change(function(){
                    $(".checkbox_languages").prop('checked', $(this).prop("checked"));
                });

                form.getWin().find("button[class*=save]").click(function (element)
                {

                    var saveButton=$(this);  //save button for this modal popup
                    if ( response.role == ROLE_SUPERADMIN ) {

                        languagesSelector.find("input").attr('disabled', false);

                    }
                    var formData = new FormData();
                    var other_data = form.getWin().find("form").serializeArray();
                    $.each(other_data,function(key, input){
                        formData.append(input.name, input.value);
                    });

                    if ( uploadFile.getInput().get(0)['files'][0] ) {

                        formData.append("image", uploadFile.getInput().get(0)['files'][0]);

                    }


                    $.ajax({
                        url     : cfg.editUrl,
                        method  : 'POST',
                        data    : formData,
                        dataType: 'json',
                        contentType : false,
                        processData : false,
                        beforeSend: function()
                        {
                            //disable button so they cannot do multiple submit
                            saveButton.prop("disabled",true);
                        },
                        success : function ( response )
                        {

                            if ( response.errors )
                            {

                                //if there was an error, enable buttons
                                saveButton.prop("disabled",false);
                                sweetAlert("Oops...", "There was an error, contact us or try again", "error"); //SweetAlert script in "js" folder
                                /*if ( response.role == ROLE_SUPERADMIN ) {

                                    languagesSelector.find("input").attr('disabled', false);

                                }

                                var frm         = form.getWin().find("form"),
                                    errorCnt    = frm.parents('div').find(".errors");

                                if ( errorCnt.length == 0 ) {

                                    errorCnt = $('<div class="errors"></div>');
                                    frm.before(errorCnt);

                                }
                                errorCnt.html("");
                                errorCnt.append(response.errors.join("<br>"));
                                  /*/
                            }
                            else
                            {
                                sweetAlert(":)", "Everything was fine, page will reload", "success"); //SweetAlert script in "js" folder
                                location.reload();
                               /* form.getWin().modal("hide");
                                dataTable.api().ajax.reload();
                                $("#title").before('<div id="blue" class="section group pright"><div class="col span_5_of_5">User account has been succesfully saved.</div></div>');*/

                            }
                            //

                        }
                    });


                });

            }
        });

    }

    function actionDelete( id )
    {

        function deleteRequest()
        {

            var params = {
                data    : { userId : id},
                dataType: 'json',
                success : function ( response ) {

                    dataTable.api().ajax.reload();

                }
            };
            params['url']       = cfg.deleteUrl;
            params['method']    = "DELETE";
            $.ajax( params );

        }

        deleteModal = new ModalBootstrap({
            title: 'Are you sure you want to delete this user?',
            buttons: [
                {class: 'btn-primary yes', text: 'Yes'},
                {class: 'btn-primary cancel', text: 'Cancel'}
            ]
        });
        deleteModal.show();
        deleteModal.getWin().find("button[class*=yes]").click(function () {
            deleteRequest();
        });

    }

    return {

        init: function( config ){


            cfg = $.extend(cfg, config);
            $('.edit_user_icon').click(function(){

                var userId = $(this).data('userid');
                actionEdit( userId );
            });

            newUserLink.click(function()
            {

                var form = createForm('Create', 'Add new user'),
                    languagesSelector = form.getWin().find(languagesSelectorId),
                    selected,
                    option;

                form.show();

                uploadFile = form.getWin().find("#upload-file-1").uploadFileField({
                    name : 'image'
                });
                for ( var i=0; i < cfg.languages.length; i++ ) {

                    option = $('<div class="option"><input value="'+cfg.languages[i]['id'] + '" type="checkbox" name="'+selectName+'"  class="checkbox_languages">&nbsp;' + cfg.languages[i]['name'] + '</div>');
                    languagesSelector.append( option );

                }

                form.getWin().find("button[class*=cancel]").click(function () {

                    form.getWin().modal("hide");

                });


                //check all language checboxes, all at once
                form.getWin().find("#selectall").change(function(){
                    $(".checkbox_languages").prop('checked', $(this).prop("checked"));
                });

                form.getWin().find("button[class*=save]").click(function (element)
                {
                    var saveButton=$(this); //save button for this modal popup
                    var formData = new FormData();
                    var other_data = form.getWin().find("form").serializeArray();
                    $.each(other_data,function(key, input){
                        formData.append(input.name, input.value);
                    });

                    if ( uploadFile.getInput().get(0)['files'][0] ) {

                        formData.append("image", uploadFile.getInput().get(0)['files'][0]);

                    }

                    $.ajax({
                        url     : cfg.createUrl,
                        method  : 'POST',
                        data    : formData,
                        dataType: 'json',
                        contentType: false,
                        processData: false,
                        beforeSend: function()
                        {
                            //disable button so they cannot do multiple submit
                            saveButton.prop("disabled",true);
                        },
                        success : function ( response )
                        {

                            if ( response.errors )
                            {
                                //if there was an error, enable buttons
                                saveButton.prop("disabled",false);
                                sweetAlert("Oops...", "There was an error, contact us or try again", "error"); //SweetAlert script in "js" folder
                               /* var frm         = form.getWin().find("form"),
                                    errorCnt    = frm.parents('div').find(".errors");

                                if ( errorCnt.length == 0 ) {

                                    errorCnt = $('<div class="errors"></div>');
                                    frm.before(errorCnt);

                                }
                                errorCnt.html("");
                                errorCnt.append(response.errors.join("<br>")); */

                            }
                            else
                            {
                                sweetAlert(":)", "Everything was fine, page will reload", "success");  //SweetAlert script in "js" folder
                                location.reload();
                               /* form.getWin().modal("hide");
                                dataTable.api().ajax.reload();

                                $("#title").before('<div id="blue" class="section group pright"><div class="col span_5_of_5">User account has been succesfully created.</div></div>');    */

                            }
                            //

                        }
                    });


                });

            });

        }
    };

})();