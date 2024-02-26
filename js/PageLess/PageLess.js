/*!
 * Purpose: this simple framework manages all widgets creation in an async manner 
 * Version Release: 2.0
 * Created Date: March 22, 2023
 * Author(s):Enoch C. Jallah
 * Contact Mail: enochcjallah@gmail.com
*/
// "use esversion: 11";
export class PageLessComponent{
    constructor(name, details) {
        if(window.PageLessComponent === undefined)
            window.PageLessComponent = PageLessComponent;

        if (PageLessComponent.instances == undefined) {
            PageLessComponent.instances = [];
        }

        if (PageLessComponent.instances[name] == undefined) {
            PageLessComponent.instances[name] = details;
            
        }
        
        this.props                        = (details.props != undefined && typeof details.props == 'object') ? details.props : {};
        this.parseHTMLString              = details.view;
        this.callback                     = details.callback;
        this.isRendered                   = false;
        this.isPageLessComponent          = true;
        this.components                   = [];
        PageLessComponent.supportedEvents = ['onclick', 'onchange', 'onmouseenter', 'onmouseleave', 'onsubmit', 'onkeyup', 'onkeydown', 'onfocusin', 'onfocusout', 'onblur', 'onfocus', 'onscroll', 'onload'];
        this.creator                      = name.includes('table-row') ? document.createElement('tbody') : document.createElement('div');
        this.setRawData(details.data);
        this.creator.innerHTML            = this.parseHTMLString().trim();
        this.finalView                    = this.creator.firstChild;
        this.instanceTagName              = name;
        this.setRawProps(this.finalView);
        this.renderComponents(this.finalView);
        this.addToDOM(this.finalView);
        
        if(typeof this.finalView.callback == "function"){
            this.finalView.callback = this.finalView.callback.bind(this.finalView);
            this.finalView.callback();
        }
        
    }
    
    view(){
        return this.finalView;
    }

    ready(handler){
        if(typeof handler == "function" && this.isRendered === true)
            handler.call(this);
    }
    
    renderComponents(obj){
        return new Promise(resolve=>{
            this.getComponents(obj).then(components=>{
                components.forEach((component, index) =>{
                    this.getNewInstance(component, index).then(instance=>{
                        component.rendered                     = instance.setData(component.properties);
                        component.rendered.instanceTagName     = component.instanceName;
                        component.rendered.parentComponent     = obj;
                        component.rendered.allParentComponents = [];
                        let current = obj;
                        while (current != undefined) {
                            component.rendered.allParentComponents[current.instanceTagName] = current;
                            current = current.parentComponent;
                        }
                        component.rendered.isRendered      = true;
                        component.rendered.ready           = instance.ready.bind(component.rendered);
                        component.element.replaceWith(component.rendered);
                        component.rendered.setProps(component.rendered, component.events);
                        if(typeof component.rendered.callback == "function")
                            component.rendered.callback.call(component.rendered);
                    });
                });
            });

            resolve();
        });
    }

    parentComponents(name){
        let parent = this.allParentComponents[name];
        if (parent !== undefined) {
            return parent;
        } else {
            throw `{${name}} is not a parent of the child component {${this.instanceTagName}}`;
        }
    }

    static Render(component){
        let creator       = document.createElement('div');
        creator.innerHTML =  component.trim();
        let element       = creator.firstChild;
        let instanceName  = element.nodeName.toLowerCase();
        let instance      = window.PageLessComponent.instances[instanceName];
        let elemProps     = {};
        let events        = {};
        
        if (instance != undefined) {
            let  elemProp   = element.attributes;
            for (let i = 0; i < elemProp.length; i++) {
                elemProps[elemProp[i].name] =  elemProp[i].value;
                if(PageLessComponent.supportedEvents.includes(elemProp[i].name))
                {
                    let eventCheck  = PageLessComponent.propsIsEvent(element[elemProp[i].name]);
                    let stringEvent = (eventCheck !== false) ? eventCheck : element[elemProp[i].name];
                    if(typeof stringEvent == 'function' || typeof stringEvent == 'string')
                    {
                        events[elemProp[i].name] = stringEvent;
                    }
                }
            }
            let name        = `${instanceName}_${element.nodeName}_manual${0}`;
            let newInstance = new PageLessComponent(name, instance);
            delete PageLessComponent.instances[name];
            
            let rendered             = newInstance.setData(elemProps);
            rendered.parentComponent = null;
            rendered.isRendered      = true;
            rendered.ready           = newInstance.ready.bind(rendered);
            element.replaceWith(rendered);
            rendered.setProps(rendered, events);
            if(typeof rendered.callback == "function"){
                rendered.callback.call(rendered);
            }

            return rendered;
        } else {
            throw(`Invalid Component ${instanceName} provided. Unable to located component instance`);
        }
    }

