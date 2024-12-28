<template>
    <v-alert
        v-if="hasError"
        type="error"
        class="mt-3 mb-0"
        prominent
        dense
    >
        <ul class="mb-0">
            <li
                v-for="(validationError, name) in validationErrors"
                :key="name"
            >
                <b
                    v-if="name !== 'global'"
                    v-text="`${name}:`"
                    class="text-capitalize"
                />
                {{ validationError }}
            </li>
        </ul>
    </v-alert>
</template>

<script lang="ts">
import { defineComponent, computed } from 'vue';

export default defineComponent({
    name: 'FormError',
    props: {
        error: {
            type: [Object, Boolean] as () => any | boolean,
            required: true,
        },
    },
    setup(props) {
        const hasError = computed(() => !!props.error);

        const validationErrors = computed(() => {
            const errors: Record<string, string> = {};
            if (!hasError.value) {
                return errors;
            }
            const error = props.error;

            if (error?.data?.violations) {
                error.data.violations.forEach((violation: { propertyPath?: string; message: string }) => {
                    const key = violation.propertyPath || 'global';
                    errors[key] = violation.message;
                });
                return errors;
            }

            if (error?.data?.['hydra:description']) {
                errors.global = error.data['hydra:description'];
            }

            return errors;
        });

        return {
            hasError,
            validationErrors,
        };
    },
});
</script>

<style scoped lang="scss">
</style>
