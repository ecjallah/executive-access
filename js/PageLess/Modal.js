/**
 * Purpose: sets the controls for the modal 
 * Version Release: 1.0
 * Created Date: April 6, 2019
 * Author(s):Enoch C. Jallah
 * Contact Mail: enochcjallah@gmail.com
 */
import {PageLessComponent} from './PageLess.min.js';
export class PageLessModal
{
	constructor (name = null)
	{
		this.modalHolder = document.body;
        this.name        = name;
        this.modal       = null;
    }
    
    push () {
        if(window.PageLessModal === undefined)
            window.PageLessModal = PageLessModal;

        if(PageLessModal.Holder == undefined){
            PageLessModal.Holder = this.modalHolder;
        }
        return new Promise((resolve) => {
            if(Object.getPrototypeOf(this).hasOwnProperty(this.name)) {
                this[this.name](); 

                if(this.modal instanceof PageLessComponent) {
                    let oldSetData               = this.modal.finalView.setData;
                    this.modal.finalView.PageLessModal   = this;
                    this.modal.finalView.setData = (data)=>{
                        this.modal.finalView     = oldSetData.call(this.modal.finalView, data);
                        this.modalHolder.append(this.modal.finalView);
                        return this.modal.finalView;
                    };

                    this.modalHolder.append(this.modal.finalView);
                    resolve(this.modal.finalView);
                }
                else{
                    this.modalHolder = this.modalHolder
                    this.modalHolder.append(this.modal);
                    resolve(this.modal);
                }
            }
        });
    }

    /** closes the modal */
    close()
    {
        if(this.modal instanceof PageLessComponent) {
            this.modal.finalView.querySelector('.custom-modal-close-button').click();
        }
        else{
            this.modal.querySelector('.custom-modal-close-button').click();
        }
    }

    static Open(modal){
        if(window.PageLessModal === undefined)
            window.PageLessModal = PageLessModal;

        if(PageLessModal.Holder == undefined){
            PageLessModal.Holder = document.body;
        }

        if(PageLessModal.Holder != null && PageLessModal.Holder != undefined){
            let closeBtnsSet1 = modal.querySelectorAll('.custom-modal-close-button');
            let closeBtnsSet2 = modal.querySelectorAll('.close-modal');
            if (closeBtnsSet1.length >= 1) {
                closeBtnsSet1.forEach(btn => {
                    btn.addEventListener('click', ()=>{
                        PageLessModal.Close(modal);
                    })
                });
            }

            if (closeBtnsSet2.length >= 1) {
                closeBtnsSet2.forEach(btn => {
                    btn.addEventListener('click', ()=>{
                        PageLessModal.Close(modal);
                    })
                });
            }

            modal.addEventListener('click', (event)=>{
                let elem = event.target;
                if(elem.classList.contains('closable')){
                    PageLessModal.Close(modal);
                }
            });

            PageLessModal.Holder.appendChild(modal);
        }
        else{
            throw "Cannot append modal to a null holder";
        }
    }

    static Close(modal){
        let customModal          = modal.querySelector('.custom-modal');
        if (customModal != null)
            customModal.classList.add('custom-modal-close');
		
		setTimeout(() => {
			modal.remove();
		}, 250);
    }

