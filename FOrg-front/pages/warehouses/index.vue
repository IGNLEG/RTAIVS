<template>
    <v-container justify="centre">
        <v-row justify="space-around">

            <v-card elevation="12" flex d-flex flex-column class="h-auto" sm="4" style="overflow-y:scroll;
                   height: 250px" aspect-ratio="1">
                <div id="app">
                    <v-dialog v-model="dialogCreate" max-width="448">
                        <template v-slot:activator="{ props: activatorProps }">
                            <v-btn color="indigo-lighten-2" v-bind="activatorProps">Naujas sandėlis</v-btn>
                        </template>
                        <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg"
                            title="Sukurti naują sandėlį">
                            <v-form style="width: 100%" @submit.prevent="createWarehouse">
                                <v-container>
                                    <v-row>
                                        <v-col cols="12">
                                            <v-text-field v-model="short_name" label="Trumpasis pavadinimas"
                                                required></v-text-field>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-text-field v-model="address" label="Adresas" required></v-text-field>
                                        </v-col>
                                        <v-col cols="12" align-self="end">
                                            <v-btn type="submit">Sukurti</v-btn>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-form>
                        </v-card>
                    </v-dialog>
                    <v-data-table class="{'v-data-table'}" sm="4" :headers="headers" :loading="loading"
                        :items="warehouses" :items-per-page="5">
                        <template v-slot:loading>
                            <v-skeleton-loader type="table-row@10"></v-skeleton-loader>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-dialog v-model="dialogEdit" max-width="448">
                                <template v-slot:activator="{ props: activatorProps }">
                                    <v-icon class="me-2" size="small" v-bind="activatorProps">
                                        mdi-pencil
                                    </v-icon>
                                </template>
                                <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg"
                                    title="Keisti sandėlio duomenis">
                                    <v-form style="width: 100%" @submit.prevent="editWarehouse(item)">
                                        <v-container>
                                            <v-row>
                                                <v-col cols="12">
                                                    <v-text-field v-model="item.short_name"
                                                        label="Trumpasis pavadinimas" required></v-text-field>
                                                </v-col>
                                                <v-col cols="12">
                                                    <v-text-field v-model="item.address" label="Adresas"
                                                        required></v-text-field>
                                                </v-col>
                                                <v-col cols="12" align-self="end">
                                                    <v-btn type="submit">Patvirtinti</v-btn>
                                                </v-col>
                                            </v-row>
                                        </v-container>
                                    </v-form>
                                </v-card>
                            </v-dialog>
                            <v-dialog v-model="dialogDelete" width="auto">
                                <template v-slot:activator="{ props: activatorProps }">
                                    <v-icon class="me-2" size="small" v-bind="activatorProps">
                                        mdi-delete
                                    </v-icon>
                                </template>
                                <v-card>
                                    <v-card-title class="text-h5">Ar tikrai norite ištrinti šį sandėlio
                                        įrašą?</v-card-title>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn color="primary" variant="text"
                                            @click="dialogDelete = false">Uždaryti</v-btn>
                                        <v-btn color="error" variant="text"
                                            @click="deleteWarehouse(item)">Ištrinti</v-btn>
                                        <v-spacer></v-spacer>
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>
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
const short_name = ref('');
const address = ref('');
const client = useSanctumClient();
const warehouses = ref([]);
const loading = ref(true);
const dialogEdit = ref(false);
const dialogDelete = ref(false);
const dialogCreate = ref(false);
const user = useSanctumUser();
const userPerms = ref({
  admin: user.value.roles.find(value => value.name == 'admin'),
  organiser: user.value.roles.find(value => value.name == 'organizer'),
  warehouse_worker: user.value.roles.find(value => value.name == 'warehouse_worker')
})
getWarehouses();

const headers = ref([
    { title: "Short name", key: "short_name" },
    { title: "Address", key: "address" },
    { title: 'Actions', key: 'actions', sortable: false },
]);

async function getWarehouses() {
    const data = await client(useRuntimeConfig().public.apiURL + '/warehouses', {
        method: 'GET',
        headers: { 'Accept': 'application/json' },
    })
    warehouses.value = data
    loading.value = false
};

async function deleteWarehouse(item) {
    await client(useRuntimeConfig().public.apiURL + '/warehouses/' + item.id, {
        method: 'DELETE'
    });
    const index = warehouses.value.indexOf(item, 0);
    if (index > -1) {
        warehouses.value.splice(index, 1);
    }
    dialogDelete.value = false;
}

async function editWarehouse(item) {
    await client(useRuntimeConfig().public.apiURL + '/warehouses/' + item.id, {
        method: 'PUT',
        body: {
            short_name: item.short_name,
            address: item.address
        }
    })
    dialogEdit.value = false;
}

async function createWarehouse() {
    const data = await client(useRuntimeConfig().public.apiURL + '/warehouses', {
        method: 'POST',
        body: {
            short_name: short_name.value,
            address: address.value
        }
    })
    const index = warehouses.value.indexOf(data, 0);
    if (index > -1) {
        warehouses.value.splice(index, 1);
    }
    warehouses.value.push(data);
    dialogCreate.value = false;
}
</script>

<style scoped></style>