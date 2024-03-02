import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./Components.js";
export const widget = function(){
    return new PageLessComponent("staff-assingment-manager-widget", {
        data: {
            title: "Staff Assignment",
            staffid: PageLess.GetURLData()[1]
        },
        props: {
            newassignment: function(){
                Modal.BuildForm({
                    title: "Add New Assignment",
                    icon: "user-plus",
                    description: `To add a new assignment, enter the Name, Code, or County of the Precinct you wish to assign`,
                    inputs: /*html*/ `
                        <text-input text="Precinct Code, Name, or County" icon="map-marker-alt" identity="search" required="required"></text-input>
                    `,
                    submitText: "Find",
                    closable: false,
                    autoClose: false,
                }, values=>{
                    let submitBtn = values.modal.querySelector('button[type=submit]');
                    PageLess.Request({
                        url: `/api/search-precincts`,
                        method: "POST",
                        data: {
                            search_value: values.search,
                        },
                        beforeSend: ()=>{
                            PageLess.ChangeButtonState(submitBtn, 'Adding');
                        }
                    }, true).then(result=>{
                        PageLess.RestoreButtonState(submitBtn);
                        if (result.status == 200) {
                            Modal.Close(values.modal);
                            const data    = result.response_body;
                            let precincts = '';
                            data.forEach(precinct => {
                                if (precinct.polling_center != 404) {
                                    precincts += /*html*/ `<assignable-precinct-item id="${precinct.id}" name="${precinct.title}" countyid="${precinct.county_id}" county="${precinct.county_name}" code="${precinct.code}" pollingplaces='${JSON.stringify(precinct.polling_center)}'></assignable-precinct-item>`
                                }   
                            });
                            
                            Modal.BuildForm({
                                title: "Assign Polling Places",
                                icon: "map-marker-alt",
                                description: `Check the Polling Places you with to assign`,
                                inputs: /*html*/ `
                                    ${precincts}
                                `,
                                submitText: "Assign",
                                closable: false,
                                autoClose: false,
                            }, assignmentValues=>{
                                const assignedBoxes = assignmentValues.modal.querySelectorAll('.assigned-polling-place:checked');
                                if (assignedBoxes.length > 0) {
                                    Modal.Confirmation('Confirm Action', "Are you sure you want to continue?").then(()=>{
                                        let assignedCenters = [];
                                        assignedBoxes.forEach(box=>{
                                            assignedCenters.push({center_id: box.value, precinct_id: box.closest('.polling-place').precinctid});
                                        });
    
                                        PageLess.Request({
                                            url: `/api/assign-watcher-to-center`,
                                            method: "POST",
                                            data: {
                                                user_id: this.parentComponent.staffid,
                                                center_ids: assignedCenters
                                            },
                                            beforeSend: ()=>{
                                                PageLess.ChangeButtonState(assignmentValues.submitBtn);
                                            }
                                        }, true).then(result=>{
                                            PageLess.RestoreButtonState(assignmentValues.submitBtn);
                                            if (result.status == 200) {
                                                this.parentComponent.querySelector('.scroll-view').update();
                                                PageLess.Toast('success', result.message);
                                                Modal.Close(assignmentValues.modal);
                                            } else{
                                                PageLess.Toast('danger', result.message, 5000);
                                            }
                                        });
                                    });
                                    
                                } else {
                                    PageLess.Toast('danger', "Please select the modules you wish to assign before you proceeding", 5000);
                                }
                            });
                        } else {
                            PageLess.Toast('danger', result.message);
                        }
                    });
                });
            },

            onload: function(){
                return new Promise(resolve => {
                    this.setRequest({
                        url: `/api/get-watcher-precincts-and-centers/${this.staffid}`,
                        method: "GET"
                    });

                    this.setChild(data=>{
                        return /*html*/ `<assignable-precinct-item id="${data.precinct_info.id}" precinctid="${data.precinct_info.precinct_id}" code="${data.precinct_info.code}" name="${data.precinct_info.title}" pollingplaces='${JSON.stringify(data.assigned_centers)}' county="${data.precinct_info.county_name}" mode="unassignment" staffid="${this.staffid}"></assignable-precinct-item>`;
                    });

                    // this.onCompleted(data=>{
                    //     if (data.status == 200) {
                    //         const parent = this.parentComponent;
                    //         const header = parent.querySelector('.main-content-header');
                    //         header.setData({
                    //             title: `${data.response_body.title} Polling Places`
                    //         });
                    //     }
                    // });

                    resolve();
                });
            },
        },
        view: function(){
            return /*html*/`
                <div class="main-content">
                    <main-content-header title="${this.title}"></main-content-header>
                    <div class="tool-bar">
                        <pageless-button class="tool" text="New Assignment" onclick="{{this.props.newassignment}}"></pageless-button>
                    </div>
                    <div class="main-content-body">
                        <vertical-scroll-view staffid="${this.staffid}" nodataicon="fa-key" preloader="list" onload="{{this.props.onload}}" scrollable="false"></vertical-scroll-view>
                    </div>
                </div>
            `;
        },
    });
}