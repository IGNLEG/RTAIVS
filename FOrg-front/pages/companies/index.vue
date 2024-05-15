<template>
    <v-container justify="centre">
        <v-row justify="space-around">

            <v-card elevation="12" flex d-flex flex-column class="h-auto" sm="4" style="overflow-y:scroll;
                   height: 250px" aspect-ratio="1">
                <div id="app">
                    <v-btn v-if="userPerms.admin" color="indigo-lighten-2" to="/companies/new">Nauja įmonė</v-btn>
                    <v-data-table class="{'v-data-table'}" sm="4" :headers="headers" :loading="loading" :items="companies"
                        :items-per-page="5">
                        <template v-slot:loading>
                            <v-skeleton-loader type="table-row@10"></v-skeleton-loader>
                        </template>
                        <template v-slot:item.audio_subrent="{ item }">
                            {{ item.audio_subrent == true ? 'Taip' : 'Ne' }}
                        </template>
                        <template v-slot:item.video_subrent="{ item }">
                            {{ item.video_subrent == true ? 'Taip' : 'Ne' }}
                        </template>
                        <template v-slot:item.vehicle_subrent="{ item }">
                            {{ item.vehicle_subrent == true ? 'Taip' : 'Ne' }}
                        </template>
                        <template v-slot:item.storage_subrent="{ item }">
                            {{ item.storage_subrent == true ? 'Taip' : 'Ne' }}
                        </template>
                        <template v-slot:item.stage_subrent="{ item }">
                            {{ item.stage_subrent == true ? 'Taip' : 'Ne' }}
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-icon class="me-2" size="small" @click="editCompany(item)">
                                mdi-pencil
                            </v-icon>
                            <v-dialog v-model="dialogDelete" width="auto">
                                <template v-slot:activator="{ props: activatorProps }">
                                    <v-icon class="me-2" size="small"
                                        v-bind="activatorProps">
                                        mdi-delete
                                    </v-icon>
                                </template>
                                <v-card>
                                    <v-card-title class="text-h5">Ar tikrai norite ištrinti šią įmonę?</v-card-title>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn elevation="2" color="red-lighten-1"
                                            @click="dialogDelete = false">Uždaryti</v-btn>
                                        <v-btn elevation="2" color="indigo-lighten-2"
                                            @click="deleteCompany(item)">Ištrinti</v-btn>
                                        <v-spacer></v-spacer>
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>
                            <v-icon size="small" @click="viewCompany(item)">
                                mdi-eye
                            </v-icon>
                        </template>
                    </v-data-table>
                </div>
            </v-card>
        </v-row>
    </v-container>
</template>
<script setup lang="ts">
definePageMeta({
    middleware: ['sanctum:auth'],
    layout: 'default'
})

const client = useSanctumClient();
const companies = ref([]);
const loading = ref(true);
const dialogDelete = ref(false);
const user = useSanctumUser();
const userPerms = ref({
  admin: user.value.roles.find(value => value.name == 'admin'),
  organiser: user.value.roles.find(value => value.name == 'organizer'),
  warehouse_worker: user.value.roles.find(value => value.name == 'warehouse_worker')
})
getCompanies();

const headers = ref([
    { title: "Pavadinimas", key: "name" },
    { title: "Adresas", key: "address" },
    { title: "Savininkas", key: "owner" },
    { title: "Tel. nr.", key: "phone" },
    { title: "El. paštas", key: "email" },
    { title: "Audio nuoma", key: "audio_subrent" },
    { title: "Video nuoma", key: "video_subrent" },
    { title: "Auto nuoma", key: "vehicle_subrent" },
    { title: "Dėžių/dėklų nuoma", key: "storage_subrent" },
    { title: "Scenos įrangos nuoma", key: "stage_subrent" },
    { title: 'Veiksmai', key: 'actions', sortable: false },
]);

async function getCompanies() {
    const data = await client(useRuntimeConfig().public.apiURL + '/companies', {
        method: 'GET',
        headers: { 'Accept': 'application/json' },
    })
    console.log('data', data)
    companies.value = data
    loading.value = false
};

async function deleteCompany(item){
    await client(useRuntimeConfig().public.apiURL + '/companies/' + item.id, {
        method: 'DELETE'
    });
    const index = companies.value.indexOf(item, 0);
    if(index > -1) {
        companies.value.splice(index, 1);
    }
}

async function editCompany(item){
    await navigateTo('/companies/' + item.id + '/edit');
}
async function viewCompany(item) {
    await navigateTo('/companies/' + item.id);
}
</script>

<style scoped></style>