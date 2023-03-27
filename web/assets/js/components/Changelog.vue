<template>
    <div>
        <content-collapse
            title="Changelog - Was ist neu bei Swapp?"
            collapse-key="changelog-swapp"
            is-visible-by-default
        >
            <b-list-group>
                <template v-for="item in items">
                    <b-list-group-item
                        class="d-flex justify-content-between align-items-center"
                        variant="dark"
                    >
                        <div>
                            <span class="font-weight-bold">{{ item.header }}</span>
                            <b-badge
                                v-if="hasItemNewBadge(item.header)"
                                variant="primary"
                            >
                                Neu
                            </b-badge>
                        </div>
                        <b-avatar
                            v-if="item.avatarText"
                            variant="light"
                            v-html="item.avatarText"
                            :title="item.avatarTitle ?? ''"
                        />
                    </b-list-group-item>
                    <b-list-group-item>
                        <ul class="pl-3 mb-0">
                            <li
                                v-for="entry in item.entries"
                            >
                                <template v-if="Array.isArray(entry.text)">
                                    <span
                                        v-html="entry.text[0]"
                                    />
                                    <ul>
                                        <li
                                            v-for="(textItem, i) in entry.text"
                                            v-if="i !== 0"
                                            v-html="textItem"
                                        />
                                    </ul>
                                </template>
                                <span
                                    v-else
                                    v-html="entry.text"
                                />
                                <silent-box
                                    v-if="entry.gallery && entry.gallery.length"
                                    :gallery="entry.gallery"
                                    lazy-loading
                                />
                            </li>
                        </ul>
                    </b-list-group-item>
                </template>
            </b-list-group>
        </content-collapse>
    </div>
</template>

<script>
'use strict';
import ContentCollapse from './ContentCollapse.vue';
import dayjs from 'dayjs';

export default {
    name: 'Changelog',
    components: {
        ContentCollapse,
    },
    data: () => {
        return {
            lastVisitedAt: false,
        };
    },
    computed: {
        items() {
            return this.$store.getters['changelog/changelogs'];
        },
    },
    created() {
        this.lastVisitedAt = this.$store.getters['changelog/lastVisitedAt'];
        this.$store.dispatch('changelog/updateLastVisitedAt', dayjs());
    },
    methods: {
        hasItemNewBadge(header) {
            const itemTime = dayjs(header.split(' ')[0], ["DD.MM.YYYY", "YYYY"]);

            return itemTime.isAfter(this.lastVisitedAt);
        },
    }
};
</script>

<style>
.silentbox-item img {
    border-radius: 0.5rem;
    margin-right: 0.5rem;
    border: 1px solid grey;
}
#silentbox-overlay div .arrow {
    padding: 1rem;
}
#silentbox-overlay .silentbox-video__embed {
    height: 100%;
    width: 100%;
}
</style>
