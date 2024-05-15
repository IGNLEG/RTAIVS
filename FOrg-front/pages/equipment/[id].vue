<template>
    <v-card class="mx-auto pa-12 pb-8" elevation="12">
        <v-container>
            <v-row>
                <v-col cols="12">
                    <v-text-field v-model="eqUnit.code" label="Kodas" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="eqUnit.name" label="Pavadinimas" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="eqUnit.type" label="Tipas" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="eqUnit.total" label="Kiekis" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="eqUnit.available" label="Laisva" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="eqUnit.unit_price" label="Vieneto kaina" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="eqUnit.rent_price" label="Nuomos kaina" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="eqUnit.profit" label="Pelnas" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="eqUnit.problems" label="Trūkumai/problemos" readonly hide-details></v-text-field>
                </v-col>
            </v-row>
        </v-container>
    </v-card>
</template>

<script setup lang="ts">
import { FetchError } from 'ofetch';

definePageMeta({
    middleware: ['sanctum:auth'],
    layout: 'default'
})

const { id } = useRoute().params;
const { errorToast } = useToast()

const eqUnit = ref({
    id: 0,
    code: '',
    name: '',
    type: '',
    total: 0,
    available: 0,
    unit_price: 0,
    rent_price: 0,
    profit: 0,
    problems: '',
})
const sanctClient = useSanctumClient();

getEqUnit();

async function getEqUnit() {
    let data;
    try {
        data = await sanctClient(useRuntimeConfig().public.apiURL + '/equipment/' + id, {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        })
    } catch (error) {
        if (error instanceof FetchError && error.response?.status === 404) {
            errorToast('Šis įrašas neegzistuoja.');
            await navigateTo('/');
        }

    }

    eqUnit.value.id = data[0].id;
    eqUnit.value.code = data[0].code;
    eqUnit.value.name = data[0].name;
    eqUnit.value.type = data[0].type;
    eqUnit.value.total = data[0].amount_total;
    eqUnit.value.available = data[0].amount_available;
    eqUnit.value.unit_price = data[0].unit_price;
    eqUnit.value.rent_price = data[0].rent_price;
    eqUnit.value.profit = data[0].profit;
    eqUnit.value.problems = data[0].problems;
    console.log('eq', eqUnit)
}
</script>



<style scoped></style>