    static propsIsEvent(propValue){
        let propPattern = /{{this\.props\.([a-z]+)}}/m;
        let prop        = false, matchingProp;
        if(propValue != null)
        {
            if(typeof propValue == 'function')
            {
                matchingProp    = propValue.toString().match(propPattern);
            }
            else if(typeof propValue == 'string')
            {
                matchingProp    = propValue.toString().match(propPattern);
            }

            if(matchingProp != null)
            {
                prop = matchingProp[1];
            }
        }

        return prop;
    }

    getComponents(obj){
        let components = [];
        return new Promise(resolve=>{
            for(const instance in window.PageLessComponent.instances){
                // console.log(instance);
                const elementInstances = obj.querySelectorAll(instance);
                if(elementInstances.length >= 1)
                {
                    for (const key in elementInstances) {
                        if (elementInstances.hasOwnProperty(key)) {
                            const element    = elementInstances[key];
                            const elemProps  = {};
                            const elemEvents = {};
                            const elemProp   = element.attributes;
                            for (let i = 0; i < elemProp.length; i++) {
                                elemProps[elemProp[i].name] =  elemProp[i].value;
                                if(PageLessComponent.supportedEvents.includes(elemProp[i].name))
                                {
                                    let eventCheck  = PageLessComponent.propsIsEvent(element[elemProp[i].name]);
                                    let stringEvent = (eventCheck !== false) ? eventCheck : element[elemProp[i].name];
                                    if(typeof stringEvent == 'function' || typeof stringEvent == 'string')
                                    {
                                        elemEvents[elemProp[i].name] = stringEvent;
                                    }
                                }
                            }

                            const elementObject = {
                                instanceName: instance,
                                element: element, 
                                properties: elemProps,
                                events: elemEvents
                            };
                            components.push(elementObject);
                        }
                    }
                }
            }

            resolve(components);
        });
    }

    getNewInstance(component, index){
        return new Promise(resolve=>{
            const name             = `${component.instanceName}_${component.element.nodeName}_${index}`;
            const pagelessInstance = new PageLessComponent(name, window.PageLessComponent.instances[component.instanceName]);
            delete PageLessComponent.instances[name];
            resolve(pagelessInstance);
        });
    }

    addToDOM(obj){
        return new Promise(resolve =>{
            for (const key in this)
            {
                if(this.hasOwnProperty(key)){
                    const property = this[key];

                    if(obj.hasOwnProperty(key) === false && (/^([0-9]+)$/).test(key) === false)
                    {
                        Object.defineProperty(obj, key, {value: property, writable: true});
                    }
                }
            }
    
            Object.defineProperty(obj, "props", {value: this.props, writable: true});
            Object.defineProperty(obj, "callback", {value: this.callback, writable: true});
            Object.defineProperty(obj, "parseHTMLString", {value: this.parseHTMLString, writable: true});
            Object.defineProperty(obj, "name", {value: this.name, writable: true});
            Object.defineProperty(obj, "components", {value: this.components, writable: true});
            Object.defineProperty(obj, "finalView", {value: obj, writable: true});
            Object.defineProperty(obj, "creator", {value: this.creator, writable: true});
            Object.defineProperty(obj, "isRendered", {value: this.isRendered, writable: true});
            Object.defineProperty(obj, "addToDOM", {value: this.addToDOM, writable: true});
            Object.defineProperty(obj, "getComponents", {value: this.getComponents, writable: true});
            Object.defineProperty(obj, "getNewInstance", {value: this.getNewInstance, writable: true});
            Object.defineProperty(obj, "renderComponents", {value: this.renderComponents, writable: true});
            Object.defineProperty(obj, "setData", {value: this.setData, writable: true});
            Object.defineProperty(obj, "setRawProps", {value: this.setRawProps, writable: true});
            Object.defineProperty(obj, "setProps", {value: this.setProps, writable: true});
            Object.defineProperty(obj, "parentComponents", {value: this.parentComponents, writable: true});
            Object.defineProperty(obj, "setRawData", {value: this.setRawData, writable: true});
            Object.defineProperty(obj, "refresh", {value: this.refresh, writable: true});
            Object.defineProperty(obj, "ready", {value: this.ready, writable: true});
            resolve();
        });
    }

