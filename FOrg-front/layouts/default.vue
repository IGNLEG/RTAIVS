<template>
  <v-layout class="font-display">
    <VSonner position="top-right" />
    <v-app-bar color="indigo">
      <template v-slot:prepend>
        <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
      </template>

      <v-app-bar-title>RTAĮVS</v-app-bar-title>
      <v-spacer></v-spacer>

      <v-fab class="mr-4" icon="mdi-qrcode-scan" location="right" size="40" absolute offset
        @click="dialog = !dialog">
      </v-fab>
    </v-app-bar>
    <v-navigation-drawer theme="dark" v-model="drawer">
      <v-list>
        <v-list-item :title="user.name">
          <template v-slot:prepend>
            <v-avatar @click="navigateTo('/users/' + user.id)" color="brown">
              <span class="text-h6">{{ user.name.charAt(0) }}</span>
            </v-avatar>
          </template>
        </v-list-item>
      </v-list>

      <v-divider></v-divider>

      <v-list density="compact" nav>
        <v-list-item prepend-icon="mdi-home-city" title="Pradžia" value="/" to="/"></v-list-item>
        <v-list-item v-if="userPerms.admin" prepend-icon="mdi-account-hard-hat-outline" title="Nauja Paskyra"
          value="newUser" to="/register"></v-list-item>
        <v-list-item prepend-icon="mdi-logout" title="Atsijungti" value="log_out" @click="logOut()"></v-list-item>
        <v-list-item v-if="userPerms.admin || userPerms.organiser" prepend-icon="mdi-text-box-outline" title="Renginiai"
          value="events" to="/events"></v-list-item>
        <v-list-item prepend-icon="mdi-audio-video" title="Įranga" value="equipment" to="/equipment"></v-list-item>
        <v-list-item prepend-icon="mdi-truck" title="Automobiliai" value="vehicles" to="/vehicles"></v-list-item>
        <v-list-item v-if="userPerms.admin" prepend-icon="mdi-account" title="Klientai" value="clients"
          to="/clients"></v-list-item>
        <v-list-item v-if="userPerms.admin" prepend-icon="mdi-domain" title="Įmonės" value="companies"
          to="/companies"></v-list-item>
        <v-list-item v-if="userPerms.admin" prepend-icon="mdi-script-text-outline" title="Kvalifikacijos"
          value="qualifs" to="/qualifications"></v-list-item>
        <v-list-item v-if="userPerms.admin" prepend-icon="mdi-account" title="Naudotojai" value="users"
          to="/users"></v-list-item>
        <v-list-item v-if="userPerms.admin" prepend-icon="mdi-warehouse" title="Sandėliai" value="warehouses"
          to="/warehouses"></v-list-item>
        <v-list-item v-if="userPerms.admin || userPerms.warehouse_worker" prepend-icon="mdi-tag" title="Įrangos Žymės"
          value="tags" to="/tags"></v-list-item>
        <v-list-item prepend-icon="mdi-inbox-arrow-down" title="Žinutės" value="inbox" to="/inbox"></v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-main style="min-height: 300px">

      <v-container>
        <v-col>
          <v-row>
            <v-img class="mx-auto my-6" max-width="228" src="assets/logo.svg" id="logo"></v-img>
          </v-row>
          <v-row>
            <slot />
          </v-row>
        </v-col>
      </v-container>
      <v-dialog v-model="dialog" max-width="750">
        <template v-slot:default="{ isActive }">
          <v-card title="Skenuoti QR kodą">
            <template v-slot:actions>
              <v-btn class="ml-auto" text="Close" @click="isActive.value = false"></v-btn>
            </template>
            <qrcode-stream :paused="paused" @detect="onDetect" :track="trackFunctionOption.value">
            </qrcode-stream>
          </v-card>
        </template>
      </v-dialog>
      <v-footer class="bg-black d-flex w-100">FOrg, 2024. Visos teisės saugomos.</v-footer>

    </v-main>

  </v-layout>

</template>

<style scoped>
.font-display {
  font-family: 'Nunito'
}
</style>

<script setup lang="ts">
import { VSonner } from "vuetify-sonner";
import { QrcodeStream } from 'vue-qrcode-reader'
import { useDisplay } from 'vuetify';

async function logOut() {
  const { logout } = useSanctumAuth();
  await logout();
  return navigateTo('/login')
}
const drawer = ref(useDisplay().mobile ? true : false);

const user = useSanctumUser();
const userPerms = ref({
  admin: user.value.roles.find(value => value.name == 'admin'),
  organiser: user.value.roles.find(value => value.name == 'organizer'),
  warehouse_worker: user.value.roles.find(value => value.name == 'warehouse_worker')
})
const paused = ref(false);
const trackFunctionOption = { text: 'outline', value: paintOutline }
const selectedDevice = ref(null)
const devices = ref([])
const dialog = ref(false);
onMounted(async () => {
  devices.value = (await navigator.mediaDevices.enumerateDevices()).filter(
    ({ kind }) => kind === 'videoinput'
  )

  if (devices.value.length > 0) {
    selectedDevice.value = devices.value[0]
  }
})
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
  await navigateTo(detectedCodes[0].rawValue, { external: true });
}
</script>