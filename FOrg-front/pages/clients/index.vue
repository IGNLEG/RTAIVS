<template>
    <v-container justify="centre">
        <v-row justify="space-around">

            <v-card elevation="12" flex d-flex flex-column class="h-auto" sm="4" style="overflow-y:scroll;
                   height: 250px" aspect-ratio="1">
                <div id="app">
                    <v-btn color="indigo-lighten-2" to="/clients/new">Naujas Klientas</v-btn>
                    <v-data-table class="{'v-data-table'}" sm="4" :headers="headers" :loading="loading" :items="clients"
                        :items-per-page="5">
                        <template v-slot:loading>
                            <v-skeleton-loader type="table-row@10"></v-skeleton-loader>
                        </template>
                        <template v-slot:item.company_id="{ item }" >
                            <v-chip v-if="item.company_id != null" @click="navigateTo('/clients/' + item.company_id)">{{ item.company_id }}</v-chip>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-icon class="me-2" size="small" @click="editClient(item)">
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
                                    <v-card-title class="text-h5">Ar tikrai norite ištrinti šį klientą?</v-card-title>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn elevation="2" color="red-lighten-1"
                                            @click="dialogDelete = false">Uždaryti</v-btn>
                                        <v-btn elevation="2" color="indigo-lighten-2"
                                            @click="deleteClient(item)">Ištrinti</v-btn>
                                        <v-spacer></v-spacer>
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>
                            <v-icon size="small" @click="viewClient(item)">
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
    middleware: ['sanctum:auth', 'admin'],
    layout: 'default'
})

const client = useSanctumClient();
const clients = ref([]);
const loading = ref(true);
const dialogDelete = ref(false);

getClients();

const headers = ref([
    { title: "Vardas", key: "name" },
    { title: "Pavardė", key: "surname" },
    { title: "Tel. nr.", key: "phone" },
    { title: "Įmonės ID", key: "company_id" },
    { title: "El. paštas", key: "email" },
    { title: 'Veiksmai', key: 'actions', sortable: false },
]);

async function getClients() {
    const data = await client(useRuntimeConfig().public.apiURL + '/clients', {
        method: 'GET',
        headers: { 'Accept': 'application/json' },
    })
    console.log('data', data)
    clients.value = data
    loading.value = false
};

async function deleteClient(item){
    await client(useRuntimeConfig().public.apiURL + '/clients/' + item.id, {
        method: 'DELETE'
    });
    const index = clients.value.indexOf(item, 0);
    if(index > -1) {
        clients.value.splice(index, 1);
    }
}

async function editClient(item){
    await navigateTo('/clients/' + item.id + '/edit');
}

async function viewClient(item) {
    await navigateTo('/clients/' + item.id);
}
</script>

<style scoped></style>