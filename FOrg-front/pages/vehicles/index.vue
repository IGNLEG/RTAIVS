<template>
    <v-container justify="centre">
        <v-row justify="space-around">

            <v-card elevation="12" flex d-flex flex-column class="h-auto" sm="4" style="
                   height: 250px" aspect-ratio="1">
                <div id="app">
                    <v-card-title v-if="userPerms.admin || userPerms.warehouse_worker" class="pa-4">
                        <v-row>
                            <v-btn v-if="userPerms.warehouse_worker || userPerms.admin" color="indigo-lighten-2"
                                to="/vehicles/new">Naujas Automobilis</v-btn>
                            <v-spacer />
                            <v-dialog v-model="dialog" max-width="650">
                                <template v-slot:activator="{ props: activatorProps }">
                                    <v-btn color="indigo-lighten-2" v-bind="activatorProps">Spausdinti QR
                                        kodus</v-btn>
                                </template>
                                <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="650" rounded="lg"
                                    title="QR kodų spausdinimas">
                                    <v-form style="width: 100%" @submit.prevent="printQrCodes">
                                        <v-container>
                                            <span>QR kodų dydis:</span>
                                            <v-row>
                                                <v-col cols="12">
                                                    <v-select v-model="qrSize" :hint="`${qrSize.title}, ${qrSize.size}`"
                                                        :items="sizes" item-title="title" item-value="size"
                                                        label="Select" persistent-hint return-object
                                                        single-line></v-select>
                                                </v-col>
                                                <v-col cols="12">
                                                    <v-checkbox v-model="includeNames" label="Įtraukti automobilių
                                                        pavadinimus"></v-checkbox>
                                                </v-col>
                                                <v-card elevation="0">
                                                    <template v-slot:text>
                                                        <v-text-field v-model="search" label="Paieška"
                                                            prepend-inner-icon="mdi-magnify" variant="outlined"
                                                            hide-details single-line>
                                                        </v-text-field>
                                                    </template>
                                                    <v-data-table v-model="selected" :headers="headersQr" :loading="loading"
                                                        :items="vehicles" :mobile="null" mobile-breakpoint="sm"
                                                        :items-per-page="5" show-select> 
                                                        <template v-slot:loading>
                                                            <v-skeleton-loader type="table-row@10"></v-skeleton-loader>
                                                        </template>
                                                        <template v-slot:item.warehouse_id="{ item }">
                                                            <v-chip @click="navigateTo('/warehouses')">{{
                                                                item.warehouse_id
                                                                }}</v-chip>
                                                        </template>
                                                    </v-data-table>
                                                </v-card>
                                                <v-col cols="12" align-self="end">
                                                    <v-btn  color="indigo-lighten-2" type="submit">Patvirtinti</v-btn>
                                                </v-col>
                                            </v-row>
                                        </v-container>
                                    </v-form>
                                </v-card>
                            </v-dialog>
                        </v-row>
                    </v-card-title>
                    <v-data-table :headers="headers" :loading="loading" :items="vehicles" :mobile="null"
                        mobile-breakpoint="sm" :items-per-page="5">
                        <template v-slot:loading>
                            <v-skeleton-loader type="table-row@10"></v-skeleton-loader>
                        </template>
                        <template v-slot:item.warehouse_id="{ item }">
                            <v-chip @click="navigateTo('/warehouses')">{{ item.warehouse_id
                                }}</v-chip>
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-icon v-if="userPerms.warehouse_worker || userPerms.admin" class="me-2" size="small"
                                @click="editVehicle(item)">
                                mdi-pencil
                            </v-icon>
                            <v-dialog v-model="dialogDelete" width="auto">
                                <template v-slot:activator="{ props: activatorProps }">
                                    <v-icon v-if="userPerms.warehouse_worker || userPerms.admin" class="me-2" size="small" v-bind="activatorProps">
                                        mdi-delete
                                    </v-icon>
                                </template>
                                <v-card>
                                    <v-card-title class="text-h5">Ar tikrai norite ištrinti šį automobilio
                                        įrašą?</v-card-title>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn color="primary" variant="text"
                                            @click="dialogDelete = false">Uždaryti</v-btn>
                                        <v-btn color="error" variant="text"
                                            @click="deleteVehicle(item)">Ištrinti</v-btn>
                                        <v-spacer></v-spacer>
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>
                            <v-icon size="small" @click="viewVehicle(item)">
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
import jsPDF from "jspdf";
import QRious from 'qrious';
import AmbleBold from '@/assets/fonts/Amble-bold.js';
import AmbleNormal from '@/assets/fonts/Amble-Regular-normal.js';
definePageMeta({
    middleware: ['sanctum:auth'],
    layout: 'default'
})
const dialogDelete = ref(false);

