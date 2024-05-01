/*!
 * Purpose: contains all the widgets object for all modules. 
 * Version Release: 1.0
 * Created Date: March 22, 2023
 * Author(s):Enoch C. Jallah
 * Contact Mail: enochcjallah@gmail.com
*/
// "use esversion: 11";
import { PageLess } from '/js/PageLess/PageLess.min.js';

export class Widget extends PageLess{

    constructor(url = null) {
        super(url);
        this.mainContentContainer = document.querySelector('.main-content-container');
        this.toastContainer       = this.mainContentContainer;
        this.appName              = 'Exective Access';
        this.API                  = '/api';
        this.modulesLocation      = "/js/modules/public";
        this.routes.push(
            {
                widget: "Home",
                routePattern: /^\/$/
            },
            {
                widget: "GetStarted",
                routePattern: /^\/get-started\/?$/
            },
            {
                widget: "Login",
                routePattern: /^\/login\/?$/
            },
            {
                widget: "BookingCompleted",
                routePattern: /^\/completed\/?$/
            }
        );
    }

    goToStart(){
        (new PageLess()).route('/', false);
    }
}