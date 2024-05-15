<template>
    <v-container justify="centre">
        <v-row justify="space-around">

            <v-card elevation="12" flex d-flex flex-column class="h-auto" sm="4" aspect-ratio="1" min-width="1192">
                <div id="app">
                    <v-card-title v-if="userPerms.admin || userPerms.warehouse_worker" class="pa-4">
                        <v-row>
                            <v-btn color="indigo-lighten-2" to="/equipment/new">Nauja įranga</v-btn>
                            <v-spacer />
                            <v-dialog v-model="dialog" max-width="650">
                                <template v-slot:activator="{ props: activatorProps }">
                                    <v-btn color="indigo-lighten-2" v-bind="activatorProps">Spausdinti QR kodus</v-btn>
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
                                                    <v-checkbox v-model="includeNames" label="Įtraukti įrangos
                                                        pavadinimus"></v-checkbox>
                                                </v-col>
                                                <v-list width="529">
                                                    <v-list-group v-for="item in equipment" :value="item">
                                                        <template v-slot:activator="{ props }">
                                                            <v-list-item v-bind="props" :title="item == equipment['audio'] ? 'Audio' :
                                                                item == equipment['video'] ? 'Video' :
                                                                    item == equipment['recording'] ? 'Įrašymo' :
                                                                        item == equipment['storage'] ? 'Dėžės/dėklai' :
                                                                            item == equipment['other'] ? 'Kita' : ''">
                                                            </v-list-item>
                                                        </template>
                                                        <v-card elevation="0">
                                                            <template v-slot:text>
                                                                <v-text-field v-model="search" label="Paieška"
                                                                    prepend-inner-icon="mdi-magnify" variant="outlined"
                                                                    hide-details single-line>
                                                                </v-text-field>
                                                            </template>
                                                            <v-data-table v-model="selected" :headers="qrHeaders"
                                                                :loading="loading" :items="item" :search="search"
                                                                show-select>
                                                                <template v-slot:item.img="{ item }">
                                                                    <v-img
                                                                        :src="'data:' + item.img_mime + ';base64,' + item.img_base64"
                                                                        :alt=item.img_name></v-img>
                                                                </template>
                                                            </v-data-table>
                                                        </v-card>

                                                    </v-list-group>
                                                </v-list>
                                                <v-col cols="12" align-self="end">
                                                    <v-btn type="submit">Patvirtinti</v-btn>
                                                </v-col>
                                            </v-row>
                                        </v-container>
                                    </v-form>
                                </v-card>
                            </v-dialog>
                        </v-row>
                    </v-card-title>
                    <v-list>
                        <v-list-group v-for="item in equipment" :value="item">
                            <template v-slot:activator="{ props }">
                                <v-list-item v-bind="props" :title="item == equipment['audio'] ? 'Audio' :
                                    item == equipment['video'] ? 'Video' :
                                        item == equipment['recording'] ? 'Įrašymo' :
                                            item == equipment['storage'] ? 'Dėžės/dėklai' :
                                                item == equipment['other'] ? 'Kita' : ''">
                                </v-list-item>
                            </template>
                            <v-card>
                                <template v-slot:text>
                                    <v-text-field v-model="search" label="Paeiška" prepend-inner-icon="mdi-magnify"
                                        variant="outlined" hide-details single-line>
                                    </v-text-field>
                                </template>
                                <v-data-table :headers="headers" :loading="loading" :items="item" :search="search">
                                    <template v-slot:item.img="{ item }">
                                        <v-img :src="'data:' + item.img_mime + ';base64,' + item.img_base64"
                                            :alt=item.img_name></v-img>
                                    </template>
                                    <template v-slot:item.warehouse_id="{ item }">
                                        <v-chip @click="navigateTo('/warehouses')">{{ item.warehouse_id
                                            }}</v-chip>
                                    </template>
                                    <template v-slot:item.actions="{ item }">
                                        <v-icon v-if="userPerms.admin || userPerms.warehouse_worker" class="me-2"
                                            size="small" @click="editEquipment(item)">
                                            mdi-pencil
                                        </v-icon>
                                        <v-dialog v-model="dialogDelete" width="auto">
                                            <template v-slot:activator="{ props: activatorProps }">
                                                <v-icon v-if="userPerms.warehouse_worker || userPerms.admin"
                                                    class="me-2" size="small" v-bind="activatorProps">
                                                    mdi-delete
                                                </v-icon>
                                            </template>
                                            <v-card>
                                                <v-card-title class="text-h5">Ar tikrai norite ištrinti šį įrangos
                                                    įrašą?</v-card-title>
                                                <v-card-actions>
                                                    <v-spacer></v-spacer>
                                                    <v-btn elevation="2" color="red-lighten-1"
                                                        @click="dialogDelete = false">Uždaryti</v-btn>
                                                    <v-btn elevation="2" color="indigo-lighten-2"
                                                        @click="deleteEquipment(item)">Ištrinti</v-btn>
                                                    <v-spacer></v-spacer>
                                                </v-card-actions>
                                            </v-card>
                                        </v-dialog>
                                        <v-icon size="small" @click="viewEquipment(item)">
                                            mdi-eye
                                        </v-icon>
                                    </template>
                                    <template v-slot:item.qr_code="{ item }">
                                        <qrcode-vue class="ma-2" :value="'/equipment/' + item.id" :size="75" level="H"
                                            background="#FFFFC1" />
                                    </template>
                                </v-data-table>
                            </v-card>

                        </v-list-group>
                    </v-list>

                </div>
            </v-card>
        </v-row>

    </v-container>
