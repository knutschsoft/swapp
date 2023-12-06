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

watch(() => props.walkCreator, (newValue: Props['walkCreator']) => {
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
    <b-form-group
        :label="label"
        :description="description"
        v-slot="{ ariaDescribedby }"
        content-cols="12"
        label-cols="12"
        content-cols-lg="10"
        label-cols-lg="2"
    >
        <div
            class="d-flex flex-wrap"
            data-test="users"
        >
            <template
                v-for="user in enabledUsers"
            >
                <b-form-checkbox
                    v-if="user.isEnabled"
                    :aria-describedby="ariaDescribedby"
                    name="users"
                    switch
                    :disabled="isLoading || (walkCreator && walkCreator['@id'] === user['@id'])"
                    v-model="value"
                    :key="user['@id']"
                    :value="user['@id']"
                    :data-test="`walkTeamMember-${user.username}`"
                    class="min-w-[250px]"
                    ref="selectedWalkCreator"
                >
                    {{ user.username }}
                    <template v-if="walkCreator && walkCreator['@id'] === user['@id']"> (Rundenersteller)</template>
                </b-form-checkbox>
            </template>
            <hr
                v-if="hasDisabledUser"
                class="d-block w-100 my-1 mr-2"
            >
            <template
                v-for="user in disabledUsers"
            >
                <b-form-checkbox
                    v-model="value"
                    :key="user['@id']"
                    :value="user['@id']"
                    :aria-describedby="ariaDescribedby"
                    name="users"
                    switch
                    :disabled="isLoading || (walkCreator && walkCreator['@id'] === user['@id'])"
                    :data-test="`walkTeamMember-${user.username}`"
                    ref="selectedWalkCreator"
                    class="min-w-[250px] text-muted"
                >
                        <span
                            class="text-muted d-inline-flex align-items-center"
                        >
                            {{ user.username }}
                            <span>
                                <template
                                    v-if="walkCreator && walkCreator['@id'] === user['@id']"
                                >
                                    (Rundenersteller)
                                </template>
                            </span>
                            <mdicon
                                v-if="!user.isEnabled"
                                name="AccountOff"
                                class="text-muted d-inline-flex align-items-center"
                                title="Account ist aktuell nicht aktiviert."
                                size="16"
                            />
                        </span>
                </b-form-checkbox>
            </template>
        </div>
    </b-form-group>
</template>
