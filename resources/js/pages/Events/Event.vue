<template>
    <article :id="`event-${slug}`" class="space-y-4">
        <RouterLink :to="{ name: 'events.index' }" class="text-xl hover:underline hover:text-blue-600">&larr; Back</RouterLink>

        <p v-if="error" class="text-red-600">{{ error }}</p>
        <p v-else-if="loading">Loading…</p>

        <div v-else-if="event" class="flex flex-wrap m-4 justify-center gap-8">

            <div>
                <h1 class="text-2xl font-semibold">{{ event.title }}</h1>

                <div class="text-gray-600">
                    <strong>League:</strong> {{ event.league?.title ?? '—' }}
                </div>

                <div>
                    <strong>Scheduled at:</strong> {{ formatDate(event.scheduled_at) }}
                </div>

                <div class="mt-4">
                    <strong>Home:</strong> {{ event.home_team?.title ?? '—' }}
                </div>
                <div>
                    <strong>Away:</strong> {{ event.away_team?.title ?? '—' }}
                </div>
            </div>

            <div>
                <h1 class="text-2xl font-semibold">Markets</h1>

                <ul class="list-none">
                    <li v-for="market in event.markets">
                        {{market.title}}
                    </li>
                </ul>

            </div>
        </div>
    </article>
</template>

<script setup lang="ts">

import { ref, onMounted, watch, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const slug = computed(() => String(route.params.slug ?? ''))

const event = ref<any | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)

function redirectToIndex() {
    router.push({ name: 'events.index' })
}

async function load() {

    if (!slug.value || slug.value === 'undefined' || slug.value === 'null') {
        redirectToIndex()
        return
    }

    loading.value = true
    error.value = null
    event.value = null

    try {
        const res = await fetch(`/api/v1/events/${encodeURIComponent(slug.value)}`, {
            headers: { Accept: 'application/json' },
        })
        if (!res.ok) {
            const text = await res.text()
            throw new Error(`HTTP ${res.status}: ${text || 'Failed to load event'}`)
        }

        const response = await res.json()

        event.value = response.data

    } catch (e: any) {
        if (e?.name === 'AbortError') return
        error.value = e?.message ?? String(e)
    } finally {
        loading.value = false
    }
}

function formatDate(iso?: string) {
    if (!iso) return ''
    return new Date(iso).toLocaleString()
}

watch(slug, () => load())
onMounted(load)

</script>

<style scoped></style>
