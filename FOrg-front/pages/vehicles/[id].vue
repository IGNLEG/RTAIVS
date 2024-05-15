<template>
    <v-card class="mx-auto pa-12 pb-8" elevation="12">
            <v-container>
            <v-row>
                <v-col cols="12">
                    <v-text-field v-model="vehUnit.make" label="Markė" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="vehUnit.model" label="Modelis" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="vehUnit.gearbox" label="Pavarų dėžė" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="vehUnit.mileage" label="Rida" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="vehUnit.mpg" label="Kuro sąnaudos" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="vehUnit.total" label="Kiekis" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="vehUnit.available" label="Laisva" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="vehUnit.p_km" label="€/km" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="vehUnit.profit" label="Pelnas" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="vehUnit.problems" label="Trūkumai/problemos" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="vehUnit.insurance" label="Draudimas iki" type="datetime" readonly hide-details></v-text-field>
                </v-col>
                <v-col cols="12">
                    <v-text-field v-model="vehUnit.inspection" label="TA iki" type="datetime" readonly hide-details></v-text-field>
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
const { errorToast } = useToast()

const { id } = useRoute().params;

const vehUnit = ref({
    make: '',
    model: '',
    gearbox: '',
    mileage: 0,
    mpg: 0,
    total: 0,
    available: 0,
    p_km: 0,
    profit: 0,
    problems: '',
    insurance: '',
    inspection: '',
})
const sanctClient = useSanctumClient();

getVehicle();

async function getVehicle() {
    let data;
    try{
    data = await sanctClient(useRuntimeConfig().public.apiURL + '/vehicles/' + id, {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })
    }catch (error) {
        if (error instanceof FetchError && error.response?.status === 404) {           
                errorToast('Šis įrašas neegzistuoja.');
                await navigateTo('/');
           }
        
    }
       
    vehUnit.value.make = data.make;
    vehUnit.value.model = data.model;
    vehUnit.value.gearbox = data.gearbox;
    vehUnit.value.mileage = data.mileage;
    vehUnit.value.mpg = data.mpg;
    vehUnit.value.total = data.amount_total;
    vehUnit.value.available = data.amount_available;
    vehUnit.value.p_km = data.price_per_km;
    vehUnit.value.profit = data.profit;
    vehUnit.value.problems = data.problems;
    vehUnit.value.insurance = data.insurance_until;
    vehUnit.value.inspection = data.inspection_until;


}
</script>



<style scoped></style>