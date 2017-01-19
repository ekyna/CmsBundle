/// <reference path="../../../../../../../typings/index.d.ts" />

import * as Backbone from 'backbone';
import * as _ from 'underscore';
import Dispatcher from './dispatcher';
import {OffsetInterface, Button, Select, Toolbar, ToolbarView} from './ui';

/**
 * ViewportButtonConfig
 */
export interface ViewportButtonConfig {
    width: number
    height: number
    icon: string
    title: string
    name: string
    active:boolean
}

/**
 * LocaleChoiceConfig
 */
export interface LocaleChoiceConfig {
    name: number
    title: number
}

/**
 * MainToolbarAttributes
 */
export interface MainToolbarAttributes {
    id: string
    classes: Array<string>
}

/**
 * MainToolbarOptions
 */
export interface MainToolbarOptions {
    viewports: Array<ViewportButtonConfig>
    locales: Array<LocaleChoiceConfig>
}

/**
 * MainToolbar
 */
export class MainToolbar extends Toolbar {
    initialize(attributes:MainToolbarAttributes, options:MainToolbarOptions):void {
        super.initialize(attributes, options);

        // Power button
        this.addControl('default', new Button({
            name: 'power',
            title: 'Toggle editor on/off',
            size: 'md',
            theme: 'primary',
            icon: 'power',
            event: 'controls.power.click'
        }));

        // Reload button
        this.addControl('default', new Button({
            name: 'reload',
            title: 'Reload the page',
            size: 'md',
            theme: 'default',
            icon: 'reload',
            event: 'controls.reload.click',
            spinning: true
        }));

        // Viewport selector
        if (!options.viewports || 0 == options.viewports.length) {
            throw 'Viewport buttons are not configured';
        }
        _.forEach(options.viewports, (viewport:ViewportButtonConfig) => {
            this.addControl('viewport', new Button({
                name: viewport.name,
                title: viewport.title + (viewport.width ? ' (' + viewport.width + 'x' + viewport.height + ')' : ''),
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

        // Locale selector
        if (!options.locales || 0 == options.locales.length) {
            throw 'Locale buttons are not configured';
        }
        this.addControl('document', new Select({
            name: 'locale',
            title: 'Select the locale',
            event: 'controls.locale.change',
            choices: options.locales
        }));
        // Page selector
        this.addControl('document', new Select({
            name: 'page',
            title: 'Select the page',
            event: 'controls.page.change',
            choices: [],
            width: 250,
        }));
        // Edit page button
        this.addControl('document', new Button({
            name: 'edit-page',
            title: 'Edit the page',
            size: 'md',
            theme: 'warning',
            icon: 'edit-page',
            event: 'controls.edit_page.click'
        }));
        // New page button
        this.addControl('document', new Button({
            name: 'new-page',
            title: 'Create a new page',
            size: 'md',
            theme: 'success',
            icon: 'new-page',
            event: 'controls.new_page.click'
        }));
    }
}

/**
 * MainToolbarViewOptions
 */
interface MainToolbarViewOptions extends Backbone.ViewOptions<MainToolbar> {
    model: MainToolbar
}

/**
 * MainToolbarView
 */
export class MainToolbarView extends ToolbarView<MainToolbar> {
    template:(data?:Object) => string;

    constructor(options:MainToolbarViewOptions) {
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
            this.model.getGroup('viewport').get('controls')
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

    getLocaleSelect():Select {
        return (<Select>this.model.getControl('document', 'locale'));
    }

    getPageSelect():Select {
        return (<Select>this.model.getControl('document', 'page'));
    }

    getNewPageButton():Button {
        return (<Button>this.model.getControl('document', 'new-page'));
    }

    getEditPageButton():Button {
        return (<Button>this.model.getControl('document', 'edit-page'));
    }
}
