import { defineStore } from 'pinia'
import axios from 'axios'

const ENDPOINT = window?.Attendee?.cpUrl ?? 'https://researchschool.org.uk/cp'

export const useLogsStore = defineStore( 'logs', {
    state: () => ({
        logs: null,
        loading: false
    }),
    actions: {
        fetchLogs(event){
            const self = this
            this.loading = true

            axios({
                method: 'get',
                url: `${ENDPOINT}/craft-attendees/trainings/logs/${event}`,
            })
            .then(function (response) {
                self.loading = false
                self.logs = response?.data?.logs
            });
        }
    }
} )
