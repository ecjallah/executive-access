import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import "../Components.js";
export const widget = new PageLessComponent("home-widget", {
        data: {
            title: 'Home',
        },
        props: {
            ongetstarted: function(){
                (new PageLess('/get-started')).route();
            },

            onlogin: function(){
               (new PageLess('/login')).route();
            }
        },
        view: function(){
            return /*html*/`
                <div class="main-content" onsubmit="{{this.props.onsubmit}}" style="overflow: hidden !important;">
                    <div class="row login-container no-gutters">
                        <div class="col-12 col-sm-11 col-md-7 col-lg-6 col-xl-5 login-form-container">
                            <div class="login-form align-items-center align-content-center" id="login-form">
                                <div class="flex-1 w-100 p-0 p-sm-1 p-md-3 p-xl-4 scroll-y ">
                                    <div class="w-100 h-100 d-flex flex-column align-content-center justify-content-start align-items-center">
                                        <div class="row justify-content-center">
                                            <img class="w-100px mt-4" style="height: auto;" alt="liberian-seal" src="/media/images/seal.png">
                                            <div class="w-100 text-center h5 mt-3">Welcome To a New Way of Governance</div>
                                            <div class="col-10 col-sm-9 col-md-6 text-center mt-2">
                                                We are pleased to introduce you to a smarter way to connect with your government!
                                                Skip the lines and save time! This smart appointment system lets you book government meetings 
                                                online 24/7, reduces wait times, and keeps you informed with confirmations and reminders. Let's get started!
                                            </div>
                                            <div class="w-100 d-flex justify-content-center mt-5">
                                                <pageless-button type="button" classname="btn btn-primary col-5 col-lg-4 col-xl-3" text="Get Started" onclick="{{this.props.ongetstarted}}"></pageless-button>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    });