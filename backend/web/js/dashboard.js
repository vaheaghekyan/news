/**
 * Created by Oleksii on 09.06.2015.
 */
var dashboardModule = (function(){

    var cfg =
        {
            subcategories       : [],
            storyEditUrl        : "",
            storyUnpublishUrl   : "",
            storyPublishUrl     : "",
            storySchedulePublishUrl : "",
            storyDeleteUrl      : "",
            storyApproveUrl     : "",
            statusPending       : '',
            statusApproved      : '',
            statusUnpublished   : '',
            storyGetUrl         : '/story/preview'
        },
        dataTable,
        dataTableApproved,
        dataTableUnpublished,
        dataFilter,
        dataFilterApproved,
        dataFilterUnpublished,
        filterCategories        = $("#filter-categories"),
        filterSubCategories     = $("#filter-subcategories"),
        filterAuthors           = $("#filter-authors"),
        filterKeywords          = $("#filter-keywords"),//From Application , From Database
        filterStatuses          = $("#filter-statuses"),
        resetFilterBtn          = $("#reset-filter-btn"),
        filterBtn               = $("#filter-btn"),
        pendingGrid             = $(".pending-grid"),
        unpublishedGrid         = $(".unpublished-grid"),
        approvedGrid            = $(".approved-grid"),
        deleteModal;

    function refreshSubcategoriesFilter( categoryId )
    {

        filterSubCategories.empty();
        filterSubCategories.append('<option value="">Subcategory</option>');
        for ( var i=0; i < cfg.subcategories.length; i++ ) {

            if ( cfg.subcategories[i]['parent_id'] == categoryId ) {

                filterSubCategories.append('<option value="'+cfg.subcategories[i]['id']+'">' + cfg.subcategories[i]['name'] + '</option>');

            }

        }

    }

    function actionPreview( storyId )
    {

        var params = {
            url     : cfg.storyGetUrl,
            method  : 'GET',
            data    : {storyId : storyId},
            success : function ( response ) {

                var modal = new ModalBootstrap({
                    title : false,
                    buttons : false,
                    winAttrs : {
                        class : 'modal preview-modal'
                    },
                    body : response
                });
                modal.show();


            }
        };
        $.ajax( params );
    }

    function actionEdit( storyId )
    {
        document.location.href = cfg.storyEditUrl + "?storyId=" + storyId;
    }

    function actionDelete( storyId, storyName, dataTable, dataTable2, deleteImmediately )
    {

        function deleteRequest( type )
        {
            //console.log(" deleteRequest type : " + type + " storyId " + storyId);
            var params = {
                data    : {storyId : storyId},
                dataType: 'json',
                success : function ( response ) {

                    dataTable.api().ajax.reload();
                    if ( dataTable2 && type == "application" ) {

                        dataTable2.api().ajax.reload();

                    }

                }
            };
            if ( type == "application" )
            {

                params['url']       = cfg.storyUnpublishUrl;
                params['method']    = "PUT";

            }
            else
            {

                params['url']       = cfg.storyDeleteUrl;
                params['method']    = "DELETE";

            }
            $.ajax( params );

        }

        if ( deleteImmediately )
        {

            deleteRequest("database");

        }
        else
        {

            deleteModal = new ModalBootstrap({
                title: 'Delete ' + storyName,
                winAttrs : { class : 'modal delete'},
                buttons: [
                    {class: 'btn-primary application', text: 'Unpublish Story'},
                    {class: 'btn-primary database', text: 'From Database'}
                ]
            });
            deleteModal.show();
            deleteModal.getWin().find("button[class*=application]").click(function () {
                deleteRequest("application");
            });
            deleteModal.getWin().find("button[class*=database]").click(function () {
                deleteRequest("database");
            });

        }

    }

    function actionApprove( storyId, dataTable1, dataTable2 )
    {
        var params =
        {
            url     : cfg.storyApproveUrl,
            method  : 'PUT',
            data    : {storyId : storyId},
            dataType: 'json',
            success : function ( response )
            {

                dataTable1.api().ajax.reload();
                dataTable2.api().ajax.reload();


            }
        };
        $.ajax( params );
    }


    function applyFilter()
    {
        var categoryId      = filterCategories.val(),
            subCategoryId   = filterSubCategories.val(),
            userId          = filterAuthors.val(),
            keyword         = filterKeywords.val();


        dataFilter['categoryId']    = categoryId;
        dataFilter['subCategoryId'] = subCategoryId;
        dataFilter['userId']        = userId;
        dataFilter['keyword']       = keyword;

        dataTable.api().ajax.reload();

        dataFilterApproved['categoryId']    = categoryId;
        dataFilterApproved['subCategoryId'] = subCategoryId;
        dataFilterApproved['userId']        = userId;
        dataFilterApproved['keyword']       = keyword;

        dataTableApproved.api().ajax.reload();

        dataFilterUnpublished['categoryId']    = categoryId;
        dataFilterUnpublished['subCategoryId'] = subCategoryId;
        dataFilterUnpublished['userId']        = userId;
        dataFilterUnpublished['keyword']       = keyword;

        dataTableUnpublished.api().ajax.reload();

        //Apply statuses filter
        if ( filterStatuses.val() == cfg.statusPending )
        {

            approvedGrid.slideUp();
            unpublishedGrid.slideUp();
            if ( pendingGrid.css("display") == "none" )
            {

                pendingGrid.slideDown();

            }

        }
        else if ( filterStatuses.val() == cfg.statusApproved )
        {

            pendingGrid.slideUp();
            unpublishedGrid.slideUp();
            if ( approvedGrid.css("display") == "none" )
            {

                approvedGrid.slideDown();

            }

        }
        else if ( filterStatuses.val() == cfg.statusUnpublished )
        {

            pendingGrid.slideUp();
            approvedGrid.slideUp();
            if ( unpublishedGrid.css("display") == "none" )
            {

                unpublishedGrid.slideDown();

            }

        }

    }

    function resetFilter()
    {

        filterCategories.val("");
        filterSubCategories.val("");
        filterAuthors.val("");
        filterKeywords.val("");

        dataFilter = {
            status: cfg.statusPending
        };
        dataFilterApproved = {
            status: cfg.statusApproved
        };
        dataFilterUnpublished = {
            status: cfg.statusUnpublished
        };

        dataTable.api().ajax.reload();
        dataTableApproved.api().ajax.reload();
        dataTableUnpublished.api().ajax.reload();
        if ( pendingGrid.css("display") == "none" ) {

            pendingGrid.slideDown();

        }
        if ( approvedGrid.css("display") == "none" ) {

            approvedGrid.slideDown();

        }
        if ( unpublishedGrid.css("display") == "none" ) {

            unpublishedGrid.slideDown();

        }

    }

    function categoryCanNotBeDeleted() {

        var alert = new ModalBootstrap({
            title   : 'Alert',
            body    : 'Please link the story to some category first.',
            buttons: [
                {class: 'btn-primary delete', text: 'Ok'}
            ]
        });
        alert.show();
        return false;

    }

    function actionPublish( storyId, dataTable1 )
    {

        var params = {
            url     : cfg.storyPublishUrl,
            method  : 'PUT',
            data    : {storyId : storyId},
            dataType: 'json',
            success : function ( response ) {

                dataTable1.api().ajax.reload();

            }
        };
        $.ajax( params );

    }

    function actionSchedulePublishing( storyId, date, dataTable1, dataTable2 )
    {


        var params =
        {
            url     : cfg.storySchedulePublishUrl,
            method  : 'PUT',
            data    : {storyId : storyId, date : date},
            dataType: 'json',
            success : function ( response )
            {

                dataTable1.api().ajax.reload();
                if ( dataTable2 )
                {

                    dataTable2.api().ajax.reload();

                }

            }
        };
        $.ajax( params );

    }

    return {

        init: function( config ) {
            cfg = $.extend(cfg, config);
            dataFilter = {
                status: cfg.statusPending
            };
            dataFilterApproved = {
                status: cfg.statusApproved
            };
            dataFilterUnpublished = {
                status: cfg.statusUnpublished
            };
            dataTable = $('#dashboard-table').dataTable({
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": false,
                "bSort": true,
                "pageLength": 100,
                "bInfo": false,
                "bAutoWidth": false,
                "order": [[ 6, "desc" ]],
                "columnDefs":
                [

                    {
                        "targets"   : 0,
                        "orderable" : false,
                        "render"    : function (data, type, row) {

                            return '<span style="display: none">' + data + '</span>';

                        }
                    },
                    {
                        "targets"   : 1,
                        "orderable" : false
                    },
                    {
                        "targets"   : 2,
                        "orderable" : false
                    },
                    {
                        "targets"   : 3,
                        "orderable" : false,
                        "render"    : function (data, type, row) {
                            return '<a href="' + data + '" class="external-link" target="_blank">' + data + '</a>';
                        }
                    },
                    {
                        "targets"   : 4,
                        "orderable" : false
                    },
                    {
                        "targets"   : 5,
                        "orderable" : false
                    },
                    {
                        "targets"   : 5,
                        "orderable" : false,
                        "render"    : function (data, type, row) {

                            var icons = [];
                            icons.push('<img class="media-icon' + ( data ? ' active' : "") + '" src="/img/icons/hdicon.png"  />');
                            icons.push('<img class="media-icon' + ( row[8] ? ' active' : "") + '" src="/img/icons/videoicon.png" />');

                            return '<div class="media">' + icons.join(" ") + '</div>';

                        }
                    },
                    {
                        "targets"   : 6,
                        "orderable" : true,
                        "render"    : function (data, type, row) {

                            if ( data ) {

                                return '<div class="date">' + data + '</div>';

                            }
                            return '<div class="date">' + row[7] + '</div>';

                        }

                    },
                    {
                        "targets"   : 7,
                        "orderable" : false,
                        "render"    : function (data, type, row) {

                            var icons = [];
                            icons.push('<img class="action-icon preview" src="/img/icons/preview_icon.png">');
                            if ( USER_ROLE == ROLE_SUPERADMIN ||
                                 USER_ROLE == ROLE_ADMIN ||
                                 USER_ROLE == ROLE_SENIOREDITOR ) {

                                icons.push('<img class="action-icon edit" src="/img/icons/editicon.png">');
                                icons.push('<img class="action-icon delete" src="/img/icons/deleteicon.png">');
                                icons.push('<img class="action-icon publish" src="/img/icons/publishnowicon.png">');
                                if (row[9]) {

                                    icons.push('<img class="action-icon schedule" date="' + row[9] + '" src="/img/icons/scheduleicon.png">');

                                } else {

                                    icons.push('<img class="action-icon schedule" src="/img/icons/scheduleicon.png">');

                                }


                            } else if ( USER_ROLE == ROLE_EDITOR && USER_ID == row[10]) {

                                icons.push('<img class="action-icon edit" src="/img/icons/editicon.png">');

                            }
                            return '<div class="actions">' + icons.join(" ") + '</div>';

                        }
                    }

                ],
                "fnDrawCallback": function (oSettings, oData) {

                    if ( oSettings._iRecordsTotal ) {

                        $("#number-of-pending-stories").text( oSettings._iRecordsTotal );

                    } else {

                        $("#number-of-pending-stories").text( 0 );

                    }

                },
                "ajax": {
                    "url"   :  '/story/find',
                    "data"  : function( data, settings ) {
                        for (var i in dataFilter) {

                            data[i] = dataFilter[i];

                        }
                    }
                },
                "processing": true,
                "serverSide": true
            });

            dataTableApproved = $('#dashboard-table-approved').dataTable({
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": false,
                "bSort": true,
                "pageLength": 100,
                "bInfo": false,
                "bAutoWidth": false,
                "order": [[ 6, "desc" ]],
                "columnDefs": [

                    {
                        "targets"   : 0,
                        "orderable" : false,
                        "render"    : function (data, type, row) {

                            return '<span style="display: none">' + data + '</span>';

                        }
                    },
                    {
                        "targets"   : 1,
                        "orderable" : false
                    },
                    {
                        "targets"   : 2,
                        "orderable" : false
                    },
                    {
                        "targets"   : 3,
                        "orderable" : false,
                        "render"    : function (data, type, row) {
                            return '<a href="' + data + '" class="external-link" target="_blank">' + data + '</a>';
                        }
                    },
                    {
                        "targets"   : 4,
                        "orderable" : false
                    },

                    {
                        "targets"   : 5,
                        "orderable" : false,
                        "render"    : function (data, type, row) {

                            var icons = [];
                            icons.push('<img class="media-icon' + ( data ? ' active' : "") + '" src="/img/icons/hdicon.png"  />');
                            icons.push('<img class="media-icon' + ( row[8] ? ' active' : "") + '" src="/img/icons/videoicon.png" />');

                            return '<div class="media">' + icons.join(" ") + '</div>';

                        }
                    },
                    {
                        "targets"   : 6,
                        "orderable" : true,
                        "render"    : function (data, type, row) {

                            if ( data ) {

                                return '<div class="date">' + data + '</div>';

                            }
                            return '<div class="date">' + row[7] + '</div>';

                        }

                    },
                    {
                        "targets"   : 7,
                        "orderable" : false,
                        "render"    : function (data, type, row) {

                            var icons = [];
                            icons.push('<img class="action-icon preview" src="/img/icons/preview_icon.png">');
                            if ( USER_ROLE == ROLE_SUPERADMIN ||
                                USER_ROLE == ROLE_ADMIN ||
                                USER_ROLE == ROLE_SENIOREDITOR ) {

                                icons.push('<img class="action-icon edit" src="/img/icons/editicon.png">');
                                icons.push('<img class="action-icon delete" src="/img/icons/deleteicon.png">');
                                icons.push('<img class="action-icon publish" src="/img/icons/publishnowicon.png">');
                                if (row[9]) {

                                    icons.push('<img class="action-icon schedule" date="' + row[9] + '" src="/img/icons/scheduleicon.png">');

                                } else {

                                    icons.push('<img class="action-icon schedule" src="/img/icons/scheduleicon.png">');

                                }

                            }
                            return '<div class="actions">' + icons.join(" ") + '</div>';

                        }
                    }

                ],
                "ajax": {
                    "url"   :  '/story/find',
                    "data"  : function( data, settings ) {
                        for (var i in dataFilterApproved) {

                            data[i] = dataFilterApproved[i];

                        }
                    }
                },
                "fnDrawCallback": function (oSettings, oData) {

                    if ( oSettings._iRecordsTotal ) {

                        $("#number-of-approved-stories").text( oSettings._iRecordsTotal );

                    } else {

                        $("#number-of-approved-stories").text( 0 );

                    }

                },
                "processing": true,
                "serverSide": true
            });


            dataTableUnpublished = $('#dashboard-table-unpublished').dataTable({
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": false,
                "bSort": true,
                "pageLength": 100,
                "bInfo": false,
                "bAutoWidth": false,
                "order": [[ 6, "desc" ]],
                "columnDefs": [

                    {
                        "targets"   : 0,
                        "orderable" : false,
                        "render"    : function (data, type, row) {

                            return '<span style="display: none">' + data + '</span>';

                        }
                    },
                    {
                        "targets"   : 1,
                        "orderable" : false
                    },

                    {
                        "targets"   : 2,
                        "orderable" : false
                    },
                    {
                        "targets"   : 3,
                        "orderable" : false,
                        "render"    : function (data, type, row) {
                            return '<a href="' + data + '" class="external-link" target="_blank">' + data + '</a>';
                        }
                    },
                    {
                        "targets"   : 4,
                        "orderable" : false
                    },

                    {
                        "targets"   : 5,
                        "orderable" : false,
                        "render"    : function (data, type, row) {

                            var icons = [];
                            icons.push('<img class="media-icon' + ( data ? ' active' : "") + '" src="/img/icons/hdicon.png"  />');
                            icons.push('<img class="media-icon' + ( row[8] ? ' active' : "") + '" src="/img/icons/videoicon.png" />');

                            return '<div class="media">' + icons.join(" ") + '</div>';

                        }
                    },
                    {
                        "targets"   : 6,
                        "orderable" : true,
                        "render"    : function (data, type, row) {

                            if ( data ) {

                                return '<div class="date">' + data + '</div>';

                            }
                            return '<div class="date">' + row[7] + '</div>';

                        }

                    },
                    {
                        "targets"   : 7 ,
                        "orderable" : false,
                        "render"    : function (data, type, row) {

                            var icons = [];
                            icons.push('<img class="action-icon preview" src="/img/icons/preview_icon.png">');

                            if ( USER_ROLE == ROLE_SUPERADMIN ||
                                USER_ROLE == ROLE_ADMIN ||
                                USER_ROLE == ROLE_SENIOREDITOR ) {

                                icons.push('<img class="action-icon edit" src="/img/icons/editicon.png">');
                                icons.push('<img class="action-icon delete" src="/img/icons/deleteicon.png">');
                                icons.push('<img class="action-icon publish" src="/img/icons/publishnowicon.png">');
                                if (row[9]) {

                                    icons.push('<img class="action-icon schedule" date="' + row[9] + '" src="/img/icons/scheduleicon.png">');

                                } else {

                                    icons.push('<img class="action-icon schedule" src="/img/icons/scheduleicon.png">');

                                }

                            }
                            return '<div class="actions">' + icons.join(" ") + '</div>';

                        }
                    }

                ],
                "ajax": {
                    "url"   :  '/story/find',
                    "data"  : function( data, settings ) {
                        for (var i in dataFilterUnpublished ) {

                            data[i] = dataFilterUnpublished[i];

                        }
                    }
                },
                "fnDrawCallback": function (oSettings, oData) {

                    if ( oSettings._iRecordsTotal ) {

                        $("#number-of-unpublished-stories").text( oSettings._iRecordsTotal );

                    } else {

                        $("#number-of-unpublished-stories").text( 0 );

                    }

                },
                "processing": true,
                "serverSide": true
            });


            dataTable.on( 'draw.dt', function (e, settings, data)
            {

                dataTable.find("img[class*=preview]").click(function(){

                    var storyId = $(this).parents("tr").find("td").eq(0).text();
                    actionPreview( storyId );

                });
                dataTable.find("img[class*=edit]").click(function(){

                    var storyId = $(this).parents("tr").find("td").eq(0).text();
                    actionEdit( storyId );

                });
                dataTable.find("img[class*=delete]").click(function(){

                    var storyId     = $(this).parents("tr").find("td").eq(0).text(),
                        storyName   = $(this).parents("tr").find("td").eq(1).text();
                    actionDelete( storyId, storyName, dataTable, dataTableUnpublished );

                });
                dataTable.find("img[class*=publish]").click(function(){

                    var storyId     = $(this).parents("tr").find("td").eq(0).text(),
                        categories  = $(this).parents("tr").find("td").eq(2).text();

                    if ( categories.length == 0 ) {

                        categoryCanNotBeDeleted();

                    } else {

                        actionPublish( storyId, dataTable );

                    }


                });
                var list = dataTable.find("img[class*=schedule]");
                list.each( function(index, item) {

                    var storyId     = $(this).parents("tr").find("td").eq(0).text(),
                        categories  = $(this).parents("tr").find("td").eq(2).text();

                    if ( categories.length == 0 ) {

                        $(this).click(categoryCanNotBeDeleted);

                    } else {


                        var params = {
                            autoclose: true,
                            format: 'yyyy-mm-dd',
                            startDate: '0d'
                        };
                        item = $(item);
                        item.datepicker(params).on('changeDate', function (e) {
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

                            actionSchedulePublishing(storyId, date, dataTable, dataTableApproved);

                        });
                        if (item.attr("date")) {

                            item.datepicker('update', item.attr("date"));

                        }
                    }

                });

            });

            dataTableApproved.on( 'draw.dt', function (e, settings, data)
            {

                dataTableApproved.find("img[class*=preview]").click(function(){

                    var storyId = $(this).parents("tr").find("td").eq(0).text();
                    actionPreview( storyId );

                });
                dataTableApproved.find("img[class*=edit]").click(function(){

                    var storyId = $(this).parents("tr").find("td").eq(0).text();
                    actionEdit( storyId );

                });
                dataTableApproved.find("img[class*=delete]").click(function(){

                    var storyId     = $(this).parents("tr").find("td").eq(0).text(),
                        storyName   = $(this).parents("tr").find("td").eq(1).text();
                    actionDelete( storyId, storyName, dataTableApproved, dataTableUnpublished );

                });
                dataTableApproved.find("img[class*=publish]").click(function(){

                    var storyId     = $(this).parents("tr").find("td").eq(0).text(),
                        categories  = $(this).parents("tr").find("td").eq(2).text();

                    if ( categories.length == 0 ) {

                        categoryCanNotBeDeleted();

                    } else {

                        actionPublish( storyId, dataTableApproved );

                    }


                });
                var list = dataTableApproved.find("img[class*=schedule]");
                list.each( function(index, item) {

                    var storyId     = $(this).parents("tr").find("td").eq(0).text(),
                        categories  = $(this).parents("tr").find("td").eq(2).text();

                    if ( categories.length == 0 ) {

                        $(this).click(categoryCanNotBeDeleted);

                    } else {


                        var params = {
                            autoclose: true,
                            format: 'yyyy-mm-dd',
                            startDate: '0d'
                        };
                        item = $(item);
                        item.datepicker(params).on('changeDate', function (e) {
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

                            actionSchedulePublishing(storyId, date, dataTableApproved);

                        });
                        if (item.attr("date")) {

                            item.datepicker('update', item.attr("date"));

                        }
                    }

                });


            });

            dataTableUnpublished.on( 'draw.dt', function (e, settings, data)
            {

                dataTableUnpublished.find("img[class*=preview]").click(function(){

                    var storyId = $(this).parents("tr").find("td").eq(0).text();
                    actionPreview( storyId );

                });
                dataTableUnpublished.find("img[class*=edit]").click(function(){

                    var storyId = $(this).parents("tr").find("td").eq(0).text();
                    actionEdit( storyId );

                });
                dataTableUnpublished.find("img[class*=delete]").click(function(){

                    var storyId     = $(this).parents("tr").find("td").eq(0).text(),
                        storyName   = $(this).parents("tr").find("td").eq(1).text();
                    actionDelete( storyId, storyName, dataTableUnpublished, null, true );

                });
                dataTableUnpublished.find("img[class*=publish]").click(function(){

                    var storyId     = $(this).parents("tr").find("td").eq(0).text(),
                        categories  = $(this).parents("tr").find("td").eq(2).text();

                    if ( categories.length == 0 ) {

                        categoryCanNotBeDeleted();

                    } else {

                        actionPublish( storyId, dataTableUnpublished );

                    }

                });
                var list = dataTableUnpublished.find("img[class*=schedule]");
                list.each( function(index, item) {

                    var storyId     = $(this).parents("tr").find("td").eq(0).text(),
                        categories  = $(this).parents("tr").find("td").eq(2).text();

                    if ( categories.length == 0 ) {

                        $(this).click(categoryCanNotBeDeleted);

                    } else {

                        var params = {
                            autoclose: true,
                            format: 'yyyy-mm-dd',
                            startDate: '0d'
                        };
                        item = $(item);
                        item.datepicker(params).on('changeDate', function (e) {
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

                            actionSchedulePublishing(storyId, date, dataTableUnpublished, dataTableApproved);

                        });
                        if (item.attr("date")) {

                            item.datepicker('update', item.attr("date"));

                        }

                    }

                });

            });

            filterCategories.change(function(){

                refreshSubcategoriesFilter( $(this).val() );

            });
            filterBtn.click(function(){

                applyFilter();

            });
            resetFilterBtn.click(function(){

                resetFilter();

            });

        }
    };

})();