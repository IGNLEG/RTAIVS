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
                            <v-text-field v-model="vehUnit.license_plate" label="Valstybinis nr." required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-select v-model="vehUnit.make" label="Markė"
                                :items="['Mercedes', 'Renault', 'Opel/Vauxhall', 'Ford', 'Volkswagen']"></v-select>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="vehUnit.model" label="Modelis" required></v-text-field>
                        </v-col>                        
                        <v-col cols="12">
                            <v-select v-model="vehUnit.gearbox" label="Pavarų dėžė"
                                :items="['Manual', 'Automatic']"></v-select>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="vehUnit.mileage" label="Rida" type="number" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="vehUnit.mpg" label="Kuro sąnaudos" type="number" step="0.1"
                                required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="vehUnit.amount_total" label="Kiekis" type="number"
                                readonly></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="vehUnit.amount_available" label="Laisva" type="number"
                                readonly></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="vehUnit.price_per_km" label="€/km" step="0.01"
                                required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="vehUnit.problems" label="Trūkumai/problemos" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="vehUnit.insurance_until" label="Draudimas iki" type="date"
                                required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="vehUnit.inspection_until" label="TA iki" type="date"
                                required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-btn type="submit">Patvirtinti</v-btn>
                        </v-col>
                    </v-row>
                </v-container>

            </v-form>
        </v-card>
    </div>
</template>

<script setup lang="ts">
import { VBtn } from 'vuetify/components';
import { FetchError } from 'ofetch';

definePageMeta({
    middleware: ['sanctum:auth', 'warehouse-worker'],
    layout: 'default'
})
const { errorToast } = useToast()
const warehouse = ref({
    id: 0,
    name: ''
});
const warehouses = ref([])

const vehUnit = ref({
    warehouse_id: 0,
    license_plate: '',
    make: '',
    model: '',
    gearbox: '',
    mileage: 0,
    mpg: 0,
    amount_total: 1,
    amount_available: 0,
    price_per_km: 0,
    problems: '',
    insurance_until: '',
    inspection_until: '',
})
const client = useSanctumClient();
const { v_id } = useRoute().params;

getVehicle();
getWarehouses();
async function getVehicle() {
    let data;
    try {
        data = await client(useRuntimeConfig().public.apiURL + '/vehicles/' + v_id, {
            method: 'GET'
        })
    } catch (error) {
        if (error instanceof FetchError && error.response?.status === 404) {
            errorToast('Šis įrašas neegzistuoja.');
            await navigateTo('/');
        }

    }

    vehUnit.value.warehouse_id = data.warehouse_id;
    vehUnit.value.make = data.make;
    vehUnit.value.license_plate = data.license_plate;
    vehUnit.value.model = data.model;
    vehUnit.value.gearbox = data.gearbox;
    vehUnit.value.mileage = data.mileage;
    vehUnit.value.mpg = data.mpg;
    vehUnit.value.amount_total = data.amount_total;
    vehUnit.value.amount_available = data.amount_available;
    vehUnit.value.price_per_km = data.price_per_km;
    vehUnit.value.problems = data.problems;
    vehUnit.value.insurance_until = data.insurance_until;
    vehUnit.value.inspection_until = data.inspection_until;
}
async function getWarehouses() {
    const data = await client(useRuntimeConfig().public.apiURL + '/warehouses', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })

    warehouses.value = data.map(t => ({ id: t.id, short_name: t.short_name, address: t.address }));
    warehouse.value.id = warehouses.value[0].id;
    warehouse.value.name = warehouses.value[0].short_name + ', ' + warehouses.value[0].address;
}
async function handleSubmit() {
    try {
        vehUnit.value.warehouse_id = warehouse.value.id;
        await client(useRuntimeConfig().public.apiURL + '/vehicles/' + v_id, {
            method: 'PUT',
            body: vehUnit.value
        });
    } catch (error) {
        if (error instanceof FetchError && error.response?.status === 422) {
            errorToast('Klaida atnaujinant informaciją.');
        }

    }

}
</script>



<style scoped></style>