import { defineStore } from "pinia"
import axios from 'axios'

const ENDPOINT = window?.Attendee?.cpUrl ?? 'https://researchschool.org.uk/cp'

export const useTrainingsStore = defineStore('trainings', {
    state:() => ({
        attendees: null,
        totalAttendees: null,
        attendeeInput: null,
        dataEngagement: null,
        attendeeFormErrors: false,
        attendeeSuccess: false,
        showForm: false,
        loading: false,
        loadingApi: false,
        schools: null,
        followOnSupportOptions: null,
        order: 'date'
    }),
    actions: {
        fetchOptions(event) {
            const self = this
            this.loading = true

            axios({
                method: 'get',
                url: `${ENDPOINT}/craft-attendees/trainings/fetch-support-options/${event}`,
            })
            .then(function (response) {
                self.loading = false
                self.followOnSupportOptions = response.data?.options
                self.followOnSupportSelectedOptions = response.data?.selectedOptions
            });
        },
        saveOption(data) {
            const self = this
            this.loading = true

            axios({
                method: 'post',
                url: `${ENDPOINT}/actions/craft-attendees/training/save-support-options`,
                data: data
            })
            .then(function (response) {
                self.loading = false
            });
        },
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
        deleteAttendees(attendees) {
            const self = this
            this.loading = true

            axios({
                method: 'post',
                url: `${ENDPOINT}/actions/craft-attendees/training/delete`,
                data: attendees
            })
            .then(function (response) {
                self.loading = false

                attendees.attendees.forEach((attendee) => {
                    self.attendees = self.attendees.filter((at) => at.id != attendee)
                })

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
                        updatedAttendee.orgUrn = parseInt(formObj?.orgUrn)
                        updatedAttendee.postCode = formObj?.postCode
                        updatedAttendee.days = formObj?.days
                        updatedAttendee.email = formObj?.email
                        updatedAttendee.newsletter = formObj?.newsletter
                        updatedAttendee.approved = formObj?.approved
                        updatedAttendee.jobRole = formObj?.jobRole
                        updatedAttendee.priority = formObj?.priority
                        updatedAttendee.anonymous = formObj?.anonymous

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
        fetchAttendees(event, order, limit, offset, init = false){
            const self = this;
            this.loading = true;
            this.order = order;

            if(init){
                this.attendees = []
            }

            axios({
                method: 'get',
                url: `${ENDPOINT}/craft-attendees/trainings/attendees/${event}/${order}/${limit}/${offset}`,
            })
            .then(function (response) {
                self.loading = false
                self.totalAttendees = parseInt(response?.data?.meta?.total)
                self.attendees = offset !== 0 ? self.attendees.concat(response?.data?.attendees) : response?.data?.attendees
            });
        },
        fetchEngagementData(event) {

            axios({
                method: 'get',
                url: `${ENDPOINT}/craft-attendees/trainings/engagement-data/${event}`,
            })
            .then(function (response) {
                self.loading = false
                self.dataEngagement = response?.data?.engagement
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
