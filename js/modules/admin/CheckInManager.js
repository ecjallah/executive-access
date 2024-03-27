import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./Components.js";
export const widget = new PageLessComponent("executives-manager-widget", {
    data: {
        title: "Appointment Checker",
    },
    props: {
        oncheckclick: function(){
            Modal.BuildForm({
                title: "Appointment Checker",
                icon: "calendar-day",
                inputs: /*html*/ `
                    <text-input icon="user" text="Enter Visitor Name" identity="search" required="required"></text-input>
                `,
                submitText: "Check",
                closable: false,
                autoClose: false,
            }, values=>{
                let submitBtn = values.modal.querySelector('button[type=submit]');
                PageLess.Request({
                    url: `/api/lookup-appointment`,
                    method: "POST",
                    data: {
                        search: values.search
                    },
                    beforeSend: ()=>{
                        PageLess.ChangeButtonState(submitBtn, 'Checking');
                    }
                }, true).then(result=>{
                    PageLess.RestoreButtonState(submitBtn);
                    if (result.status == 200) {
                        const data = result.response_body;
                        Modal.Close(values.modal)
                        Modal.BuildForm({
                            title: "Available Appointments",
                            icon: "calendar-check",
                            inputs: /*html*/ `
                                <div class="row border-radius-10">
                                    <vertical-scroll-view nodataicon="fa-calendar-check" preloader="card" preloadercount="3" onload="{{this.props.onload}}"></vertical-scroll-view>
                                </div>
                            `,
                            props: {
                                onload: function(){
                                    // this.setChild(()=>{
                                    //     return false;
                                    // });

                                    // this.request({});

                                    return new Promise(resolve =>{
                                        let appointmentsGroup = '';
                                        data.forEach(group=>{
                                            appointmentsGroup += /*html*/ `<appointment-group date="${group.formatted_date}" items='${JSON.stringify(group.appointments)}' editable="false" checkin="true"></appointment-group>`;
                                        });
                                        this.setData({
                                            childrenData: appointmentsGroup
                                        });

                                        resolve()
                                    });
                                },

                            },
                            noSubmit: true,
                            closable: false,
                            autoClose: false,
                        });
                    } else {
                        PageLess.Toast('danger', result.message);
                    }
                });
            });
        },
    },
    view: function(){
        return /*html*/`
            <div class="main-content">
                <main-content-header title="${this.title}"></main-content-header>
                <div class="main-content-body">
                    <div class="d-flex flex-1 w-100 p-1 p-md-3 p-xl-4 no-scroll">
                        <div class="w-100 d-flex flex-column h-100 align-items-center justify-content-center p-3">
                            <div class="text-center">
                                <no-data icon="fa-calendar-day" text="Click the button below to find a existing appointment"></no-data>
                                <pageless-button text="Verify Appointment" classname="btn btn-primary" onclick="{{this.props.oncheckclick}}"></pageless-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    },
});