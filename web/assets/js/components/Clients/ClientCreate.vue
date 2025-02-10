<template>
    <client-form
        v-if="initialClient"
        submit-button-text="Neuen Klienten erstellen"
        :initial-client="initialClient"
        @submitted="handleSubmit"
    />
</template>

<script lang="ts">
import {defineComponent, ref} from 'vue';
import ClientForm from './ClientForm.vue';
import {useAlertStore, useClientStore} from '../../stores';
import {Client, ClientCreateRequest} from "../../model";

export default defineComponent({
    name: 'ClientCreate',
    components: {
        ClientForm,
    },
    setup() {
        const alertStore = useAlertStore();
        const clientStore = useClientStore();
        const initialClient = ref<Client>({});

        const handleSubmit = async (payload: ClientCreateRequest) => {
            const client = await clientStore.createClient(payload);
            if (client) {
                alertStore.success(`Der Klient "${client.name}" wurde erfolgreich erstellt.`);
                initialClient.value = {name: 'narf'};
                initialClient.value.name = null;
            } else {
                alertStore.error('Klient erstellen fehlgeschlagen', 'Upps! :-(');
            }
        };

        return {
            initialClient,
            handleSubmit,
        };
    },
});
</script>

<style scoped lang="scss">
</style>