    static Confirmation(title, msg){
        return new Promise((resolve, reject) =>{
            PageLessModal.Open((new PageLessComponent("confirmation-modal", {
                data: {
                    title: title,
                    msg: msg
                },
                props: {
                    onyesclick: function(){
                        resolve(true);
                        PageLessModal.Close(this.parentComponent);
                    },
                    onnoclick: function(){
                        reject(true);
                        PageLessModal.Close(this.parentComponent);
                    }
                },
                view: function(){
                    return /*html*/`
                        <div class="custom-modal-container p-2 closable">
                            <div class="col-12 com-sm-12 col-md-8 col-lg-8 col-xl-5 custom-modal">
            
                                <div class="row modal-header-container p-3">
                                    <div class="col-12 custom-modal-header ">
                                        <span class="h5"><span class="fad fa-exclamation-triangle text-t-blue"></span>&emsp;<span class="title">${this.title}</span></span>
                                        <button class="btn-circle text-danger custom-modal-close-button d-none"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
            
                                <div class="row custom-modal-body p-2 pt-0 pb-0">
                                    <div class="col-12">
                                        <div class="row p-3" >
                                            <div class="flex-1">
                                                <div class="col-12">
                                                    <div class="row pb-2">
                                                        <p class="msg">${this.msg}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
            
                                        <div class="row justify-content-end pt-3">
                                            <pageless-button type="button" class="btn btn-light mr-3 col-5 col-sm-3 col-md-3 close-modal" onclick="{{this.props.onnoclick}}" text="No"></pageless-button>
                                            <pageless-button onclick="{{this.props.onyesclick}}" type="button" classname="btn btn-primary col-5 col-sm-3 col-md-3 confirm" text="Yes"></pageless-button>
                                        </div>	            								
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                },
            })).view());
        });
    }
	
    static Success(title, msg){
        return new Promise(resolve=>{
            PageLessModal.Open((new PageLessComponent("success-modal", {
                data: {
                    title: title,
                    msg: msg,
                },
                
                view: function(){
                    return /*html*/`
                        <div class="custom-modal-container p-2">
                            <div class="col-12 com-sm-12 col-md-8 col-lg-8 col-xl-5 custom-modal">
    
                                <div class="row modal-header-container p-3">
                                    <div class="col-12 custom-modal-header ">
                                        <span class="h5"><span class="fad fa-shield-check text-t-blue"></span>&emsp;<span class="title">${this.title}</span></span>
                                        <button class="btn-circle btn-clean text-danger custom-modal-close-button d-none"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
    
                                <div class="row custom-modal-body p-2 pt-0 pb-0">
                                    <div class="col-12">
    
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="row tab">
                                                    <form class="col-12 " id="pin-recovery-form">
                                                        <div class="row p-3" >
                                                            <div class="flex-1">
                                                                <div class="col-12 form-content">
                                                                    <div class="row justify-content-center result-container"></div>
                                                                    <div class="row pb-2 justify-content-around">
                                                                        <p class="msg">${this.msg}</p>
                                                                    </div>
                                                    
                                                                    <div class="row justify-content-center">
                                                                        <div class="check-animation">
                                                                            <svg width="150" height="150"   id="circle-check" class="svg">
                                                                                <path id="draw-circle" class="strokes one" stroke-width="4" fill="transparent" d="M 75, 75  m -50, 0 a 50, 50 0 1, 0 100, 0 a 50, 50 0 1, 0 -100, 0
                                                                                "/>
                                                                                <path id="draw-check" class="strokes two" d="M 50, 80 L 65, 95 L 95, 60"  stroke-width="4" fill="transparent"/>
                                                                            </svg>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
    
                                                        <div class="row justify-content-center">
                                                            <div class="col-12">
                                                                <div class="row justify-content-around pt-3">
                                                                    
                                                                    <button type="button" class="btn btn-light col-5 col-sm-3 col-md-3 primary close-modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>	            						
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                },
                
            })).view());
            resolve();
        });
    }
    
