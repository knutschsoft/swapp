<template>
    <client-form
        submit-button-text="Neuen Klienten erstellen"
        :initial-client="{}"
        ref="clientForm"
        @submit="handleSubmit"
    />
</template>

<script lang="ts">
import {defineComponent, ref} from 'vue';
import ClientForm from './ClientForm.vue';
import {useAlertStore, useClientStore} from '../../stores';
import {ClientCreateRequest} from "@/js/model";

export default defineComponent({
    name: 'ClientCreate',
    components: {
        ClientForm,
    },
    setup() {
        const clientForm = ref<InstanceType<typeof ClientForm> | null>(null);
        const alertStore = useAlertStore();
        const clientStore = useClientStore();

        const handleSubmit = async (payload: ClientCreateRequest) => {
            const client = await clientStore.createClient(payload);
            if (client) {
                alertStore.success(`Der Klient "${client.name}" wurde erfolgreich erstellt.`);
                clientForm.value?.resetForm();
            } else {
                alertStore.error('Klient erstellen fehlgeschlagen', 'Upps! :-(');
            }
        };

        return {
            clientForm,
            handleSubmit,
        };
    },
});
</script>

<style scoped lang="scss">
</style>
