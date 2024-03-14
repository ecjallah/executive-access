/*!
 * Purpose: contains shared components for all modules. 
 * Version Release: 1.0
 * Created Date: March 22, 2023
 * Author(s):Enoch C. Jallah
 * Contact Mail: enochcjallah@gmail.com
*/
// "use esversion: 11";
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { PageLess } from '../../PageLess/PageLess.min.js';
import { Modal } from "./Modals.js";
import "../Components.js";

export const Components = {

    userCard : new PageLessComponent("user-card", {
        data: {
            userid: "",
            firstname: '',
            lastname: '',
            address: '',
            gender: '',
            email: '',
            county: '',
            image: "",
            fullname: "",
            username: "",
            phoneno: "",
            role: "",
            roleid: "",
            defaultImage: "/media/images/user-image-placeholder.png"
        },
        props: {
            useroptionclick: function(event){
                event.stopPropagation();
                let parent = this.parentComponent;
                PageLess.ContextMenu([
                    {
                        text: 'View Details', 
                        callback: ()=>{
                            Modal.BuildForm({
                                title: `${parent.fullname} Details`,
                                icon: "user",
                                inputs: /*html*/ `
                                    <text-input value="${parent.firstname}" text="First Name" icon="user" identity="firstname" required="required"></text-input>
                                    <text-input value="${parent.lastname}" text="Last Name" icon="user" identity="lastname" required="required"></text-input>
                                    <text-input value="${parent.adress}" text="Address" icon="map-marked" identity="address" required="required"></text-input>
                                    <gender-select selectedvalue="${parent.gender}"></gender-select>
                                    <email-input value="${parent.email}" required="required"></email-input>
                                    <staff-role-select selectedvalue="${parent.rollid}"></staff-role-select>
                                `,
                                submitText: "Update",
                                closable: false,
                                autoClose: false,
                            }, values=>{
                                let submitBtn = values.modal.querySelector('button[type=submit]');
                                let newTitle  = values.modal.querySelector('.role');
                                newTitle      = newTitle.options[newTitle.selectedIndex].text;
                                PageLess.Request({
                                    url: `/api/edit-staff-details`,
                                    method: "POST",
                                    data: {
                                        user_id: parent.userid,
                                        first_name: values.firstname,
                                        last_name: values.lastname,
                                        role_id: values.role,
                                        address : values.address,
                                        sex: values.gender,
                                        email: values.email
                                    },
                                    beforeSend: ()=>{
                                        PageLess.ChangeButtonState(submitBtn, 'Updating');
                                    }
                                }, true).then(result=>{
                                    PageLess.RestoreButtonState(submitBtn);
                                    if (result.status == 200) {
                                        parent.setData({
                                            fullname: `${values.fullname} `,
                                            roleid: values.role,
                                            role: newTitle,
                                            number: values['phone-no'],
                                            username: values.username,
                                        });
                                        PageLess.Toast('success', result.message);
                                        Modal.Close(values.modal);
                                    } else {
                                        PageLess.Toast('danger', result.message);
                                    }
                                });
                            });
                        }
                    },
                    {
                        text: 'Block', 
                        callback: ()=>{
                            
                        }
                    },
                    {
                        text: 'Delete', 
                        callback: ()=>{
                            Modal.Confirmation("Confirm Deletion", "This cannot be undone! Are you sure you want to continue?").then(()=>{
                                PageLess.Request({
                                    url: `/api/delete-user-account`,
                                    method: "POST",
                                    data: {
                                        user_id: parent.userid,
                                    },
                                    beforeSend: ()=>{
                                        PageLess.ChangeButtonState(this, '');
                                    }
                                }).then(result=>{
                                    PageLess.RestoreButtonState(this);
                                    if (result.status == 200) {
                                        PageLess.Toast('success', result.message);
                                        parent.remove();
                                    } else {
                                        PageLess.Toast('danger', result.message);
                                    }
                                });
                            });
                        }
                    }
                ], this);
            },
        },
        view: function(){
            return /*html*/`
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <div class="row p-1 p-sm-1 p-md-2 p-lg-2 p-xl-3 justify-content-center">
                        <div class="user-card position-relative">
                            <pageless-button classname="init-context-menu" text="<i class='fad fa-lg fa-ellipsis-v-alt'></i>" onclick="{{this.props.useroptionclick}}"></pageless-button>
                            <div class="user-image-container">
                                <div class="user-image" style="background: url('${this.image != "" && this.image !== 'null' && this.image !== null ? this.image : this.defaultImage}')"></div>
                            </div>
                            <div class="user-fullname">${this.fullname}</div>
                            <div class="user-name">${this.username}</div>
                            <div class="user-right">${this.role}</div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    dashboardCard : new PageLessComponent("dashboard-card", {
        data: {
            title: '',
            value: '',
            icon: '',
            iconcolor: 'text-mw-primary',
            classname: "",
        },
        props: {
            key: function(){},
        },
        view: function(){
            return /*html*/`
                <div class="col-6 col-sm-6 col-md-4 col-lg-3">
                    <div class="row p-1 p-sm-1 p-md-2 p-lg-2 p-xl-3">
                        <div class="col-12 dashboard-card ${this.classname}">
                            <div class="row justify-content-center justify-content-sm-between h-100 align-items-center p-3">
                                <div class="col-12 col-sm-auto text-center p-0">
                                    <div class="fa-stack fa-lg">
                                        <i class="fa fa-circle text-light fa-stack-2x"></i>
                                        <i class="fad ${this.iconcolor} fa-${this.icon} fa-stack-1x"></i>
                                    </div>
                                </div>

                                <div class="stats-container">
                                    <div class="row justify-content-center text-dark pb-2 order-2 order-sm-1">
                                        <h4>${this.value}</h4>
                                    </div>
                                    <div class="row justify-content-center pt-2 pl-2 pr-2 pb-0 text-dark order-1 order-sm-2">
                                        <span>${this.title}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        },
        callback: function(){
            
        }
    }),

    customChart : new PageLessComponent("custom-chart", {
        data: {
            title: "My Title",
            classname: 'h-225px',
            type: '',
            chartdata: {},
            defaultOptions: {
                plugins: false,
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: true, 
                    position: 'bottom',
                    labels: {
                        boxWidth: 0,
                        fontSize: 14
                    }
                },
                scales: {
                    yAxes: [{
                        display: false,
                        ticks: {
                            beginAtZero: true,
                            stepSize: 25
                        }
                    }],
                    xAxes: [{display: false, ticks: {fontSize: 12}, gridLines: {display: false}}],
                },
                tooltips: {
                    position: 'nearest',
                    callbacks: {
                        title: function(tooltipItem, data){
                            let dataset = data.datasets[tooltipItem[0].datasetIndex];
                            let labels  = dataset.labels != undefined ? dataset.labels : data.labels;
                            let index   = tooltipItem[0].index;
                            return (dataset.pdata != undefined) ? labels[index] +` (${dataset.pdata[index]})` : labels[index];
                        },
                        label: function(tooltipItem, data) {
                            return data.datasets[tooltipItem.datasetIndex].fdata[tooltipItem.index];
                        }
                    }
                },
                layout: {padding: 10},
            }
        },
        props: {
            setoption: function(sentOpt = this.chartdata.options, defaultOpts = this.defaultOptions){
                if (sentOpt !== undefined && typeof sentOpt == 'object') {
                    for (const option in sentOpt) {
                        if (Object.hasOwnProperty.call(sentOpt, option)) {
                            const opt = sentOpt[option];
                            if (typeof opt == 'object') {
                                this.props.setoption.call(this, opt, defaultOpts[option]);
                            }else{
                                defaultOpts[option] = opt;
                            }
                        }
                    }
                }
            },
        },
        view: function(){
            return /*html*/`
                <div class="row m-0 p-2 justify-content-center align-items-end">
                    <div class="w-100 ${this.classname} d-flex align-items-end">
                        <canvas class="w-100" aria-label="data-chart" role="img"></canvas>
                    </div>
                </div>
            `;
        },
        callback: function(){
            this.ready(()=>{
                if (this.chartdata.length != 0) {
                    this.props.setoption.call(this);
                    (new Chart(this.querySelector('canvas'), {
                        type: this.type,
                        data: this.chartdata.data,
                        options: this.defaultOptions
                    }));
                } else {
                    
                }
            });
        }
    }),

    statsChart : new PageLessComponent("stats-chart", {
        data: {
            title: "",
            type: '',
            userid: '',
            interval: 'daily',
            mode: 'amount',
            totallocal: '',
            localcount: '',
            localcounttext: '',
            totalusd: '',
            usdcount: '',
            usdcounttext: '',
            intervaltext: '',
            chartdata: {}
        },
        props:{
            oncontextclick: function(){
                let parent = this.parentComponent;
                VendorPageLess.ContextMenu([
                    {
                        text: 'Daily',
                        callback: ()=>{
                            parent.interval = 'daily';
                            parent.props.updatechart.call(parent, parent.datagroup);
                        }
                    },
                    {
                        text: 'Weekly',
                        callback: ()=>{
                            parent.interval = 'weekly';
                            parent.props.updatechart.call(parent, parent.datagroup);
                        }
                    },
                    {
                        text: 'Monthly',
                        callback: ()=>{
                            parent.interval = 'monthly';
                            parent.props.updatechart.call(parent, parent.datagroup);
                        }
                    },
                    {
                        text: 'Yearly',
                        callback: ()=>{
                            parent.interval = 'yearly';
                            parent.props.updatechart.call(parent, parent.datagroup);
                        }
                    }
                ], this);
            },
            generateChartData: function(transactions){
                let data      = transactions[this.interval];
                let chartData = {
                    data: {
                        labels: [],
                        datasets: [
                            {
                                label: 'LRD',
                                data: [],
                                fdata: [],
                                borderWidth: 2,
                                borderColor: 'rgba(255, 0, 0, 0.3)',
                                backgroundColor: 'rgba(255, 0, 0, 0.3)',
                                fill: true,
                            },
                            {
                                label: 'USD',
                                data: [],
                                fdata: [],
                                borderWidth: 2,
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                fill: true,
                            }
                        ]
                    },

                    options:{
                        legend: {
                            display: true, 
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                fontSize: 8
                            }
                        },
                        scales: {
                            xAxes: [{display: true, ticks:{fontSize: 10}}],
                            yAxes: [{display: false, ticks:{fontSize: 10}}],
                        },
                    }
                };
                for (const key in data) {
                    if (Object.hasOwnProperty.call(data, key)) {
                        const set = data[key];
                        chartData.data.labels.push(set.name);
                        chartData.data.datasets[0].data.push(set.local_amount);
                        chartData.data.datasets[0].fdata.push(`${set.formatted_local_amount} (${set.local})`);
                        chartData.data.datasets[1].data.push(set.usd_amount);
                        chartData.data.datasets[1].fdata.push(`${set.formatted_usd_amount} (${set.usd})`);
                    }
                }
                return chartData;
            },
            // updatedata: function(){
            //     if (year != '' && type != '') {
            //         VendorPageLess.Request({
            //             url: `${Vendor'/api'}/transactions/stats`,
            //             method: "GET",
            //             data: {
            //                 'user-id': '',
            //                 'interval': this.parentComponent.interval,
            //                 'mode': this.parentComponent.mode,
            //                 'type': this.parentComponent.type,
            //                 'combine': this.parentComponent.combine !== false ? "true" : "false"
            //             },
            //             beforeSend: ()=>{
            //                 VendorPageLess.ChangeButtonState(this,'');
            //             }
            //         }).then(result=>{
            //             VendorPageLess.RestoreButtonState(this);
            //             if (result.response == 200) {
            //                 this.parentComponent.props.updatechart.call(this.parentComponent, result.response_body.additional);
            //             }
            //         });
            //     }
            // },
            getintervaltext(){
                let text = '';
                switch (this.interval) {
                    case 'daily':
                        text = 'Today';
                        break;
                    case 'weekly':
                        text = 'This Week';
                        break;
                    case 'monthly':
                        text = 'This Month';
                        break;
                    case 'yearly':
                        text = 'This Year';
                        break;
                    default:
                        text = 'Today';
                        break;
                } 
                return text; 
            },
            updatechart: function(data){
                let transactions = data;
                let chartData    = this.props.generateChartData.call(this, transactions);
                let mainData     = transactions[this.interval];
                this.setData({
                    datagroup: JSON.stringify(data),
                    totallocal: mainData.now.formatted_local_amount,
                    localcount: mainData.now.local,
                    localcounttext: `Total`,
                    totalusd: mainData.now.formatted_usd_amount,
                    usdcount: mainData.now.usd,
                    usdcounttext: `Total`,
                    intervaltext: this.props.getintervaltext.call(this),
                    chartdata: chartData,
                });
            }
        },
        view: function(){
            return /*html*/`
                <div class="w-100 m-0 p-2 p-md-3">
                    <div class="row m-0 justify-content-center main-content-item border-radius-10 ">
                        <div class="w-100 d-flex justify-content-center pb-2 pt-3 align-items-center position-relative">
                            <h6 class="text-muted m-0 p-0">${this.title}</h6>
                            <pageless-button text="<i class='fa fa-lg fa-ellipsis-h text-muted'></i>" classname="btn-circle init-context-menu" onclick="{{this.props.oncontextclick}}"></pageless-button>
                        </div>
                        <div class="p-0 w-100 h-100">
                            <custom-chart classname="h-200px w-100" type="line" chartdata='${JSON.stringify(this.chartdata)}'></custom-chart>
                        </div>

                        <div class="justify-content-center align-items-center no-gutters w-100 h-100">
                            <div class="w-100 text-center"><b class="text-muted text-preload-100 preload-50">${this.intervaltext}</b></div>
                            <div class="w-100 p-2 p-md-3">
                                <div class="w-100 h-50px border-radius-5 p-2 bg-light-gray d-flex justify-content-between align-items-center">
                                    <div class="flex-1 justify-content-center">
                                        <span class="text-dark"><b class="text-preloader preload-50">${this.totallocal}</b></span>
                                    </div>
                                    <div class="text-center d-flex align-items-center flex-column">
                                        <div class="text-preloader preload-100">${this.localcount}</div>
                                        <div class="text-preloader preload-85 text-muted small">${this.localcounttext}</div>
                                    </div>
                                </div>
                                <div class="w-100 h-50px mt-2 mt-md-3 border-radius-5 p-2 bg-light-gray d-flex justify-content-between align-items-center">
                                    <div class="flex-1 justify-content-center">
                                        <span class="text-dark"><b class="text-preloader preload-50">${this.totalusd}</b></span>
                                    </div>
                                    <div class="text-center d-flex align-items-center flex-column">
                                        <div class="text-preloader preload-100">${this.usdcount}</div>
                                        <div class="text-preloader preload-85 text-muted small">${this.usdcounttext}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

        },
        callback: function(){
            this.ready(()=>{
                // VendorPageLess.Request({
                //     url: `${Vendor'/api'}/transactions/stats`,
                //     method: "GET",
                //     data: {
                //         'user-id': this.userid,
                //         'interval': this.interval,
                //         'mode': this.mode,
                //         'type': this.type,
                //         'combine': this.combine !== false ? "true" : "false"
                //     },
                // }).then(result=>{
                //     if (result.response == 200) {
                //         this.props.updatechart.call(this, result.response_body.additional);
                //     } else {
                        
                //     }
                // });
            });
        }
    }),

    ToolCountySelect : new PageLessComponent("tool-county-select", {
        data: {
            text: "All Counties",
            items: [{text: "Loading...", value: ' '}],
            selectedvalue: ""
        },
        props: {
            oncountychange: ()=>{}
        },
        view: function(){
            return /*html*/`
                <div class="month-section tool d-flex flex-nowrap align-items-center" style="background: rgba(0, 0, 0, 0.05) !important;">
                    <i class="fa fa-map-marked-alt text-muted"></i>&nbsp;
                    <native-select onchange="{{this.props.oncountychange}}" placeholder="${this.text}" items='${JSON.stringify(this.items)}' selectedvalue="${this.selectedvalue}" classname="form-select types text-muted" style="border-bottom: 0;"></native-select>
                </div>
            `;
        },
        callback: function(){
            this.ready(()=>{
                PageLess.Request({
                    url: `/api/view-all-counties`,
                    method: "GET",
                }).then(result=>{
                    if(result.status == 200){
                        const data    = result.response_body;
                        let options = [];
                        data.forEach(county=>{
                            options.push({
                                text: county.title,
                                value: county.id,
                                districts: county.districts
                            });
                        });
                        this.setData({
                            items: options
                        });
                    } else {
                        this.setData({
                            items: [{text: "No County Available", value: ''}]
                        });
                    }
                });
            });
        }
    }),

    ToolCountyPrecinctSelect : new PageLessComponent("tool-county-precinct-select", {
        data: {
            countyid: '',
            text: "All Precincts",
            items: [{text: "Loading...", value: ' '}],
            selectedvalue: ""
        },
        props: {
            onprecinctchange: ()=>{}
        },
        view: function(){
            return /*html*/`
                <div class="month-section tool d-flex flex-nowrap align-items-center" style="background: rgba(0, 0, 0, 0.05) !important;">
                    <i class="fa fa-map-marker-alt text-muted"></i>&nbsp;
                    <native-select onchange="{{this.props.onprecinctchange}}" placeholder="${this.text}" items='${JSON.stringify(this.items)}' selectedvalue="${this.selectedvalue}" classname="form-select types text-muted" style="border-bottom: 0;"></native-select>
                </div>
            `;
        },
        callback: function(){
            PageLess.Request({
                url: `/api/get-precincts-by-county`,
                method: "GET",
                data:{
                    "county-id": this.countyid
                }
            }).then(result=>{
                if(result.status == 200){
                    const data  = result.response_body;
                    let options = [];
                    data.forEach(precinct=>{
                        options.push({
                            text: precinct.title,
                            value: precinct.id
                        });
                    });
                    this.setData({
                        items: options
                    });
                }else {
                    this.setData({
                        items: [{text: "No Precinct Available", value: ''}]
                    });
                }
            });
        }
    }),

    // list items for the staff roles
    staffRole : new PageLessComponent("staff-role", {
        data: {
            id: "",
            name: "",
            description: "",
            rights: []
        },
        props: {
            oncontextclick: function(event){
                event.stopPropagation();
                PageLess.ContextMenu([
                    {
                        text: "Manage Rights",
                        callback: ()=>{
                            
                            PageLess.Request({
                                url: `/api/view-role-details/${this.parentComponent.id}`,
                                method: "GET",
                                beforeSend: ()=>{
                                    PageLess.ChangeButtonState(this, '');
                                }
                            }).then(result=>{
                                PageLess.RestoreButtonState(this);

                                if (result.status == 200) {
                                    let data                 = result.response_body;
                                    let assignedModules      = data.assigned_modules;
                                    let assignedModulesInput = `<div>The following roles are currently assigned to this role</div>`;
                                    if (assignedModules != 404) {
                                        assignedModules.forEach(module => {
                                            assignedModulesInput += /*html*/ `
                                                <check-box checked="1" attributes='disabled="disabled"' classname="modules" text="${module.module_title}" value="${module.module_id}" identity="${module.module_id}"></check-box>
                                            `;
                                        });

                                    } else {
                                        assignedModulesInput = /*html*/ `
                                            <no-data icon="fa-key" text="There are no modules assined to this role yet. "></no-data>
                                        `;
                                    }
                                    Modal.BuildForm({
                                        title: "Assigned Modules",
                                        icon: "key",
                                        inputs: /*html*/ `
                                            ${assignedModulesInput}
                                        `,
                                        submitText: "Assign New",
                                        closable: false,
                                        autoClose: false,
                                    }, (assignedModuleValues)=>{
                                        let assignableModules = data.app_modules[0];
                                        if (assignableModules.length > 0) {
                                            let modulesInput      = '';
                                            assignableModules.forEach(module => {
                                                let checkState = "0";
                                                if (assignedModules != 404) {
                                                    assignedModules.forEach(aModule=>{
                                                        if(aModule.module_id == module.module_id){
                                                            checkState = "1";
                                                        }
                                                    });
                                                }
                                                if (checkState == "0") {
                                                    modulesInput += /*html*/ `
                                                        <check-box classname="modules" text="${module.module_title}" value="${module.module_id}" identity="${module.module_id}"></check-box>
                                                    `;
                                                }

                                            });
                                            Modal.BuildForm({
                                                title: "Assign Modules",
                                                icon: "key",
                                                description: modulesInput == '' ? `<no-data icon="fa-key" text="There are no more modules available to assign. You've already assigned all to this role"></no-data>` : `Please select the modules you with to assign to this role`,
                                                inputs: /*html*/ `
                                                    <div class="w-100">
                                                        ${modulesInput}
                                                    </div>
                                                `,
                                                noSubmit: modulesInput == '' ? true : false,
                                                submitText: "Assign",
                                                closeText: 'Cancel',
                                                closable: false,
                                                autoClose: false,
                                            }, assignmentValues=>{
                                                let moduleCheckBoxes = assignmentValues.modal.querySelectorAll('.modules:checked');
                                                if (moduleCheckBoxes.length > 0) {
                                                    let selectedModules = [];
                                                    moduleCheckBoxes.forEach(moduleCheckBox=>{
                                                        selectedModules.push(moduleCheckBox.value);
                                                    });

                                                    PageLess.Request({
                                                        url: `/api/assign-module-rights-to-role`,
                                                        method: "POST",
                                                        data: {
                                                            role_id: this.parentComponent.id,
                                                            module_list: selectedModules
                                                        },
                                                        beforeSend: ()=>{
                                                            PageLess.ChangeButtonState(assignmentValues.submitBtn);
                                                        }
                                                    }, true).then(result=>{
                                                        PageLess.RestoreButtonState(assignmentValues.submitBtn);
                                                        if (result.status == 200) {
                                                            PageLess.Toast('success', result.message);
                                                            Modal.Close(assignedModuleValues.modal);
                                                            Modal.Close(assignmentValues.modal);
                                                        } else{
                                                            PageLess.Toast('danger', result.message, 5000);
                                                        }
                                                    });
                                                    
                                                } else {
                                                    PageLess.Toast('danger', "Please select the modules you wish to assign before you proceeding", 5000);
                                                }
                                            });
                                            
                                        } else {
                                            PageLess.Toast('danger', 'There a no modules to assign at the moment. Please try again later');
                                        }
                                    });
                                } else {
                                    PageLess.Toast("danger", 'Unable to fetch role details at the moment.');
                                }
                            });
                        }
                    },
                    {
                        text: "Delete",
                        callback: ()=>{
                            Modal.Confirmation("Confirm Deletion", "Before deleting, please make sure this roles is not assigned to any user. This action cannot be undone! Are you sure you want to continue?").then(()=>{
                                
                            });
                        }
                    }
                ], this);
            },
        },
        view: function(){
            
            return /*html*/`
                <div class="col-12">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-6 main-content-item container-shadow">
                            <div class="settings-details">
                                <div class="settings-body">
                                    <div class="settings-title text-muted">${this.name}</div>
                                    <div class="settings-details text-muted">${this.description}</div>
                                </div>
                            </div>
                            <div class="settings-action">
                                <pageless-button class="btn-circle" text='<i class="fa text-muted fa-lg fa-ellipsis-v"></i>' onclick="{{this.props.oncontextclick}}"></pageless-button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    // empty data component
    noDataComponent : new PageLessComponent("no-data", {
        data: {
            text: null, 
            icon: "fa-meh"
        },
        view: function(){
            return /*html*/ `
                <div class="col-12 content" style="padding-bottom: 0 !important;">
                    <div class="row h-100">
                        <div class="col-12 h-100">
                            <div class="row p-0 h-100 flex-column align-items-center justify-content-center">
                                <div class="error-404-page-wrapper">
                                    <div class="col-12">
                                        <div class="oops-text-holder mb-2">
                                            <h1 class="text-center"><i class="fad fa-2x ${this.icon} text-t-orange fa-primary-opacity-0_5 fa-secondary-opacity-0_2"></i></h1>
                                        </div>
                                        <div class="page-not-found-text">
                                            <p>${this.text}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    ContentHeader : new PageLessComponent("main-content-header", {
        data:{
            title: null,
            actions: '',
            startpage: false,
        },
        props: {
            togglesidebar: function(){
                const sidebar = this.closest('.main-container').querySelector('.sidebar-container');
                if (sidebar != null) {
                    sidebar.props.toggle.call(sidebar);
                }
            }
        },
        view: function(){
            let navButton;
            if (this.startpage !== false) {
                navButton = /*html*/ `
                    <button class="btn-circle d-md-none mobile-side-bar-toggler" toggle="0" onclick="this.closest('.main-content-header').props.togglesidebar.call(this)">
                        <span class="fa-stack fa-lg">
                            <i class="fa fa-circle fa-stack-2x text-white"></i>
                            <i class="fa fa-bars fa-stack-1x text-t-blue" style="font-size: 1.2em"></i>
                        </span>
                    </button>
                `;
            } else {
                navButton = /*html*/ `
                    <back-button></back-button>
                `;
                
            }
            return /*html*/ `
                <div class="main-content-header p-relative">
                    <div class="row p-2 h-100 align-items-center justify-content-between">
                        <div class="d-flex h-100 align-items-center pl-2">
                            ${navButton}
                            <div class="bread-crumb-container flex-1 pl-2">
                                <span class="content-title">${this.title}</span>
                            </div>                                  
                        </div>
                        <div class="text-t-blue">
                            ${this.actions}
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    sidebarItem : new PageLessComponent("sidebar-item", {
        data: {
            text: '',
            icon: '',
            link: '',
            title: ''
        },
        props: {
            onclick: function(){
                if (this.link != 'logout') {
                    (new PageLess(this.link)).route();
                } else {
                    PageLess.Request({
                        url: `/api/app-logout`,
                        method: "POST",
                        data: {}
                    }, true).then(result=>{
                        window.location.href = '/';
                    });
                }
            }
        },
        view: function(){
            return /*html*/`
                <li class="sidebar-menu-item" onclick="{{this.props.onclick}}">
                    <div class="sidebar-menu-item-content pl-4 pr-3 slow-trans">
                        <span class="sidebar-menu-item-icon" title="${this.title}"><i class="fad fa-lg fa-${this.icon}"></i></span>
                        <span class="sidebar-menu-item-desc">&emsp;${this.text}</span>
                    </div>
                </li>
            `;
        }
    }),

    sidebar : new PageLessComponent("side-bar", {
        data: {
            title: 'Exective Access',
            userimage: '/media/images/seal.png',
            username: '',
            hidden: true,
            items: /*html*/ `
                <sidebar-item text="Loading..." icon="spinner-third fad fa-spin"></sidebar-item>
                <sidebar-item text="Loading..." icon="spinner-third fad fa-spin"></sidebar-item>
                <sidebar-item text="Loading..." icon="spinner-third fad fa-spin"></sidebar-item>
                <sidebar-item text="Loading..." icon="spinner-third fad fa-spin"></sidebar-item>
            `
        },
        props: {
            toggle: function(){
                const sidebar        = this.querySelector('.sidebar');
                if (this.hidden == true) {
                    this.style.display = 'block';
                    const sideBarWidth = sidebar.offsetWidth;
                    sidebar.style.left = `-${sideBarWidth}px`;
                    sidebar.animate(
                        [
                            { left: `-${sideBarWidth}px` },
                            { left: "0px" },
                        ],
                        {
                            fill: 'forwards',
                            duration: 250,
                            iterations: 1,
                            easing: 'ease-in-out'
                        }
                    );
                    this.classList.remove('mobile-sidebar-hider');
                    this.hidden = false; 
                } else {
                    const sideBarWidth = sidebar.offsetWidth;
                    sidebar.animate(
                        [
                            { left: "0px" },
                            { left: `-${sideBarWidth}px` },
                        ],
                        {
                            duration: 250,
                            iterations: 1,
                            easing: 'ease-in-out'
                        }
                    ).finished.then(()=>{
                        this.style.display = 'none';
                        this.hidden = true;
                        this.classList.add('mobile-sidebar-hider');
                        sidebar.style.left = '0';
                    });
                }
            },

            onclick: function(event){
                const style = getComputedStyle(this);
                if(event.target.classList.contains('sidebar-container')){
                    if(this.style.display != 'none' && style.position == 'absolute'){
                        this.props.toggle.call(this);
                    }
                }
            }
        },
        view: function(){
            return /*html*/`
                <div class="sidebar-container" onclick="{{this.props.onclick}}">
                    <div class="sidebar">
                        <div class="text-white">
                            <div class="sidebar-title">
                                <div class="app-name">${this.title}</div>
                            </div>
                        </div>

                        <ul class="sidebar-menu-container p-relative">
                            <div class="w-100 d-flex pl-3 pr-3 align-items-center py-3">
                                <div class="w-100 d-flex justify-content-center align-items-center">
                                    <bg-image classname=" w-75px h-75px d-flex justify-content-center align-items-center" src="${this.userimage}"></bg-image>
                                </div>
                            </div>
                            <div class="w-100 d-flex justify-content-center p-2 mb-3">
                                <span class="text-white">${this.username}</span>
                            </div>
                            ${this.items}
                        </ul>
                    </div>
                </div>
            `;
        },
        callback: function(){
            this.ready(()=>{
                PageLess.Request({
                    url: `/api/user-sidebar-items`,
                    method: "GET",
                }).then(result=>{
                    if (result.status == 200) {
                        let data  = result.response_body;
                        let userType = data.user_type;
                        window.AppUserType = userType;
                        let items = ``;
                        data.side_bar_items.forEach(item=>{
                            items += /*html*/ `<sidebar-item text="${item.title}" link="/${item.link}" icon="${item.icon}"></sidebar-item>`;
                        });

                        items += /*html*/ `<sidebar-item text="Logout" link="logout" icon="door-open"></sidebar-item>`;

                        this.setData({
                            username: data.username,
                            usertype: data.user_type,
                            items: items
                        });
                    } else if(result.status == 404){
                        let items = /*html*/ `
                            <sidebar-item text="Logout" link="logout" icon="door-open"></sidebar-item>
                        `;

                        this.setData({
                            items: items
                        });
                    }
                });
            });
        }
    }),

    StaffRoleSelect : new PageLessComponent("staff-role-select", {
        data: {
            userid: '',
            selectedvalue: '',
            options: [{text: 'Loading...', value: ''}],
        },
        props: {
            key: function(){},
        },
        view: function(){
            return /*html*/`
                <div>
                    <custom-select
                        identity="role"
                        icon="lock"
                        text="User Role"
                        items='${JSON.stringify(this.options)}'
                        required="required"
                        selectedvalue="${this.selectedvalue}"
                    ></custom-select>
                </div>
            `;
        },
        callback: function(){
            this.ready(()=>{
                PageLess.Request({
                    url: `/api/view-staff-roles`,
                    method: "GET",
                }).then(result=>{
                    if(result.status == 200){
                        let data    = result.response_body.role_details;
                        let options = [];
                        data.forEach(role=>{
                            options.push({
                                text: role.role_title,
                                value: role.role_id
                            });
                        });
                        this.setData({
                            options: options
                        });
                    } else {
                        this.setData({
                            options: [{text: 'No role has been added', value: "..."}]
                        });
                    }
                });
            });
        }
    }),

    CountyItem: new PageLessComponent("county-item", {
        data: {
            id: '',
            name: '',
            actionicon: 'ellipsis-v'
        },
        props: {
            ondistrictclick: function(){
                (new PageLess(`/counties/${this.parentComponent.id}/districts`)).route();
            },
            oncontextclick: function(event){
                event.stopPropagation();
                let parent = this.parentComponent;
                PageLess.ContextMenu([
                    {
                        text: "Edit",
                        callback: ()=>{
                            
                            Modal.BuildForm({
                                title: "Update County",
                                icon: "map-marker-edit",
                                description: ``,
                                inputs: /*html*/ `
                                    <text-input value="${parent.name}" icon="user" text="County Name" identity="name" required="required"></text-input>
                                `,
                                submitText: "Update",
                                closeText: 'Cancel',
                                closable: false,
                                autoClose: false,
                            }, values=>{
                                PageLess.Request({
                                    url: `/api/update-county`,
                                    method: "POST",
                                    data: {
                                        county_id: parent.id,
                                        title: values.name,
                                    },
                                    beforeSend: ()=>{
                                        PageLess.ChangeButtonState(values.submitBtn);
                                    }
                                }, true).then(result=>{
                                    PageLess.RestoreButtonState(values.submitBtn);
                                    if (result.status == 200) {
                                        PageLess.Toast('success', result.message);
                                        Modal.Close(values.modal);
                                        parent.setData({
                                            name: values.name,
                                        });
                                    } else{
                                        PageLess.Toast('danger', result.message, 5000);
                                    }
                                });
                            });
                        }
                    },
                    {
                        text: "Delete",
                        callback: ()=>{
                            Modal.Confirmation("Confirm Deletion", "This action cannot be undone! Are you sure you want to continue?").then(()=>{
                                PageLess.Request({
                                    url: `/api/remove-county`,
                                    method: "POST",
                                    data: {
                                        county_id: parent.id
                                    },
                                    beforeSend: ()=>{
                                        PageLess.ChangeButtonState(this, '');
                                    }
                                }, true).then(result=>{
                                    PageLess.RestoreButtonState(this);
                                    if (result.status == 200) {
                                        PageLess.Toast('success', result.message);
                                        parent.remove();
                                    } else {
                                        PageLess.Toast('success', result.message, 5000);
                                    }
                                });
                            });
                        }
                    }
                ], this);
            },
        },
        view: function(){
            return /*html*/`
                <div class="col-12" onclick="{{this.props.onclick}}">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-5 main-content-item container-shadow">
                            <div class="settings-details">
                                <div class="settings-body">
                                    <div class="settings-title text-muted">${this.name}</div>
                                </div>
                            </div>
                            <div class="settings-action position-relative d-flex align-items-center">
                                <pageless-button class="btn btn-lignt" text='Districts' onclick="{{this.props.ondistrictclick}}"></pageless-button>&emsp;
                                <pageless-button class="btn-circle" text='<i class="fa text-muted fa-lg fa-ellipsis-v"></i>' onclick="{{this.props.oncontextclick}}"></pageless-button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    DistrictItem: new PageLessComponent("district-item", {
        data: {
            id: '',
            name: '',
            actionicon: 'ellipsis-v'
        },
        props: {
            oncontextclick: function(event){
                event.stopPropagation();
                let parent = this.parentComponent;
                PageLess.ContextMenu([
                    {
                        text: "Edit",
                        callback: ()=>{
                            
                            Modal.BuildForm({
                                title: "Update District",
                                icon: "map-marker-edit",
                                description: ``,
                                inputs: /*html*/ `
                                    <text-input value="${parent.name}" icon="user" text="District Name" identity="name" required="required"></text-input>
                                `,
                                submitText: "Update",
                                closeText: 'Cancel',
                                closable: false,
                                autoClose: false,
                            }, values=>{
                                PageLess.Request({
                                    url: `/api/update-county-district`,
                                    method: "POST",
                                    data: {
                                        county_id: parent.countyid,
                                        district_id: parent.id,
                                        title: values.name,
                                    },
                                    beforeSend: ()=>{
                                        PageLess.ChangeButtonState(values.submitBtn);
                                    }
                                }, true).then(result=>{
                                    PageLess.RestoreButtonState(values.submitBtn);
                                    if (result.status == 200) {
                                        PageLess.Toast('success', result.message);
                                        Modal.Close(values.modal);
                                        parent.setData({
                                            name: values.name,
                                        });
                                    } else{
                                        PageLess.Toast('danger', result.message, 5000);
                                    }
                                });
                            });
                        }
                    },
                    {
                        text: "Delete",
                        callback: ()=>{
                            Modal.Confirmation("Confirm Deletion", "This action cannot be undone! Are you sure you want to continue?").then(()=>{
                                PageLess.Request({
                                    url: `/api/remove-district`,
                                    method: "POST",
                                    data: {
                                        county_id: parent.countyid,
                                        district_id: parent.id
                                    },
                                    beforeSend: ()=>{
                                        PageLess.ChangeButtonState(this, '');
                                    }
                                }, true).then(result=>{
                                    PageLess.RestoreButtonState(this);
                                    if (result.status == 200) {
                                        PageLess.Toast('success', result.message);
                                        parent.remove();
                                    } else {
                                        PageLess.Toast('success', result.message, 5000);
                                    }
                                });
                            });
                        }
                    }
                ], this);
            },
        },
        view: function(){
            return /*html*/`
                <div class="col-12" onclick="{{this.props.onclick}}">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-5 main-content-item container-shadow">
                            <div class="settings-details">
                                <div class="settings-body">
                                    <div class="settings-title text-muted">${this.name}</div>
                                </div>
                            </div>
                            <div class="settings-action position-relative d-flex align-items-center">
                                <pageless-button class="btn-circle" text='<i class="fa text-muted fa-lg fa-ellipsis-v"></i>' onclick="{{this.props.oncontextclick}}"></pageless-button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    DepartmentItem: new PageLessComponent("department-item", {
        data: {
            id: '',
            name: '',
            actionicon: 'ellipsis-v'
        },
        props: {
            oncontextclick: function(event){
                event.stopPropagation();
                const parent = this.parentComponent;
                PageLess.ContextMenu([
                    {
                        text: "View Staff",
                        callback: ()=>{
                            (new PageLess(`/departments-manager/${parent.id}/staff`)).route()
                        }
                    },
                    {
                        text: "View Executives",
                        callback: ()=>{
                            (new PageLess(`/departments-manager/${parent.id}/executives`)).route()
                        }
                    },
                    {
                        text: "Edit",
                        callback: ()=>{
                            
                            Modal.BuildForm({
                                title: "Update Department",
                                icon: "sitemap",
                                description: ``,
                                inputs: /*html*/ `
                                    <text-input value="${parent.name}" icon="user" text="Department Name" identity="name" required="required"></text-input>
                                `,
                                submitText: "Update",
                                closeText: 'Cancel',
                                closable: false,
                                autoClose: false,
                            }, values=>{
                                PageLess.Request({
                                    url: `/api/update-department`,
                                    method: "POST",
                                    data: {
                                        department_id: parent.id,
                                        title: values.name,
                                    },
                                    beforeSend: ()=>{
                                        PageLess.ChangeButtonState(values.submitBtn);
                                    }
                                }, true).then(result=>{
                                    PageLess.RestoreButtonState(values.submitBtn);
                                    if (result.status == 200) {
                                        PageLess.Toast('success', result.message);
                                        Modal.Close(values.modal);
                                        parent.setData({
                                            name: values.name,
                                        });
                                    } else{
                                        PageLess.Toast('danger', result.message, 5000);
                                    }
                                });
                            });
                        }
                    },
                    {
                        text: "Delete",
                        callback: ()=>{
                            Modal.Confirmation("Confirm Deletion", "This action cannot be undone! Are you sure you want to continue?").then(()=>{
                                PageLess.Request({
                                    url: `/api/delete-department`,
                                    method: "POST",
                                    data: {
                                        department_id: parent.id
                                    },
                                    beforeSend: ()=>{
                                        PageLess.ChangeButtonState(this, '');
                                    }
                                }, true).then(result=>{
                                    PageLess.RestoreButtonState(this);
                                    if (result.status == 200) {
                                        PageLess.Toast('success', result.message);
                                        parent.remove();
                                    } else {
                                        PageLess.Toast('success', result.message, 5000);
                                    }
                                });
                            });
                        }
                    }
                ], this);
            },
        },
        view: function(){
            return /*html*/`
                <div class="col-12" onclick="{{this.props.onclick}}">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-10 main-content-item">
                            <div class="settings-details">
                                <div class="settings-body">
                                    <div class="settings-title text-muted">${this.name}</div>
                                </div>
                            </div>
                            <div class="settings-action position-relative d-flex align-items-center">
                                <pageless-button class="btn-circle" text='<i class="fa text-muted fa-lg fa-ellipsis-v"></i>' onclick="{{this.props.oncontextclick}}"></pageless-button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    DepartmentSelect : new PageLessComponent("department-select", {
        data: {
            userid: '',
            selectedvalue: '',
            selectedexecutive: '',
            executives: '',
            includeexecutives: false,
            options: [{text: 'Loading...', value: '...'}],
        },
        view: function(){
            return /*html*/`
                <div>
                    <custom-select
                        identity="department"
                        icon="sitemap"
                        text="Department"
                        items='${JSON.stringify(this.options)}'
                        required="required"
                        selectedvalue="${this.selectedvalue}"
                        onchange="{{this.props.onchange}}"
                    ></custom-select>
                    ${this.includeexecutives !== false ? this.executives : ''}
                </div>
            `;
        },
        props: {
            onchange: function(){
                const value      = this.querySelector('select').value;
                let executives = ''
                if (value != '') {
                    executives = /*html*/ `<executive-select selectedvalue="${this.parentComponent.selectedexecutive}" departmentid="${value}"></executive-select>`
                }

                this.parentComponent.setData({
                    selectedvalue: value, 
                    executives: executives
                });
            }
        },
        callback: function(){
            this.ready(()=>{
                PageLess.Request({
                    url: `/api/get-departments`,
                    method: "GET",
                }).then(result=>{
                    if(result.status == 200){
                        let data    = result.response_body;
                        let options = [];
                        data.forEach(department=>{
                            options.push({
                                text: department.title,
                                value: department.id
                            });
                        });

                        let obj = {
                            options: options
                        }

                        if (this.selectedvalue && this.selectedexecutive != '') {
                            obj.executives = /*html*/ `<executive-select selectedvalue="${this.selectedexecutive}" departmentid="${this.selectedvalue}"></executive-select>`
                        }
                        this.setData(obj);
                    } else {
                        this.setData({
                            options: [{text: 'No department has been added', value: "..."}]
                        });
                    }
                });
            });
        }
    }),

    DepartmentStaff : new PageLessComponent("department-staff", {
        data: {
            departmentid: "",
            userid: "",
            fullname: '',
            image: "",
        },
        props: {
            staffoptionclick: function(event){
                event.stopPropagation();
                const parent = this.parentComponent;
                PageLess.ContextMenu([
                    {
                        text: 'Unassign', 
                        callback: ()=>{
                            Modal.Confirmation("Confirm Action", "Are you sure you want to continue?").then(()=>{
                                PageLess.Request({
                                    url: `/api/unassign-staff-from-department`,
                                    method: "POST",
                                    data: {
                                        staff_id : parent.userid,
                                        department_id : parent.departmentid
                                    },
                                    beforeSend: ()=>{
                                        PageLess.ChangeButtonState(this, '');
                                    }
                                }, true).then(result=>{
                                    PageLess.RestoreButtonState(this);
                                    if (result.status == 200) {
                                        PageLess.Toast('success', result.message);
                                        parent.remove();
                                    } else {
                                        PageLess.Toast('danger', result.message, 5000);
                                    }
                                });
                            })
                        }
                    },
                ], this);
            },
        },
        view: function(){
            return /*html*/`
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <div class="row p-1 p-sm-1 p-md-2 p-lg-2 p-xl-3 justify-content-center">
                        <div class="user-card position-relative">
                            <pageless-button classname="init-context-menu" text="<i class='fad fa-lg fa-ellipsis-v-alt'></i>" onclick="{{this.props.staffoptionclick}}"></pageless-button>
                            <div class="user-image-container">
                                <div class="user-image" style="background: url('${this.image != "" && this.image !== 'null' && this.image !== null ? this.image : this.defaultImage}')"></div>
                            </div>
                            <div class="user-fullname">${this.fullname}</div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    ExecutiveSelect : new PageLessComponent("executive-select", {
        data: {
            departmentid: '',
            selectedvalue: '',
            options: [{text: 'Loading...', value: '...'}],
        },
        view: function(){
            return /*html*/`
                <div>
                    <custom-select
                        identity="executive"
                        icon="user-tie"
                        text="Meeting With"
                        items='${JSON.stringify(this.options)}'
                        required="required"
                        selectedvalue="${this.selectedvalue}"
                    ></custom-select>
                </div>
            `;
        },
        callback: function(){
            this.ready(()=>{
                let request;
                if (this.department != '') {
                    request = {
                        url: `/api/get-department-executives/${this.departmentid}`,
                        method: 'GET'
                    }
                } else {
                    request = {
                        url: '/api/get-executive-members',
                        method: 'GET'
                    }
                }

                PageLess.Request(request, true).then(result=>{
                    if(result.status == 200){
                        const data  = result.response_body;
                        let options = [];
                        data.forEach(executive=>{
                            console.log(executive.id);
                            options.push({
                                text: executive.full_name,
                                value: executive.id
                            });
                        });
                        this.setData({
                            options: options
                        });
                    } else {
                        this.setData({
                            options: [{text: 'No executive has been added', value: "..."}]
                        });
                    }
                });
            });
        }
    }),

    ExecutiveItem: new PageLessComponent("executive-item", {
        data: {
            image: '/media/images/default_other_avatar.png',
            fullname: '',
            firstname: "",
            middlename: "",
            lastname: "",
            number: "",
            department: "",
            departmentid: "",
            actionicon: 'ellipsis-v',
            number: ''
        },
        props: {
            oncontextclick: function(event){
                event.stopPropagation();
                let parent = this.parentComponent;
                PageLess.ContextMenu([
                    {
                        text: "View Details",
                        callback: ()=>{
                            
                            Modal.BuildForm({
                                title: "Executive Details",
                                icon: "user-tie",
                                description: ``,
                                inputs: /*html*/ `
                                    <text-input value="${parent.firstname}" icon="user" text="First Name" identity="firstname" required="required"></text-input>
                                    <text-input value="${parent.middlename}" icon="user" text="Middle Name" identity="middlename"></text-input>
                                    <text-input value="${parent.lastname}" icon="user" text="Last Name" identity="lastname" required="required"></text-input>
                                    <number-input value="${parent.number}" icon="user" text="Phone Number" identity="number" required="required"></number-input>
                                    <department-select selectedvalue="${parent.departmentid}"></department-select>
                                `,
                                submitText: "Update",
                                closeText: 'Close',
                                closable: false,
                                autoClose: false,
                            }, values=>{
                                PageLess.Request({
                                    url: `/api/update-executive-member-info`,
                                    method: "POST",
                                    data: {
                                        executive_id: parent.id,
                                        first_name: values.firstname,
                                        middle_name: values.middlename,
                                        last_name: values.lastname,
                                        number: values.number,
                                        department_id: values.department,
                                    },
                                    beforeSend: ()=>{
                                        PageLess.ChangeButtonState(values.submitBtn);
                                    }
                                }, true).then(result=>{
                                    PageLess.RestoreButtonState(values.submitBtn);
                                    if (result.status == 200) {
                                        PageLess.Toast('success', result.message);
                                        Modal.Close(values.modal);
                                        parent.parentComponents('vertical-scroll-view').update()
                                    } else{
                                        PageLess.Toast('danger', result.message, 5000);
                                    }
                                });
                            });
                        }
                    },
                    {
                        text: "Delete",
                        callback: ()=>{
                            Modal.Confirmation("Confirm Deletion", "This action cannot be undone! Are you sure you want to continue?").then(()=>{
                                PageLess.Request({
                                    url: `/api/delete-executive_member`,
                                    method: "POST",
                                    data: {
                                        executive_id: parent.id
                                    },
                                    beforeSend: ()=>{
                                        PageLess.ChangeButtonState(this, '');
                                    }
                                }, true).then(result=>{
                                    PageLess.RestoreButtonState(this);
                                    if (result.status == 200) {
                                        PageLess.Toast('success', result.message);
                                        parent.remove();
                                    } else {
                                        PageLess.Toast('success', result.message, 5000);
                                    }
                                });
                            });
                        }
                    }
                ], this);
            },
        },
        view: function(){
            return /*html*/`
                <div class="col-12" onclick="{{this.props.onclick}}">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-10 main-content-item">
                            <div class="settings-details">
                                <bg-image classname="w-50px h-50px" src="${this.image}" rounded="true"></bg-image>
                                <div class="settings-body">
                                    <div class="settings-title text-muted">${this.fullname}</div>
                                    <div class="settings-details text-muted">${this.department}</div>
                                </div>
                            </div>
                            <div class="settings-action position-relative d-flex align-items-center">
                                <pageless-button class="btn-circle" text='<i class="fa text-muted fa-lg fa-ellipsis-v"></i>' onclick="{{this.props.oncontextclick}}"></pageless-button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    DepartmentExecutiveItem: new PageLessComponent("department-executive-item", {
        data: {
            image: '/media/images/default_other_avatar.png',
            fullname: '',
            firstname: "",
            middlename: "",
            lastname: "",
            number: "",
            department: "",
            departmentid: "",
            actionicon: 'ellipsis-v',
            number: ''
        },
        props: {
            oncontextclick: function(event){
                event.stopPropagation();
                let parent = this.parentComponent;
                PageLess.ContextMenu([
                    {
                        text: "View Details",
                        callback: ()=>{
                            
                            Modal.BuildForm({
                                title: "Executive Details",
                                icon: "user-tie",
                                description: ``,
                                inputs: /*html*/ `
                                    <text-input value="${parent.firstname}" icon="user" text="First Name" identity="firstname" required="required" disabled="disabled"></text-input>
                                    <text-input value="${parent.middlename}" icon="user" text="Middle Name" identity="middlename" disabled="disabled"></text-input>
                                    <text-input value="${parent.lastname}" icon="user" text="Last Name" identity="lastname" required="required" disabled="disabled"></text-input>
                                    <number-input value="${parent.number}" icon="user" text="Phone Number" identity="number" required="required" disabled="disabled"></number-input>
                                `,
                                noSubmit: true,
                                closeText: 'Close',
                                closable: false,
                                autoClose: false,
                            });
                        }
                    },
                    {
                        text: "Remove",
                        callback: ()=>{
                            Modal.Confirmation("Confirm Deletion", "This action cannot be undone! Are you sure you want to continue?").then(()=>{
                                PageLess.Request({
                                    url: `/api/delete-executive_member`,
                                    method: "POST",
                                    data: {
                                        executive_id: parent.id
                                    },
                                    beforeSend: ()=>{
                                        PageLess.ChangeButtonState(this, '');
                                    }
                                }, true).then(result=>{
                                    PageLess.RestoreButtonState(this);
                                    if (result.status == 200) {
                                        PageLess.Toast('success', result.message);
                                        parent.remove();
                                    } else {
                                        PageLess.Toast('success', result.message, 5000);
                                    }
                                });
                            });
                        }
                    }
                ], this);
            },
        },
        view: function(){
            return /*html*/`
                <div class="col-12" onclick="{{this.props.onclick}}">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-10 main-content-item">
                            <div class="settings-details">
                                <bg-image classname="w-50px h-50px" src="${this.image}" rounded="true"></bg-image>
                                <div class="settings-body">
                                    <div class="settings-title text-muted">${this.fullname}</div>
                                    <div class="settings-details text-muted">${this.number}</div>
                                </div>
                            </div>
                            <div class="settings-action position-relative d-flex align-items-center">
                                <pageless-button class="btn-circle" text='<i class="fa text-muted fa-lg fa-ellipsis-v"></i>' onclick="{{this.props.oncontextclick}}"></pageless-button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    StaffSelect : new PageLessComponent("staff-select", {
        data: {
            userid: '',
            selectedvalue: '',
            options: [{text: 'Loading...', value: '...'}],
        },
        view: function(){
            return /*html*/`
                <div>
                    <custom-select
                        identity="staff"
                        icon="user"
                        text="Staff"
                        items='${JSON.stringify(this.options)}'
                        required="required"
                        selectedvalue="${this.selectedvalue}"
                    ></custom-select>
                </div>
            `;
        },
        callback: function(){
            this.ready(()=>{
                PageLess.Request({
                    url: `/api/view-staffs`,
                    method: "GET",
                }).then(result=>{
                    if(result.status == 200){
                        let data    = result.response_body;
                        let options = [];
                        data.forEach(staff=>{
                            options.push({
                                text: staff.full_name,
                                value: staff.user_id
                            });
                        });
                        this.setData({
                            options: options
                        });
                    } else {
                        this.setData({
                            options: [{text: 'No user has been added', value: "..."}]
                        });
                    }
                });
            });
        }
    }),

    AppointmentItem: new PageLessComponent("appointment-item", {
        data: {
            id : "",
            name : "",
            number : "",
            purpose : "",
            departmentid : "",
            executiveid : "",
            executive : "",
            status : "",
            starttime : "",
            endtime : "",
            startday : "",
            startmonth : "",
            startyear : "",
            editable : "",
            // colors: ["bg-secondary", "bg-primary", "bg-danger", "bg-success", "bg-light-blue", "bg-gold", "bg-brown", "bg-coral", "bg-orange"]
            colors: {
                earlyMorning: "bg-primary", 
                lateMorning: 'bg-secondary',  
                noon: "bg-gold", 
                earlyAfternoon: 'bg-coral', 
                lateAfternoon: "bg-orange", 
                evening: "bg-danger"
            },
            bgColor: '',
            editable: true
        },

        props: {
            setColor: function(){
                let   bgColor;
                const intStartTime = parseInt(this.starttime.replace(':', ''));
                
                if (intStartTime <= 900) {
                    bgColor = this.colors.earlyMorning;
                } else if (intStartTime < 1200) {
                    bgColor = this.colors.lateMorning;
                } else if (intStartTime <= 1230) {
                    bgColor = this.colors.noon;
                } else if (intStartTime <= 1500) {
                    bgColor = this.colors.earlyAfternoon;
                } else if (intStartTime <= 1700) {
                    bgColor = this.colors.lateAfternoon;
                } else {
                    bgColor = this.colors.evening;
                }

                this.setData({
                    bgColor: bgColor
                })
            },

            oncontextclick: function(event){
                event.stopPropagation();
                let parent = this.parentComponent;
                PageLess.ContextMenu([
                    {
                        text: "Edit",
                        callback: ()=>{
                            
                            Modal.BuildForm({
                                title: "Update Appointment",
                                icon: "user-tie",
                                description: `Please enter the details below`,
                                inputs: /*html*/ `
                                    <text-input value="${parent.name}" icon="user" text="Visitor Name" identity="visitor-name" required="required" ></text-input>
                                    <department-select selectedvalue="${parent.departmentid}" selectedexecutive="${parent.executiveid}" includeexecutives=true ></department-select>
                                    <long-text-input value="${parent.purpose}" icon="align-justify" text="Purpose" identity="purpose"></long-text-input>
                                    <number-input value="${parent.number}" icon="phone-alt" text="Visitor Phone Number" identity="number"></number-input>
                                    <date-input selectedyear="${parent.startyear}" selectedmonth="${parent.startmonth}" selectedday="${parent.startday}" text="Date"></date-input>
                                    <time-input selectedvalue="${parent.starttime}:00" icon="clock" text="Start Time" identity="start-time" required="required"></time-input>
                                    <time-input selectedvalue="${parent.endtime}:00" icon="clock" text="End Time" identity="end-time" required="required"></time-input>
                                `,
                                submitText: "Update",
                                closable: false,
                                autoClose: false,
                            }, values=>{
                                console.log(values);
                                PageLess.Request({
                                    url: `/api/update-appointment`,
                                    method: "POST",
                                    data: {
                                        appointment_id: parent.id,
                                        executive_id: values.executive,
                                        department_id: values.department,
                                        visitor_name: values['visitor-name'],
                                        number: values.number,
                                        purpose: values.purpose,
                                        visit_date: `${values.year}-${values.month}-${values.day}`,
                                        start_time: values['start-time'],
                                        end_time: values['end-time']
                                    },
                                    beforeSend: ()=>{
                                        PageLess.ChangeButtonState(values.submitBtn, 'Adding');
                                    }
                                }, true).then(result=>{
                                    PageLess.RestoreButtonState(values.submitBtn);
                                    if (result.status == 200) {
                                        this.parentComponents('vertical-scroll-view').update();
                                        PageLess.Toast('success', result.message);
                                        Modal.Close(values.modal);
                                    } else {
                                        PageLess.Toast('danger', result.message);
                                    }
                                });
                            });
                        }
                    },
                    {
                        text: "Delete",
                        callback: ()=>{
                            Modal.Confirmation("Confirm Deletion", "This action cannot be undone! Are you sure you want to continue?").then(()=>{
                                PageLess.Request({
                                    url: `/api/delete-appointment`,
                                    method: "POST",
                                    data: {
                                        appointment_id: parent.id
                                    },
                                    beforeSend: ()=>{
                                        PageLess.ChangeButtonState(this, '');
                                    }
                                }, true).then(result=>{
                                    PageLess.RestoreButtonState(this);
                                    if (result.status == 200) {
                                        PageLess.Toast('success', result.message);
                                        parent.remove();
                                    } else {
                                        PageLess.Toast('success', result.message, 5000);
                                    }
                                });
                            });
                        }
                    }
                ], this);
            },
        },
        
        view: function(){
            return /*html*/`
                <div class="appointment-item">
                    <div class="time-container position-relative">
                        <div class="small d-flex justify-content-center flex-column flex-sm-row align-items-center">
                            <div>${this.starttime}</div>
                            <div><span class="d-none d-sm-inline p-1">-</span><span class="d-inline d-sm-none"> to </span></div>
                            <div>${this.endtime}</div>
                        </div>
                        <div class="dot"></div>
                    </div>
                    <div class="details-container pl-3 p-2">
                        <div class="details p-2 p-sm-3 text-dark ${this.bgColor}">
                            <div class="properties">
                                <div class="name h6 font-weight-bold">${this.name}</div>
                                <div class="property text-muted">Meeting With: <b>${this.executive}</b></div>
                                <div class="property w-100 text-muted">Purpose: <b>${this.purpose}</b></div>
                                <div class="property small text-muted">Status: <b>${this.status.toUpperCase()}</b></div>
                            </div>
                            <div class="action">
                                ${this.editable === true || this.editable == 'true' ? /*html*/ `<pageless-button class="btn-circle" text='<i class="fa text-muted fa-lg fa-ellipsis-v"></i>' onclick="{{this.props.oncontextclick}}" />` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        },

        callback: function(){
            this.ready(()=>{
                this.props.setColor.call(this);
            });
        }
        
    }),

    AppointmentGroup: new PageLessComponent("appointment-group", {
        data: {
            date: '',
            items: '',
            appointments: '',
            editable: true,
        },

        view: function(){
            return /*html*/`
                <div class="col-12" onclick="{{this.props.onclick}}">
                    <div class="row" >
                        <div class="appointment-group">
                            <div class="details">
                                <div class="body">
                                    <div class="title text-muted">${this.date}</div>
                                    ${this.appointments}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }, 
        callback: function(){
            this.ready(()=>{
                let appointments = '';
                this.items.forEach(item=>{
                    appointments += /*html*/ `
                        <appointment-item 
                            id="${item.id}" 
                            name="${item.visitor_name}" 
                            number="${item.visitor_number}" 
                            purpose="${item.purpose}"
                            departmentid="${item.department_id}"
                            executiveid="${item.executive_id}"
                            executive="${item.executive_details.full_name}"
                            status="${item.status}" 
                            starttime="${item.start_time}" 
                            endtime="${item.end_time}" 
                            startday="${item.start_day}" 
                            startmonth="${item.start_month}" 
                            startyear="${item.start_year}" 
                            editable="${this.editable}"
                        ></appointment-item>
                    `
                });

                this.setData({
                    appointments: appointments
                })
            });
        }
    }),
    

}