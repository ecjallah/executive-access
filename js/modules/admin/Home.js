import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
export const widget = new PageLessComponent("political-party-home-widget", {
        data: {
            title: 'Home',
        },
        props: {
            ongetstarted: function(){
                (new PageLess('/staff-account-management')).route();
            }
        },
        view: function(){
            return /*html*/`
                <div class="main-content">
                    <main-content-header title="${this.title}" startpage="true"></main-content-header>
                    <div class="main-content-body">
                        <div class="flex-1 w-100 p-2 p-md-3 p-xl-4 scroll-y d-flex">
                            <div class="w-100 p-1 d-flex flex-wrap list-items-container dashboard-cards-container">
                                <general-dashboard-stats></general-dashboard-stats>
                                <appointment-dashboard-stats></appointment-dashboard-stats>
                                <div class="welcome-container p-1 p-sm-1 p-md-2 p-lg-2 p-xl-3 align-items-center">
                                    <div class="col-12 col-sm-8 col-md-6 order-2 order-sm-1">
                                        <div class="welcome-note p-0 p-sm-1 p-md-3 p-lg-4">
                                            <h3>Hi there!</h3>
                                            <span class="text-muted">
                                                Welcome to Executive Access Management Portal <br> 
                                                We understand your needs and priorities for a centralize appointment tracking system.  
                                                And That's why we're here. <br><br> 
                                                Let's get started by setting your staff members for each departments and their roles
                                            </span><br>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-md-6 order-1 order-sm-2 d-flex align-items-center">
                                        <img class="instructional-image" src="/media/images/appointment-clipart.png">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    });