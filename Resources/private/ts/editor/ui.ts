/// <reference path="../../../../../../../../assets/typings/index.d.ts" />

import * as $ from 'jquery';
import * as Backbone from 'backbone';
import * as _ from 'underscore';
import * as Bootstrap from 'bootstrap';
import 'select2';

$.fn.select2.defaults.set('theme', 'bootstrap');

//noinspection JSUnusedLocalSymbols
let bs = Bootstrap;

import Dispatcher from './dispatcher';
import SliderUIParams = JQueryUI.SliderUIParams;


export class Util {
    static addEditorParameterToUrl(url: string): string {
        let anchor: HTMLAnchorElement = <HTMLAnchorElement>document.createElement('a');
        anchor.href = url;

        // Parse search query string
        let params: Backbone.ObjectHash = {},
            seg: any = anchor.search.replace('?', '').split('&'),
            len: number = seg.length, i: number = 0, s: any;
        for (; i < len; i++) {
            if (!seg[i]) {
                continue;
            }
            s = seg[i].split('=');
            params[s[0]] = s[1];
        }

        // Add cms-editor-enable parameter if not exists
        if (!params.hasOwnProperty('cms-editor-enable')) {
            params['cms-editor-enable'] = 1;

            // Rebuild search query string
            seg = [];
            for (let k in params) {
                if (params.hasOwnProperty(k)) {
                    seg.push(k + '=' + params[k]);
                }
            }
            anchor.search = '?' + seg.join('&');
        }

        return anchor.href;
    }
}

/**
 * OffsetInterface
 */
export interface OffsetInterface {
    top: number,
    left: number
}

/**
 * ControlConfig
 */
const CONTROL_DEFAULTS: ControlConfig = {
    name: null,
    title: null,
    disabled: false
};

interface ControlConfig extends Backbone.ObjectHash {
    name: string
    title: string
    disabled?: boolean
}

export interface ControlInterface {
    getName():string
    getValue():any
    setValue(value: string, trigger?:boolean):void
}

/**
 * Control
 */
abstract class Control extends Backbone.Model implements ControlInterface {
    abstract createView(): ControlView<Control>

    static buildDefaults = function<T>(defaults?:any):T {
        return <T>_.extend(CONTROL_DEFAULTS, {
            key: (function (x: number) {
                let s = "";
                while (s.length < x && x > 0) {
                    let r = Math.random();
                    s += (r < 0.1 ? Math.floor(r * 100) : String.fromCharCode(Math.floor(r * 26) + (r > 0.5 ? 97 : 65)));
                }
                return s;
            }(8))
        }, defaults);
    };

    defaults(): ControlConfig {
        return Control.buildDefaults<ControlConfig>();
    }

    validate(attributes: any, options?: any): any {
        attributes = attributes || this.attributes;

        if (0 == String(attributes.name).length) {
            throw 'Control.name is mandatory';
        }
        if (0 == String(attributes.title).length) {
            throw 'Control.event is mandatory';
        }
    }

    enable(): Control {
        this.set('disabled', false);
        return this;
    }

    disable(): Control {
        this.set('disabled', true);
        return this;
    }

    getName() {
        return this.get('name');
    }

    getValue() {
        return this.get('value');
    }

    setValue(value: string, trigger:boolean = false):void {
        this.set('value', value);
    }
}

/**
 * ControlView
 */
abstract class ControlView<T extends Backbone.Model> extends Backbone.View<T> {

}

/**
 * ButtonChoiceConfig
 */
export interface ButtonChoiceConfig extends ControlConfig {
    confirm?: string
    data: any
}

/**
 * ButtonConfig
 */
export interface ButtonConfig extends ControlConfig {
    size: string
    theme: string
    icon: string
    active: boolean
    confirm: string
    event: string
    data: any
    spinning: boolean
    rotate: boolean
    choices: Array<ButtonChoiceConfig>
}

/**
 * Button
 */
export class Button extends Control {
    createView(): ControlView<Button> {
        if (0 < this.get('choices').length) {
            return new ButtonDropdownView({model: this});
        }
        return new ButtonView({model: this});
    }

