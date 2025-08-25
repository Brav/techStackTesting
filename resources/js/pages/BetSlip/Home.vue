<template>
    <div class="flex flex-col">
        <Slip :ticket="ticket" v-if="ticket" class="w-full"></Slip>
        <Selections @place-bet="handleSlip" class="w-full"></Selections>
    </div>
</template>

<script setup lang="ts">

import Selections from '@/pages/BetSlip/Selections.vue';
import Slip from '@/pages/BetSlip/Slip.vue';

import {ref} from 'vue';

const ticket = ref(null)
function handleSlip(payload: any) {
    ticket.value = payload
}
</script>

<style scoped></style>
