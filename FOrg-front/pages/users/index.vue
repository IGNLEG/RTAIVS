<template>
    <v-container justify="centre">
        <v-row justify="space-around">

            <v-card elevation="12" flex d-flex flex-column class="h-auto" sm="4" style="
                   height: 250px" aspect-ratio="1">
                <div id="app">
                    <v-btn class="ml-1 mt-1"color="indigo-lighten-2" to="/register">Naujas naudotojas</v-btn>
                    <v-data-table class="{'v-data-table'}" sm="4" :headers="headers" :loading="loading" :items="users"
                        :items-per-page="5">
                        <template v-slot:loading>
                            <v-skeleton-loader type="table-row@10"></v-skeleton-loader>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-icon class="me-2" size="small" @click="editUser(item)">
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
                                    <v-card-title class="text-h5">Ar tikrai norite ištrinti šį naudotoją?</v-card-title>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn elevation="2" color="red-lighten-1"
                                            @click="dialogDelete = false">Uždaryti</v-btn>
                                        <v-btn elevation="2" color="indigo-lighten-2"
                                            @click="deleteUser(item)">Ištrinti</v-btn>
                                        <v-spacer></v-spacer>
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>
                            <v-icon size="small" @click="viewUser(item)">
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
const users = ref([]);
const loading = ref(true);

getUsers();
const dialogDelete = ref(false);

const headers = ref([
    { title: "Vardas, pavardė", key: "name" },
    { title: "El. paštas", key: "email" },
    { title: "Vairuotojo teisės", key: "driving_license" },
    { title: "Rolė", key: "roles" },
    { title: "Kvalifikacijos", key: "qualifications" },
    { title: 'Veiksmai', key: 'actions', sortable: false },
]);

async function getUsers() {
    const data = await client(useRuntimeConfig().public.apiURL + '/users', {
        method: 'GET',
        headers: { 'Accept': 'application/json' },
    })

    for (let i = 0; i < data.length; i++) {
        const str = data[i].roles.map(t => t.name).join(", ")
        data[i].roles = str;
        const qstr = data[i].qualifications.map(t => t.name).join(", ")
        data[i].qualifications = qstr;
    }
    console.log('data', data)
    users.value = data
    loading.value = false
};

async function deleteUser(item){
    await client(useRuntimeConfig().public.apiURL + '/users/' + item.id, {
        method: 'DELETE'
    });
    const index = users.value.indexOf(item, 0);
    if(index > -1) {
        users.value.splice(index, 1);
    }
}

async function editUser(item){
    await navigateTo('/users/' + item.id + '/edit');
}

async function viewUser(item) {
    await navigateTo('/users/' + item.id);
}
</script>

<style scoped></style>