    defaults(): ButtonConfig {
        return Control.buildDefaults<ButtonConfig>({
            size: 'sm',
            theme: 'default',
            icon: null,
            active: false,
            spinning: false,
            rotate: false,
            confirm: null,
            event: null,
            choices: [],
            data: {}
        });
    }

    validate(attributes: any, options?: any): any {
        super.validate(attributes, options);

        attributes = attributes || this.attributes;

        if (0 == String(attributes.event).length) {
            throw 'Button.event is mandatory';
        }
    }

    activate(): Button {
        this.set('active', true);
        return this;
    }

    deactivate(): Button {
        this.set('active', false);
        return this;
    }

    startSpinning(): Button {
        this.set('spinning', true);
        return this;
    }

    stopSpinning(): Button {
        this.set('spinning', false);
        return this;
    }
}

/**
 * ButtonView
 */
export class ButtonView extends ControlView<Button> {
    template: (data: Partial<ButtonConfig>) => string;

    events(): Backbone.EventsHash {
        return {
            'click': 'onClick'
        }
    }

    constructor(options?: Backbone.ViewOptions<Button>) {
        options.tagName = 'span';
        options.attributes = {
            'class': 'input-group-btn'
        };

        super(options);

        this.template = _.template(`
        <button type="button" class="btn btn-<%= theme %> btn-<%= size %>" title="<%= title %>">
          <span class="cei cei-<%= icon %>"></span>
        </button>
        `);

        //_.bindAll(this, 'render');
        this.listenTo(this.model, 'change', this.render);
    }

    onClick(e: JQueryEventObject): void {
        e.preventDefault();

        if (this.model.get('disabled')) {
            return;
        }

        let dispatch = () => {
                Dispatcher.trigger(this.model.get('event'), this.model, e);
            },
            message: string = this.model.get('confirm');
        if (message && 0 < message.length) {
            if (confirm(message)) {
                dispatch();
            }
        } else {
            dispatch();
        }
    }

    render(): this {
        this.$el.html(this.template(this.model.attributes));

        this.$('button')
            .prop('disabled', this.model.get('disabled'))
            .toggleClass('active', this.model.get('active'))
            .toggleClass('rotate', this.model.get('rotate'))
            .find('span').toggleClass('cei-spin', this.model.get('spinning'));

        Dispatcher.trigger('ui.control.render', this);

        return this;
    }
}

/**
 * ButtonDropdownView
 */
export class ButtonDropdownView extends ControlView<Button> {
    template: (data: Partial<ButtonConfig>) => string;

    events(): Backbone.EventsHash {
        return {
            'click li a': 'onClick'
        }
    }

    constructor(options?: Backbone.ViewOptions<Button>) {
        options.tagName = 'span';
        options.attributes = {
            'class': 'input-group-btn'
        };

        super(options);

        this.template = _.template(`
        <button type="button" class="btn btn-<%= theme %> btn-<%= size %> dropdown-toggle"
                title="<%= title %>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="cei cei-<%= icon %>"></span> 
        </button>
        <ul class="dropdown-menu"></ul>
        `); // <span class="caret"></span>

        this.listenTo(this.model, 'change', this.render);
    }

    onClick(e: JQueryEventObject): void {
        e.preventDefault();

        if (this.model.get('disabled')) {
            return;
        }

        let $target = $(e.target).closest('a');

        let choice: ButtonChoiceConfig = _.findWhere(
            (<Array<ButtonChoiceConfig>>this.model.get('choices')),
            {name: $target.data('choice')}
        );
        if (!choice) {
            throw 'Choice not found';
        }

        let dispatch = () => {
                Dispatcher.trigger(this.model.get('event'), this.model, choice);
            },
            message: string = choice.confirm;
        if (message && 0 < message.length) {
            if (confirm(message)) {
                dispatch();
            }
        } else {
            dispatch();
        }

    }

