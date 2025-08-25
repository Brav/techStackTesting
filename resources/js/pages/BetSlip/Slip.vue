<template>

    <div class="p-4">
        <h1 class="mb-4 text-2xl text-center"><strong>Wining Slip</strong></h1>
        <ul class="list-none list-inside space-y-2 text-gray-800 bg-green-100 p-4">
            <li><strong>Combine Odds:</strong> {{ ticket.combine_odds }}</li>
            <li><strong>Stake:</strong> {{ ticket.stake }}</li>
            <li><strong>Potential payout:</strong> {{ ticket.potential_payout }}</li>
            <li v-for="selection in ticket.selections" :key="selection.id">
                <strong>Selection:</strong> {{ selection.title }}</li>
            <li><strong>Event:</strong> {{ ticket.event.title }}</li>
        </ul>
    </div>

</template>

<script setup lang="ts">

import { toRefs } from 'vue'

const props = defineProps({
    ticket: {}
})

const { ticket } = toRefs(props)

</script>

<style scoped></style>
