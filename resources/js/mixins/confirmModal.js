import emitter from '@/mixins/emitter'
import { computed } from 'vue'

export default {

    mixins: [ emitter ],

    data() {
        return {
            modalForms: {},
            modalPromise: undefined,
        }
    },

    methods: {
        showConfirmModal(modal) {
            this.$nextTick(() => {
                this.$refs[modal].forceShowModal = false
            })
            
            return (form) => {
                console.log(this.selectedOrder);
                
                this.modalForms[modal] = {
                    form: this.$inertia.form({
                        ...form.data
                    }),
                    url: form.url,
                    method: form.method
                }
                this.$refs[modal].forceShowModal = true
                return false
            }
        },
        onCloseModal(modal) {
            this.$refs[modal].forceShowModal = false
        },
        onSubmitModal(modal) {
            this.modalForms[modal].form.submit(
                this.modalForms[modal].method,
                this.modalForms[modal].url,
                {
                    onFinish: () => {
                        this.$refs[modal].forceShowModal = false
                    }
                }
            )
        },
        openModal(modalData) {
            this.confirmModalForm = this.$inertia.form({
                ...modalData.form.data
            })
            this.confirmModalForm.url = modalData.form.url
            this.confirmModalForm.method = modalData.form.method

            this.modalPromise = new Promise((resolve, reject) => {
                this.emitter.emit('modal.open', {
                    title: '...',
                    content: '...',
                    status: 'warning',
                    dismissible: true,
                    autoClose: false,
                    actions: [{
                        action: {
                            type: 'reject',
                            reject
                        },
                        color: 'white',
                        text: 'Prekliči',
                        disabled: computed(() => {
                            return this.confirmModalForm.processing
                        })
                    }, {
                        action: {
                            type: 'resolve',
                            resolve
                        },
                        text: 'Potrdi',
                        loading: computed(() => {
                            return this.confirmModalForm.processing
                        })
                    }],
                    ...modalData
                })
            })

            this.modalPromise.then(() => {
                return this.executeAction()
            }).catch(() => {
                this.emitter.emit('modal.close')
            })
            
            return this.modalPromise
        },
    },

}