    setRawProps(domElement, props = this.props){
        for(const prop in props){
            if(domElement.getAttribute(prop) != null && domElement.getAttribute(prop) != undefined)
            {
                const propValue = props[prop];
                if(PageLessComponent.supportedEvents.includes(prop) && domElement.props != undefined){
                    domElement.props[prop] = propValue;
                    domElement.addEventListener(prop.replace('on', ''), domElement.props[prop]);
                }else{
                    domElement[prop] = props[prop];
                }
            }
        }
    }

    setProps(domElement, props)
    {
        for(let prop in props){
            let propValue = props[prop];
            
            if(typeof propValue == 'function')
            {
                domElement.props[prop] = propValue.bind(domElement);
                domElement[prop]       = domElement.props[prop];
            }
            else{
                let currentParent    = domElement.parentComponent;
                let parentEventCheck = false; 
                while (currentParent != undefined) {
                    if (currentParent.props[propValue] != undefined) {
                        domElement.props[prop] = currentParent.props[propValue];
                        domElement.addEventListener(prop.replace('on', ''), domElement.props[prop]);
                        parentEventCheck = true;
                    }
                    currentParent = currentParent.parentComponent;
                }
                if(parentEventCheck === false)
                {
                    if(domElement.props[propValue] != undefined)
                    {
                        domElement.props[prop] = domElement.props[propValue].bind(domElement);
                        domElement.addEventListener(prop.replace('on', ''), domElement.props[prop]);

                    }
                    else{
                        throw `Prop ${propValue} handler not found`;
                    }
                }                    
            }
        }
    }

    static isJSONData(data){
        if (typeof data !== "string") {
            return false;
        }

        try {
            data = JSON.parse(data);
        } catch (e) {
            return false;
        }

        if (typeof data === "object" && data !== null) {
            return true;
        }

        return false;
    }

    setRawData(data){
        return new Promise(resolve =>{
            for(let key in data)
            {
                if(PageLessComponent.isJSONData(data[key]) === true){
                    data[key] = JSON.parse(data[key]);
                }
                
                this[key] = data[key];
            }
            resolve();
        });
    }

    setData(data)
    {

        this.setRawData(data);
        const oldElement       = this.finalView;
        this.creator.innerHTML = this.parseHTMLString().trim();
        let newView            = this.creator.firstChild;
        let neededProperties   = Object.getOwnPropertyNames(this);
        neededProperties.forEach((prop)=>{
            if(this.hasOwnProperty(prop))
            {
                if(newView.hasOwnProperty(prop) === false && (/^([0-9]+)$/).test(prop) === false)
                {
                    Object.defineProperty(newView, prop, {value: this[prop], writable: true});
                }
            }
        });

        this.setRawProps(newView);
        this.renderComponents(newView);
        this.addToDOM(newView);
        
        // callbackHandler.then(()=>{
        //     if(typeof newView.callback == "function")
        //     {
        //         newView.callback = newView.callback.bind(newView);
        //         newView.callback();
        //     }
        // });
        oldElement.replaceWith(newView);
        return newView;
    }
    refresh(callbackOnly = false)
    {
        if(callbackOnly === false)
        {
            let newView = this.setData({});
            if(typeof newView.callback == 'function')
                newView.callback.call(newView);
                
            return newView;
        }
        else{
            if(typeof this.callback == 'function')
                this.callback.call(this);

            return this.finalView;
        }
    }
}


export class PageLess{

    constructor(url = null) {
        this.mainContentContainer = document.querySelector('body');
        this.toastContainer       = this.mainContentContainer;
        this.entryAnimation       = 'fadeIn';
        this.exitAnimation        = 'fadeOut';
        this.Page404              = '404';
        this.modulesLocation      = "./modules";
        this.sharedModulesLocation= null;
        this.zIndex               = 1;
        this.widget               = null;
        this.data                 = null;
        this.urlData              = null;
        PageLess.appName          = null;
        this.url                  = url;
        this.routes               = []; 
        this.urlSearch            = {};
        this.previousURLs         = [];
    }

