import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./PoliticalPartyComponents.js";
export const widget = function(){
    return new PageLessComponent("political-party-manager-widget", {
        data: {
            title: "Political Parties Requests",
        },
        props: {

            onload: function(){
                return new Promise(resolve => {
                    this.setRequest({
                        url: `/api/view-all-approved-parties`,
                        method: "GET",
                    });

                    this.setChild(data=>{
                        return /*html*/ `<political-party id="${data.user_id}" name="${data.full_name}" status="approved" blocked="${data.blocked}"></political-party>`;
                    });

                    resolve();
                });
            },
        },
        view: function(){
            return /*html*/`
                <div class="main-content">
                    <main-content-header title="${this.title}"></main-content-header>
                    <div class="main-content-body">
                        <vertical-scroll-view nodataicon="fa-key" preloader="list" onload="{{this.props.onload}}" scrollable="false"></vertical-scroll-view>
                    </div>
                </div>
            `;
        },
    });
}