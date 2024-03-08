/*!
 * Purpose: contains all the widgets object for all modules. 
 * Version Release: 1.0
 * Created Date: March 22, 2023
 * Author(s):Enoch C. Jallah
 * Contact Mail: enochcjallah@gmail.com
*/
// "use esversion: 11";
import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from "../../PageLess/PageLess.min.js";
import '../Components.js'
import './Components.js'
export class Widget extends PageLess{

    constructor(url = null) {
        super(url);
        this.mainContentContainer = document.querySelector('.main-content-container');
        this.toastContainer       = this.mainContentContainer;
        this.appName              = 'Exective Access';
        this.API                  = '/api';
        this.modulesLocation      = "/js/modules/admin";
        this.routes.push(
            {
                widget: "Home",
                routePattern: /^\/$/
            },
            {
                widget: "Home",
                routePattern: /^\/dashboard\/?$/
            },
            {
                widget: "UserTypeManager",
                routePattern: /^\/user-type-management\/?$/
            },
            {
                widget: "StaffManager",
                routePattern: /^\/staff-account-management\/?$/
            },
            {
                widget: "StaffRolesManager",
                routePattern: /^\/staff-account-management\/roles\/?$/
            },
            {
                widget: "DepartmentManager",
                routePattern: /^\/departments-manager\/?$/
            },
            {
                widget: "ExecutivesManager",
                routePattern: /^\/executives-manager\/?$/
            },
            {
                widget: "AppointmentManager",
                routePattern: /^\/appointments\/?$/
            },
        );
    }

    goToStart(){
        (new PageLess()).route('/', false);
    }

    static BuildSidebar(){
        let container = document.querySelector('.main');
        container.prepend(PageLessComponent.Render(/*html*/`<side-bar></side-bar>`));
    }
}