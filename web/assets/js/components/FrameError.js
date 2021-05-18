export default {
    props: {
        capture: {
            default: false,
            type: Boolean,
        },
    },
    data() {
        return {
            error: null,
        };
    },
    methods: {
        reset() {
            this.error = null;
            this.$emit('reset');
        },
    },
    errorCaptured(error) {
        this.error = error;
        this.$emit('error', error);
        // Optionally capture errors.
        if (this.capture) return false;
    },
    render() {
        return this.$scopedSlots.default({
            error: this.error,
            reset: this.reset,
        });
    },
};
