<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import DemoInfo from './Demo/DemoInfo.vue';
import { useAuthStore } from '../stores';

interface Credentials {
    username: string;
    password: string;
}

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const username = ref('');
const password = ref('');
const passwordFieldType = ref<'password' | 'text'>('password');
const isPasswordVisible = ref(false);

const isOnDemoPage = computed(() => {
    return window.location.host.includes('swapp.demo') || route.query.demo;
});

const isLoading = computed(() => authStore.isLoading);

const hasError = computed(() => !!authStore.getErrors.login);

const error = computed(() => authStore.getErrors.login);

const handleCredentialsSelect = (credentials: Credentials) => {
    username.value = credentials.username;
    password.value = credentials.password;
};

const switchPasswordVisibility = () => {
    passwordFieldType.value = passwordFieldType.value === 'password' ? 'text' : 'password';
    isPasswordVisible.value = passwordFieldType.value === 'text';
};

const performLogin = async () => {
    if (passwordFieldType.value === 'text') {
        // ensure that password can be saved via browser
        switchPasswordVisibility();
    }

    const payload = {
        username: username.value,
        password: password.value
    };

    const redirect = route.query.redirect as string | undefined;
    const loginResult = await authStore.login(payload);

    if (!error.value && loginResult) {
        if (typeof redirect !== 'undefined') {
            router.push({ path: redirect });
        } else {
            router.push({ name: 'Dashboard' });
        }
    }
};

onMounted(() => {
    const redirect = route.query.redirect as string | undefined;

    if (authStore.isAuthenticated) {
        if (typeof redirect !== 'undefined') {
            router.push({ path: redirect });
        } else {
            router.push({ name: 'Dashboard' });
        }
    }
});
</script>


<template>
    <v-row>
        <v-col
            cols="12"
            sm="8"
            md="6"
            lg="6"
            xl="4"
            offset-sm="2"
            offset-md="3"
            offset-lg="3"
            offset-xl="4"
            class="mt-4"
        >
            <v-card>
                <h2
                    class="text-center my-3"
                >
                    Anmeldung
                </h2>
                <v-card-text>
                    <p class="text-center mb-3">
                        <template
                            v-if="isOnDemoPage"
                        >
                            Bitte melde dich mit einem der unten stehenden Zugangsdaten an
                            <br>
                            <span class="text-muted ">
                                oder alternativ mit deiner E-Mail-Adresse - oder deinem Benutzernamen - und deinem selbst gewählten Passwort an.
                           </span>
                        </template>
                        <template
                            v-else
                        >
                            Bitte melde dich mit deiner E-Mail-Adresse - oder deinem Benutzernamen - und deinem selbst gewählten Passwort an.
                        </template>
                    </p>
                    <div>
                        <v-form
                            novalidate
                            @submit.stop.prevent
                        >
                            <v-text-field
                                v-model="username"
                                id="username"
                                prepend-inner-icon="mdi-account-circle-outline"
                                autofocus
                                type="text"
                                :disabled="isLoading"
                                label="Benutzername oder E-Mail"
                                placeholder="vorname.nachname@domain.de"
                                name="username"
                                data-test="username"
                                autocomplete="username email"
                                density="compact"
                                variant="outlined"
                            />
                            <v-text-field
                                v-model="password"
                                prepend-inner-icon="mdi-lock-outline"
                                :append-inner-icon="isPasswordVisible ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                                :type="passwordFieldType"
                                :disabled="isLoading"
                                label="Passwort"
                                placeholder="Passwort"
                                name="password"
                                data-test="password"
                                autocomplete="password"
                                density="compact"
                                variant="outlined"
                                @click:append-inner="switchPasswordVisibility"
                            />
                            <v-btn
                                :disabled="username.length < 3 || password.length < -1 || isLoading"
                                block
                                density="default"
                                color="secondary"
                                type="submit"
                                :loading="isLoading"
                                class="text-transform-none"
                                @click="performLogin()"
                            >
                                Anmelden
                            </v-btn>
                            <v-alert
                                v-if="hasError"
                                type="warning"
                                class="mt-3"
                            >
                                {{ 'Die Kombination aus E-Mail-Adresse und Passwort ist ungültig.' }}
                            </v-alert>
                            <router-link
                                class="mt-3 d-block"
                                :to="{ name: 'PasswordReset' }"
                            >
                                Passwort vergessen oder noch kein Passwort?
                            </router-link>
                        </v-form>
                    </div>
                </v-card-text>
            </v-card>

            <DemoInfo
                @credentials-select="handleCredentialsSelect($event)"
            />
        </v-col>
    </v-row>
</template>
