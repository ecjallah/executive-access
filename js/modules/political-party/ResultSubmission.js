import { PageLess } from '../../PageLess/PageLess.min.js';
import { PageLessComponent } from '../../PageLess/PageLess.min.js';
import { Modal } from './Modals.js';
import "../Components.js";
import "./PoliticalPartyComponents.js";
export const widget = function() {
    return new PageLessComponent("result-submission-manager-widget", {
        data: {
            title: "Result Submission",
            precinctid: PageLess.GetURLData()[1],
            centerid: PageLess.GetURLData()[2],
        },
        props: {
            onsubmit: function(event){
                event.preventDefault()
                Modal.BuildForm({
                    title: "Confirm Submission",
                    icon: "exclamation-triangle",
                    description: `PAY ATTENTION! Make sure you've reviewed these result before procceding. Enter the Precinct Code to confirm your submission.`,
                    inputs: /*html*/ `
                        <text-input icon="map-marker-alt" text="Precinct Code" identity="code" required="required"></text-input>
                    `,
                    submitText: "Add",
                    closable: false,
                    autoClose: false,
                }, values=>{
                    const candidatesVotes = this.querySelectorAll('.candidate-votes');
                    if (candidatesVotes.length >= 1) {
                        const voteResults = [];
                        let totalVotes    = 0;
                        candidatesVotes.forEach(vote=>{
                            const parent = vote.closest('.candidate-result');
                            const id     = parent.id;
                            const value  = vote.value.trim();
                            totalVotes  += parseInt(value);
                            voteResults.push({candidate_id: id, vote_value: value});
                        })

                        if (totalVotes <= 3000) {
                            let submitBtn = values.modal.querySelector('button[type=submit]');
                            PageLess.Request({
                                url: `/api/add-candidate-votes`,
                                method: "POST",
                                data: {
                                    center_id: this.centerid,
                                    candidate_votes: voteResults,
                                },
                                beforeSend: ()=>{
                                    PageLess.ChangeButtonState(submitBtn, 'Submitting');
                                }
                            }, true).then(result=>{
                                PageLess.RestoreButtonState(submitBtn);
                                if (result.status != 200) {
                                    Modal.Close(values.modal);
                                    Modal.Success("Submission Successful", "Result successfully submitted").then(()=>{
                                        (new PageLess('/')).route();
                                    });
                                } else {
                                    PageLess.Toast('danger', result.message);
                                }
                            });
                        } else {
                            PageLess.Toast('danger', "Vote counts may be incorrect. Please check and try again")
                        }
                    } else {
                        PageLess.Toast("danger", "No candidate found", 5000);
                    }
                });
            },

            onload: function(){
                return new Promise(resolve => {
                    this.setRequest({
                        url: `/api/get-national-candidates-list`,
                        method: "POST",
                    });

                    this.mapData(data=>{
                        data.push({
                            id : "invalid_vote",
                            election_type_id : "1",
                            full_name : "<span class='text-danger'>Invalid Votes</span>",
                            county : "",
                            position : "Number of Invalid Votes",
                        })

                        return data;
                    })

                    this.setChild(data=>{
                        return /*html*/ `<political-candidate-result id="${data.id}" fullname="${data.full_name}" firstname="${data.first_name}" middlename="${data.middle_name}" lastname="${data.last_name}" position="${data.position}" electionid="${data.election_type_id}" county="${data.county}"></political-candidate-result>`;
                    });

                    resolve();
                });
            },
        },
        view: function(){
            return /*html*/`
                <form class="main-content" onsubmit="{{this.props.onsubmit}}">
                    <main-content-header title="${this.title}"></main-content-header>
                    <div class="main-content-body">
                        <vertical-scroll-view nodataicon="fa-key" preloader="list" onload="{{this.props.onload}}" scrollable="false"></vertical-scroll-view>
                        <div class="w-100 d-flex justify-content-center my-3">
                            <pageless-button type="submit" classname="btn btn-primary px-4 col-5 col-md-4 col-xl-3" text="Submit Result"></pageless-button>
                        </div>
                    </div>
                </form>
            `;
        },
    });
}