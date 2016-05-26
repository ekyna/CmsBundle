/// <reference path="../../../../../../../typings/jquery/jquery.d.ts" />
/// <reference path="../../../../../../../typings/backbone/backbone.d.ts" />

interface StringMap {
    [key: string]: string|boolean;
}

class ViewportButton extends Backbone.Model {
    defaults(): StringMap {
        return {
            width: null,
            height: null,
            landscape: false,
            active: false,
        }
    }

    public activate():void {
        if (this.get('active')) {
            this.set('landscape', !this.get('landscape'));
        } else {
            this.set('active', true);
        }
    }

    public deactivate():void {
        this.set('false', true);
    }
}

class ViewportButtonView extends Backbone.View<ViewportButton> {

}

class Controls {
    private $element: JQuery;

    constructor($element: JQuery) {
        this.$element = $element;
    }

    public init(): void {

    }
}

export = Controls;
