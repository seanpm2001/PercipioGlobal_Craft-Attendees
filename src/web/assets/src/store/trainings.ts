import { defineStore } from "pinia"
import axios from 'axios'

const ENDPOINT = window?.Attendee?.cpUrl ?? 'https://researchschool.org.uk/cp'

export const useTrainingsStore = defineStore('trainings', {
    state:() => ({
        attendees: null,
        totalAttendees: null,
        attendeeInput: null,
        attendeeFormErrors: false,
        attendeeSuccess: false,
        showForm: false,
        loading: false,
        loadingApi: false,
        schools: null
    }),
    actions: {
        async fetchSchools(query) {

            if(query){
                this.loadingApi = true

                const api = axios.create({
                    baseURL: `https://api.v2.metaseed.io/schools/?mode=legacy&query=${query}`,
                    timeout: 3000
                })

                const response = await api.get('')

                if(response?.data){
                    this.schools = response.data?.suggestions;
                    this.loadingApi = false
                }else{
                    this.loadingApi = false
                }
            }
        },
        deleteAttendee(attendee) {
            const self = this
            this.loading = true

            axios({
                method: 'post',
                url: `${ENDPOINT}/actions/craft-attendees/training/delete`,
                data: attendee
            })
            .then(function (response) {
                self.loading = false
                self.attendees = self.attendees.filter((at) => at.id != attendee.attendeeId)
            });
        },
        submitAttendee(formValues) {

            let formObj = {};

            if(formValues instanceof FormData) {
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
                url: `${ENDPOINT}/actions/craft-attendees/training/save`,
                data: formValues
            })
            .then(function (response) {

                if(!response.data.success){
                    self.attendeeFormErrors = response.data.errors
                    self.loading = false
                    self.attendeeSuccess = false
                }else{
                    let updatedAttendee = self.attendees.filter(a => a.id == response?.data?.attendee?.id)
                    const attendeeIndex = self.attendees.findIndex(a => a.id == response?.data?.attendee?.id)

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
                        self.attendees.unshift(response?.data?.attendee)
                    }

                    self.loading = false
                    self.attendeeFormErrors = false
                    self.attendeeSuccess = true
                    self.attendeeInput = null
                }
            });

        },
        fetchAttendees(event, limit, offset){
            const self = this;
            this.loading = true;

            axios({
                method: 'get',
                url: `${ENDPOINT}/craft-attendees/trainings/attendees/${event}/${limit}/${offset}`,
            })
            .then(function (response) {
                self.loading = false
                self.totalAttendees = parseInt(response?.data?.meta?.total)
                self.attendees = self.attendees ? self.attendees.concat(response?.data?.attendees) : response?.data?.attendees
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
        },
        clearSchools() {
            this.schools = null
        }
    }
})
