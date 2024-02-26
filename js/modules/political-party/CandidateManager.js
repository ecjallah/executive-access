import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./PoliticalPartyComponents.js";
export const widget = new PageLessComponent("candidate-manager-widget", {
    data: {
        title: "Candidate Manager",
    },
    props: {
        addnew: function(){
            Modal.BuildForm({
                title: "Add New Candidate",
                icon: "user-tie",
                description: `Please enter the details below`,
                inputs: /*html*/ `
                    <text-input icon="user" text="First Name" identity="firstname" required="required"></text-input>
                    <text-input icon="user" text="Middle Name" identity="middlename"></text-input>
                    <text-input icon="user" text="Last Name" identity="lastname" required="required"></text-input>
                    <position-select></position-select>
                    <election-select></election-select>
                    <county-select required="required"></county-select>
                `,
                submitText: "Add",
                closable: false,
                autoClose: false,
            }, values=>{
                let submitBtn = values.modal.querySelector('button[type=submit]');
                PageLess.Request({
                    url: `/api/add-candidate`,
                    method: "POST",
                    data: {
                        first_name: values.firstname,
                        middle_name: values.middlename,
                        last_name: values.lastname,
                        position: values.position,
                        election_type_id: values.election,
                        county: values.county,
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
                    url: `/api/view-candidate-list`,
                    method: "GET",
                });

                this.setChild(data=>{
                    return /*html*/ `<political-candidate id="${data.id}" fullname="${data.full_name}" firstname="${data.first_name}" middlename="${data.middle_name}" lastname="${data.last_name}" position="${data.position}" electionid="${data.election_type_id}" county="${data.county}"></political-candidate>`;
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
                    <vertical-scroll-view nodataicon="fa-key" preloader="list" onload="{{this.props.onload}}" scrollable="false"></vertical-scroll-view>
                </div>
            </div>
        `;
    },
});