<template>
    <div>
        <ContentCollapse
            title="Changelog - Was ist neu bei Swapp?"
            collapse-key="changelog-swapp"
            is-visible-by-default
        >
            <v-list class="pt-0">
                <template v-for="item in items" :key="item.header">
                    <v-list-item
                        class="bg-grey-lighten-2"
                    >
                        <template v-slot:prepend>
                            <div class="font-weight-bold">{{ item.header }}</div>
                        </template>
                        <v-badge
                            v-if="hasItemNewBadge(item.header)"
                            content="Neu"
                            class="mr-2"
                            color="primary"
                            inline
                            left
                            floating
                        />
                        <template v-slot:append>
                            <v-avatar
                                v-if="item.avatarText"
                                color="white"
                                class=""
                                v-html="item.avatarText"
                                :title="item.avatarTitle ?? ''"
                            />
                        </template>
                    </v-list-item>
                    <v-list-item
                        class="py-3"
                    >
                        <ul class="pl-5 mb-0">
                            <li v-for="(entry,entryKey) in item.entries" :key="`${item.header}-${entryKey}`">
                                <template v-if="Array.isArray(entry.text)">
                                    <span v-html="entry.text[0]" />
                                    <ul class="pl-5">
                                        <template v-for="(textItem, key) in entry.text" :key="key">
                                            <li
                                                v-if="key !== 0"
                                                v-html="textItem"
                                            />
                                        </template>
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
import { useChangelogStore } from '../stores';

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
