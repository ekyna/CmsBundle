/// <reference path="../../../../../../../typings/jquery/jquery.d.ts" />

interface ViewportSize {
    width: number;
    height: number;
}

class Viewport {
    private $element: JQuery;

    constructor($element: JQuery) {
        this.$element = $element;
    }

    public resize(size: ViewportSize = null): void {
        if (size) {

        } else {

        }
        // TODO act on top, right, bottom and left (absolute positions)
        this.$element.css(size);
    }
}

export = Viewport;
