<template>
    <v-container justify="center">
        <v-row dense justify="center">
            <v-col>
                <v-sheet>
                    <ClientOnly>
                        <ScheduleXCalendar :calendar-app="calendarApp">
                            <template #eventModal="{ calendarEvent }">
                                <v-card>
                                    <v-list>
                                        <v-list-item :title="calendarEvent.title" prepend-icon="mdi-subtitles-outline">
                                        </v-list-item>
                                        <v-list-item :title="calendarEvent.location"
                                            prepend-icon="mdi-map-marker-outline">
                                        </v-list-item>
                                        <v-list-item :title="'Nuo ' + calendarEvent.start + ' iki ' + calendarEvent.end"
                                            prepend-icon="mdi-clock-time-four-outline">
                                        </v-list-item>
                                        <v-list-item @click="toEvent(calendarEvent.id)" :title="'Į renginį'"
                                            prepend-icon="mdi-clock-time-four-outline">
                                        </v-list-item>
                                    </v-list>
                                </v-card>
                            </template>
                        </ScheduleXCalendar>
                    </ClientOnly>
                </v-sheet>
            </v-col>
        </v-row>
        <v-row v-if="userPerms.admin" dense justify="center">
                    <v-col>
                        <VueApexCharts type="bar" :options="audioOptions" :series="audioSeries"></VueApexCharts>
                        <VueApexCharts type="bar" :options="videoOptions" :series="videoSeries"></VueApexCharts>
                    </v-col>
                    <v-col>
                        <VueApexCharts type="bar" :options="recordingOptions" :series="recordingSeries"></VueApexCharts>
                        <VueApexCharts type="bar" :options="storageOptions" :series="storageSeries"></VueApexCharts>
                    </v-col>
                    <v-col>
                        <VueApexCharts type="bar" :options="otherOptions" :series="otherSeries"></VueApexCharts>
                        <VueApexCharts type="bar" :options="vehOptions" :series="vehiclesSeries"></VueApexCharts>
                    </v-col>
                </v-row>

    </v-container>
</template>

<script setup lang='ts'>
import { ScheduleXCalendar } from '@schedule-x/vue'
import {
    createCalendar,
    viewDay,
    viewWeek,
    viewMonthGrid,
    viewMonthAgenda,

} from '@schedule-x/calendar'
import '@schedule-x/theme-default/dist/index.css'
import { createEventModalPlugin } from '@schedule-x/event-modal'
import VueApexCharts from 'vue3-apexcharts'

definePageMeta({
    middleware: ['sanctum:auth'],
})
const user = useSanctumUser();
const userPerms = ref({
    admin: user.value.roles.find(value => value.name == 'admin'),
    organiser: user.value.roles.find(value => value.name == 'organizer'),
    warehouse_worker: user.value.roles.find(value => value.name == 'warehouse_worker')
})
const client = useSanctumClient();
const events = ref([]);
const equipment = ref([]);
const vehicles = ref([]);
if (userPerms.value.admin) {
    await getEquipment();
    await getVehicles();
}
const audioOptions = ref({
    chart: {
        id: "Audio įrangos pelningumas"
    },
    colors: ['#F44336'],
    xaxis: {
        categories:
            userPerms.value.admin ? equipment.value.audio.map(eq => eq.name).length != 0 ? equipment.value.audio.map(eq => eq.name) : '' : ''
    }
})
const videoOptions = ref({
    chart: {
        id: "Video įrangos pelningumas"
    },
    colors: ['#E91E63'],
    xaxis: {
        categories:
            userPerms.value.admin ? equipment.value.video.map(eq => eq.name).length != 0 ? equipment.value.video.map(eq => eq.name) : '' : '',
    }
})
const recordingOptions = ref({
    chart: {
        id: "Įrašymo įrangos pelningumas"
    },
    colors: ['#9C27B0'],
    xaxis: {
        categories:
            userPerms.value.admin ? equipment.value.recording.map(eq => eq.name).length != 0 ? equipment.value.recording.map(eq => eq.name) : '' : '',
    }
})
const storageOptions = ref({
    chart: {
        id: "Dėžių/dėklų pelningumas"
    },
    colors: ['#2E93fA'],
    xaxis: {
        categories:
            userPerms.value.admin ? equipment.value.storage.map(eq => eq.name).length != 0 ? equipment.value.storage.map(eq => eq.name) : '' : '',
    }
})
const otherOptions = ref({
    chart: {
        id: "Kitos įrangos pelningumas"
    },
    colors: ['#66DA26'],
    xaxis: {
        categories:
            userPerms.value.admin ? equipment.value.video.map(eq => eq.name).length != 0 ? equipment.value.video.map(eq => eq.name) : '' : '',
    }
})
const vehOptions = ref({
    chart: {
        id: "Transporto pelningumas"
    },
    colors: ['#546E7A'],
    xaxis: {
        categories:
            userPerms.value.admin ? vehicles.value.map(eq => eq.license_plate).length != 0 ? vehicles.value.map(eq => eq.license_plate) : '' : '',
    }
})

