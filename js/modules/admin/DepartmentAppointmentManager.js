import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./Components.js";
export const widget = new PageLessComponent("appointment-manager-widget", {
    data: {
        title: "Deportment Appointment Manager",
        departmentid: ""
    },
    props: {
        addnew: function(){
            if (this.parentComponent.departmentid != '') {
                Modal.BuildForm({
                    title: "Create New",
                    icon: "user-tie",
                    description: `Please enter the details below`,
                    inputs: /*html*/ `
                        <text-input icon="user" text="Visitor Name" identity="visitor-name" required="required"></text-input>
                        <executive-select departmentid="${this.parentComponent.departmentid}"></executive-select>
                        <long-text-input icon="align-justify" text="Purpose" identity="purpose"></long-text-input>
                        <number-input icon="phone-alt" text="Visitor Phone Number" identity="number"></number-input>
                        <date-input text="Date"></date-input>
                        <time-input icon="clock" text="Start Time" identity="start-time" required="required"></time-input>
                        <time-input icon="clock" text="End Time" identity="end-time" required="required"></time-input>
                    `,
                    submitText: "Add",
                    closable: false,
                    autoClose: false,
                }, values=>{
                    console.log(values);
                    PageLess.Request({
                        url: `/api/add-new-department-appointment`,
                        method: "POST",
                        data: {
                            executive_id: values.executive,
                            visitor_name: values['visitor-name'],
                            number: values.number,
                            purpose: values.purpose,
                            visit_date: `${values.year}-${values.month}-${values.day}`,
                            start_time: values['start-time'],
                            end_time: values['end-time']
                        },
                        beforeSend: ()=>{
                            PageLess.ChangeButtonState(values.submitBtn, 'Adding');
                        }
                    }, true).then(result=>{
                        PageLess.RestoreButtonState(values.submitBtn);
                        if (result.status == 200) {
                            this.parentComponent.querySelector('.scroll-view').update();
                            PageLess.Toast('success', result.message);
                            Modal.Close(values.modal);
                        } else {
                            PageLess.Toast('danger', result.message);
                        }
                    });
                });
            } else {
                PageLess.Toast('danger', "Department not set. Please wait a minute and try again");
            }
        },

        onload: function(){
            return new Promise(resolve => {
                this.setRequest({
                    url: `/api/get-appointments-from-department`,
                    method: "GET",
                    data: {
                        pager: this.getPageNum()
                    }
                });

                this.mapData(data=>{
                    return data.department_appointments;
                });

                this.setChild(data=>{
                    return /*html*/ `<appointment-group date="${data.formatted_date}" items='${JSON.stringify(data.appointments)}' departmentonly={true}></appointment-group>`;
                });

                this.onCompleted(response=>{
                    if ((response.status == 404 || response.status == 200) && response.response_body !== null) {
                        this.parentComponent.departmentid = response.response_body.department_id
                    }
                });

                resolve();
            });
        },

        onlineappointmentclick: function(){
            (new PageLess(`/department-appointments/online`)).route();
        }
    },
    view: function(){
        return /*html*/`
            <div class="main-content">
                <main-content-header title="${this.title}"></main-content-header>
                <div class="tool-bar">
                    <pageless-button class="tool" text="Create New" onclick="{{this.props.addnew}}"></pageless-button>
                    <pageless-button class="tool" text="Online Appointments" onclick="{{this.props.onlineappointmentclick}}"></pageless-button>
                </div>
                <div class="main-content-body">
                    <vertical-scroll-view nodataicon="fa-calendar-check" preloader="list" onload="{{this.props.onload}}"></vertical-scroll-view>
                </div>
            </div>
        `;
    },
});