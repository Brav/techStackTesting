<template>

    <form @submit.prevent="placeBet" class="mb-4 flex flex-col p-4 gap-2">

        <div>
            <label class="mb-3 block text-sm font-medium">Selection</label>
            <select v-model.number="selectedIds" class="w-full rounded-md border px-3 py-2" multiple>
                <option v-for="selection in filteredSelections" :key="selection.id" :value="selection.id">{{ selection.title }}</option>
            </select>
        </div>

        <div>
            <label class="mb-3 block text-sm font-medium">Stake</label>
            <input v-model="stake" type="number" min="1" placeholder="Stake" class="w-full rounded-md border px-3 py-2" />
        </div>

        <div>
            Possible Win: <strong class="text-red-500">{{ potentialPayout }}</strong>
        </div>

        <button type="submit" class="rounded-md cursor-pointer bg-green-500 px-4 py-2 text-white hover:bg-green-800">Place Bet</button>

    </form>

</template>

<script setup lang="ts">
import {onMounted, ref, computed} from 'vue';

const emit = defineEmits(['place-bet'])

const selections = ref([])
const stake = ref(0)
const selectedIds = ref<Array<number|string>>([])
const selectedIdSet = computed(() => new Set(selectedIds.value.map(String)))
const err = ref<string | null>(null)
const ok  = ref<string | null>(null)

async function loadSelections() {
    const url = new URL('/api/v1/selections', window.location.origin);

    const res = await fetch(url, { headers: { Accept: 'application/json' } });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);

    const json = await res.json();

    selections.value = json.data ?? [];
}

const selectedEventIdSet = computed(() => {
    const set = new Set<number|string>()
    for (const s of selections.value) {
        if (selectedIdSet.value.has(String(s.id)) && s.market?.event.id != null) {
            set.add(s.market.event.id)
        }
    }
    return set
})

const selectedMarketIdSet = computed(() => {
    const set = new Set<number|string>()
    for (const s of selections.value) {
        if (selectedIdSet.value.has(String(s.id)) && s.market?.event.id != null) {
            set.add(s.market.id)
        }
    }
    return set
})

const posting = ref<boolean>(false)
const placeBet = async function(){

    if(posting.value){
        return;
    }

    err.value = null
    ok.value = null

    const ids = selectedIds.value
    const stakePlaced = Number(stake.value)

    if (!ids.length) {
        err.value = 'Pick at least one selection.'; return
    }
    if (!Number.isFinite(stakePlaced) || stakePlaced <= 0) {
        err.value = 'Stake must be > 0.'; return
    }

    try {
        const res = await fetch('/api/v1/bets', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify({
                selection_ids: ids,
                stake: stakePlaced
            }),
        })

        if (!res.ok) {
            const text = await res.text()
            throw new Error(text || `HTTP ${res.status}`)
        }

        const data = await res.json()
        ok.value = 'Bet placed successfully.'

        selectedIdSet.value.clear()
        selectedEventIdSet.value.clear()
        selectedMarketIdSet.value.clear()
        stake.value = 0

        emit('place-bet', data)
    } catch (e: any) {
        err.value = e?.message ?? String(e)
    } finally {
        posting.value = false
    }
}

const potentialPayout = computed(() => {

    const selected = filteredSelections.value.filter( selection =>
        selectedIdSet.value.has(String(selection.id)) )

    const odds = selected.map(s => +s.odds).filter(Boolean)

    const payout = odds.reduce((acc, n) => acc * Number(n), 1) * stake.value

    return payout.toFixed(2)

})

const filteredSelections = computed(() => {

    if(!selectedEventIdSet.value.size){
        return selections.value
    }

    const keepIds = selectedIdSet.value
    const allowedEvent = selectedEventIdSet.value
    const blockedEvents = selectedMarketIdSet.value

    return selections.value.filter(
        selection => {
            const isSelected = keepIds.has(String(selection.id))
            const eventId = selection.market.event.id

            return isSelected || (allowedEvent.has(eventId) && !blockedEvents.has(selection.market.id))
        }
    )
})

onMounted(() => {
    loadSelections();
})
</script>

<style scoped></style>