    render(): this {
        this.$el.html(this.template(this.model.attributes));

        this.$('button')
            .prop('disabled', this.model.get('disabled'))
            .toggleClass('active', this.model.get('active'))
            .toggleClass('rotate', this.model.get('rotate'))
            .find('span').toggleClass('cei-spin', this.model.get('spinning'));

        let $ul: JQuery = this.$('ul');

        this.model.get('choices').forEach(function (choice: ButtonChoiceConfig) {
            let $a = $('<a></a>')
                .attr('href', 'javascript:void(0)')
                .data('choice', choice.name)
                .text(choice.title)
                .appendTo($ul);
            $('<li></li>').append($a).appendTo($ul);
        });

        Dispatcher.trigger('ui.control.render', this);

        return this;
    }
}


/**
 * SliderConfig
 */
interface SliderConfig extends ControlConfig {
    value: string
    event: string
    min: number
    max: number
}

export class Slider extends Control {
    defaults(): SliderConfig {
        return Control.buildDefaults<SliderConfig>({
            value: 1,
            min: 1,
            max: 12,
        });
    }

    initialize(attributes?: SliderConfig, options?: any): void {

    }

    createView(): SliderView {
        return new SliderView({model: this});
    }

    validate(attributes: any, options?: any): any {
        super.validate(attributes, options);

        attributes = attributes || this.attributes;

        if (0 == String(attributes.event).length) {
            throw 'Slider.event is mandatory';
        }
    }

    setValue(value: string, trigger:boolean = false) {
        this.set('value', value);

        if (trigger) {
            Dispatcher.trigger(this.get('event'), this);
        }
    }
}

export class SliderView extends ControlView<Slider> {
    template: (data: Partial<SliderConfig>) => string;

    events(): Backbone.EventsHash {
        return {
            'change input': 'onInputChange'
        }
    }

    constructor(options?: Backbone.ViewOptions<Slider>) {
        options.tagName = 'span';
        options.attributes = {
            'class': 'input-group-slider'
        };

        super(options);

        this.template = _.template(`
            <label for="<%= key %>-input"><%= title %></label>
            <input id="<%= key %>-input" name="<%= name %>" value="<%= value %>" 
                   type="number" min="<%= min %>" max="<%= max %>">
            <div>
              <div id="<%= key %>-slider" class="slider"></div>
            </div>
        `);

        this.listenTo(this.model, 'change', this.onModelChange);
    }

    onModelChange(): void {
        this.$('.slider').slider({value: this.model.getValue()});
    }

    onInputChange(e: JQueryEventObject): void {
        e.preventDefault();

        this.model.setValue(<string>this.$('input').val(), true);
    }

    render(): this {
        this.$el.html(this.template(this.model.attributes));

        let $input:JQuery = this.$('input');

        this.$('.slider').slider({
            value: this.model.get('value'),
            min: this.model.get('min'),
            max: this.model.get('max'),
            step: 1,
            slide: (event: Event, ui: SliderUIParams) => {
                $input.val(ui.value);
            },
            stop: () => {
                $input.trigger('change');
            }
        });

        Dispatcher.trigger('ui.control.render', this);

        return this;
    }
}


/**
 * SelectChoiceConfig
 */
export interface SelectChoiceConfig extends ControlConfig {
    value: string
    active: boolean
    data: any
}

/**
 * SelectConfig
 */
interface SelectConfig extends ControlConfig {
    value: string
    event: string
    maxWidth: number
    choices: Array<SelectChoiceConfig>
}

export class Select extends Control {
    defaults(): SelectConfig {
        return Control.buildDefaults<SelectConfig>({
            width: null,
            value: null,
            choices: [],
        });
    }

    initialize(attributes?: SelectConfig, options?: any): void {
        if (attributes.choices.length) {
            this.setChoices(attributes.choices);
        }
    }

    createView(): SelectView {
        return new SelectView({model: this});
    }

    validate(attributes: any, options?: any): any {
        super.validate(attributes, options);

        attributes = attributes || this.attributes;

        if (0 == String(attributes.event).length) {
            throw 'Button.event is mandatory';
        }
    }

