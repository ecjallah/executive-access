import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./PoliticalPartyComponents.js";
export const widget = new PageLessComponent("pending-political-parties-widget", {
    data: {
        title: "Political Parties Requests",
    },
    props: {
        addnew: function(){
            (new PageLess('/political-parties/approved')).route();
        },

        onload: function(){
            return new Promise(resolve => {
                this.setRequest({
                    url: `/api/view-all-pending-parties`,
                    method: "GET",
                });

                this.setChild(data=>{
                    return /*html*/ `<political-party id="${data.user_id}" name="${data.full_name}" status="pending"></political-party>`;
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
                    <pageless-button class="tool" text="Approved Parties" onclick="{{this.props.addnew}}"></pageless-button>
                </div>
                <div class="main-content-body">
                    <vertical-scroll-view nodataicon="fa-key" preloader="list" onload="{{this.props.onload}}" scrollable="false"></vertical-scroll-view>
                </div>
            </div>
        `;
    },
});