const client = useSanctumClient();
const vehicles = ref([]);
const search = ref('');
const loading = ref(true);
const dialog = ref(false);
const qrSize = ref({ title: 'Vidutinis', size: 75 });
const selected = ref([]);
const includeNames = ref(true);
const sizes = ref([
    { title: 'Mažas', size: 50 },
    { title: 'Vidutinis', size: 75 },
    { title: 'Didelis', size: 100 },
])
getVehicles();
const user = useSanctumUser();
const userPerms = ref({
    admin: user.value.roles.find(value => value.name == 'admin'),
    organiser: user.value.roles.find(value => value.name == 'organizer'),
    warehouse_worker: user.value.roles.find(value => value.name == 'warehouse_worker')
})
const headers = ref([
    { title: "Sandėlis", key: "warehouse_id" },
    { title: "Markė", key: "make" },
    { title: "Modelis", key: "model" },
    { title: "Kiekis", key: "amount_total" },
    { title: "Laisva", key: "amount_available" },
    { title: "Pavarų dėžė", key: "gearbox" },
    { title: "Rida", key: "mileage" },
    { title: "Kuro sąnaudos", key: "mpg" },
    { title: "€/km", key: "price_per_km" },
    { title: "Pelnas, €", key: "profit" },
    { title: "Trūkumai/problemos", key: "problems" },
    { title: "Draudimas iki", key: "insurance_until" },
    { title: "TA iki", key: "inspection_until" },
    { title: 'Veiksmai', key: 'actions', sortable: false },
]);
const headersQr = ref([
    { title: "Sandėlis", key: "warehouse_id" },
    { title: "Markė", key: "make" },
    { title: "Modelis", key: "model" },
]);

async function getVehicles() {
    const data = await client(useRuntimeConfig().public.apiURL + '/vehicles', {
        method: 'GET',
        headers: { 'Accept': 'application/json' },
    })
    vehicles.value = data
    loading.value = false
};

async function deleteVehicle(item) {
    await client(useRuntimeConfig().public.apiURL + '/vehicles/' + item.id, {
        method: 'DELETE'
    });
    const index = vehicles.value.indexOf(item, 0);
    if (index > -1) {
        vehicles.value.splice(index, 1);
    }
}
async function viewVehicle(item) {
    await navigateTo('/vehicles/' + item.id);
}
async function editVehicle(item) {
    await navigateTo('/vehicles/' + item.id + '/edit');
}

function printQrCodes() {
    const doc = new jsPDF();
    doc.setLineWidth(0.1);
    doc.addFileToVFS('Amble-Bold.ttf', AmbleBold)
    doc.addFileToVFS('Amble-Regular.ttf', AmbleNormal)
    doc.addFont('Amble-Bold.ttf', "Amble", "bold");
    doc.addFont('Amble-Regular.ttf', "Amble", "normal");
    doc.setFont("Amble", "normal");
    doc.setLineDashPattern([2.5, 1], 0);
    var x: number = 105;
    var y: number = 5;
    if (includeNames.value == true) {
        selected.value.forEach(function (selVeh) {
            var vehUnit = vehicles.value.find(veh => veh.id == selVeh);
            if (y + qrSize.value.size + 10 > 285 && vehUnit) {
                doc.addPage("a4", "portrait");
                doc.setLineDashPattern([2.5, 1], 0);
                doc.setLineWidth(0.1);
                doc.setFont("Amble", "normal");
                y = 5;
            }
            const qr = new QRious({ size: qrSize.value.size, value: '/vehicles/' + selVeh, level: 'H', background: 'green', backgroundAlpha: 0.2 });
            doc.text(vehUnit.make + ' ' + vehUnit.model + ' ' + vehUnit.license_plate, x, y, { align: "center" });
            y = y + 5;
            doc.addImage(qr.toDataURL(), x - qrSize.value.size / 2, y, qrSize.value.size, qrSize.value.size);
            y = y + qrSize.value.size + 5;
            doc.line(10, y, 200, y);
            y = y + 7;
        });
        doc.save('transporto_qr_kodai.pdf');
    }
    else {

    }
}
</script>

<style scoped></style>