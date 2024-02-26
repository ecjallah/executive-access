import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./PoliticalPartyComponents.js";
export const widget = function(){
    return new PageLessComponent("polling-place-manager-widget", {
        data: {
            title: "Polling Places",
            precinctid: PageLess.GetURLData()[1]
        },
        props: {
            addnew: function(){
                Modal.BuildForm({
                    title: "Add New Polling Place",
                    icon: "map-marker-plus",
                    description: `Please enter the details below`,
                    inputs: /*html*/ `
                        <text-input text="Polling Place Name" icon="map-marker-alt" identity="name" required="required"></text-input>
                        <text-input text="Polling Place Code" icon="hashtag" identity="code" required="required"></text-input>
                    `,
                    submitText: "Add",
                    closable: false,
                    autoClose: false,
                }, values=>{
                    let submitBtn = values.modal.querySelector('button[type=submit]');
                    PageLess.Request({
                        url: `/api/add-new-polling-center`,
                        method: "POST",
                        data: {
                            title: values.name,
                            code: values.code,
                            precinct_id: this.parentComponent.precinctid,
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
                        url: `/api/get-precinct-details/${this.precinctid}`,
                        method: "GET",
                    });

                    this.mapData(data =>{
                        return data.polling_centers != 404 ? data.polling_centers : [];
                    });

                    this.setChild(data=>{
                        return /*html*/ `<polling-place-item id="${data.id}" precinctid="${data.precinct_id}" code="${data.code}" name="${data.title}"></polling-place-item>`;
                    });

                    this.onCompleted(data=>{
                        if (data.status == 200) {
                            const parent = this.parentComponent;
                            const header = parent.querySelector('.main-content-header');
                            header.setData({
                                title: `${data.response_body.title} Polling Places`
                            });
                        }
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
                        <vertical-scroll-view precinctid="${this.precinctid}" nodataicon="fa-key" preloader="list" onload="{{this.props.onload}}" scrollable="false"></vertical-scroll-view>
                    </div>
                </div>
            `;
        },
    });
}