    setChoices(choices: Array<SelectChoiceConfig>): Select {
        this.set('choices', choices);

        if (choices.length) {
            let activeChoice = (<SelectChoiceConfig>_.findWhere(choices, {active: true}));
            if (activeChoice) {
                this.setValue(activeChoice.value);
            } else {
                activeChoice = (<SelectChoiceConfig>_.findWhere(choices, {value: this.get('value')}));
                if (activeChoice) {
                    activeChoice.active = true;
                } else {
                    choices[0].active = true;
                    this.setValue(choices[0].value);
                }
            }
        }

        return this;
    }

    getActiveChoice(): SelectChoiceConfig {
        return (<SelectChoiceConfig>_.findWhere(this.get('choices'), {active: true}));
    }

    setValue(value: string, trigger: boolean = false):void {
        if (value == this.getValue()) {
            return;
        }

        let choice: SelectChoiceConfig;
        this.get('choices').forEach(function (c: SelectChoiceConfig) {
            if (c.value == value) {
                c.active = true;
                choice = c;
            } else {
                c.active = false;
            }
        });
        if (!choice) {
            throw 'Value "' + value + '" not found in select choices.';
        }

        this.set('value', value);

        if (trigger) {
            Dispatcher.trigger(this.get('event'), this);
        }
    }

    select(value:string) {
        this.setValue(value);
    }
}

export class SelectView extends ControlView<Select> {
    template: (data: Partial<SelectConfig>) => string;

    events(): Backbone.EventsHash {
        return {
            'change select': 'onSelectChange'
        }
    }

    constructor(options?: Backbone.ViewOptions<Select>) {
        options.tagName = 'span';
        options.attributes = {
            'class': 'input-group-select'
        };

        super(options);

        this.template = _.template('<select class="form-control" name="<%= name %>" title="<%= title %>"></select>');

        this.listenTo(this.model, 'change', this.render);
    }

    onSelectChange(e: JQueryEventObject): void {
        e.preventDefault();

        this.model.setValue(<string>this.$('select').val(), true);
    }

    render(): this {
        this.$el.html(this.template(this.model.attributes));

        let $select = this.$('select')
            .empty()
            .prop('disabled', this.model.get('disabled'));
        let width = this.model.get('width');
        if (0 < width) {
            $select.removeAttr('style').css({'width': width});
        }

        this.model.get('choices').forEach(function (choice: SelectChoiceConfig) {
            $('<option></option>')
                .attr('value', choice.value)
                .prop('selected', choice.active)
                .prop('disabled', choice.disabled)
                .text(choice.title)
                .appendTo($select);
        });

        Dispatcher.trigger('ui.control.render', this);

        return this;
    }
}

/**
 * ControlGroup
 */
export class ControlGroup extends Backbone.Model {
    defaults(): Backbone.ObjectHash {
        return {
            name: null,
            controls: new Backbone.Collection<Control>()
        }
    }

    /**
     * Adds the controls
     * @param control
     * @returns ControlGroup
     */
    addControl(control: Control): ControlGroup {
        this.get('controls').add(control);

        return this;
    }

    /**
     * Returns the control by name
     * @param name
     * @returns Control
     */
    getControl(name: string): Control {
        return this.get('controls').findWhere({name: name});
    }
}

/**
 * ControlGroupView
 */
export class ControlGroupView extends Backbone.View<ControlGroup> {
    private subViews: Array<ControlView<Control>>;

    constructor(options?: Backbone.ViewOptions<ControlGroup>) {
        options.tagName = 'div';
        options.attributes = {
            'class': 'input-group'
        };

        super(options);

        this.subViews = [];

        this.listenTo(this.model, 'add remove', this.render);
    }

    private clear(): void {
        this.subViews.forEach((view: ControlView<Control>) => view.remove());
    }

    render(): this {
        this.clear();

        this.model.get('controls').each((control: Control) => {
            let view = control.createView();
            this.$el.append(view.render().$el);
            this.subViews.push(view);
        });

        return this;
    }

    remove(): this {
        this.clear();

        super.remove();

        return this;
    }
}

