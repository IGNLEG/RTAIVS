<template>
    <v-stepper :items="stepperHeaders" show-actions :mobile="smAndDown">

        <template v-slot:item.1>
            <h3 class="text-h6">Įveskite bendrąją renginio informacija</h3>

            <br>

            <v-sheet border>
                <v-container>
                    <v-row>
                        <v-col>
                            <v-col cols="12">
                                <v-select id="evedit-1" v-model="client" :items="clients" label="Klientas"
                                    item-title="name" item-value="id" return-object>
                                </v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field id="evedit-2" v-model="phone" label="Kontaktinis nr."
                                    type="text"></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field id="evedit-0" v-model="location" label="Vieta"></v-text-field>
                            </v-col>
                            <iframe width="400" height="450" style="border:0" loading="lazy" allowfullscreen
                                referrerpolicy="no-referrer-when-downgrade"
                                :src="'https://www.google.com/maps/embed/v1/place?key=AIzaSyC9L4qKGsl0r1O34wBNknqd2TGMg1h0zLQ&q=' + encodeURI(location)">
                            </iframe>
                        </v-col>
                        <v-col>

                            <v-col cols="12">
                                <v-text-field id="evedit-3" v-model="start_date" label="Pradžia"
                                    type="datetime-local"></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field id="evedit-4" v-model="end_date" label="Pabaiga"
                                    type="datetime-local"></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-select id="evedit-5" v-model="event_type" label="Tipas" item-title="name"
                                    item-value="value" :items="event_types"></v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-select id="evedit-6" v-model="event_subtype" label="Potipis" item-title="name"
                                    item-value="value" :items="event_subtypes"></v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-select id="evedit-7" v-model="event_status" label="Būsena" item-title="name"
                                    item-value="value" :items="event_statuses"></v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field id="evedit-8" v-model="description" label="Aprašymas"
                                    type="text"></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-file-input id="evedit-9" v-model="file" label="Failas"></v-file-input>
                            </v-col>
                        </v-col>

                    </v-row>
                </v-container>
            </v-sheet>
        </template>

        <template v-slot:item.2>
            <h3 class="text-h6">Pasirinkite reikalingą įrangą ir transportą</h3>

            <br>

            <v-row class="d-flex flex-wrap ga-3" no-gutters>
                <v-col>
                    <v-card>
                        <v-list>
                            <v-list-group>
                                <template v-slot:activator="{ props }">
                                    <v-list-item v-bind="props" title="Įranga"></v-list-item>
                                </template>
                                <v-list-group v-for="item in equipment" :value="item">
                                    <template v-slot:activator="{ props }">
                                        <v-list-item v-bind="props" :title="item == equipment['audio'] ? 'Audio' :
                                            item == equipment['video'] ? 'Video' :
                                                item == equipment['recording'] ? 'Garso Įrašymo' :
                                                    item == equipment['storage'] ? 'Dėžės/dėklai' :
                                                        item == equipment['other'] ? 'Kita' : ''"></v-list-item>
                                    </template>
                                    <v-card>
                                        <template v-slot:text>
                                            <v-text-field v-model="search" label="Paieška"
                                                prepend-inner-icon="mdi-magnify" variant="outlined" hide-details
                                                single-line>
                                            </v-text-field>
                                        </template>
                                        <v-data-table :headers="eqHeaders" :items="item" :search="search">
                                            <template v-slot:item.img="{ item }">
                                                <v-img :width="50" height="100"
                                                    :src="'data:' + item.img_mime + ';base64,' + item.img_base64"
                                                    :alt=item.img_name></v-img>
                                            </template>
                                            <template v-slot:item.amount>
                                                <v-text-field v-model="amount" :value="amount" type="number" outlined
                                                    dense />
                                            </template>
                                            <template v-slot:item.add="{ item }">
                                                <v-btn @click="addSelectedEquipment(item)">Pridėti</v-btn>
                                            </template>
                                        </v-data-table>
                                    </v-card>

                                </v-list-group>
                            </v-list-group>

                            <v-list-group>
                                <template v-slot:activator="{ props }">
                                    <v-list-item v-bind="props" title="Transportas"></v-list-item>
                                </template>
                                <v-card>
                                    <template v-slot:text>
                                        <v-text-field v-model="veh_search" label="Paieška"
                                            prepend-inner-icon="mdi-magnify" variant="outlined" hide-details
                                            single-line>
                                        </v-text-field>
                                    </template>
                                    <template v-slot:activator="{ props }">
                                        <v-list-item v-bind="props" title="Transportas"></v-list-item>
                                    </template>
                                    <v-data-table :headers="vehHeaders" :items="vehicles" :search="veh_search">
                                        <template v-slot:item.add="{ item }">
                                            <v-btn @click="addSelectedVehicle(item)">Pridėti</v-btn>
                                        </template>
                                    </v-data-table>
                                </v-card>
                            </v-list-group>
                        </v-list>
                    </v-card>
                </v-col>
                <v-col>
                    <v-card>
                        <v-row>
                            <v-card elevation="4">
                                <template v-slot:text>
                                    <v-text-field v-model="searchSelectedEq" label="Paieška"
                                        prepend-inner-icon="mdi-magnify" variant="outlined" hide-details single-line>
                                    </v-text-field>
                                </template>
                                <v-data-table :headers="selectedEquipmentHeaders" :items="selectedEquipment"
                                    :search="searchSelectedEq">
                                    <template v-slot:item.img="{ item }">
                                        <v-img :width="50" height="100"
                                            :src="'data:' + item.img_mime + ';base64,' + item.img_base64"
                                            :alt=item.img_name></v-img>
                                    </template>
                                    <template v-slot:item.actions="{ item }">
                                        <v-icon size="small" @click="removeEquipment(item)">
                                            mdi-delete
                                        </v-icon>
                                    </template>
                                </v-data-table>
                            </v-card>
                        </v-row>
                        <v-row>
                            <v-card elevation="4">
                                <template v-slot:text>
                                    <v-text-field v-model="searchSelectedVeh" label="Paieška"
                                        prepend-inner-icon="mdi-magnify" variant="outlined" hide-details single-line>
                                    </v-text-field>
                                </template>
                                <v-data-table :headers="selectedVehiclesHeaders" :items="selectedVehicles"
                                    :search="searchSelectedVeh">
                                </v-data-table>
                            </v-card>
                        </v-row>
                    </v-card>
                </v-col>

            </v-row>
        </template>

        <template v-slot:item.3>
            <h3 class="text-h6">Pasirinkite renginio darbuotojus</h3>

            <br>

            <v-row>
                <v-col>
                    <v-card>
                        <v-list>
                            <v-list-group>
                                <template v-slot:activator="{ props }">
                                    <v-list-item v-bind="props" title="Darbuotojai"></v-list-item>
                                </template>
                                <v-data-table :headers="userHeaders" :items="users">
                                    <template v-slot:item.add="{ item }">
                                        <v-btn @click="addSelectedUser(item)">Pridėti</v-btn>
                                    </template>
                                </v-data-table>
                            </v-list-group>

                        </v-list>
                    </v-card>
                </v-col>
                <v-col>
                    <v-card color="secondary">
                        <v-row>
                            <v-data-table :headers="selectedUserHeaders" :items="selectedUsers">
                                <template v-slot:item.actions="{ item }">
                                    <v-icon size="small" @click="removeUser(item)">
                                        mdi-delete
                                    </v-icon>
                                </template>
                            </v-data-table>
                        </v-row>
                    </v-card>
                </v-col>

            </v-row>
        </template>
        <template v-slot:item.4>
            <h3 class="text-h6">Patvirtinkite duomenis</h3>

            <br>

            <v-sheet border>
                <v-form style="width: 100%" @submit.prevent="updateEvent">
                    <v-container>
                        <v-row>
                            <v-col>
                                <v-col cols="12">
                                    <v-text-field id="evedit-10" v-model="location" label="Vieta"></v-text-field>
                                </v-col>
                                <iframe width="400" height="450" style="border:0" loading="lazy" allowfullscreen
                                    referrerpolicy="no-referrer-when-downgrade"
                                    :src="'https://www.google.com/maps/embed/v1/place?key=AIzaSyC9L4qKGsl0r1O34wBNknqd2TGMg1h0zLQ&q=' + encodeURI(location)">
                                </iframe>
                                <v-col cols="12">
                                    <v-select id="evedit-11" v-model="client" :items="clients" label="Klientas"
                                        item-title="name" item-value="id" return-object>
                                    </v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field id="evedit-12" v-model="phone" label="Kontaktinis nr."
                                        type="text"></v-text-field>
                                </v-col>
                            </v-col>
                            <v-col>
                                <v-col cols="12">
                                    <v-text-field id="evedit-13" v-model="start_date" label="Pradžia"
                                        type="date"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field id="evedit-14" v-model="end_date" label="Pabaiga"
                                        type="date"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-select id="evedit-15" v-model="event_type" label="Tipas" item-title="name"
                                        item-value="value" :items="event_types"></v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-select id="evedit-16" v-model="event_subtype" label="Potipis" item-title="name"
                                        item-value="value" :items="event_subtypes"></v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-select id="evedit-17" v-model="event_status" label="Būsena" item-title="name"
                                        item-value="value" :items="event_statuses"></v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field id="evedit-18" v-model="description" label="Aprašymas"
                                        type="text"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-file-input id="evedit-19" v-model="file" label="Failas"></v-file-input>
                                </v-col>
                            </v-col>

                        </v-row>
                        <v-row>
                            <v-col>
                                <v-card>
                                    <template v-slot:text>
                                        <v-text-field v-model="search" label="Paieška" prepend-inner-icon="mdi-magnify"
                                            variant="outlined" hide-details single-line>
                                        </v-text-field>
                                    </template>
                                    <v-data-table :headers="selectedEquipmentHeaders" :items="selectedEquipment"
                                        :search="search">
                                        <template v-slot:item.img="{ item }">
                                            <v-img :width="50" height="100"
                                                :src="'data:' + item.img_mime + ';base64,' + item.img_base64"
                                                :alt=item.img_name></v-img>
                                        </template>
                                        <template v-slot:item.actions="{ item }">
                                            <v-icon size="small" @click="removeEquipment(item)">
                                                mdi-delete
                                            </v-icon>
                                        </template>
                                    </v-data-table>
                                </v-card>
                            </v-col>
                            <v-col>
                                <v-card>
                                    <template v-slot:text>
                                        <v-text-field v-model="veh_search" label="Paieška"
                                            prepend-inner-icon="mdi-magnify" variant="outlined" hide-details
                                            single-line>
                                        </v-text-field>
                                    </template>
                                    <v-data-table :headers="selectedVehiclesHeaders" :items="selectedVehicles"
                                        :search="veh_search">
                                        <template v-slot:item.actions="{ item }">
                                            <v-icon size="small" @click="removeVehicle(item)">
                                                mdi-delete
                                            </v-icon>
                                        </template>
                                    </v-data-table>
                                </v-card>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col>
                                <v-card elevation="4">
                                    <v-data-table :headers="selectedUserHeaders" :items="selectedUsers">
                                        <template v-slot:item.actions="{ item }">
                                            <v-icon size="small" @click="removeUser(item)">
                                                mdi-delete
                                            </v-icon>
                                        </template>
                                    </v-data-table>
                                </v-card>
                            </v-col>
                            <v-col></v-col>
                        </v-row>
                        <v-row justify="end">
                            <v-col cols="1">
                                <v-btn color="indigo-lighten-2" type="submit">Patvirtinti</v-btn>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-form>
            </v-sheet>
        </template>
    </v-stepper>
