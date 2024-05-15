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
                            <v-text-field v-model="license_plate" label="Valstybinis nr." required></v-text-field>
                        </v-col>                  
                        <v-col cols="12">
                            <v-select v-model="make" label="Markė"
                                :items="['Mercedes', 'Renault', 'Opel/Vauxhall', 'Ford', 'Volkswagen']"></v-select>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="model" label="Modelis" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-select v-model="gearbox" label="Pavarų dėžė"
                                :items="['Manual', 'Automatic']"></v-select>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="mileage" label="Rida" type="number" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="mpg" label="Kuro sąnaudos" type="number" step="0.1" required></v-text-field>
                        </v-col>                        
                        <v-col cols="12">
                            <v-text-field v-model="p_km" label="€/km" type="number" step="0.01" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="problems" label="Trūkumai/problemos" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="insurance" label="Draudimas iki" type="date" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="inspection" label="TA iki" type="date" required></v-text-field>
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
const make = ref('');
const model = ref('');
const gearbox = ref('');
const mileage = ref(0);
const mpg = ref(0);
const p_km = ref(0);
const problems = ref('');
const insurance = ref('');
const inspection = ref('');
const license_plate = ref('');
const warehouse = ref({
    id: 0,
    name: ''
});
const warehouses = ref([])

const client = useSanctumClient();

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
async function handleSubmit() {
    await client(useRuntimeConfig().public.apiURL + '/vehicles', {
        method: 'POST',
        body: {
            warehouse_id: warehouse.value.id,
            license_plate: license_plate.value,
            make: make.value,
            model: model.value,
            amount_total: 1,
            amount_available: 1,
            gearbox: gearbox.value,
            mileage: mileage.value,
            mpg: mpg.value,
            price_per_km: p_km.value,
            problems: problems.value,
            insurance_until: insurance.value,
            inspection_until: inspection.value,
        }
    })
    window.history.length > 1 ? useRouter().go(-1) : await navigateTo('/vehicles')
}

</script>

<style scoped></style>