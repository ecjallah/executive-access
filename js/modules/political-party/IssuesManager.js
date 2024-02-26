import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./PoliticalPartyComponents.js";
export const widget = new PageLessComponent("issues-manager-widget", {
    data: {
        title: "Political Issues Manager",
    },
    props: {
        addnew: function(){
            Modal.BuildForm({
                title: "Add New Issue",
                icon: "question-square",
                description: `Please enter the details below`,
                inputs: /*html*/ `
                    <text-input text="Title" icon="key" identity="title" required="required"></text-input>
                    <long-text-input text="Description" identity="description" icon="align-justify" required="required"></long-text-input>
                    <text-input text="Base Point" icon="star" identity="base" required="required"></text-input>
                `,
                submitText: "Add",
                closable: false,
                autoClose: false,
            }, values=>{
                let submitBtn = values.modal.querySelector('button[type=submit]');
                PageLess.Request({
                    url: `/api/add-new-issue`,
                    method: "POST",
                    data: {
                        title: values.title,
                        description: values.description,
                        base_value: values.base,
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
                    url: `/api/view-all-issues`,
                    method: "GET",
                });

                this.mapData(data=>{
                    return data.issues;
                });

                this.setChild(data=>{
                    return /*html*/ `<editable-political-issue id="${data.id}" title="${data.issue_title}" description="${data.description}" base="${data.raw_base_value}"></editable-political-issue>`;
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