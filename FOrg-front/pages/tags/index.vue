<template>
    <v-container justify="centre">
        <v-row justify="space-around">

            <v-card elevation="12" flex d-flex flex-column class="h-auto" sm="4" style="
                   height: 250px" aspect-ratio="1">
                <div id="app">
                    <v-dialog v-model="dialogCreate" max-width="448">
                        <template v-slot:activator="{ props: activatorProps }">
                            <v-btn class="ml-1 mt-1" color="indigo-lighten-2" v-bind="activatorProps">Nauja žymė</v-btn>
                        </template>
                        <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg"
                            title="Sukurti naują žymę">
                            <v-form style="width: 100%" @submit.prevent="createTag">
                                <v-container>
                                    <v-row>
                                        <v-col cols="12">
                                            <v-select v-model="category" label="Kategorija"
                                                :items="['Sound', 'Video', 'Recording', 'Storage', 'Stage', 'Other']"></v-select>
                                        </v-col>
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
                    <v-data-table class="{'v-data-table'}" sm="4" :headers="headers" :loading="loading" :items="tags"
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
                                    <v-form style="width: 100%" @submit.prevent="editTag(item)">
                                        <v-container>
                                            <v-row>
                                                <v-col cols="12">
                                                    <v-text-field v-model="item.category" label="Kategorija"
                                                        required></v-text-field>
                                                </v-col>
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
                                    <v-card-title class="text-h5">Ar tikrai norite ištrinti šią žymę?</v-card-title>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn elevation="2" color="red-lighten-1"
                                            @click="dialogDelete = false">Uždaryti</v-btn>
                                        <v-btn  elevation="2" color="indigo-lighten-2" @click="deleteTag(item)">Ištrinti</v-btn>
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
    middleware: ['sanctum:auth', 'warehouse-worker'],
    layout: 'default'
})
const category = ref('');
const name = ref('');
const client = useSanctumClient();
const tags = ref([]);
const loading = ref(true);
const dialogEdit = ref(false);
const dialogDelete = ref(false);
const dialogCreate = ref(false);

getTags();

const headers = ref([
    { title: "Kategorija", key: "category" },
    { title: "Pavadinimas", key: "name" },
    { title: 'Actions', key: 'actions', sortable: false },
]);

async function getTags() {
    const data = await client(useRuntimeConfig().public.apiURL + '/tags', {
        method: 'GET',
        headers: { 'Accept': 'application/json' },
    })
    tags.value = data
    loading.value = false
};

async function deleteTag(item) {
    await client(useRuntimeConfig().public.apiURL + '/tags/' + item.id, {
        method: 'DELETE'
    });
    const index = tags.value.indexOf(item, 0);
    if (index > -1) {
        tags.value.splice(index, 1);
    }
    dialogDelete.value = false;
}

async function editTag(item) {
    await client(useRuntimeConfig().public.apiURL + '/tags/' + item.id, {
        method: 'PUT',
        body: {
            category: item.category,
            name: item.name
        }
    })
    dialogEdit.value = false;
}

async function createTag() {
    const data = await client(useRuntimeConfig().public.apiURL + '/tags', {
        method: 'POST',
        body: {
            category: category.value,
            name: name.value
        }
    })
    const index = tags.value.indexOf(data, 0);
    if (index > -1) {
        tags.value.splice(index, 1);
    }
    tags.value.push(data);
    dialogCreate.value = false;
    category.value = '';
    name.value = '';
}
</script>

<style scoped></style>