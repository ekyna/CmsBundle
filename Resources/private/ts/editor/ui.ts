/// <reference path="../../../../../../../typings/index.d.ts" />

import * as $ from 'jquery';
import * as Backbone from 'backbone';
import * as _ from 'underscore';
import * as Bootstrap from 'bootstrap';

import Dispatcher from './dispatcher';


/**
 * OffsetInterface
 */
export interface OffsetInterface {
    top: number,
    left: number
}

interface ButtonChoiceConfig extends Backbone.ObjectHash {
    name: string
    title: string
    confirm: string
    event: string
    data: Object
}

interface ButtonConfig extends ButtonChoiceConfig {
    size: string
    theme: string
    icon: string
    active: boolean
    disabled: boolean
    spinning: boolean
    rotate: boolean
    choices: Array<ButtonChoiceConfig>
}

/**
 * Button
 */
export class Button extends Backbone.Model {
    defaults():ButtonConfig {
        return {
            name: null,
            title: null,
            size: 'sm',
            theme: 'default',
            icon: null,
            active: false,
            disabled: false,
            spinning: false,
            rotate: false,
            confirm: null,
            event: null,
            choices: [],
            data: {}
            //onClick:(button:Button) => {}
        }
    }

    validate(attributes: any, options?: any): any {
        attributes = attributes || this.attributes;

        if (0 == String(attributes.name).length) {
            throw 'Button.name is mandatory';
        }
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

    enable():Button {
        this.set('disabled', false);
        return this;
    }

    disable():Button {
        this.set('disabled', true);
        return this;
    }
}

/**
 * ButtonView
 */
export class ButtonView extends Backbone.View<Button> {
    template:(data: {icon: string}) => string;

    events():Backbone.EventsHash {
        return {
            'click': 'onClick'
        }
    }

    constructor(options?:Backbone.ViewOptions<Button>) {
        options.tagName = 'button';
        options.attributes = {
            type: 'button',
            'class': 'btn'
        };

        super(options);

        this.template = _.template('<span class="fa fa-<%= icon %>"></span>');

        //_.bindAll(this, 'render');
        this.listenTo(this.model, 'change', this.render);
    }

    onClick(e:JQueryEventObject):void {
        e.preventDefault();

        var dispatch = () => { Dispatcher.trigger(this.model.get('event'), this.model, this.model.get('data'));},
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
        this.$el
            .html(this.template({icon: this.model.get('icon')}))
            .addClass('btn-' + this.model.get('size'))
            .addClass('btn-' + this.model.get('theme'))
            .attr('title', this.model.get('title'));


        this.$el.prop('disabled', this.model.get('disabled'));
        //console.log(this.model.get('name'), this.model.get('active'));
        this.$el.toggleClass('active', this.model.get('active'));
        this.$el.toggleClass('rotate', this.model.get('rotate'));

        this.$('span').toggleClass('fa-spin', this.model.get('spinning'));

        return this;
    }
}

/**
 * ButtonDropdownView
 */
export class ButtonDropdownView extends Backbone.View<Button> {
    template:(data:ButtonConfig) => string;

    events():Backbone.EventsHash {
        return {
            'click li a': 'onClick'
        }
    }

    constructor(options?:Backbone.ViewOptions<Button>) {
        options.tagName = 'div';
        options.attributes = {
            'class': 'btn-group'
        };

        super(options);

        this.template = _.template(`
        <button type="button" class="btn btn-<%= theme %> btn-<%= size %> dropdown-toggle"
                title="<%= title %>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="fa fa-<%= icon %>"></span> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu"></ul>
        `);

        //_.bindAll(this, 'render');
        this.listenTo(this.model, 'change', this.render);
    }

