import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./Components.js";
export const widget = new PageLessComponent("online-department-appointment-manager-widget", {
    data: {
        title: "Online Deportment Appointment Manager",
        departmentid: ""
    },
    props: {
        onload: function(){
            return new Promise(resolve => {
                this.setRequest({
                    url: `/api/get-online-appointments`,
                    method: "GET",
                    data: {
                        pager: this.getPageNum(),
                        appointment_type: "online",
                        approval_status: "pending",
                    }
                });

                // this.mapData(data=>{
                //     return data.appointments;
                // });

                this.setChild(data=>{
                    return /*html*/ `<online-appointment-group date="${data.formatted_date}" items='${JSON.stringify(data.appointments)}' departmentonly={true}></online-appointment-group>`;
                });

                this.onCompleted(response=>{
                    if ((response.status == 404 || response.status == 200) && response.response_body !== null) {
                        this.parentComponent.departmentid = response.response_body.department_id
                    }
                });

                resolve();
            });
        },
    },
    view: function(){
        return /*html*/`
            <div class="main-content">
                <main-content-header title="${this.title}"></main-content-header>
                <div class="main-content-body">
                    <vertical-scroll-view nodataicon="fa-calendar-check" preloader="list" onload="{{this.props.onload}}"></vertical-scroll-view>
                </div>
            </div>
        `;
    },
});