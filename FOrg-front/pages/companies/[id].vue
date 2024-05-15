<template>
    <div>
        <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg">
            <v-container>
                <v-row>
                    <v-col cols="12">
                        <v-text-field v-model="name" label="Pavadinimas" readonly></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="address" label="Adresas" readonly></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="owner" label="Savininkas" readonly></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="phone" label="Tel. nr." readonly></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="email" label="El. paštas" type="email" readonly></v-text-field>
                    </v-col>
                    <span>Iš įmonės galima nuomotis:</span>
                    <v-col cols="12">
                        <v-checkbox v-model="audio_subrent" label="Audio įrangą" disabled></v-checkbox>
                    </v-col>
                    <v-col cols="12">
                        <v-checkbox v-model="video_subrent" label="Video įrangą" disabled></v-checkbox>
                    </v-col>
                    <v-col cols="12">
                        <v-checkbox v-model="vehicle_subrent" label="Automobilius" disabled></v-checkbox>
                    </v-col>
                    <v-col cols="12">
                        <v-checkbox v-model="storage_subrent" label="Dėžes/dėklus" disabled></v-checkbox>
                    </v-col>
                    <v-col cols="12">
                        <v-checkbox v-model="stage_subrent" label="Scenos įrangą" disabled></v-checkbox>
                    </v-col>
                </v-row>
            </v-container>
        </v-card>
    </div>
</template>
<script setup lang="ts">
definePageMeta({
    middleware: ['sanctum:auth'],
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

const { id } = useRoute().params;

const sanctClient = useSanctumClient();

getCompany();
async function getCompany() {
    const data = await sanctClient(useRuntimeConfig().public.apiURL + '/companies/' + id, {
        method: 'GET'
    })

    name.value = data[0].name;
    address.value = data[0].address;
    owner.value = data[0].owner;
    phone.value = data[0].phone;
    email.value = data[0].email;
    audio_subrent.value = data[0].audio_subrent;
    video_subrent.value = data[0].video_subrent;
    vehicle_subrent.value = data[0].vehicle_subrent;
    storage_subrent.value = data[0].storage_subrent;
    stage_subrent.value = data[0].stage_subrent;
}

</script>

<style scoped></style>