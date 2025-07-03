<script setup lang="ts">
import {ComponentPublicInstance, computed, nextTick, ref, watch} from "vue";
import {User} from "../../../model";

const emit = defineEmits(['update:modelValue']);

export interface Props {
    modelValue: string[],
    walkCreator?: string,
    users: User[],
    label?: string,
    description?: string,
    showDisabled?: boolean,
    isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    walkCreator: undefined,
    label: 'Teilnehmende der Runde',
    description: 'Wer war mit dabei?',
    showDisabled: true,
    isLoading: false,
});
const value = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});

const selectedWalkCreator = ref<ComponentPublicInstance<HTMLInputElement>[]>();

watch(() => props.walkCreator, (newValue: string | undefined) => {
    if (undefined === newValue) {
        return;
    }
    // ensure Rundenersteller is also walkTeamMember
    if (!value.value.includes(newValue)) {
        value.value.push(newValue);
    }
    // let it blink
    if (!selectedWalkCreator.value) {
        return;
    }
    selectedWalkCreator.value.some((walkCreatorElement) => {
        if (walkCreatorElement.value !== newValue) {
            return false;
        }
        nextTick(() => {
            const classList = walkCreatorElement.$el.classList;
            classList.add('blinking');
            window.setTimeout(() => {
                classList.remove('blinking');
            }, 1500);
        });
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
        <v-col cols="12" class="pt-0">
            <v-card
                variant="outlined"
                color="grey"
            >
                <v-card-text class="mb-0 pb-0">
                    <div class="text-black text-body-2">{{ label }}</div>
                    <div class="d-flex flex-wrap" data-test="users">
                        <template v-for="user in enabledUsers" :key="user['@id']">
                            <v-switch
                                v-if="user.isEnabled"
                                :aria-describedby="description"
                                :name="`users-${user.username}`"
                                density="compact"
                                color="primary"
                                :readonly="isLoading || (walkCreator === user['@id'])"
                                v-model="value"
                                :value="user['@id']"
                                :data-test="`walkTeamMember-${user.username}`"
                                class="min-w-[250px]"
                                ref="selectedWalkCreator"
                                :label="walkCreator === user['@id'] ? `${user.username} (Rundenersteller)` : user.username"
                                hide-details
                            >
                            </v-switch>
                        </template>
                    </div>
                </v-card-text>
                <v-card-text v-if="showDisabled" class="py-0">
                    <v-divider
                        v-if="hasDisabledUser"
                        class="d-block w-100 my-1 mr-2"
                    ></v-divider>
                </v-card-text>
                <v-card-text v-if="showDisabled" class="py-0">
                    <div class="d-flex flex-wrap">
                        <template v-for="user in disabledUsers" :key="user['@id']">
                            <v-switch
                                v-model="value"
                                :value="user['@id']"
                                :aria-describedby="description"
                                :name="`users-${user.username}`"
                                color="primary"
                                density="compact"
                                :disabled="isLoading || (walkCreator === user['@id'])"
                                :data-test="`walkTeamMember-${user.username}`"
                                class="min-w-[250px] text-disabled"
                                hide-details
                            >
                                <template v-slot:label>
                                    <span
                                        class="text-disabled d-inline-flex align-items-center"
                                        title="Account ist aktuell nicht aktiviert."
                                    >
                                        {{ user.username }}
                                        <template v-if="walkCreator === user['@id']">
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
                    <p v-if="description">{{ description }}</p>
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
