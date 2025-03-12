import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./Components.js";
export const widget = function(){
    return new PageLessComponent("executive-management-widget", {
        data: {
            title: "Department Executive",
            departmentid: PageLess.GetURLData()[1],
            staff: /*html*/ `
                <list-view-preloader></list-view-preloader>
                <list-view-preloader></list-view-preloader>
                <list-view-preloader></list-view-preloader>
                <list-view-preloader></list-view-preloader>
                <list-view-preloader></list-view-preloader>
                <list-view-preloader></list-view-preloader>
            `
        },
        props: {
            onload: function(){
                return new Promise(resolve => {
                    this.setRequest({
                        url: `/api/get-department-executives/${this.parentComponent.departmentid}`,
                        method: "GET"
                    });

                    this.setChild(data=>{
                        return /*html*/ `
                            <department-executive-item id="${data.id}" fullname="${data.full_name}" firstname="${data.first_name}" middlename="${data.middle_name}" lastname="${data.last_name}" departmentid="${data.department_id}" department="${this.parentComponent.departmentid}" number="${data.number}"></department-executive-item>`;
                    });

                    resolve();
                });
            }
        },
        view: function(){
            return /*html*/`
                <div class="main-content">
                    <main-content-header title="${this.title}"></main-content-header>
                    <div class="main-content-body">
                        ${this.staff}
                    </div>
                </div>
            `;
        },
        callback: function(){
            PageLess.Request({
                url: `/api/get-department-details/${this.departmentid}`,
                method: "GET",
            }).then(result=>{
                if (result.status == 200) {
                    this.setData({
                        staff: /*html*/ `<vertical-scroll-view nodataicon="fa-user-tie" preloader="list" onload="{{this.props.onload}}"></vertical-scroll-view>`,
                        title: result.response_body.title + " Executives"
                    })
                } else {
                    PageLess.Toast('danger', "Invalid department ID provided", 5000)
                    PageLess.GoBack();
                }
            });
        }
    });
}