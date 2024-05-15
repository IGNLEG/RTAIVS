<template>

    <v-container justify="centre">
        <v-row justify="space-around">

            <v-card elevation="12" flex d-flex flex-column class="h-auto" sm="4" style="
                   height: 250px" aspect-ratio="1">
                <div id="app">
                    <v-btn class="ml-1 mt-1" color="indigo-lighten-2" to="/events/new">Naujas Renginys</v-btn>
                    <v-data-table :headers="headers" :loading="loading" :items="events" :items-per-page="itemsPerPage"
                        :mobile="null" mobile-breakpoint="sm">
                        <template v-slot:loading>
                            <v-skeleton-loader type="table-row@10"></v-skeleton-loader>
                        </template>
                        <template v-slot:item.user_id="{ item }">
                            <v-chip @click="navigateTo('/users/' + item.user_id)">{{ item.user_id }}</v-chip>
                        </template>
                        <template v-slot:item.client_id="{ item }">
                            <v-chip @click="navigateTo('/clients/' + item.client_id)">{{ item.client_id }}</v-chip>
                        </template>
                        <template v-slot:item.event_type="{ item }">
                            {{ translate.translation(item.event_type) }}
                        </template>
                        <template v-slot:item.event_subtype="{ item }">
                            {{ translate.translation(item.event_subtype) }}
                        </template>
                        <template v-slot:item.event_status="{ item }">
                            {{ translate.translation(item.event_status) }}
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-icon class="me-2" size="small" @click="editEvent(item)">
                                mdi-pencil
                            </v-icon>
                            <v-dialog v-model="dialogDelete" width="auto">
                                <template v-slot:activator="{ props: activatorProps }">
                                    <v-icon v-if="userPerms.organiser || userPerms.admin" class="me-2" size="small"
                                        v-bind="activatorProps">
                                        mdi-delete
                                    </v-icon>
                                </template>
                                <v-card>
                                    <v-card-title class="text-h5">Ar tikrai norite ištrinti šį renginį?</v-card-title>
                                    <v-card-text>
                                        <v-text-field v-model="reason" label="Priežastis"
                                            :rules="[rules.required]"></v-text-field>
                                    </v-card-text>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn elevation="2" color="red-lighten-1"
                                            @click="dialogDelete = false">Uždaryti</v-btn>
                                        <v-btn elevation="2" color="indigo-lighten-2"
                                            @click="deleteEvent(item)">Ištrinti</v-btn>
                                        <v-spacer></v-spacer>
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>
                            <v-icon size="small" @click="viewEvent(item)">
                                mdi-eye
                            </v-icon>

                            <v-dialog v-if="userPerms.admin" v-model="dialog" max-width="448">
                                <template v-slot:activator="{ props: activatorProps }">
                                    <v-icon class="ml-2" size="small" v-bind="activatorProps">
                                        mdi-invoice-edit
                                    </v-icon>
                                </template>
                                <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg"
                                    title="Sukurti sąskaitą-faktūrą">
                                    <v-form style="width: 100%" @submit.prevent="createInvoice(item)">
                                        <v-container>
                                            <v-row>
                                                <v-col cols="12">
                                                    <v-text-field id="inv-1"v-model="distance" label="Nuvažiuotas atstumas, km"
                                                        required></v-text-field>
                                                </v-col>
                                                <v-checkbox v-model="manualClient"
                                                    label="Įvesti kliento informaciją ranka"></v-checkbox>
                                                <v-col v-if="manualClient" cols="12">
                                                    <v-text-field v-model="invClient.name" label="Vardas/pavadinimas"
                                                        required></v-text-field>
                                                </v-col>
                                                <v-col v-if="manualClient" cols="12">
                                                    <v-text-field v-model="invClient.email" label="El. paštas"
                                                        required></v-text-field>
                                                </v-col>
                                                <v-col v-if="manualClient" cols="12">
                                                    <v-text-field v-model="invClient.phone" label="Tel. nr."
                                                        required></v-text-field>
                                                </v-col>
                                                <v-col v-if="manualClient" cols="12">
                                                    <v-text-field v-model="invClient.repCompany"
                                                        label="Atstovauja įmonei" required></v-text-field>
                                                </v-col>
                                                <v-col cols="12">
                                                    <div v-for="(textField, i) in textFields" :key="i">
                                                        <v-col :id="i.toString()" cols="12">
                                                            <v-text-field :label="textField.label1"
                                                                v-model="textField.value1"></v-text-field>
                                                        </v-col>
                                                        <v-col :id="(i+1).toString()" cols="16">
                                                            <v-text-field :label="textField.label2"
                                                                v-model="textField.value2"></v-text-field>
                                                        </v-col>
                                                        <v-col :id="(i+2).toString()" cols="12">
                                                            <v-text-field :label="textField.label3"
                                                                v-model="textField.value3"></v-text-field>
                                                        </v-col>

                                                        <v-btn @click="remove(i)" color="red-lighten-1">Ištrinti
                                                            Paslaugą</v-btn>
                                                    </div>
                                                </v-col>

                                                <v-col cols="12">
                                                    <v-btn @click="add" elevation="2" color="green-lighten-1">Pridėti
                                                        Paslaugą</v-btn>
                                                </v-col>
                                                <v-col cols="12" align-self="end">
                                                    <v-btn elevation="2" color="indigo-lighten-2"
                                                        type="submit">Patvirtinti</v-btn>
                                                </v-col>
                                            </v-row>
                                        </v-container>
                                    </v-form>
                                </v-card>
                            </v-dialog>
                        </template>
                        <template v-slot:item.qr_code="{ item }">
                            <qrcode-vue @click="navigateTo('/events/' + item.id)" class="ma-3"
                                :value="'/events/' + item.id" :size="75" level="H" />
                        </template>
                        <template v-slot:bottom>
                            <div class="text-center pt-2">
                                <v-pagination v-model="currentPage" :length="toPage" @update:modelValue="getEvents()">
                                </v-pagination>
                            </div>
                        </template>
                    </v-data-table>
                </div>
            </v-card>
        </v-row>
    </v-container>

