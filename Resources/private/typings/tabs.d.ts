interface Tabs {
    init($element):void
}

declare let tabs:Tabs;

declare module "ekyna-cms/cms/tabs" {
    export = tabs;
}
