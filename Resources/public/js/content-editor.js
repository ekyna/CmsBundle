;(function($) {

	$.fn.contentEditor = function(params) {

		params = $.extend({
			columnMinWidth: 2,
        }, params);

		this.each(function() {

			var editor = this;

			editor.setColumnWidth = function($column, width, fake) {
            	if (fake === undefined) fake = false;
                if (params.columnMinWidth <= width) {
                	$column.attr('class', $column.attr('class').replace(/col-sm-[0-9]+/, 'col-sm-' + width));
                	if (!fake) {
             		   $column.data('col', width);
                	}
                }
            };

			editor.fixColumnsWidth = function($row, fake) {
            	if (fake === undefined) fake = false;
            	$columns = $row.find('.cms-content-column').not('.ui-sortable-helper');
            	if ($columns.length > 0) {
                	var width = Math.floor(12 / $columns.length);
                	if (params.columnMinWidth <= width) {
                    	$columns.each(function() {
                        	if ($columns.length == 5 && ($(this).is(':first-child') || $(this).is(':last-child'))) {
                        		editor.setColumnWidth($(this), 3, fake);
                        	} else {
                        		editor.setColumnWidth($(this), width, fake);
                        	}
                        });
                	}
            	}
            };

			editor.fixColumnsResizable = function($row) {
            	$row.find('.cms-content-column').each(function() {
                	if ($(this).is(':first-child')) {
                		$(this).resizable( "disable" );
                	} else {
                		$(this).resizable( "enable" );
                	}
                });
            };

			editor.restoreRowColumns = function($row) {
            	$row.find('.cms-content-column').each(function() {
            		editor.setColumnWidth($(this), $(this).data('col'));
                });
            };

            editor.initColumn = function($column) {
                var gridX = $(editor).find('.cms-content-row').eq(0).width() / 12;
                $column.resizable({
                    handles: 'w',
                    ghost: true,
                    grid: [ gridX, 0 ],
                    stop: function(event, ui) {
                        var $row = ui.element.parent();
                        var $targColumn = ui.element;
                        var $prevColumn = $targColumn.prev();

                        var diff = Math.round((ui.size.width - ui.originalSize.width) / ($row.width() / 12));
                        var newTargWidth = $targColumn.data('col') + diff;
                        var newPrevWidth = $prevColumn.data('col') - diff;

                        if (params.columnMinWidth <= newTargWidth 
                            && newTargWidth <= 12 - ($row.find('.cms-content-column').length - 1) 
                            && newPrevWidth >= params.columnMinWidth)
                        {
                        	editor.setColumnWidth($targColumn, newTargWidth);
                        	editor.setColumnWidth($prevColumn, newPrevWidth);
                        }
                        ui.element.css({width: false, height: false});
                    },
                });
                if ($column.is(':first-child')) {
                	$column.resizable( "disable" );
                }
            };

            editor.initColumns = function($columns) {
            	$columns = $columns || $('.cms-content-column');
            	$columns.each(function() {
            		editor.initColumn($(this));
                });
            };

            editor.createNewRow = function() {
            	var $newRow = $('<div class="row cms-content-row cms-content-row-new"></div>');
            	editor.initRow($newRow);
            	return $newRow;
            };

            editor.initRows = function() {
                $(editor).find('.cms-content-row').each(function() {
                	editor.initRow($(this));
                });
            };

            editor.cleanNewRows = function() {
                $(editor).find('.cms-content-row-new').hide().not(':last-child').each(function() {
                    if ($(this).prev().hasClass('cms-content-row-new')) {
                        $(this).remove();
                    }
                });
            };

            editor.initRow = function($row) {
                $row.sortable({
                    handle: '.cms-content-widget',
                    connectWith: '.cms-content-row',
                    placeholder: 'col-sm-2 cms-content-column cms-content-column-placehoder',
                    appendTo: $('body'),
                    cursorAt: { left: 75, top: 20 },
                    tolerance: 'pointer',
                    forcePlaceholderSize: true,
                    start: function(event, ui) {
                        // TODO: not triggered by connected draggable ...
                        // http://bugs.jqueryui.com/ticket/9335
                    	$('.cms-content-row-new').show();
                    },
                    over: function(event, ui) {
                    	editor.fixColumnsWidth($(this), true);
                    },
                    out: function(event, ui) {
                    	editor.restoreRowColumns($(this))
                    },
                    receive: function(event, ui) {
                        if (ui.item.hasClass('new-widget')) {
                            var $newCol = $('<div class="col-sm-2 cms-content-column"></div>');
                            $newCol.append(ui.item.data('prototype'));
                            $(event.target).find('.new-widget').replaceWith($newCol);
                            editor.initColumn($newCol);
                        }
                    	if ($(event.target).hasClass('cms-content-row-new')) {
                            $(event.target).removeClass('cms-content-row-new');
                            if($(event.target).find('.cms-content-row').length > 1) {
                                $(event.target)
                                    .after(createNewRow)
                                    .before(createNewRow);
                            }
                        }
                    	editor.fixColumnsWidth($(this));
                    },
                    remove: function(event, ui) {
                        if ($(event.target).is(':empty')) {
                        	$(event.target).addClass('cms-content-row-new');
                        }
                        editor.fixColumnsWidth($(this));
                    },
                    stop: function(event, ui) {
                    	editor.fixColumnsWidth($(this));
                    	editor.fixColumnsResizable($(this));
                    	editor.cleanNewRows();
                    },
                }).draggable({
                    connectToSortable: '.cms-content-widget-trash',
                    helper: "clone",
                    appendTo: $('body'),
                    cursorAt: { left: 75, top: 20 },
                    snap: true,
                    revert: false,
                });
            };

			editor.init = function() {

				editor.initRows();
				editor.initColumns();

	            $(editor).find('.new-widget').draggable({
	            	connectToSortable: '.cms-content-row',
	                appendTo: $('body'),
	            	helper: 'clone',
	            	stop: function(event, ui) {
	            		editor.cleanNewRows();
	            	},
	            });

	            $(editor).find('.cms-content-widget-trash').droppable({
	                accept: '.cms-content-column',
	                hoverClass: 'cms-content-widget-trash-hover',
	                tolerance: 'pointer',
	                drop: function(event, ui) {
	                    ui.draggable.remove();
	                    editor.cleanNewRows();
	                },
	            });
			};

			editor.init();

		});
		return this;
	};

	$(document).ready(function() {
		$('.cms-content-editor').contentEditor();
	});

})(window.jQuery);