    route(rawUrl = this.url, setHistory = true) {

        if(window.PageLess == undefined){
            window.PageLess = PageLess;
        }

        if(PageLess.MainContentContainer == undefined){
            PageLess.MainContentContainer = this.mainContentContainer;
        }

        if(PageLess.AppName == undefined){
            PageLess.AppName = this.appName;
        }

        if(PageLess.Routes == undefined){
            PageLess.Routes = this.routes;
        }

        if(PageLess.ModulesLocation == undefined){
            PageLess.ModulesLocation = this.modulesLocation;
        }
        if(PageLess.SharedModulesLocation == undefined){
            PageLess.SharedModulesLocation = this.sharedModulesLocation;
        }

        if(PageLess.CurrentPage == undefined){
            PageLess.CurrentPage = 0;
        }
        
        if(PageLess.URLData == undefined){
            PageLess.URLData = [];
        }

        if(PageLess.URLSearchData == undefined){
            PageLess.URLSearchData = [];
        }

        if(PageLess.Pages == undefined){
            PageLess.Pages = [];
        }

        if(PageLess.Page404 == undefined){
            PageLess.Page404 = this.Page404;
        }

        if(PageLess.EntryAnimation == undefined){
            PageLess.EntryAnimation = this.entryAnimation;
        }

        if(PageLess.ExitAnimation == undefined){
            PageLess.ExitAnimation = this.exitAnimation;
        }

        if(PageLess.ToastContainer == undefined){
            PageLess.ToastContainer = this.toastContainer;
        }

        if (this.url === null) {
            this.url = rawUrl;
        }

        if(rawUrl != null) {
            PageLess.PrevURL = PageLess.GetURL('path');
            PageLess.setURL(PageLess.AppName, rawUrl);
            return new Promise(resolve =>{
                // if(PageLess.CurrentPage != 0 && rawUrl == PageLess.CurrentPage.finalView.PageLess.url) {
                //     resolve(PageLess.CurentPage);
                //     return;
                // }

                let existingPage = PageLess.Pages[rawUrl];
                if (existingPage == undefined) {
                    let check = false;
                    let url = rawUrl.split('?')[0];
                    PageLess.Routes.forEach(route =>{
                        if(route.routePattern.test(url)== true){
                            const fromShared = route.shared !== undefined && route.shared === true ? route.shared : false;
                            PageLess.URLSearchData[url] = {};
                            let urlData     = new URL(`${window.location.origin}${rawUrl}`);
                            urlData.searchParams.forEach((value, key)=>{
                                this.urlSearch[key]              = value;
                                PageLess.URLSearchData[url][key] = value;
                            });
                            PageLess.URLData[url] = route.routePattern.exec(url);
                            if (this[route.widget] !== undefined) {
                                this[route.widget]();
                                if(this.widget.isPageLessComponent) { 
                                    this.widget.finalView.PageLess = this;
                                    this.widget.finalView.urlData  = this.urlData;
                                    let oldSetData                 = this.widget.finalView.setData;
                                    this.widget.finalView.setData  = (data)=>{
                                        this.widget.finalView      = oldSetData.call(this.widget.finalView, data);
                                        this.setURLAttr();
                                        return this.widget.finalView;
                                        // PageLess.Display(this.widget.finalView, setHistory).then(widg=>resolve(widg));
                                    };
                                    
                                    this.setURLAttr();
                                    PageLess.Display(this.widget, setHistory).then(widg=>resolve(widg));
                                }
                                check = true;
                            } else{
                                PageLess.StartLoader();
                                PageLess.Import(route.widget, fromShared).then(widget=>{
                                    PageLess.StopLoader();
                                    this.widget = widget;
                                    if(this.widget.isPageLessComponent) {
                                        this.widget.finalView          = PageLess[route.widget] !== undefined ? widget.refresh() : this.widget.finalView; 
                                        PageLess[route.widget]         = this.widget;
                                        this.widget.finalView.PageLess = this;
                                        this.widget.finalView.urlData  = this.urlData;
                                        let oldSetData                 = this.widget.finalView.setData;
                                        this.widget.finalView.setData  = (data)=>{
                                            this.widget.finalView      = oldSetData.call(this.widget.finalView, data);
                                            this.setURLAttr();
                                            return this.widget.finalView;
                                            // PageLess.Display(this.widget.finalView, setHistory).then(widg=>{
                                            //     resolve(widg);
                                            // });
                                        };
                                        
                                        this.setURLAttr();
                                        PageLess.Display(this.widget, setHistory).then(widg=>resolve(widg));
                                    }
                                });
                                check = true; 
                            }
                        }
                    });
                    
    
                    if(check == false)
                    {
                        if (this[PageLess.Page404] !== undefined) {
                            this[PageLess.Page404]();
                            this.widget.finalView.PageLess = this;
                            this.setURLAttr();
                            PageLess.Display(this.widget).then((widg)=>resolve(widg));
                        } else{
                            PageLess.StartLoader();
                            PageLess.Import(PageLess.Page404).then(widget=>{
                                PageLess.StopLoader();
                                this.widget = widget;
                                if(this.widget.isPageLessComponent) { 
                                    this.widget.finalView.PageLess = this;
                                    let oldSetData                 = this.widget.finalView.setData;
                                    this.widget.finalView.setData  = (data)=>{
                                        this.widget.finalView      = oldSetData.call(this.widget.finalView, data);
                                        this.setURLAttr();
                                        return this.widget.finalView;
                                        // PageLess.Display(this.widget.finalView, setHistory).then(widg=>{
                                        //     resolve(widg);
                                        // });
                                    };
                                }
                                this.setURLAttr();
                                PageLess.Display(this.widget).then((widg)=>resolve(widg));
                            });
                        }

                    }
                } else {
                    this.widget = existingPage;
                    PageLess.Display(this.widget, setHistory).then(widg=>{
                        if (setHistory === true) {
                            let previousURLs                            = this.widget.finalView.PageLess.previousURLs;
                            this.widget.finalView                       = widg.refresh();
                            this.widget.finalView.PageLess              = this;
                            this.widget.finalView.PageLess.previousURLs = previousURLs;
                            let oldSetData                              = this.widget.finalView.setData;
                            this.widget.finalView.setData               = (data)=>{
                                this.widget.finalView                   = oldSetData.call(this.widget.finalView, data);
                                this.setURLAttr();
                                return this.widget.finalView;
                                // PageLess.Display(this.widget.finalView, setHistory).then(widg=>resolve(widg));
                            };
                        }
                        resolve(widg);
                    });
                }
            });
        }
    }

