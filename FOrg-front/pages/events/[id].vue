<template>

    <v-container>
        <!--

        <v-row>
            <v-col>
                <v-card elevation="12" flex d-flex flex-column class="h-auto pa-3" sm="4" max-width="450">
                    <v-col cols="12">
                        <v-text-field v-model="location" label="Vieta" readonly></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-checkbox v-model="streetView" label="Gatvės vaizdas" />
                        <iframe v-if="streetView == false" width="400" height="450" style="border:0" loading="lazy"
                            allowfullscreen referrerpolicy="no-referrer-when-downgrade"
                            :src="'https://www.google.com/maps/embed/v1/place?key=AIzaSyC9L4qKGsl0r1O34wBNknqd2TGMg1h0zLQ&q=' + encodeURI(location)">
                        </iframe>
                        <iframe v-if="streetView == true" width="400" height="450" style="border:0" loading="lazy"
                            allowfullscreen referrerpolicy="no-referrer-when-downgrade"
                            :src="'https://www.google.com/maps/embed/v1/streetview?key=AIzaSyC9L4qKGsl0r1O34wBNknqd2TGMg1h0zLQ&location=' + encodeURI(center.lat + ', ' + center.lon)">
                        </iframe>
                    </v-col>
                </v-card>
            </v-col>
            <v-col class="pa-8">
                <v-card >
                <v-row class="pa-2">
                    <v-card min-width="100%" class="pa-2">
                        <v-row>
                            <v-col>
                                <v-text-field class="pa-2" v-model="clientName" label="Klientas" readonly hide-details></v-text-field>
                                <v-text-field class="pa-2" v-model="phone" label="Kontaktinis nr." readonly hide-details></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field class="pa-2" v-model="start_date" label="Pradžia" type="datetime"
                                    readonly hide-details></v-text-field>
                                <v-text-field class="pa-2" v-model="end_date" label="Pabaiga" type="datetime"
                                    readonly hide-details></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field class="pa-2" v-model="type" label="Tipas" readonly hide-details></v-text-field>
                                <v-text-field class="pa-2" v-model="subtype" label="Potipis" readonly hide-details></v-text-field>
                            </v-col>
                        </v-row>
                    </v-card>
                </v-row>
                <v-row>
                    <v-col>
                        <v-card>
                            <v-textarea class="pa-2" v-model="description" label="Aprašymas" type="text" readonly hide-details></v-textarea>
                        </v-card>
                    </v-col>
                    <v-col>
                        <v-card>
                            <v-list v-for="item in files">
                                <v-list-item @click="downloadFile(item.id)">
                                    {{ item.name }}
                                    <template v-slot:append>
                                        <v-icon> mdi-download-box </v-icon>
                                    </template>
</v-list-item>
</v-list>
</v-card>
</v-col>
</v-row>
<v-row>
    <v-col>
        <v-card title="Įranga" link :to="'/events/' + id + '/equipment'">
            <v-card-item>
                <div>
                    <qrcode-vue :size="150" :value="'/events/' + id + '/equipment'"></qrcode-vue>
                </div>
            </v-card-item>
        </v-card>
    </v-col>
    <v-col>
        <v-card title="Transportas" link :to="'/events/' + id + '/vehicles'">
            <v-card-item>
                <div>
                    <qrcode-vue :size="150" :value="'/events/' + id + '/vehicles'"></qrcode-vue>
                </div>
            </v-card-item>
        </v-card>
    </v-col>
</v-row>
</v-card>
</v-col>
<v-col>
    <v-row>
        <v-card title="Darbuotojai">
            <v-list v-for="usrnm in userNames">
                <v-list-item>{{ usrnm }}</v-list-item>
            </v-list>
        </v-card>
    </v-row>
    <v-row>
        <v-card class="fill-height" min-width="100%">
            <v-container class="fill-height;overflow-y:scroll">
                <v-row class="fill-height pb-14" align="end">
                    <v-col>
                        <div v-for="(item, index) in chat" :key="index"
                            :class="['d-flex flex-row align-center my-2', item.sender_name == user.name ? 'justify-end' : null]">
                            <span v-if="item.sender_name == user.name" class="blue--text mr-3">{{
                                item.message
                                }}</span>
                            <v-avatar :color="item.sender_name == user.name ? 'indigo' : 'red'" size="36">
                                <span class="white--text">{{ item.sender_name[0] }}</span>
                            </v-avatar>
                            <span v-if="item.sender_name != user.name" class="blue--text ml-3">{{
                                item.message
                                }}</span>
                        </div>
                    </v-col>
                </v-row>
            </v-container>
            <v-footer fixed>
                <v-container class="ma-0 pa-0">
                    <v-row no-gutters>
                        <v-col>
                            <div class="d-flex flex-row align-center">
                                <v-text-field v-model="msg" placeholder="Įrašykite žinutę"
                                    @keypress.enter="sendMessage"></v-text-field>
                                <v-btn icon class="ml-4" @click="sendMessage"><v-icon>mdi-send</v-icon></v-btn>
                            </div>
                        </v-col>
                    </v-row>
                </v-container>
            </v-footer>
        </v-card>

    </v-row>
