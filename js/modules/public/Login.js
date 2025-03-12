import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
export const widget = new PageLessComponent("login-widget", {
        data: {
            title: 'Executive Access',
        },
        props: {
            onsubmit: function(event){
                event.preventDefault();
                let username  = this.querySelector('.username').value.trim();
                let password  = this.querySelector('.password').value.trim();
                let submitBtn = this.querySelector('button[type=submit]');
                PageLess.Request({
                    url: `/api/app-login`,
                    method: "POST",
                    data: {
                        'username': username,
                        'password': password
                    },
                    beforeSend: ()=>{
                        PageLess.ChangeButtonState(submitBtn);
                    }
                }, true).then(result=>{
                    PageLess.RestoreButtonState(submitBtn);
                    if(result.status == 200){
                        window.location.href = '/dashboard';
                    }
                    else{
                        PageLess.Toast('danger', result.message, 5000);
                    }
                });
            },
            
            onback: function(){
                PageLess.GoBack();
            },
            
            onforgotpassword : function(){
                Modal.BuildForm({
                    title: "Password Recovery",
                    icon: "lock",
                    inputs: /*html*/ `
                        <text-input icon="signature" text="Enter Username" identity="rec-username" required=""></text-input>
                    `,
                    submitText: "Verify",
                    closeText: "Back",
                    closable: false,
                    autoClose: false,
                }, usernameValues=>{
                    Modal.BuildForm({
                        title: "Verification",
                        icon: "key",
                        description: `Enter the six (6) digit code that was sent to your email address`,
                        inputs: /*html*/ `
                            <number-input icon="key" text="Enter Code" identity="code" required=""></number-input>
                        `,
                        submitText: "Verify",
                        closeText: "Back",
                        closable: false,
                        autoClose: false,
                    }, codeValues=>{
                        Modal.BuildForm({
                            title: "Change Password",
                            icon: "lock",
                            description: `Enter a new password and confirm in the fields below`,
                            inputs: /*html*/ `
                                <password-input text="New Password" identity="rec-passwd" required="required"></password-input>
                                <password-input text="Confirm New Password" identity="rec-confirm-passwd" required="required"></password-input>
                            `,
                            submitText: "Save",
                            closeText: "Back",
                            closable: false,
                            autoClose: false,
                        }, passwordValues=>{
                            if (passwordValues['rec-passwd'].trim() == passwordValues['rec-confirm-passwd'].trim()) {
                                PageLess.Toast('success', 'Password Successfully Changed. You can now login using your new password', 7000);
                                Modal.Close(usernameValues.modal);
                                Modal.Close(codeValues.modal);
                                Modal.Close(passwordValues.modal);

                            } else {
                                PageLess.Toast('danger', 'Passwords do not match. Check and try again');
                            }
                        });
                    });
                });
            },
            
            createaccount : function(){
                (new PageLess('/political-party/create-account')).route();
            },

            ongoback : function(){
                PageLess.GoBack();
            }
        },
        view: function(){
            return /*html*/`
                <form class="main-content" onsubmit="{{this.props.onsubmit}}" style="overflow: hidden !important;">
                    <div class="row login-container no-gutters">
                        <div class="col-12 col-sm-5 col-md-4 col-lg-3 login-form-container">
                            <div class="login-form" id="login-form">
                                <div class="flex-1 w-100 p-1 p-sm-2 scroll-y">
                                    <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-start align-content-center">
                                        <img class="w-100px mt-4" style="height: auto;" alt="liberian-seal" src="/media/images/seal.png">
                                        <div class="description text-left py-4">
                                            ${this.title}
                                        </div>
                                        <div class="w-100">
                                            <text-input icon="user" text="Username" identity="username" required="required"></text-input>
                                            <password-input icon="key" text="Password" identity="password" required="required"></password-input>
                                        </div>
                                        <div class="w-100 d-flex justify-content-center mt-3">
                                            <pageless-button type="submit" classname="btn btn-primary col-11 col-lg-10 col-xl-9" text="Login"></pageless-button>
                                        </div>
                                        <div class="w-100 d-flex justify-content-center mt-3">
                                            <pageless-button type="button" classname="btn btn-clean col-11 col-lg-10 col-xl-9" text="Go Back" onclick="{{this.props.ongoback}}"></pageless-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            `;
        }
    });