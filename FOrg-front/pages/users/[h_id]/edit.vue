<template>
    <div>
        <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg">
            <v-form style="width: 100%" @submit.prevent="handleSubmit">
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <v-text-field v-model="user.name" label="Vardas, Pavardė" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="user.email" label="El. paštas" type="email" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-select v-model="user.driving_license" label="Vairuotojo teisės"
                                :items="['None', 'B', 'BE', 'C', 'B, BE', 'B, C', 'B, BE, C']"></v-select>
                        </v-col>

                        <v-col cols="12">
                            <v-select v-if="userPerms.admin && role.value !='admin'" v-model="role" :items="roles" label="Rolė"
                                item-title="name" item-value="value" return-object>
                            </v-select>
                        </v-col>
                        <v-col cols="12">
                            <v-select v-if="userPerms.admin" v-model="qualifsForEdit" :items="qualifs"
                                label="Kvalifikacijos" item-title="name" item-value="id" return-object multiple>
                            </v-select>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-if="!userPerms.admin || (userPerms.admin && role.value =='admin')" v-model="role" label="Rolė" :value="role.name"
                                readonly></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-if="!userPerms.admin" v-model="user.qualification_ids"
                                label="Kvalifikacijos" readonly></v-text-field>
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
    middleware: ['sanctum:auth'],
    layout: 'default'
})
const { errorToast } = useToast()
const { h_id } = useRoute().params;

const userDB = useSanctumUser();
if (userDB.value.id != h_id) {
    await navigateTo('/')
}
const qualifs = ref([]);
const qualifsForEdit = ref([]);
const userPerms = ref({
    admin: userDB.value.roles.find(value => value.name == 'admin'),
    organiser: userDB.value.roles.find(value => value.name == 'organizer'),
    warehouse_worker: userDB.value.roles.find(value => value.name == 'warehouse_worker')
})
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
const user = ref({
    id: 0,
    name: '',
    email: '',
    driving_license: '',
    roles: [],
    qualification_ids: []
});


const sanctClient = useSanctumClient();

getUser();
if (userPerms.value.admin) {
    getQualifications();
}
async function getUser() {
    let data;
    try {
        data = await sanctClient(useRuntimeConfig().public.apiURL + '/users/' + h_id, {
            method: 'GET'
        })
    } catch (error) {
        if (error instanceof FetchError && error.response?.status === 404) {
            errorToast('Šis įrašas neegzistuoja.');
            await navigateTo('/');
        }

    }
    user.value.id = data[0].id;
    user.value.name = data[0].name;
    user.value.email = data[0].email;
    user.value.driving_license = data[0].driving_license;
    user.value.roles = data[0].roles.map(t => t.name).join(", ");
    role.value.value = data[0].roles.map(t => t.name);
    role.value.name = roles.find(t => t.value == role.value.value).name;
    user.value.qualification_ids = data[0].qualifications.map(t => t.name).join(", ");
    qualifsForEdit.value = data[0].qualifications.map(t => ({ id: t.id, name: t.name }));
}
async function getQualifications() {
    const data = await sanctClient(useRuntimeConfig().public.apiURL + '/qualifications', {
        method: 'GET',
    })

    qualifs.value = data;
}
async function handleSubmit() {
    user.value.qualification_ids = qualifsForEdit.value.map(t => t.id);
    try {
        await sanctClient(useRuntimeConfig().public.apiURL + '/users/' + h_id, {
            method: 'PUT',
            body: user.value
        })
    } catch (error) {
        if (error instanceof FetchError && error.response?.status === 422) {
            errorToast('Klaida atnaujinant įrašą.');
        }

    }
    if (userPerms.value.admin){
        await sanctClient(useRuntimeConfig().public.apiURL + '/users/'+ user.value.id +'/role', {
            method: 'PUT',
            body: user.value
        })
    }
    await navigateTo('/');

}

</script>


<style scoped></style>