    onClick(e:JQueryEventObject):void {
        e.preventDefault();

        var $target = $(e.target).closest('a');

        var choice:ButtonChoiceConfig = _.findWhere(
            (<Array<ButtonChoiceConfig>>this.model.get('choices')),
            {name: $target.data('choice')}
        );
        if (!choice) {
            throw 'Choice not found';
        }

        var dispatch = () => { Dispatcher.trigger(choice.event, this.model, choice.data); },
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

        var $ul:JQuery = this.$('ul');

        this.model.get('choices').forEach(function(choice:ButtonChoiceConfig) {
            var $a = $('<a></a>')
                .attr('href', 'javascript:void(0)')
                .data('choice', choice.name)
                .text(choice.title)
                .appendTo($ul);
            $('<li></li>').append($a).appendTo($ul);
        });

        return this;
    }
}

/**
 * ButtonGroup
 */
export class ButtonGroup extends Backbone.Model {
    defaults():Backbone.ObjectHash {
        return {
            name: null,
            buttons: new Backbone.Collection<Button>()
        }
    }

    /**
     * Adds the button
     * @param button
     * @returns ButtonGroup
     */
    addButton(button:Button):ButtonGroup {
        this.get('buttons').add(button);

        return this;
    }

    /**
     * Returns thee button by name
     * @param name
     * @returns Button
     */
    getButton(name:string):Button {
        return this.get('buttons').findWhere({name: name});
    }
}

/**
 * ButtonGroupView
 */
export class ButtonGroupView extends Backbone.View<ButtonGroup> {
    private subViews:Array<ButtonView>;

    constructor(options?:Backbone.ViewOptions<ButtonGroup>) {
        options.tagName = 'div';
        options.attributes = {
            'class': 'btn-group'
        };

        super(options);

        this.subViews = [];

        //_.bindAll(this, 'render');
        this.listenTo(this.model, 'add remove', this.render);
    }

    private clear():void {
        this.subViews.forEach((view:ButtonView) => view.remove());
    }

    render():ButtonGroupView {
        this.clear();

        this.model.get('buttons').each((button:Button) => {
            var view;
            if (0 < button.get('choices').length) {
                view = new ButtonDropdownView({model: button});
            } else {
                view = new ButtonView({model: button});
            }
            this.$el.append(view.render().$el);
            this.subViews.push(view);
        });

        return this;
    }

    remove():ButtonGroupView {
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
            groups: new Backbone.Collection<ButtonGroup>()
        }
    }

    /**
     * Returns whether the Toolbar has the group by name.
     * @param groupName
     * @returns boolean
     */
    hasGroup(groupName:string) {
        return -1 < this.get('groups').findIndex(function(group:ButtonGroup) {
            return group.get('name') === groupName;
        });
    }

    /**
     * Adds the group.
     * @param group
     * @returns Toolbar
     */
    addGroup(group:ButtonGroup):Toolbar {
        this.get('groups').add(group);
        return this;
    }

    /**
     * Returns the group by name.
     * @param name
     * @returns ButtonGroup
     */
    getGroup(name:string):ButtonGroup {
        return this.get('groups').findWhere({name: name});
    }

    /**
     * Adds the button
     * @param groupName
     * @param button
     * @returns Toolbar
     */
    addButton(groupName:string, button:Button):Toolbar {
        if (!this.hasGroup(groupName)) {
            this.addGroup(new ButtonGroup({name: groupName}));
        }
        this.getGroup(groupName).addButton(button);

        return this;
    }

    /**
     * Returns the button by name
     * @param groupName
     * @param buttonName
     * @returns Button|null
     */
    getButton(groupName:string, buttonName:string):Button {
        return this.getGroup(groupName).getButton(buttonName);
    }
}

/**
 * ToolbarView
 */
export class ToolbarView<T extends Toolbar> extends Backbone.View<T> {
    private subViews:Array<ButtonGroupView>;

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
        this.subViews.forEach((view:ButtonGroupView) => view.remove());
    }

    protected position(origin: OffsetInterface):void {
        var position:any = {};
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

        this.model.get('groups').each((group:ButtonGroup) => {
            var view = new ButtonGroupView({
                model: group
            });
            this.$el.append(view.render().$el);
            this.subViews.push(view);
        });

        this.position(this.model.get('origin'));

        return this;
    }

    postRender() {
        $('.dropdown-toggle').dropdown();
    }

    remove():ToolbarView<T> {
        this.clear();

        super.remove();

        return this;
    }
}
