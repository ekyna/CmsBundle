;
(function (win, $) {
    "use strict";

    var Editor = {
        enabled: false,
        busy: false,
        debug: true,
        dragging: false,
        dragOffsetX: 0,
        dragOffsetY: 0,
        $box: null,
        config: {
            blockSelector: ".cms-editor-block",
            containerSelector: 'div.cms-editor',
            contentSelector: '.cms-editor-content',
            rowSelector: '.cms-editor-row',
            rowCreateHtml: '<div class="row cms-editor-row"></div>',
            rowDefaultData: {num: 1},
            columnSelector: 'div[class^="col-"]',
            columnSizeRegex: /col-\w{2}-\d+/,
            columnCreateHtml: '<div class="col-md-12 cms-editor-block"></div>',
            columnDefaultData: {id: null, row: 1, column: 1, size: 12, type: null}
        },
        plugingRegistry: {},
        updatedBlocks: {},
        current: {
            plugin: null,
            $block: null,
            $content: null,
            $row: null
        }
    };

    /* Updated columns */
    Editor.addUpdatedBlock = function ($block) {
        var datas = $block.data();
        this.updatedBlocks[datas.id] = datas;
    };
    Editor.getUpdatedBlocks = function () {
        return this.updatedBlocks;
    };
    Editor.hasUpdatedBlocks = function () {
        var size = 0;
        for (var key in this.updatedBlocks) {
            if (this.updatedBlocks.hasOwnProperty(key)) size++;
        }
        return 0 < size;
    };
    Editor.clearUpdatedBlocks = function () {
        this.updatedBlocks = {};
    };

    /* Clear the block selection */
    Editor.clearCurrent = function () {
        if (this.current.$row !== null) {
            this.current.$row.removeClass('cms-editor-selected-row');
        }
        if (this.current.$block !== null) {
            this.current.$block.removeClass('cms-editor-selected-block');
        }
        if (this.current.plugin !== null) {
            this.current.plugin.destroy();
        }
        this.current = {
            plugin: null,
            $block: null,
            $content: null,
            $row: null
        };
    };

    /* Returns the mouse event origin block */
    Editor.getMouseEventTarget = function(e) {
        var $target = $(e.target);

        /* Do nothing on editor control click */
        if ($target.parents('.cms-editor-box').length > 0) {
            return null;
        }
        /* Do nothing on Tinymce click :s */
        if ($target.parents('.mce-container').length > 0) {
            return null;
        }

        /* Look for a block click */
        if ($target.is(Editor.config.blockSelector)) {
            return $target;
        } else if ($target.parents(Editor.config.blockSelector).length > 0) {
            return $target.parents(Editor.config.blockSelector).eq(0);
        } else {
            return false;
        }
    };

    /* Initializes the editor */
    Editor.init = function () {
        var pressedBlock = null;

        $(win).on('mousedown', function(e) {
            pressedBlock = Editor.getMouseEventTarget(e);
        })
        .on('mouseup', function (e) {
            /* Stop dragging box */
            Editor.dragging = false;
            Editor.dragOffsetX = 0;
            Editor.dragOffsetY = 0;

            /* Watch for block selection */
            if (false === pressedBlock) {
                Editor.selectBlock(null);
            } else if(null !== pressedBlock && pressedBlock.is(Editor.getMouseEventTarget(e))) {
                if (Editor.current.$block === null || !(Editor.current.$block !== null && Editor.current.$block.is(pressedBlock))) {
                    Editor.selectBlock(pressedBlock);
                }
            }
            pressedBlock = null;
        })
        .on('mousemove', function (e) {
            /* Start dragging box */
            if (Editor.dragging) {
                Editor.$box.offset({
                    top: e.pageY - Editor.dragOffsetY,
                    left: e.pageX - Editor.dragOffsetX
                });
            }
        })
        .on('beforeunload', function () {
            /* Prevent exit if unsaved modifications */
            if (Editor.hasUpdatedBlocks() || (Editor.current.plugin !== null && Editor.current.plugin.isUpdated())) {
                return "CMS : Des modifications n'ont pas été enregistrées !";
            }
        })
        .on('resize', function () {
            /* TODO: prevent control box to go out of window */
            /* TODO: disable on mobile ? */
        });

        /* Initializes content datas */
        $(this.config.contentSelector).each(function (i, content) {
            var $content = $(content);
            $content.data($content.data('init'));
            $content.removeAttr('data-init').removeData('init');
        });

        /* Initializes row datas */
        $(this.config.rowSelector).each(function (i, row) {
            var $row = $(row);
            $row.data($row.data('init'));
            $row.removeAttr('data-init').removeData('init');
        });

        /* Initializes blocks/columns datas */
        $(this.config.blockSelector).each(function (i, block) {
            var $block = $(block);
            $block.data($block.data('init'));
            $block.removeAttr('data-init').removeData('init');
        });

        this.clearCurrent();
        this.createControlBox();

        //this.enable();
    };

    /* Plugin registration */
    Editor.registerPlugin = function (name, contructor) {
        this.plugingRegistry[name] = contructor;
        this.buildPluginSelector();
    };

    /* Plugin selector */
    Editor.buildPluginSelector = function () {
        var $selector = this.$box.find('#cms-editor-plugin-type');
        $selector.empty();
        for (var type in this.plugingRegistry) {
            var $option = $('<option></option>');
            $option.text(this.plugingRegistry[type].title);
            $option.attr('value', type);
            $selector.append($option);
        }
    };

    /* Creates the control box */
    Editor.createControlBox = function () {
        this.$box = $('.cms-editor-box');
        this.$box.find('.cms-editor-busy').hide();
        this.$box.find('.cms-editor-save').hide();
        this.$box.find('.cms-editor-enable').on('click', function (e) {
            if (Editor.isBusy()) return;
            if (Editor.enabled) {
                Editor.disable();
            } else {
                Editor.enable();
            }
        });
        this.$box.find('.cms-editor-pin').on('click', function (e) {
            if (Editor.isBusy()) return;
            if (Editor.$box.hasClass('pinned')) {
                Editor.$box.removeClass('pinned');
            } else {
                Editor.$box.addClass('pinned').removeAttr('style');
                Editor.dragging = false;
            }
        });
        this.$box.find('.cms-editor-save').on('click', function (e) {
            if (Editor.isBusy()) return;
            if (Editor.hasUpdatedBlocks()) {
                Editor.request();
            }
        });
        this.$box.find('.cms-editor-head').on('mousedown', function (e) {
            if (e.target == e.delegateTarget && !(Editor.$box.hasClass('pinned'))) {
                var offset = Editor.$box.offset();
                Editor.dragOffsetX = e.pageX - offset.left;
                Editor.dragOffsetY = e.pageY - offset.top;
                Editor.dragging = true;
            }
        });
    };

    /* Busy state */
    Editor.setBusy = function (bool) {
        this.busy = bool;
        if (this.busy) {
            this.$box.find('.cms-editor-busy').show();
            this.clearHandlers();
        } else {
            this.$box.find('.cms-editor-busy').hide();
            this.initHandlers();
        }
        this.updateControlsStates();
    };
    Editor.isBusy = function () {
        return this.busy;
    };

    /* Enables the editor. */
    Editor.enable = function () {
        this.enabled = true;
        $('body').addClass('cms-editor-enabled');
        this.setBusy(false);
    };

    /* Disables the editor. */
    Editor.disable = function () {
        if (this.isBusy()) return;
        this.selectBlock(null);
        this.enabled = false;
        $('body').removeClass('cms-editor-enabled');
        this.clearHandlers();
        this.updateControlsStates();
    };

    /* Removes events handlers. */
    Editor.clearHandlers = function () {
        this.$box.off('click');
    };

    /* Initializes the events handlers*/
    Editor.initHandlers = function () {

        /* New row button */
        this.$box.on('click', '.cms-editor-row-add:not(:disabled)', function (e) {
            Editor.insertNewRow();
        });
        /* Remove row */
        this.$box.on('click', '.cms-editor-row-remove:not(:disabled)', function (e) {
            if (confirm("Êtes-vous sûr de vouloir supprimer cette ligne ?\nLes contenus des colonnes seront perdus.")) {
                Editor.removeRow();
            }
        });
        /* Insert row before */
        this.$box.on('click', '.cms-editor-row-insert-before:not(:disabled)', function (e) {
            Editor.insertRowBefore();
        });
        /* Insert row after */
        this.$box.on('click', '.cms-editor-row-insert-after:not(:disabled)', function (e) {
            Editor.insertRowAfter();
        });
        /* Move row up  */
        this.$box.on('click', '.cms-editor-row-move-up:not(:disabled)', function (e) {
            Editor.moveRowUp();
        });
        /* Move row down  */
        this.$box.on('click', '.cms-editor-row-move-down:not(:disabled)', function (e) {
            Editor.moveRowDown();
        });

        /* New column button */
        this.$box.on('click', '.cms-editor-column-add:not(:disabled)', function (e) {
            Editor.insertNewColumn();
        });
        /* Remove column */
        this.$box.on('click', '.cms-editor-column-remove:not(:disabled)', function (e) {
            if (confirm("Êtes-vous sûr de vouloir supprimer cette colonne ?\nLe contenu sera perdu.")) {
                Editor.removeColumn();
            }
        });
        /* Insert column before */
        this.$box.on('click', '.cms-editor-column-insert-before:not(:disabled)', function (e) {
            Editor.insertColumnBefore();
        });
        /* Insert column after */
        this.$box.on('click', '.cms-editor-column-insert-after:not(:disabled)', function (e) {
            Editor.insertColumnAfter();
        });
        /* Move column left  */
        this.$box.on('click', '.cms-editor-column-move-left:not(:disabled)', function (e) {
            Editor.moveColumnLeft();
        });
        /* Move column right  */
        this.$box.on('click', '.cms-editor-column-move-right:not(:disabled)', function (e) {
            Editor.moveColumnRight();
        });
        /* Move column up  */
        this.$box.on('click', '.cms-editor-column-move-up:not(:disabled)', function (e) {
            Editor.moveColumnUp();
        });
        /* Move column down  */
        this.$box.on('click', '.cms-editor-column-move-down:not(:disabled)', function (e) {
            Editor.moveColumnDown();
        });
        /* Grow up column  */
        this.$box.on('click', '.cms-editor-column-grow:not(:disabled)', function (e) {
            Editor.growUpColumn();
        });
        /* Reduce column  */
        this.$box.on('click', '.cms-editor-column-reduce:not(:disabled)', function (e) {
            Editor.reduceColumn();
        });

        /* Plugin type selector */
        this.$box.on('change', '#cms-editor-plugin-type', function (e) {
            if (Editor.current.$block !== null) {
                var $selector = Editor.$box.find('#cms-editor-plugin-type');
                var type = Editor.current.$block.data('type');
                if (type != $selector.val()) {
                    if (confirm("Souhaitez-vous changer le composant de la colonne active ?\nLes données actuelles seront perdues.")) {
                        /* TODO : new block */
                    } else {
                        $selector.val(type);
                    }
                }
            }
        });
    };

    Editor.request = function (datas, callback) {
        this.setBusy(true);

        datas = typeof datas === "object" ? datas : {};
        callback = typeof callback === "function" ? callback : function () {};

        if (this.hasUpdatedBlocks) {
            datas.layout = this.getUpdatedBlocks();
        }
        if (this.current.$content !== null) {
            datas.contentId = this.current.$content.data('id');
        }
        $.ajax(Routing.generate('ekyna_cms_editor_request'), {
            data: datas,
            dataType: 'json',
            type: 'POST'
        })
        .done(function (data) {
            callback(data);
            Editor.clearUpdatedBlocks();
            Editor.setBusy(false);
            $('body').trigger('cms_editor_request_succeed');
        })
        .fail(function () {
            Editor.log('An error occured.');
            Editor.setBusy(false);
        });
    };

    Editor.log = function (msg) {
        if (this.debug) console.log(msg);
    };

    /* Selects a column */
    Editor.selectBlock = function ($block) {
        if (this.isBusy()) return;
        if (this.current.$block !== null) {
            if (this.current.plugin.isUpdated()) {
                if (confirm('Enregistrer les modifications ?')) {
                    var plugin = this.current.plugin;
                    this.request({
                        updateBlock: $.extend(this.current.$block.data(), this.current.plugin.getDatas())
                    }, function (data) {
                        plugin.setUpdated(false);
                        Editor.selectBlock($block);
                    });
                } else {
                    this.current.plugin.focus();
                    return;
                }
            }
        }

        this.clearCurrent();

        if ($block !== null && $block.length == 1) {
            var type = $block.data('type');
            if (type in this.plugingRegistry) {
                this.$box.find('#cms-editor-plugin-type').val(type);
                this.current.plugin = new this.plugingRegistry[type]($block);
                this.current.$block = $block.addClass('cms-editor-selected-block');
                var $content = this.current.$block.parents(this.config.contentSelector);
                if ($content.length > 0) {
                    this.current.$content = $content;
                    this.current.$row = $block.parents(this.config.rowSelector).eq(0).addClass('cms-editor-selected-row');
                }
                this.log($block.data());
                this.current.plugin.init();
            } else {
                this.log('"' + type + '" plugin is not registered.');
                /* TODO exception ? */
            }
        }
        this.updateControlsStates();
    };

    /* Updates controls states */
    Editor.updateControlsStates = function () {

        /* Disables all */
        this.$box.find('.cms-editor-group button').prop('disabled', true);

        /* End if busy */
        if (this.isBusy()) return;

        /* Save layout button */
        if (this.hasUpdatedBlocks()) {
            this.$box.find('.cms-editor-save').prop('disabled', false).show();
        } else {
            this.$box.find('.cms-editor-save').prop('disabled', true).hide();
        }

        if (this.current.$content === null) {
            this.$box.find('.cms-editor-content-controls').hide();
        } else {
            this.$box.find('.cms-editor-content-controls').show();

            /* New row */
            this.$box.find('.cms-editor-row-add').prop('disabled', false);

            if (this.current.$block !== null) {
                var nbCols = this.current.$row.find(this.config.columnSelector).length;
                /* Remove row */
                this.$box.find('.cms-editor-row-remove').prop('disabled', false);
                /* Add row before / Move row up */
                if (this.current.$row.is(':not(:first-child)')) {
                    this.$box.find('.cms-editor-row-insert-before').prop('disabled', false);
                    this.$box.find('.cms-editor-row-move-up').prop('disabled', false);
                }
                /* Add row after */
                this.$box.find('.cms-editor-row-insert-after').prop('disabled', false);
                /* Move row down */
                if (this.current.$row.is(':not(:last-child)')) {
                    this.$box.find('.cms-editor-row-move-down').prop('disabled', false);
                }
                /* New Column */
                if (nbCols < 12) {
                    this.$box.find('.cms-editor-column-add').prop('disabled', false);
                }
                /* Remove Column */
                if (nbCols > 1) {
                    this.$box.find('.cms-editor-column-remove').prop('disabled', false);
                }
                /* Add column after / before */
                if (nbCols < 12) {
                    this.$box.find('.cms-editor-column-insert-after').prop('disabled', false);
                    this.$box.find('.cms-editor-column-insert-before').prop('disabled', false);
                }
                /* Move column left */
                if (this.current.$block.is(':not(:first-child)')) {
                    this.$box.find('.cms-editor-column-move-left').prop('disabled', false);
                }
                /* Move column right */
                if (this.current.$block.is(':not(:last-child)')) {
                    this.$box.find('.cms-editor-column-move-right').prop('disabled', false);
                }
                /* Move column up */
                if (this.current.$row.is(':not(:first-child)')) {
                    this.$box.find('.cms-editor-column-move-up').prop('disabled', false);
                }
                /* Move column down */
                if (this.current.$row.is(':not(:last-child)')) {
                    this.$box.find('.cms-editor-column-move-down').prop('disabled', false);
                }
                /* Grow up column */
                if (this.isColumnGrowable()) {
                    this.$box.find('.cms-editor-column-grow').prop('disabled', false);
                }
                /* Reduce column */
                if (this.isColumnReduceable()) {
                    this.$box.find('.cms-editor-column-reduce').prop('disabled', false);
                }
            }
        }
    };

    /* Creates a new row */
    Editor.createNewRow = function (rowDatas, colDatas) {
        rowDatas = typeof rowDatas === "object" ? rowDatas : {};
        colDatas = typeof colDatas === "object" ? colDatas : {}; //this.config.columnDefaultData;

        rowDatas = $.extend(this.config.rowDefaultData, rowDatas);
        colDatas = $.extend(this.config.columnDefaultData, colDatas, {row: rowDatas.num});

        var $newRow = $(this.config.rowCreateHtml).data(rowDatas);
        $newRow.append(this.createNewColumn(colDatas));
        return $newRow;
    };

    /* Creates a new column */
    Editor.createNewColumn = function (colDatas) {
        var colType = this.$box.find('#cms-editor-plugin-type').val();
        if (!(colType in this.plugingRegistry)) {
            this.log('Undefined type.');
            return false;
        }
        colDatas = typeof colDatas === "object" ? colDatas : {};
        colDatas = $.extend(this.config.columnDefaultData, colDatas, {type: colType});

        var $newColumn = $(this.config.columnCreateHtml).data(colDatas);
        this.setColumnSize($newColumn, colDatas.size, true);

        this.request({createBlock: colDatas}, function (data) {
            if (data.created) {
                if (data.created.datas) {
                    $newColumn.data(data.created.datas);
                }
                if (data.created.innerHtml) {
                    $newColumn.html(data.created.innerHtml);
                }
            } else {
                Editor.log('Column creation failed.');
            }
        });
        $('body').one('cms_editor_request_succeed', function() {
            Editor.selectBlock($newColumn);
        });

        return $newColumn;
    };

    /* Inserts a new row */
    Editor.insertNewRow = function () {
        if (this.current.$content === null) {
            return;
        }
        var $newRow = this.createNewRow({
            num: parseInt(this.current.$content.find(this.config.rowSelector + ':last-child').data('num')) + 1
        });
        this.current.$content.append($newRow);
    };

    /* Removes the current row */
    Editor.removeRow = function () {
        if (this.current.$content === null) {
            return;
        }

        var $currentRow = this.current.$row;

        var removeDatas = [];
        $currentRow.find(this.config.columnSelector).each(function (i, column) {
            removeDatas.push($(column).data());
        });

        /* Decrement num of rows after deletion */
        var rowNum = $currentRow.data('num');
        this.current.$content.find(this.config.rowSelector).each(function (i, row) {
            var $row = $(row);
            if ($row.data('num') > rowNum) {
                $row.data('num', parseInt($row.data('num')) - 1);
                Editor.fixColumnsIndexes($row);
            }
        });

        this.request({removeBlocks: removeDatas}, function (datas) {
            $currentRow.remove();
        });

        this.clearCurrent();
    };

    /* Inserts a new row before the current one */
    Editor.insertRowBefore = function () {
        if (this.current.$content === null || this.current.$row.is(':first-child')) {
            return;
        }

        var rowNum = this.current.$row.data('num');

        /* Increment num of rows after insertion */
        this.current.$content.find(this.config.rowSelector).each(function (i, row) {
            var $row = $(row);
            if ($row.data('num') >= rowNum) {
                $row.data('num', parseInt($row.data('num')) + 1);
                Editor.fixColumnsIndexes($row);
            }
        });

        var $newRow = this.createNewRow({num: rowNum});
        this.current.$row.before($newRow);
    };

    /* Inserts a new row after the current one */
    Editor.insertRowAfter = function () {
        if (this.current.$content === null) {
            return;
        }

        var rowNum = parseInt(this.current.$row.data('num')) + 1;

        /* Increment num of rows after insertion */
        this.current.$content.find(this.config.rowSelector).each(function (i, row) {
            var $row = $(row);
            if ($row.data('num') >= rowNum) {
                $row.data('num', parseInt($row.data('num')) + 1);
                Editor.fixColumnsIndexes($row);
            }
        });

        var $newRow = this.createNewRow({num: rowNum});
        this.current.$row.after($newRow);
    };

    /* Move up the current row */
    Editor.moveRowUp = function () {
        if (this.current.$content !== null && this.current.$row.is(':not(:first-child)')) {
            var $prevRow = this.current.$row.prev('.row');

            /* Increment num of previous row */
            $prevRow.data('num', parseInt($prevRow.data('num')) + 1);
            this.fixColumnsIndexes($prevRow);

            /* Decrement num of current row */
            this.current.$row.data('num', parseInt(this.current.$row.data('num')) - 1);
            this.fixColumnsIndexes(this.current.$row);

            /* Move current row */
            $prevRow.before(this.current.$row);

            this.updateControlsStates();
        }
    };

    /* Move down the current row */
    Editor.moveRowDown = function () {
        if (this.current.$content === null || this.current.$row.is(':last-child')) {
            return;
        }
        var $nextRow = this.current.$row.next('.row');

        /* Decrement num of next row */
        $nextRow.data('num', parseInt($nextRow.data('num')) - 1);
        this.fixColumnsIndexes($nextRow);

        /* Increment num of current row */
        this.current.$row.data('num', parseInt(this.current.$row.data('num')) + 1);
        this.fixColumnsIndexes(this.current.$row);

        /* Move current row */
        $nextRow.after(this.current.$row);

        this.updateControlsStates();
    };

    /* Inserts a column in the current row */
    Editor.insertNewColumn = function () {
        if (this.current.$content === null || this.current.$row.find(this.config.columnSelector).length == 12) {
            return;
        }
        /* Sizing */
        var $sibling = this.getReduceableSibling();
        var siblingSize = parseInt($sibling.data('size'));
        var newColSize = Math.floor(siblingSize / 2);
        siblingSize -= newColSize;
        this.setColumnSize($sibling, siblingSize);

        /* Create column */
        var $newColumn = this.createNewColumn({
            row: this.current.$row.data('num'),
            column: this.current.$row.find(this.config.columnSelector).length + 1,
            size: newColSize
        });
        this.current.$row.append($newColumn);
    };

    /* Removes a column */
    Editor.removeColumn = function () {
        if (this.current.$content === null || this.current.$row.find(this.config.columnSelector).length <= 1) {
            return
        }
        var $column = this.current.$block.detach();
        this.fixColumnSize();

        this.request({removeBlocks: [$column.data()]}, function (datas) {
            $column.remove();
        });

        this.clearCurrent();
    };

    /* Inserts a new column before the current one */
    Editor.insertColumnBefore = function () {
        if (this.current.$content === null || this.current.$row.find(this.config.columnSelector).length == 12) {
            return;
        }

        /* Sizing */
        var $sibling = this.current.$block;
        if ($sibling.data('size') == 1) {
            $sibling = this.getReduceableSibling();
        }
        var siblingSize = parseInt($sibling.data('size'));
        var newColSize = Math.floor(siblingSize / 2);
        siblingSize -= newColSize;
        this.setColumnSize($sibling, siblingSize);

        /* Increment next column's num */
        var colNum = this.current.$block.data('column');
        this.current.$row.find(this.config.columnSelector).each(function (i, column) {
            var $column = $(column);
            var cn = parseInt($column.data('column'));
            if (cn >= colNum) {
                $column.data('column', cn + 1);
                Editor.addUpdatedBlock($column);
            }
        });

        /* Create new column */
        var $newColumn = this.createNewColumn({
            row: this.current.$row.data('num'),
            column: colNum,
            size: newColSize
        });
        this.current.$block.before($newColumn);
    };

    /* Inserts a new column after the current one */
    Editor.insertColumnAfter = function () {
        if (this.current.$content === null || this.current.$row.find(this.config.columnSelector).length == 12) {
            return;
        }

        /* Sizing */
        var $sibling = this.current.$block;
        if ($sibling.data('size') == 1) {
            $sibling = this.getReduceableSibling();
        }
        var siblingSize = parseInt($sibling.data('size'));
        var newColSize = Math.floor(siblingSize / 2);
        siblingSize -= newColSize;
        this.setColumnSize($sibling, siblingSize);

        /* Increment next column's num */
        var colNum = this.current.$block.data('column') + 1;
        this.current.$row.find(this.config.columnSelector).each(function (i, column) {
            var $column = $(column);
            var cn = parseInt($column.data('column'));
            if (cn >= colNum) {
                $column.data('column', cn + 1);
                Editor.addUpdatedBlock($column);
            }
        });

        /* Create new column */
        var $newColumn = this.createNewColumn({
            row: this.current.$row.data('num'),
            column: colNum,
            size: newColSize
        });
        this.current.$block.after($newColumn);
    };

    /* Move left the current column */
    Editor.moveColumnLeft = function () {
        if (this.current.$content === null || this.current.$block.is(':first-child')) {
            return;
        }
        var $prevCol = this.current.$block.prev(this.config.columnSelector);

        /* Increment previous column's num */
        $prevCol.data('column', parseInt($prevCol.data('column')) + 1);
        this.addUpdatedBlock($prevCol);

        /* Decrement current column's num */
        this.current.$block.data('column', parseInt(this.current.$block.data('column')) - 1);
        this.addUpdatedBlock(this.current.$block);

        /* Move columns */
        $prevCol.before(this.current.$block);
        this.updateControlsStates();
    };

    /* Move right the current column */
    Editor.moveColumnRight = function () {
        if (this.current.$content === null || this.current.$block.is(':last-child')) {
            return;
        }
        var $nextCol = this.current.$block.next(this.config.columnSelector);

        /* Decrement next column's num */
        $nextCol.data('column', parseInt($nextCol.data('column')) - 1);
        this.addUpdatedBlock($nextCol);

        /* Increment current column's num */
        this.current.$block.data('column', parseInt(this.current.$block.data('column')) + 1);
        this.addUpdatedBlock(this.current.$block);

        /* Move columns */
        $nextCol.after(this.current.$block);
        this.updateControlsStates();
    };

    /* Move up the current column */
    Editor.moveColumnUp = function () {
        if (this.current.$content === null || this.current.$row.is(':first-child')) {
            return;
        }
        var $prevRow = this.current.$row.prev(this.config.rowSelector);
        this.current.$block.detach().appendTo($prevRow);

        this.fixColumnSize($prevRow);
        this.fixColumnSize(this.current.$row);
        this.fixColumnsIndexes($prevRow);
        this.fixColumnsIndexes(this.current.$row);

        this.current.$row.removeClass('cms-editor-selected-row');
        $prevRow.addClass('cms-editor-selected-row');

        this.current.$row = $prevRow;
        this.updateControlsStates();
    };

    /* Move down the current column */
    Editor.moveColumnDown = function () {
        if (this.current.$content === null || this.current.$row.is(':last-child')) {
            return;
        }
        var $nextRow = this.current.$row.next(this.config.rowSelector);
        this.current.$block.detach().appendTo($nextRow);

        this.fixColumnSize($nextRow);
        this.fixColumnSize(this.current.$row);
        this.fixColumnsIndexes($nextRow);
        this.fixColumnsIndexes(this.current.$row);

        this.current.$row = $nextRow;
        this.updateControlsStates();
    };

    /* Grows up the current column */
    Editor.growUpColumn = function () {
        this.log('Grow up column');
        if (!this.isColumnGrowable()) return;
        var $sibling = this.getReduceableSibling();
        if ($sibling !== null) {
            this.setColumnSize($sibling, parseInt($sibling.data('size')) - 1);
            this.setColumnSize(this.current.$block, parseInt(this.current.$block.data('size')) + 1);

            this.updateControlsStates();
        }
    };

    /* Reduces the current column */
    Editor.reduceColumn = function () {
        this.log('Reduce column');
        if (!this.isColumnReduceable()) return;
        var $sibling = this.getGrowableSibling();
        if ($sibling !== null) {
            this.setColumnSize($sibling, parseInt($sibling.data('size')) + 1);
            this.setColumnSize(this.current.$block, parseInt(this.current.$block.data('size')) - 1);
            this.updateControlsStates();
        }
    };

    /* Sets the column size */
    Editor.setColumnSize = function ($column, size, noUpdate) {
        noUpdate = noUpdate || false;
        var classes = $column.attr('class');
        $column.attr('class', classes.replace(this.config.columnSizeRegex, 'col-md-' + size));
        $column.data('size', parseInt(size));
        if (!noUpdate) this.addUpdatedBlock($column);
    };

    /* Fix the columns positions indexes for the given row */
    Editor.fixColumnsIndexes = function ($row) {
        if (typeof $row === "undefined") {
            if (this.current.$content === null) return;
            $row = this.current.$row;
        }

        $row.find(this.config.columnSelector).each(function (ci, column) {
            var $column = $(column);
            if ($column.data('row') != $row.data('num') || $column.data('column') != ci + 1) {
                $column.data({row: $row.data('num'), column: ci + 1});
                Editor.addUpdatedBlock($column);
            }
        });
    };

    /* Fix the size of given column */
    Editor.fixColumnSize = function ($row) {
        if (typeof $row === "undefined") {
            if (this.current.$content === null) return;
            $row = this.current.$row;
        }

        var total = 0;
        var $columns = $row.find(this.config.columnSelector);
        $columns.each(function (i, column) {
            total += $(column).data('size');
        });
        var delta = total - 12;
        if (delta == 0) return;

        var editor = this;
        var avg = Math.ceil(Math.abs(delta) / $columns.length);

        if (delta > 0) {
            /* Reduce widests */
            this.log('Reducing widests.');
            $columns.sort(function (a, b) {
                return $(a).data('size') == $(b).data('size') ? 0 : $(a).data('size') < $(b).data('size');
            }).each(function (i, column) {
                var $col = $(column);
                if (delta > 0) {
                    if (avg > delta) avg = delta;
                    var tmp = avg;
                    var size = parseInt($col.data('size'));
                    if (size - tmp < 1) tmp = Math.abs(1 - size);
                    editor.setColumnSize($col, size - tmp);
                    delta -= tmp;
                }
            });
        } else {
            delta = -delta;
            /* Grow smallests */
            this.log('Growing smallests.');
            $columns.sort(function (a, b) {
                return $(a).data('size') == $(b).data('size') ? 0 : $(a).data('size') > $(b).data('size');
            }).each(function (i, column) {
                var $col = $(column);
                if (delta > 0) {
                    if (avg > delta) avg = delta;
                    editor.setColumnSize($col, parseInt($col.data('size')) + avg);
                    delta -= avg;
                }
            });
        }
    };

    /* Returns wether the current row's columns can be resized */
    Editor.isRowResizeable = function () {
        return (
            this.current.$content !== null
            && this.current.$row.find(this.config.columnSelector).length > 1
            && this.current.$row.find(this.config.columnSelector).length < 12
        );
    };

    /* Returns wether the current columns can be grown */
    Editor.isColumnGrowable = function () {
        if (!this.isRowResizeable()) return false;
        var found = false;
        this.current.$block.siblings().each(function (i, column) {
            if ($(column).data('size') > 1) found = true;
        });
        return found;
    };

    /* Returns wether the current columns can be reduced */
    Editor.isColumnReduceable = function () {
        if (!this.isRowResizeable()) return false;
        return this.current.$block.data('size') > 1;
    };

    /* Returns a reduceable column sibling of the current one. */
    Editor.getReduceableSibling = function () {
        var $sibling = null;
        if (this.isRowResizeable()) {
            if (this.current.$block.is(':last-child')) {
                do {
                    $sibling = this.current.$block.prev(this.config.columnSelector);
                } while (1 == $sibling.length && 1 == $sibling.data('size'));
            } else {
                do {
                    $sibling = this.current.$block.next(this.config.columnSelector);
                } while (1 == $sibling.length && 1 == $sibling.data('size'));
                if (0 == $sibling.length || 1 == $sibling.data('size')) {
                    do {
                        $sibling = this.current.$block.prev(this.config.columnSelector);
                    } while (1 == $sibling.length && 1 == $sibling.data('size'));
                }
            }
        }
        return $sibling;
    };

    /* Returns a "growable" column sibling of the current one. */
    Editor.getGrowableSibling = function () {
        if (this.current.$block.is(':last-child')) {
            return this.current.$block.prev(this.config.columnSelector)
        } else {
            return this.current.$block.next(this.config.columnSelector);
        }
    };

    win.CmsEditor = Editor;


    /* Base plugin */
    function CmsPlugin($el) {
        this.$element = $el;
        this.name = 'CmsPlugin';
        var updated = false;

        this.setUpdated = function (bool) {
            updated = bool;
        };
        this.isUpdated = function () {
            return updated;
        };
    }

    CmsPlugin.title = 'none';
    CmsPlugin.prototype.init = function () {
        if (Editor.debug) console.log(this.name + ' :: init');
    };
    CmsPlugin.prototype.destroy = function () {
        if (Editor.debug) console.log(this.name + ' :: destroy');
    };
    CmsPlugin.prototype.focus = function () {
        if (Editor.debug) console.log(this.name + ' :: focus');
    };
    CmsPlugin.prototype.getDatas = function () {
        return {};
    };

    win.CmsPlugin = CmsPlugin;


    $(win.document).ready(function () {
        CmsEditor.init();
    });

})(window, jQuery);