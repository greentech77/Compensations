import mitt from 'mitt'

const emitter = mitt()

export default {

    data() {
        return {
            emitter
        }
    },

}