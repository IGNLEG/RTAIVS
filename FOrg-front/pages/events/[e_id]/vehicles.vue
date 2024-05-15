<template>
    <v-container justify="centre">
        <v-row justify="space-around">

            <v-card elevation="12" flex d-flex flex-column min-width="450">
                <v-container v-if="!loading && !selectOption">
                    <div id="app">
                        <v-form v-if="vehicles.length != 0 && option != 'view'">
                            <v-fab class="ms-4" icon="mdi-qrcode-scan" location="top right" size="40" absolute
                                @click="dialog = !dialog">
                            </v-fab>
                            <v-list v-for="item in vehicles">
                                <v-list-item>
                                    <template v-slot:prepend>
                                        <v-list-item-action start>
                                            <v-checkbox-btn v-model="vehiclesUpdate" :value="item">
                                            </v-checkbox-btn>
                                        </v-list-item-action>
                                    </template>
                                    <div>
                                        {{ item.license_plate }}, {{ item.make }}, {{ item.model }}
                                    </div>
                                </v-list-item>
                            </v-list>
                            <v-btn color="indigo-lighten-2" @click="submitVehicles">Patvirtinti</v-btn>
                        </v-form>
                        <v-container flex d-flex v-if="vehicles.length < 1" align-self="center">
                            <v-row>
                                <v-col align-self="center">
                                    <v-sheet v-if="option != 'view'">
                                        Nėra automobilių, kuriuos reiktų {{ option == 'take' ? 'paimti.' : 'grąžinti.'
                                        }}
                                    </v-sheet>
                                    <v-sheet v-if="option == 'view'">
                                        Renginiui nėra priskirti automobiliai.
                                    </v-sheet>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col align-self="center">
                                    <v-sheet>
                                        <v-btn color="indigo-lighten-2" to="/">Pradinis puslapis</v-btn>
                                        <v-btn class="ml-2" color="indigo-lighten-2" :to="'/events/' + e_id">Renginio
                                            informacija</v-btn>
                                    </v-sheet>
                                </v-col>
                            </v-row>
                        </v-container>
                        <v-container flex d-flex v-if="vehicles.length > 0 && option == 'view'" align-self="center">
                            <v-list v-for="item in vehicles">
                                <v-list-item>
                                    <div>
                                        <p>Valst. nr.: {{ item.license_plate }}</p>
                                        <p>Markė: {{ item.make }}</p>
                                        <p>Modėlis: {{ item.model }}</p>
                                        <p>Kiekis: {{ item.vehicle_amount }}</p>
                                    </div>
                                </v-list-item>
                            </v-list>
                            <v-btn color="indigo-lighten-2" to="/">Pradinis puslapis</v-btn>
                            <v-btn class="ml-2" color="indigo-lighten-2" :to="'/events/' + e_id">Renginio informacija</v-btn>
                        </v-container>
                    </div>
                </v-container>
                <v-dialog v-model="dialog" max-width="750">
                    <template v-slot:default="{ isActive }">
                        <v-card title="Nuskenuokite transporto QR kodą">
                            <template v-slot:actions>
                                <v-btn color="indigo-lighten-2" text="Uždaryti" @click="isActive.value = false"></v-btn>
                            </template>
                            <qrcode-stream :paused="paused" @detect="onDetect" :track="trackFunctionOption.value"
                                @camera-on="resetValidationState">
                                <div v-if="validationSuccess" class="validation-success">
                                    Sėkmingai pridėta!
                                </div>
                                <div v-if="validationFailure" class="validation-failure">
                                    Šios įrangos renginiui nereikia!
                                </div>
                                <div v-if="validationAdded" class="validation-added">
                                    Įranga jau pridėta!
                                </div>
                            </qrcode-stream>
                        </v-card>
                    </template>
                </v-dialog>
                <v-dialog v-model="selectOption" max-width="420" persistent>
                    <v-container>
                        <v-card elevation="12">
                            <v-card-item>
                                <v-row no-gutters cols="auto">
                                    <v-col>
                                        <v-btn color="indigo-lighten-2" text="Paimti"
                                            @click="selectOption = false; option = 'take'; getVehicles()"></v-btn>
                                    </v-col>
                                    <v-col>
                                        <v-btn color="indigo-lighten-2" class="mr-4" text="Grąžinti"
                                            @click="selectOption = false; option = 'return'; getVehicles()"></v-btn>
                                    </v-col>
                                    <v-col>
                                        <v-btn color="indigo-lighten-2" text="Peržiūrėti"
                                            @click="selectOption = false; option = 'view'; getVehicles()"></v-btn>
                                    </v-col>
                                </v-row>
                            </v-card-item>
                        </v-card>
                    </v-container>
                </v-dialog>
            </v-card>
        </v-row>
    </v-container>
