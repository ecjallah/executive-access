import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./PoliticalPartyComponents.js";
export const widget = new PageLessComponent("polling-place-assignment-widget", {
    data: {
        title: "Polling Place Assignment",
    },
    props: {
        managestaff: function(){
            (new PageLess('/staff-account-management')).route();
        },

        onload: function(){
            return new Promise(resolve => {
                this.setRequest({
                    url: `/api/view-staffs`,
                    method: "GET",
                });

                this.setChild(data=>{
                    return /*html*/ `<staff-list-item id="${data.user_id}" fullname="${data.full_name}" role="${data.user_role_title}" image="${data.image}"></staff-list-item>`;
                });

                resolve();
            });
        },
    },
    view: function(){
        return /*html*/`
            <div class="main-content">
                <main-content-header title="${this.title}"></main-content-header>
                <div class="tool-bar">
                    <pageless-button class="tool" text="Manage Staff" onclick="{{this.props.managestaff}}"></pageless-button>
                </div>
                <div class="main-content-body">
                    <vertical-scroll-view nodataicon="fa-key" preloader="list" onload="{{this.props.onload}}" scrollable="false"></vertical-scroll-view>
                </div>
            </div>
        `;
    },
});