import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./Components.js";
export const widget = new PageLessComponent("staff-management-widget", {
    data: {
        title: "Staff Management"
    },
    props: {
        adduser: function(){
            Modal.BuildForm({
                title: "Add New User",
                icon: "user-plus",
                description: `Please enter the user details below`,
                inputs: /*html*/ `
                    <!--<file-input text="Add Image" icon="user-shield" identity="test" classname="w-100 h-200px mb-2" required="required"></file-input>-->
                    <text-input text="First Name" icon="user" identity="firstname" required="required"></text-input>
                    <text-input text="Last Name" icon="user" identity="lastname" required="required"></text-input>
                    <text-input text="Username" icon="signature" identity="username" required="required"></text-input>
                    <text-input text="Address" icon="map-marked" identity="address" required="required"></text-input>
                    <gender-select></gender-select>
                    <number-input text="Phone Number" icon="phone-alt" identity="phone-no" required="required"></number-input>
                    <email-input required="required"></email-input>
                    <staff-role-select></staff-role-select>
                `,
                submitText: "Add Staff",
                closable: false,
                autoClose: false,
            }, values=>{
                PageLess.Request({
                    url: `/api/create-new-staff`,
                    method: "POST",
                    data: {
                        role_id : values.role,
                        first_name : values.firstname,
                        last_name : values.lastname,
                        address : values.address,
                        sex : values.gender,
                        email : values.email,
                        county : 'N/A',
                        number : values['phone-no'],
                        username : values.username,
                    },
                    beforeSend: ()=>{
                        PageLess.ChangeButtonState(values.submitBtn, 'Adding');
                    }
                }, true).then(result=>{
                    PageLess.RestoreButtonState(values.submitBtn);
                    if (result.status == 200) {
                        Modal.Success('Completed Successfully', result.message);
                        this.parentComponent.querySelector('.scroll-view').update();
                        Modal.Close(values.modal);
                    } else {
                        PageLess.Toast('danger', result.message, 5000);
                    }
                });
            });
        },
        onload: function(){
            return new Promise(resolve => {
                this.setRequest({
                    url: `/api/view-staffs`,
                    method: "GET",
                });

                this.setChild(data=>{
                    return /*html*/ `
                        <user-card 
                            firstname=""
                            lastname=""
                            address=""
                            gender=""
                            email=""
                            county=""
                            userid="${data.user_id}"
                            phoneno="${data.number}" 
                            roleid="${data.user_role_id}" 
                            fullname="${data.full_name}" 
                            username="${data.username}" 
                            image="${data.image}" 
                            role="${data.user_role_title}"
                        ></user-card>`;
                });

                resolve();
            });
        }
    },
    view: function(){
        return /*html*/`
            <div class="main-content">
                <main-content-header title="${this.title}"></main-content-header>
                <div class="tool-bar">
                    <pageless-button classname="tool" text="Add User" onclick="{{this.props.adduser}}"></pageless-button>
                    <pageless-link classname="tool" text="Role Manager" href="/staff-account-management/roles"></pageless-link>
                </div>
                <div class="main-content-body">
                    <vertical-scroll-view nodataicon="fa-users" preloader="card" onload="{{this.props.onload}}" scrollable="false"></vertical-scroll-view>
                </div>
            </div>
        `;
    },
});