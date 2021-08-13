<template>
    <div>
        <content-collapse
            title="Liste der Tags"
            data-test="header-tag-list"
            collapse-key="tag-list"
            is-visible-by-default
        >
            <tag-list />
        </content-collapse>
        <content-collapse
            title="Neuen Tag erstellen"
            data-test="header-tag-create"
            collapse-key="tag-create"
        >
            <tag-create />
        </content-collapse>
    </div>
</template>

<script>
    "use strict";
    import TagList from './Tags/TagList';
    import TagCreate from './Tags/TagCreate';
    import ContentCollapse from './ContentCollapse.vue';

    export default {
        name: "Tags",
        components: {
            ContentCollapse,
            TagCreate,
            TagList
        },
        mounted() {
        }
    }
</script>

<style scoped>

</style>
