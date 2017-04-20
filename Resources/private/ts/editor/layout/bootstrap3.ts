/// <reference path="../../../../../../../../../assets/typings/index.d.ts" />

import 'jquery';
import {AdapterInterface, LayoutDataInterface} from '../document-manager';
import {Toolbar, ControlInterface, ToolbarView} from "../ui";

interface Device {
    name:string
    minWidth:number
}

export class Bootstrap3Adapter implements AdapterInterface {
    static devicesMap: Array<Device> = [
        {name: 'lg', minWidth: 1200},
        {name: 'md', minWidth: 992},
        {name: 'sm', minWidth: 768},
        {name: 'xs', minWidth: 0}
    ];

    private data:LayoutDataInterface;
    private $element:JQuery;
    private device: string;

    constructor(data:LayoutDataInterface, $element:JQuery) {
        this.data = data;
        this.$element = $element;
    }

    initialize():void {
        if (!this.$element.hasClass('cms-block')) {
            return;
        }

        let map: Array<Device> = JSON.parse(JSON.stringify(Bootstrap3Adapter.devicesMap)).reverse(),
            classes = (<string>this.$element.attr('class')).split(' '),
            bsData:any, previousBsData:any, key, device:Device, matches;

        previousBsData = {size: 12, offset: 0};
        for (let i in map) {
            device = map[i];
            bsData = this.data.hasOwnProperty(device.name) ? this.data[device.name] : previousBsData;

            // Resolve size
            for (key in classes) {
                if (classes.hasOwnProperty(key) &&
                    null !== (matches = new RegExp('col-' + device.name + '-(\\d+)').exec(classes[key]))
                ) {
                    matches = new RegExp('col-' + device.name + '-(\\d+)').exec(classes[key]);
                    bsData.size = parseInt(matches[1]);
                    break;
                }
            }

            // Resolve offset
            for (key in classes) {
                if (classes.hasOwnProperty(key) &&
                    null !== (matches = new RegExp('col-' + device.name + '-offset-(\\d+)').exec(classes[key]))
                ) {
                    bsData.offset = parseInt(matches[1]);
                    break;
                }
            }

            this.data[device.name] = bsData;

            previousBsData = JSON.parse(JSON.stringify(bsData));
        }
    }

    onResize(width: number, toolbar: ToolbarView<Toolbar>):void {
        this.device = null;

        let device: Device;
        for (let i in Bootstrap3Adapter.devicesMap) {
            if (Bootstrap3Adapter.devicesMap.hasOwnProperty(i)) {
                device = Bootstrap3Adapter.devicesMap[i];
                if (width >= device.minWidth) {
                    this.device = device.name;
                    break;
                }
            }
        }

        if (null === this.device) {
            throw 'Failed to resolve the device';
        }

        let control:ControlInterface,
            bsData = this.data.hasOwnProperty(this.device)
                ? this.data[this.device]
                : {size: 12, offset: 0};

        // Size
        if (control = toolbar.model.getControl('default', 'size')) {
            control.setValue(bsData.size);
        }
        // Offset
        if (control = toolbar.model.getControl('default', 'offset')) {
            control.setValue(bsData.offset);
        }

        toolbar.render();
    }

    setData(property: string, value: string): void {
        if (0 <= ['size', 'offset'].indexOf(property)) {
            let bsData:any = this.data.hasOwnProperty(this.device)
                ? this.data[this.device]
                : {size: 12, offset: 0};

            bsData[property] = value;

            this.data[this.device] = bsData;
        }
    }

    apply(data:LayoutDataInterface):void {
        if (!this.$element.hasClass('cms-block')) {
            return;
        }

        (<string>this.$element.attr('class')).split(' ').forEach((value) => {
            // Remove current device size class
            let matches = new RegExp('col-' + this.device + '-(\\d+)').exec(value);
            if (matches) {
                this.$element.removeClass(matches[0]);
            }
            // Remove current device offset class
            matches = new RegExp('col-' + this.device + '-offset-(\\d+)').exec(value);
            if (matches) {
                this.$element.removeClass(matches[0]);
            }
        });

        let bsData:any = data.hasOwnProperty(this.device)
            ? data[this.device]
            : {size: 12, offset: 0};

        this.$element.addClass('col-' + this.device + '-' + bsData.size);
        this.$element.addClass('col-' + this.device + '-offset-' + bsData.offset);
    }
}

export default Bootstrap3Adapter;
