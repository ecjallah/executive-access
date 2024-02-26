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
    PoliticalParty: new PageLessComponent("political-party", {
        data: {
            classname: '',
            image: '/media/images/political-party.png',
            id: '',
            name: '',
            description: '',
            status: '',
            blocked: '',
            actionicon: 'ellipsis-v'
        },
        props: {
            imageclick: function(event){
                event.stopPropagation();
                console.log('click');
            },

            oncontextclick: function(event){
                event.stopPropagation();
                const parent = this.parentComponent;
                let  details = [];
                if (parent.status == 'approved') {
                    details.push(
                        {
                            text: parent.blocked == 0 ? "Block" : 'Unblock',
                            callback: ()=>{
                                Modal.Confirmation("Confirm Deletion", `PAY ATTENTION! You're about <b class="text-danger">${parent.blocked == 0 ? 'Block' : 'Unblock'}</b> this political party (${parent.name}). This action cannot be undone! Are you sure you want to continue?`).then(()=>{
                                    PageLess.Request({
                                        url: `/api/block-and-unblock-political-party`,
                                        method: "POST",
                                        data: {
                                            party_id: parent.id,
                                            status: parent.blocked == 0 ? 'block' : 'unblock'
                                        },
                                        beforeSend: ()=>{
                                            PageLess.ChangeButtonState(this, '');
                                        }
                                    }, true).then(result=>{
                                        PageLess.RestoreButtonState(this);
                                        if (result.status == 200) {
                                            PageLess.Toast('success', result.message);
                                            parent.setData({
                                                blocked: parent.blocked == 0 ? 1 : 0,
                                            });
                                            this.remove();
                                        } else {
                                            PageLess.Toast('success', result.message, 5000);
                                        }
                                    });
                                }).catch(()=>{});
                            }
                        },
                        {
                            text: "Unapprove",
                            callback: ()=>{
                                Modal.Confirmation("Confirm Deletion", `PAY ATTENTION! You're about <b class="text-danger">Unapproved</b> this political party (${parent.name}). The account will no longer be accessable by the user. This action cannot be undone! Are you sure you want to continue?`).then(()=>{
                                    PageLess.Request({
                                        url: `/api/update-party-approval-status`,
                                        method: "POST",
                                        data: {
                                            party_id: parent.id,
                                            status: 'pending'
                                        },
                                        beforeSend: ()=>{
                                            PageLess.ChangeButtonState(this, '');
                                        }
                                    }, true).then(result=>{
                                        PageLess.RestoreButtonState(this);
                                        if (result.status == 200) {
                                            PageLess.Toast('success', result.message);
                                            parent.setData({
                                                status: 'pending',
                                            });
                                            this.remove();
                                        } else {
                                            PageLess.Toast('success', result.message, 5000);
                                        }
                                    });
                                }).catch(()=>{});
                            }
                        }
                    )
                } else if (parent.status == 'pending') {
                    details.push(
                        {
                            text: "Approve",
                            callback: ()=>{
                                Modal.Confirmation("Confirm Deletion", `PAY ATTENTION! You're about <b class="text-success">APPROVE</b> this political party (${parent.name}). This action cannot be undone! Are you sure you want to continue?`).then(()=>{
                                    PageLess.Request({
                                        url: `/api/update-party-approval-status`,
                                        method: "POST",
                                        data: {
                                            party_id: parent.id,
                                            status: 'approved'
                                        },
                                        beforeSend: ()=>{
                                            PageLess.ChangeButtonState(this, '');
                                        }
                                    }, true).then(result=>{
                                        PageLess.RestoreButtonState(this);
                                        if (result.status == 200) {
                                            PageLess.Toast('success', result.message);
                                            parent.setData({
                                                status: 'approved',
                                            });
                                            this.remove();
                                        } else {
                                            PageLess.Toast('success', result.message, 5000);
                                        }
                                    });
                                }).catch(()=>{});
                            }
                        },
                        {
                            text: "Reject",
                            callback: ()=>{
                                Modal.Confirmation("Confirm Deletion", `PAY ATTENTION! You're about <b class="text-danger">REJECT</b> this political party (${parent.name}). This action cannot be undone! Are you sure you want to continue?`).then(()=>{
                                    PageLess.Request({
                                        url: `/api/update-party-approval-status`,
                                        method: "POST",
                                        data: {
                                            party_id: parent.id,
                                            status: 'rejected'
                                        },
                                        beforeSend: ()=>{
                                            PageLess.ChangeButtonState(this, '');
                                        }
                                    }, true).then(result=>{
                                        PageLess.RestoreButtonState(this);
                                        if (result.status == 200) {
                                            PageLess.Toast('success', result.message);
                                            parent.setData({
                                                status: 'rejected',
                                            });
                                            this.remove();
                                        } else {
                                            PageLess.Toast('success', result.message, 5000);
                                        }
                                    });
                                }).catch(()=>{});
                            }
                        }
                    )
                }
                PageLess.ContextMenu(details, this);
            },
        },
        view: function(){
            return /*html*/`
                <div class="col-12 cursor-pointer" onclick="{{this.props.onclick}}">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-5 main-content-item container-shadow ${this.classname}">
                            <div class="settings-details">
                                <bg-image classname="w-50px h-50px" src="${this.image}" rounded="true" onclick="{{this.props.imageclick}}"></bg-image>
                                <div class="settings-body">
                                    <div class="settings-title text-muted">${this.name}</div>
                                    <div class="settings-details font-weight-bold ${this.status == 'approved' ? 'text-success' : this.status == 'rejected' ? 'text-danger' : 'text-warning'} text-uppercase">${this.status}</div>
                                    ${this.blocked == 1 ? /*html*/ `<div class="settings-details text-danger text-uppercase">Blocked</div>` : ''}
                                </div>
                            </div>
                            <div class="settings-action position-relative d-flex align-items-center">
                                <pageless-button classname="btn btn-circle" text='<i class="fa fa-${this.actionicon}"></i>' onclick="{{this.props.oncontextclick}}"></pageless-button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

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
                                    <text-input value="${parent.firstname}" text="Full Name" icon="user" identity="fullname" required="required"></text-input>
                                    <text-input value="${parent.username}" text="Username" icon="signature" identity="username" required="required"></text-input>
                                    <text-input value="${parent.address}" text="Address" icon="signature" identity="address" required="required"></text-input>
                                    <number-input value="${parent.phoneno}" text="Phone Number" icon="phone-alt" identity="phone-no" required="required"></number-input>
                                    <staff-role-select selectedvalues="${parent.roleid}"></staff-role-select>
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
                                        fullname: values.fullname,
                                        role : values.role,
                                        username: values.username,
                                        address : values.address,
                                        number: values['phone-no']
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

    ToolPollingPlaceSelect : new PageLessComponent("tool-polling-place-select", {
        data: {
            precinctid: '',
            text: "All Polling Places",
            items: [{text: "Loading...", value: ' '}],
            selectedvalue: ""
        },
        props: {
            onpollingplacechange: ()=>{}
        },
        view: function(){
            return /*html*/`
                <div class="month-section tool d-flex flex-nowrap align-items-center" style="background: rgba(0, 0, 0, 0.05) !important;">
                    <i class="fa fa-person-booth text-muted"></i>&nbsp;
                    <native-select onchange="{{this.props.onpollingplacechange}}" placeholder="${this.text}" items='${JSON.stringify(this.items)}' selectedvalue="${this.selectedvalue}" classname="form-select types text-muted" style="border-bottom: 0;"></native-select>
                </div>
            `;
        },
        callback: function(){
            PageLess.Request({
                url: `/api/get-precinct-details/${this.precinctid}`,
                method: "GET"
            }).then(result=>{
                if(result.status == 200){
                    const data  = result.response_body.polling_centers;
                    let options = [];
                    data.forEach(pollingPlace=>{
                        options.push({
                            text: pollingPlace.title,
                            value: pollingPlace.id
                        });
                    });
                    this.setData({
                        items: options
                    });
                } else {
                    this.setData({
                        items: [{text: "No Polling Place Available", value: ''}]
                    });
                }
            });
        }
    }),

    ResultChartFilter : new PageLessComponent("results-chart-filter", {
        data: {
            selectedcounty: '',
            selectedprecinct: '',
            selectedpollingplace: '',
            precinctFilter : '',
            pollingPlaceFilter : '',
        },
        props: {
            oncountychange: function(){
                const filter = this.parentComponents('results-chart-filter');
                const parent = this.parentComponents('results-chart-view');
                const chart  = parent.querySelector('.results-chart');
                const value  = this.value;
                chart.props.updatedata.call(chart, value)

                if (value != '') {
                    filter.setData({
                        selectedcounty: value,
                        precinctFilter: /*html*/ `&emsp;<tool-county-precinct-select selectedvalue="${this.selectedprecinct}" countyid="${value}" onchange="{{this.props.onprecinctchange}}"></tool-county-precinct-select>`,
                        pollingPlaceFilter: ''
                    });
                } else {
                    filter.setData({
                        selectedprecinct: '',
                        selectedpollingplace: '',
                        selectedcounty: '',
                        precinctFilter: '',
                        pollingPlaceFilter: '',
                    })
                }
            },

            onprecinctchange: function(){
                const filter = this.parentComponents('results-chart-filter');
                const parent = this.parentComponents('results-chart-view');
                const chart  = parent.querySelector('.results-chart');
                const value  = this.value;
                chart.props.updatedata.call(chart, filter.selectedcounty, value)

                if (value != '') {
                    filter.setData({
                        selectedprecinct: value,
                        precinctFilter: /*html*/ `&emsp;<tool-county-precinct-select selectedvalue="${value}" countyid="${filter.selectedcounty}" onchange="{{this.props.onprecinctchange}}"></tool-county-precinct-select>`,
                        pollingPlaceFilter: /*html*/ `&emsp;<tool-polling-place-select precinctid="${value}" onchange="{{this.props.onpollingplacechange}}"></tool-polling-place-select>`
                    });
                } else {
                    filter.setData({
                        selectedprecinct: '',
                        pollingPlaceFilter: ''
                    })
                }
            },

            onpollingplacechange: function(){
                const filter = this.parentComponents('results-chart-filter');
                const parent = this.parentComponents('results-chart-view');
                const chart  = parent.querySelector('.results-chart');
                const value  = this.value;
                chart.props.updatedata.call(chart, filter.selectedcounty, filter.selectedprecinct, value)
            },
        },
        view: function(){
            return /*html*/`
                <form class="w-100 d-flex tool-bar mb-2">
                    <tool-county-select selectedvalue="${this.selectedcounty}" text="All Counties" onchange="{{this.props.oncountychange}}"></tool-county-select>
                    ${this.precinctFilter}
                    ${this.pollingPlaceFilter}
                </form>
            `;
        },
    }),

    ResultsChart   : new PageLessComponent("results-chart", {
        data: {
            title: "",
            countyid: "",
            precinctid: "",
            pollingplaceid: "",
            chartdata: {}
        },

        props: {
            generateChartData: function(results, total){
                let chartData = {
                    data: {
                        labels: [],
                        datasets: [{
                            label: `Election Results - Total Votes: ${total}`,
                            data: [],
                            fdata: [],
                            borderWidth: 1,
                            borderColor: ['rgba(0, 0, 255, 0.3)', 'rgba(0, 255, 0, 0.3)'],
                            backgroundColor: ['rgba(0, 0, 255, 0.3)', 'rgba(0, 255, 0, 0.3)'],
                            fill: true,
                        }]
                    },

                    options:{
                        scales: {
                            
                            xAxes: [{
                                display: true,
                                gridLines: {
                                    display: false
                                }
                            }],
                            yAxes: [{
                                display: true,
                                // categoryPercentage: 0.2,
                            }],
                        },
                    }
                };

                results.forEach(index=>{
                    chartData.data.labels.push(index.candidate_info.full_name);
                    chartData.data.datasets[0].data.push(index.vote_value);
                    chartData.data.datasets[0].fdata.push(`${index.vote_value} (${((parseInt(index.vote_value) * 100) / parseInt(total)).toFixed(2)}%)`);
                });

                return chartData;
            },

            updatedata: function(countyId = null, precinctId = null, pollingPlaceId = null){
                PageLess.Request({
                    url: '/api/get-candidate-vote-reports',
                    method: "POST",
                    data: {
                        county_id : countyId != null ? countyId : '',
                        precint_id : precinctId != null ? precinctId : '',
                        polling_center_id : pollingPlaceId != null ? pollingPlaceId : '',
                    },
                }, true).then(result=>{
                    if (result.status == 200) {
                        this.props.updatechart.call(this, result.response_body);
                    } else {
                        
                    }
                });
            }, 

            updatechart: function(data){
                let chartData = this.props.generateChartData(data.candidates, data.total_vote.vote_value);
                this.setData({
                    chartdata: chartData
                });
            }
        },
        view: function(){
            return /*html*/`
                <div class="w-100 main-content-item border-radius-10 results-chart">
                    <custom-chart classname="h-300px" type="bar" chartdata='${JSON.stringify(this.chartdata)}'></custom-chart>
                </div>
                
            `;
        },
        callback: function(){
            this.ready(()=>{
                this.props.updatedata.call(this, this.countyid, this.precinctid, this.pollingplaceid)
            });
        }
    }),

    ResultsChartView   : new PageLessComponent("results-chart-view", {
        data: {
            title: "Elections Results",
        },
        view: function(){
            return /*html*/`
                <div class="w-100 d-flex flex-wrap m-0 p-2 p-md-3 p-xl-4 mt-2">
                    <div class="w-100 d-flex px-2 px-md-3 px-xl-4 justify-content-between align-items-center">
                        <div class="font-weight-bold preloader preload-25 text-muted">${this.title}</div>
                    </div>
                    <div class="w-100 p-2 p-md-3 ">
                        <div class="w-100 d-flex flex-wrap bg-white border-radius-10" style="padding: 0 !important;">
                            <div class="w-100">
                                <div class="w-100 d-flex flex-wrap p-3 p-lg-2 p-xl-3 justify-content-center">
                                    <results-chart-filter></results-chart-filter>
                                    <results-chart></results-chart>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    NoSubscription : new PageLessComponent("no-subscription-content", {
        data: {
            
        },
        props: {
            onsubscribe: function(){
                Modal.BuildForm({
                    title: "Choose A Provider",
                    icon: "user-cog",
                    description: ``,
                    inputs: /*html*/ `
                        <vertical-scroll-view preloader="card" nodataicon="user-cog" preloadercount="6" onload="{{this.props.onload}}"></vertical-scroll-view>
                    `,
                    // inputs: /*html*/ `
                    //     <div class="row">
                    //         <selectable-service-provider-card name="Squid Garbage Collector" address="10th Street, Sinkor" phoneno="+231 777 142 785"></selectable-service-provider-card>
                    //         <selectable-service-provider-card name="Easy Waste" address="Omega, Paynesville" phoneno="+231 888 587 896"></selectable-service-provider-card>
                    //     </div>
                    // `,
                    noSubmit: true,
                    closable: false,
                    autoClose: false,
                    props: {
                        onload: function(){
                            return new Promise(resolve=>{
                                this.setRequest({
                                    url: `/api/get-all-vaild-service-providers`,
                                    method: "GET",
                                });
            
                                this.setChild(data=>{
                                    return /*html*/ `<selectable-service-provider-card userid="${data.user_id}" name="${data.full_name}" address="" phoneno="+231 777 142 785" image="${data.image}"></selectable-service-provider-card>`;
                                });

                                resolve();
                            });
                        }
                    }
                }, values=>{
                    //submit handler
                });
            }
        },
        view: function(){
            return /*html*/`
                <div class="flex-1 w-100 p-0 p-sm-1 p-md-3 p-xl-4 scroll-y ">
                    <div class="w-100 h-100 d-flex flex-column align-content-center justify-content-center align-items-center">
                        <div class="row justify-content-center">
                            <img class="col-10 col-sm-9 col-md-6" style="height: auto;" alt="waste-art" src="/media/images/waste-art-1.png">
                            <div class="w-100 text-center h6 mt-3">No Subscription Yet</div>
                            <div class="col-10 col-sm-9 col-md-6 text-center mt-2">
                                You haven't subscribed to any waste management service yet
                                Please subscribe to a waste managment service to make the most out of MyWaste service
                            </div>
                            <div class="w-100 d-flex justify-content-center mt-3">
                                <pageless-button type="button" classname="btn btn-success col-5 col-lg-4 col-xl-3" text="Subscribe" onclick="{{this.props.onsubscribe}}"></pageless-button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        },
        callback: function(){
            
        }
    }),

    ToolSearch : new PageLessComponent("tool-search-input", {
        data: {
            text: "Search",
        },
        props: {
            onsearchclick: function(){
                let value = this.parentComponent.querySelector('input').value;
                if (value != '') {
                    this.parentComponent.querySelector('.clear-container').classList.remove('display-none');
                }
            },
            
            onsearchclear: function(){
                this.parentComponent.querySelector('input').value = '';
                this.parentComponent.querySelector('.clear-container').classList.add('display-none');
            }
        },
        view: function(){
            return /*html*/`
                <form class="d-flex ml-2">
                    <div class="search-inputs-container">
                        <div class="clear-container display-none">
                            <pageless-button type="button" class="btn-circle clear-button" onclick="{{this.props.onsearchclear}}" text='<i class="fa fa-times"></i>'></pageless-button>
                        </div>
                        <div class="input">
                            <input class="search-input" type="text" placeholder="${this.text}" name="search-value"/>
                        </div>
                        <div class="search-icon">
                            <pageless-button type="submit" class="btn-circle search-button" text='<i class="fa fa-search"></i>' onclick="{{this.props.onsearchclick}}"></pageless-button>
                        </div>
                    </div>
                </form>
            `;
        }
    }),

    BeneficiaryDetails : new PageLessComponent("beneficiary-details", {
        data: {
            id: "",
            businessid: '',
            packageid: '',
            fullname: '',
            percent: '0',
            proportion: '',
            remainingvisits: '',
            visithistory: {},
            visitdesc: '',
            mode: '',
            detailsitems: /*html*/`
                <list-view-preloader></list-view-preloader>
                <list-view-preloader></list-view-preloader>
                <list-view-preloader></list-view-preloader>
                <list-view-preloader></list-view-preloader>
                <list-view-preloader></list-view-preloader>
            `,
            dimension: 180
        },
        props: {
            checkin: function(){
                let parent = this.parentComponent;
                if (parent.mode == 'checked-in') {
                    PageLess.Toast('danger', `${parent.fullname} has already been checked in`);
                } else {
                    if (parseInt(parent.remainingvisits) > 0 ) {
                        Modal.Confirmation("Confirm Action", `You're about to check-in ${parent.fullname}. The will be deducted from the beneficiary's or employee's available visits. This cannot be undone! Are you sure you want to continue?`).then(()=>{
                            PageLess.Request({
                                url: `/api/check-empoyee-visit-in/${parent.id}`,
                                method: "GET",
                                beforeSend: ()=>{
                                    PageLess.ChangeButtonState(this, 'Checking In');
                                }
                            }).then(result=>{
                                PageLess.RestoreButtonState(this);
                                if (result.status == 200) {
                                    PageLess.Toast('success', result.message, 10000);
                                    parent.setData({
                                        mode: 'checked-in',
                                    }).refresh(true);
                                } else {
                                    PageLess.Toast('danger', result.message, 7000);
                                }

                            });
                        });
                    } else {
                        PageLess.Toast('danger', `SORRY! <b>${parent.fullname}</b> isn't allowed to check-in. They have exhauseted all their available visits`, 7000);
                    }
                }
            },

            viewhistory: function(){
                let hospitals = ``;
                let parent    = this.parentComponent;
                if (parent.visithistory != 404) {
                    let visitGroups = parent.visithistory.visit_details;
                    for (const date in visitGroups) {
                        if (Object.hasOwnProperty.call(visitGroups, date)) {
                            const details = visitGroups[date];
                            hospitals    += /*html*/ `
                                <visit-history-item title="${date}" details='${JSON.stringify(details)}'></visit-history-item>
                            `;
                        }
                    }
                    Modal.BuildForm({
                        title: `${parent.fullname} Visit History`,
                        icon: "history",
                        inputs: /*html*/ `
                            <div class="w-100 d-flex flex-wrap align-content-start">
                                ${hospitals}
                            
                            </div>
                        `,
                        noSubmit: true,
                        closable: true,
                        autoClose: false,
                    });
                } else {
                    PageLess.Toast('danger', `There's no history available. ${parent.fullname} has not visited any registered health care provider up to this point. `, 7000);
                }
            }
        },
        view: function(){
            return /*html*/`
                <div class="flex-1 w-100 p-0 p-sm-1 p-md-3 p-xl-4 scroll-y">
                    <div class="w-100 d-flex justify-content-center flex-wrap no-gutters flex-column align-items-center">
                        <div class="col-12 d-flex justify-content-center py-3">
                            <div class="w-${this.dimension}px h-${this.dimension}px position-relative d-flex justify-content-center align-items-center">
                                <circular-progress percent="${this.percent}" width="${this.dimension}" height="${this.dimension}" strokewidth="0" strokecolor="#dc3545"></circular-progress>
                                <div class="center position-absolute w-${this.dimension - 20}px h-${this.dimension - 20}px d-flex justify-content-center align-items-center border-radius-rounded bg-base flex-column">
                                    <span class="h3 font-weight-bold text-preloader preload-50">${this.proportion}</span>
                                    <span class="text-preloader preload-25">${this.visitdesc}</span>
                                </div>
                            </div>
                        </div>
                        ${this.mode == 'checkin' || this.mode == 'checked-in' ? /*html*/ `<div class="w-100 text-center my-3" ${this.mode== 'checked-in' ? 'disabled="disabled"' : ''}><pageless-button classname="btn btn-danger px-5" text="Check In" onclick="{{this.props.checkin}}"></pageless-button></div>` : ''}
                        <div class="w-100 text-center h5 mt-3 text-preloader preload-50">${this.fullname}</div>
                        <div class="col-11 col-sm-9 col-md-6 text-center mt-2">
                            <div class="w-100 d-flex list-items-container flex-wrap">
                                ${this.detailsitems}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        },
        callback: function(){
            this.ready(()=>{
                PageLess.Request({
                    url: `/api/lookup-${this.mode == 'checkin' || this.mode == 'checked-in' ? 'employee' : 'beneficiary'}-visit-info/${this.id}/${this.packageid}/${this.businessid}`,
                    method: "GET",
                }).then(result=>{
                    if (result.status == 200) {
                        let visitData = result.response_body;
                        let data      = visitData.beneficiary_personal_info;
                        let activeVisitCount   = parseInt(visitData.beneficiary_visited_amount);
                        let activeVisitBalance = visitData.beneficiary_balance_visit_amount;
                        let interval           = visitData.package_interval;
                        let intervalValue      = parseInt(visitData.interval_int_value);
                        let visitCap           = parseInt(visitData.current_visit_cap);
                        let percentage         = (activeVisitCount * 100 ) / visitCap;
                        let proportion         = `${activeVisitCount}/${visitCap}`;
                        let visitHistory       = visitData.beneficiary_visit_details;
                        let lastVisitDate      = visitHistory != 404 ? visitHistory.last_visit_date : 'Not Available';
                        let lastHospitalVisted = visitHistory != 404 ? visitHistory.last_hospital_visited : 'Not Available';

                        this.setData({
                            fullname: data.full_name,
                            percent: percentage == 100 ? 99.99 : percentage,
                            proportion: proportion,
                            visitdesc: 'Visits',
                            remainingvisits: activeVisitBalance,
                            visithistory: visitHistory,
                            detailsitems: /*html*/ `
                                <list-item icon="id-card text-mw-primary" title="${data.employee_id}" description="Employee ID" actionicon=""></list-item>
                                <list-item icon="venus-mars text-mw-primary" title="<span class='text-capitalize'>${data.sex}</span>" description="Gender" actionicon=""></list-item>
                                <list-item icon="calendar text-mw-primary" title="${lastVisitDate}" description="Last Visit" actionicon=""></list-item>
                                <list-item icon="hospital text-mw-primary" title="${lastHospitalVisted}" description="Last Hospital Visited" actionicon=""></list-item>
                                <list-item icon="history text-mw-primary" description="View Full Visit History" actionicon="angle-right" onclick="{{this.props.viewhistory}}"></list-item>
                            `
                        });
                    } else {
                        (new Widget("/not-found")).route().then(()=>this.remove());
                    }
                });
            });
        }
    }),

    VistoryHistoryItem : new PageLessComponent("visit-history-item", {
        data: {
            title: "",
            details: [],
            hospitals: ``,
            state: false
        },
        props: {
            showhospitals: function(){
                let parent    = this.parentComponent;
                let container = parent.querySelector('.hospitals-container');
                
                this.style.transform = parent.state == false ?  "rotateX(-180deg)" : "rotateX(0deg)";
                container.classList.toggle(`h-${container.scrollHeight}px`);
                container.classList.toggle('h-1px');
                parent.state = !parent.state; 
            },
        },
        view: function(){
            let hospitals = '';
            this.details.forEach(hospital =>{
                hospitals += /*html*/ `
                    <list-item title="${hospital.hospital_name}" description="${hospital.date_of_visit}" actionicon=""></list-item>
                `;
            });
            return /*html*/`
                <div class="col-12">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-2 main-content-item container-shadow ${this.classname}">
                            <div class="settings-details">
                                <div class="settings-icon-container"><span><i class="fad fa-calendar-alt text-danger fa-swap-opacity"></i> </span></div>
                                <div class="settings-body">
                                    <div class="settings-title text-muted text-left">${this.title}</div>
                                </div>
                            </div>
                            <div class="settings-action position-relative">
                                <pageless-button class="btn-circle" text='<i class="fa text-muted fa-2x fa-angle-down"></i>' onclick="{{this.props.showhospitals}}"></pageless-button>
                            </div>
                        </div>
                        <div class="col-12 hospitals-container h-1px list-items-container bg-base" style="transition: all .5s ease-in-out !important; overflow: hidden;">
                            <div class="row p-1">${hospitals}</div>
                        </div>
                    </div>
                </div>
            `;
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
                        <span class="sidebar-menu-item-icon" title="${this.title}"><i class="fal fa-lg fa-${this.icon}"></i></span>
                        <span class="sidebar-menu-item-desc">&emsp;${this.text}</span>
                    </div>
                </li>
            `;
        }
    }),

    sidebar : new PageLessComponent("side-bar", {
        data: {
            title: 'RERM',
            userimage: '/media/images/user-image-placeholder.png',
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
                                <div class="app-logo w-33px h-33px border-radius-5 bg-mw-primary"></div>
                                <div class="app-name">${this.title}</div>
                            </div>
                        </div>

                        <ul class="sidebar-menu-container p-relative">
                            <div class="w-100 d-flex pl-3 pr-3 align-items-center py-3">
                                <div class="user-profile-img-container d-flex justify-content-center align-items-center">
                                    <bg-image classname=" w-50px h-50px d-flex justify-content-center align-items-center" rounded="true" src="${this.userimage}"></bg-image>
                                </div>

                                <div class="flex-1">
                                    <div class="w-100 justify-content-left p-2">
                                        <span class="text-white">${this.username}</span>
                                    </div>
                                </div>
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
                            userimage: data.full_image != "" ? data.full_image : this.userimage,
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
                            options: [{text: 'No role have been added', value: "..."}]
                        });
                    }
                });
            });
        }
    }),
    
    InsurancePackageSelect : new PageLessComponent("insurance-package-select", {
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
                        identity="insurance-package"
                        icon="lock"
                        text="Insurance Package"
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
                    url: `/api/view-all-packages`,
                    method: "GET",
                }).then(result=>{
                    if(result.status == 200){
                        let data    = result.response_body;
                        let options = [];
                        data.forEach(packageDetails=>{
                            options.push({
                                text: packageDetails.title,
                                value: packageDetails.id
                            });
                        });
                        this.setData({
                            options: options
                        });
                    } else {
                        this.setData({
                            options: [{text: 'No package have been added', value: "..."}]
                        });
                    }
                });
            });
        }
    }),

    PoliticalIssue: new PageLessComponent("political-issue", {
        data: {
            classname: '',
            title: '',
            description: '',
            base: 100,
            total: "",
            actionicon: 'ellipsis-v'
        },
        props: {
        },
        view: function(){
            return /*html*/`
                <div class="col-12" onclick="{{this.props.onclick}}">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-5 main-content-item container-shadow ${this.classname}">
                            <div class="settings-details">
                                <div class="settings-body">
                                    <div class="settings-title text-muted">${this.title}</div>
                                    <div class="settings-details text-muted">${this.description}</div>
                                </div>
                            </div>
                            <div class="settings-action w-auto">
                                <points-allocator min="${this.base}" max="${this.total}"></points-allocator>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    EditablePoliticalIssue : new PageLessComponent("editable-political-issue", {
        data: {
            id: "",
            title: "",
            description: "",
            base: ""
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
                                title: "Update Issue",
                                icon: "question-square",
                                description: ``,
                                inputs: /*html*/ `
                                    <text-input value="${parent.title}" text="Title" icon="key" identity="title" required="required"></text-input>
                                    <long-text-input value="${parent.description}"  text="Description" identity="description" icon="align-justify" required="required"></long-text-input>
                                    <text-input value="${parent.base}" text="Base Point" icon="star" identity="base" required="required"></text-input>
                                `,
                                submitText: "Update",
                                closeText: 'Cancel',
                                closable: false,
                                autoClose: false,
                            }, values=>{
                                PageLess.Request({
                                    url: `/api/update-issue`,
                                    method: "POST",
                                    data: {
                                        issue_id: parent.id,
                                        title: values.title, 
                                        description: values.description,
                                        base_value: values.base
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
                                            title: values.title,
                                            description: values.description, 
                                            base: values.base
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
                                    url: `/api/remove-issue`,
                                    method: "POST",
                                    data: {
                                        issue_id: parent.id
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
                <div class="col-12">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-6 main-content-item container-shadow">
                            <div class="settings-details">
                                <div class="settings-body">
                                    <div class="settings-title text-muted">${this.title}</div>
                                    <div class="settings-details text-muted">${this.description}</div>
                                    <div class="settings-details text-muted">Base Point(s): ${this.base}</div>
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

    ElectionItem : new PageLessComponent("election-item", {
        data: {
            id: "",
            title: "",
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
                                title: "Update Election",
                                icon: "box-ballot",
                                description: ``,
                                inputs: /*html*/ `
                                    <text-input value="${parent.title}" text="Title/Name" icon="key" identity="title" required="required"></text-input>
                                `,
                                submitText: "Update",
                                closeText: 'Cancel',
                                closable: false,
                                autoClose: false,
                            }, values=>{
                                PageLess.Request({
                                    url: `/api/update-election`,
                                    method: "POST",
                                    data: {
                                        election_id: parent.id,
                                        election_title: values.title
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
                                            title: values.title,
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
                                    url: `/api/remove-election`,
                                    method: "POST",
                                    data: {
                                        election_id: parent.id
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
                <div class="col-12">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-6 main-content-item container-shadow">
                            <div class="settings-details">
                                <div class="settings-body">
                                    <div class="settings-title text-muted">${this.title}</div>
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

    PositionSelect : new PageLessComponent("position-select", {
        data: {
            userid: '',
            selectedvalue: '',
            options: [
                {text: 'President', value: 'President'},
                {text: 'Vice President', value: 'Vice President'},
                {text: 'Senator', value: 'Senator'},
                {text: 'Representative', value: 'Representative'}
            ],
        },
        view: function(){
            return /*html*/`
                <div>
                    <custom-select
                        identity="position"
                        icon="user-tag"
                        text="Choose Political Position"
                        items='${JSON.stringify(this.options)}'
                        required="required"
                        selectedvalue="${this.selectedvalue}"
                    ></custom-select>
                </div>
            `;
        }
    }),

    ElectionsSelect : new PageLessComponent("election-select", {
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
                        identity="election"
                        icon="box-ballot"
                        text="Choose Election"
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
                    url: `/api/view-public-elections`,
                    method: "GET",
                }).then(result=>{
                    if(result.status == 200){
                        let data    = result.response_body;
                        let options = [];
                        data.forEach(election=>{
                            options.push({
                                text: election.title,
                                value: election.id
                            });
                        });
                        this.setData({
                            options: options
                        });
                    } else {
                        this.setData({
                            options: [{text: 'No election has been added', value: "..."}]
                        });
                    }
                });
            });
        }
    }),

    EditablePoliticalCandidate: new PageLessComponent("political-candidate", {
        data: {
            image: '/media/images/candidate.png',
            fullname: '',
            firstname: "",
            middlename: "",
            lastname: "",
            position: "",
            electionid: "",
            county: "",
            actionicon: 'ellipsis-v'
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
                                title: "Candidate Details",
                                icon: "user-tie",
                                description: ``,
                                inputs: /*html*/ `
                                    <text-input value="${parent.firstname}" icon="user" text="First Name" identity="firstname" required="required"></text-input>
                                    <text-input value="${parent.middlename}" icon="user" text="Middle Name" identity="middlename"></text-input>
                                    <text-input value="${parent.lastname}" icon="user" text="Last Name" identity="lastname" required="required"></text-input>
                                    <position-select selectedvalue="${parent.position}"></position-select>
                                    <election-select selectedvalue="${parent.electionid}"></election-select>
                                    <county-select selectedvalue="${parent.county}" required="required"></county-select>
                                `,
                                submitText: "Update",
                                closeText: 'Close',
                                closable: false,
                                autoClose: false,
                            }, values=>{
                                PageLess.Request({
                                    url: `/api/edit-candidate`,
                                    method: "POST",
                                    data: {
                                        candidate_id: parent.id,
                                        first_name: values.firstname,
                                        middle_name: values.middlename,
                                        last_name: values.lastname,
                                        position: values.position,
                                        election_type_id: values.election,
                                        county: values.county,
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
                                            fullname: `${values.firstname} ${values.middlename} ${values.lastname}`,
                                            firstname: values.firstname,
                                            middlename: values.middlename,
                                            lastname: values.lastname,
                                            position: values.position,
                                            electionid: values.election,
                                            county: values.county,
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
                                    url: `/api/remove-candidate-from-list`,
                                    method: "POST",
                                    data: {
                                        candidate_id: parent.id
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
                                <bg-image classname="w-50px h-50px" src="${this.image}" rounded="true"></bg-image>
                                <div class="settings-body">
                                    <div class="settings-title text-muted">${this.fullname}</div>
                                    <div class="settings-details text-muted">${this.position}</div>
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

    RemainingPoints: new PageLessComponent("remaining-points", {
        data: {
            points: '',
        },
        props: {
            reduce: function(reduceBy){
                this.parentComponent.totalbudget = parseFloat(this.parentComponent.totalbudget) - parseFloat(reduceBy);
                let newPoints = this.parentComponent.totalbudget
                this.setData({
                    points: newPoints
                })
            },

            increase: function(increaseBy){
                this.parentComponent.totalbudget = parseFloat(this.parentComponent.totalbudget) + parseFloat(increaseBy);
                let newPoints = this.parentComponent.totalbudget
                this.setData({
                    points: newPoints
                })
            }
        },
        view: function(){
            return /*html*/`
                <div class="remaining-points h6 w-100 text-center my-3 font-weight-bold text-dark">
                    Remaining Budget(s): $${parseInt(this.points).toLocaleString()}
                </div>
            `;
        },
        callback: function(){
            
        }
    }),

    // stepped Number Input Component 
    PointAllocator: new PageLessComponent('points-allocator', {
        data: {
            identity: 'point-allocator',
            enabled: true,
            icon: null,
            text: null,
            value: 0,
            attributes: null,
            min: 1,
            max: null,
            required: null,
            description: '',
            autocomplete: 'off'
        },

        props: {
            increment: function(event){
                event.stopPropagation();
                let parent     = this.parentComponent;
                let input      = parent.querySelector('input');
                let max        = parseInt(parent.max);
                let min        = parseInt(parent.min);
                let rPoints    = parent.props.getremaining.call(parent);
                let newRPoints = rPoints - min; 
                let newValue   = parseInt(parent.value) + min;
                if (rPoints > 0 && newRPoints >= 0 && parent.value < max && newValue <= max) {
                    parent.value = newValue;
                    input.value  = `$${parent.value.toLocaleString()}`;
                    parent.props.reduceremaining.call(parent, min);
                } else {
                    PageLess.Toast('warning', `The allocated amount cannot be greater than $${max.toLocaleString()}`);
                }
            },

            decrement: function(event){
                event.stopPropagation();
                let parent   = this.parentComponent;
                let input    = parent.querySelector('input');
                let min      = parseInt(parent.min);
                if (parent.value > 0) {
                    parent.value = parseInt(parent.value) - min; 
                    input.value  = `$${parent.value.toLocaleString()}`;
                    parent.props.increaseremaining.call(parent, min);
                } else {
                    PageLess.Toast('warning', `The allocated amount cannot be less than 0`);
                }
            },

            reduceremaining: function(value){
                let parent       = this.parentComponents('political-priorities');
                let remainingObj = parent.querySelector('.remaining-points')
                remainingObj.props.reduce.call(remainingObj, value);
            },

            increaseremaining: function(value){
                let parent       = this.parentComponents('political-priorities');
                let remainingObj = parent.querySelector('.remaining-points')
                remainingObj.props.increase.call(remainingObj, value);
            },

            getremaining: function(){
                let parent       = this.parentComponents('political-priorities');
                return parseFloat(parent.totalbudget);
            }
        },
        
        view: function(){
            return /*html*/`
                <div class="form-content-row d-flex align-items-center h-40px point-allocator" style="padding: 0 !important; margin-bottom: 0 !important">
                    ${this.enabled === true ? /*html*/ `<pageless-button type="button" classname="btn-clean container-shadow border-radius-5 w-30px h-100" text="<i class='fa fa-minus'></i>" onclick="{{this.props.decrement}}"></pageless-button>&nbsp;` : ''}
                    <div class="form-input-container">
                        <input
                            type="text" 
                            class="form-input w-100px text-center"
                            name="${this.identity}" 
                            placeholder=" " 
                            pattern="[0-9\,?]+\.?([0-9]+)?" 
                            title="Enter numbers only. Eg: 1.00"
                            disabled="disabled"
                            value="$${this.value}" ${this.attributes}
                        >
                    </div>  
                    ${this.enabled === true ? /*html*/ `<pageless-button type="button" classname="btn-clean container-shadow border-radius-5 w-30px h-100" text="<i class='fa fa-plus'></i>" onclick="{{this.props.increment}}"></pageless-button>` : ''}
                 </div>
            `;
        }
    }),

    PoliticalIssuePriority: new PageLessComponent("political-issue-priority", {
        data: {
            id: '',
            editable: true,
            classname: '',
            title: '',
            description: '',
            base: 100,
            value: 0,
            total: "",
            actionicon: 'ellipsis-v'
        },
        props: {
        },
        view: function(){
            return /*html*/`
                <div class="col-12" onclick="{{this.props.onclick}}">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-5 main-content-item container-shadow ${this.classname}">
                            <div class="settings-details">
                                <div class="settings-body">
                                    <div class="settings-title text-muted">${this.title}</div>
                                    <div class="settings-details text-muted">${this.description}</div>
                                    <div class="settings-details text-danger small">Unit: $${parseFloat(this.base).toLocaleString()}</div>
                                </div>
                            </div>
                            <div class="settings-action w-auto">
                                <points-allocator min="${this.base}" max="${this.total}" value="${this.value}" ${this.editable !== true ? 'enabled="false"' : ''}></points-allocator>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    PoliticalPriorities: new PageLessComponent("political-priorities", {
        data: {
            totalbudget: 1000,
            notice: '',
            budgettext: '',
            remainintpoints: '',
            issues: /*html*/ `
                <div class="list-items-container py-3">
                    <list-view-preloader></list-view-preloader>
                    <list-view-preloader></list-view-preloader>
                    <list-view-preloader></list-view-preloader>
                    <list-view-preloader></list-view-preloader>
                </div>
            `,
            savebutton: '',
        },
        props: {
            onload: function(){
                return new Promise(resolve => {
                    this.setRequest({
                        url: `/api/get-party-issues-priorities`,
                        method: "GET",
                    });
    
                    this.mapData(data=>{
                        return this.unset == '1' ? data.issues : data.priorities;
                    });
    
                    this.setChild(data=>{
                        return this.unset == '1' ? /*html*/ `<political-issue-priority id="${data.id}" total="${this.total}" title="${data.issue_title}" description="${data.description}" base="${data.base_value}"></political-issue-priority>` : /*html*/ `<political-issue-priority id="${data.id}" total="${this.total}" title="${data.issue_details.issue_title}" description="${data.issue_details.description}" base="${data.issue_details.base_value}" value="${parseFloat(data.money_allocated).toLocaleString()}" editable="false"></political-issue-priority>`;
                    });
    
                    resolve();
                });
            },

            onsubmit: function(event){
                event.preventDefault();
                let submitBtn = this.querySelector('button[type=submit]');
                Modal.Confirmation('Confirm Action', `PAY ATTENTION! Please make sure you've verified all the amount you allocated before Continue. This cannot be undone; you won't be able to make changes once this is saved. Are you sure you want to continue`).then(()=>{
                    let allocators = this.querySelectorAll('.point-allocator');
                    let priorities = [];
                    let total      = 0;
                    allocators.forEach(allocator=>{
                        total += parseFloat(allocator.value);
                        priorities.push({
                            id: allocator.parentComponent.id,
                            value: allocator.value
                        })
                    });

                    if (total > 0) {
                        PageLess.Request({
                            url: `/api/set-party-issues-priorities`,
                            method: "POST",
                            data: {
                                details: priorities
                            },
                            beforeSend: ()=>{
                                PageLess.ChangeButtonState(submitBtn, "Saving");
                            }
                        }, true).then(result=>{
                            PageLess.RestoreButtonState(submitBtn);
                            if (result.status == 200) {
                                PageLess.Toast('success', result.message);
                                this.refresh(true);
                            } else {
                                PageLess.Toast('danger', result.message, 5000);
                            }
                        });
                    } else{
                        PageLess.Toast('danger', "You need to prioritize at lease one sector")
                    }
                });

                
            }
        },
        view: function(){
            return /*html*/`
                <form class="main-content-body" onsubmit="{{this.props.onsubmit}}">
                    <div class="w-100 d-flex m-0 p-0 h-100 flex-column align-items-center justify-content-centert">
                        <div class="w-100 d-flex flex-column align-items-center align-content-start no-gutters">
                            <div class="col-12 col-sm-10 col-md-9 flex-1 d-flex flex-column">
                                <div class=" text-left px-1 px-sm-1 px-md-3 px-xl-5 pt-5">
                                    <div class="w-100 description p-2 pt-4 h5 text-center font-weight-bold text-preloader prelaod-50">${this.budgettext}</div>
                                    <div class="p-2 text-preloader preload-100">${this.notice}</div>
                                </div>
                                ${this.issues}
                                ${this.remainintpoints}
                            </div>
                            <div class="w-100 d-flex justify-content-center mt-3 text-preloader preload-25">${this.savebutton}</div>
                        </div>
                    </div>
                </form>
            `;
        },
        callback: function(){
            this.ready(()=>{
                PageLess.Request({
                    url: `/api/get-party-issues-priorities`,
                    method: "GET",
                }).then(result=>{
                    if (result.status == 200) {
                        let data   = result.response_body;
                        let issues = '';
                        if (data.issues != undefined) {
                            issues = /*html*/ `<vertical-scroll-view unset="1" total="${data.total_dollar_value}" nodataicon="fa-key" preloader="list" onload="{{this.props.onload}}" scrollable="false"></vertical-scroll-view>`
                        }else{
                            issues = /*html*/ `<vertical-scroll-view unset="0" nodataicon="fa-key" preloader="list" onload="{{this.props.onload}}" scrollable="false"></vertical-scroll-view>`
                        }

                        this.setData({
                            totalbudget: data.total_dollar_value,
                            notice: data.issues != undefined ? "Consider you're given the above budget for operation. From a point of prioritization, allocate the amount you deem neccessary to the list of sectors below" : "Considering the above given budget for operation, below are the sectors with you've prioritized, along with the allocated amount which you deemed neccessary",
                            budgettext: data.issues != undefined ? /*html*/ `Budget: $${parseFloat(data.total_dollar_value).toLocaleString()}` : 'Prioritized Sectors',
                            remainintpoints: data.issues != undefined ? /*html*/ `<remaining-points points="${data.total_dollar_value}"></remaining-points>` : '',
                            issues: issues,
                            savebutton:  data.issues != undefined ? /*html*/`<pageless-button type="submit" classname="btn btn-primary px-4 col-5 col-lg-4 col-xl-3" text="Save" onclick="{{this.props.onsave}}"></pageless-button>` : ' ',
                        });
                    } else {

                    }
                });
            });
        }
    }),

    PrecinctItem: new PageLessComponent("precinct-item", {
        data: {
            id: '',
            name: '',
            code: '',
            county: '',
            countyid: '',
            actionicon: 'ellipsis-v'
        },
        props: {
            onpollingplaceclick: function(){
                (new PageLess(`/precincts/${this.parentComponent.id}/polling-places`)).route();
            },
            oncontextclick: function(event){
                event.stopPropagation();
                let parent = this.parentComponent;
                PageLess.ContextMenu([
                    {
                        text: "Edit",
                        callback: ()=>{
                            
                            Modal.BuildForm({
                                title: "Update Precinct",
                                icon: "map-marker-edit",
                                description: ``,
                                inputs: /*html*/ `
                                    <text-input value="${parent.name}" icon="map-marker" text="Precinct Name" identity="name" required="required"></text-input>
                                    <text-input value="${parent.code}" icon="hashtag" text="Precinct Code" identity="code" required="required"></text-input>
                                    <county-select selectedvalue="${parent.countyid}"></county-select>
                                `,
                                submitText: "Update",
                                closeText: 'Cancel',
                                closable: false,
                                autoClose: false,
                            }, values=>{
                                PageLess.Request({
                                    url: `/api/update-precincts`,
                                    method: "POST",
                                    data: {
                                        id: parent.id,
                                        title: values.name,
                                        code: values.code,
                                        county_id: values.county,
                                    },
                                    beforeSend: ()=>{
                                        PageLess.ChangeButtonState(values.submitBtn);
                                    }
                                }, true).then(result=>{
                                    PageLess.RestoreButtonState(values.submitBtn);
                                    if (result.status == 200) {
                                        const countySelect = values.modal.querySelector(".county");
                                        const newCounty    = countySelect.options[county.options.selectedIndex].text
                                        PageLess.Toast('success', result.message);
                                        Modal.Close(values.modal);
                                        parent.setData({
                                            name: values.name,
                                            title: values.name,
                                            code: values.code,
                                            county_id: values.county,
                                            county: newCounty
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
                                    url: `/api/remove-precinct`,
                                    method: "POST",
                                    data: {
                                        id: parent.id
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
                                    <div class="text-muted small">Code: ${this.code}</div>
                                    <div class="text-muted small">County: ${this.county} </div>
                                </div>
                            </div>
                            <div class="settings-action position-relative d-flex align-items-center">
                                <pageless-button class="btn btn-lignt" text='Polling Places' onclick="{{this.props.onpollingplaceclick}}"></pageless-button>&emsp;
                                <pageless-button class="btn-circle" text='<i class="fa text-muted fa-lg fa-ellipsis-v"></i>' onclick="{{this.props.oncontextclick}}"></pageless-button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    PollingPlaceItem: new PageLessComponent("polling-place-item", {
        data: {
            id: '',
            name: '',
            code: '',
            precinctid: '',
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
                                title: "Update Polling Place",
                                icon: "map-marker-edit",
                                description: ``,
                                inputs: /*html*/ `
                                    <text-input value="${parent.name}" icon="user" text="Polling Place Name" identity="name" required="required"></text-input>
                                    <text-input value="${parent.code}" icon="hashtag" text="Polling Place Code" identity="code" required="required"></text-input>
                                `,
                                submitText: "Update",
                                closeText: 'Cancel',
                                closable: false,
                                autoClose: false,
                            }, values=>{
                                PageLess.Request({
                                    url: `/api/update-polling-center`,
                                    method: "POST",
                                    data: {
                                        id: parent.id,
                                        precinct_id: parent.precinctid,
                                        title: values.name,
                                        code: values.code,
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
                                            code: values.code,
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
                                    url: `/api/remove-polling-center`,
                                    method: "POST",
                                    data: {
                                        id: parent.id
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
                                    <div class="text-muted small">Code: ${this.code}</div>
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

    StaffAssignmentCounter: new PageLessComponent("staff-assignment-counter", {
        data: {
            text: "",
        },
        view: function(){
            return /*html*/`
                <div class="small text-muted text-preloader preload-25">${this.text}</div>
            `;
        },
        callback: function(){
            this.ready(()=>{
                PageLess.Request({
                    url: `api/get-watcher-assigned-polling-centers-counts`,
                    method: "GET",
                }).then(result=>{
                    if (result.status == 200) {
                        const data = result.response_body
                        this.setData({
                            text: `Polling Places Assigned: <span class='text-danger'>${data.watcher_assigned_count}</span>`
                        });
                    }
                });
            });
        }
    }),

    StaffListItem: new PageLessComponent("staff-list-item", {
        data: {
            classname: '',
            image: '',
            defaultimage: '/media/images/user-image-placeholder.png',
            id: '',
            fullname: '',
            role: '',
            assignmentcount: '0',
            actionicon: 'angle-right'
        },
        props: {
            onclick: function(){
                (new PageLess(`/polling-places/${this.id}/assignment`)).route()
            }
        },
        view: function(){
            return /*html*/`
                <div class="col-12 cursor-pointer" onclick="{{this.props.onclick}}">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-5 main-content-item container-shadow ${this.classname}">
                            <div class="settings-details">
                                <bg-image classname="w-50px h-50px" src="${this.image != "" && this.image !== 'null' && this.image !== null ? this.image : this.defaultImage}" rounded="true"></bg-image>
                                <div class="settings-body">
                                    <div class="settings-title text-muted">${this.fullname}</div>
                                    <div class="small text-muted">${this.role}</div>
                                    <staff-assignment-counter></staff-assignment-counter>
                                </div>
                            </div>
                            <div class="settings-action position-relative d-flex align-items-center">
                                <pageless-button classname="btn btn-circle" text='<i class="fa fa-${this.actionicon} fa-lg"></i>'></pageless-button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    AssignablePrecinctItem: new PageLessComponent("assignable-precinct-item", {
        data: {
            id: '',
            name: '',
            code: '',
            county: '',
            countyid: '',
            actionicon: 'ellipsis-v',
            expanded: true,
            staffid: "",
            mode: "",
            pollingplaces: [],
        },
        props: {
            onpollingplaceclick: function(){
                (new PageLess(`/precincts/${this.parentComponent.id}/polling-places`)).route();
            },
            toggle: function(event){
                event.stopPropagation();
                const parent      = this.parentComponent;
                const icon        = this.querySelector('i:empty')
                const ppContainer = parent.querySelector('.polling-places-container');
                if (parent.expanded === true) {
                    parent.expanded = false
                    icon.classList.remove('fa-angle-down');
                    icon.classList.add('fa-angle-right');
                    PageLess.SlideUp(ppContainer)
                } else {
                    parent.expanded = true
                    icon.classList.remove('fa-angle-right');
                    icon.classList.add('fa-angle-down');
                    PageLess.SlideDown(ppContainer)
                }
            },

            checkedchange: function(){
                const parent        = this.parentComponent;
                const pollingPlaces = parent.querySelectorAll('.polling-place');
                const checkbox      = this.querySelector('input[type=checkbox]');
                if (pollingPlaces != null) {
                    pollingPlaces.forEach(pollingPlace=>{
                        pollingPlace.props.check.call(pollingPlace, checkbox.checked);
                    });
                }
            },

            check: function(){
                const checkbox = this.querySelector('input[type=checkbox]');
                if (checkbox != null) {
                    checkbox.checked = true
                }
            },

            unassign: function(){
                const parent = this.parentComponent;
                const checkboxes = parent.querySelectorAll('.assigned-polling-place:checked');
                if (checkboxes.length >= 1) {
                    Modal.Confirmation('Confirm Action', 'Are you sure you continue?').then(()=>{
                        const unassignedCenters = [];
                        checkboxes.forEach(box=>{
                            unassignedCenters.push(box.value);
                        });

                        PageLess.Request({
                            url: `/api/unassign-watcher-from-center`,
                            method: "POST",
                            data: {
                                user_id: parent.staffid,
                                center_ids: unassignedCenters
                            },
                            beforeSend: ()=>{
                                PageLess.ChangeButtonState(this, "Unassigning");
                            }
                        }, true).then(result=>{
                            PageLess.RestoreButtonState(this, "Unassigning");
                            if (result.status == 200) {
                                this.parentComponents('staff-assingment-manager-widget').querySelector('.scroll-view').update();
                                PageLess.Toast('success', result.message);
                            } else{
                                PageLess.Toast('danger', result.message, 5000);
                            }
                        });
                    });
                } else {
                    PageLess.Toast('danger', "No polling place selected from this precinct. Please select at least one polling place to be unassigned", 5000)
                }
            }
        },
        view: function(){
            let pollingPlaces = '';
            this.pollingplaces.forEach(pollingPlace=>{
                pollingPlaces += /*html*/ `<assignable-polling-place-item id="${pollingPlace.id}" name="${pollingPlace.title}" code="${pollingPlace.code}" precinctid="${pollingPlace.precinct_id}"></assignable-polling-place-item>`
            });

            return /*html*/`
                <div class="w-100" onclick="{{this.props.onclick}}">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-5 main-content-item container-shadow flex-column">
                            <div class="w-100 d-flex align-items-center ${this.mode }">
                                <div class="pl-3 w-auto">
                                    <check-box classname="pl-3" identity="${this.id}" value="${this.id}" onchange="{{this.props.checkedchange}}"></check-box>
                                </div>
                                <div class="settings-details h-100 pb-2">
                                    <div class="settings-body">
                                        <div class="settings-title text-muted">${this.name}</div>
                                        <div class="text-muted small">Code: ${this.code}</div>
                                        <div class="text-muted small">County: ${this.county} </div>
                                    </div>
                                </div>
                                <div class="d-flex w-auto">
                                    ${this.mode == 'unassignment' ? /*html*/ `<pageless-button class="btn btn-light" text='Unassign' onclick="{{this.props.unassign}}"></pageless-button>&emsp;` : ''}
                                    <pageless-button class="btn-circle" text='<i class="fa text-muted fa-lg fa-${this.expanded === true ? 'angle-down' : 'angle-right'}"></i>' onclick="{{this.props.toggle}}"></pageless-button>
                                </div>
                            </div>
                            <div class="w-100 polling-places-container bg-light borde-radius-10">
                                ${pollingPlaces}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    AssignablePollingPlaceItem: new PageLessComponent("assignable-polling-place-item", {
        data: {
            id: '',
            name: '',
            code: '',
            precinctid: '',
            mode: "assignment"
        },
        props: {
            onclick: function(event){
                this.props.check.call(this);
            },

            check: function(value = null){
                const checkbox = this.querySelector('input[type=checkbox]');
                if (value == null) {
                    if (checkbox.checked == true) {
                        checkbox.checked = false;
                    } else {
                        checkbox.checked = true; 
                    }
                } else {
                    checkbox.checked = value
                }
            }
        },
        view: function(){
            return /*html*/`
                <div class="col-12 polling-place" onclick="{{this.props.onclick}}">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-5 main-content-item cursor-pointer">
                            <div class="pl-3 w-auto h-100"><check-box classname="assigned-polling-place" identity="${this.id}" value="${this.id}"></check-box></div>
                            <div class="settings-details">
                                <div class="settings-body">
                                    <div class="settings-title text-muted">${this.name}</div>
                                    <div class="text-muted small">Code: ${this.code}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

    PoliticalCandidateResult: new PageLessComponent("political-candidate-result", {
        data: {
            id: '',
            image: '/media/images/candidate.png',
            fullname: '',
            position: "",
            electionid: "",
            county: "",
            actionicon: 'ellipsis-v'
        },
        props: {

        },
        view: function(){
            return /*html*/`
                <div class="col-12 candidate-result" onclick="{{this.props.onclick}}">
                    <div class="row p-1 p-sm-1 p-xl-2" >
                        <div class="settings-container border-radius-5 main-content-item container-shadow">
                            <div class="settings-details">
                                <bg-image classname="w-50px h-50px" src="${this.image}" rounded="true"></bg-image>
                                <div class="settings-body">
                                    <div class="settings-title text-muted">${this.fullname}</div>
                                    <div class="settings-details text-muted">${this.position}</div>
                                </div>
                            </div>
                            <div class="settings-action position-relative d-flex align-items-center w-auto">
                                <div class="form-content-row d-flex align-items-center h-40px point-allocator" style="padding: 0 !important; margin-bottom: 0 !important">
                                    ${this.enabled === true ? /*html*/ `<pageless-button type="button" classname="btn-clean container-shadow border-radius-5 w-30px h-100" text="<i class='fa fa-minus'></i>" onclick="{{this.props.decrement}}"></pageless-button>&nbsp;` : ''}
                                    <div class="form-input-container">
                                        <input
                                            type="number" 
                                            class="form-input w-100px text-center candidate-votes"
                                            name="${this.id}" 
                                            placeholder="Votes"
                                            title="Enter number only of vote only"
                                            value="" ${this.attributes}
                                            required="required"
                                        >
                                    </div>  
                                    ${this.enabled === true ? /*html*/ `<pageless-button type="button" classname="btn-clean container-shadow border-radius-5 w-30px h-100" text="<i class='fa fa-plus'></i>" onclick="{{this.props.increment}}"></pageless-button>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }),

}