</template>

<script setup lang="ts">
import { VBtn } from 'vuetify/components';
import { FetchError } from 'ofetch';
import { useDisplay } from 'vuetify';
definePageMeta({
    middleware: ['sanctum:auth', 'organiser'],
    layout: 'default'
})
const smAndDown = useDisplay().smAndDown;

const sanctClient = useSanctumClient();
const stepperHeaders = ['Bendroji informacija', 'Įranga ir Transportas', 'Darbuotojai', 'Patvirtinimas'];
const event_types = [
    { name: 'Privatus', value: 'Private' },
    { name: 'Viešas', value: 'Public' },
    { name: 'Nuomos užsakymas', value: 'Rent Order' }];
const event_subtypes = [
    { name: 'Vestuvės', value: 'Wedding' },
    { name: 'Gimtadienis', value: 'Birthday' },
    { name: 'Įmonės', value: 'Corporate' },
    { name: 'Koncertas', value: 'Concert' },
    { name: 'Miesto Šventė', value: 'Town Celebration' },
    { name: 'Kalendorinė Šventė', value: 'Holiday Related' },
    { name: 'Kita', value: 'Other' }];
const event_statuses = [
    { name: 'Juodraštis', value: 'Draft' },
    { name: 'Planavimas', value: 'Planning' },
    { name: 'Vykdomas', value: 'In Progress' },
    { name: 'Užbaigtas', value: 'Completed' }
]

