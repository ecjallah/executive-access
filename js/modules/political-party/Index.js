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
import './PoliticalPartyComponents.js'
export class Widget extends PageLess{

    constructor(url = null) {
        super(url);
        this.mainContentContainer = document.querySelector('.main-content-container');
        this.toastContainer       = this.mainContentContainer;
        this.appName              = 'RERM';
        this.API                  = '/api';
        this.modulesLocation      = "/js/modules/political-party";
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
                widget: "IssuesManager",
                routePattern: /^\/issues-manager\/?$/
            },
            {
                widget: "ElectionsManager",
                routePattern: /^\/elections-manager\/?$/
            },
            {
                widget: "CandidateManager",
                routePattern: /^\/candidate-manager\/?$/
            },
            {
                widget: "CountiesManager",
                routePattern: /^\/counties\/?$/
            },
            {
                widget: "DistrictsManager",
                routePattern: /^\/counties\/([0-9]+)\/districts\/?$/
            },
            {
                widget: "PoliticalPriorities",
                routePattern: /^\/political-priorities-manager\/?$/
            },
            {
                widget: "PendingPoliticalParties",
                routePattern: /^\/political-parties\/?$/
            },
            {
                widget: "ApprovedPoliticalParties",
                routePattern: /^\/political-parties\/approved\/?$/
            },
            {
                widget: "PrecinctManager",
                routePattern: /^\/precincts\/?$/
            },
            {
                widget: "PollingPlaceManager",
                routePattern: /^\/precincts\/([0-9]+)\/polling-places\/?$/
            },
            {
                widget: "PollingPlaceAssignmentManager",
                routePattern: /^\/polling-places\/assignment\/?$/
            },
            {
                widget: "StaffAssignmentManager",
                routePattern: /^\/polling-places\/([0-9]+)\/assignment\/?$/
            },
            {
                widget: "ResultCenter",
                routePattern: /^\/result-collection\/?$/
            },
            {
                widget: "ResultSubmission",
                routePattern: /^\/result-collection\/([0-9]+)\/polling-places\/([0-9]+)\/?$/
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