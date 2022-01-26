import { defineStore } from "pinia"
import axios from 'axios'

const ENDPOINT = window?.Attendee?.cpUrl ?? 'https://researchschool.org.uk/cp'

export const useDashboardStore = defineStore('trainings', {
    state:() => ({
        events: null,
        loading: false
    }),
    actions: {
        fetchEvents(school, period){
            const self = this;
            this.loading = true;

            axios({
                method: 'get',
                url: `${ENDPOINT}/craft-attendees/dashboard/events/${school}/${period}`,
            })
            .then(function (response) {
                self.loading = false
                console.log("response", response.data)
                self.events = response?.data?.events
            });
        },
    }
})