const { errorToast } = useToast()

const location = ref(" ");
const event_type = ref({
    name: '',
    value: ''
})
const event_subtype = ref({
    name: '',
    value: ''
})
const event_status = ref({
    name: '',
    value: ''
})
const client = ref({
    id: 0,
    name: ''
});
const start_date = ref();
const end_date = ref();
const type = ref("");
const subtype = ref("");
const description = ref("");
const phone = ref('');
const file = ref([]);
const equipment = ref([]);
const vehicles = ref([]);
const users = ref([]);
const { e_id } = useRoute().params;
const loading = ref(true);
const veh_search = ref('');
const search = ref('');
const status = ref('');
const searchSelectedEq = ref('');
const searchSelectedVeh = ref('');
const newUsers = ref([]);
const clients = ref([])

const amount = ref(0);


const selectedEqUnit = ref({
    id: 0,
    code: '',
    tags: '',
    name: '',
    amount: 0,
    img_mime: '',
    img_base64: '',
    img_name: '',
});

const selectedVehicle = ref({
    id: 0,
    make: '',
    model: '',
    amount: 0,

});

const selectedUser = ref({
    id: 0,
    name: '',
    email: '',
    role: 'ROLĖ'

});

const selectedEquipment = ref([]);
const selectedVehicles = ref([]);
const selectedUsers = ref([]);

