<template>
    <div>
        <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg">
            <v-container>
                <v-row>
                    <v-col cols="12">
                        <v-text-field v-model="client.name" label="Name" readonly></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="client.surname" label="Surname" readonly></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="client.company_id" label="Company ID" readonly></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="client.phone" label="Phone number" readonly></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="client.email" label="Email" type="email" readonly></v-text-field>
                    </v-col>
                </v-row>
            </v-container>
        </v-card>
    </div>
</template>
<script setup lang="ts">
import { FetchError } from 'ofetch';

definePageMeta({
    middleware: ['sanctum:auth', 'admin'],
    layout: 'default'
})
const { errorToast } = useToast()

const client = ref({
    name: '',
    surname: '',
    company_id: 0,
    phone: '',
    email: ''
});

const { id } = useRoute().params;

const sanctClient = useSanctumClient();

getClient();
async function getClient() {
    let data;
    try {
        data = await sanctClient(useRuntimeConfig().public.apiURL + '/clients/' + id, {
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

</script>

<style scoped></style>