</template>
<script setup lang="ts">
import QrcodeVue from 'qrcode.vue'
import jsPDF from "jspdf";
import QRious from 'qrious';
import AmbleBold from '@/assets/fonts/Amble-bold.js';
import AmbleNormal from '@/assets/fonts/Amble-Regular-normal.js';

definePageMeta({
    middleware: ['sanctum:auth'],
    layout: 'default'
})
const client = useSanctumClient();
const dialogDelete = ref(false);
const equipment = ref([]);
const loading = ref(true);
const search = ref('');
const dialog = ref(false);
const allInOne = ref([]);
const qrSize = ref({ title: 'Vidutinis', size: 75 });
const includeNames = ref(true);
const sizes = ref([
    { title: 'Mažas', size: 50 },
    { title: 'Vidutinis', size: 75 },
    { title: 'Didelis', size: 100 },
])
const user = useSanctumUser();
const userPerms = ref({
    admin: user.value.roles.find(value => value.name == 'admin'),
    organiser: user.value.roles.find(value => value.name == 'organizer'),
    warehouse_worker: user.value.roles.find(value => value.name == 'warehouse_worker')
})
getEquipment();
const selected = ref([]);

const headers = ref([
    { title: "Sandėlis", key: "warehouse_id" },
    { title: "Kodas", key: "code" },
    { title: "Pavadinimas", key: "name" },
    { title: "Nuotrauka", key: "img" },
    { title: "Tipas", key: "type" },
    { title: "Kiekis", key: "amount_total" },
    { title: "Laisva", key: "amount_available" },
    { title: "Vieneto kaina", key: "unit_price" },
    { title: "Nuomos kaina", key: "rent_price" },
    { title: "Pelnas", key: "profit" },
    { title: "Trūkumai", key: "problems" },
    { title: "Žymės", key: "tags" },
    { title: "QR kodas", key: 'qr_code' },
    { title: "Veiksmai", key: "actions", sortable: "false" }
]);

const qrHeaders = ref([
    { title: "Pavadinimas", key: "name" },
    { title: "Paveiksliukas", key: "img" },
    { title: "Tipas", key: "type" },
    { title: "Žymės", key: "tags" },
]);

async function getEquipment() {
    const data = await client(useRuntimeConfig().public.apiURL + '/equipment', {
        method: 'GET',
        headers: { 'Accept': 'application/json' },
    })
    for (let i = 0; i < data.audio.length; i++) {
        const str = data.audio[i].tags.map(t => t.name).join(", ")
        data.audio[i].tags = str;
        allInOne.value.push(data.audio[i]);
    }
    for (let i = 0; i < data.video.length; i++) {
        const str = data.video[i].tags.map(t => t.name).join(", ")
        data.video[i].tags = str;
        allInOne.value.push(data.video[i]);
    }
    for (let i = 0; i < data.other.length; i++) {
        const str = data.other[i].tags.map(t => t.name).join(", ")
        data.other[i].tags = str;
        allInOne.value.push(data.other[i]);
    }
    for (let i = 0; i < data.recording.length; i++) {
        const str = data.recording[i].tags.map(t => t.name).join(", ")
        data.recording[i].tags = str;
        allInOne.value.push(data.recording[i]);
    }
    for (let i = 0; i < data.storage.length; i++) {
        const str = data.storage[i].tags.map(t => t.name).join(", ")
        data.storage[i].tags = str;
        allInOne.value.push(data.storage[i]);
    }
    equipment.value = data
    loading.value = false
};

async function deleteEquipment(item) {
    await client(useRuntimeConfig().public.apiURL + '/equipment/' + item.id, {
        method: 'DELETE'
    });
    const index = equipment.value.indexOf(item, 0);
    if (index > -1) {
        equipment.value.splice(index, 1);
    }
}

async function editEquipment(item) {
    await navigateTo('/equipment/' + item.id + '/edit');
}
async function viewEquipment(item) {
    await navigateTo('/equipment/' + item.id);
}
function printQrCodes() {
    const doc = new jsPDF();
    doc.setLineWidth(0.1);
    doc.addFileToVFS('Amble-Bold.ttf', AmbleBold)
    doc.addFileToVFS('Amble-Regular.ttf', AmbleNormal)
    doc.addFont('Amble-Bold.ttf', "Amble", "bold");
    doc.addFont('Amble-Regular.ttf', "Amble", "normal");
    doc.setFont("Amble", "normal");
    console.log(doc.getFontList())
    doc.setLineDashPattern([2.5, 1], 0);
    var x: number = 105;
    var y: number = 5;
    if (includeNames.value == true) {
        selected.value.forEach(function (selEq) {
            var eqUnit = allInOne.value.find(eq => eq.id == selEq);
            if (y + qrSize.value.size + 10 > 285 && eqUnit) {
                doc.addPage("a4", "portrait");
                doc.setLineDashPattern([2.5, 1], 0);
                doc.setLineWidth(0.1);
                doc.setFont("Amble", "normal");
                y = 5;
            }
            const qr = new QRious({ size: qrSize.value.size, value: '/equipment/' + selEq, level: 'H', background: 'green', backgroundAlpha: 0.2 });
            doc.text(eqUnit.name, x, y, { align: "center" });
            y = y + 5;
            doc.addImage(qr.toDataURL(), x - qrSize.value.size / 2, y, qrSize.value.size, qrSize.value.size);
            y = y + qrSize.value.size + 5;
            doc.line(10, y, 200, y);
            y = y + 7;
        });
        doc.save('irangos_qr_kodai.pdf');
    }
    else {

    }
}
</script>

<style scoped></style>