<template>
    <div>
        <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg">
            <v-form style="width: 100%" @submit.prevent="handleSubmit">
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <v-text-field v-model="client.name" label="Vardas" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="client.surname" label="Pavardė" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="client.company_id" label="Įmonė" type="number"></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="client.phone" label="Tel. nr." required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="client.email" label="El. paštas" type="email"
                                required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-btn color="indigo-lighten-2" type="submit">Patvirtinti</v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </v-form>
        </v-card>
    </div>
</template>
<script setup lang="ts">
import { FetchError } from 'ofetch';

definePageMeta({
    middleware: ['sanctum:auth', 'admin'],
    layout: 'default'
})

const client = ref({
    name: '',
    surname: '',
    company_id: 0,
    phone: '',
    email: ''
});
const { errorToast } = useToast()

const { c_id } = useRoute().params;

const sanctClient = useSanctumClient();

getClient();
async function getClient() {
    let data;
    try {
        data = await sanctClient(useRuntimeConfig().public.apiURL + '/clients/' + c_id, {
            method: 'GET'
        })
    } catch (error) {
        if (error instanceof FetchError && error.response?.status === 404) {
            errorToast('Šis įrašas neegzistuoja.');
            await navigateTo('/');
        }

    }
    client.value.name = data.name;
    client.value.surname = data.surname;
    client.value.company_id = data.company_id;
    client.value.phone = data.phone;
    client.value.email = data.email;
}

async function handleSubmit() {
    const data = await sanctClient(useRuntimeConfig().public.apiURL + '/clients/' + c_id, {
        method: 'PUT',
        body: client.value
    })
}

</script>


<style scoped></style>