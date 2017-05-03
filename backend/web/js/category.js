/**
 * Created by Oleksii on 12.06.2015.
 */
var categoryModule = (function(){

    var SELECTOR_ADD_CATEGORY = "#add-new-category",
        createCategoryHtml,
        createCategoryForm,
        createCategoryWin,
        deleteModal,
        cfg = {
            isOrdering  : true,
            createUrl   : "",
            orderUrl    : "",
            updateUrl   : "",
            deleteUrl   : "",
            createSubUrl: "",
            deleteSubUrl: ""
        };

    newCategoryHtml = '<div id="countryfilter" style="display: none"  class="section group pright"><div class="col span_1_of_3"><div id="category" class="gpad">' +
    '<form method="post"><h2><div class="bginputtext noborder"><input type="text" class="form-control" required="required" name="category" placeholder="CATEGORY NAME" /></div></h2>' +
    '<table id="tcountries" width="100%" cellpadding="0" cellspacing="0"><tr><td><strong>Subcategories</strong></td>' +
    '</tr><tr><td><div class="bginputtext noborder"><input type="text" name="subcategory" class="form-control" required="required" placeholder="Subcategory name" /></div></td></tr>' +
    '</table><input type="submit" value="Create"></form></div></div></div>';

    function editSubcategory( element )
    {

        var parent          = element.parents("tr"),
            categoryName    = parent.find(".category-name"),
            form            = $('<form><input type="hidden" name="id" value="' + parent.attr("_id") + '" />' +
            '<div class="bginputtext noborder">'+
            '<input type="text" name="name" class="form-control" value="' + categoryName.text() + '" />'+
            '</div>' +
            '<input type="submit" value="Save" class="btn btn-sm btn-success" /></form>');

        if ( categoryName.find("form").length > 0 ) {

            return false;

        }

        categoryName.html("");
        categoryName.append( form );
        form.submit( function() {

            var data    = form.serializeArray(),
                cat     = data[1]['value'].replace(/\s/g, "");
            if ( cat.length == 0 ) {

                var alert = new ModalBootstrap({
                    title   : 'Alert',
                    body    : 'Please fill in the subcategory field its name.',
                    buttons: [
                        {class: 'btn-primary delete', text: 'Ok'}
                    ]
                });
                alert.show();
                return false;

            }

            $.ajax({
                url     : cfg.updateUrl,
                method  : 'POST',
                data    : data,
                success : function () {

                    var name =  form.find("input[name=name]").val();
                    form.remove();
                    categoryName.text(  name );

                }
            });
            return false;

        });

    }

    function deleteSubcategory( element )
    {


        var parent          = element.parents("tr"),
            categoryName    = parent.find(".category-name"),
            numStories      = parent.find('.number-stories');

        if ( parseInt( numStories.text() ) > 0 ) {

            var alert = new ModalBootstrap({
                title   : 'Alert',
                body    : 'Category cannot be deleted. Please, move the stories first.',
                buttons: [
                    {class: 'btn-primary delete', text: 'Ok'}
                ]
            });
            alert.show();
            return false;
        }
        deleteModal = new ModalBootstrap({
            title: 'Delete ' + categoryName.text(),
            body : 'Are you sure you want to delete this subcategory?',
            buttons: [
                {class: 'btn-primary delete', text: 'Delete'},
                {class: 'btn-primary cancel', text: 'Cancel'}
            ]
        });
        deleteModal.show();
        deleteModal.getWin().find("button[class*=delete]").click(function () {

            $.ajax({
                url     : cfg.deleteSubUrl,
                method  : 'DELETE',
                data    : {id : parent.attr("_id")},
                success : function () {

                    parent.remove();
                    //document.location.reload();

                }
            });

        });


    }

    return {

        init: function( config ) {

            cfg = $.extend(cfg, config);

            $(SELECTOR_ADD_CATEGORY).click(function () {

                categoryModule.showCreateCategoryForm();

            });

            if (cfg.isOrdering == true) {

                $("#sortable").sortable({
                stop: function (event, ui) {

                    var cells = $("#sortable li[_id]"),
                        ids = [],
                        el,
                        key = 1,
                        j = 0;

                    $("#sortable .clear").remove();
                    cells.each(function () {

                        el = $(this);
                        el.find(".ordernumber").text(key);
                        key++;
                        j++;
                        ids.push(el.attr("_id"));

                        if (j == 3) {

                            el.after('<div class="clear"></div>');
                            j = 0;

                        }


                    });
                    $.ajax({
                        url: cfg.orderUrl,
                        method: 'POST',
                        data: {ids: ids.join(",")}
                    });


                }
            });
            var fixHelper = function (e, ui) {
                ui.children().each(function () {
                    $(this).width($(this).width());
                });
                return ui;
            };

            $("#countries tbody").sortable({
                helper: fixHelper,
                stop: function (event, ui) {

                    var mainItem = ui.item,
                        cells = mainItem.parents("#countries").find("tr"),
                        ids = [],
                        el,
                        key = 1,
                        j = 0;

                    cells.each(function () {

                        el = $(this);
                        key++;
                        j++;
                        ids.push(el.attr("_id"));


                    });
                    $.ajax({
                        url: cfg.orderUrl,
                        method: 'POST',
                        data: {ids: ids.join(",")}
                    });


                }
            });

        }

            $(".edit-action").click(function(){
                var el = $(this),
                    parent = el.parents(".parent-category"),
                    form = $('<form><input type="hidden" name="id" value="' + el.data("id") + '" />' +
                    '<div class="bginputtext noborder"><input type="text" name="name" class="form-control" value="' + parent.find('h2').text() + '" /></div>' +
                    '<input type="submit" value="Save" /></form>');
                parent.slideUp();
                parent.after( form );
                form.submit( function() {

                    var data    = form.serializeArray(),
                        cat     = data[1]['value'].replace(/\s/g, "");
                    console.log( data );
                    console.log( cat);
                    if ( cat.length == 0 ) {

                        var alert = new ModalBootstrap({
                            title   : 'Alert',
                            body    : 'Please fill in the category field its name.',
                            buttons: [
                                {class: 'btn-primary delete', text: 'Ok'}
                            ]
                        });
                        alert.show();
                        return false;

                    }

                    $.ajax({
                        url     : cfg.updateUrl,
                        method  : 'POST',
                        data    : data,
                        success : function () {

                            parent.find('h2').text( form.find("input[name=name]").val() );
                            parent.slideDown();
                            form.remove();

                        }
                    });
                    return false;

                });

            });
            $(".delete-action").click(function(){

                var deleteBtn = $(this),
                links = deleteBtn.parents("li").find('.number-stories'),
                canDelete = true;
                links.each (function(){

                    var number = parseInt( $(this).text() );
                    if ( number > 0 ) {

                        var alert = new ModalBootstrap({
                            title   : 'Alert',
                            body    : 'Category cannot be deleted. Please, move the stories first.',
                            buttons: [
                                {class: 'btn-primary delete', text: 'Ok'}
                            ]
                        });
                        alert.show();
                        canDelete = false;
                        return false;
                    }

                });
                if ( canDelete == true ) {

                    deleteModal = new ModalBootstrap({
                        title: 'Delete ' + deleteBtn.parents(".parent-category").find('h2').text(),
                        body : 'Are you sure you want to delete this category?',
                        buttons: [
                            {class: 'btn-primary delete', text: 'Delete'},
                            {class: 'btn-primary cancel', text: 'Cancel'}
                        ]
                    });
                    deleteModal.show();
                    deleteModal.getWin().find("button[class*=delete]").click(function () {

                        $.ajax({
                            url     : cfg.deleteUrl,
                            method  : 'DELETE',
                            data    : {id : deleteBtn.parents("li").attr("_id")},
                            success : function () {

                                document.location.reload();

                            }
                        });

                    });

                }

            });
            $(".add-subcategory-action").click(function()
            {
                var el = $(this),
                    categoryid = el.data("categoryid"),
                    form = $('<form><input type="hidden" name="id" value="' +categoryid+ '" />' +
                    '<div class="bginputtext noborder"><input type="text" name="name" class="form-control" placeholder="Subcategory Name" value="" /></div>' +
                    '<input type="submit" value="Create" class="btn btn-sm btn-primary submit_sub_category" /></form>');

                //disable this button

                (form).insertAfter(el);
                el.hide();
                form.submit( function()
                {
                    $(".submit_sub_category").prop('disabled', true); 

                    $.ajax({
                        url     : cfg.createSubUrl,
                        method  : 'POST',
                        data    : form.serialize(),
                        dataType    : 'json',
                        success : function ( response )
                        {

                            //just reload
                            location.reload();
                            /*var listBlock = parent.find("#countries"),
                                row = $('<tr _id="' + response.id + '">' +
                                '<td width="60%" class="category-name">' + form.find('input[name=name]').val()+ '</td>' +
                                '<td width="20%" class="center"><a href="#" class="number-stories">0</a></td>' +
                                '<td width="20%" class="center"><img class="edit-sub-action" src="/img/icons/editicon.png"><img class="delete-sub-action" src="/img/icons/deleteicon.png"></td>' +
                                '</tr>');
                            listBlock.append( row );
                            //parent.find('h2').text( form.find("input[name=name]").val() );
                            el.show();
                            form.remove();
                            row.find(".edit-sub-action").click(function(){

                                editSubcategory( $(this) );

                            });
                            row.find(".delete-sub-action").click(function(){

                                deleteSubcategory( $(this) );

                            }); */

                        }
                    });
                    return false;

                });

            });

            $(".edit-sub-action").click(function(){

                editSubcategory( $(this) );

            });
            $(".delete-sub-action").click(function(){

                deleteSubcategory( $(this) );

            });

        },
        showCreateCategoryForm: function () {

            if ( !createCategoryWin ) {

                createCategoryWin  = $( newCategoryHtml );
                $("#title").after( createCategoryWin );
                createCategoryForm = createCategoryWin.find("form");
                createCategoryForm.submit( function() {

                    var data    = createCategoryForm.serializeArray(),
                        cat     = data[0]['value'].replace(/\s/g, ""),
                        subcat  = data[1]['value'].replace(/\s/g, "");
                    if ( cat.length == 0 || subcat.length == 0 ) {

                        var alert = new ModalBootstrap({
                            title   : 'Alert',
                            body    : 'Please fill in the category and subcategory fields their names.',
                            buttons: [
                                {class: 'btn-primary delete', text: 'Ok'}
                            ]
                        });
                        alert.show();
                        return false;

                    }
                    $.post(cfg.createUrl, data, function(  response ){

                        document.location.reload();

                    });
                    return false;

                });

            }
            createCategoryWin.fadeIn();

        }

    };

})();