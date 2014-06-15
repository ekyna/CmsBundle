;(function(win, $) {
    "use strict";

    var Editor = {
    	test: null	
    };
    
    function CmsEditor() {
    	var updatedColumns = {},
    		enabled = false;
    	
        this.config = {
        	enabled: false,
        	containerSelector: 'div.cms-editor',
        	
        	rowSelector: 'div.row',
            rowCreateHtml: '<div class="row"></div>',
            rowDefaultData: {num: 1},
            
            columnSelector: 'div[class^="col-"]',
            columnSizeRegex: /col-\w{2}-(\d+)/,
            columnCreateHtml: '<div class="col-md-12"></div>',
            columnDefaultData: {id: null, row: 1, column: 1, size: 12, type: null},
            contentId: null
        };
        this.busy = false;
        this.$container = null;
        this.$box = null;
        this.current = null;
        this.plugingRegistry = {};
        
        /* Updated columns */
        this.addUpdatedColumn = function($column) {
        	var datas = $column.data();
        	updatedColumns[datas.id] = datas;
        };
        this.getUpdatedColumns = function() {
        	return updatedColumns;
        };
        this.hasUpdatedColumns = function() {
        	var size = 0;
            for (var key in updatedColumns) {
                if (updatedColumns.hasOwnProperty(key)) size++;
            }
        	return 0 < size;
        };
        this.clearUpdatedColumns = function() {
        	updatedColumns = {};
        };
        
        /* Enable state */
        this.setEnabled = function(bool) {
        	enabled = bool;
        };
        this.isEnabled = function() {
        	return enabled;
        };
    }
    CmsEditor.debug = true;
    CmsEditor.dragging = false;
    CmsEditor.dragOffsetX = 0;
    CmsEditor.dragOffsetY = 0;
    
    
    /* Sets the container */
    CmsEditor.prototype.setContainer = function($container) {
        this.config = $.extend(this.config, $container.data('config'));
        this.$container = $container;
        this.init();
    };
    
    /* Initializes the editor */
    CmsEditor.prototype.init = function () {
        var self = this;
        
        /* Column selection */
        $(win).on('click', function(e) {
            if (!self.isEnabled()) return;
            /* Do nothing on editor control click */
            if ($(e.target).parents('.cms-editor-box').length > 0) {
                return;
            }
            /* Tinymce controls :s */
            if ($(e.target).parents('.mce-container').length > 0) {
                return;
            }
            /* If the event bubbles in CmsEditor */
            if ($(e.target).parents('.cms-editor').length > 0) {
                /* Column lookup */
                if ($(e.target).is(self.config.columnSelector)) {
                    var column = $(e.target);
                } else {
                    var column = $(e.target).parents(self.config.columnSelector);
                }
                /* Update column selection */
                if (column.length == 1) {
                    self.selectColumn(column);
                    return;
                }
            }
            /* Default : clear column selection */
            self.selectColumn(null);
        })
        /* Dragging box */
        .on('mousemove', function(e) {
            if (CmsEditor.dragging) {
            	self.$box.offset({
                    top: e.pageY - CmsEditor.dragOffsetY,
                    left: e.pageX - CmsEditor.dragOffsetX
                });
            }
        })
        /* Stop dragging box */
        .on('mouseup', function(e) {
        	CmsEditor.dragging = false;
            CmsEditor.dragOffsetX = 0;
            CmsEditor.dragOffsetY = 0;
        })        
        /* Prevent exit if unsaved modifications */
        .on('beforeunload', function(){
        	if (self.hasUpdatedColumns() || (self.current !== null && self.current.plugin.isUpdated())) {
        		return "CMS : Des modifications n'ont pas été enregistrées !"
        	}
        })
        .on('resize', function() {
        	/* TODO: prevent control box to go out of window */
        	/* TODO: disable on mobile ? */
        });
        
        /* Initializes row nums */
        this.$container.find(this.config.rowSelector).each(function(i, row) {
        	var $row = $(row);
        	$row.data($row.data('init'));
        	$row.removeAttr('data-init').removeData('init');
        });
        
        /* Initializes columns sizes */
        this.$container.find(this.config.columnSelector).each(function(i, column) {
        	var $column = $(column);
        	$column.data($column.data('init'));
        	$column.removeAttr('data-init').removeData('init');
        });
        
        this.createControlBox();
        
        if (this.config.enabled) {
        	this.enable();
        }
    };
    
    /* Plugin registration */
    CmsEditor.prototype.registerPlugin = function(name, contructor) {
        this.plugingRegistry[name] = contructor;
        this.buildPluginSelector();
    };
    
    /* Plugin selector */
    CmsEditor.prototype.buildPluginSelector = function() {
    	var $selector = this.$box.find('#cms-editor-plugin-type');
    	$selector.empty();
    	for(var type in this.plugingRegistry) {
    		var $option = $('<option></option>');
    		$option.text(this.plugingRegistry[type].title);
    		$option.attr('value', type);
    		$selector.append($option);
    	}
    };
    
    /* Creates the control box */
    CmsEditor.prototype.createControlBox = function () {
    	var self = this;
        this.$box = $('.cms-editor-box');
        this.$box.find('.cms-editor-busy').hide();
        this.$box.find('.cms-editor-save').hide();
        this.$box.find('.cms-editor-enable').on('click', function(e) {
        	if(self.isBusy()) return;
            if (self.$box.hasClass('enabled')) {
                self.disable();
            } else {
                self.enable();
            }
        });
        this.$box.find('.cms-editor-pin').on('click', function(e) {
        	if(self.isBusy()) return;
        	if (self.$box.hasClass('pinned')) {
        		self.$box.removeClass('pinned');
        	} else {
        		self.$box.addClass('pinned').removeAttr('style');
        		CmsEditor.dragging = false;
        	}
        });
        this.$box.find('.cms-editor-save').on('click', function(e) {
        	if(self.isBusy()) return;
        	if (self.hasUpdatedColumns()) {
        		self.request();
        	}
        });
        this.$box.find('.cms-editor-head').on('mousedown', function(e) {
        	if (e.target == e.delegateTarget && !(self.$box.hasClass('pinned'))) {
        		var offset = self.$box.offset();
        	    CmsEditor.dragOffsetX = e.pageX - offset.left;
        	    CmsEditor.dragOffsetY = e.pageY - offset.top;
        		CmsEditor.dragging = true;
        	}
        });
    };
    
    /* Busy state */
    CmsEditor.prototype.setBusy = function(bool) {
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
    CmsEditor.prototype.isBusy = function() {
    	return this.busy;
    };
    
    /* Enables the editor. */
    CmsEditor.prototype.enable = function() {
        this.setEnabled(true);
        this.$box.addClass('enabled');
        this.$container.addClass('enabled');
        this.setBusy(false);
    };
    
    /* Disables the editor. */
    CmsEditor.prototype.disable = function() {
    	if (this.isBusy()) return;
    	this.selectColumn(null);
    	this.setEnabled(false);
        this.$box.removeClass('enabled');
        this.$container.removeClass('enabled');
        this.clearHandlers();
        this.updateControlsStates();
    };
    
    /* Removes events handlers. */
    CmsEditor.prototype.clearHandlers = function() {
        this.$box.off('click');
    };
    
    /* Initializes the events handlers*/
    CmsEditor.prototype.initHandlers = function() {

    	var self = this;
    	
        /* New row button */
        this.$box.on('click', '.cms-editor-row-add:not(:disabled)', function(e) {
            self.insertNewRow();
        });
        /* Remove row */
        this.$box.on('click', '.cms-editor-row-remove:not(:disabled)', function(e) {
        	if(confirm("Êtes-vous sûr de vouloir supprimer cette ligne ?\nLes contenus des colonnes seront perdus.")) {
        		self.removeRow();
        	}
        });
        /* Insert row before */
        this.$box.on('click', '.cms-editor-row-insert-before:not(:disabled)', function(e) {
            self.insertRowBefore();
        });
        /* Insert row after */
        this.$box.on('click', '.cms-editor-row-insert-after:not(:disabled)', function(e) {
            self.insertRowAfter();
        });
        /* Move row up  */
        this.$box.on('click', '.cms-editor-row-move-up:not(:disabled)', function(e) {
            self.moveRowUp();
        });
        /* Move row down  */
        this.$box.on('click', '.cms-editor-row-move-down:not(:disabled)', function(e) {
            self.moveRowDown();
        });
        
        /* New column button */
        this.$box.on('click', '.cms-editor-column-add:not(:disabled)', function(e) {
            self.insertNewColumn();
        });
        /* Remove column */
        this.$box.on('click', '.cms-editor-column-remove:not(:disabled)', function(e) {
        	if(confirm("Êtes-vous sûr de vouloir supprimer cette colonne ?\nLe contenu sera perdu.")) {
        		self.removeColumn();
        	}
        });
        /* Insert column before */
        this.$box.on('click', '.cms-editor-column-insert-before:not(:disabled)', function(e) {
            self.insertColumnBefore();
        });
        /* Insert column after */
        this.$box.on('click', '.cms-editor-column-insert-after:not(:disabled)', function(e) {
            self.insertColumnAfter();
        });
        /* Move column left  */
        this.$box.on('click', '.cms-editor-column-move-left:not(:disabled)', function(e) {
            self.moveColumnLeft();
        });
        /* Move column right  */
        this.$box.on('click', '.cms-editor-column-move-right:not(:disabled)', function(e) {
            self.moveColumnRight();
        });
        /* Move column up  */
        this.$box.on('click', '.cms-editor-column-move-up:not(:disabled)', function(e) {
        	self.moveColumnUp();
        });
        /* Move column down  */
        this.$box.on('click', '.cms-editor-column-move-down:not(:disabled)', function(e) {
        	self.moveColumnDown();
        });
        /* Grow up column  */
        this.$box.on('click', '.cms-editor-column-grow:not(:disabled)', function(e) {
            self.growUpColumn();
        });
        /* Reduce column  */
        this.$box.on('click', '.cms-editor-column-reduce:not(:disabled)', function(e) {
            self.reduceColumn();
        });
        
        /* Plugin type selector */
        this.$box.on('change', '#cms-editor-plugin-type', function(e) {
        	if (self.current !== null) {
        		var $selector = self.$box.find('#cms-editor-plugin-type');
        		var type = self.current.$column.data('type');
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
    
    CmsEditor.prototype.request = function (datas, callback) {
    	this.setBusy(true);
    	var self = this;
    	datas = typeof datas === "object" ? datas : {};
    	callback = typeof callback === "function" ? callback : function() {};
    	if (this.hasUpdatedColumns) {
    		datas.layout = this.getUpdatedColumns();
    	}
    	$.ajax(Routing.generate('ekyna_cms_editor_request', {'contentId': this.config.contentId}), {
    		data: datas,
    		dataType: 'json',
    		type: 'POST'
		})
		.done(function (data) {
			callback(data);
			self.clearUpdatedColumns();
		})
    	.fail(function() {
    		this.log('An error occured.');
    	})
		.always(function() {
			self.setBusy(false);
		});
    };
    
    CmsEditor.prototype.log = function(msg) {
    	if (CmsEditor.debug) console.log(msg);
    };
    
    /* Selects a column */
    CmsEditor.prototype.selectColumn = function ($column) {
    	if (this.isBusy()) return;
        if (this.current !== null) {
        	if (this.current.plugin.isUpdated()) {
        		if(confirm('Enregistrer les modifications ?')) {
        			var self = this;
        			var plugin = this.current.plugin;
        			this.request({
        				updateBlock: $.extend(this.current.$column.data(), this.current.plugin.getDatas())
        			}, function (data) {
        				plugin.setUpdated(false);
        				self.selectColumn($column);
        			});
        		} else {
        			this.current.plugin.focus();
        			return;
        		}
        	}
            this.current.$row.removeClass('cms-editor-selected-row');
            this.current.$column.removeClass('cms-editor-selected-column');
            this.current.plugin.destroy();
        }
        this.current = null;
        if ($column !== null && $column.length == 1) {
            var type = $column.data('type');
            if(type in this.plugingRegistry) {
            	this.$box.find('#cms-editor-plugin-type').val(type);
                var plugin = new this.plugingRegistry[type]($column);
                this.current = {
                    $column: $column.addClass('cms-editor-selected-column'),
                    $row: $column.parents('.row').eq(0).addClass('cms-editor-selected-row'),
                    plugin: plugin
                };
                this.log($column.data());
                plugin.init();
            } else {
                this.log('"' + type + '" plugin is not registered.');
            }
        }
        this.updateControlsStates();
    };
    
    /* Updates controls states */
    CmsEditor.prototype.updateControlsStates = function() {
        
        /* Disables all */
        this.$box.find('.cms-editor-group button').prop('disabled', true);
        
        /* End if busy */
        if (this.isBusy()) return;
        
        /* Save layout button */
        if (this.hasUpdatedColumns()) {
        	this.$box.find('.cms-editor-save').prop('disabled', false).show();
        } else {
        	this.$box.find('.cms-editor-save').prop('disabled', true).hide();
        }
        
        /* New row */
        this.$box.find('.cms-editor-row-add').prop('disabled', false);
        
        if (this.current !== null) {
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
            if (this.current.$column.is(':not(:first-child)')) {
                this.$box.find('.cms-editor-column-move-left').prop('disabled', false);                
            }
            /* Move column right */
            if (this.current.$column.is(':not(:last-child)')) {
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
    };
    
    /* Creates a new row */
    CmsEditor.prototype.createNewRow = function(rowDatas, colDatas) {
    	rowDatas = typeof rowDatas === "object" ? rowDatas : {};
    	colDatas = typeof colDatas === "object" ? colDatas : {}; //this.config.columnDefaultData;
    	
    	rowDatas = $.extend(this.config.rowDefaultData, rowDatas);
    	colDatas = $.extend(this.config.columnDefaultData, colDatas, {row: rowDatas.num});
    	
    	var $newRow = $(this.config.rowCreateHtml).data(rowDatas);
    	$newRow.append(this.createNewColumn(colDatas));
    	return $newRow;
    };
    
    /* Creates a new column */
    CmsEditor.prototype.createNewColumn = function(colDatas) {
    	var colType = this.$box.find('#cms-editor-plugin-type').val();
    	if(!(colType in this.plugingRegistry)) {
    		this.log('Undefined type.');
    		return false;
    	}
    	colDatas = typeof colDatas === "object" ? colDatas : {};
    	colDatas = $.extend(this.config.columnDefaultData, colDatas, {type: colType});
    	
    	var self = this;
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
				self.selectColumn($newColumn);
			} else {
				self.log('Column creation failed.');
			}
		});
    	
        return $newColumn;
    };
    
    /* Inserts a new row */
    CmsEditor.prototype.insertNewRow = function() {
        var $newRow = this.createNewRow({
        	num: parseInt(this.$container.find(this.config.rowSelector + ':last-child').data('num')) + 1
        });
        this.$container.append($newRow);
    };
    
    /* Removes the current row */
    CmsEditor.prototype.removeRow = function() {
        if (this.current === null) {
        	return;
        }
    	var self = this;
    	var $row = this.current.$row;
    	
    	/* Unselect current */
    	this.current.$row.removeClass('cms-editor-selected-row');
        this.current.$column.removeClass('cms-editor-selected-column');
        this.current.plugin.destroy();
    	
    	var removeDatas = [];
    	this.current.$row.find(this.config.columnSelector).each(function(i, column) {
    		removeDatas.push($(column).data());
    	});
    	
    	/* Decrement num of rows after deletion */
    	var rowNum = this.current.$row.data('num');
    	this.$container.find(this.config.rowSelector).each(function(i, row) {
    		var $row = $(row);
    		if ($row.data('num') > rowNum) {
    			$row.data('num', parseInt($row.data('num')) - 1);
    			self.fixColumnsIndexes($row);
    		}
    	});
    	
    	/* Clear current */
    	this.current = null;

    	this.request({removeBlocks: removeDatas}, function(datas) {
    		$row.remove();
    		self.selectColumn(null);
    	});
    };
    
    /* Inserts a new row before the current one */
    CmsEditor.prototype.insertRowBefore = function() {
        if (this.current === null || this.current.$row.is(':first-child')) {
        	return;
        }
    	var self = this;
    	var rowNum = this.current.$row.data('num');

    	/* Increment num of rows after insertion */
    	this.$container.find(this.config.rowSelector).each(function(i, row) {
    		var $row = $(row);
    		if ($row.data('num') >= rowNum) {
    			$row.data('num', parseInt($row.data('num')) + 1);
    			self.fixColumnsIndexes($row);
    		}
    	});
        
        var $newRow = this.createNewRow({num: rowNum});
        this.current.$row.before($newRow);
    };
    
    /* Inserts a new row after the current one */
    CmsEditor.prototype.insertRowAfter = function() {
        if (this.current === null) {
        	return;
        }
    	var self = this;
    	var rowNum = parseInt(this.current.$row.data('num')) + 1;
    	
    	/* Increment num of rows after insertion */
    	this.$container.find(this.config.rowSelector).each(function(i, row) {
    		var $row = $(row);
    		if ($row.data('num') >= rowNum) {
    			$row.data('num', parseInt($row.data('num')) + 1);
    			self.fixColumnsIndexes($row);
    		}
    	});
    	
        var $newRow = this.createNewRow({num: rowNum});
        this.current.$row.after($newRow);
    };
    
    /* Move up the current row */
    CmsEditor.prototype.moveRowUp = function() {
        if (this.current !== null && this.current.$row.is(':not(:first-child)')) {
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
    CmsEditor.prototype.moveRowDown = function() {
        if (this.current === null || this.current.$row.is(':last-child')) {
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
    CmsEditor.prototype.insertNewColumn = function() {
    	if (this.current === null || this.current.$row.find(this.config.columnSelector).length == 12) {
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
    CmsEditor.prototype.removeColumn = function() {
        if (this.current === null || this.current.$row.find(this.config.columnSelector).length <= 1) {
        	return
        }
    	var $column = this.current.$column.detach();
    	this.fixColumnSize();
    	
    	/* Unselect current */
    	this.current.$row.removeClass('cms-editor-selected-row');
        this.current.$column.removeClass('cms-editor-selected-column');
        this.current.plugin.destroy();
    	this.current = null;
        
    	var self = this;
    	this.request({removeBlocks: [$column.data()]}, function(datas) {
    		$column.remove();
    		self.selectColumn(null);
    	});
    	/* TODO failure closure */
    };
    
    /* Inserts a new column before the current one */
    CmsEditor.prototype.insertColumnBefore = function() {
        if (this.current === null || this.current.$row.find(this.config.columnSelector).length == 12) {
            return;
        }
        var self = this;

        /* Sizing */
        var $sibling = this.current.$column;
        if ($sibling.data('size') == 1) {
            $sibling = this.getReduceableSibling();
        }
        var siblingSize = parseInt($sibling.data('size'));
        var newColSize = Math.floor(siblingSize / 2);
        siblingSize -= newColSize;
        this.setColumnSize($sibling, siblingSize);

        /* Increment next column's num */
        var colNum = this.current.$column.data('column');
        this.current.$row.find(this.config.columnSelector).each(function(i, column) {
        	var $column = $(column);
        	var cn = parseInt($column.data('column'));
        	if (cn >= colNum) {
        		$column.data('column', cn + 1);
        		self.addUpdatedColumn($column);
        	}
        });
        
        /* Create new column */
        var $newColumn = this.createNewColumn({
    		row: this.current.$row.data('num'),
    		column: colNum,
    		size: newColSize
    	});
        this.current.$column.before($newColumn);
    };
    
    /* Inserts a new column after the current one */
    CmsEditor.prototype.insertColumnAfter = function() {
        if (this.current === null || this.current.$row.find(this.config.columnSelector).length == 12) {
            return;
        }
        var self = this;
        
        /* Sizing */
        var $sibling = this.current.$column;
        if ($sibling.data('size') == 1) {
            $sibling = this.getReduceableSibling();
        }
        var siblingSize = parseInt($sibling.data('size'));
        var newColSize = Math.floor(siblingSize / 2);
        siblingSize -= newColSize;
        this.setColumnSize($sibling, siblingSize);
        
        /* Increment next column's num */
        var colNum = this.current.$column.data('column') + 1;
        this.current.$row.find(this.config.columnSelector).each(function(i, column) {
        	var $column = $(column);
        	var cn = parseInt($column.data('column'));
        	if (cn >= colNum) {
        		$column.data('column', cn + 1);
        		self.addUpdatedColumn($column);
        	}
        });
        
        /* Create new column */
        var $newColumn = this.createNewColumn({
    		row: this.current.$row.data('num'),
    		column: colNum,
    		size: newColSize
    	});
        this.current.$column.after($newColumn);
    };
    
    /* Move left the current column */
    CmsEditor.prototype.moveColumnLeft = function() {
        if (this.current === null || this.current.$column.is(':first-child')) {
        	return;
        }
    	var $prevCol = this.current.$column.prev(this.config.columnSelector);

    	/* Increment previous column's num */
    	$prevCol.data('column', parseInt($prevCol.data('column')) + 1);
    	this.addUpdatedColumn($prevCol);
    	
    	/* Decrement current column's num */
    	this.current.$column.data('column', parseInt(this.current.$column.data('column')) - 1);
    	this.addUpdatedColumn(this.current.$column);
    	
    	/* Move columns */
    	$prevCol.before(this.current.$column);
        this.updateControlsStates();
    };
    
    /* Move right the current column */
    CmsEditor.prototype.moveColumnRight = function() {
        if (this.current === null || this.current.$column.is(':last-child')) {
        	return;
        }
    	var $nextCol = this.current.$column.next(this.config.columnSelector);
    	
    	/* Decrement next column's num */
    	$nextCol.data('column', parseInt($nextCol.data('column')) - 1);
    	this.addUpdatedColumn($nextCol);
    	
    	/* Increment current column's num */
    	this.current.$column.data('column', parseInt(this.current.$column.data('column')) + 1);
    	this.addUpdatedColumn(this.current.$column);
    	
    	/* Move columns */
    	$nextCol.after(this.current.$column);
        this.updateControlsStates();
    };

    /* Move up the current column */
    CmsEditor.prototype.moveColumnUp = function() {
    	if (this.current === null || this.current.$row.is(':first-child')) {
    		return;
    	}
		var $prevRow = this.current.$row.prev(this.config.rowSelector);
		this.current.$column.detach().appendTo($prevRow);
		
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
    CmsEditor.prototype.moveColumnDown = function() {
    	if (this.current === null || this.current.$row.is(':last-child')) {
    		return;
    	}
		var $nextRow = this.current.$row.next(this.config.rowSelector);
		this.current.$column.detach().appendTo($nextRow);
		
		this.fixColumnSize($nextRow);
		this.fixColumnSize(this.current.$row);
		this.fixColumnsIndexes($nextRow);
		this.fixColumnsIndexes(this.current.$row);
		
		this.current.$row = $nextRow;
        this.updateControlsStates();
    };
    
    /* Grows up the current column */
    CmsEditor.prototype.growUpColumn = function() {
    	console.log('Grow up column');
        if (! this.isColumnGrowable()) return;
        var $sibling = this.getReduceableSibling();
        if($sibling !== null) {
            this.setColumnSize($sibling, parseInt($sibling.data('size')) - 1);
            this.setColumnSize(this.current.$column, parseInt(this.current.$column.data('size')) + 1);
            
            this.updateControlsStates();
        }
    };
    
    /* Reduces the current column */
    CmsEditor.prototype.reduceColumn = function() {
    	console.log('Reduce column');
        if (! this.isColumnReduceable()) return;
        var $sibling = this.getGrowableSibling();
        if($sibling !== null) {
	        this.setColumnSize($sibling, parseInt($sibling.data('size')) + 1);
	        this.setColumnSize(this.current.$column, parseInt(this.current.$column.data('size')) - 1);
	        this.updateControlsStates();
        }
    };
    
    /* Sets the column size */
    CmsEditor.prototype.setColumnSize = function($column, size, noUpdate) {
    	noUpdate = noUpdate || false;
		var classes = $column.attr('class');
		$column.attr('class', classes.replace(/col-\w{2}-\d+/, 'col-md-' + size));
		$column.data('size', parseInt(size));
		if(!noUpdate) this.addUpdatedColumn($column);
    };
    
    /* Fix the rows positions indexes */
    CmsEditor.prototype.fixRowsIndexes = function() {
    	var self = this;
    	self.$container.find(this.config.rowSelector).each(function(ri, row) {
    		var $row = $(row);
    		$row.data('num', ri + 1);
    		self.fixColumnsIndexes($row);
    	});
    };
    
    /* Fix the columns positions indexes for the given row */
    CmsEditor.prototype.fixColumnsIndexes = function($row) {
    	if (typeof $row === "undefined") {
    		if (this.current === null) return;
        	$row = this.current.$row;
    	}
    	
    	var self = this;
		$row.find(this.config.columnSelector).each(function(ci, column) {
			var $column = $(column);
			if ($column.data('row') != $row.data('num') || $column.data('column') != ci+1) {
				$column.data({row: $row.data('num'), column: ci+1});
				self.addUpdatedColumn($column);
			}
		});
    };
    
    /* Fix the size of given column */
    CmsEditor.prototype.fixColumnSize = function($row) {
        if (typeof $row === "undefined") {
        	if (this.current === null) return;
        	$row = this.current.$row;
        }
        
        var total = 0;
        var $columns = $row.find(this.config.columnSelector);
        $columns.each(function(i, column) {
            total += $(column).data('size');
        });
        var delta = total - 12;
        if (delta == 0) return;
        
        var editor = this;
        var avg = Math.ceil(Math.abs(delta) / $columns.length);
        
        if (delta > 0) {
            /* Reduce widests */
        	this.log('Reducing widests.');
            $columns.sort(function(a,b) {
                return $(a).data('size') == $(b).data('size') ? 0 : $(a).data('size') < $(b).data('size');
            }).each(function(i, column) {
                var $col = $(column);
                if(delta > 0) {
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
            $columns.sort(function(a,b) {
                return $(a).data('size') == $(b).data('size') ? 0 : $(a).data('size') > $(b).data('size');
            }).each(function(i, column) {
                var $col = $(column);
                if(delta > 0) {
                    if (avg > delta) avg = delta;
                    editor.setColumnSize($col, parseInt($col.data('size')) + avg);
                    delta -= avg;
                }
            });;
        }
    };
    
    /* Returns wether the current row's columns can be resized */
    CmsEditor.prototype.isRowResizeable = function() {
        if (this.current !== null 
            && this.current.$row.find(this.config.columnSelector).length > 1 
            && this.current.$row.find(this.config.columnSelector).length < 12) {
            return true;
        }
        return false;
    };
    
    /* Returns wether the current columns can be grown */
    CmsEditor.prototype.isColumnGrowable = function() {
        if(! this.isRowResizeable()) return false;
        var found = false;
        this.current.$column.siblings().each(function(i, column) {
            if($(column).data('size') > 1) found = true;
        });
        return found;
    };
    
    /* Returns wether the current columns can be reduced */
    CmsEditor.prototype.isColumnReduceable = function() {
        if (! this.isRowResizeable()) return false;
        if (this.current.$column.data('size') > 1) {
            return true;
        }
        return false;
    };
    
    /* Returns a reduceable column sibling of the current one. */
    CmsEditor.prototype.getReduceableSibling = function() {
        var $sibling = null;
        if (this.isRowResizeable()) {
            if (this.current.$column.is(':last-child')) {
                do {
                    $sibling = this.current.$column.prev(this.config.columnSelector);
                } while(1 == $sibling.length && 1 == $sibling.data('size'));
            } else {
                do {
                    $sibling = this.current.$column.next(this.config.columnSelector);
                } while(1 == $sibling.length && 1 == $sibling.data('size'));
                if (0 == $sibling.length || 1 == $sibling.data('size')) { 
                    do {
                        $sibling = this.current.$column.prev(this.config.columnSelector);
                    } while(1 == $sibling.length && 1 == $sibling.data('size'));
                }
            }
        }
        return $sibling;
    };
    
    /* Returns a "growable" column sibling of the current one. */
    CmsEditor.prototype.getGrowableSibling = function() {
        if (this.current.$column.is(':last-child')) {
            return this.current.$column.prev(this.config.columnSelector)
        } else {
            return this.current.$column.next(this.config.columnSelector);
        };
    };
    
    win.cmsEditor = new CmsEditor();
    
    
    /* Base plugin */
    function CmsPlugin($el) {
        this.$element = $el;
        this.name = 'CmsPlugin';
        var updated = false;
        
        this.setUpdated = function(bool) {
        	updated = bool;
        }
        this.isUpdated = function() {
        	return updated;
        }
    }
    CmsPlugin.title = 'none';
    CmsPlugin.prototype.init = function() {
        if (CmsEditor.debug) console.log(this.name + ' :: init');
    };
    CmsPlugin.prototype.destroy = function() {
    	if (CmsEditor.debug) console.log(this.name + ' :: destroy');
    };
    CmsPlugin.prototype.focus = function() {
    	if (CmsEditor.debug) console.log(this.name + ' :: focus');
    };
    CmsPlugin.prototype.getDatas = function() {
        return {};
    };

    win.CmsPlugin = CmsPlugin;
    
    
    $(win.document).ready(function() {
        cmsEditor.setContainer($('.cms-editor'));
    });
    
})(window, jQuery);