    static TransactionSuccess(title = null, text='')
    {
        PageLessModal.Open((new PageLessComponent("transaction-success-modal", {
            data: {
                title: title == null? "Transaction Successful" : title,
                text: text
            },
            view: function(){
                return /*html*/`
                    <div class="custom-modal-container p-2">
                        <div class="col-12 com-sm-12 col-md-8 col-lg-8 col-xl-5 custom-modal">
                            <div class="row modal-header-container p-3">
                                <div class="col-12 custom-modal-header ">
                                    <span class="h5"><span class="fad fa-shield-check text-t-blue"></span>&emsp;<span class="title">${this.title}</span></span>
                                    <button class="btn-circle btn-clean text-danger custom-modal-close-button d-none"><i class="fa fa-times"></i></button>
                                </div>
                            </div>

                            <div class="row custom-modal-body p-2 pt-0 pb-0">
                                <div class="col-12">
                                    <div class="row p-3" >
                                        <div class="flex-1">
                                            <div class="col-12 form-content">
                                                <div class="row justify-content-center result-container"></div>
                                                <div class="row pb-2 justify-content-around">
                                                    <p class="text">${this.text}</p>
                                                </div>
                                
                                                <div class="row justify-content-center">
                                                    <div class="check-animation">
                                                        <svg width="150" height="150"   id="circle-check" class="svg">
                                                            <path id="draw-circle" class="strokes one" stroke-width="4" fill="transparent" d="M 75, 75  m -50, 0 a 50, 50 0 1, 0 100, 0 a 50, 50 0 1, 0 -100, 0
                                                            "/>
                                                            <path id="draw-check" class="strokes two" d="M 50, 80 L 65, 95 L 95, 60"  stroke-width="4" fill="transparent"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>	            						
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        })).view());
    }
    
    static BuildForm(data, callback=null){
        return new Promise(resolve=>{
            PageLessModal.Open((new PageLessComponent ('form-builder', {
                data:{
                    title: data.title != undefined ? data.title : "Form",
                    icon: data.icon != undefined? data.icon : 'file-alt',
                    inputs: data.inputs != undefined ? data.inputs : '',
                    description: data.description != undefined ? data.description : '',
                    submitText: data.submitText != undefined ? data.submitText : 'Complete',
                    closeText: data.closeText != undefined ? data.closeText : 'Close',
                    closable: data.closable != undefined ? data.closable : false,
                    autoClose: data.autoClose != undefined ? data.autoClose : true,
                    noClose: data.noClose != undefined ? data.noClose : false,
                    noSubmit: data.noSubmit != undefined ? data.noSubmit : false,
                    sentProps: data.props != undefined ? data.props : false,
                    values: {},
                    handler: typeof callback == 'function' ? callback : null
                },
                props: {
                    onsubmit: function(e){
                        e.preventDefault();
                        let inputs       = {
                            selects    : this.querySelectorAll('select'),
                            inputs     : this.querySelectorAll('input'),
                            textarea   : this.querySelectorAll('textarea'),
                            checkboxes : this.querySelectorAll('checkbox'),
                            radios     : this.querySelectorAll('radio')
                        };

                        this.values = {};
                        for (const group in inputs) {
                            if (Object.hasOwnProperty.call(inputs, group)) {
                                const inputGroup = inputs[group];
                                if (inputGroup.length >= 1) {
                                    inputGroup.forEach(input => {
                                        let type = input.getAttribute('type');
                                        if(type == 'checkbox'){
                                            this.values[input.getAttribute('id')] = input.checked;
                                        }else if (type == 'radio'){
                                            let selectedRadio = this.querySelector(`input#${input.getAttribute('id')}[type=radio]:checked`);
                                            this.values[input.getAttribute('id')] = selectedRadio !== null ? selectedRadio.value : null;
                                        }else{
                                            this.values[input.getAttribute('id')] = input.value;
                                        }
                                    });
                                }
                                
                            }
                        }
                        this.values.modal = this;
                        this.values.submitBtn = this.querySelector('button[type=submit]');
                        if (this.handler !== null) {
                            this.handler(this.values);
                        } else {
                            resolve(this.values);
                        }
                        if (this.autoClose === true) {
                            PageLessModal.Close(this);
                        }
                    }
                },
                view : function(){
                    return /*html*/`
                        <form class="custom-modal-container p-2 ${this.closable === true ? 'closable' : ''}" onsubmit={this.props.onsubmit}>
                            <div class="col-12 com-sm-12 col-md-8 col-lg-8 col-xl-5 custom-modal d-flex flex-column">
                                <div class="row modal-header-container p-3">
                                    <div class="col-12 custom-modal-header ">
                                        <span class="h6"><span class="fad fa-${this.icon} text-t-blue"></span>&emsp;${this.title}</span>
                                        <button type="button" class="btn-circle btn-clean text-danger custom-modal-close-button d-none"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
    
                                <div class="row custom-modal-body p-2 pt-0 pb-0 flex-1 scroll-y">
                                    <div class="col-12">
                                        <div class="row alert-container px-3"></div>
    
                                        <div class="w-100 d-flex p-2 pb-0 no-gutters">
                                            <div class="w-100 d-flex flex-column animated fadeIn fast">
                                                <div class="w-100 pb-2  ${this.description == '' ? 'display-none' : ''}">
                                                    <p>${this.description}</p>
                                                </div>
                                                ${this.inputs}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row no-gutters ${this.noClose !== false || this.noSubmit !== false ? 'justify-content-center' : 'justify-content-end'} pt-3 pb-3">
                                    ${this.noClose === false ? `<button type="button" class="btn btn-muted col-5 col-sm-3 col-md-3 switch-mode animated fadeIn faster close-modal">${this.closeText}</button>&emsp;` : ''}
                                    ${this.noSubmit === false ? `<progress-button type="submit" classname="btn btn-primary col-5 col-sm-3 col-md-3 animated fadeIn faster" progresstype="warning" text="${this.submitText}"></progress-button>` : ''}

                                </div>
                            </div>
                        </form>
                    `;
                }, 
                callback: function(){
                    if (this.sentProps !== false && typeof this.sentProps == 'object') {
                        for (const prop in this.sentProps) {
                            if (Object.hasOwnProperty.call(this.sentProps, prop)) {
                                const element = this.sentProps[prop];
                                if (typeof element == 'function') {
                                    this.props[prop] = element;
                                }
                            }
                        }
                    }
                }
            })).view());
        });
    }
}