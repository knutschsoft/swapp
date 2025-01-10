<template>
    <div>
        <ContentCollapse
            title="Changelog - Was ist neu bei Swapp?"
            collapse-key="changelog-swapp"
            is-visible-by-default
        >
            <v-list class="p-0">
                <template v-for="item in items">
                    <v-list-item
                        dense
                        class="grey lighten-2 p-2 mt-0"
                        :key="item.header"
                    >
                        <v-list-item-avatar
                            v-if="item.avatarText"
                            color="white"
                            v-html="item.avatarText"
                            :title="item.avatarTitle ?? ''"
                        />
                        <v-list-item-content>
                            <v-badge
                                v-if="hasItemNewBadge(item.header)"
                                content="Neu"
                                inline
                                right
                            >
                                <div class="mr-auto font-weight-bold">{{ item.header }}</div>
                            </v-badge>
                            <div v-else class="font-weight-bold">{{ item.header }}</div>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                        :key="`${item.header}2`"
                        class="py-3"
                    >
                        <ul class="pl-3 mb-0">
                            <li v-for="(entry,entryKey) in item.entries" :key="`${item.header}-${entryKey}`">
                                <template v-if="Array.isArray(entry.text)">
                                    <span v-html="entry.text[0]" />
                                    <ul>
                                        <li
                                            v-for="(textItem, i) in entry.text"
                                            v-if="i !== 0"
                                            :key="i"
                                            v-html="textItem"
                                        />
                                    </ul>
                                </template>
                                <span v-else v-html="entry.text" />
                                <silent-box
                                    v-if="entry.gallery && entry.gallery.length"
                                    :gallery="entry.gallery"
                                    lazy-loading
                                />
                            </li>
                        </ul>
                    </v-list-item>
                </template>
            </v-list>
        </ContentCollapse>
    </div>
</template>

<script lang="ts">
import { ref, computed, onMounted } from 'vue';
import ContentCollapse from './ContentCollapse.vue';
import dayjs, {Dayjs} from 'dayjs';
import { useChangelogStore } from '../stores/changelog';

export default {
    name: 'Changelog',
    components: {
        ContentCollapse,
    },
    setup() {
        const changelogStore = useChangelogStore();
        const lastVisitedAt = ref<boolean | Dayjs>(false );

        onMounted(() => {
            lastVisitedAt.value = changelogStore.getLastVisitedAt;
            changelogStore.updateLastVisitedAt(dayjs());
        });

        const items = computed(() => changelogStore.getChangelogs);

        const hasItemNewBadge = (header: string): boolean => {
            const itemTime = dayjs(header.split(' ')[0], ['DD.MM.YYYY', 'YYYY']);
            if (!lastVisitedAt.value) {
                return false;
            }
            return itemTime.isAfter(lastVisitedAt.value);
        };

        return {
            items,
            hasItemNewBadge,
        };
    },
};
</script>

<style scoped>
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
