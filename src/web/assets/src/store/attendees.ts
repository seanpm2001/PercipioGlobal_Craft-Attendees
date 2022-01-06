import {defineStore} from "pinia"
import axios from 'axios'

export const useAttendeeStore = defineStore('attendees', {
    state:() => ({
        attendees: null,
        attendeeInput: null,
        attendeeFormErrors: false,
        attendeeSuccess: false,
        showForm: false,
        loading: false
    }),
    getters: {
        getAttendees(id) {
            return this.attendees
        }
    },
    actions: {
        submitAttendee(formValues) {

            let formObj = {};

            if(typeof formValues !== 'object') {
                for (var pair of formValues.entries()) {
                    formObj[pair[0]] = pair[1]
                }
            }else{
                formObj = formValues
            }

            this.attendeeFormErrors = false
            this.loading = true
            this.attendeeSuccess = false
            this.attendeeInput = formObj
            const self = this

            axios({
                method: 'post',
                url: '/admin/actions/craft-attendees/training/save',
                data: formValues
            })
            .then(function (response) {

                if(!response.data.success){
                    self.attendeeFormErrors = response.data.errors
                    self.loading = false
                    self.attendeeSuccess = false
                }else{
                    let updatedAttendee = self.attendees.filter(a => a.id == formObj.attendeeId)
                    const attendeeIndex = self.attendees.findIndex(a => a.id == formObj.attendeeId)

                    console.log("updatedAttendee",updatedAttendee, attendeeIndex)

                    if(updatedAttendee.length > 0){
                        updatedAttendee = updatedAttendee[0]
                        updatedAttendee.name = formObj?.name
                        updatedAttendee.orgName = formObj?.orgName
                        updatedAttendee.postCode = formObj?.postCode
                        updatedAttendee.days = formObj?.days
                        updatedAttendee.email = formObj?.email
                        updatedAttendee.newsletter = formObj?.newsletter
                        updatedAttendee.approved = formObj?.approved
                        updatedAttendee.jobRole = formObj?.jobRole

                        self.attendees[attendeeIndex] = updatedAttendee
                    }else{
                        self.attendees.unshift(formObj)
                    }

                    self.loading = false
                    self.attendeeFormErrors = false
                    self.attendeeSuccess = true
                    self.attendeeInput = null
                }
            });

        },
        fetchAttendees(event, hitsPerPage, currentPage){
            const self = this;
            this.loading = true;

            axios({
                method: 'get',
                url: `/admin/craft-attendees/trainings/attendees/${event}/${hitsPerPage}/${currentPage}`,
            })
            .then(function (response) {
                self.loading = false
                self.attendees = response?.data?.attendees
            });
        },
        setShowFrom(value){
            this.attendeeFormErrors = false
            // this.attendeeSuccess = false
            this.showForm = value
        },
        resetForm(){
            this.loading = false
            this.attendeeFormErrors = false
            // this.attendeeSuccess = false
            this.attendeeInput = null
        }
    }
})
