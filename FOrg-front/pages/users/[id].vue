<template>
    <div>
        <v-card class="mx-auto pa-6 pb-8" elevation="8" max-width="448" rounded="lg">
            <v-fab class="mr-4 mb-4" icon="mdi-pencil" location="right" size="40" absolute offset
                @click="navigateTo('/users/' + userS.id + '/edit')">
            </v-fab>
            <v-container>
                <v-row>
                    <v-col cols="12">
                        <v-text-field v-model="user.name" label="Vardas, pavardė" readonly></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="user.email" label="El. paštas" type="email" readonly></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="user.driving_license" label="Vairuotojo teisės" readonly></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="role" label="Rolė" :value="role.name" readonly></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="user.qualifications" label="Kvalifikacijos" readonly></v-text-field>
                    </v-col>
                </v-row>
            </v-container>
        </v-card>
    </div>
</template>
<script setup lang="ts">
import { FetchError } from 'ofetch';

definePageMeta({
    middleware: ['sanctum:auth'],
    layout: 'default'
})
const { id } = useRoute().params;
const userS = useSanctumUser();
if (userS.value.id != id) {
    await navigateTo('/')
}
const { errorToast } = useToast()

const user = ref({
    name: '',
    email: '',
    driving_license: '',
    roles: [],
    qualifications: []
});
const role = ref({
    name: '',
    value: '',
})
const roles = [
    { name: 'Vadovas', value: 'admin' },
    { name: 'Sandėlio darbuotojas', value: 'warehouse_worker' },
    { name: 'Organizatorius', value: 'organizer' },
    { name: 'Naudotojas', value: 'user' },
];
const sanctClient = useSanctumClient();


getUser();
async function getUser() {
    let data;
    try {
        data = await sanctClient(useRuntimeConfig().public.apiURL + '/users/' + id, {
            method: 'GET'
        })
    } catch (error) {
        if (error instanceof FetchError && error.response?.status === 404) {
            errorToast('Šis įrašas neegzistuoja.');
            await navigateTo('/');
        }

    }
    user.value.name = data[0].name;
    user.value.email = data[0].email;
    user.value.driving_license = data[0].driving_license;
    user.value.roles = data[0].roles.map(t => t.name).join(", ");
    user.value.qualifications = data[0].qualifications.map(t => t.name).join(", ");
    role.value.value = data[0].roles.map(t => t.name);
    role.value.name = roles.find(t => t.value == role.value.value).name;
}

</script>

<style scoped></style>