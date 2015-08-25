(function(root, factory) {
    "use strict";
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = factory(require('require'), require('jquery'), require('routing'));
    }
    else if (typeof define === 'function' && define.amd) {
        define('ekyna-cms-editor', ['require', 'jquery', 'routing'], function(Require, $, Router) {
            return factory(Require, $, Router);
        });
    } else {
        root.EkynaCmsEditor = factory(root.require, root.jQuery, root.Routing);
    }

}(this, function(Require, $, Router) {
    "use strict";

    var Editor = function() {
        this.enabled = false;
        this.busy = false;
        this.dragging = false;
        this.dragOffsetX = 0;
        this.dragOffsetY = 0;
        this.$box = null;
        this.$body = null;
        this.config = {
            debug: false,
            autosave: true,
            blockSelector: '.cms-editor-block',
            blockSelectedClass: 'cms-editor-selected-block',
            contentSelector: '.cms-editor-content',
            rowSelector: '.cms-editor-row',
            rowSelectedClass: 'cms-editor-selected-row',
            rowCreateHtml: '<div class="row cms-editor-row"></div>',
            rowDefaultData: {num: 1},
            columnSelector: 'div[class^="col-"]',
            columnCreateHtml: '<div class="col-md-12 cms-editor-block"></div>',
            columnDefaultData: {id: null, row: 1, column: 1, size: 12, type: null},
            columnSizeRegex: /col-\w{2}-\d+/,
            plugins: ['tinymce'] // TODO configurable
        };
        this.plugingRegistry = {};
        this.updatedBlocks = {};
        this.current = {
            plugin: null,
            $block: null,
            $content: null,
            $row: null
        };
    };

    Editor.prototype.constructor = Editor;

    /* Updated columns */
    Editor.prototype.addUpdatedBlock = function ($block) {
        var datas = $block.data();
        this.updatedBlocks[datas.id] = datas;
    };
    Editor.prototype.getUpdatedBlocks = function () {
        return this.updatedBlocks;
    };
    Editor.prototype.hasUpdatedBlocks = function () {
        var size = 0;
        if (this.current.plugin !== null && this.current.plugin.isUpdated()) {
            return true;
        }
        for (var key in this.updatedBlocks) {
            if (this.updatedBlocks.hasOwnProperty(key)) size++;
        }
        return 0 < size;
    };
    Editor.prototype.clearUpdatedBlocks = function () {
        this.updatedBlocks = {};
    };

    /* Clear the block selection */
    Editor.prototype.clearCurrent = function () {
        if (this.current.$row !== null) {
            this.current.$row.removeClass(this.config.rowSelectedClass);
        }
        if (this.current.$block !== null) {
            this.current.$block.removeClass(this.config.blockSelectedClass);
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
    Editor.prototype.getMouseEventTarget = function(e) {
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
        if ($target.is(this.config.blockSelector)) {
            return $target;
        } else if ($target.parents(this.config.blockSelector).length > 0) {
            return $target.parents(this.config.blockSelector).eq(0);
        } else {
            return false;
        }
    };

    /* Initializes the editor */
    Editor.prototype.init = function () {
        var ed = this;
        var pressedBlock = null;

        this.$body = $('body');

        $(window).on('mousedown', function(e) {
            if (ed.enabled) {
                pressedBlock = ed.getMouseEventTarget(e);
            }
        })
        .on('mouseup', function (e) {
            /* Stop dragging box */
            ed.dragging = false;
            ed.dragOffsetX = 0;
            ed.dragOffsetY = 0;

            /* Watch for block selection */
            if (ed.enabled) {
                if (false === pressedBlock) {
                    ed.selectBlock(null);
                } else if(null !== pressedBlock && pressedBlock.is(ed.getMouseEventTarget(e))) {
                    if (ed.current.$block === null || !(ed.current.$block !== null && ed.current.$block.is(pressedBlock))) {
                        ed.selectBlock(pressedBlock);
                    }
                }
            }
            pressedBlock = null;
        })
        .on('mousemove', function (e) {
            /* Start dragging box */
            if (ed.dragging) {
                ed.$box.offset({
                    top: e.pageY - ed.dragOffsetY,
                    left: e.pageX - ed.dragOffsetX
                });
            }
        })
        .on('beforeunload', function () {
            /* Prevent exit if unsaved modifications */
            if (ed.hasUpdatedBlocks()) {
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

        for (var type in this.config.plugins) {
            if (this.config.plugins.hasOwnProperty(type)) {
                Require(['ekyna-cms-editor/' + this.config.plugins[type]], function (plugin) {
                    ed.registerPlugin(plugin);
                });
            }
        }

        this.clearCurrent();
        this.createControlBox();

        //this.enable();
    };

    /* Plugin registration */
    Editor.prototype.registerPlugin = function (plugin) {
        this.log('Editor.registerPlugin()');
        if (!(plugin.hasOwnProperty('name'))) {
            throw 'Unexpected plugin.';
        }
        if (!(plugin.name in this.plugingRegistry)) {
            this.plugingRegistry[plugin.name] = plugin;
            this.buildPluginSelector();
        }
    };

    /* Plugin selector */
    Editor.prototype.buildPluginSelector = function () {
        var $selector = this.$box.find('#cms-editor-plugin-type');
        $selector.empty();
        for (var type in this.plugingRegistry) {
            if (this.plugingRegistry.hasOwnProperty(type)) {
                var $option = $('<option></option>');
                $option.text(this.plugingRegistry[type].title);
                $option.attr('value', type);
                $selector.append($option);
            }
        }
    };

    /* Creates the control box */
    Editor.prototype.createControlBox = function () {
        var ed = this;
        this.$box = $('.cms-editor-box');
        this.$box.find('.cms-editor-busy').hide();
        this.$box.find('.cms-editor-save').hide();
        this.$box.find('.cms-editor-enable').on('click', function () {
            if (ed.isBusy()) return;
            if (ed.enabled) {
                ed.disable();
            } else {
                ed.enable();
            }
        });
        this.$box.find('.cms-editor-pin').on('click', function () {
            if (ed.isBusy()) return;
            if (ed.$box.hasClass('pinned')) {
                ed.$box.removeClass('pinned');
            } else {
                ed.$box.addClass('pinned').removeAttr('style');
                ed.dragging = false;
            }
        });
        this.$box.find('.cms-editor-save').on('click', function () {
            if (ed.isBusy()) return;
            ed.save(true);
        });
        this.$box.find('.cms-editor-head').on('mousedown', function (e) {
            if (e.target == e.delegateTarget && !(ed.$box.hasClass('pinned'))) {
                var offset = ed.$box.offset();
                ed.dragOffsetX = e.pageX - offset.left;
                ed.dragOffsetY = e.pageY - offset.top;
                ed.dragging = true;
            }
        });
    };

    /* Busy state */
    Editor.prototype.setBusy = function (bool) {
        this.log('Editor.setBusy()');
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
    Editor.prototype.isBusy = function () {
        return this.busy;
    };

    /* Enables the editor. */
    Editor.prototype.enable = function () {
        this.enabled = true;
        this.$body.addClass('cms-editor-enabled');
        this.setBusy(false);
    };

    /* Disables the editor. */
    Editor.prototype.disable = function () {
        this.log('Editor.disable()');
        var ed = this;
        if (this.isBusy()) return;
        if (this.save()) {
            this.$body.one('cms_editor_request_succeed', function() {
                ed.disable();
            });
            return;
        }
        this.selectBlock(null);
        this.enabled = false;
        this.$body.removeClass('cms-editor-enabled');
        this.clearHandlers();
        this.updateControlsStates();
    };

    /* Removes events handlers. */
    Editor.prototype.clearHandlers = function () {
        this.$box.off('click');
    };

    /* Initializes the events handlers*/
    Editor.prototype.initHandlers = function () {
        var ed = this;
        /* New row button */
        this.$box.on('click', '.cms-editor-row-add:not(:disabled)', function () {
            ed.insertNewRow();
        });
        /* Remove row */
        this.$box.on('click', '.cms-editor-row-remove:not(:disabled)', function () {
            if (confirm("Êtes-vous sûr de vouloir supprimer cette ligne ?\nLes contenus des colonnes seront perdus.")) {
                ed.removeRow();
            }
        });
        /* Insert row before */
        this.$box.on('click', '.cms-editor-row-insert-before:not(:disabled)', function () {
            ed.insertRowBefore();
        });
        /* Insert row after */
        this.$box.on('click', '.cms-editor-row-insert-after:not(:disabled)', function () {
            ed.insertRowAfter();
        });
        /* Move row up  */
        this.$box.on('click', '.cms-editor-row-move-up:not(:disabled)', function () {
            ed.moveRowUp();
        });
        /* Move row down  */
        this.$box.on('click', '.cms-editor-row-move-down:not(:disabled)', function () {
            ed.moveRowDown();
        });

        /* New column button */
        this.$box.on('click', '.cms-editor-column-add:not(:disabled)', function () {
            ed.insertNewColumn();
        });
        /* Remove column */
        this.$box.on('click', '.cms-editor-column-remove:not(:disabled)', function () {
            if (confirm("Êtes-vous sûr de vouloir supprimer cette colonne ?\nLe contenu sera perdu.")) {
                ed.removeColumn();
            }
        });
        /* Insert column before */
        this.$box.on('click', '.cms-editor-column-insert-before:not(:disabled)', function () {
            ed.insertColumnBefore();
        });
        /* Insert column after */
        this.$box.on('click', '.cms-editor-column-insert-after:not(:disabled)', function () {
            ed.insertColumnAfter();
        });
        /* Move column left  */
        this.$box.on('click', '.cms-editor-column-move-left:not(:disabled)', function () {
            ed.moveColumnLeft();
        });
        /* Move column right  */
        this.$box.on('click', '.cms-editor-column-move-right:not(:disabled)', function () {
            ed.moveColumnRight();
        });
        /* Move column up  */
        this.$box.on('click', '.cms-editor-column-move-up:not(:disabled)', function () {
            ed.moveColumnUp();
        });
        /* Move column down  */
        this.$box.on('click', '.cms-editor-column-move-down:not(:disabled)', function () {
            ed.moveColumnDown();
        });
        /* Grow up column  */
        this.$box.on('click', '.cms-editor-column-grow:not(:disabled)', function () {
            ed.growUpColumn();
        });
        /* Reduce column  */
        this.$box.on('click', '.cms-editor-column-reduce:not(:disabled)', function () {
            ed.reduceColumn();
        });

        /* Plugin type selector */
        this.$box.on('change', '#cms-editor-plugin-type', function () {
            if (ed.current.$block !== null) {
                var $selector = ed.$box.find('#cms-editor-plugin-type');
                var type = ed.current.$block.data('type');
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

    Editor.prototype.request = function (datas, callback) {
        var ed = this;
        ed.setBusy(true);

        datas = typeof datas === "object" ? datas : {};
        callback = typeof callback === "function" ? callback : function () {};

        if (this.hasUpdatedBlocks) {
            datas.layout = this.getUpdatedBlocks();
        }
        if (this.current.$content !== null) {
            datas.contentId = this.current.$content.data('id');
        }
        $.ajax(Router.generate('ekyna_cms_editor_request'), {
            data: datas,
            dataType: 'json',
            type: 'POST'
        })
        .done(function (data) {
            callback(data);
            ed.clearUpdatedBlocks();
            ed.setBusy(false);
            ed.$body.trigger('cms_editor_request_succeed');
        })
        .fail(function () {
            ed.log('An error occured.');
            ed.setBusy(false);
        });
    };

    /* Log messages */
    Editor.prototype.log = function (msg) {
        if (this.config.debug) this.log(msg);
    };

    /* Watch for updated blocks and returns true if save requested */
    Editor.prototype.save = function(force) {
        force = force === "undefined" ? false : force;
        if (this.hasUpdatedBlocks()) {
            if (this.config.autosave || force || confirm('Enregistrer les modifications ?')) {
                if (this.current.$block !== null) {
                    var plugin = this.current.plugin;
                    this.request({
                        updateBlock: $.extend(this.current.$block.data(), this.current.plugin.getDatas())
                    }, function () { // args: data
                        plugin.setUpdated(false);
                    });
                } else {
                    this.request();
                }
                return true;
            } else if(this.current.$block !== null) {
                this.current.plugin.focus();
            }
        }
        return false;
    };

    /* Selects a block */
    Editor.prototype.selectBlock = function ($block) {
        this.log('Editor.selectBlock()');
        var ed = this;
        if (ed.isBusy()) return;
        if (ed.save()) {
            ed.$body.one('cms_editor_request_succeed', function() {
                ed.selectBlock($block);
            });
            return;
        }

        ed.clearCurrent();

        if ($block !== null && $block.length == 1) {
            var type = $block.data('type');
            if (type in ed.plugingRegistry) {
                ed.$box.find('#cms-editor-plugin-type').val(type);
                ed.current.plugin = new ed.plugingRegistry[type].create($block);
                ed.current.$block = $block.addClass(ed.config.blockSelectedClass);
                var $content = ed.current.$block.parents(ed.config.contentSelector);
                if ($content.length > 0) {
                    ed.current.$content = $content;
                    ed.current.$row = $block.parents(ed.config.rowSelector).eq(0).addClass(ed.config.rowSelectedClass);
                }
                ed.current.plugin.init();
            } else {
                throw '"' + type + '" plugin is not registered.';
            }
        }
        ed.updateControlsStates();
    };

    /* Updates controls states */
    Editor.prototype.updateControlsStates = function () {
        this.log('Editor.updateControlsStates()');

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

        this.log(this.current.$content);
        if (this.current.$content === null) {
            this.$box.find('.cms-editor-content-controls').hide();
        } else {
            this.$box.find('.cms-editor-content-controls').show();

            /* New row */
            this.$box.find('.cms-editor-row-add').prop('disabled', false);

            if (this.current.$block !== null) {
                var nbRows = this.current.$content.find(this.config.rowSelector).length;
                var nbCols = this.current.$row.find(this.config.columnSelector).length;
                /* Remove row */
                if (nbRows > 1) {
                    this.$box.find('.cms-editor-row-remove').prop('disabled', false);
                }
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
    Editor.prototype.createNewRow = function (rowDatas, colDatas) {
        rowDatas = typeof rowDatas === "object" ? rowDatas : {};
        colDatas = typeof colDatas === "object" ? colDatas : {}; //this.config.columnDefaultData;

        rowDatas = $.extend({}, this.config.rowDefaultData, rowDatas);
        colDatas = $.extend({}, this.config.columnDefaultData, colDatas, {row: rowDatas.num});

        var $newRow = $(this.config.rowCreateHtml).data(rowDatas);
        $newRow.append(this.createNewColumn(colDatas));
        return $newRow;
    };

    /* Creates a new column */
    Editor.prototype.createNewColumn = function (colDatas) {
        var ed = this;
        //var colType = ed.$box.find('#cms-editor-plugin-type').val();
        /* TODO temp as only tinymce works for now */
        var colType = 'tinymce';

        if (!(colType in ed.plugingRegistry)) {
            this.log('Undefined type : ' + colType);
            return false;
        }
        colDatas = typeof colDatas === "object" ? colDatas : {};
        colDatas = $.extend({}, ed.config.columnDefaultData, colDatas, {type: colType});

        var $newColumn = $(ed.config.columnCreateHtml).data(colDatas);
        ed.setColumnSize($newColumn, colDatas.size, true);

        ed.request({createBlock: colDatas}, function (data) {
            if (data.created) {
                if (data.created.datas) {
                    $newColumn.data(data.created.datas);
                }
                if (data.created.innerHtml) {
                    $newColumn.html(data.created.innerHtml);
                }
            } else {
                ed.log('Column creation failed.');
            }
        });
        ed.$body.one('cms_editor_request_succeed', function() {
            ed.selectBlock($newColumn);
        });

        return $newColumn;
    };

    /* Inserts a new row */
    Editor.prototype.insertNewRow = function () {
        if (this.current.$content === null) {
            return;
        }
        var $newRow = this.createNewRow({
            num: parseInt(this.current.$content.find(this.config.rowSelector + ':last-child').data('num')) + 1
        });
        this.current.$content.append($newRow);
    };

    /* Removes the current row */
    Editor.prototype.removeRow = function () {
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
        var ed = this;
        this.current.$content.find(this.config.rowSelector).each(function (i, row) {
            var $row = $(row);
            if ($row.data('num') > rowNum) {
                $row.data('num', parseInt($row.data('num')) - 1);
                ed.fixColumnsIndexes($row);
            }
        });

        this.request({removeBlocks: removeDatas}, function () { // args: datas
            $currentRow.remove();
        });

        this.clearCurrent();
    };

    /* Inserts a new row before the current one */
    Editor.prototype.insertRowBefore = function () {
        if (this.current.$content === null || this.current.$row.is(':first-child')) {
            return;
        }

        var ed = this;
        var rowNum = this.current.$row.data('num');

        /* Increment num of rows after insertion */
        this.current.$content.find(this.config.rowSelector).each(function (i, row) {
            var $row = $(row);
            if ($row.data('num') >= rowNum) {
                $row.data('num', parseInt($row.data('num')) + 1);
                ed.fixColumnsIndexes($row);
            }
        });

        var $newRow = this.createNewRow({num: rowNum});
        this.current.$row.before($newRow);
    };

    /* Inserts a new row after the current one */
    Editor.prototype.insertRowAfter = function () {
        if (this.current.$content === null) {
            return;
        }

        var ed = this;
        var rowNum = parseInt(this.current.$row.data('num')) + 1;

        /* Increment num of rows after insertion */
        this.current.$content.find(this.config.rowSelector).each(function (i, row) {
            var $row = $(row);
            if ($row.data('num') >= rowNum) {
                $row.data('num', parseInt($row.data('num')) + 1);
                ed.fixColumnsIndexes($row);
            }
        });

        var $newRow = this.createNewRow({num: rowNum});
        this.current.$row.after($newRow);
    };

    /* Move up the current row */
    Editor.prototype.moveRowUp = function () {
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
    Editor.prototype.moveRowDown = function () {
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
    Editor.prototype.insertNewColumn = function () {
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
        // TODO createNewColumn() can return false (type not found). So don't change the layout until we've got a positive result.
        var $newColumn = this.createNewColumn({
            row: this.current.$row.data('num'),
            column: this.current.$row.find(this.config.columnSelector).length + 1,
            size: newColSize
        });
        this.current.$row.append($newColumn);
    };

    /* Removes a column */
    Editor.prototype.removeColumn = function () {
        if (this.current.$content === null || this.current.$row.find(this.config.columnSelector).length <= 1) {
            return
        }
        var $column = this.current.$block.detach();
        this.fixColumnSize();

        this.request({removeBlocks: [$column.data()]}, function () { // args: datas
            $column.remove();
        });

        this.clearCurrent();
    };

    /* Inserts a new column before the current one */
    Editor.prototype.insertColumnBefore = function () {
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
        var ed = this;
        var colNum = this.current.$block.data('column');
        this.current.$row.find(this.config.columnSelector).each(function (i, column) {
            var $column = $(column);
            var cn = parseInt($column.data('column'));
            if (cn >= colNum) {
                $column.data('column', cn + 1);
                ed.addUpdatedBlock($column);
            }
        });

        /* Create new column */
        // TODO createNewColumn() can return false (type not found). So don't change the layout until we've got a positive result.
        var $newColumn = this.createNewColumn({
            row: this.current.$row.data('num'),
            column: colNum,
            size: newColSize
        });
        this.current.$block.before($newColumn);
    };

    /* Inserts a new column after the current one */
    Editor.prototype.insertColumnAfter = function () {
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
        var ed = this;
        var colNum = this.current.$block.data('column') + 1;
        this.current.$row.find(this.config.columnSelector).each(function (i, column) {
            var $column = $(column);
            var cn = parseInt($column.data('column'));
            if (cn >= colNum) {
                $column.data('column', cn + 1);
                ed.addUpdatedBlock($column);
            }
        });

        /* Create new column */
        // TODO createNewColumn() can return false (type not found). So don't change the layout until we've got a positive result.
        var $newColumn = this.createNewColumn({
            row: this.current.$row.data('num'),
            column: colNum,
            size: newColSize
        });
        this.current.$block.after($newColumn);
    };

    /* Move left the current column */
    Editor.prototype.moveColumnLeft = function () {
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

        this.current.plugin.focus();
        this.updateControlsStates();
    };

    /* Move right the current column */
    Editor.prototype.moveColumnRight = function () {
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

        this.current.plugin.focus();
        this.updateControlsStates();
    };

    /* Move up the current column */
    Editor.prototype.moveColumnUp = function () {
        if (this.current.$content === null || this.current.$row.is(':first-child')) {
            return;
        }
        var $prevRow = this.current.$row.prev(this.config.rowSelector);
        this.current.$block.detach().appendTo($prevRow);

        if (this.current.$row.find(this.config.columnSelector).length > 0) {
            this.fixColumnSize(this.current.$row);
            this.fixColumnsIndexes(this.current.$row);
            this.current.$row.removeClass(this.config.rowSelectedClass);
        } else {
            this.current.$row.remove();
        }

        this.fixColumnSize($prevRow);
        this.fixColumnsIndexes($prevRow);
        $prevRow.addClass(this.config.rowSelectedClass);

        this.current.$row = $prevRow;

        this.current.plugin.focus();
        this.updateControlsStates();
    };

    /* Move down the current column */
    Editor.prototype.moveColumnDown = function () {
        if (this.current.$content === null || this.current.$row.is(':last-child')) {
            return;
        }
        var $nextRow = this.current.$row.next(this.config.rowSelector);
        this.current.$block.detach().appendTo($nextRow);

        if (this.current.$row.find(this.config.columnSelector).length > 0) {
            this.fixColumnSize(this.current.$row);
            this.fixColumnsIndexes(this.current.$row);
            this.current.$row.removeClass(this.config.rowSelectedClass);
        } else {
            this.current.$row.remove();
        }

        this.fixColumnSize($nextRow);
        this.fixColumnsIndexes($nextRow);
        $nextRow.addClass(this.config.rowSelectedClass);

        this.current.$row = $nextRow;

        this.current.plugin.focus();
        this.updateControlsStates();
    };

    /* Grows up the current column */
    Editor.prototype.growUpColumn = function () {
        if (!this.isColumnGrowable()) return;
        var $sibling = this.getReduceableSibling();
        if ($sibling !== null) {
            this.setColumnSize($sibling, parseInt($sibling.data('size')) - 1);
            this.setColumnSize(this.current.$block, parseInt(this.current.$block.data('size')) + 1);

            this.updateControlsStates();
        }
    };

    /* Reduces the current column */
    Editor.prototype.reduceColumn = function () {
        if (!this.isColumnReduceable()) return;
        var $sibling = this.getGrowableSibling();
        if ($sibling !== null) {
            this.setColumnSize($sibling, parseInt($sibling.data('size')) + 1);
            this.setColumnSize(this.current.$block, parseInt(this.current.$block.data('size')) - 1);
            this.updateControlsStates();
        }
    };

    /* Sets the column size */
    Editor.prototype.setColumnSize = function ($column, size, noUpdate) {
        noUpdate = noUpdate || false;
        var classes = $column.attr('class');
        $column.attr('class', classes.replace(this.config.columnSizeRegex, 'col-md-' + size));
        $column.data('size', parseInt(size));
        if (!noUpdate) this.addUpdatedBlock($column);
    };

    /* Fix the columns positions indexes for the given row */
    Editor.prototype.fixColumnsIndexes = function ($row) {
        if (typeof $row === "undefined") {
            if (this.current.$content === null) return;
            $row = this.current.$row;
        }
        var ed = this;
        $row.find(this.config.columnSelector).each(function (ci, column) {
            var $column = $(column);
            if ($column.data('row') != $row.data('num') || $column.data('column') != ci + 1) {
                $column.data({row: $row.data('num'), column: ci + 1});
                ed.addUpdatedBlock($column);
            }
        });
    };

    /* Fix the size of given column */
    Editor.prototype.fixColumnSize = function ($row) {
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
    Editor.prototype.isRowResizeable = function () {
        return (
            this.current.$content !== null
            && this.current.$row.find(this.config.columnSelector).length > 1
            && this.current.$row.find(this.config.columnSelector).length < 12
        );
    };

    /* Returns wether the current columns can be grown */
    Editor.prototype.isColumnGrowable = function () {
        if (!this.isRowResizeable()) return false;
        var found = false;
        this.current.$block.siblings().each(function (i, column) {
            if ($(column).data('size') > 1) found = true;
        });
        return found;
    };

    /* Returns wether the current columns can be reduced */
    Editor.prototype.isColumnReduceable = function () {
        if (!this.isRowResizeable()) return false;
        return this.current.$block.data('size') > 1;
    };

    /* Returns a reduceable column sibling of the current one. */
    Editor.prototype.getReduceableSibling = function () {
        var $sibling = null;
        if (this.isRowResizeable()) {
            $sibling = this.current.$block;
            if (this.current.$block.is(':last-child')) {
                do {
                    $sibling = $sibling.prev(this.config.columnSelector);
                } while (1 == $sibling.length && 1 == $sibling.data('size'));
            } else {
                do {
                    $sibling = $sibling.next(this.config.columnSelector);
                } while (1 == $sibling.length && 1 == $sibling.data('size'));
                if (0 == $sibling.length || 1 == $sibling.data('size')) {
                    $sibling = this.current.$block;
                    do {
                        $sibling = $sibling.prev(this.config.columnSelector);
                    } while (1 == $sibling.length && 1 == $sibling.data('size'));
                }
            }
        }
        return $sibling;
    };

    /* Returns a "growable" column sibling of the current one. */
    Editor.prototype.getGrowableSibling = function () {
        if (this.current.$block.is(':last-child')) {
            return this.current.$block.prev(this.config.columnSelector)
        } else {
            return this.current.$block.next(this.config.columnSelector);
        }
    };

    return new Editor;

}));