    static Display(widget, setHistory = true){
        return new Promise(resolve =>{
            if(widget.isPageLessComponent){
                let currentURL    = PageLess.PrevURL;
                let currentWidget = PageLess.CurrentPage;
                let checkPromise  = currentWidget !== 0 ? PageLess.Undisplay(currentWidget) : (new Promise(resolve=>{resolve();}));
                
                checkPromise.then(()=>{
                    PageLess.AddPage(widget).then(()=>{
                        widget.finalView.style.zIndex = 1;
                        widget.finalView.classList.add('animated', PageLess.EntryAnimation, 'faster');

                        if (setHistory === true && currentURL != widget.finalView.PageLess.url) {
                            widget.finalView.PageLess.setPreviousURL(currentURL);
                        }
                        PageLess.MainContentContainer.appendChild(widget.finalView);
                        let removeAnimation = ()=>{
                            widget.finalView.classList.remove('animated', PageLess.EntryAnimation, 'faster');
                            widget.finalView.removeEventListener('animationend', removeAnimation);
                        };
                        widget.finalView.addEventListener('animationend', removeAnimation);
                        resolve(widget);
                    });
                });
            }
        });
    }

    static Undisplay(widget){
        return new Promise(resolve =>{
            if(widget.isPageLessComponent){
                widget.finalView.style.zIndex = -1;
                // widget.finalView.classList.add('animated', PageLess.ExitAnimation, 'faster');
                // let removeWidget = ()=>{
                //     widget.finalView.classList.remove('animated', PageLess.ExitAnimation, 'faster');
                //     widget.finalView.removeEventListener('animationend', removeWidget);
                // };
                // widget.finalView.addEventListener('animationend', removeWidget);
                widget.finalView.remove();
                resolve(widget);
            }
        });
    }

    static Import(widgetName, fromShared = false){
        return new Promise(resolve=>{
            if (fromShared === false) {
                import(`${PageLess.ModulesLocation}/${widgetName}.js`).then(module => {
                    resolve(typeof module.widget == "function" ? module.widget() : module.widget);
                }).catch(error=>{
                    console.log(error)
                });
            } else {
                if (PageLess.SharedModulesLocation !== null) {
                    import(`${PageLess.SharedModulesLocation}/${widgetName}.js`).then(module => {
                        resolve(typeof module.widget == "function" ? module.widget() : module.widget);
                    }).catch(error=>{
                        console.log(error)
                    });
                } else {
                    console.error("Unable to load module. Shared modules location hasn't been set");
                }
            }
        });
    }

    setURLAttr(){
        // if(this.url != '/') {
            let rawUrl =  `${this.url}`;

            if(this.widget.isPageLessComponent){
                this.widget.finalView.setAttribute('url-title', this.appName);
                this.widget.finalView.setAttribute('url', rawUrl);
            }
        // }
    }

    setPreviousURL(url){
        if(this.widget.isPageLessComponent){
            this.widget.finalView.PageLess.previousURLs.push(url);
        }
    }

    static AddPage(widget){
        return new Promise(resolve =>{
            PageLess.Pages[widget.finalView.PageLess.url] = widget;
            PageLess.CurrentPage = widget;
            resolve(PageLess.Pages);
        });
    }
    
    static GetURL(returnedContent = 'full-url') {
        
        let url   = window.location;
        let path  = url.pathname;
        let paths = url.pathname.split('/');
        let value = null;
        
        switch (returnedContent) {
            case 'full-url':
                value =  url.href;
                break;
            case 'path':
                value = `${path}${url.search}`;
                break;
            case 'paths':
                value = paths;
                break;
            default:
                throw "Invalid Returned Content Provided";
        }

        return value;
    }

