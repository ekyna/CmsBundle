import RouteParams = FOS.RouteParams;

declare module 'ekyna-cms/editor/plugin/base-plugin' {
    export class BasePlugin {
        protected window:any;

        protected $element:JQuery;
        protected updated:boolean;
        protected destroyed:boolean;
        protected modal:Ekyna.Modal;

        constructor($element: JQuery, win: Window)

        isUpdated(): boolean

        edit(): void

        save(): Promise<any>

        destroy(): Promise<any>

        preventDocumentSelection($target: JQuery): boolean

        setUpdated(updated: boolean): void

        openModal(url: string, callback?: (e: Ekyna.ModalResponseEvent) => void): void
    }
}


declare module 'ekyna-cms/editor/document-manager' {
    interface ElementData {
        id: string
        type: string
        position: number
        actions: { [key: string]: any }
    }

    interface ElementAttributes {
        id: string
        data: ElementData
        [key: string]: any
    }

    interface WidgetData {
        attributes: ElementAttributes
        content: string
    }

    interface BlockData {
        attributes: ElementAttributes
        widgets: Array<WidgetData>
    }

    export class BlockManager {
        static parse(blocks: Array<BlockData>, $row?: JQuery)

        static generateUrl($block: JQuery, route: string, params?: RouteParams)

        static request($block: JQuery, route: string, params?: RouteParams, settings?: JQueryAjaxSettings): JQueryXHR

        static edit($block: JQuery)

        static changeType($block: JQuery, type: string)

        static remove($block: JQuery)

        static add($block: JQuery, type: string)

        static moveUp($block: JQuery)

        static moveDown($block: JQuery)

        static moveLeft($block: JQuery)

        static moveRight($block: JQuery)
    }
}

