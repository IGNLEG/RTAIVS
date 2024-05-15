<template>
    <v-row>
        <v-col>
            <v-card elevation="12">
                <v-list lines="two">
                    <v-list-subheader title="Gautos žinutės"></v-list-subheader>

                    <template v-for="message in receivedMessages">
                        <v-list-item>
                            <v-list-item-title> <span class="text-grey-darken-1">{{ 'Nuo: ' + message.sender_name
                                    }}</span></v-list-item-title>
                            <template v-slot:prepend>
                                <v-avatar color="brown">
                                    <span class="text-h5">{{ message.sender_name.charAt(0) }}</span>
                                </v-avatar>
                            </template>
                            {{ message.replied_to != null
                                ? 'Atsakė į: \'' + message.replied_to + '\' žinute \'' + message.message + '\''
                                : 'Nauja žinutė: \'' + message.message + '\'' }}
                            <template v-slot:append>
                                <v-dialog v-model="dialogReply" max-width="448">
                                    <template v-slot:activator="{ props: activatorProps }">
                                        <v-btn color="indigo-lighten-2" icon="mdi-reply" v-bind="activatorProps"
                                            variant="text"></v-btn>
                                    </template>
                                    <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg"
                                        title="Atsakyti į žinutę" :subtitle="'Kam: ' + message.sender_name">
                                        <v-form style="width: 100%"
                                            @submit.prevent="sendMessage(message.sender_name, message.message)">
                                            <v-container>
                                                <v-row>
                                                    <v-col cols="12">
                                                        <v-textarea v-model="replyMessage" label="Žinutė"
                                                            required></v-textarea>
                                                    </v-col>
                                                    <v-col cols="12" align-self="end">
                                                        <v-btn color="indigo-lighten-2" type="submit">Siųsti</v-btn>
                                                    </v-col>
                                                </v-row>
                                            </v-container>
                                        </v-form>
                                    </v-card>
                                </v-dialog>
                                <v-btn color="indigo-lighten-2" icon="mdi-trash-can-outline" variant="text"
                                    @click="dialogDelete = !dialogDelete; messageToDelete = message; deleteFrom = 'inbox'"></v-btn>
                            </template>
                        </v-list-item>
                    </template>
                </v-list>
            </v-card>
        </v-col>
        <v-col>
            <v-card elevation="12">
                <v-dialog v-model="dialogNew" max-width="448">
                    <template v-slot:activator="{ props: activatorProps }">
                        <v-fab class="ms-4" icon="mdi-plus" location="top" size="30" v-bind="activatorProps" absolute>
                        </v-fab>
                    </template>
                    <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg" title="Nauja žinutė">
                        <v-form style="width: 100%" @submit.prevent="sendMessage(sendTo, null)">
                            <v-container>
                                <v-row>
                                    <v-col cols="12">
                                        <v-select v-model="sendTo" label="Kam"
                                            :items="names"
                                            item-value="name"
                                            item-title="name"
                                            ></v-select>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-textarea id="t-1" v-model="replyMessage" label="Žinutė" required></v-textarea>
                                    </v-col>
                                    <v-col cols="12" align-self="end">
                                        <v-btn color="indigo-lighten-2" type="submit">Siųsti</v-btn>
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-form>
                    </v-card>
                </v-dialog>

                <v-list lines="two">

                    <v-list-subheader title="Išsiųstos žinutės"></v-list-subheader>
                    <template v-for="message in sentMessages">
                        <v-list-item>
                            <v-list-item-title> <span class="text-grey-darken-1"> {{ 'Kam: ' + message.recipient_name }}
                                </span></v-list-item-title>
                            <template v-slot:prepend>
                                <v-avatar color="brown">
                                    <span class="text-h5">{{ message.sender_name.charAt(0) }}</span>
                                </v-avatar>
                            </template>
                            {{ message.replied_to != null
                                ? 'Atsakėte į: \'' + message.replied_to + '\' žinute \'' + message.message + '\''
                                : 'Nauja žinutė: \'' + message.message + '\'' }}
                            <template v-slot:append>
                                <v-btn color="indigo-lighten-2" icon="mdi-trash-can-outline" variant="text"
                                    @click="dialogDelete = !dialogDelete; messageToDelete = message; deleteFrom = 'sent'"></v-btn>
                            </template>
                        </v-list-item>
                    </template>
                </v-list>
            </v-card>
        </v-col>
    </v-row>
    <v-dialog v-model="dialogDelete" width="auto">
        <v-card>
            <v-card-title class="text-h5">Ar tikrai norite ištrinti šią žinutę?</v-card-title>
            <v-card-text>Ši žinutė bus ištrinta tik Jūsų pašto dėžutėje.</v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="dialogDelete = false">Uždaryti</v-btn>
                <v-btn color="error" @click="deleteMessage()">Ištrinti</v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup lang="ts">

definePageMeta({
    middleware: ['sanctum:auth'],
    layout: 'default'
})

const client = useSanctumClient();
const receivedMessages = ref([]);
const sentMessages = ref([]);
const dialogDelete = ref(false);
const messageToDelete = ref();
const deleteFrom = ref('');
const dialogReply = ref(false);
const dialogNew = ref(false);
const replyMessage = ref('');
const sendTo = ref('');
const names = ref([]);
await getMessages();
await getNames();
async function getNames(){
    const data = await client(useRuntimeConfig().public.apiURL + '/inbox/users/names', {
        method: 'GET',
        headers: { 'Accept': 'application/json' },
    })

    names.value = data;
    console.log(names);
}

async function getMessages() {
    const data = await client(useRuntimeConfig().public.apiURL + '/inbox', {
        method: 'GET',
        headers: { 'Accept': 'application/json' },
    })
    receivedMessages.value = data.inbox;
    sentMessages.value = data.sent;
}

async function deleteMessage() {
    await client(useRuntimeConfig().public.apiURL + '/inbox/' + messageToDelete.value.id, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json' },
        body: {
            option: deleteFrom.value
        }
    })

    dialogDelete.value = false;
}

async function sendMessage(recipient_name, reply_to) {
    const data = await client(useRuntimeConfig().public.apiURL + '/inbox', {
        method: 'POST',
        body: {
            recipient_name: recipient_name,
            message: replyMessage.value,
            replied_to: reply_to
        }
    })
    await getMessages()
    dialogNew.value = false;
    dialogReply.value = false;
}
</script>