    static GetURLData() {
        
            const url   = window.location;
            const path  = url.pathname;
            const data  = PageLess.URLData[path];
            let   value = false; 
            if (data !== undefined) {
                value = data;
            }
            return value;
    }

    static GetURLSearchData() {
        
            const url   = window.location;
            const path  = url.pathname;
            const data  = PageLess.URLSearchData[path];
            let   value = false; 
            if (data !== undefined) {
                value = data;
            }
            return value;
    }

    static setURL(title, url){
        let obj = {
            pageTitle: title,
            pageUrl: url
        };
        history.pushState(obj, obj.pageTitle, obj.pageUrl);
        document.title = title;
    }

    /** returns the actual widget  */
    getContent(widgetName){
        return new Promise(resolve=>{
            if(Object.getPrototypeOf(this).hasOwnProperty(widgetName)){
                this[widgetName]();
                resolve(this.widget);
            }
        });
    }
    findById(widgetId){
        return new Promise(resolve =>{
            let content =  this.mainContentContainer.querySelector(`#${widgetId}`);
            if(content != undefined){
                this.widget = content;
                resolve(content);
            }
            else{
                resolve(false);
            }
        });
    }
    
    findByURL(url){
        return new Promise(resolve => {
            let children =  this.mainContentContainer.children();
            if(children.length >= 1 ) {
                let locatedElem = false;
                children.forEach(child => {
                    let childUrl = child.getAttribute('url');
                    if(childUrl == url){
                        locatedElem = jqChild;
                    }
                });

                this.widget = locatedElem;
                resolve(this.widget);
            }
            else{
                resolve(false);
            }
        });
    }

    static GoBack(){
        return new Promise(resolve=>{
            let widget = PageLess.CurrentPage;
            if(widget.isPageLessComponent) {
                let previousURL = widget.finalView.PageLess.previousURLs.pop();
                if (previousURL != undefined) {
                    (new PageLess()).route(previousURL, false).then(widget=>resolve(widget));
                } else {
                    widget.finalView.PageLess.goToStart();
                }
            }
        });
    }

    static ClearHistory(){
        return new Promise(resolve=>{
            PageLess.CurrentPage = 0;
            PageLess.Pages       = [];
            resolve();
        });
    }

    goToStart(){
        console.warn("This is the last page. You must override {GoToStart} Method. You haven't overriden {GoToStart} Method yet");
    }

    refresh(){
        let currentWidget = this.widget;
        this.url = PageLess.getURL('path').replace(`/${PageLess.getURL('paths')[1]}/`, '');

        this.route().then(refreshedWidg =>{
            currentWidget.remove();
        });
    }

    static IsJSONData(data){
        if (typeof data !== "string") {
            return false;
        }

        try {
            data = JSON.parse(data);
        } catch (e) {
            return false;
        }

        if (typeof data === "object" && data !== null) {
            return true;
        }

        return false;
    }

    static async Request(keys, fullAsync=false){
            if(typeof keys.beforeSend == "function")
                keys.beforeSend();
            
            

            if(fullAsync === true){
                if(keys.data !== undefined && PageLess.IsJSONData(keys.data) !== true){
                    keys.data = JSON.stringify(keys.data);
                }
                
                if(keys.contentType === undefined){
                    keys.contentType = 'application/json';
                }
            }

            const url    = keys.url;
            let method   = keys.type !== undefined ? keys.type.toUpperCase() : keys.method.toUpperCase();
            let search   = '';
            let data     = {
                method: method, 
                mode: "same-origin", // no-cors, *cors, same-origin
                cache: "no-cache",
                credentials: "same-origin",
                headers: {
                   "Content-Type": keys.contentType === undefined ? 'application/x-www-form-urlencoded' : keys.contentType
                },
                redirect: "follow",
                referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
            }

            if ((method == 'GET' || method == "HEAD") && keys.data !== undefined && keys.data != '') {
                search = `?${new URLSearchParams(keys.data).toString()}`
            } else {
                data.body = keys.data
            }
            const response = await fetch(url + search, data);
            
            return response.json();
    }

    static ImageRequest(data){
        return new Promise((resolve, reject) =>{
            let url             = data.url != undefined ? data.url : Widget.API(); 
            let formData        = data.formData;
            let progressHandler = data.onProgress != undefined && typeof data.onProgress == 'function' ? data.onProgress : ()=>{};
            let request         = new XMLHttpRequest();
    
            request.open('POST', url); 
            request.upload.addEventListener('progress', progressHandler.bind(formData));
            request.addEventListener('load', function(e) {
                let response = request.response;
                if(PageLess.IsJSONData(response) === true){
                    response = JSON.parse(response);
                }
                resolve(response);
            });
            request.send(formData);
        });
    }