</v-col>
</v-row> -->

        <v-row>
            <v-col cols="auto">
                <v-card elevation="12" flex d-flex flex-column class="h-auto" sm="4" max-width="450">

                    <v-col cols="12">
                        <v-text-field id="e-1" v-model="location" label="Vieta" readonly></v-text-field>
                    </v-col>
                    <v-fab class="mr-4" icon="mdi-printer" location="end" size="40" absolute offset
                        @click="printEvent"></v-fab>

                    <v-col cols="12">
                        <v-checkbox v-model="streetView" label="Gatvės vaizdas" />
                        <iframe v-if="streetView == false" width="400" height="450" style="border:0" loading="lazy"
                            allowfullscreen referrerpolicy="no-referrer-when-downgrade"
                            :src="'https://www.google.com/maps/embed/v1/place?key=AIzaSyC9L4qKGsl0r1O34wBNknqd2TGMg1h0zLQ&q=' + encodeURI(location)">
                        </iframe>
                        <iframe v-if="streetView == true" width="400" height="450" style="border:0" loading="lazy"
                            allowfullscreen referrerpolicy="no-referrer-when-downgrade"
                            :src="'https://www.google.com/maps/embed/v1/streetview?key=AIzaSyC9L4qKGsl0r1O34wBNknqd2TGMg1h0zLQ&location=' + encodeURI(center.lat + ', ' + center.lon)">
                        </iframe>
                    </v-col>
                    <v-col>
                        <v-text-field v-model="clientName" label="Klientas" readonly></v-text-field>
                    </v-col>
                    <v-col>
                        <v-text-field id="e-2" v-model="phone" label="Kontaktinis nr." readonly></v-text-field>
                    </v-col>
                    <v-col>
                        <v-text-field v-model="start_date" label="Pradžia" type="datetime" readonly></v-text-field>
                    </v-col>
                    <v-col>

                        <v-text-field v-model="end_date" label="Pabaiga" type="datetime" readonly></v-text-field>
                    </v-col>
                    <v-col>

                        <v-text-field v-model="type" label="Tipas" readonly></v-text-field>
                    </v-col>
                    <v-col>
                        <v-text-field v-model="subtype" label="Potipis" readonly></v-text-field>
                    </v-col>
                </v-card>
            </v-col>
            <v-col cols="auto">
                <v-row no-gutters>
                    <v-card elevation="12">
                        <v-col cols="12">
                            <v-textarea id="e-3" v-model="description" label="Aprašymas" type="text" readonly></v-textarea>
                        </v-col>
                        <v-col>
                            <v-card title="Darbuotojai">
                                <v-list v-for="usrnm in userNames">
                                    <v-list-item>{{ usrnm }}</v-list-item>
                                </v-list>
                            </v-card>
                        </v-col>
                        <v-col cols="12">
                            <v-list v-for="item in files">
                                <v-list-item @click="downloadFile(item.id)">
                                    {{ item.name }}
                                    <template v-slot:append>
                                        <v-icon> mdi-download-box </v-icon>
                                    </template>
                                </v-list-item>
                            </v-list>
                        </v-col>
                    </v-card>
                </v-row>
            </v-col>

            <v-col>
                <v-card elevation="12" title="Transportas" link :to="'/events/' + id + '/vehicles'">
                    <v-card-item>
                        <div>
                            <qrcode-vue :size="150" :value="'/events/' + id + '/vehicles'"
                                background="#CEFACE"></qrcode-vue>
                        </div>
                    </v-card-item>
                </v-card>
                <v-card elevation="12" title="Įranga" link :to="'/events/' + id + '/equipment'">
                    <v-card-item>
                        <div>
                            <qrcode-vue :size="150" :value="'/events/' + id + '/equipment'"
                                background="#FFFFC1"></qrcode-vue>
                        </div>
                    </v-card-item>
                </v-card>
            </v-col>
            <v-row>
                <v-col>
                    <v-card elevation="12">
                        <v-container class="fill-height">
                            <v-row class="fill-height pb-14" align="end">
                                <v-col>
                                    <div v-for="(item, index) in chat" :key="index"
                                        :class="['d-flex flex-row align-center my-2', item.sender_name == user.name ? 'justify-end' : null]">
                                        <span v-if="item.sender_name == user.name" class="blue--text mr-3">{{
                                            item.message
                                        }}</span>
                                        <v-avatar :color="item.sender_name == user.name ? 'indigo' : 'red'" size="36">
                                            <span class="white--text">{{ item.sender_name[0] }}</span>
                                        </v-avatar>
                                        <span v-if="item.sender_name != user.name" class="blue--text ml-3">{{
                                            item.message
                                        }}</span>
                                    </div>
                                </v-col>
                            </v-row>
                        </v-container>
                        <v-footer fixed>
                            <v-container class="ma-0 pa-0">
                                <v-row no-gutters>
                                    <v-col>
                                        <div class="d-flex flex-row align-center">
                                            <v-text-field v-model="msg" placeholder="Įrašykite žinutę"
                                                @keypress.enter="sendMessage"></v-text-field>
                                            <v-btn icon class="ml-4"
                                                @click="sendMessage"><v-icon>mdi-send</v-icon></v-btn>
                                        </div>
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-footer>
                    </v-card>

                </v-col>
            </v-row>



        </v-row>
    </v-container>