interface ToolbarData extends Backbone.ObjectHash {
    id: string,
    name: string,
    classes: Array<string>,
    origin: OffsetInterface
    groups: Backbone.Collection<ControlGroup>
}

/**
 * Toolbar
 */
export class Toolbar extends Backbone.Model {
    defaults(): ToolbarData {
        return {
            id: null,
            name: null,
            classes: ['vertical'],
            origin: <OffsetInterface>{top: 0, left: 0},
            groups: new Backbone.Collection<ControlGroup>()
        }
    }

    getName():string {
        return this.get('name');
    }

    /**
     * Returns whether the Toolbar has the group by name.
     * @param groupName
     * @returns boolean
     */
    hasGroup(groupName: string) {
        return -1 < this.get('groups').findIndex(function (group: ControlGroup) {
                return group.get('name') === groupName;
            });
    }

    /**
     * Adds the group.
     * @param group
     * @returns Toolbar
     */
    addGroup(group: ControlGroup): Toolbar {
        this.get('groups').add(group);
        return this;
    }

    /**
     * Returns the group by name.
     * @param name
     * @returns ControlGroup
     */
    getGroup(name: string): ControlGroup {
        return this.get('groups').findWhere({name: name});
    }

    /**
     * Adds the control
     * @param groupName
     * @param control
     * @returns Toolbar
     */
    addControl(groupName: string, control: Control): Toolbar {
        if (!this.hasGroup(groupName)) {
            this.addGroup(new ControlGroup({name: groupName}));
        }
        this.getGroup(groupName).addControl(control);

        return this;
    }

    /**
     * Returns the control by name
     * @param groupName
     * @param controlName
     * @returns Control|null
     */
    getControl(groupName: string, controlName: string): Control {
        if (!this.hasGroup(groupName)) {
            return null;
        }
        return this.getGroup(groupName).getControl(controlName);
    }
}

/**
 * ToolbarView
 */
export class ToolbarView<T extends Toolbar> extends Backbone.View<T> {
    private subViews: Array<ControlGroupView>;

    constructor(options?: Backbone.ViewOptions<T>) {
        options.tagName = 'div';
        options.attributes = {
            'class': 'editor-toolbar ' + options.model.get('classes').join(' ')
        };
        if (0 < String(options.model.get('id')).length) {
            _.extend(options.attributes, {id: options.model.get('id')});
        }

        super(options);

        this.subViews = [];
    }

    private clear(): void {
        this.subViews.forEach((view: ControlGroupView) => view.remove());
    }

    protected position(origin: OffsetInterface): void {
        let position: any = {};
        if (origin.left > (window.innerWidth / 2)) {
            this.$el.addClass('right').removeClass('left');
            position.right = window.innerWidth - origin.left;
        } else {
            this.$el.addClass('left').removeClass('right');
            position.left = origin.left;
        }
        if (origin.top > (window.innerHeight / 2)) {
            this.$el.addClass('bottom').removeClass('top');
            position.bottom = window.innerHeight - origin.top;
        } else {
            this.$el.addClass('top').removeClass('bottom');
            position.top = origin.top;
        }
        this.$el.removeAttr('style').css(position);
    }

    applyOriginOffset(origin: OffsetInterface): ToolbarView<T> {
        this.position({
            top: origin.top + this.model.get('origin').top,
            left: origin.left + this.model.get('origin').left,
        });

        // noinspection TypeScriptValidateTypes
        return this;
    }

    render(): this {
        this.clear();

        this.model.get('groups').each((group: ControlGroup) => {
            let view = new ControlGroupView({
                model: group
            });
            this.$el.append(view.render().$el);
            this.subViews.push(view);
        });

        this.position(this.model.get('origin'));

        return this.postRender();
    }

    postRender(): this {
        this.$('.dropdown-toggle').dropdown();
        this.$('select').select2({width: "resolve"});

        // noinspection TypeScriptValidateTypes
        return this;
    }

    remove(): this {
        this.clear();

        super.remove();

        // noinspection TypeScriptValidateTypes
        return this;
    }
}