    static ChangeButtonState(button, text="Processing"){
        let buttonOldText = button.innerHTML;
        button.innerHTML  = `<span>${text}&nbsp;<i class="fad fa-md fa-spinner-third fa-spin"></i></span>`
        button.setAttribute('data-oldtext', buttonOldText);
        button.setAttribute('disabled', 'disabled');
    }
    
    static RestoreButtonState(button){
        let oldText      = button.getAttribute('data-oldtext');
        button.innerHTML = oldText;
        button.removeAttribute('disabled');
    }

    static ContextMenu(details, initializer){
        let contextMenu = document.createElement(('div'));
        contextMenu.classList.add('context-menu');
        contextMenu.style.cssText = `
            right: 10px;
            top: 10px; 
        `;

        let generateContextMenu = () =>{
            details.forEach(menuItem => {
                let classes   = (menuItem.class == undefined)? '' : menuItem.class;
                let attr      = (menuItem.additionalAttr == undefined)? '' : menuItem.additionalAttr;
                let creator   = document.createElement('div');
                let bluePrint = `<button ${menuItem.id != undefined ? `id="${menuItem.id}"`:''} class="menu-item ${classes}" ${menuItem.contextId != undefined ? `context-id="${menuItem.contextId}"`:''} ${attr}>${menuItem.text}</button>`;
                
                creator.innerHTML = bluePrint;
                let button = creator.firstChild;
                button.addEventListener('click', (event)=>{
                    event.stopPropagation();
                    if(menuItem.callback != undefined && typeof menuItem.callback == 'function'){
                        menuItem.callback();
                        button.parentElement.remove();
                    }
                });

                contextMenu.append(button);
            });
            return contextMenu;
        };

        let closer = (event)=>{
            let elem = event.target;
            if(elem.classList.contains('menu-item') != true && elem.classList.contains('context-menu') != true && elem.classList.contains('init-context-menu') != true){
                contextMenu.remove();
                document.body.removeEventListener('click', closer);
            }
        }

        document.body.addEventListener('click', closer);
        let existingMenus = document.querySelectorAll('.context-menu');
        if(existingMenus.length >= 1){
            existingMenus.forEach(menu=>{
                menu.remove();
            })
        }

        let holder = initializer.parentElement;
        holder.style.position = "relative !important";
        holder.append(generateContextMenu());
    }

    static Toast(type, text, timer=2000){
        const target = PageLess.ToastContainer;
        const toast  = new PageLessComponent("custom-toast", {
            data: {
                type: type,
                text: text,
                timer: timer,
            },
            view: function(){
                return /*html*/`
                    <div class="w-100 position-absolute" style="bottom: 50px; z-index: 10000">
                        <div class="w-100 d-flex justify-content-center px-md-3">
                            <div class="animated zoomIn faster w-auto toast toast-${this.type} w-100px ${this.identity} ${this.classname}" id="${this.identity}" onclick="{{this.props.onclick}}" style="${this.style}">
                                ${this.text}&nbsp;
                            </div>
                        </div>
                    </div>
                `;
            },
        });
        target.style.position = 'relative';
        target.appendChild(toast.view());
        setTimeout(() => {
            target.removeChild(toast.view());
        }, timer);
    }

    static StartLoader(durationToFinish=2500){
        const target = document.body;
        const toast  = new PageLessComponent("header-loader", {
            data: {
                duration: durationToFinish,
                animation: null,
            },
            props: {
                stop: function(){
                    this.props.stopAnimation.call(this).then(()=>{
                        this.remove();
                    });
                },

                stopAnimation: function(){
                    return new Promise(resolve=>{
                        if (this.animation !== null ) {
                            this.animation.cancel();
                        }
                        resolve();
                    })
                }
            },
            view: function(){
                return /*html*/`
                    <div class="pageless-loader" style="width: 100% !important;">
                        <div style="position: absolute !important; top: 0 !important; z-index: 1000 !important; height: 3px; width: 0px; background-color: #dc3545;"></div> 
                    </div>
                `;
            },
            callback: function(){
                const grow = [
                    { width: "0%" },
                    { width: "100%" },
                    { right: "0" },
                  ];
                  
                const timer = {
                    duration: this.duration,
                    iterations: Infinity,
                    easing: 'ease-in-out'
                };

                this.animation = this.querySelector('div').animate(grow, timer);
            }
        });
        target.style.position = 'relative';
        target.appendChild(toast.view());;
    }

