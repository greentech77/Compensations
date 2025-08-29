export let stepperEventsMixin = {
    methods: {
        onBack() {
            this.$emit('cancel')
        },
        onComplete() {
            this.$emit('finish')
        },
    }
}

export let stepperControllerMixin = {
    data() {
        return {
            currentStepIndex: 0,
        }
    },

    methods: {
        onChangeStep(index) {
            this.currentStepIndex = index
        },
        onCancelStep() {
            if (this.currentStepIndex >= 1) {
                this.currentStepIndex--
            } else {
                // Return to compensations list when canceling from the first step
                this.$inertia.get(this.route('compenzations'))
            }
        },
        onFinishStep() {
            if (this.currentStepIndex == this.steps.length - 1) {
                // For compensation flow, redirect to compensations list
                this.$inertia.get(this.route('compenzations'))
            } else {
                this.currentStepIndex++
            }
        },
    }
}