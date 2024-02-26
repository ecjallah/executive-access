import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./PoliticalPartyComponents.js";
export const widget = new PageLessComponent("elections-manager-widget", {
    data: {
        title: "Elections Manager",
    },
    props: {
        addnew: function(){
            Modal.BuildForm({
                title: "Add New Election",
                icon: "box-ballot",
                description: `Please enter the details below`,
                inputs: /*html*/ `
                    <text-input text="Title/Name" icon="key" identity="title" required="required"></text-input>
                `,
                submitText: "Add",
                closable: false,
                autoClose: false,
            }, values=>{
                let submitBtn = values.modal.querySelector('button[type=submit]');
                PageLess.Request({
                    url: `/api/add-new-election`,
                    method: "POST",
                    data: {
                        election_title: values.title,
                    },
                    beforeSend: ()=>{
                        PageLess.ChangeButtonState(submitBtn, 'Adding');
                    }
                }, true).then(result=>{
                    PageLess.RestoreButtonState(submitBtn);
                    if (result.status == 200) {
                        this.parentComponent.querySelector('.scroll-view').update();
                        PageLess.Toast('success', result.message);
                        Modal.Close(values.modal);
                    } else {
                        PageLess.Toast('danger', result.message);
                    }
                });
            });
        },

        onload: function(){
            return new Promise(resolve => {
                this.setRequest({
                    url: `/api/view-all-elections`,
                    method: "GET",
                });

                this.setChild(data=>{
                    return /*html*/ `<election-item id="${data.id}" title="${data.title}"></election-item>`;
                });

                resolve();
            });
        },
    },
    view: function(){
        return /*html*/`
            <div class="main-content">
                <main-content-header title="${this.title}"></main-content-header>
                <div class="tool-bar">
                    <pageless-button class="tool" text="Add New" onclick="{{this.props.addnew}}"></pageless-button>
                </div>
                <div class="main-content-body">
                    <vertical-scroll-view nodataicon="fa-key" preloader="list" onload="{{this.props.onload}}" scrollable="false"></vertical-scroll-view>
                </div>
            </div>
        `;
    },
});