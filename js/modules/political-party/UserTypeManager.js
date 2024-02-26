import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./PoliticalPartyComponents.js";
export const widget = new PageLessComponent("user-type-manager-widget", {
    data: {
        title: "Account Type Manager",
    },
    props: {
        addnew: function(){
            Modal.BuildForm({
                title: "Add New Role",
                icon: "key",
                description: `Please enter the details below`,
                inputs: /*html*/ `
                    <text-input text="Title" icon="key" identity="title" required="required"></text-input>
                    <text-input text="Account Type" icon="user-shield" identity="account-type" required="required"></text-input>
                    <text-input text="Icon" icon="icons" identity="icon" required="required"></text-input>
                    <text-input text="Color" icon="palette" identity="color" required="required"></text-input>
                `,
                submitText: "Add",
                closable: false,
                autoClose: false,
            }, values=>{
                let submitBtn = values.modal.querySelector('button[type=submit]');
                PageLess.Request({
                    url: `/api/create-user-account-group`,
                    method: "POST",
                    data: {
                        account_type: values['account-type'],
                        title: values.title,
                        icon: values.icon,
                        color: values.color,
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
                    url: `/api/view-all-account-groups`,
                    method: "GET",
                });

                this.setChild(data=>{
                    return /*html*/ `<account-type id="${data.id}" name="${data.title}" icon="${data.icon}" color="${data.color}" accounttype="${data.account_type}"></account-type>`;
                });

                resolve();
            });
        }
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