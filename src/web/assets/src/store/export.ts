import { defineStore } from 'pinia'
import axios from 'axios'

const ENDPOINT = window.Attendee?.cpUrl ?? 'https://researchschool.org.uk/cp'

export const useExportStore = defineStore( 'export', {
    state: () => ({
        tags: null,
        loading: false
    }),
    actions: {
        fetchTags(query){
            const self = this
            this.loading = true

            const data = new FormData()
            data.append("tag", query)
            data.append('CRAFT_CSRF_TOKEN', window.Attendee?.csrf)
            data.append('action', 'actions/craft-attendees/tag/fetch-tags')

            axios({
                method: 'post',
                url: `${ENDPOINT}/actions/craft-attendees/tag/fetch-tags`,
                data: data
            })
            .then(function (response) {
                self.loading = false

                if(response.data?.success){
                    self.tags = response.data?.tags
                }
            });
        },
        clearTags(){
            this.tags = null
        }
    }
} )
