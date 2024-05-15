<template>
    <div>
        <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg">
            <v-form v-model="valid" style="width: 100%" @submit.prevent="handleRegister">
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <v-text-field v-model="name" label="Vardas, Pavardė"
                                :rules="[rules.required]"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="email" label="E-mail" type="email"
                                :rules="[rules.required]"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-select v-model="driving_license" label="Vairuotojo teisės"
                                :items="['None', 'B', 'BE', 'C', 'B, BE', 'B, C', 'B, BE, C']"
                                :rules="[rules.required]"></v-select>
                        </v-col>
                        <v-col cols="12">
                            <v-select v-model="role" :items="roles" label="Rolė" item-title="name" item-value="value"
                                return-object>
                            </v-select>
                        </v-col>
                        <v-col cols="12">
                            <v-select :items="qualifs" v-model="qualifsForEdit" label="Kvalifikacijos" item-title="name" item-value="id"
                                return-object multiple>
                            </v-select>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="password" label="Slaptažodis"
                                :rules="[rules.required, rules.password_length]" type="password"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="confirm_password"
                                :rules="[(password === confirm_password) || 'Slaptažodžiai privalo sutapti.', rules.required]"
                                label="Patvirtinti slaptažodį" type="password"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-btn color="indigo-lighten-2" type="submit">Sukurti</v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </v-form>
        </v-card>
    </div>
</template>
<script setup lang="ts">
import { useRules } from '~/composables/useRules';
import { FetchError } from 'ofetch';

definePageMeta({
    middleware: ['sanctum:auth', 'admin'],
    layout: 'default',
})
const rules = useRules();
const valid = ref(false)
const email = ref('')
const name = ref('')
const qualifs = ref([]);
const qualifsForEdit = ref([]);
const password = ref('')
const confirm_password = ref('')
const driving_license = ref('');
const client = useSanctumClient();
const { errorToast } = useToast()
const roles = [
    { name: 'Vadovas', value: 'admin' },
    { name: 'Sandėlio darbuotojas', value: 'warehouse_worker' },
    { name: 'Organizatorius', value: 'organizer' },
    { name: 'Naudotojas', value: 'user' },
];
const role = ref({
    name: '',
    value: '',
})
getQualifications();
async function getQualifications() {
    const data = await client(useRuntimeConfig().public.apiURL + '/qualifications', {
        method: 'GET',
    })

    qualifs.value = data;
}
async function handleRegister() {
    if (valid.value) {
        try {
            await client('/register', {
                method: 'POST',
                body: {
                    name: name.value,
                    email: email.value,
                    password: password.value,
                    password_confirmation: confirm_password.value,
                    driving_license: driving_license.value
                }
            })
            window.history.length > 1 ? useRouter().go(-1) : await navigateTo('/')
        }
        catch (error) {
            if (error instanceof FetchError && error.response?.status === 422) {
                if (error.response?._data.errors['email'].find(t => t == "The email has already been taken.")) {
                    errorToast('Paskyra su šiuo el. paštu jau egzistuoja.');
                }

            }
            else (
                errorToast('Įvyko klaida kuriant paskyrą.')
            )

        }
    }


}

</script>