const eqHeaders = ref([
    { title: 'Kodas', key: 'code' },
    { title: 'Pavadinimas', key: 'name' },
    { title: 'Nuotrauka', key: 'img' },
    { title: 'Žymės', key: 'tags' },
    { title: 'Viso', key: 'amount_total' },
    { title: 'Laisva', key: 'amount_available' },
    { title: 'Kiekis', key: 'amount' },
    { title: 'Pridėti', key: 'add' },
]);

const vehHeaders = ref([
    { title: 'Markė', key: 'make' },
    { title: 'Modelis', key: 'model' },
    { title: 'Viso', key: 'amount_total' },
    { title: 'Laisva', key: 'amount_available' },
    { title: 'Pridėti', key: 'add' },

]);

const userHeaders = ref([
    { title: 'Vardas', key: 'name' },
    { title: 'El. Paštas', key: 'email' },
    { title: 'Rolės', key: 'role' },
    { title: 'Pridėti', key: 'add' },
]);

const selectedUserHeaders = ref([
    { title: 'Vardas', key: 'name' },
    { title: 'El. Paštas', key: 'email' },
    { title: 'Rolės', key: 'role' },
    { title: 'Veiksmai', key: 'actions' },
]);

const selectedEquipmentHeaders = ref([
    { title: 'Kodas', key: 'code' },
    { title: 'Pavadinimas', key: 'name' },
    { title: 'Nuotrauka', key: 'img' },
    { title: 'Žymės', key: 'tags' },
    { title: 'Kiekis', key: 'amount' },
    { title: 'Veiksmai', key: 'actions' },

]);

