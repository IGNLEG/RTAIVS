<template>
    <div>
        <v-card class="mx-auto pa-12 pb-8" elevation="8" max-width="448" rounded="lg">
            <v-form style="width: 100%" @submit.prevent="handleSubmit">
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <v-text-field v-model="name" label="Vardas" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="surname" label="Pavardė" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-select v-model="company" :items="companies" label="Įmonė" item-title="name"
                                item-value="id" return-object>
                            </v-select>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="phone" label="Tel. nr." required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-text-field v-model="email" label="El. paštas" type="email" required></v-text-field>
                        </v-col>
                        <v-col cols="12">
                            <v-btn color="indigo-lighten-2" type="submit">Sukurti</v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </v-form>
        </v-card>
    </div>
</template>
<script setup lang="ts">
definePageMeta({
    middleware: ['sanctum:auth', 'admin'],
    layout: 'default'
})
const name = ref('');
const surname = ref('');
const phone = ref('');
const email = ref('');
const company = ref({
    id: '',
    name: ''
});
const companies = ref([])

const client = useSanctumClient();

await getCompanies();
async function getCompanies() {
    const data = await client(useRuntimeConfig().public.apiURL + '/companies', {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })

    companies.value = data.map(t => ({ id: t.id, name: t.name }));

}
async function handleSubmit() {
    await client(useRuntimeConfig().public.apiURL + '/clients', {
        method: 'POST',
        body: {
            name: name.value,
            surname: surname.value,
            company_id: company.value.id,
            phone: phone.value,
            email: email.value,
        }
    })
    window.history.length > 1 ? useRouter().go(-1) : await navigateTo('/clients')

}

</script>

<style scoped></style>