/// <reference path="../../../../../../../typings/tsd.d.ts" />

import Backbone = require('backbone');
import _ = require('underscore');

import Dispatcher from './dispatcher';
import {Button, ButtonGroup, Toolbar, ToolbarView} from './ui';
import {OffsetInterface} from "./ui";

/**
 * Controls model
 */
export class MainToolbar extends Toolbar {
    initialize(attributes?:any, options?:any):void {
        super.initialize(attributes, options);

        this.addButton('default', new Button({
            name: 'power',
            title: 'Toggle editor on/off',
            size: 'md',
            theme: 'primary',
            icon: 'power-off',
            event: 'controls.power.click'
        }));
        this.addButton('default', new Button({
            name: 'reload',
            title: 'Reload the page',
            size: 'md',
            theme: 'default',
            icon: 'refresh',
            event: 'controls.reload.click',
            spinning: true
        }));

        if (options.viewports) {
            _.forEach(options.viewports, (viewport:{
                width: number; height: number, icon: string, title: string, name: string, active?:boolean
            }) => {
                this.addButton('viewport', new Button({
                    name: viewport.name,
                    title: viewport.title,
                    size: 'md',
                    icon: viewport.icon,
                    event: 'controls.viewport.click',
                    rotate: false,
                    active: viewport.active,
                    data: {
                        width: viewport.width,
                        height: viewport.height
                    }
                }));
            });
        }
    }
}

/**
 * Controls view
 */
export class MainToolbarView extends ToolbarView<MainToolbar> {
    template:(data?:Object) => string;

    constructor(options?:Backbone.ViewOptions<MainToolbar>) {
        super(options);

        this.template = _.template(`
            <div id="editor-control-main" class="btn-group"></div>
            <div id="editor-control-viewport" class="btn-group"></div>
        `);
    }

    initialize(options?:Backbone.ViewOptions<MainToolbar>) {
        // Power button click handler
        Dispatcher.on('controls.power.click', (button:Button) => {
            button.set('active', !button.get('active'));
        });

        // Viewport button click handler
        Dispatcher.on('controls.viewport.click', (button:Button) => {
            // Deactivate others buttons.
            this.model.getGroup('viewport').get('buttons')
                .reject(function(b:Button) {
                    return b == button;
                })
                .forEach((b:Button) => b.deactivate());

            if (button.get('active')) {
                if (button.get('name') != 'Adjust') {
                    button.set('rotate', !button.get('rotate'));
                }
            } else {
                button.set('active', true);
            }
        });
    }

    protected position(origin: OffsetInterface):void {
        // Prevent positioning
    }

    setBusy(e?:Event):void {
        if (e && e.defaultPrevented) {
            return;
        }
        this.model.getButton('default', 'reload').activate().startSpinning();
    }

    unsetBusy():void {
        this.model.getButton('default', 'reload').deactivate().stopSpinning();
    }
}
