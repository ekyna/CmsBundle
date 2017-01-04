/// <reference path="../../../../../../../typings/index.d.ts" />

import * as $ from 'jquery';
import * as Backbone from 'backbone';
import * as _ from 'underscore';
import * as Bootstrap from 'bootstrap';
import * as Select2 from 'select2';

$.fn.select2.defaults.set('theme', 'bootstrap');

//noinspection JSUnusedLocalSymbols
let bs = Bootstrap;
//noinspection JSUnusedLocalSymbols
let s2 = Select2;

import Dispatcher from './dispatcher';


export class Util {
    static addEditorParameterToUrl(url:string):string {
        let anchor:HTMLAnchorElement = <HTMLAnchorElement>document.createElement('a');
        anchor.href = url;

        // Parse search query string
        let params:Backbone.ObjectHash = {},
            seg:any = anchor.search.replace('?','').split('&'),
            len:number = seg.length, i:number = 0, s:any;
        for (;i<len;i++) {
            if (!seg[i]) { continue; }
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
interface ControlConfig extends Backbone.ObjectHash {
    name: string
    title: string
    disabled: boolean
}

const CONTROL_DEFAULTS:ControlConfig = {
    name: null,
    title: null,
    disabled: false
};

/**
 * Control
 */
abstract class Control extends Backbone.Model {
    abstract createView():ControlView<Control>

    defaults():ControlConfig {
        return CONTROL_DEFAULTS;
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

    enable():Control {
        this.set('disabled', false);
        return this;
    }

    disable():Control {
        this.set('disabled', true);
        return this;
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
    confirm: string
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
    createView():ControlView<Button> {
        if (0 < this.get('choices').length) {
            return new ButtonDropdownView({model: this});
        }
        return new ButtonView({model: this});
    }

    defaults():ButtonConfig {
        return _.extend(CONTROL_DEFAULTS, {
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

    activate():Button {
        this.set('active', true);
        return this;
    }

    deactivate():Button {
        this.set('active', false);
        return this;
    }

    startSpinning():Button {
        this.set('spinning', true);
        return this;
    }

    stopSpinning():Button {
        this.set('spinning', false);
        return this;
    }
}

/**
 * ButtonView
 */
export class ButtonView extends ControlView<Button> {
    template:(data:ButtonConfig) => string;

    events():Backbone.EventsHash {
        return {
            'click': 'onClick'
        }
    }

    constructor(options?:Backbone.ViewOptions<Button>) {
        options.tagName = 'span';
        options.attributes = {
            'class': 'input-group-btn'
        };

        super(options);

        this.template = _.template(`
        <button type="button" class="btn btn-<%= theme %> btn-<%= size %>" title="<%= title %>">
          <span class="fa fa-<%= icon %>"></span>
        </button>
        `);

        //_.bindAll(this, 'render');
        this.listenTo(this.model, 'change', this.render);
    }

    onClick(e:JQueryEventObject):void {
        e.preventDefault();

        let dispatch = () => { Dispatcher.trigger(this.model.get('event'), this.model);},
            message:string = this.model.get('confirm');
        if (message && 0 < message.length) {
            if (confirm(message)) {
                dispatch();
            }
        } else {
            dispatch();
        }
    }

    render():ButtonView {
        this.$el.html(this.template(this.model.attributes));

        this.$('button')
            .prop('disabled', this.model.get('disabled'))
            .toggleClass('active', this.model.get('active'))
            .toggleClass('rotate', this.model.get('rotate'))
            .find('span').toggleClass('fa-spin', this.model.get('spinning'));

        Dispatcher.trigger('ui.control.render', this);

        return this;
    }
}

/**
 * ButtonDropdownView
 */
export class ButtonDropdownView extends ControlView<Button> {
    template:(data:ButtonConfig) => string;

    events():Backbone.EventsHash {
        return {
            'click li a': 'onClick'
        }
    }

    constructor(options?:Backbone.ViewOptions<Button>) {
        options.tagName = 'span';
        options.attributes = {
            'class': 'input-group-btn'
        };

        super(options);

        this.template = _.template(`
        <button type="button" class="btn btn-<%= theme %> btn-<%= size %> dropdown-toggle"
                title="<%= title %>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="fa fa-<%= icon %>"></span> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu"></ul>
        `);

        this.listenTo(this.model, 'change', this.render);
    }

    onClick(e:JQueryEventObject):void {
        e.preventDefault();

        let $target = $(e.target).closest('a');

        let choice:ButtonChoiceConfig = _.findWhere(
            (<Array<ButtonChoiceConfig>>this.model.get('choices')),
            {name: $target.data('choice')}
        );
        if (!choice) {
            throw 'Choice not found';
        }

        let dispatch = () => { Dispatcher.trigger(this.model.get('event'), this.model, choice); },
            message:string = choice.confirm;
        if (message && 0 < message.length) {
            if (confirm(message)) {
                dispatch();
            }
        } else {
            dispatch();
        }

    }

    render():ButtonView {
        this.$el.html(this.template(this.model.attributes));

        this.$('button')
            .prop('disabled', this.model.get('disabled'))
            .toggleClass('active', this.model.get('active'))
            .toggleClass('rotate', this.model.get('rotate'))
            .find('span').toggleClass('fa-spin', this.model.get('spinning'));

        let $ul:JQuery = this.$('ul');

        this.model.get('choices').forEach(function(choice:ButtonChoiceConfig) {
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
    defaults():SelectConfig {
        return _.extend(CONTROL_DEFAULTS, {
            width: null,
            value: null,
            choices: [],
        });
    }

    initialize(attributes?: SelectConfig, options?: any):void {
        if (attributes.choices.length) {
            this.setChoices(attributes.choices);
        }
    }

    createView():SelectView {
        return new SelectView({model: this});
    }

    validate(attributes: any, options?: any): any {
        super.validate(attributes, options);

        attributes = attributes || this.attributes;

        if (0 == String(attributes.event).length) {
            throw 'Button.event is mandatory';
        }
    }

    setChoices(choices:Array<SelectChoiceConfig>):Select {
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

        this.set('choices', choices);

        return this;
    }

    getActiveChoice():SelectChoiceConfig {
        return (<SelectChoiceConfig>_.findWhere(this.get('choices'), {active: true}));
    }

    private setValue(value:string):Select {
        if (value != this.getValue()) {
            this.set('value', value);
        }
        return this;
    }

    getValue():string {
        return this.get('value');
    }

    // TODO don't return bool
    select(value:string):boolean {
        if (value == this.get('value')) {
            return false;
        }

        let choice:SelectChoiceConfig;
        this.get('choices').forEach(function(c:SelectChoiceConfig) {
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

        return true;
    }
}

export class SelectView extends ControlView<Select> {
    template:(data:SelectConfig) => string;

    events():Backbone.EventsHash {
        return {
            'change select': 'onSelectChange'
        }
    }

    constructor(options?:Backbone.ViewOptions<Select>) {
        options.tagName = 'span';
        options.attributes = {
            'class': 'input-group-select'
        };

        super(options);

        this.template = _.template('<select class="form-control" name="<%= name %>" title="<%= title %>"></select>');

        this.listenTo(this.model, 'change', this.render);
    }

    onSelectChange(e:JQueryEventObject):void {
        e.preventDefault();

        this.model.select(this.$('select').val());

        Dispatcher.trigger(this.model.get('event'), this.model);
    }

    render():SelectView {
        this.$el.html(this.template(this.model.attributes));

        let $select = this.$('select')
            .empty()
            .prop('disabled', this.model.get('disabled'));
        let width = this.model.get('width');
        if (0 < width) {
            $select.removeAttr('style').css({'width': width});
        }

        this.model.get('choices').forEach(function(choice:SelectChoiceConfig) {
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
    defaults():Backbone.ObjectHash {
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
    addControl(control:Control):ControlGroup {
        this.get('controls').add(control);

        return this;
    }

    /**
     * Returns the control by name
     * @param name
     * @returns Control
     */
    getControl(name:string):Control {
        return this.get('controls').findWhere({name: name});
    }
}

/**
 * ControlGroupView
 */
export class ControlGroupView extends Backbone.View<ControlGroup> {
    private subViews:Array<ControlView<Control>>;

    constructor(options?:Backbone.ViewOptions<ControlGroup>) {
        options.tagName = 'div';
        options.attributes = {
            'class': 'input-group'
        };

        super(options);

        this.subViews = [];

        this.listenTo(this.model, 'add remove', this.render);
    }

    private clear():void {
        this.subViews.forEach((view:ControlView<Control>) => view.remove());
    }

    render():ControlGroupView {
        this.clear();

        this.model.get('controls').each((control:Control) => {
            let view = control.createView();
            this.$el.append(view.render().$el);
            this.subViews.push(view);
        });

        return this;
    }

    remove():ControlGroupView {
        this.clear();

        super.remove();

        return this;
    }
}

/**
 * Toolbar
 */
export class Toolbar extends Backbone.Model {
    defaults():Backbone.ObjectHash {
        return {
            id: null,
            classes: ['vertical'],
            origin: <OffsetInterface>{top: 0, left: 0},
            groups: new Backbone.Collection<ControlGroup>()
        }
    }

    /**
     * Returns whether the Toolbar has the group by name.
     * @param groupName
     * @returns boolean
     */
    hasGroup(groupName:string) {
        return -1 < this.get('groups').findIndex(function(group:ControlGroup) {
            return group.get('name') === groupName;
        });
    }

    /**
     * Adds the group.
     * @param group
     * @returns Toolbar
     */
    addGroup(group:ControlGroup):Toolbar {
        this.get('groups').add(group);
        return this;
    }

    /**
     * Returns the group by name.
     * @param name
     * @returns ControlGroup
     */
    getGroup(name:string):ControlGroup {
        return this.get('groups').findWhere({name: name});
    }

    /**
     * Adds the control
     * @param groupName
     * @param control
     * @returns Toolbar
     */
    addControl(groupName:string, control:Control):Toolbar {
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
    getControl(groupName:string, controlName:string):Control {
        return this.getGroup(groupName).getControl(controlName);
    }
}

/**
 * ToolbarView
 */
export class ToolbarView<T extends Toolbar> extends Backbone.View<T> {
    private subViews:Array<ControlGroupView>;

    constructor(options?:Backbone.ViewOptions<T>) {
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

    private clear():void {
        this.subViews.forEach((view:ControlGroupView) => view.remove());
    }

    protected position(origin: OffsetInterface):void {
        let position:any = {};
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

    applyOriginOffset(origin: OffsetInterface):ToolbarView<T> {
        this.position({
            top: origin.top + this.model.get('origin').top,
            left: origin.left + this.model.get('origin').left,
        });

        return this;
    }

    render():ToolbarView<T> {
        this.clear();

        this.model.get('groups').each((group:ControlGroup) => {
            let view = new ControlGroupView({
                model: group
            });
            this.$el.append(view.render().$el);
            this.subViews.push(view);
        });

        this.position(this.model.get('origin'));

        return this.postRender();
    }

    postRender():ToolbarView<T> {
        this.$('.dropdown-toggle').dropdown();
        this.$('select').select2({width: "resolve"});

        return this;
    }

    remove():ToolbarView<T> {
        this.clear();

        super.remove();

        return this;
    }
}