</template>

<script setup lang="ts">
import { VBtn } from 'vuetify/components';
import QrcodeVue from 'qrcode.vue'
import jsPDF from 'jspdf';
import QRious from 'qrious';
import AmbleBold from '@/assets/fonts/Amble-bold.js';
import AmbleNormal from '@/assets/fonts/Amble-Regular-normal.js';
import { FetchError } from 'ofetch';

definePageMeta({
    middleware: ['sanctum:auth'],
    layout: 'default'
})
const user = useSanctumUser();
const userPerms = ref({
    admin: user.value.roles.find(value => value.name == 'admin'),
    organiser: user.value.roles.find(value => value.name == 'organizer'),
    warehouse_worker: user.value.roles.find(value => value.name == 'warehouse_worker')
})
const { errorToast } = useToast()
const sanctClient = useSanctumClient();
const location = ref("");
const client = ref("");
const start_date = ref();
const end_date = ref();
const type = ref("");
const subtype = ref("");
const description = ref("");
const file = ref([]);
const phone = ref('');
const streetView = ref(false);
const clientName = ref('');
const files = ref([]);
const { id } = useRoute().params;
const userNames = ref([])
const chat = ref([]);
const msg = ref(null);
const center = ref({
    lat: 0,
    lon: 0
});
const selectedEqUnit = ref({
    id: 0,
    code: '',
    name: '',
    equipment_amount: 0,

});

const selectedVehicle = ref({
    id: 0,
    make: '',
    model: '',
    vehicle_amount: 0,

});


const selectedEquipment = ref([]);
const selectedVehicles = ref([]);
const selectedUsers = ref([]);

getEvent();
getMessages();
getFileNames();