</template>

<script setup lang="ts">
import { QrcodeStream } from 'vue-qrcode-reader'

definePageMeta({
    middleware: ['sanctum:auth']
})

const client = useSanctumClient();
const vehicles = ref([]);
const vehiclesUpdate = ref([]);
const dialog = ref(false);
const paused = ref(false);
const loading = ref(true);
const selectOption = ref(true);
const trackFunctionOption = { text: 'outline', value: paintOutline }
// const selectedDevice = ref(null)
// const devices = ref([])
const isValid = ref(false);
const isWrong = ref(false);
const isAdded = ref(false);
const option = ref('');
const e_id = useRoute().params.e_id;
const validationSuccess = computed(() => { return isValid.value === true });
const validationFailure = computed(() => { return isWrong.value === true });
const validationAdded = computed(() => { return isAdded.value === true });

async function getVehicles() {
    const data = await client(useRuntimeConfig().public.apiURL + '/events/' + e_id + '/vehicles', {
        method: 'GET'
    });
    if (option.value == 'take') {
        vehicles.value = data.toTake;
    }
    else if (option.value == 'return') {
        vehicles.value = data.toReturn;
    }
    else if (option.value == 'view') {
        vehicles.value = data.all_veh;
    }
    loading.value = false;
}
async function submitVehicles() {
    await client(useRuntimeConfig().public.apiURL + '/events/' + e_id + '/vehicles', {
        method: 'PUT',
        body: {
            veh_ids: vehiclesUpdate.value.map(t => t.id),
            option: option.value
        }
    });
    vehicles.value = [];
    vehiclesUpdate.value = [];
}

function resetValidationState() {
    isValid.value = false;
    isWrong.value = false;
    isAdded.value = false;
}

// onMounted(async () => {
//     devices.value = (await navigator.mediaDevices.enumerateDevices()).filter(
//         ({ kind }) => kind === 'videoinput'
//     )

//     if (devices.value.length > 0) {
//         selectedDevice.value = devices.value[0]
//     }
// })
function paintOutline(detectedCodes, ctx) {
    for (const detectedCode of detectedCodes) {
        const [firstPoint, ...otherPoints] = detectedCode.cornerPoints

        ctx.strokeStyle = 'red'

        ctx.beginPath()
        ctx.moveTo(firstPoint.x, firstPoint.y)
        for (const { x, y } of otherPoints) {
            ctx.lineTo(x, y)
        }
        ctx.lineTo(firstPoint.x, firstPoint.y)
        ctx.closePath()
        ctx.stroke()
    }
}
async function onDetect(detectedCodes) {
    paused.value = true;
    var parts = detectedCodes[0].rawValue.split("/");
    var id = parts[parts.length - 1];
    var foundValue = vehicles.value.find(veh => veh.id == id);
    if (!foundValue) {
        isValid.value = false;
        await setTimeout(() => { paused.value = false; }, 2000);
    }
    else if (!vehiclesUpdate.value.find(vehT => vehT == foundValue)) {
        vehiclesUpdate.value.push(foundValue);
        isValid.value = true;
        await setTimeout(() => { dialog.value = false; paused.value = false; }, 2000);
    }
    else {
        isAdded.value = true;
        await setTimeout(() => { paused.value = false; }, 2000);
    }

}
</script>

<style scoped>
.validation-success,
.validation-failure,
.validation-added,
.validation-success {
    position: absolute;
    width: 100%;
    height: 100%;

    background-color: rgba(255, 255, 255, 0.8);
    padding: 10px;
    text-align: center;
    font-weight: bold;
    font-size: 1.4rem;
    color: green;

    display: flex;
    flex-flow: column nowrap;
    justify-content: center;
}

.validation-failure {
    position: absolute;
    width: 100%;
    height: 100%;

    background-color: rgba(255, 255, 255, 0.8);
    padding: 10px;
    text-align: center;
    font-weight: bold;
    font-size: 1.4rem;
    color: red;

    display: flex;
    flex-flow: column nowrap;
    justify-content: center;
}

.validation-added {
    position: absolute;
    width: 100%;
    height: 100%;

    background-color: rgba(255, 255, 255, 0.8);
    padding: 10px;
    text-align: center;
    font-weight: bold;
    font-size: 1.4rem;
    color: rgb(255, 187, 0);

    display: flex;
    flex-flow: column nowrap;
    justify-content: center;
}
</style>