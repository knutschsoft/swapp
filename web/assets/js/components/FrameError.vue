<script lang="ts">
import {defineComponent, ref, onErrorCaptured} from "vue";

export default defineComponent({
    props: {
        capture: {
            type: Boolean,
            default: false,
        },
    },
    emits: ["reset", "error"],
    setup(props, {emit, slots}) {
        const error = ref<Error | null>(null);

        function reset() {
            error.value = null;
            emit("reset");
        }

        onErrorCaptured((err) => {
            error.value = err;
            emit("error", err);
            return props.capture ? false : undefined;
        });

        return () => {
            return slots.default?.({error: error.value, reset});
        };
    },
});
</script>