async function getEvent() {
    let data;
    try {
        data = await sanctClient(useRuntimeConfig().public.apiURL + '/events/' + id, {
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
    phone.value = data[0].phone;
    client.value = data[0].client_id;
    start_date.value = data[0].start_date;
    end_date.value = data[0].end_date;
    type.value = data[0].event_type;
    subtype.value = data[0].event_subtype;
    description.value = data[0].description;
    selectedUsers.value = data[0].staff;
    await getCoords();
    for (var val of data[0].equipment) {
        selectedEqUnit.value = {
            id: val.id,
            code: val.code,
            name: val.name,
            equipment_amount: val.pivot.equipment_amount
        }
        selectedEquipment.value.push(selectedEqUnit.value);
    }

    for (var val of data[0].vehicles) {
        selectedVehicle.value = {
            id: val.id,
            make: val.make,
            model: val.model,
            vehicle_amount: val.pivot.vehicle_amount,
        }
        selectedVehicles.value.push(selectedVehicle.value);
    }
    for (var val of data[0].staff) {
        userNames.value.push(val.name);
    }

    const cdata = await sanctClient(useRuntimeConfig().public.apiURL + '/clients/' + client.value +'/name', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })

    clientName.value = cdata[0].name + ' ' + cdata[0].surname;
}

async function getMessages() {
    const messages = await sanctClient(useRuntimeConfig().public.apiURL + '/events/' + id + '/messages', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })
    chat.value = messages;
}
async function sendMessage() {
    if (msg.value != null) {
        await sanctClient(useRuntimeConfig().public.apiURL + '/events/' + id + '/messages', {
            method: 'POST',
            body: {
                message: msg.value
            }
        })
        chat.value.push({
            sender_name: user.value.name,
            message: msg.value
        })
        msg.value = null;
    }

}

async function getFileNames() {
    files.value = await sanctClient(useRuntimeConfig().public.apiURL + '/event/' + id + '/files/', {
        method: 'GET'
    })
    console.log(files)
}

async function downloadFile(fid) {
    await sanctClient(useRuntimeConfig().public.apiURL + '/event/files/' + fid, {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    }).then(function (file) {
        var link = document.createElement("a");
        document.body.appendChild(link);
        link.setAttribute("type", "hidden");
        link.href = "data:" + file[0].mime + ";base64," + file[0].file;
        link.download = file[0].name;
        link.click();
        document.body.removeChild(link);
    })


}
async function printEvent() {
    const doc = new jsPDF();
    const eventQr = new QRious({ size: 50, value: '/events/' + id, level: 'H', background: 'white', backgroundAlpha: 0.2 });
    const eqQr = new QRious({ size: 325, value: '/events/' + id + '/equipment', level: 'H', background: '#CEFACE' });
    const vehQr = new QRious({ size: 325, value: '/events/' + id + '/vehicles', level: 'H', background: '#FFFFC1' });
    doc.addFileToVFS('Amble-Bold.ttf', AmbleBold)
    doc.addFileToVFS('Amble-Regular.ttf', AmbleNormal)
    doc.addFont('Amble-Bold.ttf', "Amble", "bold");
    doc.addFont('Amble-Regular.ttf', "Amble", "normal");
    doc.setFont("Amble", "normal");
    doc.setFontSize(22);

    doc.text(type.value + ' ' + subtype.value, 105, 10, { align: "center" });
    doc.setFontSize(17);
    doc.text(start_date.value.slice(0, (start_date.value.lastIndexOf(":"))) + ' - ' + end_date.value.slice(0, (end_date.value.lastIndexOf(":"))), 105, 20, { align: "center" });
    doc.setFontSize(12);

    doc.line(10, 30, 200, 30);
    doc.text('Vieta: ' + location.value, 10, 40, { maxWidth: 75 });
    doc.text('Tel. nr.: ' + phone.value, 10, 47, { maxWidth: 75 });
    doc.text('Aprašymas: ' + description.value, 10, 54, { maxWidth: 75 });
    const descrLines = ((doc.getStringUnitWidth('Aprašymas: ' + description.value) * 12) / (72 / 25.6) + 10) / 75
    const y = ref(61);
    y.value = y.value <= (descrLines * 5) + 54 ? y.value + Math.ceil(descrLines) * 5 : y.value;
    doc.text('Darbuotojai: ', 10, y.value, { maxWidth: 75 });
    selectedUsers.value.forEach((user) => {
        doc.text(user.name + ', ' + user.email + ', ' + user.driving_license, 34, y.value);
        y.value += 7;
    })
    y.value = y.value <= 95 ? 105 : y.value;
    doc.text('Renginio QR', 150, 40);
    doc.addImage(eventQr.toDataURL(), 137, 45, 50, 50);
    doc.line(10, y.value + 10, 200, y.value + 10);

    doc.text('Įrangos QR', 30, y.value + 20);
    doc.text('Transporto QR', 155, y.value + 20);
    doc.addImage(vehQr.toDataURL(), 135, y.value + 25, 65, 65);
    doc.addImage(eqQr.toDataURL(), 10, y.value + 25, 65, 65);
    console.log(doc.getFontList())
    doc.save(type.value + '_' + subtype.value + '.pdf');
}

async function getCoords() {
    var uri: string = "https://nominatim.openstreetmap.org/search?q=" + location.value + "&format=json&addressdetails=1&limit=1";

    const data = await $fetch(uri, {
        headers: {
            'Access-Control-Allow-Origin': 'http://localhost:3000'
        }
    });
    center.value.lat = data[0].lat;
    center.value.lon = data[0].lon;


}



</script>



<style scoped></style>