<template>
    <v-data-table
      v-model:expanded="expanded"
      :fixed-header="true"
      :headers="headers"
      :items="desserts"
      :mobile="smAndDown"
      :multi-sort="false"
      item-value="name"
      items-per-page="5"
      theme="dark"
      show-expand
      show-select
    >
      <template #[`header.name`]>
        name header slot
      </template>
      <template #[`item.calories`]>
        calories item slot
      </template>
      <template #expanded-row="{ index, columns }">
        <tr>
          <td :colspan="columns.length">
            Index: {{ index }} Expanded Row
          </td>
        </tr>
      </template>
    </v-data-table>
  </template>
  
  <script setup lang="ts">
    import { ref } from 'vue'
    import { useDisplay } from 'vuetify'
  
    const { smAndDown } = useDisplay()
  
    const expanded = ref([])
    const headers = ref([
      {
        title: 'Dessert (100g serving)',
        align: 'start',
        sortable: false,
        key: 'name',
      },
      { title: 'Calories', key: 'calories' },
      { title: 'Fat (g)', key: 'fat' },
      { title: '', key: 'data-table-expand' },
    ] as const)
    const desserts = ref([
      {
        name: 'Frozen Yogurt',
        calories: 159,
        fat: 6,
      },
      {
        name: 'Ice cream sandwich with some really long name',
        calories: 237,
        fat: 9,
      },
      {
        name: 'Eclair',
        calories: 262,
        fat: 16,
      },
      {
        name: 'Cupcake',
        calories: 305,
        fat: 3.7,
      },
      {
        name: 'Gingerbread',
        calories: 356,
        fat: 16,
      },
      {
        name: 'Jelly bean',
        calories: 375,
        fat: 0,
      },
      {
        name: 'Lollipop',
        calories: 392,
        fat: 0.2,
      },
      {
        name: 'Honeycomb',
        calories: 408,
        fat: 3.2,
      },
      {
        name: 'Donut',
        calories: 452,
        fat: 25,
      },
      {
        name: 'KitKat',
        calories: 518,
        fat: 26,
      },
    ])
  </script>
  