const audioSeries = ref([
    {
        name: 'Audio',
        data: userPerms.value.admin ? equipment.value.audio.map(eq => eq.profit) : ''
    }

])
const videoSeries = ref([
    {
        name: 'Video',
        data: userPerms.value.admin ? equipment.value.video.map(eq => eq.profit) : ''
    },
])
const recordingSeries = ref([
    {
        name: 'Įrašymo',
        data: userPerms.value.admin ? equipment.value.recording.map(eq => eq.profit) : ''
    },
])
const storageSeries = ref([
    {
        name: 'Dėžės/dėklai',
        data: userPerms.value.admin ? equipment.value.storage.map(eq => eq.profit) : ''
    },
])
const otherSeries = ref([
    {
        name: 'Kita',
        data: userPerms.value.admin ? equipment.value.other.map(eq => eq.profit) : ''
    }
])
const vehiclesSeries = ref([
    {
        name: 'Transportas',
        data: userPerms.value.admin ? vehicles.value.map(eq => eq.profit) : ''
    }
])
await getEventsByDate();
var today = new Date();
var dd = today.getDate();
var mm = today.getMonth() + 1;
var yyyy = today.getFullYear();
if (dd < 10) {
    dd = '0' + dd;
}

if (mm < 10) {
    mm = '0' + mm;
}
today = yyyy + '-' + mm + '-' + dd;

const calendarApp = ref(createCalendar({
    selectedDate: today,
    plugins: [
        createEventModalPlugin()
    ],
    locale: 'lt-LT',
    views: [viewDay, viewWeek, viewMonthGrid, viewMonthAgenda],
    defaultView: viewMonthGrid.name,
    events: events.value,
}))

async function getEventsByDate() {
    const data = await client(useRuntimeConfig().public.apiURL + '/staff/events?from=2024-01-01&to=2024-12-31', { //change needed
        method: 'GET'
    });
    events.value = data.map(event => ({
        id: event.id,
        location: event.location,
        title: event.type + ' ' + event.subtype,
        start: event.start.slice(0, (event.start.lastIndexOf(":"))),
        end: event.end.slice(0, (event.end.lastIndexOf(":"))),
    }));
}

async function getEquipment() {
    const data = await client(useRuntimeConfig().public.apiURL + '/equipment', {
        method: 'GET',
        headers: { 'Accept': 'application/json' },
    })

    equipment.value = data;
}
async function getVehicles() {
    const data = await client(useRuntimeConfig().public.apiURL + '/vehicles', {
        method: 'GET',
        headers: { 'Accept': 'application/json' },
    })

    vehicles.value = data;
}

async function toEvent(id) {
    await navigateTo('/events/' + id);
}
</script>

<style scoped></style>