const selectedVehiclesHeaders = ref([
    { title: 'Markė', key: 'make' },
    { title: 'Modelis', key: 'model' },
    { title: 'Kiekis', key: 'amount' },
    { title: 'Veiksmai', key: 'actions' },

]);
await getEvent();
getEquipment();
getVehicles();
getUsers();
getClients();
async function getEvent() {
    let data;
    try {
        data = await sanctClient(useRuntimeConfig().public.apiURL + '/events/' + e_id, {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        })
    } catch (error) {
        if (error instanceof FetchError && error.response?.status === 404) {
            errorToast('Šis įrašas neegzistuoja.');
            await navigateTo('/');
        }

    }
    location.value = data[0].location;
    client.value = data[0].client_id;
    start_date.value = data[0].start_date;
    end_date.value = data[0].end_date;
    phone.value = data[0].phone;
    event_type.value.value = data[0].event_type;
    event_type.value.name = event_types.filter(t => t.value == data[0].event_type)[0].name;
    event_status.value.value = data[0].event_status;
    event_status.value.name = event_statuses.filter(t => t.value == data[0].event_status)[0].name;
    event_subtype.value.value = data[0].event_subtype;
    event_subtype.value.name = event_subtypes.filter(t => t.value == data[0].event_subtype)[0].name;
    description.value = data[0].description;
    selectedUsers.value = data[0].staff;

    for (var val of data[0].equipment) {
        console.log('eqItem', val.pivot.equipment_amount)
        selectedEqUnit.value = {
            id: val.id,
            code: val.code,
            tags: val.tags,
            img_mime: val.img_mime,
            img_base64: val.img_base64,
            img_name: val.img_name,
            name: val.name,
            amount: val.pivot.equipment_amount
        }
        selectedEquipment.value.push(selectedEqUnit.value);
    }

    for (var val of data[0].vehicles) {
        console.log('vehItem', val)
        selectedVehicle.value = {
            id: val.id,
            make: val.make,
            model: val.model,
            amount: val.pivot.vehicle_amount,
        }
        selectedVehicles.value.push(selectedVehicle.value);
    }
}

async function getEquipment() {
    const data = await sanctClient(useRuntimeConfig().public.apiURL + '/equipment', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })
    let tmp;
    for (let i = 0; i < data.audio.length; i++) {
        const str = data.audio[i].tags.map(t => t.name).join(", ")
        if (selectedEquipment.value.find(eq => eq.id == data.audio[i].id && eq.code == data.audio[i].code)) {
            selectedEquipment.value.filter(eq => eq.id == data.audio[i].id && eq.code == data.audio[i].code)[0].tags = str
        }
        data.audio[i].tags = str;
    }
    for (let i = 0; i < data.video.length; i++) {
        const str = data.video[i].tags.map(t => t.name).join(", ")
        if (selectedEquipment.value.find(eq => eq.id == data.video[i].id && eq.code == data.video[i].code)) {
            selectedEquipment.value.filter(eq => eq.id == data.video[i].id && eq.code == data.video[i].code)[0].tags = str
        }
        data.video[i].tags = str;
    }
    for (let i = 0; i < data.other.length; i++) {
        const str = data.other[i].tags.map(t => t.name).join(", ")
        if (selectedEquipment.value.find(eq => eq.id == data.other[i].id && eq.code == data.other[i].code)) {
            selectedEquipment.value.filter(eq => eq.id == data.other[i].id && eq.code == data.other[i].code)[0].tags = str
        }
        data.other[i].tags = str;
    }
    for (let i = 0; i < data.recording.length; i++) {
        const str = data.recording[i].tags.map(t => t.name).join(", ")
        if (selectedEquipment.value.find(eq => eq.id == data.recording[i].id && eq.code == data.recording[i].code)) {
            selectedEquipment.value.filter(eq => eq.id == data.recording[i].id && eq.code == data.recording[i].code)[0].tags = str
        }
        data.recording[i].tags = str;
    }
    for (let i = 0; i < data.storage.length; i++) {
        const str = data.storage[i].tags.map(t => t.name).join(", ")
        if (selectedEquipment.value.find(eq => eq.id == data.storage[i].id && eq.code == data.storage[i].code)) {
            selectedEquipment.value.filter(eq => eq.id == data.storage[i].id && eq.code == data.storage[i].code)[0].tags = str
        }
        data.storage[i].tags = str;
    }
    equipment.value = data;
    loading.value = false
};

async function getVehicles() {
    const data = await sanctClient(useRuntimeConfig().public.apiURL + '/vehicles', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })
    vehicles.value = data;
    console.log(vehicles)
};

