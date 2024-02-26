import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import "../Components.js";
export const widget = new PageLessComponent("not-found-widget", {
    data: {
        title: "Page Not Found",
    },
    props: {
        ongoback: function(){
            PageLess.GoBack();
        }
    },
    view: function(){
        return /*html*/`
            <div class="main-content" onsubmit="{{this.props.onsubmit}}" style="overflow: hidden !important;">
                <div class="row login-container no-gutters">
                    <div class="col-12 col-sm-11 col-md-7 col-lg-6 col-xl-5 login-form-container">
                        <div class="login-form align-items-center align-content-center" id="login-form">
                            <div class="flex-1 w-100 p-0 p-sm-1 p-md-3 p-xl-4 scroll-y ">
                                <div class="w-100 h-100 d-flex flex-column align-content-center justify-content-center align-items-center">
                                    <div class="row justify-content-center">
                                        <img class="w-250px" style="height: auto;" alt="waste-art" src="/media/images/voteclipart.png">
                                        <div class="w-100 text-center h5 mt-3">404! Not Found</div>
                                        <div class="col-10 col-sm-9 col-md-6 text-center mt-2">
                                            Sorry we could find what you're looking for.
                                        </div>
                                        <div class="w-100 d-flex justify-content-center mt-3">
                                            <pageless-button type="button" classname="btn btn-primary col-7 col-lg-6 col-xl-5" text="Go Back" onclick="{{this.props.ongoback}}"></pageless-button>
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