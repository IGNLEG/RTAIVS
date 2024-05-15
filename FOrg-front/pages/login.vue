<template>
    <div>
        <v-img class="mx-auto my-6" max-width="228"
            src="assets/logo.svg"></v-img>
        <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg">
            <v-form style="width: 100%" @submit.prevent="handleLogin" class="font-display">
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <v-text-field id="login-0" v-model="email" label="E-mail" :rules="[rules.required, rules.email]" type="email"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field id="login-1" v-model="password" label="Password" :rules="[rules.required]" type="password"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-btn color="indigo-lighten-2" type="submit">Prisijungti</v-btn>
                        </v-col>
                    </v-row>
                </v-container>                
            </v-form>
        </v-card>
    </div>
</template>
<script setup lang="ts">
import { FetchError } from 'ofetch';
import { useRules } from '~/composables/useRules';
const { errorToast } = useToast()
const rules = useRules();
definePageMeta({
    layout: 'guest'
})
const email = ref('')
const password = ref('')

const { login } = useSanctumAuth();

async function handleLogin() {
    const userCredentials = {
        email: email.value,
        password: password.value
    };

    try {
        await login(userCredentials);
    } catch (error) {
        if (error instanceof FetchError && error.response?.status === 422) {
           // here you can extract errors from a response 
           // and put it in your form for example
           errorToast('Neteisingas el. paštas arba slaptažodis.');
        }
        else (
            errorToast('Įvyko klaida.')
        )
    }
    await navigateTo('/');
};

</script>

<style scoped>
.font-display {
  font-family: 'Nunito'
}</style>