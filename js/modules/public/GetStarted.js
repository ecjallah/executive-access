import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
export const widget = new PageLessComponent("getting-started-widget", {
        data: {
            title: 'Home',
            occupation: [
                {text: "Student", value: "Student"},
                {text: "Self-employed", value: "Self-employed"},
                {text: "Employed", value: "Employed"},
                {text: "Unemployed", value: "Unemployed"},
            ]
        },
        props: {
            onsubmit: function(event){
                event.preventDefault();

                const submitBtn    = this.querySelector('button[type=submit]');
                const name         = this.querySelector('.visitor-name').value;
                const ministry     = this.querySelector('.ministry').value;
                const department   = this.querySelector('.department').value;
                const executive    = this.querySelector('.executive').value;
                Modal.BuildForm({
                    title: "Enter Appointment Details",
                    icon: "clock",
                    description: ``,
                    inputs: /*html*/ `
                        <number-input icon="phone-alt" text="Phone Valid Number" identity="number" required="required"></number-input>
                        <long-text-input icon="align-justify" text="Purpose" identity="purpose" required="required"></long-text-input>
                        <date-input text="Date"></date-input>
                        <time-input icon="clock" text="Start Time" identity="start-time" required="required" required="required"></time-input>
                        <time-input icon="clock" text="End Time" identity="end-time" required="required" required="required"></time-input>
                    `,
                    submitText: "Book",
                    closable: false,
                    autoClose: false,
                }, values=>{
                    Modal.Confirmation("Confirm Action", "Please make sure you've verified your appointment details. Are you sure you want to continue?").then(()=>{
                        PageLess.Request({
                            url: `/api/set-outside-appointment`,
                            method: "POST",
                            data: {
                                ministry_id: ministry,
                                department_id: department,
                                executive_id: executive,
                                visitor_name: name,
                                purpose: values.purpose,
                                number: values.number,
                                visit_date: `${values.year}-${values.month}-${values.day}`,
                                start_time: values['start-time'],
                                end_time: values['end-time'],
                            },
                            beforeSend: ()=>{
                                PageLess.ChangeButtonState(values.submitBtn, 'Booking')
                            }
                        }, true).then(result=>{
                            PageLess.RestoreButtonState(values.submitBtn);
                            if (result.status == 200) {
                                (new PageLess('/completed')).route().then(()=>{
                                    Modal.Close(values.modal);
                                    this.remove();
                                });
                            } else {
                                PageLess.Toast('danger', result.message);
                            }
                        });
                    });
                });
            },

            onback: function(){
                PageLess.GoBack();
            }
        },
        view: function(){
            return /*html*/`
                <form class="main-content scroll-y" onsubmit="{{this.props.onsubmit}}">
                    <div class="row login-container no-gutters">
                        <div class="col-12 col-sm-11 col-md-7 col-lg-6 col-xl-5 login-form-container">
                            <div class="login-form" id="login-form">
                                <div class="flex-1 w-100 p-1 p-sm-2 p-md-3 p-xl-4 scroll-y">
                                    <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-start align-content-center">
                                        <img class="w-100px mt-4" style="height: auto;" alt="liberian-seal" src="/media/images/seal.png">
                                        <div class="description text-left py-3 my-3">
                                            Ready to take control of your schedule? Scheduling has been made easier. Here, you can find the perfect time slot, all in a few simple clicks. Fill in your details below to <b>Get Started</b>!
                                        </div>
                                        <div class="w-100">
                                            <text-input icon="user" text="Enter Your Name" identity="visitor-name" required="required"></text-input>
                                            <ministry-select includedepartments=true></ministry-select>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-100 d-flex justify-content-end mt-2 mb-3">
                                    <pageless-button type="button" classname="btn btn-light col-5 col-lg-4 col-xl-3 mr-4" text="Back" onclick="{{this.props.onback}}"></pageless-button>
                                    <pageless-button type="submit" classname="btn btn-primary col-5 col-lg-4 col-xl-3" text="Next"></pageless-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            `;
        }
    });