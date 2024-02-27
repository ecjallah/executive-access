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
            <div class="main-content">
                <!-- main content header -->
                <main-content-header title="${this.title}"></main-content-header>
                <div class="main-content-body ">
                    <div class="w-100 d-flex m-0 p-0 h-100 flex-column align-items-center justify-content-center error-page-parent">
                        <div class="w-100 d-flex flex-wrap justify-content-center">
                            <img class="col-10 col-sm-9 col-md-6" style="height: auto;" alt="waste-art" src="/media/images/voteclipart.png">
                            <div class="w-100 text-center h6 mt-3">404! Not Found</div>
                            <div class="col-10 col-sm-9 col-md-6 text-center mt-2">
                                Sorry we could find what you're looking for.
                            </div>
                            <div class="w-100 d-flex justify-content-center mt-3">
                                <pageless-button type="button" classname="btn btn-primary px-4 col-5 col-md-4 col-xl-3" text="Go Back" onclick="{{this.props.ongoback}}"></pageless-button>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        `;
    }
});