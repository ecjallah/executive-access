/*!
 * Purpose: contains all the widgets object for all modules. 
 * Version Release: 1.0
 * Created Date: March 22, 2023
 * Author(s):Enoch C. Jallah
 * Contact Mail: enochcjallah@gmail.com
*/
// "use esversion: 11";
// import {PageLess} from './PageLess/PageLess.min.js';
// PageLess.Request({
//     url: `/api/user-sidebar-items`,
//     method: "GET",
// }).then(result=>{

    
//     if (result.status == 200 || result.status == 404) {
//         import('./modules/political-party/Index.js').then(module=>{
//             const Widget = module.Widget;
//             Widget.BuildSidebar();
//             let path = window.location.pathname;
//             (new Widget(path)).route();
//         })
//     } else {
        import('./modules/public/Index.js').then(module=>{
            const Widget = module.Widget;
            // if (result.status == 309) {
                (new Widget('/')).route();
            // }else {
            //     let path = window.location.pathname;
            //     (new Widget(path)).route();
            // }
        });
//     }
// });