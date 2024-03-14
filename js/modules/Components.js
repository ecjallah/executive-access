/*!
 * Purpose: contains shared components for all modules. 
 * Version Release: 1.0
 * Created Date: March 22, 2023
 * Author(s):Enoch C. Jallah
 * Contact Mail: enochcjallah@gmail.com
*/
// "use esversion: 11";
import { PageLessComponent } from '../PageLess/PageLess.min.js';
import { PageLess } from '../PageLess/PageLess.min.js';
import { Modal } from './Modal.js';

export const Components = {
    // native button with PageLess Implementation
    PageLessButon : new PageLessComponent('pageless-button', {
        data: {
            attributes: '',
            class: '',
            classname: '',
            type: '',
            disabled: false,
            id: null,
            text: null,
            href: ''
        },
        view: function(){
            return /*html*/`
                <button ${this.href != '' ? `href="${this.href}"` : ''} type="${this.type}" class="${this.class} ${this.classname}"  onclick="{{this.props.onclick}}" ${this.attributes}>${this.text}</button>
            `;
        }
    }),

    PageLessButon : new PageLessComponent('pageless-link', {
        data: {
            attributes: '',
            class: '',
            classname: '',
            type: '',
            disabled: false,
            id: null,
            text: null,
            href: ''
        },
        props: {
            onfollow: function(){
                if (this.href != '') {
                    (new PageLess(this.href)).route();
                }
            }
        },
        view: function(){
            return /*html*/`
                <div>
                    <pageless-button href="${this.href}" type="${this.type}" classname="${this.classname}"  onclick="{{this.props.onfollow}}" attributes="${this.attributes}" text='${this.text}'></pageless-button>
                </div>
            `;
        }
    }),

    // circular progress bar
    CircularProgress : new PageLessComponent("circular-progress", {
        data: {
            width: 120,
            height: 120,
            strokewidth: 4,
            strokecolor: 'white',
            fillcolor: 'transparent',
            strokepathcolor: "rgba(220,53,69,0.15)",
            classname: '',
            percent: 0,
            identity: ''
        },
        props: {
            setProgress: function(percent){
                return new Promise(resolve=>{
                    this.percent = percent;

                    if(this.percent <= 100){
                        const offset = this.circumference - this.percent / 100 * this.circumference;
                        this.querySelector('.circular-progress-ring').style.strokeDashoffset = offset;

                        if(this.percent == 100){
                            this.percent = 0;
                            this.querySelector('.circular-progress-ring').addEventListener('transitionend', ()=>{
                                resolve();
                                const offset = this.circumference - this.percent / 100 * this.circumference;
                                this.querySelector('.circular-progress-ring').style.strokeDashoffset = offset;
                            });
                        }
                    }
                });
            }
        },
        view: function(){
            let clipId         = Date.now();
            this.radius        = (this.width / 2);
            this.circumference = this.radius * 2 * Math.PI;
            this.offset        = this.circumference - this.percent / 100 * this.circumference;
            return /*html*/`
                <svg
                    id="${this.identity}"
                    class="circular-progress ${this.identity} ${this.classname}"
                    width="${this.width}"
                    height="${this.height}">

                    <clipPath id="${clipId}">
                        <circle
                            stroke="${this.strokecolor}"
                            fill="${this.fillcolor}"
                            r="${this.radius}"
                            cx="${this.width/2}"
                            cy="${this.height/2}"
                        />
                    </clipPath>
                    <circle
                        clip-path="url(#${clipId})"
                        class="circular-progress-ring"
                        stroke="${this.strokecolor}"
                        stroke-width="${(this.radius * 2)}"
                        stroke-dasharray="${this.circumference} ${this.circumference}"
                        style="stroke-dashoffset:${this.circumference}"
                        fill="${this.strokepathcolor}"}
                        r="${this.radius}"
                        cx="${this.width/2}"
                        cy="${this.height/2}"/>
                    <circle
                        clip-path="url(#${clipId})"
                        class="circular-progress-ring"
                        stroke="${this.strokecolor}"
                        stroke-width="${this.strokewidth}"
                        fill="${this.fillcolor}"
                        r="${this.radius}"
                        cx="${this.width/2}"
                        cy="${this.height/2}"/>
                </svg>
            `;
        },
        callback: function(){
            this.ready(()=>{
                this.setProgress = this.props.setProgress.bind(this);
                setTimeout(() => { this.setProgress(parseFloat(this.percent));
                }, 250);
            });
        }
    }),

    DoughnutProgress : new PageLessComponent("doughnut-progress", {
        data: {
            title: "My Title",
        },
        
        view: function(){
            return /*html*/`
                <div class="doughnut-progress">
                    <div class="circle">
                        <div class="mask full">
                            <div class="fill"></div>
                        </div>
                        <div class="mask half">
                            <div class="fill"></div>
                        </div>
                        <div class="inside-circle"> 75% </div>
                    </div>
                </div>
            `;
        },
    }),

    // button with Progress Implementation
    ProgressButon : new PageLessComponent('progress-button', {
        data: {
            attributes: '',
            class: '',
            classname: '',
            type: '',
            disabled: false,
            id: null,
            text: null,
            progresstype:''
        },
        props: {
            setProgress: function(value, text=null){
                return new Promise(resolve=>{
                    let progress = this.querySelector('.c-progress');
                    if(text != null) 
                        this.setProgressText(text);

                    if(value <= 100){
                        if(progress.classList.contains('display-none')){
                            progress.classList.remove('display-none');
                        }
                        this.setAttribute('disabled', 'disabled');
                        progress.setProgress(value).then(()=>{
                            this.removeAttribute('disabled');
                            progress.classList.add('display-none');
                            resolve();
                        });
                    }
                });
            },

            setProgressText: function(text){
                this.querySelector('.text').innerHTML = text;
            },
            
            changeState: function(text){
                this.querySelector('.text').innerHTML = `<span>${text}&nbsp;<i class="fad fa-md fa-spinner-third fa-spin"></i></span>`;
                this.setAttribute('disabled', 'disabled');
            },

            restoreState: function(){
                this.removeAttribute('disabled');
                this.querySelector('.text').innerHTML = this.text;
            }
        },
        view: function(){
            return /*html*/`
                <button type="${this.type}" class="d-flex align-items-center justify-content-center ${this.class} ${this.classname}"  onclick="{{this.props.onclick}}" ${this.attributes}>
                    <span class="text">${this.text}</span>&emsp;
                    <circular-progress width="25" height="25" strokewidth="2" identity="c-progress" classname="display-none ${this.progresstype}"></circular-progress>    
                </button>
            `;
        },
        callback: function(){
            this.ready(()=>{
                this.setProgress     = this.props.setProgress.bind(this);
                this.setProgressText = this.props.setProgressText.bind(this);
                this.changeState     = this.props.changeState.bind(this);
                this.restoreState    = this.props.restoreState.bind(this);
            });
        }
    }),

    // native select with PageLess Implemenation
    NativeSelect : new PageLessComponent('native-select', {
        data: {
            identity: null,
            items: null,
            selectedvalue: '',
            classname: null,
            attributes: null,
            style: '',
            placeholder: '',
            required: null
        },

        view: function(){
            let opts = ``;
            if(this.items != null && typeof this.items == 'object')
            {
                this.items.forEach((option) =>{
                    let selected = (option.value == this.selectedvalue)? 'selected' : '';
                    opts += /*html*/`<option value="${option.value}" ${selected}>${option.text}</option>`; 
                });
            }
            return /*html*/`
                <select class="form-select ${this.identity} ${this.classname}" id="${this.identity}" name="${this.identity}" ${this.attributes} style="${this.style}" ${(this.required != null) ? `required="${this.required}"` : ''}>
                    <option value="">${this.placeholder}</option>
                    ${opts}
                </select>
            `;
        }
    }),

    // Number Input Component 
    NubmerInput : new PageLessComponent('number-input', {
        data: {
            identity: null,
            icon: null,
            text: null,
            value: '',
            attributes: null,
            minlen: null,
            maxlen: null,
            required: null,
            description: '',
            autocomplete: 'off'
        },

        props: {
            onvaluechange: function(){}
        },
        
        view: function(){
            return /*html*/`
                <div class="row p-2 form-content-row ${(this.description != '') ? 'align-items-center' : ''}">
                    <div class="input-icon pr-2 text-muted">
                        <span><i class="fa fa-lg fa-${this.icon}"></i></span>
                    </div>
                    <div class="form-input-container">
                        <input
                            type="number" 
                            class="form-input ${this.identity}" 
                            id="${this.identity}" 
                            name="${this.identity}" 
                            step="any"
                            placeholder="${this.text}" 
                            pattern="[0-9]+\.?([0-9]+)?" 
                            title="Enter numbers only. Eg: 1.00" 
                            value="${this.value}" ${this.attributes}
                            onchange="{{this.closest('.form-content-row').props.onvaluechange}}"
                            ${(this.required != null) ? `required="${this.required}"` : ''}
                            ${(this.autocomplete != '') ? `autocomplete="${this.autocomplete}"` : ''}
                            ${this.min != null ? `min="${this.min}"` : ''}
                            ${this.max != null ? `max="${this.max}"` : ''}
                        >
                        <span>${this.description}</span>
                    </div>                  
                </div>
            `;
        }
    }),
    // Text Input Component 
    TextInput : new PageLessComponent('text-input', {
        data: {
            identity: null,
            classname: '',
            icon: null,
            text: null,
            value: '',
            attributes: '',
            required: null,
            description: '',
            changeevent: false
        },
        props: {
        },
        view: function(){
            return /*html*/`
                <div class="row p-2 form-content-row ${(this.description != '') ? 'align-items-center' : ''}" ${this.changeevent !== false ? 'onchange=""' : ''}>
                    <div class="input-icon pr-2 text-muted">
                        <span><i class="fa fa-lg fa-${this.icon}"></i></span>
                    </div>
                    <div class="form-input-container">
                        <input type="text" class="form-input ${this.identity} ${this.classname}" 
                            id="${this.identity}" name="${this.identity}" placeholder="${this.text}" ${this.attributes}
                            ${(this.required != null) ? `required="${this.required}"` : ''}
                            ${(this.value != '') ? `value="${this.value}"` : ''}
                        >
                        <span>${this.description}</span>
                    </div>                  
                </div> 
            `;
        }
    }),

    LongTextInput : new PageLessComponent('long-text-input', {
        data: {
            identity: null,
            classname: '',
            icon: 'align-justify',
            description: '',
            text: null,
            value: '',
            attributes: '',
            required: null
        },
        props: {
            onkeyup: function(){
                this.style.height = `${this.scrollHeight}px`;
            }
        },
        view: function(){
            return /*html*/`
                <div class="row p-2 form-content-row ${(this.description != '') ? 'align-items-center' : ''}">
                    <div class="input-icon pr-2 text-muted">
                        <span><i class="fa fa-lg fa-${this.icon}"></i></span>
                    </div>
                    <div class="form-textarea-container">
                        <textarea class="form-textarea ${this.identity} ${this.classname}" id="${this.identity}" name="${this.identity}" placeholder="${this.text}" ${this.attributes}
                        ${(this.required != null) ? `required="${this.required}"` : ''}
                        onkeyup="{{this.closest('.form-content-row').props.onkeyup.call(this)}}"
                        >${(this.value != '') ? this.value : ''}</textarea>
                        <span>${this.description}</span>
                    </div>                  
                </div>
            `;
        }
    }),

    FileInput : new PageLessComponent('file-input', {
        data: {
            identity: null,
            classname: '',
            icon: null,
            text: null,
            value: '',
            attributes: '',
            required: null,
            multiple: null
        },
        
        view: function(){
            return /*html*/`
                <div class="w-100 d-flex align-items-center justify-content-center p-relative">
                    <input id="${this.identity}" type="file" class="position-absolute ${this.identity}" accept=".jpg, .jpeg, .png, .gif" style="opacity: 0" ${(this.required != null) ? `required="${this.required}"` : ''} ${(this.multiple != null) ? `multiple="true"` : ''}>
                    <label for="${this.identity}" class="${this.classname} custom-file-input d-flex justify-content-center align-items-center" ${this.value != '' ? `style="background: url('${this.value}')"` : ''}>
                        <div class="col-12 text-center "><i class="fad fa-${this.icon}"></i><br>${this.text}</div>
                    </label>
                </div>
            `;
        }, 
        callback: function(){
            this.ready(()=>{
                let imgInput         = this.querySelector('input');

                imgInput.addEventListener('change', ()=>{
                    let tempImgContainer = imgInput.nextElementSibling;
                    if(imgInput.files && imgInput.files[0])
                    {
                        let image = URL.createObjectURL(imgInput.files[0]);
                        tempImgContainer.style.background = `url('${image}')`;
                    }
                });
            });
        }
    }),

    // Text Input Component 
    EmailInput : new PageLessComponent('email-input', {
        data: {
            identity: 'email',
            classname: '',
            icon: 'envelope',
            text: "Email",
            value: '',
            attributes: '',
            required: null,
            description: ''
        },
        
        view: function(){
            return /*html*/`
                <div class="row p-2 form-content-row ${(this.description != '') ? 'align-items-center' : ''}">
                    <div class="input-icon pr-2 text-muted">
                        <span><i class="fa fa-lg fa-${this.icon}"></i></span>
                    </div>
                    <div class="form-input-container">
                        <input type="email" class="form-input ${this.identity} ${this.classname}" 
                            id="${this.identity}" name="${this.identity}" placeholder="${this.text}" ${this.attributes}
                            ${(this.required != null) ? `required="${this.required}"` : ''}
                            ${(this.value != '') ? `value="${this.value}"` : ''}
                        >
                        <span>${this.description}</span>
                    </div>                  
                </div> 
            `;
        }
    }),

    CheckBox : new PageLessComponent("check-box", {
        data: {
            identity: '',
            text: '',
            icon: '',
            classname: '',
            description: '',
            value: '',
            checked: '0',
            attributes: null,
            required: null
        },
        view: function(){
            return /*html*/`
                <div class="row p-2 align-items-center">
                    <label class="custom-checkbox-container">${this.text}
                        <input type="checkbox"
                            id="${this.identity}" 
                            name="${this.identity}" 
                            class="${this.identity} ${this.classname}"
                            value="${this.value}"
                            ${(this.attributes != null)? this.attributes : ''}
                            ${(this.required != null)? `required=${this.required}` : ''}
                            ${(this.checked != '0')? `checked=${this.checked}` : ''}
                        >
                        <span class="checkmark" style="border: 1px solid darkblue !important;"></span>
                    </label>         
                </div>
            `;
        }
    }),

    NativeRadioButton : new PageLessComponent("native-radio-button", {
        data: {
            identity: '',
            icon: '',
            classname: '',
            description: '',
            text: '',
            value: '',
            checked: '0',
            attributes: null,
            required: null
        },
        props: {
            onchange: function(){}
        },
        view: function(){
            return /*html*/`
                <div class="row px-2 align-items-center">
                    <div class="input-icon pr-2 text-muted">
                        <span><i class="far fa-lg fa-${this.icon}"></i></span>
                    </div>
                    
                    <label class="custom-radio-button-container">${this.text}
                        <input type="radio"
                            name="${this.identity}" 
                            class="${this.identity} ${this.classname}"
                            value="${this.value}"
                            ${(this.attributes != null)? this.attributes : ''}
                            ${(this.required != null)? `required=${this.required}` : ''}
                            ${(this.checked != '0')? `checked=${this.checked}` : ''}
                        >
                        <span class="radio"></span>
                    </label>         
                </div>
            `;
        }
    }),

    RadioButton : new PageLessComponent("radio-button", {
        data: {
            identity: '',
            icon: '',
            classname: '',
            description: '',
            text: '',
            value: '',
            checked: '0',
            attributes: null,
            required: null
        },
        props: {
            onchange: function(){}
        },
        view: function(){
            return /*html*/`
                <div class="row px-2 align-items-center">
                    <div class="input-icon pr-2 text-muted">
                        <span><i class="far fa-lg fa-${this.icon}"></i></span>
                    </div>
                    
                    <label class="custom-radio-button-container">${this.text}
                        <input type="radio"
                            name="${this.identity}" 
                            class="${this.identity} ${this.classname}"
                            value="${this.value}"
                            ${(this.attributes != null)? this.attributes : ''}
                            ${(this.required != null)? `required=${this.required}` : ''}
                            ${(this.checked != '0')? `checked=${this.checked}` : ''}
                        >
                        <span class="radio"></span>
                    </label>         
                </div>
            `;
        }
    }),

    // phone number with contry selection input
    PhoneNumber : new PageLessComponent('phone-number', {
        data: {
            identity: 'phone-no',
            icon: null,
            text: null,
            items: [
                {
                    text: "Loading...",
                    value: '...'
                }
            ],
            className: null,
            attributes: null,
            selectedvalue: 'Liberia'
        },
        props: {
            onchange: function(){
                this.parentComponent.setData({
                    selectedvalue: this.value
                });
            }
        },
        view: function(){
            return /*html*/`
                <div class="row p-2 form-content-row">
                    <div class="input-icon pr-2 text-muted">
                        <span><i class="fa fa-lg fa-phone-alt"></i></span>
                    </div>
                    <div class="form-input-container" style="width: auto !important; flex: unset;">
                        <span class="form-input country-name">${this.selectedvalue}</span>
                        <label for="country-code" class="input-label translate-input-label hidden">&emsp;</label>
                    </div>

                    <div class="form-input-container" style="width: auto; flex: unset;">
                        <native-select
                            identity="country-code"
                            attributes='required'
                            items='${JSON.stringify(this.items)}'
                            selectedvalue="${this.selectedvalue}"
                            onchange='{{this.props.onchange}}'
                        ></native-select>
                        <label for="country-code" class="input-label translate-input-label">&emsp;</label>
                    </div>
                                    
                    <div class="form-input-container">
                        <input type="number"  class="form-input ${this.identity}" id="${this.identity}" name="${this.identity}" placeholder="Phone Number" pattern="[0-9]+" minlength="9" maxlength="12" title="Phone Number should contain numbers only. Eg: 775901684"  autocomplete="no" required>
                        <label for="${this.identity}" class="input-label translate-input-label">&emsp;</label>
                    </div>                  
                </div> 
            `;
        },

        callback: function(){
            this.ready(()=>{
                PageLess.Request({
                    url: '/api',
                    method: "GET",
                    data: "supported_countries",
                }).then(response=>{
                    if(response.response == 200)
                    {
                        let countriesData = response.response_body.additional;
                        let countries    = [];
                        
                        countriesData.forEach(country =>{
                            countries.push({
                                text: `(+${country.code})`,
                                value: country.name
                            });
                        });
                        
                        let newThis = this.setData({
                            items: countries
                        });
                    }
                });
            });
        }
    }),

    // Password Input Component 
    PasswordInput : new PageLessComponent('password-input', {
        data: {
            identity: 'password',
            className: '',
            icon: 'lock-alt',
            text: 'Password',
            value: '',
            attributes: '',
            description: '',
            hintText: /*html*/ `<span class="text-t-orange"><i class="fa fa-info-circle"></i>&nbsp;<span class="user-error-msg">We recommend a strong password which may contain a minimum eight characters, at least one uppercase letter, one lowercase letter, one number and one special character (@, $, !, %, *, ?,or &)</span></span>`,
            hint: false
        },
        
        view: function(){
            return /*html*/`
                <div class="row form-content-row p-2 ${(this.description != '' || this.hint !== false) ? 'align-items-center' : ''}"" onchange="">
                    <div class="input-icon pr-2 text-muted">
                        <span><i class="fa fa-lg fa-${this.icon}"></i></span>
                    </div>
                    <div class="form-input-container">
                        <input type="password" class="form-input ${this.identity}" id="${this.identity}" placeholder="${this.text}" name="${this.identity}" required ${this.attributes} autocomplete="new-password">
                        <span>${this.hint !== false ? this.hintText : this.description}</span>
                    </div>                  
                </div>
            `;
        }
    }),

    // Select Input Component 
    selectComponent : new PageLessComponent('custom-select', {
        data: {
            identity: null,
            icon: null,
            text: null,
            items: null,
            selectedvalue: '',
            classname: '',
            style: '',
            attributes: null,
            description: '',
            required: null
        },

        
        view: function(){
            return /*html*/`
                <div class="row form-content-row p-2 ${(this.description != '') ? 'align-items-center ${this.classname}' : ''}" style="${this.style}">
                    <div class="input-icon pr-2 text-muted">
                        <span><i class="fa fa-lg fa-${this.icon}"></i></span>
                    </div>
                    <div class="form-input-container">
                        <native-select
                            identity="${this.identity}"
                            placeholder="${this.text}"
                            attributes='${this.attributes}'
                            items='${JSON.stringify(this.items)}'
                            selectedvalue='${this.selectedvalue}'
                            ${(this.required != null) ? `required="${this.required}"` : ''}
                        ></native-select>
                        <span>${this.description}</span>
                    </div>                    
                </div>
            `;
        }
    }),

    DateInput : new PageLessComponent('date-input', {
        data: {

            identity: '',
            icon: 'calendar-alt',
            text: null,
            prefix: '',
            monthItems: [
                {text: "January",value: '01'},
                {text: "February",value: '02'},
                {text: "March",value: '03'},
                {text: "April",value: '04'},
                {text: "May",value: '05'},
                {text: "June",value: '06'},
                {text: "July",value: '07'},
                {text: "August",value: '08'},
                {text: "September",value: '09'},
                {text: "October",value: '10'},
                {text: "November",value: '11'},
                {text: "December",value: '12'}
            ],
            yearorder: 'desc',
            yearstep: 5,
            yearItems: [],
            className: null,
            attributes: null,
            selectedday: '',
            selectedmonth: '',
            selectedyear: '',
            value: ''
        },
        view: function(){
            this.date          = new Date();
            this.selectedmonth = this.selectedmonth == '' ? (0 + `${this.date.getMonth()+1}`).slice(-2) : (0 + `${this.selectedmonth}`).slice(-2);
            this.selectedday   = this.selectedday == '' ? (0 + `${this.date.getDate()}`).slice(-2) : (0 + `${this.selectedday}`).slice(-2);
            this.selectedyear  = this.selectedyear == '' ? this.date.getFullYear().toString() : this.selectedyear.toString();

            let dayItems       = [];
            for (let index = 1; index < 32; index++) {
                dayItems.push({text: (0 + `${index}`).slice(-2), value: (0 + `${index}`).slice(-2)});
            }

            let yearItems       = [];

            if(this.yearorder == 'desc'){
                for (let index = (new Date()).getFullYear(); index > (new Date()).getFullYear()-parseInt(this.yearstep) ; index--) {
                    yearItems.push({text: index, value: index});
                }
            }
            else if(this.yearorder == 'asc'){
                for (let index = (new Date()).getFullYear(); index < (new Date()).getFullYear()+parseInt(this.yearstep) ; index++) {
                    yearItems.push({text: index, value: index});
                }
            }
            return /*html*/`
                <div class="row p-2 form-content-row">
                    <div class="input-icon pr-2 text-muted">
                        <span><i class="fa fa-lg fa-${this.icon}"></i></span>
                    </div>
                    <div class="form-input-container" style="width: auto !important; flex: unset;">
                        <span class="form-input country-name">${this.prefix}${this.text}</span>
                    </div>
                    <div class="form-input-container flex-2" style="width: auto !important;">
                        <native-select
                            identity="${this.identity}month"
                            attributes='required'
                            selectedvalue="${this.selectedmonth}"
                            items='${JSON.stringify(this.monthItems)}'
                            placeholder="Month"
                            classname="text-right"
                        ></native-select>
                    </div>

                    <div class="form-input-container flex-1" style="width: auto;">
                        <native-select
                            identity="${this.identity}day"
                            attributes='required'
                            selectedvalue="${this.selectedday}"
                            items='${JSON.stringify(dayItems)}'
                            placeholder="Day"
                            classname="text-right"
                        ></native-select>
                    </div>
                                    
                    <div class="form-input-container flex-1" style="width: auto;">
                        <native-select
                            identity="${this.identity}year"
                            attributes='required'
                            selectedvalue="${this.selectedyear}"
                            items='${JSON.stringify(yearItems)}'
                            placeholder="Year"
                            classname="text-right"
                        ></native-select>
                    </div>                  
                </div> 
            `;
        },

        callback: function(){
        }
    }),

    TimeInput : new PageLessComponent('time-input', {
        data: {

            identity: 'time',
            icon: 'clock',
            text: 'Time',
            items: [],
            className: '',
            required: null,
            attributes: '',
            selectedvalue: '',
            description: '',
            halfhour: true
        },
        view: function(){
            let items = [];
            for (let index=0; index < 24; index++) {
                const time1 = `${index.toString().padStart(2, 0)}:00`;
                const time2 = `${index.toString().padStart(2, 0)}:30`;

                items.push({text: time1, value: time1+":00"});
                if (this.halfhour === true) {
                    items.push({text: time2, value: time2+":00"});
                }
            }
            return /*html*/`
                <div class="row p-2 form-content-row ${(this.description != '') ? 'align-items-center' : ''}">
                    <div class="input-icon pr-2 text-muted">
                        <span><i class="far fa-lg fa-${this.icon}"></i></span>
                    </div>
                    <div class="form-input-container">
                        <native-select
                            identity="${this.identity}"
                            placeholder="${this.text}"
                            attributes='${this.attributes}'
                            items='${JSON.stringify(items)}'
                            selectedValue="${this.selectedvalue}"
                            ${(this.required != null) ? `required="${this.required}"` : ''}
                        ></native-select>
                        <span>${this.description}</span>
                    </div>                    
                </div>
            `;
        }
    }),

    UserTypeSelect : new PageLessComponent("user-type-select", {
        data: {
            selectedvalue: '',
            options: [
                {text: 'Loading...', value: '...'}
            ],
        },
        props: {
            key: function(){},
        },
        view: function(){
            return /*html*/`
                <div>
                    <custom-select
                        identity="type"
                        icon="user-shield"
                        text="Account Type"
                        items='${JSON.stringify(this.options)}'
                        required="required"
                        selectedvalue="${this.selectedvalue}"
                    ></custom-select>
                </div>
            `;
        },
        callback: function(){
            this.ready(()=>{
                PageLess.Request({
                    url: `/api/get-registration-types`,
                    method: "GET",
                }).then(result=>{
                    if(result.status == 200){
                        let data    = result.response_body;
                        let options = [];
                        data.forEach(role=>{
                            options.push({
                                text: role.title,
                                value: role.id
                            });
                        });
                        this.setData({
                            options: options
                        });
                    }
                });
            });
        }
    }),

    CountySelect : new PageLessComponent("county-select", {
        data: {
            requried: '',
            selectedvalue: '',
            districts: '',
            includedistrict: false,
            options: [
                {text: 'Loading...', value: '...'}
            ],
        },
        props: {
            onchange: function(){
                let value     = this.querySelector('select').value;
                let districts = ''
                if (value != '') {
                    districts = /*html*/ `<district-select countyid="${value}"></district-select>`
                }

                this.parentComponent.setData({
                    selectedvalue: value, 
                    districts: districts
                });
            }
        },
        view: function(){
            return /*html*/`
                <div>
                    <custom-select
                        identity="county"
                        icon="map-marked-alt"
                        text="County"
                        items='${JSON.stringify(this.options)}'
                        ${this.required != '' ? 'required="required"' : ''}
                        selectedvalue="${this.selectedvalue}"
                        onchange="{{this.props.onchange}}"
                    ></custom-select>
                    ${this.includedistrict !== false ? this.districts : ''}
                </div>
            `;
        },
        callback: function(){
            this.ready(()=>{
                PageLess.Request({
                    url: `/api/view-all-counties`,
                    method: "GET",
                }).then(result=>{
                    if(result.status == 200){
                        let data    = result.response_body;
                        let options = [];
                        data.forEach(role=>{
                            options.push({
                                text: role.title,
                                value: role.id,
                                districts: role.districts
                            });
                        });
                        this.setData({
                            options: options
                        });
                    }
                });
            });
        }
    }),

    DistrictSelect : new PageLessComponent("district-select", {
        data: {
            countyid: '',
            requried: '',
            selectedvalue: '',
            options: [
                {text: '...', value: 'Loading...'}
            ],
        },
        view: function(){
            return /*html*/`
                <div>
                    <custom-select
                        identity="district"
                        icon="map-marker-alt"
                        text="District"
                        items='${JSON.stringify(this.options)}'
                        ${this.required != '' ? 'required="required"' : ''}
                        selectedvalue="${this.selectedvalue}"
                    ></custom-select>
                </div>
            `;
        },
        callback: function(){
            this.ready(()=>{
                PageLess.Request({
                    url: `/api/view-district-by-id/${this.countyid}`,
                    method: "GET",
                }).then(result=>{
                    if(result.status == 200){
                        let data    = result.response_body;
                        let options = [];
                        data.forEach(role=>{
                            options.push({
                                text: role.district_title,
                                value: role.id
                            });
                        });
                        this.setData({
                            options: options
                        });
                    }
                });
            });
        }
    }),

    CurrencySelect : new PageLessComponent("currency-select", {
        data: {
            selectedvalue: '',
            options: [
                {text: 'USD', value: 'usd'},
                {text: 'LRD', value: 'lrd'},
            ],
        },
        props: {
            key: function(){},
        },
        view: function(){
            return /*html*/`
                <div>
                    <custom-select
                        identity="currency"
                        icon="globe-africa"
                        text="Currency"
                        items='${JSON.stringify(this.options)}'
                        required="required"
                        selectedvalue="${this.selectedvalue}"
                    ></custom-select>
                </div>
            `;
        }
    }),

    AgeRangeSelect : new PageLessComponent("age-range-select", {
        data: {
            selectedvalue: '',
            options: [
                {text: '18-25', value: '18-25'},
                {text: '26-35', value: '26-35'},
                {text: '36-45', value: '36-45'},
                {text: '46-55', value: '46-55'},
                {text: '56-65', value: '56-65'},
                {text: '66 Above', value: '66 Above'},
            ],
        },
        view: function(){
            return /*html*/`
                <div>
                    <custom-select
                        identity="age-range"
                        icon="globe-africa"
                        text="Age"
                        items='${JSON.stringify(this.options)}'
                        required="required"
                        selectedvalue="${this.selectedvalue}"
                    ></custom-select>
                </div>
            `;
        }
    }),

    GenderSelect : new PageLessComponent("gender-select", {
        data: {
            selectedvalue: '',
            options: [
                {text: 'Male', value: 'male'},
                {text: 'Female', value: 'female'},
                {text: 'Others', value: 'others'}
            ],
        },
        props: {
            key: function(){},
        },
        view: function(){
            return /*html*/`
                <div>
                    <custom-select
                        identity="gender"
                        icon="venus-mars"
                        text="Gender"
                        items='${JSON.stringify(this.options)}'
                        required="required"
                        selectedvalue="${this.selectedvalue}"
                    ></custom-select>
                </div>
            `;
        }
    }),

    BackButton : new PageLessComponent("back-button", {
        data: {
            title: "Go Back",
            outterclassname: '',
            classname: '',
            icon: 'arrow-left',
        },
        props: {
            onback: function(){
                PageLess.GoBack();
            }
        },  
        view: function(){
            return /*html*/`
                <div class="${this.outterclassname}">
                    <pageless-button attributes='title="${this.title}"' type="button" class="text-mw-primary-dark btn btn-circle ${this.classname}" text='<i class="fa fa-${this.icon} fa-lg"></i>' onclick="{{this.props.onback}}"></pageless-button>
                </div>
            `;
        }
    }),

    // image component
    Image : new PageLessComponent('bg-image', {
        data:{
            src: '',
            rounded: "false",
            classname: '',
            attributes: '',
            style: '',
        },
        view: function(){
            return /*html*/ `
                <div class="image ${this.classname} ${(this.rounded == 'true') ? 'rounded' : ''}" 
                    ${(this.src != '') ? `style="background: url('${this.src}');  ${(this.style != '') ? this.style : ''}"` : ''} 
                >${(this.src != '') ? ' ' : ''}</div>
            `;
        }
    }),

    circularLoader : new PageLessComponent("circular-loader", {
        data: {
            size: "2x",
        },
        view: function(){
            return /*html*/`
                
                <div class="w-100 p-2 text-center">
                    <span class="fad fa-${this.size} fa-spinner-third fa-spin text-muted fa-primary-opacity-0_7"></span>
                </div>
                
            `;
        }
    }),

    chip : new PageLessComponent("custom-chip", {
        data: {
            identity: '',
            type: '',
            icon: "times",
            text: '',
            close: false,
            style: '',
            classname: '',
            closable: 'true',
        },
        props: {
            onclick: function(){},
            onclose: function(event){
                event.stopPropagation();
                this.parentComponent.close = true;
                this.parentComponent.click();
            }
        },
        view: function(){
            return /*html*/`
                <div class="col-auto chip chip-${this.type} w-100px ${this.identity} ${this.classname}" id="${this.identity}" onclick="{{this.props.onclick}}" style="${this.style}">
                    ${this.text}&nbsp;
                    ${this.closable === 'true' ? /*html*/`<pageless-button identity="chip-close" text='<i class="fa fa-${this.icon}"></i>' classname="btn-clean" onclick="{{this.props.onclose}}"></pageless-button>` : ''}
                </div>
            `;
        },
    }),

    // List View Proloader component
    listViewPreloader : new PageLessComponent("list-view-preloader", {
        view: function(){
            return /*html*/`
                <div class="col-12 ">
                    <div class="row p-2 p-sm-2 p-xl-2">
                        <div class="item p-2 bg-white no-container-shadow h-50px">
        
                            <div class="properties">
                                <div class="title"></div>
                            </div>
        
                            <div class="action h-10px w-50px" style="border-radius: 5px !important;"></div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    //detailed linked list item 
    detailedLIstComponent : new PageLessComponent("detailed-linked-list", {
        data: {
            title: null,
            description: null,
            href: null,
        },
        view: function(){
            return /*html*/`
                <div class="col-12">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-5 main-content-item container-shadow widg-link" href="${this.href}">
                            <div class="settings-details">
                                <div class="settings-body">
                                    <div class="settings-title text-muted">${this.title}</div>
                                    <div class="settings-details text-muted">${this.description}</div>
                                </div>
                            </div>
                            <div class="settings-action">
                                <label class="custom-toggler service-level-action">
                                    <i class="fa text-muted fa-lg fa-angle-right"></i> 
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    //regular list item 
    listItem : new PageLessComponent("list-item", {
        data: {
            classname: '',
            icon: '',
            title: '',
            description: '',
            actionicon: 'ellipsis-v',
            href: '',
        },
        view: function(){
            return /*html*/`
                <div class="col-12">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-6 main-content-item container-shadow ${this.classname} ${this.href != '' ? 'widg-link' : ''}" href="${this.href}">
                            <div class="settings-details">
                                ${this.icon != '' ? /*html*/`<div class="settings-icon-container"><span><i class="fad fa-${this.icon} text-t-orange fa-swap-opacity"></i> </span></div>` : ''}
                                <div class="settings-body">
                                    <div class="settings-title text-muted text-left">${this.title}</div>
                                    <div class="settings-details text-muted text-left">${this.description}</div>
                                </div>
                            </div>
                            <div class="settings-action position-relative">
                                <pageless-button class="btn-circle" text='<i class="fa text-muted fa-lg fa-${this.actionicon}"></i>'></pageless-button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    // list items for the staff roles
    AccountType : new PageLessComponent("account-type", {
        data: {
            id: "",
            name: "",
            icon: "",
            color: "",
            accounttype: "",
            rights: []
        },
        props: {
            oncontextclick: function(event){
                event.stopPropagation();
                PageLess.ContextMenu([
                    {
                        text: "Manage Rights",
                        callback: ()=>{
                            PageLess.Request({
                                url: `/api/view-user-account-group-modules/${this.parentComponent.id}`,
                                method: "GET",
                                beforeSend: ()=>{
                                    PageLess.ChangeButtonState(this, '');
                                }
                            }).then(result=>{
                                PageLess.RestoreButtonState(this);
                                if (result.status == 200) {
                                    let data                 = result.response_body;
                                    let assignedModules      = data.assigned_modules[0];
                                    let assignedModulesInput = `<div>The following roles are currently assigned to this account type</div>`;
                                    if (assignedModules != 404) {
                                        assignedModules.forEach(module => {
                                            assignedModulesInput += /*html*/ `
                                                <check-box checked="1" attributes='disabled="disabled"' classname="modules" text="${module.module_title}" value="${module.module_id}" identity="${module.module_id}"></check-box>
                                            `;
                                        });

                                    } else {
                                        assignedModulesInput = /*html*/ `
                                            <no-data icon="fa-key" text="There are no modules assined to this role yet. "></no-data>
                                        `;
                                    }
                                    Modal.BuildForm({
                                        title: "Assigned Modules",
                                        icon: "key",
                                        inputs: /*html*/ `
                                            ${assignedModulesInput}
                                        `,
                                        submitText: "Assign New",
                                        closable: false,
                                        autoClose: false,
                                    }, (assignedModuleValues)=>{
                                        let assignableModules = data.app_modules[0];
                                        if (assignableModules.length > 0) {
                                            let modulesInput      = '';
                                            assignableModules.forEach(module => {
                                                let checkState = "0";
                                                if (assignedModules != 404) {
                                                    assignedModules.forEach(aModule=>{
                                                        if(aModule.module_id == module.module_id){
                                                            checkState = "1";
                                                        }
                                                    });
                                                }

                                                if(checkState == "0"){
                                                    modulesInput += /*html*/ `
                                                        <check-box classname="modules" text="${module.module_title}" value="${module.module_id}" identity="${module.module_id}"></check-box>
                                                    `;
                                                }

                                            });
                                            Modal.BuildForm({
                                                title: "Assign Modules",
                                                icon: "key",
                                                description: modulesInput == '' ? `<no-data icon="fa-key" text="There are no more modules available to assign. You've already assigned all to this role"></no-data>` : `Please select the modules you with to assign to this role`,
                                                inputs: /*html*/ `
                                                    <div class="w-100">
                                                        ${modulesInput}
                                                    </div>
                                                `,
                                                noSubmit: modulesInput == '' ? true : false,
                                                submitText: "Assign",
                                                closeText: 'Cancel',
                                                closable: false,
                                                autoClose: false,
                                            }, assignmentValues=>{
                                                let moduleCheckBoxes = assignmentValues.modal.querySelectorAll('.modules:checked');
                                                if (moduleCheckBoxes.length > 0) {
                                                    let selectedModules = [];
                                                    moduleCheckBoxes.forEach(moduleCheckBox=>{
                                                        selectedModules.push(moduleCheckBox.value);
                                                    });

                                                    PageLess.Request({
                                                        url: `/api/assign-modules-to-account-group`,
                                                        method: "POST",
                                                        data: {
                                                            account_group_id: this.parentComponent.id,
                                                            module_list: selectedModules
                                                        },
                                                        beforeSend: ()=>{
                                                            PageLess.ChangeButtonState(assignmentValues.submitBtn);
                                                        }
                                                    }, true).then(result=>{
                                                        PageLess.RestoreButtonState(assignmentValues.submitBtn);
                                                        if (result.status == 200) {
                                                            PageLess.Toast('success', result.message);
                                                            Modal.Close(assignedModuleValues.modal);
                                                            Modal.Close(assignmentValues.modal);
                                                        } else{
                                                            PageLess.Toast('danger', result.message, 5000);
                                                        }
                                                    });
                                                    
                                                } else {
                                                    PageLess.Toast('danger', "Please select the modules you wish to assign before you proceeding", 5000);
                                                }
                                            });
                                            
                                        } else {
                                            PageLess.Toast('danger', 'There a no modules to assign at the moment. Please try again later');
                                        }
                                    });
                                } else {
                                    PageLess.Toast("danger", 'Unable to fetch role details at the moment.');
                                }
                            });
                        }
                    },
                    {
                        text: "Delete",
                        callback: ()=>{
                            Modal.Confirmation("Confirm Deletion", "Before deleting, please make sure this roles is not assigned to any user. This action cannot be undone! Are you sure you want to continue?").then(()=>{
                                
                            });
                        }
                    }
                ], this);
            },
        },
        view: function(){
            
            return /*html*/`
                <div class="col-12">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-6 main-content-item container-shadow pl-3">
                            <div class="settings-icon-container w-30px">
                                <span><i class="fad fa-${this.icon.replace('fa-', '')} fa-swap-opacity fa-lg" style="color: ${this.color} !important;"></i> </span>
                            </div>
                            <div class="settings-details">
                                <div class="settings-body">
                                    <div class="settings-title text-muted">${this.name}</div>
                                    <div class="settings-details text-muted text-uppercase small">${this.accounttype}</div>
                                </div>
                            </div>
                            <div class="settings-action">
                                <pageless-button class="btn-circle" text='<i class="fa text-muted fa-lg fa-ellipsis-v"></i>' onclick="{{this.props.oncontextclick}}"></pageless-button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    // generic scroll view
    scrollView : new PageLessComponent("vertical-scroll-view", {
        data: {
            nodataicon: 'fa-meh',
            requestData: 'notset',
            scrollable: true,
            pageNum: 0,
            preloader: false,
            loadingData: false,
            pageEnded: false,
            preloadercount: 20,
            circularLoader: PageLessComponent.Render(/*html*/`<circular-loader></circular-loader>`),
            childrenData: '',
            chips: ''
        },
        props: {
            onscroll: function(){
                if(this.scrollable === true){
                    this.showLoader      = this.props.showLoader.bind(this);
                    this.hideLoader      = this.props.hideLoader.bind(this);
                    this.setLoadingState = this.props.setLoadingState.bind(this);
                    this.endPage         = this.props.endPage.bind(this);
                    this.addChild        = this.props.addChild.bind(this);
                    this.addChildren     = this.props.addChildren.bind(this);
                    let scrollBottom     = this.scrollHeight - this.scrollTop - this.clientHeight;
                    if (scrollBottom <= 10) {
                        if(this.pageEnded === false && this.loadingData === false){
                            this.pageNum = this.pageNum+1;
                            if (this.requestData.data['page-num'] !== undefined) {
                                this.requestData.data['page-num'] = this.pageNum;
                            }
                            if (this.requestData.data['pager'] !== undefined) {
                                this.requestData.data['pager'] = this.pageNum;
                            }
                            this.setLoadingState(true);
                            PageLess.Request(this.requestData, this.requestFullAsync).then(result=>{
                                this.setLoadingState(false);
                                if(result.status == 200){
                                    let data = typeof this.getData == 'function' ? this.getData(result.response_body) : result.response_body;
                                    data.forEach(item=>{
                                        let renderedItem = PageLessComponent.Render(this.getChild(item));
                                        this.addChild(renderedItem);
                                    });
                                }
                                else if(result.status == 404){
                                    this.endPage();
                                }
                                else{
                                    this.errorHandler.call(this, result);
                                }
                            });
                        }
                    }
                }
            },

            showLoader: function(){
                this.querySelector('.list-items-container').appendChild(this.circularLoader);
            },

            hideLoader: function(){
                this.querySelector('.list-items-container').removeChild(this.circularLoader);
            },

            setLoadingState: function(value){
                if (value === true) {
                    this.loadingData = true;
                    this.props.showLoader.call(this);
                } else {
                    this.loadingData = false;
                    this.props.hideLoader.call(this);
                }
            }, 

            getPageNum: function(){
                return this.pageNum;
            },

            endPage: function(){
                this.pageEnded  = true;
                let child       = document.createElement('div');
                child.innerHTML = /*html*/ `<span>- End of Page -</span>`;

                child.classList.add('col-12', 'pt-2', 'text-center', 'text-muted');
                this.addChild(child);
            },

            setChild: function(handler){
                if (typeof handler == 'function') {
                    this.getChild = handler.bind(this);
                } else {
                    this.getChild = null;
                    throw "Error: setChild expected arg 1 to be a function. "+ typeof handler + " given";
                }
            },

            addChild: function(child){
                this.querySelector('.list-items-container').appendChild(child);
            },

            addChildren: function(children){
                children.forEach(child=>{
                    this.props.addChild.call(this, child);
                });
            },

            setRequest: function(data, fullAsync = false){
                if (typeof data == 'object') {
                    this.requestData = data;
                    this.requestFullAsync = fullAsync;
                } else {
                    this.requestData = null;
                    throw "Error: setRequest expected arg 1 to be an object. "+ typeof data + " given";
                }
            },

            mapData: function(handler){
                if (typeof handler == 'function') {
                    this.getData = handler.bind(this);
                } else {
                    this.getData = null;
                    throw "Error: mapData expected arg 1 to be a function. "+ typeof handler + " given";
                }
            },
            onError: function(handler){
                if (typeof handler == 'function') {
                    this.errorHandler = handler.bind(this);
                } else {
                    this.getData = null;
                    throw "Error: onError expected arg 1 to be a function. "+ typeof handler + " given";
                }
            },
            onCompleted: function(handler){
                if (typeof handler == 'function') {
                    this.successHandler = handler.bind(this);
                } else {
                    this.getData = null;
                    throw "Error: onCompleted expected arg 1 to be a function. "+ typeof handler + " given";
                }
            },

            update: function(callbackOnly = false){
                this.pageEnded  = false;
                this.pageNum    = 0;
                this.refresh(callbackOnly);
            }, 

            onload: function(){}
        },
        view: function(){
            let cardpreloader = '', listpreloader = '';
            for (let index = 0; index < parseInt(this.preloadercount); index++) {
                cardpreloader += /*html*/ `<image-card-preloader></image-card-preloader>`;
                listpreloader += /*html*/ `<list-view-preloader></list-view-preloader>`;
            }
            this.childrenData = this.childrenData == '' && this.preloader !== false ? (this.preloader == 'card' ? cardpreloader : listpreloader) : this.childrenData;
            let chips = '';
            if (typeof this.chips == 'object') {
                for (const chip in this.chips) {
                    if (Object.hasOwnProperty.call(this.chips, chip)) {
                        const element = this.chips[chip];
                        chips += element;
                    }
                }
            } else {
                chips = this.chips;
            }
            
            return /*html*/`
                <div class="flex-1 w-100 p-0 p-sm-1 p-md-3 p-xl-4 scroll-y scroll-view products-view ${this.childrenData.includes('no-data') ? 'd-flex align-items-center align-content-center' : ''}" onscroll="{{this.props.onscroll}}">
                    <div class="w-100 p-1 d-flex flex-wrap list-items-container">
                        ${this.childrenData}
                    </div>
                    <div class="w-auto ${this.chips == '' ? 'd-none' : ''}" style="position: absolute !important; bottom: 0 !important; right: 0; z-index: 1">
                        <div class="w-auto align-items-end d-flex px-2 pb-1 pb-md-3 pb-xl-4 scroll-x chips-container flex-column flex-sm-row">
                            ${chips}
                        </div>
                    </div>
                </div>
            `;
        },
        callback: function(){
            this.ready(()=>{
                this.getPageNum  = this.props.getPageNum.bind(this);
                this.setRequest  = this.props.setRequest.bind(this);
                this.setChild    = this.props.setChild.bind(this);
                this.mapData     = this.props.mapData.bind(this);
                this.onError     = this.props.onError.bind(this);
                this.onCompleted = this.props.onCompleted.bind(this);
                this.urlData     = this.parentComponent.urlData;
                this.update      = this.props.update;
                let loader       = this.requestData === 'notset' ? this.props.onload.call(this) : (new Promise(resolve=>resolve()));

                if (typeof loader == 'object' && loader.then !== undefined) {
                    loader.then(()=>{
                        if (this.getChild !== undefined && typeof this.getChild == 'function') {
                            PageLess.Request(this.requestData, this.requestFullAsync).then(result=>{
                                if(result.status == 200){
                                    if (typeof this.successHandler == 'function') {
                                        this.successHandler.call(this, result);
                                    }
                                    let data    = typeof this.getData == 'function' ? this.getData(result.response_body) : result.response_body;
                                    let vendors = ``;
                                    data.forEach(vendor =>{
                                        vendors += this.getChild(vendor);
                                    });
                        
                                    let newView = this.setData({
                                        childrenData: vendors != `` ? vendors : /*html*/`<no-data icon="${this.nodataicon}" text="No available data"></no-data>`
                                    });

                                }
                                else if(result.status == 404){
                                    if (typeof this.successHandler == 'function') {
                                        this.successHandler.call(this, result);
                                    }
                                    let newView = this.setData({
                                        childrenData: /*html*/ `<no-data icon="${this.nodataicon}" text="${result.message}"></no-data>`
                                    });

                                }
                                else if(result.status == 602){
                                    if (typeof this.successHandler == 'function') {
                                        this.successHandler.call(this, result);
                                    }
                                    let newView = this.setData({
                                        childrenData: /*html*/ `<no-data icon="fa-low-vision" text="${result.message}"></no-data>`
                                    });

                                }
                                else{
                                    if (typeof this.errorHandler == 'function') {
                                        this.errorHandler(result);
                                    }
                                }
                            });
                        } else {
                            throw "Error: setChild must be defined. ";
                        }
                    });
                }
                else{
                    throw "Error: the Scroll View onload event must return a promise. " + typeof loader + " was returned instead";
                }
            });
        }
    }),

    // User Card Proloader component
    imageCardPreloader : new PageLessComponent("image-card-preloader", {
        view: function(){
            return /*html*/`
                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                    <div class="row p-2">
                        <div class="col-12">
                            <div class="row card-with-image no-container-shadow">
                                <div class="image-container"></div>
                                <div class="properties-container d-flex flex-column">
                                    <div class="properties w-100 mb-1"></div>
                                    <div class="properties w-100 mb-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),
}