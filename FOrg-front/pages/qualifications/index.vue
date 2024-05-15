<template>
    <v-container justify="centre">
        <v-row justify="space-around">

            <v-card elevation="12" flex d-flex flex-column class="h-auto" sm="4" style="
                   height: 250px" aspect-ratio="1">
                <div id="app">
                    <v-dialog v-model="dialogCreate" max-width="448">
                        <template v-slot:activator="{ props: activatorProps }">
                            <v-btn class="ml-1 mt-1" color="indigo-lighten-2" v-bind="activatorProps">Nauja kvalifikacija</v-btn>
                        </template>
                        <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg"
                            title="Sukurti naują kvalifikaciją">
                            <v-form style="width: 100%" @submit.prevent="createQualification">
                                <v-container>
                                    <v-row>
                                        <v-col cols="12">
                                            <v-text-field v-model="name" label="Pavadinimas" required></v-text-field>
                                        </v-col>
                                        <v-col cols="12" align-self="end">
                                            <v-btn color="indigo-lighten-2" type="submit">Sukurti</v-btn>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-form>
                        </v-card>
                    </v-dialog>
                    <v-data-table class="{'v-data-table'}" sm="4" :headers="headers" :loading="loading" :items="qualifications"
                        :items-per-page="5">
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
                                    title="Keisti žymės duomenis">
                                    <v-form style="width: 100%" @submit.prevent="editQualification(item)">
                                        <v-container>
                                            <v-row>
                                                <v-col cols="12">
                                                    <v-text-field v-model="item.name" label="Pavadinimas"
                                                        required></v-text-field>
                                                </v-col>
                                                <v-col cols="12" align-self="end">
                                                    <v-btn color="indigo-lighten-2" type="submit">Patvirtinti</v-btn>
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
                                    <v-card-title class="text-h5">Ar tikrai norite ištrinti šią kvalifikaciją?</v-card-title>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn color="primary" variant="text"
                                            @click="dialogDelete = false">Uždaryti</v-btn>
                                        <v-btn color="error" variant="text" @click="deleteQualification(item)">Ištrinti</v-btn>
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
const category = ref('');
const name = ref('');
const client = useSanctumClient();
const qualifications = ref([]);
const loading = ref(true);
const dialogEdit = ref(false);
const dialogDelete = ref(false);
const dialogCreate = ref(false);

getQualifications();

const headers = ref([
    { title: "Pavadinimas", key: "name" },
    { title: 'Veiksmai', key: 'actions', sortable: false },
]);

async function getQualifications() {
    const data = await client(useRuntimeConfig().public.apiURL + '/qualifications', {
        method: 'GET',
        headers: { 'Accept': 'application/json' },
    })
    qualifications.value = data
    loading.value = false
};

async function deleteQualification(item) {
    await client(useRuntimeConfig().public.apiURL + '/qualifications/' + item.id, {
        method: 'DELETE'
    });
    const index = qualifications.value.indexOf(item, 0);
    if (index > -1) {
        qualifications.value.splice(index, 1);
    }
    dialogDelete.value = false;
}

async function editQualification(item) {
    await client(useRuntimeConfig().public.apiURL + '/qualifications/' + item.id, {
        method: 'PUT',
        body: {
            name: item.name
        }
    })
    dialogEdit.value = false;
}

async function createQualification() {
    const data = await client(useRuntimeConfig().public.apiURL + '/qualifications', {
        method: 'POST',
        body: {
            name: name.value
        }
    })
    const index = qualifications.value.indexOf(data, 0);
    if (index > -1) {
        qualifications.value.splice(index, 1);
    }
    qualifications.value.push(data);
    dialogCreate.value = false;
    category.value = '';
    name.value = '';
}
</script>

<style scoped></style>