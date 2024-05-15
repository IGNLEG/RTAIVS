<template>
    <div>
        <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg">
            <v-form style="width: 100%" @submit.prevent="handleSubmit">
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <v-select v-model="warehouse" :items="warehouses" label="Sandėlis"
                                item-title="name" item-value="id" return-object>
                            </v-select>
                        </v-col> 
                        <v-col cols="12">
                            <v-text-field v-model="code" label="Kodas" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="name" label="Pavadinimas" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-file-input v-model="file" label="Įrangos nuotrauka" required></v-file-input>
                        </v-col>
                        <v-col cols="12">
                            <v-select v-model="type" label="Tipas"
                                :items="['Sound', 'Video', 'Recording', 'Storage', 'Stage', 'Other']" required></v-select>
                        </v-col>
                        <v-col cols="12">
                            <v-select v-model="selectedTags" :items="allTypeTags" label="Žymės" item-title="name"
                                item-value="id" return-object multiple>
                            </v-select>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="amount" label="Kiekis" type="number" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="u_price" label="Vieneto kaina" type="number" step="0.01"
                                required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="r_price" label="Nuomos kaina" type="number" step="0.01"
                                required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="problems" label="Trūkumai/problemos" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-btn type="submit">Sukurti</v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </v-form>
        </v-card>
    </div>
</template>
<script setup lang="ts">
definePageMeta({
    middleware: ['sanctum:auth', 'warehouse-worker'],
    layout: 'default'
})
const code = ref('');
const name = ref('');
const type = ref('');
const amount = ref('');
const u_price = ref('');
const r_price = ref('');
const problems = ref('');
const file = ref([])
const formData = new FormData();
const allTags = ref([]);
const allTypeTags = ref([]);
const selectedTags = ref([]);
const prevTags = ref([]);
const prevType = ref('none');
const warehouse = ref({
    id: 0,
    name: ''
});
const warehouses = ref([])
const client = useSanctumClient();

getAllTags();
await getWarehouses();
async function getWarehouses() {
    const data = await client(useRuntimeConfig().public.apiURL + '/warehouses', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })

    warehouses.value = data.map(t => ({ id: t.id, name: t.short_name + ', ' + t.address }));
    warehouse.value.id = warehouses.value[0].id;
    warehouse.value.name = warehouses.value[0].name;
}
watch(type, async (newType, oldType) => {
    if (newType != prevType.value && prevType.value != 'none') {
        prevType.value = oldType;
        prevTags.value = selectedTags.value;
        selectedTags.value = [];

        allTypeTags.value = allTags.value.filter(res => res.category === newType);
    }
    else if (newType == prevType.value) {

        const tempArr = ref([]);
        prevType.value = oldType;
        tempArr.value = prevTags.value;
        prevTags.value = selectedTags.value;
        selectedTags.value = tempArr.value;

        allTypeTags.value = allTags.value.filter(res => res.category === newType);
    }
    else {
        prevType.value = newType;
        allTypeTags.value = allTags.value.filter(res => res.category === newType);
    }
})

async function getAllTags() {
    const data = await client(useRuntimeConfig().public.apiURL + '/tags', {
        method: 'GET'
    })

    allTags.value = data;
    allTypeTags.value = allTags.value.filter(res => res.category === type.value);

}

async function handleSubmit() {
    formData.append("code", code.value);
    formData.append("warehouse_id", String(warehouse.value.id));
    formData.append("name", name.value);
    formData.append("type", type.value);
    formData.append("amount_total", amount.value);
    formData.append("unit_price", u_price.value);
    formData.append("rent_price", r_price.value);
    formData.append("problems", problems.value);
    formData.append("img_name", file.value[0] != null ? (code.value + '.png') : '');
    formData.append("img_file", file.value[0] != undefined ? file.value[0] : '');
    formData.append("tag_ids", JSON.stringify(selectedTags.value.map(t => t.id)));
    await client(useRuntimeConfig().public.apiURL + '/equipment', {
        method: 'POST',
        body: formData
    })
    window.history.length > 1 ? useRouter().go(-1) : await navigateTo('/equipment')
}

</script>

<style scoped></style>