<script setup lang="ts">
import {ComponentPublicInstance, computed, ref, watch} from "vue";
import {User} from "../../../model";

// const props = defineProps(['modelValue', 'value']); // vue3
// const emit = defineEmits(['update:modelValue']); // vue3
const emit = defineEmits(['input']);

export interface Props {
    value: string[],
    walkCreator?: User,
    users: User[],
    label?: String,
    description?: String,
    isLoading?: Boolean
}

const props = withDefaults(defineProps<Props>(), {
    walkCreator: undefined,
    label: 'Teilnehmende der Runde',
    description: 'Wer war mit dabei?',
    isLoading: false,
});
const value = computed({
    get() {
        // return props.modelValue // vue3
        return props.value
    },
    set(value) {
        // emit('update:modelValue', value); // vue3
        emit('input', value)
    }
});

const selectedWalkCreator = ref<ComponentPublicInstance<HTMLInputElement>[]>();

watch(() => props.walkCreator, (newValue: User | undefined) => {
    if (undefined === newValue) {
        return;
    }
    if (!selectedWalkCreator.value) {
        return;
    }
    selectedWalkCreator.value.some((walkCreatorElement) => {
        if (walkCreatorElement.value !== newValue['@id']) {
            return false;
        }

        const classList = walkCreatorElement.$el.classList;
        classList.add('blinking');
        window.setTimeout(() => {
            classList.remove('blinking');
        }, 1500);

        return true;
    });
});

const enabledUsers = computed(() => {
    return props.users.filter(user => user.isEnabled);
});
const disabledUsers = computed(() => {
    return props.users.filter(user => !user.isEnabled);
});
const hasDisabledUser = computed(() => {
    return disabledUsers.value.length > 0;
});

</script>

<template>
    <v-row class="my-0">
        <v-col cols="12">
            <v-card
                outlined
            >
                <v-card-subtitle>{{ label }}</v-card-subtitle>
                <v-card-text class="mb-0 pb-0">
                    <div class="d-flex flex-wrap" data-test="users">
                        <template v-for="user in enabledUsers">
                            <v-switch
                                v-if="user.isEnabled"
                                :aria-describedby="description"
                                name="users"
                                dense
                                :disabled="isLoading || (walkCreator && walkCreator['@id'] === user['@id'])"
                                v-model="value"
                                :key="user['@id']"
                                :value="user['@id']"
                                :data-test="`walkTeamMember-${user.username}`"
                                class="min-w-[250px]"
                                ref="selectedWalkCreator"
                                :label="user.username"
                            >
                                <template v-if="walkCreator && walkCreator['@id'] === user['@id']"> (Rundenersteller)</template>
                            </v-switch>
                        </template>
                    </div>
                </v-card-text>
                <v-card-text class="py-0">
                    <v-divider
                        v-if="hasDisabledUser"
                        class="d-block w-100 my-1 mr-2"
                    ></v-divider>
                </v-card-text>
                <v-card-text class="py-0">
                    <div>
                        <template v-for="user in disabledUsers">
                            <v-switch
                                v-model="value"
                                :key="user['@id']"
                                :value="user['@id']"
                                :aria-describedby="description"
                                name="users"
                                dense
                                :disabled="isLoading || (walkCreator && walkCreator['@id'] === user['@id'])"
                                :data-test="`walkTeamMember-${user.username}`"
                                class="min-w-[250px] text-disabled"
                            >
                                <template v-slot:label>
                                    <span
                                        class="text-disabled d-inline-flex align-items-center"
                                        title="Account ist aktuell nicht aktiviert."
                                    >
                                        {{ user.username }}
                                        <template v-if="walkCreator && walkCreator['@id'] === user['@id']">
                                            (Rundenersteller)
                                        </template>
                                        <v-icon
                                            v-if="!user.isEnabled"
                                            small
                                            class="ml-1"
                                        >mdi-account-off</v-icon>
                                    </span>
                                </template>
                            </v-switch>
                        </template>
                    </div>
                    <p>{{ description }}</p>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<style scoped>
.blinking {
    animation: blinking 1.5s ease-in-out;
}

@keyframes blinking {
    0% { opacity: 1; }
    50% { opacity: 0; }
    100% { opacity: 1; }
}
</style>
