import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./PoliticalPartyComponents.js";
export const widget = function(){
    return new PageLessComponent("districts-manager-widget", {
        data: {
            title: "Districts",
            countyid: PageLess.GetURLData()[1]
        },
        props: {
            addnew: function(){
                Modal.BuildForm({
                    title: "Add New Ditrict",
                    icon: "map-marker-plus",
                    description: `Please enter the details below`,
                    inputs: /*html*/ `
                        <text-input text="District Name" icon="map-marker-alt" identity="name" required="required"></text-input>
                    `,
                    submitText: "Add",
                    closable: false,
                    autoClose: false,
                }, values=>{
                    let submitBtn = values.modal.querySelector('button[type=submit]');
                    PageLess.Request({
                        url: `/api/add-new-county-district`,
                        method: "POST",
                        data: {
                            county_id: this.countyid,
                            title: values.name,
                        },
                        beforeSend: ()=>{
                            PageLess.ChangeButtonState(submitBtn, 'Adding');
                        }
                    }, true).then(result=>{
                        PageLess.RestoreButtonState(submitBtn);
                        if (result.status == 200) {
                            this.parentComponent.querySelector('.scroll-view').update();
                            PageLess.Toast('success', result.message);
                            Modal.Close(values.modal);
                        } else {
                            PageLess.Toast('danger', result.message);
                        }
                    });
                });
            },

            onload: function(){
                return new Promise(resolve => {
                    this.setRequest({
                        url: `/api/view-district-by-id/${this.countyid}`,
                        method: "GET",
                    });

                    this.setChild(data=>{
                        return /*html*/ `<district-item id="${data.id}" countyid="${this.countyid}" name="${data.district_title}"></district-item>`;
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
                        <pageless-button class="tool" text="Add New" onclick="{{this.props.addnew}}"></pageless-button>
                    </div>
                    <div class="main-content-body">
                        <vertical-scroll-view countyid="${this.countyid}" nodataicon="fa-key" preloader="list" onload="{{this.props.onload}}" scrollable="false"></vertical-scroll-view>
                    </div>
                </div>
            `;
        },
    });
}