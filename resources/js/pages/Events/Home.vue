<template>
    <p v-if="error" class="text-red-600">{{ error }}</p>
    <p v-else-if="loading">Loading…</p>

    <div v-else>
        <form class="mb-4 grid grid-cols-1 items-end gap-3 md:grid-cols-5" @submit.prevent="loadEvents(1)">
            <div>
                <label class="mb-1 block text-sm font-medium">Search</label>
                <input v-model="filters.search" type="text" placeholder="Event name…" class="w-full rounded-md border px-3 py-2" />
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">League</label>
                <select v-model="filters.status_id" class="w-full rounded-md border px-3 py-2">
                    <option value="">Status</option>
                    <option v-for="status in statuses" :key="status.name" :value="status.value">{{ status.label }}</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Starts after</label>
                <input v-model="filters.starts_after" type="date" lang="en-GB" class="w-full rounded-md border px-3 py-2" />
            </div>

            <div class="flex gap-2">
                <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-white hover:bg-black" :disabled="loading">Apply</button>
                <button type="button" class="rounded-md border px-4 py-2" @click="resetFilters">Reset</button>
            </div>
        </form>

        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="w-1/3 px-4 py-3 text-left text-sm font-semibold uppercase">Event</th>
                    <th class="w-1/3 px-4 py-3 text-left text-sm font-semibold uppercase">League</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Scheduled at</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Home</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Away</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold uppercase">Bet</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <tr
                    v-for="event in events"
                    :key="event.id"
                    class="cursor-pointer even:bg-gray-100 hover:bg-gray-50 focus:bg-gray-100"
                    @click="openEvent(event)"
                    @keydown.enter="openEvent(event)"
                    @keydown.space.prevent="openEvent(event)"
                    tabindex="0"
                    role="link"
                >
                    <td class="w-1/3 px-4 py-3 text-left">{{ event.title }}</td>
                    <td class="w-1/3 px-4 py-3 text-left">{{ event.league.title }}</td>
                    <td class="px-4 py-3 text-left">{{ formatDate(event.scheduled_at) }}</td>
                    <td class="px-4 py-3 text-left">{{ event.home_team.title }}</td>
                    <td class="px-4 py-3 text-left">{{ event.away_team.title }}</td>
                    <td class="px-4 py-3 text-left"></td>
                </tr>
            </tbody>
        </table>

        <nav v-if="meta" class="mt-4 flex items-center gap-2">
            <button
                class="px-3 py-1 rounded border disabled:opacity-50 cursor-pointer"
                :disabled="meta.current_page <= 1 || loading"
                @click="go(meta.current_page - 1)"
            >
                Prev
            </button>

            <span class="text-sm text-gray-600">
                Page {{ meta.current_page }} of {{ meta.last_page }}
            </span>

            <button
                class="px-3 py-1 rounded border disabled:opacity-50 cursor-pointer"
                :disabled="meta.current_page >= meta.last_page || loading"
                @click="go(meta.current_page + 1)"
            >
                Next
            </button>
        </nav>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive, computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const events = ref([]);
const links = ref(null);
const meta = ref(null);
const loading = ref(false);
const error = ref(null);
const statuses = ref([]);
const canReset = computed(() => Object.values(filters).some(isSet));

const filters = reactive({
    search: '',
    status_id: '',
    starts_after: '',
});

function resetFilters() {

    if(!canReset.value){
        return;
    }

    filters.search = '';
    filters.status_id = '';
    filters.starts_after = '';

    loadEvents();
}

async function loadEvents(page = 1) {
    loading.value = true;
    error.value = null;
    try {
        const url = new URL('/api/v1/events', window.location.origin);
        url.searchParams.set('page', String(page));

        if (filters.search && filters.search.length > 2) {
            url.searchParams.set('search', String(filters.search));
        }

        if (filters.starts_after) {
            url.searchParams.set('starts_after', String(filters.starts_after));
        }

        if (filters.status_id) {
            url.searchParams.set('status_id', String(filters.status_id));
        }

        const res = await fetch(url, { headers: { Accept: 'application/json' } });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const json = await res.json();
        events.value = json.data ?? [];
        links.value = json.links ?? null;
        meta.value = json.meta ?? null;
    } catch (e) {
        error.value = e.message ?? String(e);
    } finally {
        loading.value = false;
    }
}

async function loadStatuses() {
    const url = new URL('/api/v1/event-statuses', window.location.origin);

    const res = await fetch(url, { headers: { Accept: 'application/json' } });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);

    const json = await res.json();

    statuses.value = json.data ?? [];
}

async function openEvent(event: any) {
    await router.push({ name: 'events.show', params: { slug: event.slug } });
}

function go(page) {
    if (!meta.value) return;
    const safe = Math.min(Math.max(1, page), meta.value.last_page);
    loadEvents(safe);
}

function formatDate(isoDate: string) {
    if (!isoDate) return '';
    const date = new Date(isoDate);
    return date.toLocaleString();
}

onMounted(() => {
    loadEvents();
    loadStatuses();
});

function isSet(value: string) {
    return value.trim() !== '';
}
</script>

<style scoped></style>