</template>
<script setup lang="ts">
import QrcodeVue from 'qrcode.vue'
import JsPDF from 'jspdf'
import { generatePdfReceipt } from 'dynamic-jspdf-receipt'
import AmbleNormal from '@/assets/fonts/Amble-Regular-normal.js';
import { useTranslations } from '~/composables/useTranslations';
import { FetchError } from 'ofetch';


definePageMeta({
    middleware: ['sanctum:auth', 'organiser'],
    layout: 'default'
})
const rules = useRules();
const translate = useTranslations();
const user = useSanctumUser();
const userPerms = ref({
    admin: user.value.roles.find(value => value.name == 'admin'),
    organiser: user.value.roles.find(value => value.name == 'organizer'),
    warehouse_worker: user.value.roles.find(value => value.name == 'warehouse_worker')
})
const reason = ref('');
const manualClient = ref(false);
const dialog = ref(false);
const distance = ref(0);
const itemsPerPage = ref(1);
const dialogDelete = ref(false);
const fromPage = ref(1);
const toPage = ref(1);
const client = useSanctumClient();
const events = ref([]);
const loading = ref(true);
const currentPage = ref(1);
const textFields = ref([]);
const { errorToast } = useToast()
const invClient = ref({
    name: "",
    phone: "",
    email: "",
    repCompany: "",
})
getEvents();
const headers = ref([
    { title: "Vieta", key: "location" },
    { title: "Sukūrėjo ID", key: "user_id" },
    { title: "Kliento ID", key: "client_id" },
    { title: "Pradžia", key: "start_date" },
    { title: "Pabaiga", key: "end_date" },
    { title: "Tipas", key: "event_type" },
    { title: "Potipis", key: "event_subtype" },
    { title: "Būsena", key: 'event_status' },
    { title: "QR kodas", key: 'qr_code' },
    { title: 'Veiksmai', key: 'actions', sortable: false },
]);

async function getEvents() {
    const data = await client(useRuntimeConfig().public.apiURL + '/events?page=' + currentPage.value, {
        method: 'GET',
        headers: { 'Accept': 'application/json' },
    })
    console.log('data', data)
    events.value = data.data;
    itemsPerPage.value = data.per_page;
    fromPage.value = data.from;
    toPage.value = data.last_page;
    loading.value = false
};

function add() {
    textFields.value.push({
        label1: "Paslauga",
        value1: "",
        label2: "Kaina",
        value2: "",
        label3: "Kiekis",
        value2: "",
    });
}

function remove(index) {
    textFields.value.splice(index, 1);
}
async function deleteEvent(item) {
    console.log("reason", reason.value)
    if (!(reason.value == "")) {
        let users;
        await client(useRuntimeConfig().public.apiURL + '/events/' + item.id, {
            method: 'DELETE'
        });
        try {
            users = await client(useRuntimeConfig().public.apiURL + '/events/staff?event_id=' + item.id, {
                method: 'GET',
            });
            for (let i = 0; i < users.value.length; i++) {
            await client(useRuntimeConfig().public.apiURL + '/inbox', {
                method: 'POST',
                body: {
                    recipient_name: users[i].name,
                    message: 'Jūs buvote atsietas nuo renginio, kuris turėjo vykti' + item.start_date + ' - ' + item.end_date + '. ' + 'Atšaukimo priežastis: ' + reason.value,
                    replied_to: ''
                }
            })
        }
        } catch (error) {
            if (error instanceof FetchError && error.response?.status === 404) {
                // here you can extract errors from a response 
                // and put it in your form for example
                errorToast('Žinutės neišsiųstos, nes renginiui nebuvo priskirti darbuotojai');
            }
        }

        

        const index = events.value.indexOf(item, 0);
        if (index > -1) {
            events.value.splice(index, 1);
        }
        dialogDelete.value = false;
    }
    else {
        errorToast('Privalote įvesti atšaukimo priežastį.');
    }

}

