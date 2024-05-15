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
                                <v-select id="evcreate-1" v-model="client" :items="clients" label="Klientas"
                                    item-title="name" item-value="id" return-object>
                                </v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field id="evcreate-2" v-model="phone" label="Kontaktinis nr."
                                    type="text"></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field id="evcreate-0" v-model="location" label="Vieta"></v-text-field>
                            </v-col>
                            <iframe width="300" height="450" style="border:0" loading="lazy" allowfullscreen
                                referrerpolicy="no-referrer-when-downgrade"
                                :src="'https://www.google.com/maps/embed/v1/place?key=AIzaSyC9L4qKGsl0r1O34wBNknqd2TGMg1h0zLQ&q=' + encodeURI(location)">
                            </iframe>
                        </v-col>
                        <v-col>

                            <v-col cols="12">
                                <v-text-field id="evcreate-3" v-model="start_date" label="Pradžia"
                                    type="date"></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field id="evcreate-4" v-model="end_date" label="Pabaiga"
                                    type="date"></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-select id="evcreate-5" v-model="event_type" label="Tipas" item-title="name"
                                    item-value="value" :items="event_types"></v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-select id="evcreate-6" v-model="event_subtype" label="Potipis" item-title="name"
                                    item-value="value" :items="event_subtypes"></v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field id="evcreate-7" v-model="description" label="Aprašymas"
                                    type="text"></v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-file-input id="evcreate-8" v-model="file" label="Failas"></v-file-input>
                            </v-col>
                        </v-col>

                    </v-row>
                </v-container>
            </v-sheet>
        </template>

        <template v-slot:item.2>

            <h3 class="text-h6">Pasirinkite reikalingą įrangą ir transportą</h3>

            <br>
            <v-container>
                <v-row class="d-flex flex-wrap ga-3">
                    <v-col>
                        <v-card elevation="12">
                            <v-list id="eq-1">
                                <v-list-group id="eq-2">
                                    <template v-slot:activator="{ props }" id="eq-3">
                                        <v-list-item id="eq-4"  title="Įranga"></v-list-item>
                                    </template>
                                    <v-list-group v-for="item in equipment" :value="item">
                                        <template v-slot:activator="{ props }">
                                            <v-list-item v-bind="props"
                                                :title="item == equipment['audio'] ? 'Audio' :
                                                    item == equipment['video'] ? 'Video' :
                                                        item == equipment['recording'] ? 'Įrašymo' :
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
                                            <v-data-table :headers="eqHeaders" :items="item" :search="search"
                                                :mobile="smAndDown" items-per-page-text="Įrašų kiekis puslapyje:">
                                                <template v-slot:item.img="{ item }">
                                                    <v-img :width="50" height="100"
                                                        :src="'data:' + item.img_mime + ';base64,' + item.img_base64"
                                                        :alt=item.img_name></v-img>
                                                </template>
                                                <template v-slot:item.amount="{ item }">
                                                    <v-text-field v-model="item.amount" :value="item.amount"
                                                        type="number" outlined dense />
                                                </template>
                                                <template v-slot:item.add="{ item }">
                                                    <v-btn color="indigo-lighten-2" @click="addSelectedEquipment(item)">Pridėti</v-btn>
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
                                        <v-data-table :headers="vehHeaders" :items="vehicles" :search="veh_search"
                                            :mobile="smAndDown">
                                            <template v-slot:item.add="{ item }">
                                                <v-btn color="indigo-lighten-2" @click="addSelectedVehicle(item)">Pridėti</v-btn>
                                            </template>
                                        </v-data-table>
                                    </v-card>
                                </v-list-group>
                            </v-list>
                        </v-card>
                    </v-col>
                    <v-col class="d-flex flex-wrap ga-3">
                        <v-row>
                            <v-card elevation="12">
                                <template v-slot:text>
                                    <v-text-field v-model="searchSelectedEq" label="Paieška"
                                        prepend-inner-icon="mdi-magnify" variant="outlined" hide-details single-line>
                                    </v-text-field>
                                </template>
                                <v-data-table :headers="selectedEquipmentHeaders" :items="selectedEquipment"
                                    :search="searchSelectedEq" :mobile="smAndDown">
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
                            <v-card elevation="12">
                                <template v-slot:text>
                                    <v-text-field v-model="searchSelectedVeh" label="Paieška"
                                        prepend-inner-icon="mdi-magnify" variant="outlined" hide-details single-line
                                        :mobile="smAndDown">
                                    </v-text-field>
                                </template>
                                <v-data-table :headers="selectedVehiclesHeaders" :items="selectedVehicles"
                                    :search="searchSelectedVeh" :mobile="smAndDown">
                                </v-data-table>
                            </v-card>
                        </v-row>
                    </v-col>
                </v-row>
            </v-container>
        </template>

        <template v-slot:item.3>
            <h3 class="text-h6">Pasirinkite renginio darbuotojus</h3>

            <br>

            <v-row>
                <v-col>
                    <v-card>
                        <v-list>
                            <v-list-group id="db-1">
                                <template v-slot:activator="{ props }">
                                    <v-list-item v-bind="props" title="Darbuotojai"></v-list-item>
                                </template>
                                <v-data-table :headers="userHeaders" :items="users">
                                    <template v-slot:item.add="{ item }">
                                        <v-btn color="indigo-lighten-2" @click="addSelectedUser(item)">Pridėti</v-btn>
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
                <v-form style="width: 100%" @submit.prevent="createEvent">
                    <v-container>
                        <v-row>
                            <v-col>
                                <v-col cols="12">
                                    <v-select id="evcreate-11" v-model="client" :items="clients" label="Klientas"
                                        item-title="name" item-value="id" return-object>
                                    </v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field id="evcreate-12" v-model="phone" label="Kontaktinis nr."
                                        type="text"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field id="evcreate-9" v-model="location" label="Vieta"></v-text-field>
                                </v-col>
                                <iframe width="400" height="450" style="border:0" loading="lazy" allowfullscreen
                                    referrerpolicy="no-referrer-when-downgrade"
                                    :src="'https://www.google.com/maps/embed/v1/place?key=AIzaSyC9L4qKGsl0r1O34wBNknqd2TGMg1h0zLQ&q=' + encodeURI(location)">
                                </iframe>
                            </v-col>
                            <v-col>
                                <v-col cols="12">
                                    <v-text-field id="evcreate-13" v-model="start_date" label="Pradžia"
                                        type="date"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field id="evcreate-14" v-model="end_date" label="Pabaiga"
                                        type="date"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-select id="evcreate-15" v-model="event_type" label="Tipas" item-title="name"
                                        item-value="value" :items="event_types"></v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-select id="evcreate-16" v-model="event_subtype" label="Potipis" item-title="name"
                                        item-value="value" :items="event_subtypes"></v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field id="evcreate-17" v-model="description" label="Aprašymas"
                                        type="text"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-file-input id="evcreate-18" v-model="file" label="Failas"></v-file-input>
                                </v-col>
                            </v-col>

                        </v-row>
                        <v-row>
                            <v-col>
                                <v-card elevation="4">
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
                                <v-card elevation="4">
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
const { errorToast } = useToast()
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
const location = ref(" ");
const event_type = ref({
    name: 'Privatus',
    value: 'Private'
})
const event_subtype = ref({
    name: 'Vestuvės',
    value: 'Wedding'
})
const client = ref({
    id: 0,
    name: ''
});
const start_date = ref('2024-05-16');
const end_date = ref('2024-05-17');
const type = ref("");
const subtype = ref("");
const description = ref("");
const file = ref([]);
const equipment = ref([]);
const vehicles = ref([]);
const users = ref([]);
const search = ref('');
const veh_search = ref('');
const loading = ref(true);
const searchSelectedEq = ref('');
const searchSelectedVeh = ref('');
const amount = ref(0);
const phone = ref('');
const user = useSanctumUser();
const clients = ref([])
getEquipment();
getVehicles();
getUsers();
getClients();
const selectedEqUnit = ref({
    id: 0,
    code: '',
    name: '',
    tags: '',
    img_mime: '',
    img_base64: '',
    img_name: '',
    amount: 0,

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
    { title: 'Kiekis', key: 'amount' },
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


async function getClients() {
    const data = await sanctClient(useRuntimeConfig().public.apiURL + '/clients/event/org', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })

    clients.value = data.map(t => ({ id: t.id, name: t.name + ' ' + t.surname }));
    client.value.id = clients.value[0].id;
    client.value.name = clients.value[0].name;
}
async function getEquipment() {
    const data = await sanctClient(useRuntimeConfig().public.apiURL + '/equipment', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })

    for (let i = 0; i < data.audio.length; i++) {
        const str = data.audio[i].tags.map(t => t.name).join(", ")
        data.audio[i].tags = str;
    }
    for (let i = 0; i < data.video.length; i++) {
        const str = data.video[i].tags.map(t => t.name).join(", ")
        data.video[i].tags = str;
    }
    for (let i = 0; i < data.other.length; i++) {
        const str = data.other[i].tags.map(t => t.name).join(", ")
        data.other[i].tags = str;
    }
    for (let i = 0; i < data.recording.length; i++) {
        const str = data.recording[i].tags.map(t => t.name).join(", ")
        data.recording[i].tags = str;
    }
    for (let i = 0; i < data.storage.length; i++) {
        const str = data.storage[i].tags.map(t => t.name).join(", ")
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
    console.log('vehicles ', vehicles);
};

async function getUsers() {
    const data = await sanctClient(useRuntimeConfig().public.apiURL + '/users/event/org', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })
    users.value = data;
};