async function getUsers() {
    const data = await sanctClient(useRuntimeConfig().public.apiURL + '/users/event/org', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })
    users.value = data;
};
async function getClients() {
    const data = await sanctClient(useRuntimeConfig().public.apiURL + '/clients/event/org', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })

    clients.value = data.map(t => ({ id: t.id, name: t.name }));
    console.log(clients)
}
function addSelectedEquipment(item) {

    selectedEqUnit.value = {
        id: item.id,
        code: item.code,
        tags: item.tags,
        name: item.name,
        img_mime: item.img_mime,
        img_base64: item.img_base64,
        img_name: item.img_name,
        amount: item.amount,
    };
    const foundValue = ref(selectedEquipment.value.find(value => value.id == selectedEqUnit.value.id));

    if (!foundValue.value) {
        selectedEquipment.value.push(selectedEqUnit.value);
        item.amount_available = +item.amount_available - +item.amount

    }
    else {
        foundValue.value.amount = +foundValue.value.amount + +item.amount;
        item.amount_available = +item.amount_available - +item.amount;

    }
    item.amount = 0;
};

function addSelectedVehicle(item) {

    selectedVehicle.value = {
        id: item.id,
        make: item.make,
        model: item.model,
        amount: 1,
    };
    const foundValue = ref(selectedVehicles.value.find(value => value.id == selectedVehicle.value.id));

    if (!foundValue.value) {
        selectedVehicles.value.push(selectedVehicle.value);
        item.amount_available = 0

    }
    else {
        foundValue.value.vehicle_amount = +foundValue.value.vehicle_amount + +item.vehicle_amount;
    }
    item.vehicle_amount = 0;
};

function addSelectedUser(item) {

    selectedUser.value = {
        id: item.id,
        name: item.name,
        email: item.email,
        role: 'ROLĖ',
    };
    const foundValue = ref(selectedUsers.value.find(value => value.id == selectedUsers.value.id));

    if (!foundValue.value) {
        selectedUsers.value.push(selectedUser.value);
        newUsers.value.push(selectedUser.value);
    };
};

async function updateEvent() {
    console.log(event_status.value);
    await sanctClient(useRuntimeConfig().public.apiURL + '/events/' + e_id, {
        method: 'PUT',
        body: {
            location: location.value,
            phone: phone.value,
            client_id: client.value,
            start_date: start_date.value,
            end_date: end_date.value,
            event_type: event_type.value.value,
            event_subtype: event_subtype.value.value,
            event_status: event_status.value.value,
            description: description.value,
            equipment_id: selectedEquipment.value.map(t => [t.id, t.amount]),
            vehicles_id: selectedVehicles.value.map(t => [t.id, t.amount]),
            staff_id: selectedUsers.value.map(t => t.id)
        }
    });

    const formData = new FormData();
    formData.append("file", file.value[0]);
    formData.append("name", 'event' + e_id);
    formData.append("event_id", '' + e_id);

    try {
        await sanctClient(useRuntimeConfig().public.apiURL + '/event/files', {
            method: 'POST',
            body: formData
        });

    } catch (error) {
        if (error instanceof FetchError && error.response?.status === 422) {

            errorToast('Klaida įkeliant failą');
        }
    }

    for (let i = 0; i < newUsers.value.length; i++) {
        await sanctClient(useRuntimeConfig().public.apiURL + '/inbox', {
            method: 'POST',
            body: {
                recipient_name: newUsers.value[i].name,
                message: 'Jūs buvote priskirtas renginiui, kurio numeris yra ' + e_id,
                replied_to: ''
            }
        })
    }

    await navigateTo('/events/' + e_id);
};

function removeEquipment(item) {
    const index = selectedEquipment.value.indexOf(item, 0);
    if (index > -1) {
        selectedEquipment.value.splice(index, 1);
    }
}

function removeVehicle(item) {
    const index = selectedVehicles.value.indexOf(item, 0);
    if (index > -1) {
        selectedVehicles.value.splice(index, 1);
    }
}

function removeUser(item) {
    const index = selectedUsers.value.indexOf(item, 0);
    if (index > -1) {
        selectedUsers.value.splice(index, 1);
        newUsers.value.splice(index, 1);
    }
}
</script>



<style scoped></style>