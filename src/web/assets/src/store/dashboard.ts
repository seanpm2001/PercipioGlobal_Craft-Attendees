import { defineStore } from "pinia"
import axios from 'axios'

const ENDPOINT = window?.Attendee?.cpUrl ?? 'https://researchschool.org.uk/cp'
const CSRF = window?.Attendee?.csrf ?? ''

export const useDashboardStore = defineStore('trainings', {
    state:() => ({
        events: null,
        attendees: null,
        followOnSupport: null,
        followOnSupportOptions: null,
        period: 3,
        site: 'main',
        loading: false
    }),
    actions: {
        fetchEvents(ids){
            const self = this;
            this.loading = true;

            const site = this.site
            const period = this.period

            const obj = {
                CRAFT_CSRF_TOKEN : CSRF,
                events: ids,
            }

            axios({
                method: 'post',
                url: `${ENDPOINT}/actions/craft-attendees/dashboard/fetch`,
                data: obj
            })
            .then(function (response) {
                self.loading = false
                self.events = response?.data?.events
                self.attendees = response?.data?.attendees
                self.followOnSupport = response?.data?.follow_on_support
                self.followOnSupportOptions = response?.data?.follow_on_support_options
            });

            // axios({
            //     method: 'get',
            //     url: `${ENDPOINT}/craft-attendees/dashboard/events/${site}/${period}`,
            // })
            // .then(function (response) {
            //     self.loading = false
            //     self.events = response?.data?.events
            //     self.attendees = response?.data?.attendees
            //     self.followOnSupport = response?.data?.follow_on_support
            //     self.followOnSupportOptions = response?.data?.follow_on_support_options
            // });
        },
    }
})