function addSelectedEquipment(item) {

    selectedEqUnit.value = {
        id: item.id,
        code: item.code,
        tags: item.tags,
        img_mime: item.img_mime,
        img_base64: item.img_base64,
        img_name: item.img_name,
        name: item.name,
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
    }
};

async function createEvent() {
    console.log(selectedEquipment.value.map(t => t.id));
    const response = await sanctClient(useRuntimeConfig().public.apiURL + '/events', {
        method: 'POST',
        body: {
            location: location.value,
            client_id: client.value.id,
            phone: phone.value,
            start_date: start_date.value,
            end_date: end_date.value,
            event_type: event_type.value.value,
            event_subtype: event_subtype.value.value,
            description: description.value,
            event_status: 'Draft',
            equipment_id: selectedEquipment.value.map(t => [t.id, t.amount]),
            vehicles_id: selectedVehicles.value.map(t => [t.id, t.amount]),
            staff_id: selectedUsers.value.map(t => t.id)
        }
    });

    const formData = new FormData();
    formData.append("file", file.value[0]);
    formData.append("name", 'event' + response['id']);
    formData.append("event_id", response['id']);



    for (let i = 0; i < selectedUsers.value.length; i++) {
        await sanctClient(useRuntimeConfig().public.apiURL + '/inbox', {
            method: 'POST',
            body: {
                recipient_name: selectedUsers.value[i].name,
                message: 'Jūs buvote priskirtas renginiui, kuris vyks nuo ' + start_date.value + ' iki ' + end_date.value + '.',
                replied_to: ''
            }
        })
    }
    try {
        await sanctClient(useRuntimeConfig().public.apiURL + '/event/files', {
            method: 'POST',
            body: formData
        });
        await navigateTo('/events/' + response['id']);

    } catch (error) {
        if (error instanceof FetchError && error.response?.status === 422) {

            errorToast('Klaida įkeliant failą');
            await navigateTo('/events/');
        }
        else (
            errorToast('Įvyko klaida.')
        )
    }


    await navigateTo('/events/' + response['id']);
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
    }
}
</script>
<style scoped>
@media only screen and (max-width: 959px) {
    .v-stepper:not(.v-stepper--vertical) .v-stepper__label {
        display: flex !important;
    }
}
</style>