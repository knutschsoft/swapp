<template>
    <div
        v-if="isLoading"
        class="d-flex justify-content-center my-3"
    >
        <b-spinner
            v-show="isLoading"
            style="width: 3rem; height: 3rem;"
            label="is Loading Spinner"
        />
    </div>

</template>

<script>
export default {
    name: 'ContentLoadingSpinner',
    props: {
        isLoading: {
            type: Boolean,
            required: true,
        },
    },
};
</script>
