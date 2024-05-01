import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import "../Components.js";
export const widget = new PageLessComponent("advice-complete-widget", {
        data: {
            title: 'End of Advisory',
        },
        props: {
            onclose: function(){
                window.location.href = '/';
            }
        },
        view: function(){
            return /*html*/`
                <div class="main-content" onsubmit="{{this.props.onsubmit}}" style="overflow: hidden !important;">
                    <div class="row login-container no-gutters">
                        <div class="col-12 col-sm-11 col-md-7 col-lg-6 col-xl-5 login-form-container">
                            <div class="login-form" id="login-form">
                                <div class="flex-1 w-100 p-0 p-sm-1 p-md-3 p-xl-4 scroll-y">
                                    <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center align-content-center">
                                        <img class="w-100px mt-4" style="height: auto;" alt="liberian-seal" src="/media/images/seal.png">
                                        <div class="w-100 d-flex justify-content-center">
                                            <div class="check-animation">
                                                <svg width="150" height="150"   id="circle-check" class="svg">
                                                    <path id="draw-circle" class="strokes one" stroke-width="4" fill="transparent" d="M 75, 75  m -50, 0 a 50, 50 0 1, 0 100, 0 a 50, 50 0 1, 0 -100, 0
                                                    "/>
                                                    <path id="draw-check" class="strokes two" d="M 50, 80 L 65, 95 L 95, 60"  stroke-width="4" fill="transparent"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="description text-left pt-3">
                                            You're appointment request has been saved successfully.<br>
                                            You'll be notified via SMS once it has been processed.
                                        </div>
                                        <div class="description text-left py-3 font-weight-bold">
                                            Thank You!
                                        </div>
                                    </div>
                                </div>
                                <div class="w-100 d-flex justify-content-around mt-2 mb-3">
                                    <pageless-button type="button" classname="btn btn-light col-5 col-lg-4 col-xl-3" text="Close | Book Again" onclick="{{this.props.onclose}}"></pageless-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    });