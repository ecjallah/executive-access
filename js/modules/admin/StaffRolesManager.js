import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./Components.js";
export const widget = new PageLessComponent("staff-role-management-widget", {
    data: {
        title: "Staff Role Manager",
    },
    props: {
        addnew: function(){
            Modal.BuildForm({
                title: "Add New Role",
                icon: "key",
                description: `Please enter the details below`,
                inputs: /*html*/ `
                    <text-input text="Title" icon="key" identity="title" required="required"></text-input>
                `,
                submitText: "Add",
                closable: false,
                autoClose: false,
            }, values=>{
                let submitBtn = values.modal.querySelector('button[type=submit]');
                PageLess.Request({
                    url: `/api/add-staff-role`,
                    method: "POST",
                    data: {
                        role_title: values.title
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
                    url: `/api/view-staff-roles`,
                    method: "GET",
                });

                this.mapData(data=>{
                    return data.role_details;
                });

                this.setChild(data=>{
                    return /*html*/ `<staff-role id="${data.role_id}" name="${data.role_title}"></staff-role>`;
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