async function editEvent(item) {
    await navigateTo('/events/' + item.id + '/edit');
}
async function viewEvent(item) {
    await navigateTo('/events/' + item.id);
}
async function createInvoice(item) {

    console.log(textFields.value)
    const extraServices = textFields.value.map(t => [t.value1, t.value2, t.value3, t.value2 * t.value3]);
    if (!manualClient.value) {
        const clientInfo = await client(useRuntimeConfig().public.apiURL + '/clients/' + item.client_id, {
            method: 'GET'
        })

        invClient.value.name = clientInfo.name + ' ' + clientInfo.surname;
        invClient.value.email = clientInfo.email;
        invClient.value.phone = clientInfo.phone;
        invClient.value.repCompany = clientInfo.company_id;
    }


    const data = await client(useRuntimeConfig().public.apiURL + '/events/' + item.id + '/equipment', {
        method: 'GET'
    })
    const vehs = await client(useRuntimeConfig().public.apiURL + '/events/' + item.id + '/vehicles', {
        method: 'GET'
    })
    const vehInvData = vehs.all_veh.map(t => [t.make + ' ' + t.model, distance.value, t.price_per_km, t.price_per_km * distance.value]);
    const invoiceData = data.for_invoice.map(t => [t.name, t.equipment_amount, t.rent_price, t.rent_price * t.equipment_amount]);
    invoiceData.push(...vehInvData);
    const total = ref(0);
    for (let i = 0; i < data.for_invoice.length; i++) {
        total.value = +total.value + (+data.for_invoice[i].rent_price * +data.for_invoice[i].equipment_amount)
    }
    for (let i = 0; i < vehs.all_veh.length; i++) {
        total.value = +total.value + (+vehs.all_veh[i].price_per_km * distance.value)
    }

    for (let i = 0; i < extraServices.length; i++) {
        total.value = +total.value + extraServices[i][3];
        invoiceData.push(extraServices[i]);
    }

    const doc = new JsPDF({
        compress: true,
    });

    doc.addFileToVFS('Amble-Regular.ttf', AmbleNormal)
    doc.addFont('Amble-Regular.ttf', "Amble", "normal");
    doc.setFont("Amble", "normal");
    console.log(doc.getFontList())
    const current = new Date();
    generatePdfReceipt(doc, {
        logo: {
            source: new URL(`assets/logo.png`, import.meta.url).href,
            extension: 'PNG',
            ratioWidthPerHeigh: 2.5,
        },
        companyDetails: [
            {
                name: 'Įmonė',
                value: 'Festukas',
            },
            {
                name: 'Įmonės kodas',
                value: 'kodas',
            },
            {
                name: 'PVM mokėtojo kodas',
                value: 'kodas',
            },
            {
                name: 'Įmonės adresas',
                value: 'Naujoji Kelmė',
            },
            {
                name: 'Įmonės tel. nr.',
                value: '+37066666666',
            },
        ],
        receiptDetails: [
            {
                name: 'Sąskaitos nr.',
                value: item.id,
            },
            {
                name: 'Klientas',
                value: "",
            },
            {
                name: 'Vardas, pavardė',
                value: invClient.value.name,
            },
            {
                name: 'El. paštas',
                value: invClient.value.email,
            },
            {
                name: 'Tel. nr.',
                value: invClient.value.phone,
            },
            {
                name: 'Įmonė',
                value: invClient.value.repCompany,
            },
            {
                name: 'Išrašymo data',
                value: `${current.getDate()}/${current.getMonth() + 1}/${current.getFullYear()}`,
            },
        ],
        purchaseTable: {
            headers: ['Paslauga', 'Kiekis', 'Kaina/vnt., eur', 'Viso, eur'],
            items: invoiceData,
            headerColor: [0, 95, 173],
        },
        additionalInfo: ['Ačių, kad rinkotės mūsų paslaugas.\n Ši sąskaita sugeneruota automatiniu būdų ir galioja be parašo.'],
        ammountDetails: [
            {
                name: 'Bendra suma',
                value: total.value.toString() + ' eur',
            },
        ],
    });

    doc.save('table.pdf')
    dialog.value = false;
}
</script>

<style scoped></style>