<template>
    <div>
        <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg">
            <v-form style="width: 100%" @submit.prevent="handleSubmit">
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <v-text-field v-model="name" label="Pavadinimas" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="address" label="Adresas" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="owner" label="Savininkas" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="phone" label="Tel. nr." required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="email" label="El. paštas" type="email" required></v-text-field>
                        </v-col>
                        <span>Iš įmonės galima nuomotis:</span>
                        <v-col cols="12">
                            <v-checkbox v-model="audio_subrent" label="Audio įrangą" required></v-checkbox>
                        </v-col>
                        <v-col cols="12">
                            <v-checkbox v-model="video_subrent" label="Video įrangą" required></v-checkbox>
                        </v-col>
                        <v-col cols="12">
                            <v-checkbox v-model="vehicle_subrent" label="Automobilius" required></v-checkbox>
                        </v-col>
                        <v-col cols="12">
                            <v-checkbox v-model="storage_subrent" label="Dėžes/dėklus" required></v-checkbox>
                        </v-col>
                        <v-col cols="12">
                            <v-checkbox v-model="stage_subrent" label="Scenos įrangą" required></v-checkbox>
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
definePageMeta({
    middleware: ['sanctum:auth', 'admin'],
    layout: 'default'
})

const name = ref('');
const address = ref('');
const owner = ref('');
const phone = ref('');
const email = ref('');
const audio_subrent = ref(false);
const video_subrent = ref(false); 
const vehicle_subrent = ref(false);
const storage_subrent = ref(false);
const stage_subrent = ref(false);

const { m_id } = useRoute().params;

const sanctClient = useSanctumClient();

getCompany();
async function getCompany() {
    const data = await sanctClient(useRuntimeConfig().public.apiURL + '/companies/' + m_id, {
        method: 'GET'
    })
    name.value = data.name;
    address.value = data.address;
    owner.value = data.owner;
    phone.value = data.phone;
    email.value = data.email;
    audio_subrent.value = data.audio_subrent == 1 ? true : false;
    video_subrent.value = data.video_subrent == 1 ? true : false;
    vehicle_subrent.value = data.vehicle_subrent == 1 ? true : false;
    storage_subrent.value = data.storage_subrent == 1 ? true : false;
    stage_subrent.value = data.stage_subrent == 1 ? true : false;
}

async function handleSubmit() {
    const data = await sanctClient(useRuntimeConfig().public.apiURL + '/companies/' + m_id, {
        method: 'PUT',
        body: {
            name: name.value,
            address: address.value,
            owner: owner.value,
            phone: phone.value,
            email: email.value,
            audio_subrent: audio_subrent.value,
            video_subrent: video_subrent.value,
            vehicle_subrent: vehicle_subrent.value,
            storage_subrent: storage_subrent.value,
            stage_subrent: stage_subrent.value,
        }
    })
}

</script>


<style scoped></style>