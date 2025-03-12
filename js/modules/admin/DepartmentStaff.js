import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./Components.js";
export const widget = function(){
    return new PageLessComponent("staff-management-widget", {
        data: {
            title: "Department Staff",
            departmentid: PageLess.GetURLData()[1],
            staff: ""
        },
        props: {
            assignstaff: function(){
                const parent = this.parentComponent;
                Modal.BuildForm({
                    title: "Assign Staff",
                    icon: "user-plus",
                    description: `Please select the staff below`,
                    inputs: /*html*/ `
                        <staff-select></staff-select>
                    `,
                    submitText: "Assign Staff",
                    closable: false,
                    autoClose: false,
                }, values=>{
                    PageLess.Request({
                        url: `/api/assign-staff-to-department`,
                        method: "POST",
                        data: {
                            staff_id : values.staff,
                            department_id : parent.departmentid
                        },
                        beforeSend: ()=>{
                            PageLess.ChangeButtonState(values.submitBtn, 'Adding');
                        }
                    }, true).then(result=>{
                        PageLess.RestoreButtonState(values.submitBtn);
                        if (result.status == 200) {
                            Modal.Success('Completed Successfully', result.message);
                            this.parentComponent.querySelector('.scroll-view').update();
                            Modal.Close(values.modal);
                        } else {
                            PageLess.Toast('danger', result.message, 5000);
                        }
                    });
                });
            },
            onload: function(){
                return new Promise(resolve => {
                    this.setRequest({
                        url: `/api/get-department-staff/${this.parentComponent.departmentid}`,
                        method: "GET"
                    });

                    this.setChild(data=>{
                        return /*html*/ `
                            <department-staff 
                                departmentid="${this.parentComponent.departmentid}"
                                userid="${data.user_id}"
                                fullname="${data.full_name}"
                                image="${data.image}"
                            ></department-staff>`;
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
                        <pageless-button classname="tool" text="Assign Staff" onclick="{{this.props.assignstaff}}"></pageless-button>
                    </div>
                    <div class="main-content-body">
                        ${this.staff}
                    </div>
                </div>
            `;
        },
        callback: function(){
            PageLess.Request({
                url: `/api/get-department-details/${this.departmentid}`,
                method: "GET",
            }).then(result=>{
                if (result.status == 200) {
                    this.setData({
                        staff: /*html*/ `<vertical-scroll-view nodataicon="fa-users" preloader="list" onload="{{this.props.onload}}"></vertical-scroll-view>`,
                        title: result.response_body.title + " Staff"
                    })
                } else {
                    PageLess.Toast('danger', "Invalid department ID provided", 5000)
                    PageLess.GoBack();
                }
            });
        }
    });
}