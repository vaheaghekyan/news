/**
 * Created by Oleksii on 12.06.2015.
 */
var countryModule = (function(){

    var deleteModal,
        countries = {},
        cfg = {
            isOrdering  : true,
            deleteUrl   : '',
            orderUrl    : '',
            addUrl      : '',
            orderContinentUrl   : '',
            countriesUrl        : ''
        };

    function deleteCountry ( element ) {


        var parent          = element.parents("tr"),
            categoryName    = parent.find(".country-name"),
            numStories      = parent.find('.number-stories');

        if ( parseInt( numStories.text() ) > 0 ) {

            var alert = new ModalBootstrap({
                title   : 'Delete Country',
                body    : 'Country cannot be deleted. Please, move the stories first.',
                buttons: [
                    {class: 'btn-primary delete', text: 'Ok'}
                ]
            });
            alert.show();
            return false;
        }
        deleteModal = new ModalBootstrap({
            title: 'Delete ' + categoryName.text(),
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
                data    : {id : parent.attr("_id")},
                success : function () {

                    parent.remove();

                }
            });

        });

    }

    function addCountryForm( element )
    {
        var parent          = element.parents("li"),
            continentId     = parent.attr("_id"),
            form = $('<form><input type="hidden" name="id" value="' + continentId + '" />' +
         '<div class="countries-list"></div>' +
         '<input type="submit" value="Add" /></form>'),
            countriesList   = form.find(".countries-list"),
            existingCountries   = parent.find("#countries");

        if ( parent.find("form").length > 0 ) {

            return false;

        }

        var cExist, exitForm;

        for ( var i=0; i < countries[continentId].length; i++ ) {

            cExist = false;
            existingCountries.find("td[class=country-name]").each(function(){
                if ( $(this).text() ==countries[continentId][i] ) {

                    cExist = true;
                    return false;

                }
            });
            if ( cExist == false ) {

                exitForm = true;
                countriesList.append('<div class="row">' +
                '<div class="col-xs-10">' + countries[continentId][i] + '</div>' +
                '<div class="col-xs-2"><input type="checkbox" name="country[]" value="' + countries[continentId][i] + '" /></div>');


            }

        }
        if ( exitForm == true ) {

           // existingCountries.fadeOut();
            element.before(form);
            element.hide();


        } else {

            new ModalBootstrap({
                title   : 'Alert',
                body    : 'There is no new contries to add.',
                buttons: [
                    {class: 'btn-primary delete', text: 'Ok'}
                ]
            }).show();

            return false;

        }
        form.submit( function() {

             $.ajax({
                 url     : cfg.addUrl,
                 method  : 'POST',
                 data    : form.serialize(),
                 dataType    : 'json',
                 success : function ( response )  {

                     var listBlock = parent.find("#countries"),
                         row;
                     form.remove();
                     element.show();
                     //existingCountries.fadeIn();

                     for ( var i=0; i < response.length; i++ ) {

                         row = $('<tr _id="' + response[i].id + '">' +
                         '<td width="60%" class="country-name">' + response[i]['name'] + '</td>' +
                         '<td width="20%" class="center"><a href="#" class="number-stories">0</a></td>' +
                         '<td width="20%" class="center"><img class="delete-action" src="/img/icons/deleteicon.png"></td>' +
                         '</tr>');
                         listBlock.append( row );

                         row.find(".delete-action").click(function(){

                             deleteCountry( $(this) );

                         });

                     }

                 }
             });
             return false;

         });
    }
    return {

        init: function( config ) {

            cfg = $.extend(cfg, config);

            if ( cfg.isOrdering == true ) {


                $("#sortable").sortable({
                    stop: function (event, ui) {

                        var cells = $("#sortable li[_id]"),
                            ids = [],
                            el,
                            j = 0;

                        $("#sortable .clear").remove();
                        cells.each(function () {

                            el = $(this);
                            j++;
                            ids.push(el.attr("_id"));

                            if (j == 3) {

                                el.after('<div class="clear"></div>');
                                j = 0;

                            }


                        });
                        $.ajax({
                            url: cfg.orderContinentUrl,
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
                }).disableSelection();

            }

            $(".add-action").click(function(){

                var el              = $(this),
                    parent          = el.parents("li"),
                    continentId     = parent.attr("_id");

                if ( !countries[continentId] ) {

                    $.ajax({
                        url     : cfg.countriesUrl,
                        method  : 'GET',
                        dataType: 'json',
                        data    : { continentId : continentId},
                        success : function ( response ) {

                            countries[continentId] = response;
                            addCountryForm( el );

                        }
                    })

                } else {

                    addCountryForm( el );

                }

            });

            $(".delete-action").click(function(){

                deleteCountry( $(this) );

            });

        }

    };

})();