    static StopLoader(){
        let loaders = document.querySelectorAll('.pageless-loader');
        if (loaders !== null && loaders !== undefined) {
            loaders.forEach(loader=>{
                loader.props.stop.call(loader);
            })
        }
    }

    static ManualDownload(text, name, type){
        return new Promise(resolve =>{
            let file      = new Blob([text], {type: type});
            let link      = document.createElement("a");
            link.href     = URL.createObjectURL(file);
            link.download = name;
            link.click();
            resolve();
        });
    }

    static InitFlexScrollersX(obj){
        let leftScroller   = document.createElement('button');
        let rightScroller  = document.createElement('button');

        leftScroller.classList.add('btn-circle', 'left-scroller', 'bg-white');
        leftScroller.setAttribute('clickable', 'false');
        leftScroller.innerHTML = /*html*/`<i class="fa fa-2x fa-angle-left"></i>`;

        rightScroller.classList.add('btn-circle', 'right-scroller', 'bg-white');
        rightScroller.setAttribute('clickable', 'true');
        rightScroller.innerHTML = /*html*/`<i class="fa fa-2x fa-angle-right"></i>`;


        leftScroller.addEventListener('click', function(){
            let diff        = obj.scrollLeft - obj.clientWidth;
            let scrollValue = diff > 0 ? diff : 0;
            obj.scroll(scrollValue, 0);
            rightScroller.setAttribute('clickable', 'true');
            if(scrollValue == 0)
                this.setAttribute('clickable', 'false');
        });

        rightScroller.addEventListener('click', function(){
            let sum         = obj.scrollLeft + obj.clientWidth;
            let scrollValue = sum < obj.scrollWidth ? sum : obj.scrollWidth;
            obj.scroll(scrollValue, 0);
            leftScroller.setAttribute('clickable', 'true');
            if(scrollValue == obj.scrollWidth)
                this.setAttribute('clickable', 'false');
        });

        if(!obj.parentElement.classList.contains('scroll-x-container-parent')){
            obj.parentElement.classList.add('scroll-x-container-parent');
        }
        obj.parentElement.appendChild(leftScroller);
        obj.parentElement.appendChild(rightScroller);
    }

    static CamelCase(text){
        let firstLetter  = text.charAt(0).toUpperCase();
        let otherLetters = text.substring(1);
        return firstLetter+otherLetters;
    }

    static SlideDown(elem, duration = 250){
        return new Promise(resolve=>{
            elem.style.display  = 'block';
            elem.style.overflow = 'hidden'; 
            const height        = elem.offsetHeight;
            elem.style.height   = '0px';
            elem.animate(
                [
                    {height: `0px`},
                    {height: `${height}px`}
                ],
                {
                    fill: 'forwards',
                    duration: duration,
                    iteration: 1,
                    easing: 'ease-in-out'
                }
            ).finished.then(()=>{
                resolve(elem);
            });
        })
    }

    static SlideUp(elem, duration = 250){
        return new Promise(resolve=>{
            elem.animate(
                [
                    {
                        height: `0px`
                    }
                ],
                {
                    duration: duration,
                    iteration: 1,
                    easing: 'ease-in-out'
                }
            ).finished.then(()=>{
                elem.style.display = 'none';
                resolve(elem);
            });
        });
    }

    static SlideToggle(elem, duration = 250){
        let promise; 
        if (elem.expanded == undefined || elem.expanded === false) {
            promise       = PageLess.SlideDown(elem, duration)
            elem.expanded = true;
        } else {
            promise       = PageLess.SlideUp(elem, duration);
            elem.expanded = false; 
        }

        return promise;
    }

    static FadeOut(elem, duration = 250){
        return new Promise(resolve=>{
            elem.animate(
                [
                    {opacity: 1},
                    {opacity: 0 }
                ],
                {
                    fill: 'forwards',
                    duration: duration,
                    iteration: 1,
                    easing: 'ease-in-out'
                }
            ).finished.then(()=>{
                elem.style.setProperty('display', 'none', 'important');
                resolve(elem);
            });
        })
    }

    static FadeIn(elem, duration = 250){
        return new Promise(resolve=>{
            elem.style.removeProperty('display');
            elem.animate(
                [
                    {opacity: 0},
                    {opacity: 1}
                ],
                {
                    fill: 'forwards',
                    duration: duration,
                    iteration: 1,
                    easing: 'ease-in-out'
                }
            ).finished.then(()=>{
                resolve(elem);
            });
        })
    }
}

window.onpopstate = event => {
    event.preventDefault();
    window.PageLess.GoBack();
};