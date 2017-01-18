/// <reference path="../../../../../../../../typings/index.d.ts" />

import 'jquery';
import {AdapterInterface, LayoutDataInterface} from "../document-manager";
import {Toolbar, ToolbarView, ControlInterface} from "../ui";

export class CommonAdapter implements AdapterInterface {
    private data:LayoutDataInterface;
    private $element:JQuery;

    constructor(data:LayoutDataInterface, $element:JQuery) {
        this.data = data;
        this.$element = $element;
    }

    initialize():void {
        this.data['padding_top'] = parseInt(this.$element.css('paddingTop'));
        this.data['padding_bottom'] = parseInt(this.$element.css('paddingBottom'));
    }

    onResize(width: number, toolbar: ToolbarView<Toolbar>):void {
        let control:ControlInterface;

        // Size
        if (control = toolbar.model.getControl('default', 'padding_top')) {
            control.setValue(this.data['padding_top']);
        }
        // Offset
        if (control = toolbar.model.getControl('default', 'padding_bottom')) {
            control.setValue(this.data['padding_bottom']);
        }
    }

    setData(property: string, value: string): void {
        if (0 <= ['padding_top', 'padding_bottom'].indexOf(property)) {
            this.data[property] = value;
        }
    }

    apply(data:LayoutDataInterface):void {
        this.$element.css({
            paddingTop: (data['padding_top'] ? data['padding_top'] + 'px' : '0px'),
            paddingBottom: (data['padding_bottom'] ? data['padding_bottom'] + 'px' : '0px'),
        });
    }
}
