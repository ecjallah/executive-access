import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./Components.js";
export const widget = new PageLessComponent("executives-manager-widget", {
    data: {
        title: "Executives Manager",
    },
    props: {
        addnew: function(){
            Modal.BuildForm({
                title: "Add New",
                icon: "user-tie",
                description: `Please enter the details below`,
                inputs: /*html*/ `
                    <text-input icon="user" text="First Name" identity="firstname" required="required"></text-input>
                    <text-input icon="user" text="Middle Name" identity="middlename"></text-input>
                    <text-input icon="user" text="Last Name" identity="lastname" required="required"></text-input>
                    <number-input icon="user" text="Phone Number" identity="number" required="required"></number-input>
                    <department-select></department-select>
                `,
                submitText: "Add",
                closable: false,
                autoClose: false,
            }, values=>{
                let submitBtn = values.modal.querySelector('button[type=submit]');
                PageLess.Request({
                    url: `/api/add-new-executive-member`,
                    method: "POST",
                    data: {
                        first_name: values.firstname,
                        middle_name: values.middlename,
                        last_name: values.lastname,
                        number: values.number,
                        department_id: values.department
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
                    url: `/api/get-executive-members`,
                    method: "GET",
                    data: {
                        pager: this.getPageNum(),
                        filter: 'active'
                    }
                });

                this.setChild(details=>{
                    const data = details.staff_info;
                    return /*html*/ `<executive-item id="${data.id}" fullname="${data.full_name}" firstname="${data.first_name}" middlename="${data.middle_name}" lastname="${data.last_name}" departmentid="${data.department_id}" department="${details.department_info.title}" number="${data.number}"></executive-item>`;
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
                    <vertical-scroll-view nodataicon="fa-user-tie" preloader="list" onload="{{this.props.onload}}" scrollable="false"></vertical-scroll-view>
                </div>
            </div>
        `;
    },
});