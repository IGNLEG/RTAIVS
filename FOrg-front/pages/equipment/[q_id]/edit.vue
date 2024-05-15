<template>
    <v-card class="mx-auto pa-12 pb-8" elevation="10">
        <v-container>
            <v-form style="width: 100%" @submit.prevent="handleSubmit">
                <v-row>
                    <v-col cols="12">
                        <v-text-field v-model="eqUnit.warehouse" label="Sandėlis" type="number" required></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="eqUnit.code" label="Kodas" required></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="eqUnit.name" label="Pavadinimas" required></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <span>Dabartinis paveiksliukas:</span>
                        <v-img heigth="200" width="150" :src="'data:' + img_mime + ';base64,' + img_base64"
                            :alt=img_name></v-img>
                        <v-checkbox v-model="enabled" label="Keisti paveiksliuką"></v-checkbox>
                        <v-file-input :disabled="!enabled" v-model="file" label="Failas"></v-file-input>

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
                        <v-text-field v-model="eqUnit.total" label="Kiekis" type="number" required></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="eqUnit.unit_price" label="Vieneto kaina" type="number" step="0.01"
                            required></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="eqUnit.profit" label="Profit" type="number" step="0.01"
                            readonly></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="eqUnit.rent_price" label="Nuomos kaina" type="number" step="0.01"
                            required></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-text-field v-model="eqUnit.problems" label="Trūkumai" required></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-btn type="submit">Patvirtinti</v-btn>
                    </v-col>
                </v-row>
            </v-form>
        </v-container>
    </v-card>
</template>

<script setup lang="ts">
import { VBtn } from 'vuetify/components';
import { FetchError } from 'ofetch';

definePageMeta({
    middleware: ['sanctum:auth', 'warehouse-worker'],
    layout: 'default'
})
const { errorToast } = useToast()

const enabled = ref(false);
const eqUnit = ref({
    warehouse: 0,
    code: '',
    name: '',
    total: 0,
    unit_price: 0,
    rent_price: 0,
    profit: 0,
    problems: '',
});

const type = ref('');
const img_name = ref('');
const img_mime = ref('');
const img_base64 = ref('');
const file = ref([]);
const allTags = ref([]);
const allTypeTags = ref([]);
const selectedTags = ref([]);
const prevTags = ref([]);
const prevType = ref('');



const sanctClient = useSanctumClient();
const { q_id } = useRoute().params;

getEqUnit();
getAllTags();

async function getEqUnit() {
    let data;
    try {
        data = await sanctClient(useRuntimeConfig().public.apiURL + '/equipment/' + q_id, {
            method: 'GET'
        })
    } catch (error) {
        if (error instanceof FetchError && error.response?.status === 404) {
            errorToast('Šis įrašas neegzistuoja.');
            await navigateTo('/');
        }

    }
    eqUnit.value.warehouse = data[0].warehouse_id;
    eqUnit.value.code = data[0].code;
    eqUnit.value.name = data[0].name;
    type.value = data[0].type;
    eqUnit.value.total = data[0].amount_total;
    eqUnit.value.unit_price = data[0].unit_price;
    eqUnit.value.rent_price = data[0].rent_price;
    eqUnit.value.profit = data[0].profit;
    eqUnit.value.problems = data[0].problems;
    img_name.value = data[0].img_name;
    img_mime.value = data[0].img_mime;
    img_base64.value = data[0].img_base64;

    selectedTags.value = data[0].tags;

    prevType.value = 'none'
}

async function getAllTags() {
    const data = await sanctClient(useRuntimeConfig().public.apiURL + '/tags', {
        method: 'GET'
    })

    allTags.value = data;
    allTypeTags.value = allTags.value.filter(res => res.category === type.value);

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
    }
})
async function handleSubmit() {

    if (!enabled) {
        await sanctClient(useRuntimeConfig().public.apiURL + '/equipment/' + q_id, {
            method: 'PUT',
            body: eqUnit.value
        });
    }
    else {
        const formData = new FormData();
        formData.append("warehouse_id", String(eqUnit.value.warehouse));
        formData.append("code", eqUnit.value.code);
        formData.append("name", eqUnit.value.name);
        formData.append("type", type.value);
        formData.append("amount_total", String(eqUnit.value.total));
        formData.append("unit_price", String(eqUnit.value.unit_price));
        formData.append("rent_price", String(eqUnit.value.rent_price));
        formData.append("problems", eqUnit.value.problems);
        formData.append("profit", String(eqUnit.value.profit));
        formData.append("img_name", file.value[0] != null ? (eqUnit.value.code + '.png') : '');
        formData.append("img_file", file.value[0]);
        formData.append("tag_ids", JSON.stringify(selectedTags.value.map(t => t.id)));
        await sanctClient(useRuntimeConfig().public.apiURL + '/equipment/' + q_id, {
            method: 'PUT',
            body: formData
        })
    }
}
</script>



<style scoped></style>